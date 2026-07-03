<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('admin.managementuser', compact('users'));
    }

    /**
     * Menyimpan data pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:20', 'unique:users,nip'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['nullable', 'in:superadmin,admin,user'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP ini sudah terdaftar di sistem.',
            'nip.max' => 'NIP maksimal 20 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.in' => 'Role tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            User::create([
                'nip' => trim($validated['nip']),
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'] ?? 'user',
            ]);

            DB::commit();
            return redirect()->route('admin.user.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form tambah pengguna baru.
     */
    public function create()
    {
        return view('admin.formuser-clean');
    }

    /**
     * Menampilkan form edit untuk pengguna tertentu.
     */
    public function edit($nip)
    {
        $user = User::where('nip', $nip)->firstOrFail();

        return view('admin.formuser-clean', compact('user'));
    }

    /**
     * Memperbarui data pengguna di database.
     */
    public function update(Request $request, $nip)
    {
        $user = User::where('nip', $nip)->firstOrFail();

        $validated = $request->validate([
            'nip' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'nip')->ignore($user->nip, 'nip'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->nip, 'nip'),
            ],
            'role' => ['required', 'in:superadmin,admin,user'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP ini sudah terdaftar di sistem.',
            'nip.max' => 'NIP maksimal 20 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'nip' => trim($validated['nip']),
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'role' => $validated['role'],
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            DB::commit();
            return redirect()->route('admin.user.index')->with('success', 'Data pengguna berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus data pengguna dari database.
     */
    public function destroy($nip)
    {
        $user = User::where('nip', $nip)->firstOrFail();

        if (Auth::id() === $user->nip) {
            return redirect()->route('admin.user.index')->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.user.index')->with('success', 'Pengguna berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')->with('error', 'Gagal menghapus pengguna karena memiliki relasi data (misal: terkait dengan penugasan/laporan).');
        }
    }
}
