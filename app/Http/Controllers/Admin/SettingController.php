<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = [
            'nama_sekolah'        => Setting::get('nama_sekolah', ''),
            'alamat'              => Setting::get('alamat', ''),
            'telepon'             => Setting::get('telepon', ''),
            'email'               => Setting::get('email', ''),
            'deskripsi'           => Setting::get('deskripsi', ''),
            'logo'                => Setting::get('logo', ''),
            'keterangan_formulir' => Setting::get('keterangan_formulir', ''),
            'seleksi_bobot_nilai' => Setting::get('seleksi_bobot_nilai', 70),
            'seleksi_bobot_umur'  => Setting::get('seleksi_bobot_umur', 30),
            'seleksi_umur_min'    => Setting::get('seleksi_umur_min', 15),
            'seleksi_umur_max'    => Setting::get('seleksi_umur_max', 21),
        ];

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_sekolah'        => ['required', 'string', 'max:255'],
            'alamat'              => ['nullable', 'string', 'max:500'],
            'telepon'             => ['nullable', 'string', 'max:20'],
            'email'               => ['nullable', 'email', 'max:255'],
            'deskripsi'           => ['nullable', 'string', 'max:1000'],
            'logo'                => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'seleksi_bobot_nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'seleksi_bobot_umur'  => ['required', 'integer', 'min:0', 'max:100'],
            'seleksi_umur_min'    => ['required', 'integer', 'min:1', 'max:99'],
            'seleksi_umur_max'    => ['required', 'integer', 'min:1', 'max:99', 'gt:seleksi_umur_min'],
        ]);

        Setting::set('nama_sekolah', $request->nama_sekolah);
        Setting::set('alamat', $request->alamat ?? '');
        Setting::set('telepon', $request->telepon ?? '');
        Setting::set('email', $request->email ?? '');
        Setting::set('deskripsi', $request->deskripsi ?? '');
        Setting::set('keterangan_formulir', $request->keterangan_formulir ?? '');
        Setting::set('seleksi_bobot_nilai', $request->seleksi_bobot_nilai);
        Setting::set('seleksi_bobot_umur', $request->seleksi_bobot_umur);
        Setting::set('seleksi_umur_min', $request->seleksi_umur_min);
        Setting::set('seleksi_umur_max', $request->seleksi_umur_max);

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('logo', $path);
        }

        return back()->with('success', 'Pengaturan sekolah berhasil disimpan.');
    }
}
