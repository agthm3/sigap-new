<?php

namespace App\Http\Controllers;

use App\Repositories\KinerjaRepository;
use Illuminate\Http\Request;

class SigapFeedController extends Controller
{
    public function create(KinerjaRepository $repo)
    {
        // Menarik data kinerja yang sama persis seperti di SIGAP Story
        $kinerjaItems = $repo->paginateForIndex([], 50)->items();
        return view('kinerja.feed', compact('kinerjaItems'));
    }
}