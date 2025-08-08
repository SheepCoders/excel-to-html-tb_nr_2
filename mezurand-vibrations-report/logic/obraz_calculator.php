<?php

function calculate_light_data(Obraz $obraz) {
    $xsr = calculateAverageReadingFromInstance($obraz);
    $obraz->average_illuminance = createConcatenatedString($obraz, $xsr);
    $d = calculateIlluminationUniformity($obraz, $xsr);
    $obraz->illumination_uniformity = buildObrazUncertaintyStringSafe($obraz, $d);

}


function calculate_and_save_obrazy() {
    $obrazy = Obraz::get_all();

    foreach ($obrazy as $obraz) {

        $location_id = $obraz->location_id ? $obraz->location_id : null;

        calculate_light_data($obraz);
        $obraz->save($location_id);
    }
}