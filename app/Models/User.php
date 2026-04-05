<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'nisn', 'tahun_lulus', 'jurusan',
        'pekerjaan_sekarang', 'perusahaan_universitas',
        'nomor_telepon', 'foto_profil',
        'alamat', 'bio',
        'social_id', 'social_type',
        'is_mentor', 'mentor_bio', 'mentor_expertise',
        'latitude', 'longitude', 'points',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'double',
            'longitude' => 'double',
            'points' => 'integer',
        ];
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class);
    }

    /**
     * Get Centralized Map Data & Statistics
     */
    public static function getMapAnalytics()
    {
        $alumniLocations = static::where('role', 'alumni')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['name', 'latitude', 'longitude', 'jurusan', 'tahun_lulus']);

        // Demo data with International presence if empty
        if ($alumniLocations->isEmpty()) {
            $cities = [
                ['name' => 'Jakarta',   'lat' => -6.2088,  'lng' => 106.8456, 'is_int' => false],
                ['name' => 'Surabaya',  'lat' => -7.2575,  'lng' => 112.7521, 'is_int' => false],
                ['name' => 'Makassar',  'lat' => -5.1476,  'lng' => 119.4327, 'is_int' => false],
                ['name' => 'Ternate',   'lat' =>  0.7901,  'lng' => 127.3820, 'is_int' => false],
                ['name' => 'Medan',     'lat' =>  3.5952,  'lng' =>  98.6722, 'is_int' => false],
                ['name' => 'Tokyo',     'lat' => 35.6762,  'lng' => 139.6503, 'is_int' => true],
                ['name' => 'Singapore', 'lat' =>  1.3521,  'lng' => 103.8198, 'is_int' => true],
                ['name' => 'Melbourne', 'lat' => -37.8136, 'lng' => 144.9631, 'is_int' => true],
                ['name' => 'London',    'lat' => 51.5074,  'lng' =>  -0.1278, 'is_int' => true],
            ];
            $alumniLocations = collect($cities)->map(function($city, $index) {
                return (object) [
                    'name' => 'Alumni ' . $city['name'],
                    'latitude' => $city['lat'],
                    'longitude' => $city['lng'],
                    'jurusan' => 'TKJ',
                    'tahun_lulus' => '2022',
                    'is_international' => $city['is_int']
                ];
            });
        }

        // Indonesia bounding box — easternmost point Merauke ~141.0°
        $idBounds = ['lat' => [-11, 6], 'lng' => [95, 141]];
        $nationalCount = 0;
        $internationalCount = 0;

        foreach ($alumniLocations as $loc) {
            $isID = ($loc->latitude >= $idBounds['lat'][0] && $loc->latitude <= $idBounds['lat'][1]) &&
                    ($loc->longitude >= $idBounds['lng'][0] && $loc->longitude <= $idBounds['lng'][1]);
            
            // Attach is_international so the frontend doesn't need to recalculate
            $loc->is_international = !$isID;

            if ($isID) {
                $nationalCount++;
            } else {
                $internationalCount++;
            }
        }

        return [
            'alumniLocations' => $alumniLocations,
            'nationalCount' => $nationalCount,
            'internationalCount' => $internationalCount
        ];
    }

    /**
     * Award points to user and check for new badges
     */
    public function awardPoints(int $amount)
    {
        $this->increment('points', $amount);
        
        // Refresh to get latest points
        $this->refresh();

        // Find badges that user qualifies for but doesn't have yet
        $eligibleBadges = Badge::where('points_required', '<=', $this->points)
            ->whereDoesntHave('users', function ($query) {
                $query->where('user_id', $this->id);
            })
            ->get();

        if ($eligibleBadges->isNotEmpty()) {
            $this->badges()->attach($eligibleBadges->pluck('id'));
            return true; // New badges awarded
        }

        return false;
    }
}
