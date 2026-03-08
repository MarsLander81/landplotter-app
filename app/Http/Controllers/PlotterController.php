<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PlotterController extends Controller
{
    public function plotLand(Request $request){
        $lotJsonData = $request->input();
        $output = $this->outputCoords($lotJsonData);
        //return redirect()->route('plot-view')->with('lot_data', $lotJsonData);
        return Inertia::render('ViewPlotter',['plotRenderData' => $output]);
    }
    private function bearingToDegree($direction, $degree){
        switch($direction){
            case 'SE':
                $degree = 180 - $degree;
                break;
            case 'SW':
                $degree = 180 + $degree;
                break;
            case 'NW':
                $degree = 360 - $degree;
                break;
        }
        return $degree;
    }

    // Convert degree format to bearing format
    private function degreeToBearing($degree)
    {
        $deg = ($degree + 360) % 360;

        if ($deg <= 90) {
            return ['N','E',$deg,0];
        } 
        elseif ($deg <= 180) {
            return ['S','E',180-$deg,0];
        } 
        elseif ($deg <= 270) {
            return ['S','W',$deg-180,0];
        } 
        else {
            return ['N','W',360-$deg,0];
        }
    }

    // Calculate destination point given distance and bearing from starting point
    private function destinationPoint($distance, $degree, $startingX, $startingY){

        $a = 6378137; // WGS84 semi-major axis (meters)
        $e2 = 0.00669438; // WGS84 eccentricity squared
        $lat = deg2rad($startingY);
        $N = $a / sqrt(1 - $e2 * sin($lat) * sin($lat));
        
        // Convert bearing and distance to cartesian offsets (meters)
        $theta = deg2rad($degree);
        $dx = $distance * sin($theta); // east offset
        $dy = $distance * cos($theta); // north offset
        
        // Convert meter offsets back to lat/lon differences
        $dLat = $dy / ($N * (1 - $e2));
        $dLon = $dx / ($N * cos($lat));
        
        $yPoint = $startingY + rad2deg($dLat);
        $xPoint = $startingX + rad2deg($dLon);
        return [$xPoint, $yPoint];
    }
    // Build information array for each line
    private function informationBuilder($lotNumber, $direction, $angle, $minutes, $bearing, $distance){
        return [
            'Line' => $lotNumber,
            'Bearing' => $direction.' '.$angle.'° '.$minutes."' ".$bearing,
            'Distance' => $distance
        ];
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2){
        // Cadastral formula: Project lat/lon to local cartesian coordinates
        // and use Euclidean geometry (standard for land surveying)
        
        // Get mean latitude for accurate projection
        $meanLat = deg2rad(($lat1 + $lat2) / 2);
        
        // WGS84 Earth parameters for accurate projection
        $a = 6378137; // semi-major axis (meters)
        $e2 = 0.00669438; // eccentricity squared
        $N = $a / sqrt(1 - $e2 * sin($meanLat) * sin($meanLat));
        
        // Project lat/lon to local cartesian coordinates (meters)
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $x = $N * cos($meanLat) * $dLon; // east component (meters)
        $y = $N * (1 - $e2) * $dLat; // north component (meters)
        
        // Euclidean distance and bearing calculation
        $distance = sqrt($x * $x + $y * $y);
        $bearing = fmod(rad2deg(atan2($x, $y)) + 360, 360); // bearing from north
        
        return [round($distance, 6), ...$this->degreeToBearing($bearing)];
    }

    public function outputCoords($lotJsonData){
        $fullLotInfo = [];
        $allDistances = [];
        
        $lotTieData = $lotJsonData['tiePoint'];
        $lotAddressLabel = $lotJsonData['lotAddress'] ?? $lotJsonData['lotTitle'] ?? 'LOT';
        $tieLocation = (!empty($lotTieData['tieLoc']) && !empty($lotTieData['tieCity']) && !empty($lotTieData['tieRef']))
        ? $lotTieData['tieLoc'].', '.$lotTieData['tieCity'].', '.$lotTieData['tieRef']
        :  $lotAddressLabel . ' - ' .$lotTieData['tieLatitude'].', '.$lotTieData['tieLongitude'];

        $mLatitude = $lotTieData['tieLatitude'] ?? 0;
        $mLongitude = $lotTieData['tieLongitude'] ?? 0;

        foreach ($lotJsonData['lotItem'] as $lot) {
            $allDistances = array_merge($allDistances, array_column($lot['points'], 'Distance'));
        }
        $maxDistancePoint = !empty($allDistances) ? max($allDistances) : 1; //max distance for scaling

        foreach($lotJsonData['lotItem'] as $i => $lotItem){
            $lotInfo = [];
            
            $tieInfo = $lotItem['tie'];
            $tieAngle = $this->bearingToDegree($tieInfo['direction'].$tieInfo['bearing'], $tieInfo['degree'] + ($tieInfo['minutes'] / 60));
            $tieMapCoord = $this->destinationPoint($tieInfo['distance'], $tieAngle, $mLongitude, $mLatitude);

            $pLon = $tieMapCoord[0];
            $pLat = $tieMapCoord[1];

            $lotInfo = [
                "plotId" => $lotItem['id'],
                "plotName" => $lotItem['plotname'] ?? 'Plot '.($i+1),
                "tieLabel" => $this->informationBuilder('Tie Point', $tieInfo['direction'], $tieInfo['degree'], $tieInfo['minutes'], $tieInfo['bearing'], $tieInfo['distance']),
                "endPoint" => [
                    "latitude" => $pLat,
                    "longitude" => $pLon
                ],
                "points" => [],
                "marginDistance" => []
            ];
            //Mapping Full Body Coordinates
            foreach($lotItem['points'] as $j => $pointItem){
                $pointAngle = $this->bearingToDegree(
                    $pointItem['direction'].$pointItem['bearing'], 
                    $pointItem['degree'] + ($pointItem['minutes'] / 60
                ));
                $pointMapCoord = $this->destinationPoint(
                    $pointItem['distance'], 
                    $pointAngle, 
                    $pLon, 
                    $pLat
                );
    
                $lotPointInfo = [
                    "pointId"=> $pointItem['id'],
                    "pointNumber" => $j + 1,
                    "pointLabel" => $this->informationBuilder('Line '.($j+1), $pointItem['direction'], $pointItem['degree'], $pointItem['minutes'], $pointItem['bearing'], $pointItem['distance']),
                    "latitude" => $pointMapCoord[1],
                    "longitude" => $pointMapCoord[0]
                ];
                array_push($lotInfo['points'], $lotPointInfo);

                $pLon = $pointMapCoord[0];
                $pLat = $pointMapCoord[1];
            }

            $marginDist = $this->calculateDistance(
                $tieMapCoord[1], 
                $tieMapCoord[0], 
                $lotInfo['points'][count($lotInfo['points']) - 1]['latitude'], 
                $lotInfo['points'][count($lotInfo['points']) - 1]['longitude']
            );
            $marginInfo = $this->informationBuilder(
                'Line '.count($lotInfo['points'] ).'- 1', 
                $marginDist[1], 
                $marginDist[3],
                $marginDist[4], 
                $marginDist[2],
                round($marginDist[0],2));
            $lotInfo['marginDistance'] = [
                "distance" => round($marginDist[0],2),
                "pointLabel" => $marginInfo
            ];
            array_push($fullLotInfo, $lotInfo);
        }

        $mainArr = [
            "lotTitle" => $lotJsonData['lotTitle'] ?? 'N/A',
            "lotAddress" => $tieLocation,
            "tiePoint" => [
                "tieLoc" => $lotTieData['tieLoc'] ?? 'N/A',
                "tieCity" => $lotTieData['tieCity'] ?? 'N/A',
                "tieRef" => $lotTieData['tieRef'] ?? 'N/A',
                "tieLabel" => $tieLocation,
                "tieLatitude" => $mLatitude,
                "tieLongitude" => $mLongitude
            ],
            "lotItem" => $fullLotInfo,
            "maxDistancePoint" => $maxDistancePoint
        ];
        
        return $mainArr;
    }
}
