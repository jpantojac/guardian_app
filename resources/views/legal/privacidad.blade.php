@extends('layouts.app')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto; line-height: 1.6;">
    <h1 style="border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 2rem;">Política de Tratamiento de Datos Personales</h1>
    
    <div class="alert alert-success" style="position: static; transform: none; margin-bottom: 2rem;">
        Este documento da estricto cumplimiento a la <strong>Ley Estatutaria 1581 de 2012</strong> (Protección de Datos Personales en Colombia) y sus decretos reglamentarios.
    </div>

    <h3>1. Objetivo</h3>
    <p>Garantizar el derecho constitucional que tienen todas las personas a conocer, actualizar y rectificar la información que se haya recogido sobre ellas en las bases de datos de GuardianApp.</p>

    <h3>2. Tratamiento y Finalidad de los Datos</h3>
    <p>Los datos recolectados (ubicaciones geográficas, nombres, correos electrónicos y material fotográfico) serán utilizados exclusivamente para:</p>
    <ul>
        <li>Construir mapas de calor estadísticos de seguridad ciudadana.</li>
        <li>Permitir a las autoridades analizar tendencias de incidentes en Bogotá.</li>
        <li>Garantizar la trazabilidad y reducir los reportes fraudulentos.</li>
    </ul>

    <h3>3. Derechos de los Titulares</h3>
    <p>De acuerdo con la Ley 1581 de 2012, el Titular de los datos personales tiene los siguientes derechos:</p>
    <ul>
        <li><strong>Conocer, actualizar y rectificar</strong> sus datos personales.</li>
        <li><strong>Solicitar prueba</strong> de la autorización otorgada.</li>
        <li><strong>Revocar la autorización</strong> y/o solicitar la supresión del dato en cualquier momento.</li>
        <li><strong>Acceder en forma gratuita</strong> a sus datos personales que hayan sido objeto de Tratamiento.</li>
    </ul>

    <h3>4. Seguridad de la Información</h3>
    <p>GuardianApp implementa protocolos técnicos como ofuscación temporal y filtros de validación estricta para garantizar la reserva de las evidencias y evitar filtraciones o accesos no autorizados.</p>
</div>
@endsection
