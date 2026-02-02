<?php

namespace App\Http\Controllers;

use App\Models\TiePoints;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DatabaseController extends Controller
{
    public function fetchLocation(Request $request){
        $queryLocation = trim($request->loc);
        if (!$queryLocation || strlen($queryLocation) < 2) {
            return [];
        }
        return TiePoints::query()
            ->select('location')
            ->distinct()
            ->whereLike('location', $queryLocation . '%')
            ->limit(10)
            ->get();
    }

    public function fetchCityMun(Request $request){
        $queryLocation = trim($request->loc);
        $queryCityMun = trim($request->citymun);
        if (!$queryCityMun || strlen($queryCityMun) < 2) {
            return [];
        }
        return TiePoints::query()
            ->select('city_municipality')
            ->distinct()
            ->where('location', $queryLocation)
            ->whereLike('city_municipality', $queryCityMun . '%')
            ->limit(10)
            ->get();
    }

    public function fetchPointReference(Request $request){
        $queryLocation = trim($request->loc);
        $queryCityMun = trim($request->citymun);
        $queryPointReference = trim($request->pointref);
        if (!$queryPointReference || strlen($queryPointReference) < 2) {
            return [];
        }
        return TiePoints::query()
            ->select('point_of_reference', 'latitude', 'longitude')
            ->distinct()
            ->where('location', $queryLocation)
            ->where('city_municipality', $queryCityMun)
            ->whereLike('point_of_reference', $queryPointReference . '%')
            ->limit(10)
            ->get();
    }
}
