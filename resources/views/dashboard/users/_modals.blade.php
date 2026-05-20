<div class="modal" id="user-create-modal" hidden aria-hidden="true" role="dialog" aria-labelledby="user-create-title" data-open-on-load="{{ $openCreate ? '1' : '0' }}">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-dialog" role="document">
        <header class="modal-head">
            <h2 id="user-create-title">Create user</h2>
            <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
        </header>
        <form method="POST" action="{{ route('dashboard.users.store') }}" class="modal-form">
            @csrf

            <div class="field">
                <label for="create-name">Name</label>
                <input type="text" id="create-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label for="create-email">Email</label>
                <input type="email" id="create-email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label for="create-password">Password</label>
                <div class="input-with-action">
                    <input type="password" id="create-password" name="password" required autocomplete="new-password">
                    <button type="button" class="input-toggle" data-password-toggle="create-password" aria-label="Show password" aria-pressed="false">
                        @include('partials.eye-icons')
                    </button>
                </div>
                <p class="field-hint">Min 8 chars · upper + lower + digit + special.</p>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label for="create-password-confirm">Confirm password</label>
                <div class="input-with-action">
                    <input type="password" id="create-password-confirm" name="password_confirmation" required autocomplete="new-password">
                    <button type="button" class="input-toggle" data-password-toggle="create-password-confirm" aria-label="Show password" aria-pressed="false">
                        @include('partials.eye-icons')
                    </button>
                </div>
            </div>

            <div class="field">
                <label for="create-role">Role</label>
                <select id="create-role" name="role_id" required>
                    <option value="">Select a role…</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((int) old('role_id') === $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label class="checkbox-label">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1'))>
                    Account active
                </label>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Create user</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="user-edit-modal" hidden aria-hidden="true" role="dialog" aria-labelledby="user-edit-title">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-dialog" role="document">
        <header class="modal-head">
            <h2 id="user-edit-title">Edit user</h2>
            <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
        </header>
        <form method="POST" id="user-edit-form" class="modal-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="role" value="{{ $roleFilter ?? '' }}">
            <input type="hidden" name="q" value="{{ $search ?? '' }}">

            <div class="field">
                <label for="edit-name">Name</label>
                <input type="text" id="edit-name" name="name" required maxlength="120">
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" id="edit-email" disabled>
                <p class="field-hint">Email cannot be changed.</p>
            </div>

            <div class="field">
                <label for="edit-password">New password (leave blank to keep current)</label>
                <div class="input-with-action">
                    <input type="password" id="edit-password" name="password" autocomplete="new-password">
                    <button type="button" class="input-toggle" data-password-toggle="edit-password" aria-label="Show password" aria-pressed="false">
                        @include('partials.eye-icons')
                    </button>
                </div>
                <p class="field-hint">Min 8 chars · upper + lower + digit + special.</p>
            </div>

            <div class="field">
                <label for="edit-password-confirm">Confirm new password</label>
                <div class="input-with-action">
                    <input type="password" id="edit-password-confirm" name="password_confirmation" autocomplete="new-password">
                    <button type="button" class="input-toggle" data-password-toggle="edit-password-confirm" aria-label="Show password" aria-pressed="false">
                        @include('partials.eye-icons')
                    </button>
                </div>
            </div>

            <div class="field">
                <label for="edit-role">Role</label>
                <select id="edit-role" name="role_id" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <p class="field-hint" id="edit-role-hint" hidden>The master user must keep the super admin role.</p>
            </div>

            <div class="field">
                <label class="checkbox-label">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="edit-active" name="is_active" value="1">
                    Account active
                </label>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="user-delete-modal" hidden aria-hidden="true" role="alertdialog" aria-labelledby="user-delete-title">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-dialog modal-dialog-sm" role="document">
        <header class="modal-head">
            <h2 id="user-delete-title">Delete user?</h2>
            <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
        </header>
        <div class="modal-body">
            <p>This action cannot be undone. The user <strong id="delete-user-email"></strong> will be permanently removed.</p>
        </div>
        <form method="POST" id="user-delete-form">
            @csrf
            <input type="hidden" name="role" value="{{ $roleFilter ?? '' }}">
            <input type="hidden" name="q" value="{{ $search ?? '' }}">
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-danger">Delete permanently</button>
            </div>
        </form>
    </div>
</div>
