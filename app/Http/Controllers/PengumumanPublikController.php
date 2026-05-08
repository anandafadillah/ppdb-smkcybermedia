<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;

class PengumumanPublikController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::published()
            ->latest('tanggal_publish')
            ->paginate(10);

        return view('pengumuman.index', compact('pengumuman'));
    }

    public function show(Pengumuman $pengumuman)
    {
        abort_if($pengumuman->status !== 'published', 404);

        return view('pengumuman.show', compact('pengumuman'));
    }
}
