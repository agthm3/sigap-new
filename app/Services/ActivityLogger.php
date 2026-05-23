<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityLogger
{
    public static function log(string $module, string $action, $object = null, array $extra = [], ?Request $request = null): ActivityLog
    {
        $user = Auth::user();

        $role = ($user && method_exists($user, 'getRoleNames'))
            ? ($user->getRoleNames()->first() ?? null)
            : ($extra['user_role'] ?? null);

        $userName = $user?->name ?? ($extra['user_name'] ?? 'Tamu');

        $objectType  = null;
        $objectId    = null;
        $objectTitle = null;
        $objectAlias = null;
        $sensitivity = null;

        if (is_object($object)) {
            $objectType  = get_class($object);
            $objectId    = $object->id ?? null;
            $objectTitle = $object->title ?? ($object->name ?? null);
            $objectAlias = $object->alias ?? null;
            $sensitivity = $object->sensitivity ?? null;
        } elseif (is_array($object)) {
            $objectType  = $object['type'] ?? null;
            $objectId    = $object['id'] ?? null;
            $objectTitle = $object['title'] ?? null;
            $objectAlias = $object['alias'] ?? null;
            $sensitivity = $object['sensitivity'] ?? null;
        }

        $req = $request ?? request();

        return ActivityLog::create([
            'user_id'      => $user?->id,
            'user_name'    => $userName,
            'user_role'    => $role,
            'module'       => $module,
            'action'       => $action,
            'object_type'  => $objectType,
            'object_id'    => $objectId,
            'object_title' => $objectTitle,
            'object_alias' => $objectAlias,
            'sensitivity'  => $sensitivity,
            'success'      => $extra['success'] ?? true,
            'reason'       => $extra['reason'] ?? null,
            'ip_address'   => $req?->ip(),
            'user_agent'   => substr($req?->userAgent() ?? '', 0, 1000),
        ]);
    }
}