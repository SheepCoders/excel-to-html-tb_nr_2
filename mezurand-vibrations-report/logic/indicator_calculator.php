<?php

function calculate_indicator_data(Indicator $indicator) {
    $hand = 'right';

    $indicator->exposure_time_rh = hand_exposure_time($hand);

    $indicator->multiplicity_NDN_rh = multiplicity_ndn($hand);
    $indicator->action_threshold_multiplicity_rh = action_threshold_multiplicity($hand);
    $indicator->multiplicity_pregnant_breastfeeding_rh = multiplicity_pregnant_breastfeeding($hand);
    $indicator->multiplicity_young_rh = multiplicity_young($hand);
    $indicator->multiplicity_young_wg_2016_poz_1509_rh = multiplicity_young($hand, True);
    $indicator->daily_exposure_rh = daily_exposure($hand);
//         for debug
    $indicator->bg20_r = value_a8($hand);
    $indicator->ba17_r = hand_exposure_time($hand);
    $indicator->bm9_r = num_impact_lt_30($hand);
    $indicator->bg17_r = max_vector_summ_impact_lt_30($hand);
    $indicator->bq18_r = exceedings_ndn_05h($hand);
    $indicator->bq17_r = exceedings_ndn_8h($hand);
    $indicator->bq15_r = num_values_exceeded($hand);
    $indicator->h19_r = multiplicity_ndn($hand);
    $indicator->bq21_r = numb_threshold_values_exceeded($hand);
    $indicator->h20_r = action_threshold_multiplicity($hand);
    $indicator->br18_r = exceedings_ndn_05h_women($hand);
    $indicator->br17_r = exceedings_ndn_8h_women($hand);
    $indicator->br15_r = num_values_exceeded_pregn_breast($hand);
    $indicator->h21_r = multiplicity_pregnant_breastfeeding($hand);
    $indicator->bs18_r = exceedings_ndn_05h_young($hand);
    $indicator->bs17_r = exceedings_ndn_8h_young($hand);
    $indicator->bs15_r = num_values_exceeded_young($hand);
    $indicator->uca_8_r = uca8($hand);
    $indicator->_2xuca8_r = _2xuca8($hand);
    $indicator->h22_r = multiplicity_young($hand);
    $indicator->ucahvmax_r = ucahvmax($hand);
    $indicator->exposure_30_less_rh = exposure_30_less($hand);

    $hand = 'left';

    $indicator->exposure_time_lh = hand_exposure_time($hand);
    $indicator->multiplicity_NDN_lh = multiplicity_ndn($hand);
    $indicator->action_threshold_multiplicity_lh = action_threshold_multiplicity($hand);
    $indicator->multiplicity_pregnant_breastfeeding_lh = multiplicity_pregnant_breastfeeding($hand);
    $indicator->multiplicity_young_lh = multiplicity_young($hand);
    $indicator->multiplicity_young_wg_2016_poz_1509_lh = multiplicity_young($hand, True);
    $indicator->daily_exposure_lh = daily_exposure($hand);
//         for debug
    $indicator->bg20_l = value_a8($hand);
    $indicator->ba17_l = hand_exposure_time($hand);
    $indicator->bm9_l = num_impact_lt_30($hand);
    $indicator->bg17_l = max_vector_summ_impact_lt_30($hand);
    $indicator->bq18_l = exceedings_ndn_05h($hand);
    $indicator->bq17_l = exceedings_ndn_8h($hand);
    $indicator->bq15_l = num_values_exceeded($hand);
    $indicator->h19_l = multiplicity_ndn($hand);
    $indicator->bq21_l = numb_threshold_values_exceeded($hand);
    $indicator->h20_l = action_threshold_multiplicity($hand);
    $indicator->br18_l = exceedings_ndn_05h_women($hand);
    $indicator->br17_l = exceedings_ndn_8h_women($hand);
    $indicator->br15_l = num_values_exceeded_pregn_breast($hand);
    $indicator->h21_l = multiplicity_pregnant_breastfeeding($hand);
    $indicator->bs18_l = exceedings_ndn_05h_young($hand);
    $indicator->bs17_l = exceedings_ndn_8h_young($hand);
    $indicator->bs15_l = num_values_exceeded_young($hand);
    $indicator->uca_8_l = uca8($hand);
    $indicator->_2xuca8_l = _2xuca8($hand);
    $indicator->h22_l = multiplicity_young($hand);
    $indicator->ucahvmax_l = ucahvmax($hand);
    $indicator->exposure_30_less_lh = exposure_30_less($hand);
}

function calculate_and_save_indicator() {
    $indicator = Indicator::get_all()[0] ?? new Indicator();
    calculate_indicator_data($indicator);
    $indicator->save();
}
