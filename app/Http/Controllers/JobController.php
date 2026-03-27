<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class JobController extends Controller
{
    // Admin: List
    public function adminIndex()
    {
        $jobs = JobVacancy::latest()->paginate(10);
        return view('admin.jobs.index', compact('jobs'));
    }

    // Admin: Create
    public function create()
    {
        return view('admin.jobs.form');
    }

    // Admin: Store
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'company'       => 'required|string|max:255',
            'location'      => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'content'       => 'nullable|string',
            'external_link' => 'nullable|url',
            'type'          => 'required|string',
            'status'        => 'required|in:active,inactive',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('jobs', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $job = JobVacancy::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Create Job Vacancy',
            'description' => 'Added job: ' . $job->title . ' at ' . $job->company,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan kerja berhasil ditambahkan.');
    }

    // Admin: Edit
    public function edit(JobVacancy $vacancy)
    {
        $job = $vacancy;
        return view('admin.jobs.form', compact('job'));
    }

    // Admin: Update
    public function update(Request $request, JobVacancy $vacancy)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'company'       => 'required|string|max:255',
            'location'      => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'content'       => 'nullable|string',
            'external_link' => 'nullable|url',
            'type'          => 'required|string',
            'status'        => 'required|in:active,inactive',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($vacancy->image) {
                $oldPath = str_replace('/storage/', '', $vacancy->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('jobs', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $vacancy->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Update Job Vacancy',
            'description' => 'Updated job: ' . $vacancy->title . ' at ' . $vacancy->company,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan kerja berhasil diperbarui.');
    }

    // Admin: Delete
    public function destroy(JobVacancy $vacancy)
    {
        if ($vacancy->image) {
            $oldPath = str_replace('/storage/', '', $vacancy->image);
            Storage::disk('public')->delete($oldPath);
        }
        $title = $vacancy->title;
        $vacancy->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Delete Job Vacancy',
            'description' => 'Deleted job: ' . $title,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return back()->with('success', 'Lowongan kerja berhasil dihapus.');
    }

    // Public: List
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = JobVacancy::where('status', 'active');

        // Search logic
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('company', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // Matching Logic (Simplistic AI/Preference)
        if ($user && $user->jurusan && $request->get('tab') === 'recommended') {
            $query->where('description', 'like', '%' . $user->jurusan . '%')
                  ->orWhere('title', 'like', '%' . $user->jurusan . '%');
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();
        
        // Count matches for badge
        $matchCount = 0;
        if ($user && $user->jurusan) {
            $matchCount = JobVacancy::where('status', 'active')
                ->where(function($q) use ($user) {
                    $q->where('description', 'like', '%' . $user->jurusan . '%')
                      ->orWhere('title', 'like', '%' . $user->jurusan . '%');
                })->count();
        }

        return view('jobs.index', compact('jobs', 'matchCount'));
    }

    // Public: Show
    public function show($slug)
    {
        $job = JobVacancy::where('slug', $slug)->firstOrFail();
        return view('jobs.show', compact('job'));
    }
}
