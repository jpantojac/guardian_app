<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Incident $incident)
    {
        if (!$incident->allow_comments) {
            return back()->with('error', 'Los comentarios están desactivados para este incidente.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        \Illuminate\Support\Facades\Log::info('Store comment request', $request->all());

        try {
            $comment = $incident->comments()->create([
                'user_id' => auth()->id(),
                'content' => $request->content,
                'parent_id' => $request->parent_id,
            ]);

            \Illuminate\Support\Facades\Log::info('Comment created', ['id' => $comment->id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Comentario agregado correctamente.',
                    'comment' => $comment->load('user', 'replies.user', 'reactions') // Load user data
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating comment: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return back()->with('success', 'Comentario agregado correctamente.');
    }

    public function toggleReaction(Request $request, Comment $comment)
    {
        $request->validate([
            'type' => 'required|in:like,support,angry,useful',
        ]);

        $user = auth()->user();
        $reaction = $comment->reactions()->where('user_id', $user->id)->first();

        if ($reaction) {
            if ($reaction->type === $request->type) {
                $reaction->delete();
                $action = 'removed';
            } else {
                $reaction->update(['type' => $request->type]);
                $action = 'updated';
            }
        } else {
            $comment->reactions()->create([
                'user_id' => $user->id,
                'type' => $request->type,
            ]);
            $action = 'added';
        }

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'summary' => $comment->refresh()->reactions_summary,
            'user_reaction' => $comment->user_reaction,
        ]);
    }
}
