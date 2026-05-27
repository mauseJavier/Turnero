<h1>Turno confirmado por pago</h1>
<p>Se confirmo un turno en {{ $turno->empresa->nombre }}.</p>
<ul>
    <li>Cliente: {{ $turno->cliente->nombre_completo }}</li>
    <li>Servicio: {{ $turno->servicio->nombre }}</li>
    <li>Recurso: {{ $turno->recurso->nombre }}</li>
    <li>Inicio: {{ $turno->fecha_hora_inicio }}</li>
    <li>Pago: {{ $turno->pago_status }}</li>
</ul>
