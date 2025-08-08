<?php


function calculateAverageReadingFromInstance(Obraz $obraz): ?float {
    $readings = [];

    for ($i = 1; $i <= 30; $i++) {
        $prop = 'reading_' . $i;

        if (isset($obraz->$prop) && $obraz->$prop !== null && $obraz->$prop != 0) {
            $readings[] = $obraz->$prop;
        }
    }

    if (count($readings) === 0) {
        return null;
    }

    $location_id = $obraz->location_id;
    $location = Location::get_by_id($location_id);

    $round_to = $location->round_up_to;

    return array_sum($readings) / count($readings);
}


function createConcatenatedString(Obraz $obraz, float $xsr): string {

    $location_id = $obraz->location_id;
    $location = Location::get_by_id($location_id);

    $aw = $location->U;
    $round_to = $location->round_up_to;

    $rounded = round(2 * $aw * $xsr / 100, $round_to);

    return round($xsr, $round_to) . '±' . $rounded;
}


function calculateIlluminationUniformity(Obraz $obraz, float $xsr): ?float
{
    $validReadings = [];

    for ($i = 1; $i <= 30; $i++) {
        $reading = $obraz->{'reading_'.$i} ?? null;

        if ($reading !== null && $reading != 0) {
            $validReadings[] = (float)$reading;
        }
    }

    if (empty($validReadings)) {
        return null;
    }

    $minReading = min($validReadings);

    if ($xsr == 0) {
        return null;
    }

    return $minReading / $xsr;
}


function buildObrazUncertaintyStringSafe(Obraz $obraz, $d): ?string
{
    $location_id = $obraz->location_id;
    $location = Location::get_by_id($location_id);
    $U = $location->U;
    $round_to = $location->round_up_to;

    return round($d, $round_to) . '±' . round(2 * $U * $d / 100, $round_to);
}