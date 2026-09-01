<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

use App\Models\BuilderRevision;
use App\Models\PageDesign;
use App\Models\Project;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'onboarding_preferences',
        'onboarding_completed_at',
        'module_generation_status',
        'module_generation_started_at',
        'module_generation_completed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'              => 'datetime',
            'password'                       => 'hashed',
            'is_admin'                       => 'boolean',
            'onboarding_preferences'         => 'array',
            'onboarding_completed_at'        => 'datetime',
            'module_generation_started_at'   => 'datetime',
            'module_generation_completed_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the builder revisions for the user.
     */
    public function builderRevisions(): HasMany
    {
        return $this->hasMany(BuilderRevision::class);
    }

    public function pageDesigns(): HasMany
    {
        return $this->hasMany(PageDesign::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
