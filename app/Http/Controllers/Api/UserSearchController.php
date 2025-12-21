<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->q ?? '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        return User::role('employee')
            ->where('name', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id','name']);
    }
}
