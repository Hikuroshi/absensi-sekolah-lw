<?php

namespace App\Livewire;

use App\Models\Classes;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class ClassManagement extends Component
{
    use WithPagination;

    protected $queryString = ['search'];

    // Properties
    public bool $showModal = false;
    public ?int $editingClass = null;
    public string $search = '';
    public bool $showDeleteModal = false;
    public ?int $classToDelete = null;

    // Form properties
    public $name, $academic_year;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:255',
        ];
    }

    // Computed property for paginated classes
    #[Computed]
    public function classes()
    {
        return Classes::active()
            ->search($this->search)
            ->with(['subjectsWithTeachers', 'subjectTeachers'])
            ->orderBy('name')
            ->paginate(10);
    }

    // Data for select options
    #[Computed]
    public function teachers()
    {
        return User::where('role', 'guru')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function students()
    {
        return User::where('role', 'siswa')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // Methods
    public function openModal(?int $classId = null): void
    {
        $this->editingClass = $classId;
        if ($classId) {
            $class = Classes::findOrFail($classId);
            $this->fill([
                'name' => $class->name,
                'academic_year' => $class->academic_year,
            ]);
        } else {
            $this->reset(['name', 'academic_year']);
        }
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'academic_year', 'editingClass']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'academic_year' => $this->academic_year,
            'is_active' => true,
        ];
        if ($this->editingClass) {
            Classes::findOrFail($this->editingClass)->update($data);
            $this->dispatch('class-updated', message: 'Kelas berhasil diperbarui!');
        } else {
            Classes::create($data);
            $this->dispatch('class-created', message: 'Kelas berhasil ditambahkan!');
        }
        $this->closeModal();
    }

    public function confirmDelete(int $classId): void
    {
        $this->classToDelete = $classId;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        try {
            $class = Classes::findOrFail($this->classToDelete);
            $class->update(['is_active' => false]);
            $this->dispatch('class-deleted', message: 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('class-error', message: 'Terjadi kesalahan saat menghapus kelas!');
        }
        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->classToDelete = null;
    }

    // Reset pagination when search changes
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // Event listeners
    #[On('class-created')]
    #[On('class-updated')]
    #[On('class-deleted')]
    #[On('class-error')]
    public function handleFlashMessage(string $message): void
    {
        session()->flash('message', $message);
    }

    public function render()
    {
        return view('livewire.class-management');
    }
}
