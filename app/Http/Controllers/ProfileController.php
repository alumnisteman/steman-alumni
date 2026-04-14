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

        if ($request->hasFile('profile_picture')) {
            // Delete old photo if it exists and is in the avatars directory
            if ($user->profile_picture && str_contains($user->profile_picture, 'avatars/')) {
                $oldPath = 'avatars/' . basename($user->profile_picture);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            $file = $request->file('profile_picture');
            $fileName = time() . '.webp';
            $path = 'avatars/' . $fileName;

            // Use InterventionImage if available for WebP compression (Modern CDN behavior)
            if (class_exists(\Intervention\Image\ImageManager::class)) {
                try {
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->read($file);
                    
                    // Resize to standard 800px (Anti-Bloat)
                    $image->scale(width: 800);
                    
                    // Convert to WebP with 80% quality
                    $encoded = $image->toWebp(80);
                    Storage::disk('public')->put($path, (string) $encoded);
                } catch (\Exception $e) {
                    // Fallback to standard storage if processing fails
                    $path = $file->store('avatars', 'public');
                }
            } else {
                // Fallback to standard storage
                $path = $file->storeAs('avatars', $fileName, 'public');
            }

            $user->profile_picture = '/storage/' . $path;
        }

        $user->name = $data['name'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->major = $data['major'] ?? $user->major;
        $user->graduation_year = $data['graduation_year'] ?? $user->graduation_year;
        $user->current_job = $data['current_job'] ?? $user->current_job;
        $user->address = $data['address'] ?? $user->address;
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
