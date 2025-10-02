<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityLogger
{
    /**
     * Log aktivitas generik.
     * $module: 'dokumen' | 'pegawai' | 'inovasi' | 'riset' | 'other'
     * $action: 'view' | 'download' | 'create' | 'update' | 'delete' | 'access_denied'
     * $object: model instance atau array ['type'=>..., 'id'=>..., 'title'=>..., 'alias'=>..., 'sensitivity'=>...]
     * $extra:  ['reason'=>..., 'success'=>true/false]
     */
    public static function log(string $module, string $action, $object = null, array $extra = [], ?Request $request = null): ActivityLog
    {
        $user   = Auth::user();
        $role   = method_exists($user, 'getRoleNames') ? ($user?->getRoleNames()?->first() ?? null) : null;

        // Normalisasi objek
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
            'user_id'     => $user?->id,
            'user_name'   => $user?->name,
            'user_role'   => $role,
            'module'      => $module,
            'action'      => $action,
            'object_type' => $objectType,
            'object_id'   => $objectId,
            'object_title'=> $objectTitle,
            'object_alias'=> $objectAlias,
            'sensitivity' => $sensitivity,
            'success'     => $extra['success'] ?? true,
            'reason'      => $extra['reason'] ?? null,
            'ip_address'  => $req?->ip(),
            'user_agent'  => substr($req?->userAgent() ?? '', 0, 1000),
        ]);
    }
}
