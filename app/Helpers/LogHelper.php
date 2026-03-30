<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class LogHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function format(Model $model, array $ignoredFields = [])
    {
        $defaultIgnored = ['updated_at', 'created_at', 'remember_token', 'password'];
        $ignored = array_merge($defaultIgnored, $ignoredFields);
        
        $changes = $model->getChanges();
        $actualChanges = array_diff_key($changes, array_flip($ignored));
        
        $properties = [];
        foreach ($actualChanges as $field => $newValue) {
            $oldValue = $model->getOriginal($field);
            $properties[$field] = "from: '{$oldValue}' to: '{$newValue}'";
        }

        return $properties;
    }
}
