<?php

namespace App\Livewire;

use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class SubjectManagement extends Component
{
    use WithPagination;

    protected $queryString = ['search'];

    // Properties
    public bool $showModal = false;
    public ?int $editingSubject = null;
    public string $search = '';
    public bool $showDeleteModal = false;
    public ?int $subjectToDelete = null;

    // Form properties with validation rules
    public $name, $code, $description;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code,' . $this->editingSubject,
            'description' => 'nullable|string|max:500',
        ];
    }

    // Computed property for better performance
    #[Computed]
    public function subjects()
    {
        return Subject::search($this->search)
            ->with(['classesWithTeachers', 'classTeachers'])
            ->orderBy('name')
            ->paginate(10);
    }

    // Methods
    public function openModal(?int $subjectId = null): void
    {
        $this->editingSubject = $subjectId;
        
        if ($subjectId) {
            $subject = Subject::findOrFail($subjectId);
            $this->fill([
                'name' => $subject->name,
                'code' => $subject->code ?? '',
                'description' => $subject->description ?? '',
            ]);
        } else {
            $this->reset(['name', 'code', 'description']);
        }
        
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'code', 'description', 'editingSubject']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code ?: null,
            'description' => $this->description ?: null,
        ];

        if ($this->editingSubject) {            
            Subject::findOrFail($this->editingSubject)->update($data);
            $this->dispatch('subject-updated', message: 'Pelajaran berhasil diperbarui!');
        } else {
            Subject::create($data);
            $this->dispatch('subject-created', message: 'Pelajaran berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function confirmDelete(int $subjectId): void
    {
        $this->subjectToDelete = $subjectId;
        $this->showDeleteModal = true;
    }
    
    public function delete(): void
    {
        try {
            $subject = Subject::findOrFail($this->subjectToDelete);
            
            if (!$subject->canBeDeleted()) {
                $this->dispatch('subject-error', message: 'Tidak dapat menghapus pelajaran yang masih terkait dengan kelas atau guru!');
                $this->closeDeleteModal();
                return;
            }
            
            $subject->delete();
            $this->dispatch('subject-deleted', message: 'Pelajaran berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('subject-error', message: 'Terjadi kesalahan saat menghapus pelajaran!');
        }
        
        $this->closeDeleteModal();
    }
    
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->subjectToDelete = null;
    }

    // Reset pagination when search changes
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // Event listeners
    #[On('subject-created')]
    #[On('subject-updated')]
    #[On('subject-deleted')]
    #[On('subject-error')]
    public function handleFlashMessage(string $message): void
    {
        session()->flash('message', $message);
    }

    public function render()
    {
        return view('livewire.subject-management');
    }
}