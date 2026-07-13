<?php

namespace App\Http\Controllers;

use App\Models\IgaAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SigapIgaController extends Controller
{
    public function index(Request $request)
    {
        $query = IgaAccount::query();

        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('opd', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        
        $totalOpd = IgaAccount::where('role', 'opd')->count();
        $totalUpt = IgaAccount::where('role', 'upt')->count();

        
        $akuns = $query->latest()->paginate(10)->withQueryString();

        return view('dashboard.iga.index', compact('akuns', 'totalOpd', 'totalUpt'));
    }

    public function store(Request $request)
    {
        
        if (!Auth::user()->hasAnyRole(['admin', 'verificator'])) {
            abort(403, 'Anda tidak memiliki izin untuk menambah akun.');
        }

        $request->validate([
            'role'         => 'required|in:opd,upt',
            'daerah'       => 'required|string|max:255',
            'opd'          => 'required|string|max:255',
            'username'     => 'required|string|max:255',
            'password_raw' => 'required|string|max:255',
        ]);

        IgaAccount::create([
            'role'         => $request->role,
            'daerah'       => $request->daerah,
            'opd'          => $request->opd,
            'username'     => $request->username,
            'password_raw' => $request->password_raw,
        ]);

        return back()->with('success', 'Akun IGA berhasil ditambahkan.');
    }
}