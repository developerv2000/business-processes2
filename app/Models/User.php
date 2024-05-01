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

    public function getPhotoAssetPathAttribute()
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
        $settings['products_table_columns'] = $this->getDefaultProductsTableColumns();
        $settings['processes_table_columns'] = $this->getDefaultProcessTableColumns();

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
            ['name' => 'Product class', 'order' => $order++, 'width' => 126, 'visible' => 1],
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

    private function getDefaultProductsTableColumns(): array
    {
        $order = 1;

        return [
            ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            ['name' => 'Processes', 'order' => $order++, 'width' => 166, 'visible' => 1],
            ['name' => 'Category', 'order' => $order++, 'width' => 84, 'visible' => 1],
            ['name' => 'Country', 'order' => $order++, 'width' => 144, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Generic', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Form', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Basic form', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Dosage', 'order' => $order++, 'width' => 160, 'visible' => 1],
            ['name' => 'Pack', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'MOQ', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Shelf life', 'order' => $order++, 'width' => 92, 'visible' => 1],
            ['name' => 'Product class', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Dossier', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Zones', 'order' => $order++, 'width' => 54, 'visible' => 1],
            ['name' => 'Manufacturer Brand', 'order' => $order++, 'width' => 182, 'visible' => 1],
            ['name' => 'Bioequivalence', 'order' => $order++, 'width' => 124, 'visible' => 1],
            ['name' => 'Validity period', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'Registered in EU', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Sold in EU', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'Down payment', 'order' => $order++, 'width' => 132, 'visible' => 1],
            ['name' => 'Comments', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'BDM', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Analyst', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Update date', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'KVPP coincidents', 'order' => $order++, 'width' => 146, 'visible' => 1],
            ['name' => 'ID', 'order' => $order++, 'width' => 70, 'visible' => 1],
        ];
    }

    private function getDefaultProcessTableColumns()
    {
        $order = 1;

        $columns = [
            ['name' => 'Edit', 'order' => $order++, 'width' => 40, 'visible' => 1],
            ['name' => 'Status date', 'order' => $order++, 'width' => 98, 'visible' => 1],
            ['name' => 'Search country', 'order' => $order++, 'width' => 130, 'visible' => 1],
            // ['name' => 'Product status', 'order' => $order++, 'width' => 126, 'visible' => 1],
            // ['name' => 'Product status An*', 'order' => $order++, 'width' => 136, 'visible' => 1],
            // ['name' => 'General status', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'Category', 'order' => $order++, 'width' => 84, 'visible' => 1],
            ['name' => 'Manufacturer', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Country of manufacturer', 'order' => $order++, 'width' => 174, 'visible' => 1],
            ['name' => 'BDM', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Analyst', 'order' => $order++, 'width' => 142, 'visible' => 1],
            ['name' => 'Generic', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Form', 'order' => $order++, 'width' => 140, 'visible' => 1],
            ['name' => 'Dosage', 'order' => $order++, 'width' => 160, 'visible' => 1],
            ['name' => 'Pack', 'order' => $order++, 'width' => 110, 'visible' => 1],

            ['name' => 'MAH', 'order' => $order++, 'width' => 102, 'visible' => 1],
            ['name' => 'Comments', 'order' => $order++, 'width' => 106, 'visible' => 1],
            ['name' => 'Last comment', 'order' => $order++, 'width' => 240, 'visible' => 1],
            ['name' => 'Comments date', 'order' => $order++, 'width' => 116, 'visible' => 1],

            ['name' => 'Manufacturer price 1', 'order' => $order++, 'width' => 146, 'visible' => 1],
            ['name' => 'Manufacturer price 2', 'order' => $order++, 'width' => 148, 'visible' => 1],
            ['name' => 'Currency', 'order' => $order++, 'width' => 74, 'visible' => 1],
            ['name' => 'USD', 'order' => $order++, 'width' => 70, 'visible' => 1],
            ['name' => 'Agreed', 'order' => $order++, 'width' => 84, 'visible' => 1],
            ['name' => 'Our price 2', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Our price 1', 'order' => $order++, 'width' => 100, 'visible' => 1],
            ['name' => 'Price increased new price', 'order' => $order++, 'width' => 180, 'visible' => 1],
            ['name' => 'Price increased by%', 'order' => $order++, 'width' => 154, 'visible' => 1],
            ['name' => 'Date of price increased', 'order' => $order++, 'width' => 164, 'visible' => 1],

            ['name' => 'Shelf life', 'order' => $order++, 'width' => 76, 'visible' => 1],
            ['name' => 'MOQ', 'order' => $order++, 'width' => 140, 'visible' => 1],

            ['name' => 'Dossier status', 'order' => $order++, 'width' => 110, 'visible' => 1],
            ['name' => 'Year Cr/Be', 'order' => $order++, 'width' => 84, 'visible' => 1],
            ['name' => 'Countries Cr/Be', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'Country ich', 'order' => $order++, 'width' => 90, 'visible' => 1],
            ['name' => 'Zones', 'order' => $order++, 'width' => 54, 'visible' => 1],
            ['name' => 'Down payment 1', 'order' => $order++, 'width' => 124, 'visible' => 1],
            ['name' => 'Down payment 2', 'order' => $order++, 'width' => 124, 'visible' => 1],

            ['name' => 'Date of forecast', 'order' => $order++, 'width' => 136, 'visible' => 1],
            ['name' => 'Forecast 1 year', 'order' => $order++, 'width' => 112, 'visible' => 1],
            ['name' => 'Forecast 2 year', 'order' => $order++, 'width' => 112, 'visible' => 1],
            ['name' => 'Forecast 3 year', 'order' => $order++, 'width' => 112, 'visible' => 1],

            ['name' => 'Responsible', 'order' => $order++, 'width' => 120, 'visible' => 1],
            ['name' => 'Process date', 'order' => $order++, 'width' => 116, 'visible' => 1],
            ['name' => 'Days have passed!', 'order' => $order++, 'width' => 134, 'visible' => 1],
            ['name' => 'Brand Eng', 'order' => $order++, 'width' => 82, 'visible' => 1],
            ['name' => 'Brand Rus', 'order' => $order++, 'width' => 82, 'visible' => 1],
            ['name' => 'Date of creation', 'order' => $order++, 'width' => 138, 'visible' => 1],
            ['name' => 'Update date', 'order' => $order++, 'width' => 150, 'visible' => 1],
            ['name' => 'Product category', 'order' => $order++, 'width' => 126, 'visible' => 1],
        ];

        if ($this->isAdmin()) {
            array_push(
                $columns,
                ['name' => 'ВП', 'order' => $order++, 'width' => 200, 'visible' => 1],
                ['name' => 'ПО', 'order' => $order++, 'width' => 200, 'visible' => 1],
                ['name' => 'АЦ', 'order' => $order++, 'width' => 200, 'visible' => 1],
                ['name' => 'СЦ', 'order' => $order++, 'width' => 200, 'visible' => 1],
                ['name' => 'Кк', 'order' => $order++, 'width' => 200, 'visible' => 1],
            );
        }

        array_push($columns, ['name' => 'ID', 'order' => $order++, 'width' => 70, 'visible' => 1]);

        return $columns;
    }
}
