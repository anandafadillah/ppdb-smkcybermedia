<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormConfig extends Model
{
    use HasFactory;

    protected $table = 'form_configs';

    protected $fillable = ['tahun_penerimaan_id', 'field_configs', 'is_locked'];

    protected $casts = [
        'field_configs' => 'array',
        'is_locked'     => 'boolean',
    ];

    /**
     * Field yang selalu wajib aktif — tidak bisa dinonaktifkan admin.
     */
    const FIXED_FIELDS = [
        'jalur_id', 'jurusan_id',
        'nama_lengkap', 'jenis_kelamin',
        'ayah_nama', 'ibu_nama',
        'alamat_rt', 'alamat_rw', 'alamat_kelurahan', 'alamat_kecamatan', 'alamat_kota',
    ];

    /**
     * Field yang dapat dikonfigurasi, dikelompokkan per section.
     * Format: [section_key => ['label' => ..., 'fields' => [key => label]]]
     */
    const FIELD_GROUPS = [
        'data_diri' => [
            'label' => 'Data Diri',
            'fields' => [
                'diri_nisn'          => 'NISN',
                'diri_tempat_lahir'  => 'Tempat Lahir',
                'diri_tanggal_lahir' => 'Tanggal Lahir',
                'diri_agama'         => 'Agama',
                'diri_no_hp'         => 'No. HP',
                'diri_tinggi_badan'  => 'Tinggi Badan',
                'diri_berat_badan'   => 'Berat Badan',
                'diri_jumlah_saudara'=> 'Jumlah Saudara',
                'diri_asal_sekolah'  => 'Asal Sekolah',
            ],
        ],
        'data_ayah' => [
            'label' => 'Data Ayah',
            'fields' => [
                'ayah_nik'              => 'NIK Ayah',
                'ayah_tahun_lahir'      => 'Tahun Lahir Ayah',
                'ayah_pendidikan'       => 'Pendidikan Ayah',
                'ayah_pekerjaan'        => 'Pekerjaan Ayah',
                'ayah_penghasilan'      => 'Penghasilan Ayah',
                'ayah_ketidakmampuan'   => 'Ketidakmampuan Khusus Ayah',
            ],
        ],
        'data_ibu' => [
            'label' => 'Data Ibu',
            'fields' => [
                'ibu_nik'               => 'NIK Ibu',
                'ibu_tahun_lahir'       => 'Tahun Lahir Ibu',
                'ibu_pendidikan'        => 'Pendidikan Ibu',
                'ibu_pekerjaan'         => 'Pekerjaan Ibu',
                'ibu_penghasilan'       => 'Penghasilan Ibu',
                'ibu_ketidakmampuan'    => 'Ketidakmampuan Khusus Ibu',
            ],
        ],
        'data_wali' => [
            'label' => 'Data Wali',
            'fields' => [
                'data_wali'         => 'Tampilkan Section Data Wali',
                'wali_nik'          => 'NIK Wali',
                'wali_tahun_lahir'  => 'Tahun Lahir Wali',
                'wali_pekerjaan'    => 'Pekerjaan Wali',
                'wali_penghasilan'  => 'Penghasilan Wali',
            ],
        ],
        'alamat' => [
            'label' => 'Alamat Tempat Tinggal',
            'fields' => [
                'alamat_koordinat'  => 'Koordinat (Lat/Long)',
                'alamat_jarak'      => 'Jarak Tempat Tinggal',
            ],
        ],
        'data_kip' => [
            'label' => 'Data KIP / PKH',
            'fields' => [
                'data_kip'          => 'Tampilkan Section Data KIP/PKH',
                'kip_no_kip'        => 'No. KIP',
                'kip_no_kps_pkh'    => 'No. KPS/PKH',
                'kip_nama_di_kip'   => 'Nama di KIP',
                'kip_terima'        => 'Status Penerima KIP',
            ],
        ],
        'nilai_rapor' => [
            'label' => 'Nilai Rapor',
            'fields' => [
                'nilai_rapor' => 'Form Input Nilai Rapor',
            ],
        ],
        'berkas' => [
            'label' => 'Berkas / Dokumen',
            'fields' => [
                'berkas_foto'       => 'Foto Berwarna 3x4',
                'berkas_akta'       => 'Akta Kelahiran',
                'berkas_kk'         => 'Kartu Keluarga',
                'berkas_ktp_ortu'   => 'KTP Orang Tua',
                'berkas_sktm'       => 'Surat Keterangan Tidak Mampu (SKTM)',
                'berkas_pkh'        => 'Kartu PKH/KPS/KIP',
                'berkas_lainnya'    => 'Berkas Lainnya',
            ],
        ],
    ];

    /** Seluruh field configurable (flat list) */
    public static function allConfigurableFields(): array
    {
        $fields = [];
        foreach (self::FIELD_GROUPS as $group) {
            foreach ($group['fields'] as $key => $label) {
                $fields[$key] = $label;
            }
        }
        return $fields;
    }

    public function tahunPenerimaan(): BelongsTo
    {
        return $this->belongsTo(TahunPenerimaan::class);
    }

    public function isFieldActive(string $key): bool
    {
        if (in_array($key, self::FIXED_FIELDS)) {
            return true;
        }
        return (bool) ($this->field_configs[$key] ?? true);
    }

    public function isFixed(string $key): bool
    {
        return in_array($key, self::FIXED_FIELDS);
    }
}
