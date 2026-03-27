<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Major;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $majors = Major::orderBy('name')->get();
        return view('alumni.profile', compact('user', 'majors'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'jurusan' => 'nullable|string',
            'tahun_lulus' => 'nullable|integer',
            'pekerjaan_sekarang' => 'nullable|string',
            'alamat' => 'nullable|string',
            'bio' => 'nullable|string',
            'mentor_bio' => 'nullable|string',
            'mentor_expertise' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('foto_profil')) {
            // Delete old photo if it exists and is in the avatars directory
            if ($user->foto_profil && str_contains($user->foto_profil, 'avatars/')) {
                $oldPath = 'avatars/' . basename($user->foto_profil);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            // Store to public disk explicitly
            $path = $request->file('foto_profil')->store('avatars', 'public');
            $user->foto_profil = Storage::disk('public')->url($path);
        }

        $user->name = $data['name'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->jurusan = $data['jurusan'] ?? $user->jurusan;
        $user->tahun_lulus = $data['tahun_lulus'] ?? $user->tahun_lulus;
        $user->pekerjaan_sekarang = $data['pekerjaan_sekarang'] ?? $user->pekerjaan_sekarang;
        $user->alamat = $data['alamat'] ?? $user->alamat;
        $user->bio = $data['bio'] ?? $user->bio;
        $user->is_mentor = $request->has('is_mentor');
        $user->mentor_bio = $data['mentor_bio'] ?? $user->mentor_bio;
        $user->mentor_expertise = $data['mentor_expertise'] ?? $user->mentor_expertise;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
