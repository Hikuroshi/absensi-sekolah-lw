<?php

namespace App\Livewire;

use App\Models\Classes;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class ScheduleManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $search = '';
    public bool $showDeleteModal = false;
    public ?int $scheduleToDelete = null;

    // Form properties
    public $class_id, $subject_id, $user_id, $day, $start_time, $end_time;

    #[Computed]
    public function schedules()
    {
        $query = DB::table('schedules')
            ->join('classes', 'schedules.class_id', '=', 'classes.id')
            ->join('subjects', 'schedules.subject_id', '=', 'subjects.id')
            ->join('users', 'schedules.user_id', '=', 'users.id')
            ->select(
                'schedules.id',
                'schedules.class_id',
                'schedules.subject_id',
                'schedules.user_id',
                'classes.name as class_name',
                'subjects.name as subject_name',
                'users.name as user_name',
                'schedules.day',
                'schedules.start_time',
                'schedules.end_time'
            )
            ->orderBy('classes.name')
            ->orderBy('subjects.name');
        if ($this->search) {
            $query->where(function($q) {
                $q->where('classes.name', 'like', "%{$this->search}%")
                  ->orWhere('subjects.name', 'like', "%{$this->search}%")
                  ->orWhere('users.name', 'like', "%{$this->search}%");
            });
        }
        return $query->paginate(10);
    }

    #[Computed]
    public function classes()
    {
        return Classes::orderBy('name')->get();
    }

    #[Computed]
    public function subjects()
    {
        return Subject::orderBy('name')->get();
    }

    #[Computed]
    public function teachers()
    {
        return User::where('role', 'guru')->orderBy('name')->get();
    }

    public function openModal(?int $id = null): void
    {
        $this->editingId = $id;
        if ($id) {
            $row = DB::table('schedules')->where('id', $id)->first();
            if ($row) {
                $this->fill([
                    'class_id' => $row->class_id,
                    'subject_id' => $row->subject_id,
                    'user_id' => $row->user_id,
                    'day' => $row->day,
                    'start_time' => $row->start_time,
                    'end_time' => $row->end_time,
                ]);
            }
        } else {
            $this->reset(['class_id', 'subject_id', 'user_id', 'day', 'start_time', 'end_time']);
        }
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['class_id', 'subject_id', 'user_id', 'editingId']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'user_id' => 'required|exists:users,id',
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);
        if ($this->editingId) {
            DB::table('schedules')->where('id', $this->editingId)->update([
                'class_id' => $this->class_id,
                'subject_id' => $this->subject_id,
                'user_id' => $this->user_id,
                'day' => $this->day,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'updated_at' => now(),
            ]);
            $this->dispatch('schedule-updated', message: 'Jadwal berhasil diperbarui!');
        } else {
            DB::table('schedules')->updateOrInsert(
                [
                    'class_id' => $this->class_id,
                    'subject_id' => $this->subject_id,
                    'user_id' => $this->user_id,
                    'day' => $this->day,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                ],
                ['created_at' => now(), 'updated_at' => now()]
            );
            $this->dispatch('schedule-created', message: 'Jadwal berhasil ditambahkan!');
        }
        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $this->scheduleToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        try {
            DB::table('schedules')->where('id', $this->scheduleToDelete)->delete();
            $this->dispatch('schedule-deleted', message: 'Jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('schedule-error', message: 'Terjadi kesalahan saat menghapus jadwal!');
        }
        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->scheduleToDelete = null;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[On('schedule-created')]
    #[On('schedule-updated')]
    #[On('schedule-deleted')]
    #[On('schedule-error')]
    public function handleFlashMessage(string $message): void
    {
        session()->flash('message', $message);
    }

    public function render()
    {
        return view('livewire.schedule-management', [
            'schedules' => $this->schedules(),
        ]);
    }
} 