<?php

function calculate_vibration_data(Activity $activity) {
    $activity->ahwx = calculate_ahw($activity, "x");
    $activity->ahwy = calculate_ahw($activity, "y");
    $activity->ahwz = calculate_ahw($activity, "z");
    $activity->vector_summ = calculate_vector_summ($activity);
    $activity->rounded_ahwx = round($activity->ahwx, $activity->round_up_to);
    $activity->rounded_ahwy = round($activity->ahwy, $activity->round_up_to);
    $activity->rounded_ahwz = round($activity->ahwz, $activity->round_up_to);
    $activity->rounded_vector_summ = round($activity->vector_summ, $activity->round_up_to);
    $activity->partial_exposure = calculate_partial_exposure($activity);
    $activity->vector_summ_time = vector_summ_time($activity);
//      for debug
    $activity->s_axis_x = s($activity, "x");
    $activity->s_axis_y = s($activity, "y");
    $activity->s_axis_z = s($activity, "z");
    $activity->uprobkj_x = uprobkj($activity, "x");
    $activity->uprobkj_y = uprobkj($activity, "y");
    $activity->uprobkj_z = uprobkj($activity, "z");
    $activity->ucj_x = ucj($activity, "x");
    $activity->ucj_y = ucj($activity, "y");
    $activity->ucj_z = ucj($activity, "z");
    $activity->uhvi = uhvi($activity);
    $activity->cai = cai($activity);
    $activity->caiuci2 = caiuci2($activity);
    $activity->uti_rh = uti_rh($activity);
    $activity->uti_lh = uti_lh($activity);
    $activity->cti = cti($activity);
    $activity->ctiuti2 = ctiuti2($activity);
    $activity->uahv = uahv($activity);
}

function calculate_and_save_activities() {
    $activities = Activity::get_all();

    foreach ($activities as $activity) {
        calculate_vibration_data($activity);
        $activity->save();
    }
}
