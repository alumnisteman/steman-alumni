<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

interface AlumniRepositoryInterface
{
    /**
     * Get paginated alumni with search and filters.
     *
     * @param array $filters
     * @param int $perPage
     * @param array $withCount
     * @return LengthAwarePaginator
     */
    public function getPaginatedAlumni(array $filters = [], int $perPage = 12, array $withCount = []): LengthAwarePaginator;

    /**
     * Find an alumni by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;
    
    /**
     * Find an alumni by Slug or Username.
     *
     * @param string $identifier
     * @return User|null
     */
    public function findByIdentifier(string $identifier): ?User;

    /**
     * Get cached graduation years for alumni.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getGraduationYears();
}
