<?php

namespace App\Http\Controllers;

use App\Support\GeofenceStore;
use App\Support\LivestockRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request, LivestockRepository $livestock, GeofenceStore $geofence): View
    {
        $animals = $livestock->all();
        // ?ternak=SAPI-002 memilih ternak tertentu; bila tidak valid, pakai yang pertama.
        $selected = $livestock->find($request->query('ternak')) ?? $animals->first();

        return view('pages.locations', [
            'animals' => $animals,
            'summary' => $livestock->summary(),
            'geofence' => $geofence->get(),
            'selected' => $selected,
            'animalsJs' => $livestock->forJs(),
        ]);
    }
}
