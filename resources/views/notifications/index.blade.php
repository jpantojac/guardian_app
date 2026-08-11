@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8" style="margin: 0 auto; max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 1.5rem; font-weight: bold; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            Mis Notificaciones
        </h1>
        
        @if(auth()->user()->notifications()->count() > 0)
        <div style="display: flex; gap: 0.5rem;">
            @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary" style="font-size: 0.875rem;">
                    Marcar todas como leídas
                </button>
            </form>
            @endif
            <form action="{{ route('notifications.destroy-all') }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar todas las notificaciones?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn" style="background: var(--danger); color: white; border: none; font-size: 0.875rem; padding: 0.5rem 1rem; border-radius: 0.375rem; cursor: pointer;">
                    Eliminar todas
                </button>
            </form>
        </div>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="card" style="text-align: center; padding: 3rem;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="2" style="margin: 0 auto 1rem;">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">No tienes notificaciones</h3>
            <p style="color: var(--text-secondary);">Cuando se generen alertas de seguridad en tus zonas de interés, aparecerán aquí.</p>
        </div>
    @else
        <div class="notifications-list" style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($notifications as $notification)
                <div class="card" style="display: flex; gap: 1rem; align-items: flex-start; {{ is_null($notification->read_at) ? 'border-left: 4px solid var(--danger); background: #fffcfc;' : 'opacity: 0.75;' }}">
                    
                    <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.25rem; {{ is_null($notification->read_at) ? 'background: #fee2e2; color: var(--danger);' : 'background: #f3f4f6; color: #9ca3af;' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>

                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.25rem;">
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">
                                {{ $notification->data['title'] ?? 'Alerta de Seguridad' }}
                                @if(is_null($notification->read_at))
                                    <span style="display: inline-block; background: var(--danger); width: 8px; height: 8px; border-radius: 50%; margin-left: 0.5rem;" title="Nueva"></span>
                                @endif
                            </h3>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="font-size: 0.75rem; color: var(--text-secondary); white-space: nowrap;">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta notificación?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Eliminar" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <p style="margin: 0 0 0.5rem 0; color: var(--text-primary);">
                            {{ $notification->data['message'] ?? '' }}
                        </p>
                        
                        @if(isset($notification->data['details']))
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary); padding: 0.5rem; background: #f3f4f6; border-radius: 0.375rem;">
                            {{ $notification->data['details'] }}
                        </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 2rem;">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
