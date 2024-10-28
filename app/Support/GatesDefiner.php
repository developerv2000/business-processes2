<?php

namespace App\Support;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class GatesDefiner
{
    public static function defineAll()
    {
        // Full access for admins
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdministrator()) {
                return true;
            }
        });

        /*
        |--------------------------------------------------------------------------
        | View gates
        |--------------------------------------------------------------------------
        */

        // Gates for analytics department
        Gate::define('view-epp', function ($user) {
            return $user->hasPermission('can view EPP');
        });

        Gate::define('view-kvpp', function ($user) {
            return $user->hasPermission('can view KVPP');
        });

        Gate::define('view-ivp', function ($user) {
            return $user->hasPermission('can view IVP');
        });

        Gate::define('view-vps', function ($user) {
            return $user->hasPermission('can view VPS');
        });

        Gate::define('view-meetings', function ($user) {
            return $user->hasPermission('can view Meetings');
        });

        Gate::define('view-kpe', function ($user) {
            return $user->hasPermission('can view KPE');
        });

        Gate::define('view-spg', function ($user) {
            return $user->hasPermission('can view SPG');
        });

        // Gates for logistician department
        Gate::define('view-applications', function ($user) {
            return $user->hasPermission('can view Applications');
        });

        Gate::define('view-orders', function ($user) {
            return $user->hasPermission('can view Orders');
        });

        // Gates for dashboard part
        Gate::define('view-users', function ($user) {
            return $user->hasPermission('can view users');
        });

        Gate::define('view-differents', function ($user) {
            return $user->hasPermission('can view differents');
        });

        Gate::define('view-roles', function ($user) {
            return $user->hasPermission('can view roles');
        });

        /*
        |--------------------------------------------------------------------------
        | Edit gates
        |--------------------------------------------------------------------------
        */

        // Gates for analytics department
        Gate::define('edit-epp', function ($user) {
            return $user->hasPermission('can edit EPP');
        });

        Gate::define('edit-kvpp', function ($user) {
            return $user->hasPermission('can edit KVPP');
        });

        Gate::define('edit-ivp', function ($user) {
            return $user->hasPermission('can edit IVP');
        });

        Gate::define('edit-vps', function ($user) {
            return $user->hasPermission('can edit VPS');
        });

        Gate::define('edit-meetings', function ($user) {
            return $user->hasPermission('can edit Meetings');
        });

        Gate::define('edit-spg', function ($user) {
            return $user->hasPermission('can edit SPG');
        });

        // Gates for logistician department
        Gate::define('edit-applications', function ($user) {
            return $user->hasPermission('can edit Applications');
        });

        Gate::define('edit-orders', function ($user) {
            return $user->hasPermission('can edit Orders');
        });

        // Gates for dashboard part
        Gate::define('edit-users', function ($user) {
            return $user->hasPermission('can edit users');
        });

        Gate::define('edit-differents', function ($user) {
            return $user->hasPermission('can edit differents');
        });

        /*
        |--------------------------------------------------------------------------
        | Other permission gates
        |--------------------------------------------------------------------------
        */

        Gate::define('export-as-excel', function ($user) {
            return $user->hasPermission('can export as excel');
        });

        Gate::define('export-unlimited-excel', function ($user) {
            return $user->hasPermission('can export unlimited records as excel');
        });

        Gate::define('view-all-analysts-processes', function ($user) {
            return $user->hasPermission('can view all analysts processes');
        });

        Gate::define('edit-all-analysts-processes', function ($user) {
            return $user->hasPermission('can edit all analysts processes');
        });

        Gate::define('delete-from-trash', function ($user) {
            return $user->hasPermission('can delete from trash');
        });

        Gate::define('edit-comments', function ($user) {
            return $user->hasPermission('can edit comments');
        });

        Gate::define('control-spg-processes', function ($user) {
            return $user->hasPermission('can control SPG processes');
        });

        Gate::define('edit-processes-status-history', function ($user) {
            return $user->hasPermission('can edit processes status history');
        });

        Gate::define('view-kvpp-coincident-processes', function ($user) {
            return $user->hasPermission('can view kvpp coincident processes');
        });

        Gate::define('view-kpe-extended-version', function ($user) {
            return $user->hasPermission('can view KPE extended version');
        });

        Gate::define('view-kpe-of-all-analysts', function ($user) {
            return $user->hasPermission('can view KPE of all analysts');
        });

        Gate::define('upgrade-process-status-after-contract', function ($user) {
            return $user->hasPermission('can upgrade process status after contract');
        });

        Gate::define('recieve-notification-on-process-contract', function ($user) {
            return $user->hasPermission('can recieve notification on process contract');
        });

        Gate::define('send-processes-for-application', function ($user) {
            return $user->hasPermission('can send processes for application');
        });
    }
}
