<?php

namespace App\Livewire;

use App\Models\Classes;
use App\Models\User;
use App\Models\ClassMember;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class ClassDetail extends Component
{
    public Classes $class;
    public $members;
    public $allStudents;
    public $allTeachers;
    public $newMemberId = null;
    public $newMemberRole = 'siswa';
    public $editWaliKelasId = null;
    public $editKetuaKelasId = null;

    public function mount($id)
    {
        $this->class = Classes::with(['members'])->findOrFail($id);
        $this->refreshMembers();
        $this->allStudents = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();
        $this->allTeachers = User::where('role', 'guru')->where('is_active', true)->orderBy('name')->get();
        $this->editWaliKelasId = $this->class->waliKelas()?->id;
        $this->editKetuaKelasId = $this->class->members()->wherePivot('role', 'ketua_kelas')->wherePivot('is_active', true)->first()?->id;
    }

    public function refreshMembers()
    {
        $this->members = $this->class->members()->withPivot('role', 'is_active')->get();
    }

    public function addMember()
    {
        if (!$this->newMemberId || !$this->newMemberRole) return;
        // Tidak boleh tambah wali kelas/ketua kelas lewat sini
        if (in_array($this->newMemberRole, ['guru', 'ketua_kelas'])) return;
        $this->class->members()->attach($this->newMemberId, ['role' => $this->newMemberRole, 'is_active' => true]);
        $this->refreshMembers();
        $this->newMemberId = null;
    }

    public function removeMember($userId)
    {
        $member = $this->class->members()->where('user_id', $userId)->first();
        if (!$member) return;
        $role = $member->pivot->role;
        // Tidak boleh hapus wali kelas/ketua kelas
        if (in_array($role, ['guru', 'ketua_kelas'])) return;
        $this->class->members()->detach($userId);
        $this->refreshMembers();
    }

    public function updateWaliKelas()
    {
        if (!$this->editWaliKelasId) return;
        // Pastikan user adalah guru
        $guru = $this->allTeachers->firstWhere('id', $this->editWaliKelasId);
        if (!$guru) return;
        // Hapus wali kelas lama
        $this->class->members()->wherePivot('role', 'guru')->detach();
        // Tambahkan wali kelas baru
        $this->class->members()->syncWithoutDetaching([$this->editWaliKelasId => ['role' => 'guru', 'is_active' => true]]);
        $this->refreshMembers();
    }

    public function updateKetuaKelas()
    {
        if (!$this->editKetuaKelasId) return;
        // Pastikan user adalah siswa
        $siswa = $this->allStudents->firstWhere('id', $this->editKetuaKelasId);
        if (!$siswa) return;
        // Hapus ketua kelas lama
        $this->class->members()->wherePivot('role', 'ketua_kelas')->detach();
        // Tambahkan ketua kelas baru
        $this->class->members()->syncWithoutDetaching([$this->editKetuaKelasId => ['role' => 'ketua_kelas', 'is_active' => true]]);
        $this->refreshMembers();
    }

    public function render()
    {
        return view('livewire.class-detail');
    }
} 