<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; background: #fff; }
    .page { padding: 20px 28px; }

    /* Header */
    .header { display: table; width: 100%; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 14px; }
    .header-logo { display: table-cell; width: 64px; vertical-align: middle; }
    .header-logo img { width: 56px; height: 56px; object-fit: contain; }
    .header-info { display: table-cell; vertical-align: middle; padding-left: 12px; }
    .header-info .school-name { font-size: 14px; font-weight: bold; color: #1e40af; }
    .header-info .school-sub { font-size: 10px; color: #555; margin-top: 2px; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 160px; }
    .doc-title { font-size: 13px; font-weight: bold; color: #1e40af; }
    .no-pendaftaran { font-size: 10px; color: #444; margin-top: 3px; }

    /* Section */
    .section { margin-bottom: 12px; border: 1px solid #d1d5db; border-radius: 4px; overflow: hidden; }
    .section-title { background: #1e40af; color: #fff; font-size: 10px; font-weight: bold; padding: 5px 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .section-body { padding: 8px 10px; }

    /* Grid 2-column */
    .grid2 { display: table; width: 100%; }
    .col { display: table-cell; width: 50%; vertical-align: top; padding: 2px 4px; }
    .col.full { width: 100%; display: block; padding: 2px 4px; }

    /* Field row */
    .field { margin-bottom: 5px; }
    .field-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 1px; }
    .field-value { font-size: 11px; color: #111; font-weight: 500; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; min-height: 16px; }
    .field-value.empty { color: #9ca3af; font-style: italic; font-weight: normal; }

    /* Berkas table */
    .berkas-table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .berkas-table th { background: #f3f4f6; padding: 4px 8px; text-align: left; border: 1px solid #e5e7eb; font-weight: 600; color: #374151; }
    .berkas-table td { padding: 4px 8px; border: 1px solid #e5e7eb; color: #374151; }
    .badge-ada { background: #dcfce7; color: #166534; padding: 1px 6px; border-radius: 99px; font-size: 9px; font-weight: 600; }
    .badge-belum { background: #fee2e2; color: #991b1b; padding: 1px 6px; border-radius: 99px; font-size: 9px; font-weight: 600; }

    /* Nilai table */
    .nilai-table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .nilai-table th { background: #f3f4f6; padding: 4px 6px; text-align: center; border: 1px solid #e5e7eb; font-weight: 600; color: #374151; }
    .nilai-table th.mapel { text-align: left; }
    .nilai-table td { padding: 3px 6px; border: 1px solid #e5e7eb; text-align: center; }
    .nilai-table td.mapel { text-align: left; }

    /* Footer */
    .footer { margin-top: 16px; border-top: 1px solid #d1d5db; padding-top: 10px; }
    .sign-row { display: table; width: 100%; }
    .sign-col { display: table-cell; width: 33%; text-align: center; vertical-align: top; padding: 0 8px; }
    .sign-label { font-size: 9px; color: #6b7280; margin-bottom: 40px; }
    .sign-line { border-top: 1px solid #374151; padding-top: 4px; font-size: 10px; font-weight: 600; color: #111; }
    .meta-info { font-size: 9px; color: #9ca3af; text-align: center; margin-top: 8px; }

    /* Status badge */
    .status-badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 9px; font-weight: 700; }
    .status-terverifikasi { background: #dcfce7; color: #166534; }
    .status-ditolak { background: #fee2e2; color: #991b1b; }
    .status-pending { background: #fef9c3; color: #854d0e; }

    /* Page break */
    .page-break { page-break-after: always; }
</style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-logo">
            @if($logoPath && file_exists(storage_path('app/public/' . $logoPath)))
                <img src="{{ storage_path('app/public/' . $logoPath) }}">
            @endif
        </div>
        <div class="header-info">
            <div class="school-name">{{ $namaSekolah }}</div>
            <div class="school-sub">Formulir Pendaftaran Peserta Didik Baru</div>
            <div class="school-sub">Tahun Penerimaan: {{ $peserta->tahunPenerimaan->label ?? $peserta->tahunPenerimaan->tahun }}</div>
        </div>
        <div class="header-right">
            <div class="doc-title">FORMULIR PPDB</div>
            <div class="no-pendaftaran">No. Pendaftaran:</div>
            <div style="font-size:12px;font-weight:bold;color:#1e40af;margin-top:2px;">{{ $peserta->no_pendaftaran ?? '-' }}</div>
            <div style="margin-top:4px;">
                @if($peserta->status_verifikasi === 'terverifikasi')
                    <span class="status-badge status-terverifikasi">TERVERIFIKASI</span>
                @elseif($peserta->status_verifikasi === 'ditolak')
                    <span class="status-badge status-ditolak">DITOLAK</span>
                @else
                    <span class="status-badge status-pending">MENUNGGU VERIFIKASI</span>
                @endif
            </div>
        </div>
    </div>

    {{-- DATA PENDAFTARAN --}}
    <div class="section">
        <div class="section-title">Data Pendaftaran</div>
        <div class="section-body">
            <div class="grid2">
                <div class="col">
                    <div class="field">
                        <div class="field-label">Jalur Pendaftaran</div>
                        <div class="field-value">{{ $peserta->jalur?->nama ?? '-' }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="field">
                        <div class="field-label">Program Keahlian (Jurusan)</div>
                        <div class="field-value">{{ $peserta->jurusan?->nama ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA DIRI --}}
    <div class="section">
        <div class="section-title">Data Diri Peserta</div>
        <div class="section-body">
            @php $diri = $peserta->dataDiri; @endphp
            <div class="grid2">
                <div class="col">
                    <div class="field">
                        <div class="field-label">Nama Lengkap</div>
                        <div class="field-value">{{ $diri?->nama_lengkap ?? '-' }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="field">
                        <div class="field-label">Jenis Kelamin</div>
                        <div class="field-value">{{ $diri?->jenis_kelamin === 'L' ? 'Laki-laki' : ($diri?->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div>
                    </div>
                </div>
            </div>
            @if(!$c || $c->isFieldActive('diri_nisn'))
            <div class="grid2">
                <div class="col full">
                    <div class="field">
                        <div class="field-label">NISN</div>
                        <div class="field-value {{ !$diri?->nisn ? 'empty' : '' }}">{{ $diri?->nisn ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
            </div>
            @endif
            <div class="grid2">
                @if(!$c || $c->isFieldActive('diri_tempat_lahir'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Tempat Lahir</div>
                        <div class="field-value {{ !$diri?->tempat_lahir ? 'empty' : '' }}">{{ $diri?->tempat_lahir ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_tanggal_lahir'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Tanggal Lahir</div>
                        <div class="field-value {{ !$diri?->tanggal_lahir ? 'empty' : '' }}">{{ $diri?->tanggal_lahir?->format('d F Y') ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            <div class="grid2">
                @if(!$c || $c->isFieldActive('diri_agama'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Agama</div>
                        <div class="field-value {{ !$diri?->agama ? 'empty' : '' }}">{{ $diri?->agama ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_no_hp'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">No. HP</div>
                        <div class="field-value {{ !$diri?->no_hp ? 'empty' : '' }}">{{ $diri?->no_hp ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            <div class="grid2">
                @if(!$c || $c->isFieldActive('diri_tinggi_badan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Tinggi Badan</div>
                        <div class="field-value {{ !$diri?->tinggi_badan ? 'empty' : '' }}">{{ $diri?->tinggi_badan ? $diri->tinggi_badan . ' cm' : 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_berat_badan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Berat Badan</div>
                        <div class="field-value {{ !$diri?->berat_badan ? 'empty' : '' }}">{{ $diri?->berat_badan ? $diri->berat_badan . ' kg' : 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            <div class="grid2">
                @if(!$c || $c->isFieldActive('diri_jumlah_saudara'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Jumlah Saudara</div>
                        <div class="field-value {{ $diri?->jumlah_saudara === null ? 'empty' : '' }}">{{ $diri?->jumlah_saudara !== null ? $diri->jumlah_saudara . ' orang' : 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('diri_asal_sekolah'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Asal Sekolah</div>
                        <div class="field-value {{ !($diri?->asalSekolah?->nama ?? $diri?->asal_sekolah_custom) ? 'empty' : '' }}">
                            {{ $diri?->asalSekolah?->nama ?? $diri?->asal_sekolah_custom ?? 'Tidak diisi' }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- DATA AYAH --}}
    <div class="section">
        <div class="section-title">Data Orang Tua — Ayah</div>
        <div class="section-body">
            @php $ayah = $peserta->dataAyah; @endphp
            <div class="grid2">
                <div class="col">
                    <div class="field">
                        <div class="field-label">Nama Ayah</div>
                        <div class="field-value">{{ $ayah?->nama ?? '-' }}</div>
                    </div>
                </div>
                @if(!$c || $c->isFieldActive('ayah_nik'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">NIK Ayah</div>
                        <div class="field-value {{ !$ayah?->nik ? 'empty' : '' }}">{{ $ayah?->nik ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            <div class="grid2">
                @if(!$c || $c->isFieldActive('ayah_tahun_lahir'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Tahun Lahir</div>
                        <div class="field-value {{ !$ayah?->tahun_lahir ? 'empty' : '' }}">{{ $ayah?->tahun_lahir ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_pendidikan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Pendidikan Terakhir</div>
                        <div class="field-value {{ !$ayah?->pendidikan ? 'empty' : '' }}">{{ $ayah?->pendidikan ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            <div class="grid2">
                @if(!$c || $c->isFieldActive('ayah_pekerjaan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Pekerjaan</div>
                        <div class="field-value {{ !$ayah?->pekerjaan ? 'empty' : '' }}">{{ $ayah?->pekerjaan ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ayah_penghasilan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Penghasilan per Bulan</div>
                        <div class="field-value {{ !$ayah?->penghasilan ? 'empty' : '' }}">{{ $ayah?->penghasilan ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('ayah_ketidakmampuan'))
            <div class="grid2">
                <div class="col full">
                    <div class="field">
                        <div class="field-label">Ketidakmampuan Khusus</div>
                        <div class="field-value {{ !$ayah?->ketidakmampuan_khusus ? 'empty' : '' }}">{{ $ayah?->ketidakmampuan_khusus ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- DATA IBU --}}
    <div class="section">
        <div class="section-title">Data Orang Tua — Ibu</div>
        <div class="section-body">
            @php $ibu = $peserta->dataIbu; @endphp
            <div class="grid2">
                <div class="col">
                    <div class="field">
                        <div class="field-label">Nama Ibu</div>
                        <div class="field-value">{{ $ibu?->nama ?? '-' }}</div>
                    </div>
                </div>
                @if(!$c || $c->isFieldActive('ibu_nik'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">NIK Ibu</div>
                        <div class="field-value {{ !$ibu?->nik ? 'empty' : '' }}">{{ $ibu?->nik ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            <div class="grid2">
                @if(!$c || $c->isFieldActive('ibu_tahun_lahir'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Tahun Lahir</div>
                        <div class="field-value {{ !$ibu?->tahun_lahir ? 'empty' : '' }}">{{ $ibu?->tahun_lahir ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_pendidikan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Pendidikan Terakhir</div>
                        <div class="field-value {{ !$ibu?->pendidikan ? 'empty' : '' }}">{{ $ibu?->pendidikan ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            <div class="grid2">
                @if(!$c || $c->isFieldActive('ibu_pekerjaan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Pekerjaan</div>
                        <div class="field-value {{ !$ibu?->pekerjaan ? 'empty' : '' }}">{{ $ibu?->pekerjaan ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('ibu_penghasilan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Penghasilan per Bulan</div>
                        <div class="field-value {{ !$ibu?->penghasilan ? 'empty' : '' }}">{{ $ibu?->penghasilan ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('ibu_ketidakmampuan'))
            <div class="grid2">
                <div class="col full">
                    <div class="field">
                        <div class="field-label">Ketidakmampuan Khusus</div>
                        <div class="field-value {{ !$ibu?->ketidakmampuan_khusus ? 'empty' : '' }}">{{ $ibu?->ketidakmampuan_khusus ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- DATA WALI --}}
    @if(!$c || $c->isFieldActive('data_wali'))
    @php $wali = $peserta->dataWali; @endphp
    @if($wali)
    <div class="section">
        <div class="section-title">Data Wali</div>
        <div class="section-body">
            <div class="grid2">
                <div class="col">
                    <div class="field">
                        <div class="field-label">Nama Wali</div>
                        <div class="field-value">{{ $wali->nama ?? '-' }}</div>
                    </div>
                </div>
                @if(!$c || $c->isFieldActive('wali_nik'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">NIK Wali</div>
                        <div class="field-value {{ !$wali->nik ? 'empty' : '' }}">{{ $wali->nik ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            <div class="grid2">
                @if(!$c || $c->isFieldActive('wali_tahun_lahir'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Tahun Lahir</div>
                        <div class="field-value {{ !$wali->tahun_lahir ? 'empty' : '' }}">{{ $wali->tahun_lahir ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('wali_pekerjaan'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Pekerjaan</div>
                        <div class="field-value {{ !$wali->pekerjaan ? 'empty' : '' }}">{{ $wali->pekerjaan ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('wali_penghasilan'))
            <div class="grid2">
                <div class="col full">
                    <div class="field">
                        <div class="field-label">Penghasilan per Bulan</div>
                        <div class="field-value {{ !$wali->penghasilan ? 'empty' : '' }}">{{ $wali->penghasilan ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
    @endif

    {{-- DATA ALAMAT --}}
    <div class="section">
        <div class="section-title">Alamat Tempat Tinggal</div>
        <div class="section-body">
            @php $alamat = $peserta->dataAlamat; @endphp
            <div class="grid2">
                <div class="col">
                    <div class="field">
                        <div class="field-label">RT / RW</div>
                        <div class="field-value">{{ ($alamat?->rt ?? '-') . ' / ' . ($alamat?->rw ?? '-') }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="field">
                        <div class="field-label">Kelurahan</div>
                        <div class="field-value">{{ $alamat?->kelurahan ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="grid2">
                <div class="col">
                    <div class="field">
                        <div class="field-label">Kecamatan</div>
                        <div class="field-value">{{ $alamat?->kecamatan ?? '-' }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="field">
                        <div class="field-label">Kota / Kabupaten</div>
                        <div class="field-value">{{ $alamat?->kota ?? '-' }}</div>
                    </div>
                </div>
            </div>
            @if(!$c || $c->isFieldActive('alamat_jarak'))
            <div class="grid2">
                <div class="col full">
                    <div class="field">
                        <div class="field-label">Jarak Tempat Tinggal ke Sekolah</div>
                        <div class="field-value {{ !$alamat?->jarak_tempat_tinggal ? 'empty' : '' }}">
                            {{ $alamat?->jarak_tempat_tinggal ? $alamat->jarak_tempat_tinggal . ' km' : 'Tidak diisi' }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- DATA KIP --}}
    @if(!$c || $c->isFieldActive('data_kip'))
    @php $kip = $peserta->dataKip; @endphp
    @if($kip && ($kip->no_kip || $kip->no_kps_pkh || $kip->nama_di_kip || $kip->terima_kip))
    <div class="section">
        <div class="section-title">Data KIP / PKH</div>
        <div class="section-body">
            @if(!$c || $c->isFieldActive('kip_terima'))
            <div class="grid2">
                <div class="col full">
                    <div class="field">
                        <div class="field-label">Penerima KIP</div>
                        <div class="field-value">{{ $kip->terima_kip ? 'Ya' : 'Tidak' }}</div>
                    </div>
                </div>
            </div>
            @endif
            <div class="grid2">
                @if(!$c || $c->isFieldActive('kip_no_kip'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Nomor KIP</div>
                        <div class="field-value {{ !$kip->no_kip ? 'empty' : '' }}">{{ $kip->no_kip ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
                @if(!$c || $c->isFieldActive('kip_no_kps_pkh'))
                <div class="col">
                    <div class="field">
                        <div class="field-label">Nomor KPS / PKH</div>
                        <div class="field-value {{ !$kip->no_kps_pkh ? 'empty' : '' }}">{{ $kip->no_kps_pkh ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
                @endif
            </div>
            @if(!$c || $c->isFieldActive('kip_nama_di_kip'))
            <div class="grid2">
                <div class="col full">
                    <div class="field">
                        <div class="field-label">Nama yang Tertera di KIP</div>
                        <div class="field-value {{ !$kip->nama_di_kip ? 'empty' : '' }}">{{ $kip->nama_di_kip ?? 'Tidak diisi' }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
    @endif

    {{-- NILAI RAPOR --}}
    @if((!$c || $c->isFieldActive('nilai_rapor')) && $nilaiRows->isNotEmpty())
    <div class="section">
        <div class="section-title">Nilai Rapor</div>
        <div class="section-body">
            <table class="nilai-table">
                <thead>
                    <tr>
                        <th class="mapel">Mata Pelajaran</th>
                        <th>Sem 1</th>
                        <th>Sem 2</th>
                        <th>Sem 3</th>
                        <th>Sem 4</th>
                        <th>Sem 5</th>
                        <th>Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nilaiRows as $row)
                    <tr>
                        <td class="mapel">{{ $row['mapel'] }}</td>
                        @for($s = 1; $s <= 5; $s++)
                        <td>{{ isset($row['semesters'][$s]) ? number_format($row['semesters'][$s], 1) : '-' }}</td>
                        @endfor
                        <td><strong>{{ $row['rata'] !== null ? number_format($row['rata'], 1) : '-' }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- BERKAS --}}
    <div class="section">
        <div class="section-title">Kelengkapan Berkas</div>
        <div class="section-body">
            <table class="berkas-table">
                <thead>
                    <tr>
                        <th>Jenis Berkas</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($berkasRows as $row)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td>
                            @if($row['ada'])
                                <span class="badge-ada">Sudah Upload</span>
                            @else
                                <span class="badge-belum">Belum</span>
                            @endif
                        </td>
                        <td>{{ $row['keterangan'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="sign-row">
            <div class="sign-col">
                <div class="sign-label">Peserta</div>
                <div class="sign-line">{{ $peserta->dataDiri?->nama_lengkap ?? '(Nama Peserta)' }}</div>
            </div>
            <div class="sign-col">
                <div class="sign-label">Orang Tua / Wali</div>
                <div class="sign-line">{{ $peserta->dataAyah?->nama ?? '(Nama Orang Tua)' }}</div>
            </div>
            <div class="sign-col">
                <div class="sign-label">Petugas PPDB</div>
                <div class="sign-line">.....................................</div>
            </div>
        </div>
        <div class="meta-info">
            Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB &nbsp;|&nbsp;
            No. Pendaftaran: {{ $peserta->no_pendaftaran ?? '-' }} &nbsp;|&nbsp;
            Dokumen ini adalah bukti pendaftaran resmi
        </div>
    </div>

</div>
</body>
</html>
