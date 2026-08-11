<?php

namespace App\Jobs;

use App\Models\Incident;
use App\Models\User;
use App\Models\UserAlertLog;
use App\Notifications\LocalityAlertNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUserAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $incident;

    /**
     * Create a new job instance.
     */
    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->incident->localidad_id) {
            return; // Needs a localidad to trigger alerts
        }

        // Find users subscribed to this locality AND this category
        $users = User::where('is_active', true)
            ->whereHas('localidades', function ($q) {
                $q->where('localidad_id', $this->incident->localidad_id);
            })
            ->whereHas('categories', function ($q) {
                $q->where('category_id', $this->incident->category_id);
            })
            ->with('categories') // eager load to count properly later
            ->get();

        // Fetch global defaults
        $defaultThreshold = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'default_alert_threshold')->value('value') ?? 10;
        $defaultTimeframe = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'default_alert_timeframe')->value('value') ?? 3;
        $defaultCooldown = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'default_alert_cooldown')->value('value') ?? 12;

        foreach ($users as $user) {
            $threshold = $user->alert_threshold ?? $defaultThreshold;
            $timeframeHours = $user->alert_timeframe_hours ?? $defaultTimeframe;
            $cooldownHours = $user->alert_cooldown_hours ?? $defaultCooldown;

            // Check Cooldown
            $lastAlert = UserAlertLog::where('user_id', $user->id)
                ->where('localidad_id', $this->incident->localidad_id)
                ->latest()
                ->first();

            if ($lastAlert && $lastAlert->created_at->diffInHours(now()) < $cooldownHours) {
                continue; // Cooldown not met
            }

            // Count incidents in user's timeframe matching their subscribed categories in this locality
            $userCategories = $user->categories->pluck('id');
            
            $count = Incident::where('localidad_id', $this->incident->localidad_id)
                ->whereIn('category_id', $userCategories)
                ->where('created_at', '>=', now()->subHours($timeframeHours))
                ->count();

            if ($count >= $threshold) {
                // Group by category to build the message
                $breakdown = Incident::selectRaw('category_id, count(*) as total')
                    ->where('localidad_id', $this->incident->localidad_id)
                    ->whereIn('category_id', $userCategories)
                    ->where('created_at', '>=', now()->subHours($timeframeHours))
                    ->groupBy('category_id')
                    ->with('category')
                    ->get();

                // Send notification
                $user->notify(new LocalityAlertNotification(
                    $this->incident->localidad,
                    $count,
                    $timeframeHours,
                    $breakdown
                ));

                // Log alert to restart cooldown
                UserAlertLog::create([
                    'user_id' => $user->id,
                    'localidad_id' => $this->incident->localidad_id
                ]);
            }
        }
    }
}
