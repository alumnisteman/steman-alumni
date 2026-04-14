<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Jobs\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'role' => 'required|in:' . implode(',', User::ROLES),
                'major' => 'nullable|string|max:255',
                'password' => 'required|string|min:4|confirmed',
            ]);

            // Security check: Editor cannot create Admin
            if (Auth::user()->role === 'editor' && $request->role === 'admin') {
                return back()->with('error', 'Editor tidak diperbolehkan membuat akun Admin.')->withInput();
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'major' => $request->major,
                'status' => 'approved',
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);

            LogActivity::dispatch(
                Auth::id(),
                'Create User',
                'Created ' . $user->role . ': ' . $user->name,
                $request->ip(),
                $request->header('User-Agent')
            );

            return back()->with('success', 'User ' . $user->name . ' berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin Create User Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambah user: ' . $e->getMessage())->withInput();
        }
    }

    public function index()
    {
        $users = User::latest()->paginate(20);
        $activeMajors = \App\Models\Major::orderBy('group')->orderBy('name')->get();
        return view('admin.users', compact('users', 'activeMajors'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'nisn' => 'nullable|string|max:20',
                'major' => 'nullable|string|max:255',
                'graduation_year' => 'nullable|integer',
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'bio' => 'nullable|string',
                'password' => 'nullable|string|min:4|confirmed',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->nisn = $request->nisn;
            $user->major = $request->major;
            $user->graduation_year = $request->graduation_year;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;
            $user->bio = $request->bio;

            if ($request->hasFile('profile_picture')) {
                // Disk cleanup removed for simplicity in this hardened version
                $path = $request->file('profile_picture')->store('avatars', 'public');
                $user->profile_picture = '/storage/' . $path;
            }

            if ($request->filled('password')) {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            $user->save();

            LogActivity::dispatch(
                Auth::id(),
                'Update User',
                'Updated profile for user: ' . $user->name,
                $request->ip(),
                $request->header('User-Agent')
            );

            return back()->with('success', 'Profil ' . $user->name . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin Update User Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui profil: ' . $e->getMessage())->withInput();
        }
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:' . implode(',', User::ROLES),
        ]);

        // Security check: Editor cannot promote to Admin
        if (Auth::user()->role === 'editor' && $request->role === 'admin') {
            return back()->with('error', 'Editor tidak diperbolehkan menaikkan role menjadi Admin.');
        }

        // Security check: Editor cannot demote Admin
        if (Auth::user()->role === 'editor' && $user->role === 'admin') {
            return back()->with('error', 'Editor tidak diperbolehkan mengubah role Admin.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengubah role Anda sendiri.');
        }

        $user->role = $request->role;
        $user->save();

        LogActivity::dispatch(
            Auth::id(),
            'Update User Role',
            'Updated role for user ' . $user->name . ' to ' . $user->role,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Role user berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        LogActivity::dispatch(
            Auth::id(),
            'Delete User',
            'Deleted user: ' . $name,
            request()->ip(),
            request()->header('User-Agent')
        );

        return back()->with('success', 'User berhasil dihapus.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,banned',
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa mengubah status Anda sendiri.');
        }

        $user->status = $request->status;
        $user->save();

        LogActivity::dispatch(
            Auth::id(),
            'Update User Status',
            'Updated status for user ' . $user->name . ' to ' . $user->status,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function verification()
    {
        $users = User::where('status', 'pending')->where('role', 'alumni')->latest()->paginate(20);
        return view('admin.users.verification', compact('users'));
    }
}
