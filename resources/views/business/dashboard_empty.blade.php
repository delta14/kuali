{{-- resources/views/business/dashboard_empty.blade.php --}}
@extends('layouts.app')

@section('title', 'Comercio no asignado')

@section('content')
<div class="k-main-inner" style="display: flex; align-items: center; justify-content: center; min-height: 70vh; font-family: 'Poppins', sans-serif;">
    <div style="max-width: 480px; width: 100%; background: #ffffff; border: 1px solid rgba(229, 231, 235, 0.7); border-radius: 28px; padding: 2.5rem; text-align: center; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);">
        
        <div style="width: 72px; height: 72px; border-radius: 20px; background: rgba(99, 102, 241, 0.08); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.5rem; animation: float 3s ease-in-out infinite;">
            <i class="ri-store-2-line"></i>
        </div>

        <h1 style="font-size: 1.45rem; font-weight: 700; color: #1e1b4b; margin: 0 0 0.5rem; letter-spacing: -0.02em;">
            ¡Bienvenido(a) a Kuali!
        </h1>
        
        <p style="font-size: 0.88rem; color: #6b7280; line-height: 1.5; margin: 0 0 1.75rem;">
            Tu cuenta se ha registrado correctamente, pero aún no tienes un comercio asignado o activo en la plataforma.
        </p>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 16px; padding: 1.1rem; margin-bottom: 1.75rem; text-align: left;">
            <h4 style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #4b5563; margin: 0 0 0.4rem; letter-spacing: 0.05em;">
                ¿Qué debes hacer ahora?
            </h4>
            <p style="font-size: 0.78rem; color: #6b7280; line-height: 1.45; margin: 0;">
                Solicita al administrador general de Kuali que vincule tu correo electrónico (<strong>{{ auth()->user()->email }}</strong>) a tu negocio desde el panel de Súper Admin.
            </p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="mailto:robertokuali@gmail.com?subject=Asignar%20comercio%20en%20Kuali" class="k-main-cta" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.7rem 1.5rem; width: 100%; border-radius: 99px;">
                <i class="ri-mail-send-line" style="font-size: 1.1rem;"></i>
                <span>Contactar a Soporte</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                @csrf
                <button type="submit" style="background: transparent; border: 1px solid #e5e7eb; color: #4b5563; font-weight: 500; font-size: 0.85rem; padding: 0.6rem 1.5rem; border-radius: 99px; cursor: pointer; width: 100%; transition: all 0.2s ease;">
                    Cerrar sesión
                </button>
            </form>
        </div>

    </div>
</div>

<style>
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
        100% { transform: translateY(0px); }
    }
</style>
@endsection
