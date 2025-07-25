<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::search(request('search'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.user.index', [
            'title' => 'Daftar Pengguna',
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = UserRole::options(except: ['ketua_kelas', 'wali_kelas']);

        return view('dashboard.user.form', [
            'title' => 'Tambah Pengguna',
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $roles = implode(',', UserRole::values(except: ['ketua_kelas', 'wali_kelas']));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|alpha_dash|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . $roles,
        ]);

        User::create($validated);
        return redirect()->route('user.index')->with('success', 'User berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = UserRole::options(except: ['ketua_kelas', 'wali_kelas']);

        return view('dashboard.user.form', [
            'title' => 'Edit Pengguna',
            'roles' => $roles,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $roles = implode(',', UserRole::values(except: ['ketua_kelas', 'wali_kelas']));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|alpha_dash|max:255|unique:users,username,' . $user->getKey(),
            'email' => 'required|email|unique:users,email,' . $user->getKey(),
            'role' => 'required|in:' . $roles,
        ]);

        $user->update($validated);
        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update($validated);
        return redirect()->route('user.index')->with('success', 'Password berhasil diperbarui.');
    }
}
