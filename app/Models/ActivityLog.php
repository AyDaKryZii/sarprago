<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'log_name',
        'description',
        'user_id',
        'username',
        'subject_id',
        'subject_type',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'json',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function log(Model $subject, string $description, string $logName = 'System', array $properties = [])
    {
        $user = Auth::user();

        return self::create([
            'log_name' => $logName,
            'description' => $description,
            'user_id' => $user?->id,
            'username' => $user?->name ?? 'System/Guest',
            'subject_id' => $subject->getKey(),
            'subject_type' => $subject->getMorphClass(),
            'properties' => $properties,
            'ip_address' => request()->ip(),
        ]);
    }
}