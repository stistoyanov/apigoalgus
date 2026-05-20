<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserActivityController extends Controller
{
    public function index(Request $request): View
    {
        $currentUser = $request->user();
        $canManageSuperAdmins = $currentUser->canAccess('users.manage_super_admins');

        $userFilter = (int) $request->integer('user_id');
        $actionFilter = $request->string('action')->trim()->toString();
        $search = $request->string('q')->trim()->toString();

        $superAdminRoleId = Role::query()->where('slug', Role::SUPER_ADMIN)->value('id');

        $query = UserActivity::query()
            ->with(['user' => fn ($q) => $q->select('id', 'name', 'email', 'role_id')])
            ->latest('created_at')
            ->latest('id');

        if (! $canManageSuperAdmins && $superAdminRoleId !== null) {
            // Admins must not see super admin activity.
            $query->where(function ($q) use ($superAdminRoleId) {
                $q->whereNull('user_id')
                    ->orWhereHas('user', fn ($qq) => $qq->where('role_id', '!=', $superAdminRoleId)->orWhereNull('role_id'));
            });
        }

        if ($userFilter > 0) {
            $query->where('user_id', $userFilter);
        }

        if ($actionFilter !== '') {
            $query->where('action', $actionFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('ip_address', 'like', '%'.$search.'%');
            });
        }

        $activities = $query->paginate(25)->withQueryString();

        $usersForFilter = User::query()
            ->when(! $canManageSuperAdmins && $superAdminRoleId !== null, function ($q) use ($superAdminRoleId) {
                $q->where(function ($qq) use ($superAdminRoleId) {
                    $qq->where('role_id', '!=', $superAdminRoleId)->orWhereNull('role_id');
                });
            })
            ->orderBy('email')
            ->get(['id', 'name', 'email']);

        return view('dashboard.users.activities', [
            'activities' => $activities,
            'usersForFilter' => $usersForFilter,
            'actionLabels' => UserActivity::actionLabels(),
            'userFilter' => $userFilter,
            'actionFilter' => $actionFilter,
            'search' => $search,
        ]);
    }
}
