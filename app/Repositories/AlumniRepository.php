<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\AlumniRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class AlumniRepository implements AlumniRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPaginatedAlumni(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = User::with('badges')->where('role', 'alumni');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['jurusan'])) {
            $query->where('jurusan', $filters['jurusan']);
        }

        if (!empty($filters['angkatan'])) {
            $query->where('tahun_lulus', $filters['angkatan']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?User
    {
        return User::where('role', 'alumni')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdentifier(string $identifier): ?User
    {
        // Adjust depending on what your identifier field is (username, slug, etc)
        // Default to ID if it's numeric, otherwise check username
        if (is_numeric($identifier)) {
            return User::where('role', 'alumni')->find($identifier);
        }
        
        return User::where('role', 'alumni')->where(function($q) use ($identifier) {
            $q->where('username', $identifier)->orWhere('slug', $identifier);
        })->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getGraduationYears()
    {
        return Cache::remember('alumni_graduation_years', 3600, function () {
            return User::where('role', 'alumni')
                ->whereNotNull('tahun_lulus')
                ->distinct()
                ->orderBy('tahun_lulus', 'desc')
                ->pluck('tahun_lulus');
        });
    }
}
