<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class UserManagement extends Component
{
    use WithPagination;

    // Properties
    public bool $showModal = false;
    public ?int $editingUser = null;
    public string $search = '';
    public bool $showDeleteModal = false;
    public ?int $userToDelete = null;
    public string $roleFilter = '';
    protected $queryString = ['search', 'roleFilter'];

    // Form properties
    public $name, $email, $role, $id_number, $password;

    public function rules()
    {
        $uniqueEmail = 'unique:users,email,' . $this->editingUser;
        $passwordRule = $this->editingUser ? 'nullable|min:6' : 'required|min:6';
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', $uniqueEmail],
            'role' => 'required|in:admin,guru,ketua_kelas,siswa',
            'id_number' => 'nullable|string|max:50',
            'password' => $passwordRule,
        ];
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->search($this->search)
            ->filterByRole($this->roleFilter)
            ->orderBy('name')
            ->paginate(10);
    }

    // Methods
    public function openModal(?int $userId = null): void
    {
        $this->editingUser = $userId;
        if ($userId) {
            $user = User::findOrFail($userId);
            $this->fill([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'id_number' => $user->id_number ?? '',
                'password' => '',
            ]);
        } else {
            $this->reset(['name', 'email', 'role', 'id_number', 'password']);
        }
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'email', 'role', 'id_number', 'editingUser']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'id_number' => $this->id_number ?: null,
        ];
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }
        if ($this->editingUser) {
            User::findOrFail($this->editingUser)->update($data);
            $this->dispatch('user-updated', message: 'User berhasil diperbarui!');
        } else {
            User::create($data);
            $this->dispatch('user-created', message: 'User berhasil ditambahkan!');
        }
        $this->closeModal();
    }

    public function confirmDelete(int $userId): void
    {
        $this->userToDelete = $userId;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        try {
            $user = User::findOrFail($this->userToDelete);
            $user->delete();
            $this->dispatch('user-deleted', message: 'User berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('user-error', message: 'Terjadi kesalahan saat menghapus user!');
        }
        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
    }

    public function resetPassword(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['password' => \Hash::make('password123')]);
        $this->dispatch('user-reset-password', message: 'Password berhasil direset ke password123!');
    }

    // Reset pagination when search changes
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // Event listeners
    #[On('user-created')]
    #[On('user-updated')]
    #[On('user-deleted')]
    #[On('user-error')]
    #[On('user-reset-password')]
    public function handleFlashMessage(string $message): void
    {
        session()->flash('message', $message);
    }

    public function render()
    {
        return view('livewire.user-management');
    }
} 