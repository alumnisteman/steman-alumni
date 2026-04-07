<?php
namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Major;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Jobs\LogActivity;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $majors = Major::orderBy('group')->orderBy('name')->get();
        return view('alumni.profile', compact('user', 'majors'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

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
            $user->foto_profil = '/storage/' . $path;
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
        
        // Security: Only allow becoming a mentor if they already have sufficient points or status
        // For now, we allow the toggle but log it for admin review or add a point hurdle
        if ($request->has('is_mentor') && !$user->mentoring) {
            if (($user->points ?? 0) >= 50) {
                $user->mentoring = true;
            } else {
                return back()->with('error', 'Poin tidak cukup untuk menjadi Mentor (Min. 50 poin).');
            }
        } elseif (!$request->has('is_mentor')) {
            $user->mentoring = false;
        }

        $user->mentor_bio = $data['mentor_bio'] ?? $user->mentor_bio;
        $user->mentor_expertise = $data['mentor_expertise'] ?? $user->mentor_expertise;
        $user->save();
        
        LogActivity::dispatch(
            $user->id,
            'Update Profile',
            'User updated their profile.',
            request()->ip(),
            request()->header('User-Agent')
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
