<div :title="__('Nueva Empresa')">
                
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-800 p-6 flex flex-col justify-center">
                <form wire:submit.prevent="save" class="space-y-4 w-full max-w-lg mx-auto">
                    @if (session()->has('success'))
                        <div class="p-2 bg-green-200 text-green-800 rounded text-center">
                            {{ session('success') }}
                        </div>
                    @endif
            
                    <div class="grid gap-2">
                        <label class="font-semibold text-sm text-zinc-700 dark:text-zinc-200">Nombre</label>
                        <input type="text" wire:model="nombre" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent" required maxlength="255">
                        @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="grid gap-2">
                        <label class="font-semibold text-sm text-zinc-700 dark:text-zinc-200">Email</label>
                        <input type="email" wire:model="email" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent" required>
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="grid gap-2">
                        <label class="font-semibold text-sm text-zinc-700 dark:text-zinc-200">Teléfono</label>
                        <input type="text" wire:model="telefono" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent" maxlength="20">
                        @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="grid gap-2">
                        <label class="font-semibold text-sm text-zinc-700 dark:text-zinc-200">Dirección</label>
                        <input type="text" wire:model="direccion" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent" maxlength="500">
                        @error('direccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="grid gap-2">
                        <label class="font-semibold text-sm text-zinc-700 dark:text-zinc-200">Tipo de Servicio</label>
                        <input type="text" wire:model="tipo_servicio" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent" required maxlength="100">
                        @error('tipo_servicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="grid gap-2">
                        <label class="font-semibold text-sm text-zinc-700 dark:text-zinc-200">Descripción</label>
                        <textarea wire:model="descripcion" class="w-full rounded border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-900 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent" maxlength="1000"></textarea>
                        @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="activo" id="activo" class="accent-accent h-4 w-4 rounded border-zinc-300 dark:border-zinc-600">
                        <label for="activo" class="font-semibold text-sm text-zinc-700 dark:text-zinc-200">Activo</label>
                        @error('activo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
            
                    <button type="submit" class="w-full bg-accent text-white dark:bg-zinc-700 dark:text-white font-semibold py-2 px-4 rounded hover:bg-zinc-800 transition">Agregar Empresa</button>
                </form>
            </div>
    
</div>
