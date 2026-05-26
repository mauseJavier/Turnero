<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Validate;

class UserManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingUserId = null;
    public $search = '';
    
    // Arrays para datos de edición inline
    public $editingData = [];
    
    #[Validate('required|min:3')]
    public $name = '';
    
    #[Validate('required|email')]
    public $email = '';
    
    #[Validate('nullable|min:6')]
    public $password = '';
    
    public $selectedRoles = [];
    public $selectedPermissions = [];
    
    protected $listeners = ['userDeleted' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function createUser()
    {
        $this->resetForm();
        $this->editingUserId = 'new';
        $this->editingData = [
            'name' => '',
            'email' => '',
            'password' => '',
            'roles' => [],
            'permissions' => []
        ];
    }

    public function editUser($userId)
    {
        $user = User::with(['roles', 'permissions'])->find($userId);
        
        $this->editingUserId = $userId;
        $this->editingData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => '',
            'roles' => $user->roles->pluck('name')->toArray(),
            'permissions' => $user->permissions->pluck('name')->toArray()
        ];
    }

    public function saveUser($userId = null)
    {
        $targetUserId = $userId ?? $this->editingUserId;
        
        // Validación básica
        if (empty($this->editingData['name']) || empty($this->editingData['email'])) {
            session()->flash('error', 'El nombre y email son requeridos.');
            return;
        }

        if ($targetUserId === 'new') {
            // Crear nuevo usuario
            if (empty($this->editingData['password'])) {
                session()->flash('error', 'La contraseña es requerida para nuevos usuarios.');
                return;
            }

            $user = User::create([
                'name' => $this->editingData['name'],
                'email' => $this->editingData['email'],
                'password' => bcrypt($this->editingData['password']),
            ]);
            
            session()->flash('message', 'Usuario creado correctamente.');
        } else {
            // Actualizar usuario existente
            $user = User::find($targetUserId);
            
            $updateData = [
                'name' => $this->editingData['name'],
                'email' => $this->editingData['email'],
            ];

            if (!empty($this->editingData['password'])) {
                $updateData['password'] = bcrypt($this->editingData['password']);
            }

            $user->update($updateData);
            session()->flash('message', 'Usuario actualizado correctamente.');
        }

        // Sincronizar roles y permisos
        if (isset($this->editingData['roles'])) {
            $user->syncRoles($this->editingData['roles']);
        }
        
        if (isset($this->editingData['permissions'])) {
            $user->syncPermissions($this->editingData['permissions']);
        }

        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
        $this->editingData = [];
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        session()->flash('message', 'Usuario eliminado correctamente.');
        $this->dispatch('userDeleted');
    }

    public function resetForm()
    {
        $this->editingUserId = null;
        $this->editingData = [];
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRoles = [];
        $this->selectedPermissions = [];
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->with(['roles', 'permissions'])
            ->paginate(10);

        $roles = Role::all();
        $permissions = Permission::all();

        return view('livewire.user-management', [
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }
}