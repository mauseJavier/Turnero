@php
$classes = Flux::classes()
    ->add('rounded-2xl border p-6 shadow-sm')
    ->add('border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900')
    ;
@endphp

<div {{ $attributes->class($classes) }} data-flux-card>
    {{ $slot }}
</div>
