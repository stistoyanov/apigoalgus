<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $currentUser = $request->user();
        $canManageSuperAdmins = $currentUser->canAccess('users.manage_super_admins');

        $roleFilter = $request->string('role')->trim()->toString();
        $search = $request->string('q')->trim()->toString();

        $superAdminRoleId = Role::query()->where('slug', Role::SUPER_ADMIN)->value('id');

        $users = User::query()
            ->with('role')
            ->where('id', '!=', $currentUser->id)
            ->unless($canManageSuperAdmins, fn ($q) => $q->where(function ($q) use ($superAdminRoleId) {
                $q->where('role_id', '!=', $superAdminRoleId)->orWhereNull('role_id');
            }))
            ->when($roleFilter !== '', function ($query) use ($roleFilter, $canManageSuperAdmins) {
                if (! $canManageSuperAdmins && $roleFilter === Role::SUPER_ADMIN) {
                    $query->whereRaw('1 = 0');

                    return;
                }
                $query->whereHas('role', fn ($q) => $q->where('slug', $roleFilter));
            })
            ->when($search !== '', fn ($q) => $q->where('email', 'like', '%'.$search.'%'))
            ->orderBy('email')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.users.index', [
            'users' => $users,
            'roles' => $this->visibleRoles($canManageSuperAdmins),
            'roleFilter' => $roleFilter,
            'search' => $search,
            'canManageSuperAdmins' => $canManageSuperAdmins,
        ]);
    }

    public function roles(Request $request): View
    {
        $canManageSuperAdmins = $request->user()->canAccess('users.manage_super_admins');
        $currentUserId = $request->user()->id;

        $query = Role::query()
            ->withCount(['users' => function ($q) use ($currentUserId) {
                $q->where('id', '!=', $currentUserId);
            }])
            ->with(['users' => function ($q) use ($currentUserId) {
                $q->where('id', '!=', $currentUserId)
                    ->orderBy('email')
                    ->select('id', 'name', 'email', 'role_id', 'is_active');
            }])
            ->orderBy('id');

        if (! $canManageSuperAdmins) {
            $query->where('slug', '!=', Role::SUPER_ADMIN);
        }

        return view('dashboard.users.roles', [
            'roles' => $query->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $canManageSuperAdmins = $request->user()->canAccess('users.manage_super_admins');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', self::passwordRule()],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id'),
                $this->disallowSuperAdminAssignmentRule($canManageSuperAdmins),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $validated['role_id'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('dashboard.users')
            ->with('status', 'User "'.$validated['email'].'" created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->assertEditable($request, $user);

        $canManageSuperAdmins = $request->user()->canAccess('users.manage_super_admins');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'password' => ['nullable', 'confirmed', self::passwordRule()],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id'),
                $this->disallowSuperAdminAssignmentRule($canManageSuperAdmins),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($user->isMaster()) {
            $superAdminId = Role::query()->where('slug', Role::SUPER_ADMIN)->value('id');
            if ((int) $validated['role_id'] !== (int) $superAdminId) {
                return back()->with('error', 'The master user must keep the super admin role.');
            }
        }

        $user->name = $validated['name'];
        $user->role_id = $validated['role_id'];

        if (! $user->isMaster()) {
            $user->is_active = (bool) ($validated['is_active'] ?? $user->is_active);
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('dashboard.users', $request->only('role', 'q', 'page'))
            ->with('status', 'User "'.$user->email.'" updated.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        $this->assertEditable($request, $user);

        if ($user->isMaster()) {
            return back()->with('error', 'The master user cannot be deactivated.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $label = $user->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('dashboard.users', $request->only('role', 'q', 'page'))
            ->with('status', 'User "'.$user->email.'" '.$label.'.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->assertEditable($request, $user);

        if ($user->isMaster()) {
            return back()->with('error', 'The master user cannot be deleted.');
        }

        $email = $user->email;
        $user->delete();

        return redirect()
            ->route('dashboard.users', $request->only('role', 'q'))
            ->with('status', 'User "'.$email.'" deleted.');
    }

    public static function passwordRule(): Password
    {
        return Password::min(8)->mixedCase()->numbers()->symbols();
    }

    private function visibleRoles(bool $canManageSuperAdmins)
    {
        $query = Role::query()->orderBy('id');

        if (! $canManageSuperAdmins) {
            $query->where('slug', '!=', Role::SUPER_ADMIN);
        }

        return $query->get();
    }

    /**
     * Forbid non-super-admins from assigning the super_admin role.
     */
    private function disallowSuperAdminAssignmentRule(bool $canManageSuperAdmins): \Closure
    {
        return function (string $attribute, $value, \Closure $fail) use ($canManageSuperAdmins) {
            if ($canManageSuperAdmins) {
                return;
            }

            $superAdminId = Role::query()->where('slug', Role::SUPER_ADMIN)->value('id');

            if ((int) $value === (int) $superAdminId) {
                $fail('You are not allowed to assign the super admin role.');
            }
        };
    }

    private function assertEditable(Request $request, User $user): void
    {
        if ($request->user()->is($user)) {
            abort(403, 'You cannot modify your own account here.');
        }

        if ($user->isSuperAdmin() && ! $request->user()->canAccess('users.manage_super_admins')) {
            abort(403, 'You do not have access to manage super admin accounts.');
        }
    }
}
