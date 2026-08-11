<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LocalityAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $localidad;
    public $count;
    public $timeframeHours;
    public $details;

    /**
     * Create a new notification instance.
     */
    public function __construct($localidad, $count, $timeframeHours, $breakdown)
    {
        $this->localidad = $localidad;
        $this->count = $count;
        $this->timeframeHours = $timeframeHours;
        
        $detailsStr = $breakdown->map(function($item) {
            return $item->total . ' ' . ($item->category->name ?? 'Desconocidos');
        })->implode(', ');
        
        $this->details = "Discriminados así: {$detailsStr}.";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // For now, only database (In-App)
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Alerta de Seguridad en {$this->localidad->nombre}",
            'message' => "En la localidad {$this->localidad->nombre} se han presentado {$this->count} incidentes en las últimas {$this->timeframeHours} horas.",
            'details' => $this->details,
            'localidad_id' => $this->localidad->id,
            'icon' => 'fa-exclamation-triangle',
            'color' => 'red'
        ];
    }
}
