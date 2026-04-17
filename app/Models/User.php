<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Searchable;

    /**
     * SINGLE SOURCE OF TRUTH FOR ROLES
     * Tambahkan role baru hanya di sini. Controller & View otomatis mengikuti.
     */
    const ROLES = ['admin', 'editor', 'alumni'];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (!$user->qr_login_token) {
                $user->qr_login_token = (string) \Illuminate\Support\Str::uuid();
            }
        });

        static::saving(function ($user) {
            // Auto-Geocoding logic: Trigger if address is provided AND (coords are empty OR address has changed)
            $addressChanged = $user->isDirty('address');
            $needsGeocode = !empty($user->address) && (empty($user->latitude) || empty($user->longitude) || $addressChanged);
            
            if ($needsGeocode) {
                \Illuminate\Support\Facades\Log::info('Geocoding triggered for user ' . $user->id . ' with address: ' . $user->address);
                try {
                    $aiService = app(\App\Services\AIService::class);
                    $coords = $aiService->geocode($user->address);
                    
                    if ($coords && $coords['lat'] && $coords['lng']) {
                        \Illuminate\Support\Facades\Log::info('Geocoding success for user ' . $user->id . ': ' . $coords['lat'] . ', ' . $coords['lng']);
                        $user->latitude = $coords['lat'];
                        $user->longitude = $coords['lng'];
                        // Optional: Extract city name if missing
                        if (empty($user->city_name) || $addressChanged) {
                            $user->city_name = explode(',', $user->address)[0];
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::warning('Geocoding returned null or invalid for user ' . $user->id);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Auto-Geocoding failed for user ' . $user->id . ': ' . $e->getMessage());
                }
            }
        });

        static::saved(function ($user) {
            // Automation: Clear all map and alumni caches when a user with coordinates is updated
            if ($user->latitude && $user->longitude) {
                app(\App\Services\AlumniService::class)->clearCache();
                \Illuminate\Support\Facades\Log::info('Alumni caches cleared due to user update: ' . $user->name);
            }
        });
    }

    /**
     * Roles yang boleh mengakses panel admin (dashboard, news, gallery, dll)
     */
    const ADMIN_PANEL_ROLES = ['admin', 'editor'];

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'status',
        'nisn', 'graduation_year', 'major',
        'current_job', 'company_university',
        'phone_number', 'profile_picture',
        'address', 'bio',
        'mentoring', 'mentor_expertise', 'mentor_bio', 'points',
        'linkedin_url', 'instagram_url', 'twitter_url',
        'latitude', 'longitude',
        'qr_login_token',
        'city_name', 'is_active', 'show_social',
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

    public function getIsMentorAttribute(): bool
    {
        return (bool) $this->mentoring;
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
            ->get(['name', 'latitude', 'longitude', 'major', 'graduation_year']);


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
    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    public function canAccessAdminPanel(): bool
    {
        return in_array($this->role, self::ADMIN_PANEL_ROLES);
    }

    public function dashboardUrl(): string
    {
        return $this->canAccessAdminPanel() ? '/admin/dashboard' : '/alumni/dashboard';
    }

    /**
     * Relationship with Program Registrations
     */
    public function programRegistrations()
    {
        return $this->hasMany(ProgramRegistration::class);
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'approved' && $this->role === 'alumni';
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        $array = [
            'id' => (int) $this->id,
            'name' => $this->name,
            'graduation_year' => (int) $this->graduation_year,
            'major' => $this->major,
            'current_job' => $this->current_job,
            'company_university' => $this->company_university,
            'location' => $this->address,
        ];

        // Add Meilisearch specific geo-filtering data
        if ($this->latitude && $this->longitude) {
            $array['_geo'] = [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }

        return $array;
    }

    /**
     * Social Links relationship
     */
    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class);
    }

    /**
     * Get specific social link by platform
     */
    public function getSocialUrl(string $platform): ?string
    {
        return $this->socialLinks->where('platform', $platform)->first()?->url;
    }

    /**
     * Health Profile Relationship
     */
    public function healthProfile()
    {
        return $this->hasOne(HealthProfile::class);
    }

    /**
     * Get estimated age based on graduation year (assuming age 18 at graduation)
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->graduation_year) {
            return null;
        }
        $currentYear = (int) date('Y');
        return $currentYear - (int) $this->graduation_year + 18;
    }

    /**
     * Check if user is estimated to be 40 or older
     */
    public function isOver40(): bool
    {
        $age = $this->age;
        return $age !== null && $age >= 40;
    }
    /**
     * Get consistent profile picture URL
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if (!$this->profile_picture) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&color=fff';
        }

        if (str_starts_with($this->profile_picture, 'http')) {
            return $this->profile_picture;
        }

        // Remove any leading /storage/ or storage/ to prevent double prefixing
        $path = preg_replace('#^/?storage/#', '', $this->profile_picture);
        
        return asset('storage/' . $path);
    }
}
