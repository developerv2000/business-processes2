<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Support\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const DEFAULT_LOCALE_NAME = 'ru';
    const DEFAULT_SHRINK_BODY_WIDTH = false;

    const PHOTO_PATH = 'img/users';
    const PHOTO_WIDTH = 400;
    const PHOTO_HEIGHT = 400;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
        ];
    }

    protected $with = [
        'roles'
    ];

    // ********** Relations **********
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // ********** Roles Check **********
    /**
     * Robots can`t login
     */
    public function isRobot()
    {
        return $this->roles->contains('name', Role::ROBOT_NAME);
    }

    /**
     * Trainees can`t export any tables in excel format
     */
    public function isTrainee()
    {
        return $this->roles->contains('name', Role::TRAINEE_NAME);
    }

    /**
     * All privileges
     */
    public function isAdmin()
    {
        return $this->roles->contains('name', Role::ADMIN_NAME);
    }

    /**
     * Not realized yet
     */
    public function isModerator()
    {
        return $this->roles->contains('name', Role::MODERATOR_NAME);
    }

    /**
     * Not realized yet
     */
    public function isAdminOrModerator()
    {
        return $this->roles->contains(function ($role) {
            return $role->name == Role::ADMIN_NAME
                || $role->name == Role::MODERATOR_NAME;
        });
    }

    // ********** Scopes **********
    public function scopeOnlyBdms()
    {
        return $this->whereRelation('roles', 'name', Role::BDM_NAME);
    }

    public function scopeOnlyAnalysts()
    {
        return $this->whereRelation('roles', 'name', Role::ANALYST_NAME);
    }

    // ********** Miscellaneous **********
    /**
     * Used after creating & updating users by admin
     *
     * Empty settings is used for Robots
     */
    public function loadDefaultSettings()
    {
        // Refresh user because roles may have been updated
        $this->refresh();

        if ($this->isRobot()) {
            $this->update(['settings' => null]);
            return;
        }

        $settings = [
            'shrinkBodyWidth' => User::DEFAULT_SHRINK_BODY_WIDTH,
            'locale' => User::DEFAULT_LOCALE_NAME,
        ];

        $this->update(['settings' => $settings]);
    }

    /**
     * Update the specified setting for the user.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function updateSetting($key, $value): void
    {
        $settings = $this->settings;
        $settings[$key] = $value;

        $this->update(['settings' => $settings]);
    }

    public function updateFromRequest($request): void
    {
        $this->update($request->validated());
        $this->uploadPhoto($request);
    }

    private function uploadPhoto($request)
    {
        if (!$request->hasFile('photo')) return;

        Helper::uploadModelFile($this, 'photo', Helper::generateSlug($this->name), public_path(self::PHOTO_PATH));
        Helper::resizeImage($this->getPhotoPath(), self::PHOTO_WIDTH, self::PHOTO_HEIGHT);
    }

    private function getPhotoPath()
    {
        return public_path(self::PHOTO_PATH . '/' . $this->photo);
    }
}
