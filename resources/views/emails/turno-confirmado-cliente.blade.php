<h1>Tu turno esta confirmado</h1>
<p>Hola {{ $turno->cliente->nombre_completo }}, tu pago fue acreditado.</p>
<ul>
    <li>Empresa: {{ $turno->empresa->nombre }}</li>
    <li>Servicio: {{ $turno->servicio->nombre }}</li>
    <li>Recurso: {{ $turno->recurso->nombre }}</li>
    <li>Inicio: {{ $turno->fecha_hora_inicio }}</li>
</ul>
