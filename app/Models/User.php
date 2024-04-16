<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Support\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Additional attributes
    |--------------------------------------------------------------------------
    */
    public function getPhotoPathAttribute()
    {
        return public_path(User::PHOTO_PATH . '/' . $this->photo);
    }

    public function getPhotoAssetsPathAttribute()
    {
        return asset(User::PHOTO_PATH . '/' . $this->photo);
    }

    /*
    |--------------------------------------------------------------------------
    | Roles Check
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeOnlyBdms()
    {
        return $this->whereRelation('roles', 'name', Role::BDM_NAME);
    }

    public function scopeOnlyAnalysts()
    {
        return $this->whereRelation('roles', 'name', Role::ANALYST_NAME);
    }

    /*
    |--------------------------------------------------------------------------
    | Querying
    |--------------------------------------------------------------------------
    */
    public static function getAnalystsMinified()
    {
        return self::onlyAnalysts()->select('id', 'name')->withOnly([])->get();
    }

    public static function getBdmsMinifed()
    {
        return self::onlyBdms()->select('id', 'name')->withOnly([])->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */
    public function updateFromRequest($request): void
    {
        $this->update($request->validated());
        $this->uploadPhoto($request);
    }

    public function updatePassword($request): void
    {
        $this->update([
            'password' => bcrypt($request->new_password),
        ]);

        Auth::logoutOtherDevices($request->new_password);
    }

    private function uploadPhoto($request): void
    {
        if (!$request->hasFile('photo')) return;

        Helper::uploadModelFile($this, 'photo', Helper::generateSlug($this->name), public_path(self::PHOTO_PATH));
        Helper::resizeImage($this->photo_path, self::PHOTO_WIDTH, self::PHOTO_HEIGHT);
    }

    /**
     * Used after creating & updating users by admin
     *
     * Empty settings is used for Robots
     */
    public function loadDefaultSettings(): void
    {
        // Refresh user because roles may have been updated
        $this->refresh();

        if ($this->isRobot()) {
            $this->update(['settings' => null]);
            return;
        }

        $settings = [
            'shrink_body_width' => User::DEFAULT_SHRINK_BODY_WIDTH,
            'locale' => User::DEFAULT_LOCALE_NAME,
        ];

        $settings['manufacturers_table_columns'] = $this->getDefaultManufacturersTableColumns();

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

    public function collectAllTableColumns($key): Collection
    {
        return collect($this->settings[$key])->sortBy('order');
    }

    public static function filterOnlyVisibleColumns($columns): array
    {
        return $columns->where('visible', 1)->sortBy('order')->values()->all();
    }

    private function getDefaultManufacturersTableColumns(): array
    {
        $order = 1;

        return [
            ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            ['name' => 'BDM', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Analyst', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Country', 'order' => $order++, 'width' => 144, 'visible' => 1],
            ['name' => 'IVP', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Category', 'order' => $order++, 'width' => 104, 'visible' => 1],
            ['name' => 'Status', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'Important', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Product category', 'order' => $order++, 'width' => 126, 'visible' => 1],
            ['name' => 'Zones', 'order' => $order++, 'width' => 54, 'visible' => 1],
            ['name' => 'Black list', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Presence', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Website', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'About company', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Relationship', 'order' => $order++, 'width' => 200, 'visible' => 1],
            ['name' => 'Comments', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Update date', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'Meetings', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'ID', 'order' => $order++, 'width' => 70, 'visible' => 1],
        ];
    }
}
