<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const SUPER_ADMIN = 'super_admin';

    public const ADMIN = 'admin';

    public const MANAGER = 'manager';

    public const USER = 'user';

    public const VIEWER = 'viewer';

    public const SERVICE_ACCOUNT = 'service_account';

    public const OPERATOR = 'operator';

    public const SUPPORT = 'support';

    protected $fillable = ['slug', 'name', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
