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

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use MergesParamsToRequest;

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

        // Attach roles to the user
        $instance->roles()->attach($request->input('roles'));

        // Load default settings for the user
        $instance->loadDefaultSettings();

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
    public function updateFromProfilePage($request): void
    {
        // Update the user's profile
        $this->update($request->validated());
        // Upload user's photo if provided
        $this->uploadPhoto($request);
    }

    /**
     * Update the user's profile and roles based on the request data.
     * This method is used by admins to update user profiles via the dashboard.
     *
     * @param \Illuminate\Http\Request $request The request object containing the profile and role data.
     * @return void
     */
    public function updateFromDashboard($request): void
    {
        // Update the user's profile
        $this->update($request->validated());
        // Update user's roles
        $this->updateRoles($request);
        // Upload user's photo if provided
        $this->uploadPhoto($request);
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
            $this->loadDefaultSettings();

            // Logout all devices by cleaing session if not the current user
            if (Auth::user()->id != $this->id) {
                $this->logoutByClearingSession();
            }
        }
    }

    /**
     * Update the user's password from the profile edit page.
     *
     * @param \Illuminate\Http\Request $request The request object containing the new password.
     * @return void
     */
    public function updatePasswordFromProfilePage($request): void
    {
        // Update the user's password with the new hashed password
        $this->update([
            'password' => bcrypt($request->new_password),
        ]);

        // Logout other devices using the new password
        Auth::logoutOtherDevices($request->new_password);
    }

    /**
     * Update the user's password from the dashboard.
     *
     * @param \Illuminate\Http\Request $request The request object containing the new password.
     * @return void
     */
    public function updatePasswordFromDashboard($request): void
    {
        // Update the user's password with the new hashed password
        $this->update([
            'password' => bcrypt($request->new_password),
        ]);

        // Logout all devices by cleaing session if not the current user
        if (Auth::user()->id != $this->id) {
            $this->logoutByClearingSession();
        }
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

        $settings['manufacturers_table_columns'] = Manufacturer::getDefaultTableColumns();
        $settings['products_table_columns'] = Product::getDefaultTableColumns();
        $settings['processes_table_columns'] = Process::getDefaultTableColumns();
        $settings['kvpp_table_columns'] = Kvpp::getDefaultTableColumns();
        $settings['meetings_table_columns'] = Meeting::getDefaultTableColumns();

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
}
