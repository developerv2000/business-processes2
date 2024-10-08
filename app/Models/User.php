<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Support\Helper;
use App\Support\Traits\MergesParamsToRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use MergesParamsToRequest;

    const DEFAULT_THEME = 'dark';
    const DEFAULT_LOCALE_NAME = 'ru';
    const DEFAULT_SHRINK_BODY_WIDTH = false;

    const DEFAULT_ORDER_BY = 'name';
    const DEFAULT_ORDER_TYPE = 'asc';
    const DEFAULT_PAGINATION_LIMIT = 50;

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

    protected $with = [
        'roles',
        'permissions',
        'responsibleCountries',
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

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function responsibleCountries()
    {
        return $this->belongsToMany(Country::class, 'user_responsible_country');
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
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::deleting(function ($instance) {
            $instance->roles()->detach();
            $instance->permissions()->detach();
            $instance->responsibleCountries()->detach();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Roles Check
    |--------------------------------------------------------------------------
    */

    public function isAdministrator()
    {
        return $this->hasRole(Role::ADMINISTRATOR_NAME);
    }

    public function isNotAdministrator()
    {
        return $this->hasRole(Role::ADMINISTRATOR_NAME);
    }

    public function isModerator()
    {
        return $this->hasRole(Role::MODERATOR_NAME);
    }

    public function isInactive()
    {
        return $this->hasRole(Role::INACTIVE_NAME);
    }

    public function isGuest()
    {
        return $this->hasRole(Role::GUEST_NAME);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOnlyAdministrators()
    {
        return $this->whereRelation('roles', 'name', Role::ADMINISTRATOR_NAME);
    }

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

    /**
     * Get finalized records based on the request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Query\Builder|null $query
     * @param string $finaly
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function getRecordsFinalized($request, $query = null, $finaly = 'paginate')
    {
        // If no query is provided, create a new query instance
        $query = $query ?: self::query();

        // Get the finalized records based on the specified finaly option
        $records = self::finalizeRecords($request, $query, $finaly);

        return $records;
    }

    /**
     * Finalize the query based on the request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $finaly
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function finalizeRecords($request, $query, $finaly)
    {
        // Apply sorting based on request parameters
        $records = $query
            ->orderBy($request->orderBy, $request->orderType)
            ->orderBy('id', $request->orderType);

        // Handle different finaly options
        switch ($finaly) {
            case 'paginate':
                // Paginate the results
                $records = $records
                    ->paginate($request->paginationLimit, ['*'], 'page', $request->page)
                    ->appends($request->except(['page', 'reversedSortingUrl']));
                break;

            case 'get':
                // Retrieve all records without pagination
                $records = $records->get();
                break;

            case 'query':
                // No additional action needed for 'query' option
                break;
        }

        return $records;
    }

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    public function hasPermission($permission)
    {
        return $this->permissions->contains('name', $permission);
    }


    /**
     * Create a new user instance from the request data.
     * This method is used only from the dashboard.
     *
     * @param \Illuminate\Http\Request $request The request object containing the user data.
     * @return static
     */
    public static function createFromRequest($request)
    {
        // Create a new user instance
        $instance = self::create($request->all());

        // Attach belongsToMany associations
        $instance->roles()->attach($request->input('roles'));
        $instance->permissions()->attach($request->input('permissions'));
        $instance->responsibleCountries()->attach($request->input('responsibleCountries'));

        // Load default settings for the user
        $instance->resetDefaultSettings();

        // Upload user's photo if provided
        $instance->uploadPhoto($request);
    }

    /**
     * Update the user's profile based on the request data.
     * This method is used by users to update their own profile via the profile edit page.
     *
     * @param \Illuminate\Http\Request $request The request object containing the profile data.
     * @return void
     */
    public function updateProfile($request): void
    {
        // Update the user's profile
        $this->update($request->validated());
        // Upload user's photo if provided
        $this->uploadPhoto($request);
    }

    /**
     * Update the user's password from the profile edit page.
     *
     * @param \Illuminate\Http\Request $request The request object containing the new password.
     * @return void
     */
    public function updateProfilePassword($request): void
    {
        // Update the user's password with the new hashed password
        $this->update([
            'password' => bcrypt($request->new_password),
        ]);

        // Logout other devices using the new password
        Auth::logoutOtherDevices($request->new_password);
    }

    /**
     * Update the user's profile and roles based on the request data.
     * This method is used by admins to update user profiles via the dashboard.
     *
     * @param \Illuminate\Http\Request $request The request object containing the profile and role data.
     * @return void
     */
    public function updateByAdmin($request): void
    {
        // Update the user's profile
        $this->update($request->validated());
        // Update responsible countries
        $this->responsibleCountries()->sync($request->input('responsibleCountries'));
        // Update user's roles
        $this->updateRoles($request);
        // Upload user's photo if provided
        $this->uploadPhoto($request);
    }

    /**
     * Update the user's password from the dashboard.
     *
     * @param \Illuminate\Http\Request $request The request object containing the new password.
     * @return void
     */
    public function updatePasswordByAdmin($request): void
    {
        // Update the user's password with the new hashed password
        $this->update([
            'password' => bcrypt($request->new_password),
        ]);

        // Laravel automatically logouts user, while updating its password
        // Manually logout user from all devices by cleaning session, if not current users password is being changed
        if (Auth::user()->id != $this->id) {
            $this->logoutByClearingSession();
        }
    }

    /**
     * Upload the user's photo if provided in the request.
     * This method is used in both the user profile edit and dashboard users edit pages.
     *
     * @param \Illuminate\Http\Request $request The request object containing the photo data.
     * @return void
     */
    private function uploadPhoto($request): void
    {
        // Check if photo file is present in the request
        if (!$request->hasFile('photo')) return;

        // Upload and resize user's photo
        Helper::uploadModelFile($this, 'photo', Helper::generateSlug($this->name), public_path(self::PHOTO_PATH));
        Helper::resizeImage($this->photo_path, self::PHOTO_WIDTH, self::PHOTO_HEIGHT);
    }

    /**
     * Update the user's roles based on the request data.
     *
     * @param \Illuminate\Http\Request $request The request object containing role data.
     * @return void
     */
    private function updateRoles($request): void
    {
        // Get the user's current roles
        $oldRoles = $this->roles()->pluck('id')->toArray();
        // Get the new roles from the request
        $newRoles = $request->input('roles');

        // Sync the user's roles with the new roles
        $this->roles()->sync($newRoles);

        // Check if there is any difference between the old and new roles
        if (count(array_diff($oldRoles, $newRoles)) || count(array_diff($newRoles, $oldRoles))) {
            // Reload the default settings if roles have been changed
            $this->resetDefaultSettings();

            // Laravel automatically logouts user, while updating its password
            // Manually logout user from all devices by cleaning session, if not current users password is being changed
            if (Auth::user()->id != $this->id) {
                $this->logoutByClearingSession();
            }
        }
    }

    public function updatePermissions($request)
    {
        $this->permissions()->sync($request->input('permissions', []));
        $this->resetDefaultSettings();
    }

    /**
     * Logout the user by clearing session records from the database.
     *
     * @return void
     */
    private function logoutByClearingSession(): void
    {
        // Delete all sessions for the current user
        DB::table('sessions')->where('user_id', $this->id)->delete();
    }

    /**
     * Not used yet!!!
     * Handle the logout and re-login process manually when an admin updates a user's password.
     *
     * @param string $password The new password.
     * @return void
     */
    private function logoutByCurrentAdminManually(string $password): void
    {
        // Get the currently authenticated admin user
        $currentAdmin = Auth::user();

        // Logout the current admin user
        Auth::logout();

        // Login the user whose password is being updated
        Auth::login($this);

        // Logout the user from all other devices using the new password
        Auth::logoutOtherDevices($password);

        // Re-login the current admin user
        Auth::login($currentAdmin);

        // Regenerate the session to prevent session fixation attacks
        Session::regenerate();
    }

    /**
     * Used after creating & updating users by admin
     *
     * Empty settings is used for Robots
     */
    public function resetDefaultSettings(): void
    {
        // Refresh user because roles may have been updated
        $this->refresh();

        if ($this->isInactive()) {
            $this->update(['settings' => null]);
            return;
        }

        $settings = [
            'theme' => User::DEFAULT_THEME,
            'locale' => User::DEFAULT_LOCALE_NAME,
            'shrink_body_width' => User::DEFAULT_SHRINK_BODY_WIDTH,
        ];

        $settings['manufacturers_table_columns'] = Manufacturer::getDefaultTableColumns();
        $settings['products_table_columns'] = Product::getDefaultTableColumns();
        $settings['processes_table_columns'] = Process::getDefaultTableColumnsForUser($this);
        $settings['kvpp_table_columns'] = Kvpp::getDefaultTableColumnsForUser($this);
        $settings['meetings_table_columns'] = Meeting::getDefaultTableColumns();

        $this->update(['settings' => $settings]);
    }

    /**
     * Reset default settings for all users
     *
     * Used in artisan command line
     */
    public static function resetDefaultSettingsForAll()
    {
        self::all()->each(function ($user) {
            $user->resetDefaultSettings();
        });
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

    /**
     * Collects all table columns for a given key from user settings.
     *
     * @param  string  $key
     * @return \Illuminate\Support\Collection
     */
    public function collectAllTableColumns($key): Collection
    {
        return collect($this->settings[$key])->sortBy('order');
    }

    /**
     * Filters out only the visible columns from the provided collection.
     *
     * @param  \Illuminate\Support\Collection  $columns
     * @return array
     */
    public static function filterOnlyVisibleColumns($columns): array
    {
        return $columns->where('visible', 1)->sortBy('order')->values()->all();
    }

    /**
     * Delete the user by admin, throwing an error if the user is currently in use.
     *
     * @throws ValidationException
     */
    public function deleteByAdmin()
    {
        if ($this->isCurrentlyInUse()) {
            throw ValidationException::withMessages([
                'user_deletion' => trans('validation.custom.users.is_in_use'),
            ]);
        }

        $this->delete();
    }

    /**
     * Check if the user is currently in use.
     *
     * @return bool
     */
    private function isCurrentlyInUse()
    {
        $isBdmInUse = Manufacturer::where('bdm_user_id', $this->id)->exists();
        $isAnalystInUse = Manufacturer::where('analyst_user_id', $this->id)->exists()
            || Kvpp::where('analyst_user_id', $this->id)->exists();

        return $isBdmInUse || $isAnalystInUse;
    }
}
