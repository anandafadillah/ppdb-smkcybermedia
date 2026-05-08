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
        ];

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'alamat'       => ['nullable', 'string', 'max:500'],
            'telepon'      => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:255'],
            'deskripsi'    => ['nullable', 'string', 'max:1000'],
            'logo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        Setting::set('nama_sekolah', $request->nama_sekolah);
        Setting::set('alamat', $request->alamat ?? '');
        Setting::set('telepon', $request->telepon ?? '');
        Setting::set('email', $request->email ?? '');
        Setting::set('deskripsi', $request->deskripsi ?? '');
        Setting::set('keterangan_formulir', $request->keterangan_formulir ?? '');

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
