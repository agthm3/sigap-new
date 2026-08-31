<?php

namespace App\Http\Controllers;

use App\Models\SigapStoryLog;
use App\Repositories\KinerjaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SigapStoryController extends Controller
{
    public function create(KinerjaRepository $repo)
    {
        $kinerjaItems = $repo->paginateForIndex([], 50)->items();
        return view('kinerja.story', compact('kinerjaItems'));
    }

    public function index()
    {
        $logs = SigapStoryLog::latest()->paginate(12);
        return view('kinerja.story_log', compact('logs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'title' => 'nullable|string',
            'kinerja_id' => 'nullable|integer'
        ]);

        $base64Image = $request->input('image');
        
        // Bersihkan prefix data:image/...;base64,
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]); // jpg, jpeg, png
        } else {
            $type = 'jpg';
        }

        $decodedImage = base64_decode($base64Image);
        if ($decodedImage === false) {
            return response()->json(['success' => false, 'message' => 'Base64 decode failed'], 422);
        }

        $fileName = 'story_' . time() . '_' . Str::random(8) . '.' . $type;
        $filePath = 'stories/' . $fileName;

        Storage::disk('public')->put($filePath, $decodedImage);

        SigapStoryLog::create([
            'kinerja_id' => $request->kinerja_id,
            'user_id'    => auth()->id(),
            'title'      => $request->title ?: 'SIGAP Story',
            'image_path' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'url' => Storage::disk('public')->url($filePath)
        ]);
    }

    public function destroy($id)
    {
        $log = SigapStoryLog::findOrFail($id);

        // Hapus file fisik dari storage
        if ($log->image_path && Storage::disk('public')->exists($log->image_path)) {
            Storage::disk('public')->delete($log->image_path);
        }

        $log->delete();

        return back()->with('success', 'Riwayat story berhasil dihapus.');
    }

    /**
     * Hapus multiple/bulk stories
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:sigap_story_logs,id'
        ]);

        $logs = SigapStoryLog::whereIn('id', $request->ids)->get();

        foreach ($logs as $log) {
            if ($log->image_path && Storage::disk('public')->exists($log->image_path)) {
                Storage::disk('public')->delete($log->image_path);
            }
            $log->delete();
        }

        return back()->with('success', count($logs) . ' riwayat story berhasil dihapus.');
    }
}