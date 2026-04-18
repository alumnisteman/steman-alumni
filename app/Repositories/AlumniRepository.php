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
    public function getPaginatedAlumni(array $filters = [], int $perPage = 12, array $withCount = []): LengthAwarePaginator
    {
        $query = User::with('badges')->whereIn('role', ['alumni', 'admin', 'editor']);

        if (!empty($withCount)) {
            $query->withCount($withCount);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['major'])) {
            $query->where('major', $filters['major']);
        }

        if (!empty($filters['angkatan'])) {
            $query->where('graduation_year', $filters['angkatan']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdentifier(string $identifier): ?User
    {
        return User::whereIn('role', ['alumni', 'admin', 'editor'])->where(function($q) use ($identifier) {
            $q->where('id', $identifier)
              ->orWhere('nisn', $identifier);
        })->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getGraduationYears()
    {
        return Cache::remember('alumni_graduation_years', 3600, function () {
            return User::where('role', 'alumni')
                ->whereNotNull('graduation_year')
                ->distinct()
                ->orderBy('graduation_year', 'desc')
                ->pluck('graduation_year');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?User
    {
        return User::whereIn('role', ['alumni', 'admin', 'editor'])->find($id);
    }
}
