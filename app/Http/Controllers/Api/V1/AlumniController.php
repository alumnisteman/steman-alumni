<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AlumniService;
use App\Repositories\Contracts\AlumniRepositoryInterface;
use App\Http\Resources\AlumniResource;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    protected $alumniService;
    protected $alumniRepository;

    public function __construct(AlumniService $alumniService, AlumniRepositoryInterface $alumniRepository)
    {
        $this->alumniService = $alumniService;
        $this->alumniRepository = $alumniRepository;
    }

    /**
     * Display a listing of the alumni.
     */
    public function index(Request $request)
    {
        $alumni = $this->alumniRepository->getPaginatedAlumni($request->all(), 15);
        return AlumniResource::collection($alumni);
    }

    /**
     * Display the specified alumni.
     */
    public function show($identifier)
    {
        $user = $this->alumniRepository->findByIdentifier($identifier);

        if (!$user) {
            return response()->json(['message' => 'Alumni not found'], 404);
        }

        return new AlumniResource($user);
    }
}
