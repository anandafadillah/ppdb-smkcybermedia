<?php

namespace App\Observers;

use App\Models\Peserta;
use App\Services\SeleksiService;

class PesertaObserver
{
    public function __construct(private SeleksiService $seleksiService) {}

    public function updated(Peserta $peserta): void
    {
        if ($peserta->wasChanged('status_formulir') && $peserta->status_formulir === 'submitted') {
            $this->seleksiService->hitungSkor($peserta);
        }
    }
}
