<?php

namespace App;

trait HasRolePermission
{
    public static function canViewAny(): bool
    {
        $roles = static::$allowedroles ?? [];

        if (empty($roles)) {
            return true;
        }

        return in_array(auth()->user()->role, $roles);
    }
}
