<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaBerkas;
use Illuminate\Support\Facades\Storage;

class BerkasViewController extends Controller
{
    public function download(PesertaBerkas $berkas)
    {
        if (!$berkas->file_path || !Storage::disk('local')->exists($berkas->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('local')->response($berkas->file_path);
    }
}
