<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class Access
{
    public static function allows(string $permission): bool
    {
        $user = Auth::user();

        if (! $user) {
            return true;
        }

        return $user->hasPermission($permission);
    }

    public static function denyIfCannot(string $permission): void
    {
        if (! self::allows($permission)) {
            abort(403, 'Anda tidak memiliki akses untuk aksi ini.');
        }
    }

    public static function allowsAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::allows($permission)) {
                return true;
            }
        }

        return false;
    }

    public static function denyIfCannotAny(array $permissions): void
    {
        if (! self::allowsAny($permissions)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }
    }
}
