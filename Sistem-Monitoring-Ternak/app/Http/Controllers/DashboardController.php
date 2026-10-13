<?php

namespace App\Http\Controllers;

use App\Support\GeofenceStore;
use App\Support\LivestockRepository;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(LivestockRepository $livestock, GeofenceStore $geofence): View
    {
        return view('pages.dashboard', [
            'animals' => $livestock->all(),
            'summary' => $livestock->summary(),
            'geofence' => $geofence->get(),
        ]);
    }
}
