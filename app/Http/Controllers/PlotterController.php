<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PlotterController extends Controller
{
    private static $R = 6371e3; // Earth's radius in metres
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
                $degree = $degree - 180;
                break;
            case 'NW':
                $degree = 360 - $degree;
                break;
        }
        return $degree;
    }

    // Convert degree format to bearing format
    private function degreeToBearing($degree){

        if(abs($degree) <= 90){
            $direction = 'N';
            $angle = abs($degree);
        }else{
            $direction = 'S';
            $angle = 180 - abs($degree);
        }
        $degree = floor($angle);
        $minutes = round(($angle - $degree) * 60, 2);
        $bearing = ($degree >= 0) ? 'E' : 'W';

        return [$direction, $bearing, $degree, $minutes];
    }

    // Calculate destination point given distance and bearing from starting point
    private function destinationPoint($distance, $degree, $startingX, $startingY){
        /*
        Formula: 	φ2 = asin( sin φ1 ⋅ cos δ + cos φ1 ⋅ sin δ ⋅ cos θ )
	                λ2 = λ1 + atan2( sin θ ⋅ sin δ ⋅ cos φ1, cos δ − sin φ1 ⋅ sin φ2 )
                    where 	φ is latitude, λ is longitude, θ is the bearing (clockwise from north), δ is the angular distance d/R; d being the distance travelled, R the earth’s radius
                    Source: https://www.movable-type.co.uk/scripts/latlong.html
        */     
        $delta = $distance / self::$R; // angular distance in radians
        $theta = deg2rad($degree); // in radians

        $phi1 = deg2rad($startingY); 
        $lamda1 = deg2rad($startingX);

        $phi2 = asin( sin($phi1) * cos($delta) + cos($phi1) * sin($delta) * cos($theta));
        $lamda2 = $lamda1 + atan2(
            sin($theta) * sin($delta) * cos($phi1),
            cos($delta) - sin($phi1) * sin($phi2));

        $yPoint = round(rad2deg($phi2), 6);
        $xPoint = round(rad2deg($lamda2), 6);
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
        // Haversine formula to calculate distance between two points
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $deltaPhi = deg2rad($lat2 - $lat1);
        $deltaLambda = deg2rad($lon2 - $lon1);

        $a = sin($deltaPhi/2) * sin($deltaPhi/2) +
            cos($phi1) * cos($phi2) *
            sin($deltaLambda/2) * sin($deltaLambda/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        // Bearing formula to calculate bearing between two points
        $y = sin($deltaLambda) * cos($phi2);
        $x = cos($phi1) * sin($phi2) - sin($phi1) * cos($phi2) * cos($deltaLambda);
        $bearing = atan2($y, $x);   
        $bearing = rad2deg(num: $bearing);

        return [round(self::$R * $c, 6), ...$this->degreeToBearing($bearing)];
    }

    public function outputCoords($lotJsonData){
        $fullLotInfo = [];
        $allDistances = [];
        
        $lotTieData = $lotJsonData['tiePoint'];
        $tieLocation = (!empty($lotTieData['tieLoc']) && !empty($lotTieData['tieCity']) && !empty($lotTieData['tieRef']))
        ? $lotTieData['tieLoc'].', '.$lotTieData['tieCity'].', '.$lotTieData['tieRef']
        : $lotJsonData['lotAddress']. ' - ' .$lotTieData['tieLatitude'].', '.$lotTieData['tieLongitude'];

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
                    "latitude" => round($pLat,6),
                    "longitude" => round($pLon,6)
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
                    "latitude" => round($pointMapCoord[1],6),
                    "longitude" => round($pointMapCoord[0],6),
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
            $marginInfo = $this->informationBuilder('Line '.count($lotInfo['points'] ).'- 1', $marginDist[1], $marginDist[3],$marginDist[4], $marginDist[2],round($marginDist[0],2));
            $lotInfo['marginDistance'] = [
                "distance" => round($marginDist[0],2),
                "pointLabel" => $marginInfo
            ];
            array_push($fullLotInfo, $lotInfo);
        }

        $mainArr = [
            "lotTitle" => $lotJsonData['lotTitle'] ?? 'N/A',
            "lotAddress" => $lotJsonData['lotAddress'],
            "tiePoint" => [
                "tieLoc" => $lotTieData['tieLoc'] ?? 'N/A',
                "tieCity" => $lotTieData['tieCity'] ?? 'N/A',
                "tieRef" => $lotTieData['tieRef'] ?? 'N/A',
                "tieLabel" => $tieLocation,
                "tieLatitude" => round($mLatitude,6),
                "tieLongitude" => round($mLongitude,6)
            ],
            "lotItem" => $fullLotInfo,
            "maxDistancePoint" => $maxDistancePoint
        ];
        
        return $mainArr;
    }
}
