<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlotterController extends Controller
{
    public function plotLand(Request $request){
        $lotJsonData = $request->input();
        $output = $this->outputCoords($lotJsonData);
        return response()->json($output);
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
        $direction = 'N';
        $bearing = 'E';

        if(abs($degree) > 90) $direction = 'S';
        if($degree < 0) $bearing = 'W';

        if($direction == 'S' && $bearing == 'E'){
            $convertedDegree = $degree + 180 - 360;
        } elseif($direction == 'S' && $bearing == 'W'){
            $convertedDegree = $degree - 180;
        }else{
            $convertedDegree = $degree;
        }
        return array($direction.', '.$bearing, abs($convertedDegree));
    }

    // Calculate destination point given distance and bearing from starting point
    private function destinationPoint($distance, $degree, $startingX, $startingY){
        /*
        Formula: 	φ2 = asin( sin φ1 ⋅ cos δ + cos φ1 ⋅ sin δ ⋅ cos θ )
	                λ2 = λ1 + atan2( sin θ ⋅ sin δ ⋅ cos φ1, cos δ − sin φ1 ⋅ sin φ2 )
                    where 	φ is latitude, λ is longitude, θ is the bearing (clockwise from north), δ is the angular distance d/R; d being the distance travelled, R the earth’s radius
                    Source: https://www.movable-type.co.uk/scripts/latlong.html
        */
        $R = 6371e3; // Earth's radius in metres
        $delta = $distance / $R; // in metres
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

    // Scale distance to fit in canvas size
    private function cartesianPoint($angle, $pointX1, $pointY1, $srcMax, $srcDistance, $desiredMax = 380){
        $distance = round(($srcDistance * $desiredMax) / $srcMax, 2);

        $angle = deg2rad($angle);
        $pointX2 = $pointX1 + $distance * cos($angle);
        $pointY2 = $pointY1 + $distance * sin($angle);
        return [round($pointX2,6), round($pointY2,6)];
    }

    // Build information array for each line
    private function informationBuilder($lotNumber, $direction, $angle, $minutes, $bearing, $distance){
        return [
            'Line' => $lotNumber,
            'Bearing' => $direction.' '.$angle.'° '.$minutes."\' ".$bearing,
            'Distance' => $distance
        ];
    }

    public function outputCoords($lotJsonData){
        $fullLotInfo = [];
        $allDistances = [];

        $lotAddress = $lotJsonData['Address'] || 'N/A';
        $tieLocation = $lotJsonData['Province'].', '.$lotJsonData['City'].', '.$lotJsonData['PointOfReference'] ?? $tieLocation = $lotJsonData['Tie Point']['Latitude'].', '.$lotJsonData['Tie Point']['Longitude'];

        $mLatitude = $lotJsonData['TiePoint']['Latitude'] ?? 0;
        $mLongitude = $lotJsonData['TiePoint']['Longitude'] ?? 0;

        foreach ($lotJsonData['LotItem'] as $lot) {
            $allDistances = array_merge($allDistances, array_column($lot['LotCoordinates'], 'Distance'));
        }
        $maxDistancePoint = !empty($allDistances) ? max($allDistances) : 1; //max distance for scaling

        foreach($lotJsonData['LotItem'] as $i => $lotItem){
            $lotInfo = [];

            $tieAngle = $this->bearingToDegree($lotItem['TieDirection'].$lotItem['TieBearing'], $lotItem['TieDegree'] + ($lotItem['TieMinute'] / 60));
            $tieMapCoord = $this->destinationPoint($lotItem['TieDistance'], $tieAngle, $mLongitude, $mLatitude);
            $tieCartesianCoord = $this->cartesianPoint($tieAngle, 0, 0,$maxDistancePoint, $lotItem['TieDistance']);

            $pLon = $tieMapCoord[0];
            $pLat = $tieMapCoord[1];
            $cnvsX = $tieCartesianCoord[0];
            $cnvsY = $tieCartesianCoord[1];

            $lotInfo = [
                "LotName" => $lotItem['LotName'] ?? 'Lot '.($i+1),
                "TieDirection" => $this->informationBuilder('Tie Point', $lotItem['TieDirection'], $lotItem['TieDegree'], $lotItem['TieMinute'], $lotItem['TieBearing'], $lotItem['TieDistance']),
                "EndPoint" => [
                    "Latitude" => round($pLat,6),
                    "Longitude" => round($pLon,6)
                ],
                "LotPoints" => []
            ];
                
            //Mapping Full Body Coordinates
            foreach($lotItem['LotCoordinates'] as $j => $pointItem){
                $pointAngle = $this->bearingToDegree($pointItem['Direction'].$pointItem['Bearing'], $pointItem['Degree'] + ($pointItem['Minute'] / 60));
                $pointMapCoord = $this->destinationPoint($pointItem['Distance'], $pointAngle, $tieMapCoord[0], $tieMapCoord[1]);
                $pointCartesianCoord = $this->cartesianPoint($pointAngle, $tieCartesianCoord[0], $tieCartesianCoord[1], $maxDistancePoint, $pointItem['Distance']);

                $lotPointInfo = [
                    "PointNumber" => $j + 1,
                    "Direction" => $this->informationBuilder('Line '.($j+1), $pointItem['Direction'], $pointItem['Degree'], $pointItem['Minute'], $pointItem['Bearing'], $pointItem['Distance']),
                    "Latitude" => round($pLat,6),
                    "Longitude" => round($pLon,6),
                    "CanvasY" => round($cnvsY,6),
                    "CanvasX" => round($cnvsX,6),
                    "Distance" => $pointItem['Distance']
                ];
                array_push($lotInfo['LotPoints'], $lotPointInfo);

                $pLon = $pointMapCoord[0];
                $pLat = $pointMapCoord[1];
                $cnvsX = $pointCartesianCoord[0];
                $cnvsY = $pointCartesianCoord[1];
            }
            array_push($fullLotInfo, $lotInfo);
        }

        $mainArr = [
            "Lot Title" => $lotJsonData['Lot Title'] ?? 'N/A',
            "Address" => $lotAddress,
            "Province" => $lotJsonData['Province'] ?? 'N/A',
            "City" => $lotJsonData['City'] ?? 'N/A',
            "TiePoint" => [
                "Location" => $tieLocation,
                "Latitude" => round($mLatitude,6),
                "Longitude" => round($mLongitude,6)
            ],
            "LotCoordinates" => $fullLotInfo,
            "MaxDistancePoint" => $maxDistancePoint
        ];
        
        return $mainArr;
    }
}
