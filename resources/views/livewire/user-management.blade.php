<div class="max-w-7xl mx-auto p-4">
    {{-- Header con título y botón de crear --}}
    <header class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestión de Usuarios</h1>
        <button wire:click="createUser" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nuevo Usuario
        </button>
    </header>

    {{-- Mensajes de éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    {{-- Mensajes de error --}}
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Buscador --}}
    <div class="mb-6">
        <div class="relative">
            <input 
                type="search" 
                wire:model.live="search" 
                placeholder="Buscar usuarios por nombre o email..."
                class="w-full sm:w-96 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            />
            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    {{-- Tabla de usuarios editable --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contraseña</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Roles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Permisos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {{-- Fila para nuevo usuario --}}
                    @if($editingUserId === 'new')
                        <tr class="bg-blue-50 dark:bg-blue-900/20">
                            <td class="px-6 py-4">
                                <input 
                                    type="text" 
                                    wire:model="editingData.name"
                                    placeholder="Nombre completo"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                />
                            </td>
                            <td class="px-6 py-4">
                                <input 
                                    type="email" 
                                    wire:model="editingData.email"
                                    placeholder="correo@ejemplo.com"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                />
                            </td>
                            <td class="px-6 py-4">
                                <input 
                                    type="password" 
                                    wire:model="editingData.password"
                                    placeholder="Contraseña"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                />
                            </td>
                            <td class="px-6 py-4">
                                <select 
                                    wire:model="editingData.roles" 
                                    multiple
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                >
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <select 
                                    wire:model="editingData.permissions" 
                                    multiple
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                >
                                    @foreach($permissions as $permission)
                                        <option value="{{ $permission->name }}">{{ ucfirst($permission->name) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button wire:click="saveUser" class="text-green-600 hover:text-green-900 dark:text-green-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                    <button wire:click="cancelEdit" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endif

                    {{-- Filas de usuarios existentes --}}
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $editingUserId == $user->id ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                            {{-- Nombre --}}
                            <td class="px-6 py-4">
                                @if($editingUserId == $user->id)
                                    <input 
                                        type="text" 
                                        wire:model="editingData.name"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    />
                                @else
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium">
                                                {{ $user->initials() }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4">
                                @if($editingUserId == $user->id)
                                    <input 
                                        type="email" 
                                        wire:model="editingData.email"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    />
                                @else
                                    <span class="text-sm text-gray-900 dark:text-gray-300">{{ $user->email }}</span>
                                @endif
                            </td>

                            {{-- Contraseña --}}
                            <td class="px-6 py-4">
                                @if($editingUserId == $user->id)
                                    <input 
                                        type="password" 
                                        wire:model="editingData.password"
                                        placeholder="Nueva contraseña (opcional)"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    />
                                @else
                                    <span class="text-sm text-gray-500">••••••••</span>
                                @endif
                            </td>

                            {{-- Roles --}}
                            <td class="px-6 py-4">
                                @if($editingUserId == $user->id)
                                    <select 
                                        wire:model="editingData.roles" 
                                        multiple
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    >
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            {{-- Permisos --}}
                            <td class="px-6 py-4">
                                @if($editingUserId == $user->id)
                                    <select 
                                        wire:model="editingData.permissions" 
                                        multiple
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    >
                                        @foreach($permissions as $permission)
                                            <option value="{{ $permission->name }}">{{ ucfirst($permission->name) }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->permissions->take(3) as $permission)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if($user->permissions->count() > 3)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                +{{ $user->permissions->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4">
                                @if($editingUserId == $user->id)
                                    <div class="flex space-x-2">
                                        <button wire:click="saveUser({{ $user->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button wire:click="cancelEdit" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex space-x-2">
                                        <button wire:click="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="deleteUser({{ $user->id }})" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            wire:confirm="¿Estás seguro de que deseas eliminar este usuario?"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        @if($editingUserId !== 'new')
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p>No se encontraron usuarios.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-6 flex justify-center">
        {{ $users->links() }}
    </div>

    {{-- Ayuda para usuarios --}}
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-blue-800 dark:text-blue-200">
                <p><strong>Instrucciones:</strong></p>
                <ul class="mt-1 list-disc list-inside">
                    <li>Haz clic en el ícono de editar (lápiz) para editar un usuario existente</li>
                    <li>Para roles y permisos, mantén presionado Ctrl/Cmd para seleccionar múltiples opciones</li>
                    <li>La contraseña es opcional al editar (deja vacío para mantener la actual)</li>
                    <li>Haz clic en guardar (✓) para confirmar cambios o cancelar (✕) para descartar</li>
                </ul>
            </div>
        </div>
    </div>
</div>