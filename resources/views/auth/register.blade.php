@extends('layouts.app')

@section('content')
    <div
        style="min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div class="card" style="max-width: 450px; width: 100%; text-align: center;">
            <div style="margin-bottom: 2rem;">
                <div
                    style="width: 64px; height: 64px; background-color: #0A0A0A; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        <path d="M12 8v4m0 4h.01" />
                    </svg>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">
                    Seguridad Ciudadana</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">Reporta y consulta incidentes de seguridad</p>
            </div>

            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid #F5F5F5;">
                <a href="{{ route('login') }}"
                    style="flex: 1; padding: 0.75rem; background: white; border: none; color: var(--text-secondary); text-decoration: none; display: block;">Iniciar
                    Sesión</a>
                <button type="button"
                    style="flex: 1; padding: 0.75rem; background: white; border: none; border-bottom: 2px solid #0A0A0A; margin-bottom: -2px; font-weight: 600; cursor: pointer;">Registrarse</button>
            </div>

            @if ($errors->any())
                <div
                    style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; text-align: left;">
                    <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.875rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" style="text-align: left;">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        placeholder="Tu nombre completo">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="tu@email.com">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="••••••••">
                </div>

                <div style="margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <label style="display: flex; align-items: start; gap: 0.5rem; font-weight: 400;">
                        <input type="checkbox" name="terms" required style="width: auto; margin-top: 0.25rem;">
                        <span>Declaro que he leído y acepto los <a href="{{ route('legal.terminos') }}" target="_blank" style="color: var(--primary); text-decoration: underline;">Términos de Uso</a> y autorizo el manejo de mi información bajo la <a href="{{ route('legal.privacidad') }}" target="_blank" style="color: var(--primary); text-decoration: underline;">Política de Tratamiento de Datos Personales (Ley 1581)</a>.</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary"
                    style="width: 100%; padding: 0.75rem; font-size: 1rem;">Registrarse</button>
            </form>
        </div>
    </div>
@endsection