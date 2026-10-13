<?php

namespace App\Http\Controllers;

use App\Support\GeofenceStore;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    public function edit(GeofenceStore $geofence): View
    {
        return view('pages.geofence', ['geofence' => $geofence->get()]);
    }

    public function update(Request $request, GeofenceStore $geofence): RedirectResponse
    {
        // Titik dikirim sebagai JSON dari editor peta (hidden input "points").
        $request->merge(['points' => json_decode((string) $request->input('points'), true) ?? []]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'points' => ['required', 'array', 'min:3', 'max:50'],
            'points.*.lat' => ['required', 'numeric', 'between:-90,90'],
            'points.*.lng' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'name.required' => 'Nama area wajib diisi.',
            'points.min' => 'Tambahkan minimum 3 titik koordinat.',
            'points.required' => 'Tambahkan minimum 3 titik koordinat.',
        ]);

        $geofence->save($data['name'], $data['points']);

        return redirect()->route('geofence.edit')->with('status', 'Area geofence berhasil disimpan.');
    }
}
