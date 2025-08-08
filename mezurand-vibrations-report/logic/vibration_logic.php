<?php

function num_of_measur_xyz(Activity $activity, string $axis): ?int
{
    $axis = strtolower($axis);
    $count = null;

    if (!empty($activity->measurements)) {
        $valid = array_filter($activity->measurements, function ($measurement) use ($axis) {
            if (!($measurement instanceof Measurement)) return false;

            return match ($axis) {
                'x' => $measurement->ax !== null,
                'y' => $measurement->ay !== null,
                'z' => $measurement->az !== null,
                default => throw new InvalidArgumentException("Axis must be 'x', 'y', or 'z'")
            };
        });

        $count = count($valid);
    }

    return $count;
}


function sum_of_square(Activity $activity, string $axis): float
{
    $axis = strtolower($axis);
    $sum = 0.0;

    if (!empty($activity->measurements)) {
        foreach ($activity->measurements as $measurement) {
            if (!($measurement instanceof Measurement)) continue;

            switch ($axis) {
                case 'x':
                    if ($measurement->ax !== null) {
                        $sum += pow($measurement->ax, 2);
                    }
                    break;
                case 'y':
                    if ($measurement->ay !== null) {
                        $sum += pow($measurement->ay, 2);
                    }
                    break;
                case 'z':
                    if ($measurement->az !== null) {
                        $sum += pow($measurement->az, 2);
                    }
                    break;
                default:
                    throw new InvalidArgumentException("Axis must be 'x', 'y', or 'z'");
            }
        }
    }

    return $sum;
}


function calculate_ahw(Activity $activity, string $axis): ?float
{
    $count = num_of_measur_xyz($activity, $axis);

    if (!empty($activity->measurements) && $count > 0) {
        $sum = sum_of_square($activity, $axis);
        $ahw = sqrt($sum / $count);
        return $ahw;
    }

    return null;
}


function calculate_vector_summ(Activity $activity): ?float
{
    if (empty($activity->measurements)) {
        return null;
    }

    $sum_of_squares_ahw = 0.0;

    foreach (['x', 'y', 'z'] as $axis) {
        $ahw = calculate_ahw($activity, $axis);
        if ($ahw !== null) {
            $sum_of_squares_ahw += pow($ahw, 2);
        }
    }

    return sqrt($sum_of_squares_ahw);
}


function vector_summ_time(Activity $activity): ?float
{
    if ($activity->vector_summ !== null && $activity->measurement_time_Ti !== null) {
        return pow($activity->vector_summ, 2) * $activity->measurement_time_Ti;
    }

    return null;
}


function calculate_partial_exposure(Activity $activity): ?float
{
    if ($activity->vector_summ !== null) {
        $partial_exposure = (sqrt($activity->measurement_time_Ti / 480)) * $activity->vector_summ;
        return round($partial_exposure, $activity->round_up_to);
    }
    return null;
}


function hand_exposure_time(string $hand): ?int {
    $total_time = 0;
    $found = false;

    $all_activities = Activity::get_all();

    foreach ($all_activities as $activity) {
        if ($activity->hand === $hand && !empty($activity->measurements)) {
            $total_time += $activity->measurement_time_Ti;
            $found = true;
        }
    }

    return $found ? $total_time : null;
}


function num_impact_lt_30(string $hand): int {
    global $wpdb;
    $table = $wpdb->prefix . 'activity';

    $sql = $wpdb->prepare(
        "SELECT COUNT(DISTINCT id) FROM $table WHERE measurement_time_Ti < %d AND hand = %s",
        30,
        $hand
    );

    $count = (int) $wpdb->get_var($sql);
    return $count;
}


function max_vector_summ_impact_lt_30(string $hand): ?float {
    if (num_impact_lt_30($hand) < 1) {
        return null;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'activity';

    $sql = $wpdb->prepare(
        "SELECT vector_summ FROM $table WHERE measurement_time_Ti < %d AND hand = %s AND vector_summ IS NOT NULL",
        30,
        $hand
    );

    $results = $wpdb->get_col($sql);

    if (empty($results)) {
        return null;
    }

    $max_value = max($results);

    return round($max_value, 5);
}


function exceedings_ndn_05h(string $hand): ?float {
    global $wpdb;
    $activity_table = $wpdb->prefix . 'activity';
    $measurement_table = $wpdb->prefix . 'measurement';

    $sql_exists = $wpdb->prepare(
        "SELECT EXISTS(
            SELECT 1 FROM $activity_table a
            INNER JOIN $measurement_table m ON a.id = m.activity_id
            WHERE a.hand = %s
            LIMIT 1
        )",
        $hand
    );

    $exists = (bool) $wpdb->get_var($sql_exists);

    if ($exists) {
        if (num_impact_lt_30($hand) > 0) {
            $max_summ = max_vector_summ_impact_lt_30($hand);
            if ($max_summ !== null) {
                return round($max_summ / POSSIBLE_VALUE_05H_NDN, 2);
            }
        }
    }

    return null;
}


function value_a8(string $hand): ?float {
    global $wpdb;
    $activity_table = $wpdb->prefix . 'activity';
    $measurement_table = $wpdb->prefix . 'measurement';

    $sql_exists = $wpdb->prepare(
        "SELECT EXISTS(
            SELECT 1 FROM $activity_table a
            INNER JOIN $measurement_table m ON a.id = m.activity_id
            WHERE a.hand = %s
            LIMIT 1
        )",
        $hand
    );
    $exists = (bool) $wpdb->get_var($sql_exists);

    if ($exists) {

        $exposure_time = hand_exposure_time($hand);
        if ($exposure_time !== null && $exposure_time > 0) {

            $sql_sum = $wpdb->prepare(
                "SELECT SUM(vector_summ_time) FROM $activity_table WHERE hand = %s",
                $hand
            );
            $total_vector_time = $wpdb->get_var($sql_sum);

            if ($total_vector_time !== null && $total_vector_time > 0) {
                return sqrt($total_vector_time / 480);
            }
        }
    }
    return null;
}


function exceedings_ndn_8h(string $hand): ?float {
    global $wpdb;
    $activity_table = $wpdb->prefix . 'activity';
    $measurement_table = $wpdb->prefix . 'measurement';

    $sql_exists = $wpdb->prepare(
        "SELECT EXISTS(
            SELECT 1 FROM $activity_table a
            INNER JOIN $measurement_table m ON a.id = m.activity_id
            WHERE a.hand = %s
            LIMIT 1
        )",
        $hand
    );
    $exists = (bool) $wpdb->get_var($sql_exists);

    if ($exists) {
        $exposure_time = hand_exposure_time($hand);
        $valueA8 = value_a8($hand);

        if ($valueA8 !== null && $exposure_time !== null && $exposure_time > 30) {

            return round($valueA8 / POSSIBLE_VALUE_8H_NDN, 2);
        }
    }

    return null;
}


function num_values_exceeded(string $hand): ?float {
    global $wpdb;
    $activity_table = $wpdb->prefix . 'activity';
    $measurement_table = $wpdb->prefix . 'measurement';

    $sql_exists = $wpdb->prepare(
        "SELECT EXISTS(
            SELECT 1 FROM $activity_table a
            INNER JOIN $measurement_table m ON a.id = m.activity_id
            WHERE a.hand = %s
            LIMIT 1
        )",
        $hand
    );
    $exists = (bool) $wpdb->get_var($sql_exists);

    if ($exists) {
        $exposure_time = hand_exposure_time($hand);

        if ($exposure_time !== null && $exposure_time <= 30) {
            return exceedings_ndn_05h($hand);
        } elseif (num_impact_lt_30($hand) === 0) {
            return exceedings_ndn_8h($hand);
        } else {
            $exceed_8h = exceedings_ndn_8h($hand);
            $exceed_05h = exceedings_ndn_05h($hand);

            if ($exceed_8h !== null && $exceed_05h !== null) {
                return max($exceed_8h, $exceed_05h);
            }
        }
    }

    return null;
}


function multiplicity_ndn(string $hand): ?float {
    $num_exceeded = num_values_exceeded($hand);

    if ($num_exceeded !== null) {
        return round($num_exceeded, MULTIPLICITIES_ROUN_UP_TO);
    }

    return null;
}


function numb_threshold_values_exceeded(string $hand): ?float {
    global $wpdb;
    $table_activity = $wpdb->prefix . 'activity';
    $table_measurement = $wpdb->prefix . 'measurement';

    $exists = (bool) $wpdb->get_var(
        $wpdb->prepare("
            SELECT 1 FROM $table_activity AS a
            INNER JOIN $table_measurement AS m ON m.activity_id = a.id
            WHERE a.hand = %s LIMIT 1
        ", $hand)
    );

    if ($exists) {
        $valueA8 = value_a8($hand);
        if ($valueA8 !== null) {
            return round($valueA8 / ACTION_THRESHOLD_VALUE, 2);
        }
    }

    return null;
}


function action_threshold_multiplicity(string $hand): ?float {
    global $wpdb;
    $table_activity = $wpdb->prefix . 'activity';
    $table_measurement = $wpdb->prefix . 'measurement';

    $exists = (bool) $wpdb->get_var(
        $wpdb->prepare("
            SELECT 1 FROM {$table_activity} AS a
            INNER JOIN {$table_measurement} AS m ON m.activity_id = a.id
            WHERE a.hand = %s LIMIT 1
        ", $hand)
    );

    if ($exists) {
        $numb_exceeded = numb_threshold_values_exceeded($hand);
        if ($numb_exceeded !== null) {
            return round($numb_exceeded, MULTIPLICITIES_ROUN_UP_TO);
        }
    }

    return null;
}


function exceedings_ndn_05h_women(string $hand): ?float {
    if (num_impact_lt_30($hand) > 0) {
        $max_summ = max_vector_summ_impact_lt_30($hand);
        if ($max_summ !== null) {
            return round($max_summ / POSSIBLE_VALUE_05H_WOM, 2);
        }
    }
    return null;
}


function exceedings_ndn_8h_women(string $hand): ?float {
    $exposure_time = hand_exposure_time($hand);

    if (value_a8($hand) !== null && $exposure_time !== null && $exposure_time > 30) {
        return round(value_a8($hand) / POSSIBLE_VALUE_8H_WOM, 2);
    }

    return null;
}


function num_values_exceeded_pregn_breast(string $hand): ?float {
    $exposure_time = hand_exposure_time($hand);

    if ($exposure_time !== null && $exposure_time <= 30) {
        return exceedings_ndn_05h_women($hand);
    } elseif (num_impact_lt_30($hand) === 0) {
        return exceedings_ndn_8h_women($hand);
    } elseif (exceedings_ndn_8h_women($hand) !== null && exceedings_ndn_05h_women($hand) !== null) {
        return max(exceedings_ndn_8h_women($hand), exceedings_ndn_05h_women($hand));
    }

    return null;
}


function multiplicity_pregnant_breastfeeding(string $hand): ?float {
    $num_exceeded = num_values_exceeded_pregn_breast($hand);
    if ($num_exceeded !== null) {
        return round($num_exceeded, MULTIPLICITIES_ROUN_UP_TO);
    }
    return null;
}


function exceedings_ndn_05h_young(string $hand, bool $old = false): ?float {
    $variable = POSSIBLE_VALUE_05H_YOUNG;

    if ($old) {
        $variable = POSSIBLE_VALUE_05H_YOUNG_WG_2016_POZ_1509;
    }

    if (num_impact_lt_30($hand) > 0) {
        $maxSumm = max_vector_summ_impact_lt_30($hand);
        if ($maxSumm !== null) {
            return round($maxSumm / $variable, 2);
        }
    }

    return null;
}


function exceedings_ndn_8h_young(string $hand, bool $old = false): ?float {
    $variable = POSSIBLE_VALUE_8H_YOUNG;
    $exposure_time = hand_exposure_time($hand);

    if ($old) {
        $variable = POSSIBLE_VALUE_8H_YOUNG_WG_2016_POZ_1509;
    }

    $valueA8 = value_a8($hand);

    if ($valueA8 !== null && $exposure_time !== null && $exposure_time > 30) {
        return round($valueA8 / $variable, 2);
    }

    return null;
}


function num_values_exceeded_young(string $hand, bool $old = false): ?float {
    $exposure_time = hand_exposure_time($hand);

    if ($exposure_time !== null && $exposure_time <= 30) {
        return exceedings_ndn_05h_young($hand, $old);
    } elseif (num_impact_lt_30($hand) === 0) {
        return exceedings_ndn_8h_young($hand, $old);
    } else {
        $exceed_8h = exceedings_ndn_8h_young($hand, $old);
        $exceed_05h = exceedings_ndn_05h_young($hand, $old);

        if ($exceed_8h !== null && $exceed_05h !== null) {
            return max($exceed_8h, $exceed_05h);
        }
    }

    return null;
}


function multiplicity_young(string $hand, bool $old = false): ?float {
    $numValuesExceeded = num_values_exceeded_young($hand, $old);

    if ($numValuesExceeded !== null) {
        return round($numValuesExceeded, MULTIPLICITIES_ROUN_UP_TO);
    }

    return null;
}


function s(Activity $activity, string $axis): ?float {
    $measurementsCount = count($activity->measurements);

    if ($measurementsCount > 1 && $activity->measurement_time_Ti) {

        $ahw_axis = calculate_ahw($activity, $axis);

        $sum_of_square_stdev = 0.0;
        foreach ($activity->measurements as $measurement) {
            switch (strtolower($axis)) {
                case 'x':
                    $diff = $measurement->ax - $ahw_axis;
                    break;
                case 'y':
                    $diff = $measurement->ay - $ahw_axis;
                    break;
                case 'z':
                    $diff = $measurement->az - $ahw_axis;
                    break;
                default:
                    return null;
            }
            $sum_of_square_stdev += pow($diff, 2);
        }
        $result = sqrt($sum_of_square_stdev / ($measurementsCount - 1));
        return round($result, 5);
    }
    return null;
}


function uprobkj(Activity $activity, string $axis) {
    $n = count($activity->measurements);

    if ($activity->measurement_time_Ti) {
        if ($n > 1) {
            $axis = strtolower($axis);
            if ($axis === 'x') {
                if ($activity->s_axis_x && $activity->ahwx) {
                    $uprobkj = $activity->s_axis_x / (sqrt($n) * $activity->ahwx);
                } else {
                    return null;
                }
            } elseif ($axis === 'y') {
                if ($activity->s_axis_y && $activity->ahwy) {
                    $uprobkj = $activity->s_axis_y / (sqrt($n) * $activity->ahwy);
                } else {
                    return null;
                }
            } elseif ($axis === 'z') {
                if ($activity->s_axis_z && $activity->ahwz) {
                    $uprobkj = $activity->s_axis_z / (sqrt($n) * $activity->ahwz);
                } else {
                    return null;
                }
            } else {
                return null;
            }

            return round($uprobkj, 5);
        }

        if ($n === 1) {
            return 0;
        }
    }

    return null;
}


function ucj(Activity $activity, string $axis) {
    if ($activity->measurement_time_Ti) {
        $axis = strtolower($axis);

        if ($axis === 'x') {
            if (isset($activity->uprobkj_x) && $activity->uprobkj_x !== null) {
                $ucj = sqrt(pow($activity->uprobkj_x, 2) + pow(COMBINED_STANDARD_UNCERTAINTY / 100, 2));
            } else {
                return null;
            }
        } elseif ($axis === 'y') {
            if (isset($activity->uprobkj_y) && $activity->uprobkj_y !== null) {
                $ucj = sqrt(pow($activity->uprobkj_y, 2) + pow(COMBINED_STANDARD_UNCERTAINTY / 100, 2));
            } else {
                return null;
            }
        } elseif ($axis === 'z') {
            if (isset($activity->uprobkj_z) && $activity->uprobkj_z !== null) {
                $ucj = sqrt(pow($activity->uprobkj_z, 2) + pow(COMBINED_STANDARD_UNCERTAINTY / 100, 2));
            } else {
                return null;
            }
        } else {
            return null;
        }

        return round($ucj, 5);
    }

    return null;
}


function uhvi(Activity $activity) {
    if (!empty($activity->measurements)) {
        $vector_sum = $activity->vector_summ;

        if ($vector_sum && $vector_sum > 0) {
            if (
                isset($activity->ucj_x, $activity->ucj_y, $activity->ucj_z) &&
                $activity->ucj_x !== null && $activity->ucj_y !== null && $activity->ucj_z !== null
            ) {
                $part_x = pow($activity->ucj_x * pow($activity->ahwx, 2), 2);
                $part_y = pow($activity->ucj_y * pow($activity->ahwy, 2), 2);
                $part_z = pow($activity->ucj_z * pow($activity->ahwz, 2), 2);

                $value = sqrt(($part_x + $part_y + $part_z) / pow($vector_sum, 2));
                $activity->uhvi = round($value, 5);

                return $activity->uhvi;
            } else {
                return null;
            }
        }
    }

    return null;
}


function cai(Activity $activity) {
    $rightValue = value_a8("right");

    if (
        !empty($activity->measurement_time_Ti) &&
        $rightValue &&
        $activity->vector_summ
    ) {
        if ($rightValue > 0) {
            $cai = $activity->measurement_time_Ti * $activity->vector_summ / (480 * $rightValue);
            return $cai;
        }
    }

    return null;
}


function caiuci2(Activity $activity) {
    if (!empty($activity->measurement_time_Ti)) {
        if (!empty($activity->uhvi) && !empty($activity->cai)) {
            return pow($activity->uhvi * $activity->cai, 2);
        }
    }
    return null;
}


function uti_rh(Activity $activity): ?int
{
    $allActivities = Activity::get_all();

    $rightHandExists = false;
    foreach ($allActivities as $act) {
        if (isset($act->hand) && strtolower($act->hand) === Activity::HAND_RIGHT) {
            $rightHandExists = true;
            break;
        }
    }

    if ($rightHandExists) {
        if (!empty($activity->measurement_time_Ti)) {
            return 0;
        }
    }

    return null;
}


function uti_lh(Activity $activity): ?int
{
    $allActivities = Activity::get_all();

    $leftHandExists = false;
    foreach ($allActivities as $act) {
        if (isset($act->hand) && strtolower($act->hand) === Activity::HAND_LEFT) {
            $leftHandExists = true;
            break;
        }
    }

    if ($leftHandExists) {
        if (!empty($activity->measurement_time_Ti)) {
            return 0;
        }
    }

    return null;
}


function cti(Activity $activity): ?float
{

    $valueRight = value_a8('right');

    if (!empty($activity->measurement_time_Ti) && $valueRight && $activity->vector_summ) {
        if ($valueRight > 0) {
            return pow($activity->vector_summ, 2) / (2 * 480 * $valueRight);
        }
    }

    return null;
}


function ctiuti2(Activity $activity): ?float
{
    if (!empty($activity->measurement_time_Ti)) {
        if (strtolower($activity->hand) === 'right') {
            if (!empty($activity->uti_lh) && !empty($activity->cti)) {
                return pow($activity->uti_lh * $activity->cti, 2);
            }
        }
    }

    return null;
}


function uca8(string $hand): ?float
{
    $activities = Activity::get_all();
    $sum_caiuci2 = 0;
    $sum_ctiuti2 = 0;
    $has_measurements = false;

    foreach ($activities as $activity) {
        if (strtolower($activity->hand) === strtolower($hand) && !empty($activity->measurements)) {
            $has_measurements = true;
            $sum_caiuci2 += $activity->caiuci2 ?? 0;
            $sum_ctiuti2 += $activity->ctiuti2 ?? 0;
        }
    }

    if ($has_measurements && ($sum_caiuci2 > 0 || $sum_ctiuti2 > 0)) {
        return sqrt($sum_caiuci2 + $sum_ctiuti2);
    }

    return null;
}


function _2xuca8(string $hand): ?float
{

    $activities = Activity::get_all();
    $has_measurements = false;
    foreach ($activities as $activity) {
        if (strtolower($activity->hand) === strtolower($hand) && !empty($activity->measurements)) {
            $has_measurements = true;
            break;
        }
    }

    if ($has_measurements) {
        $uca8_value = uca8($hand);
        if ($uca8_value !== null) {
            return 2 * $uca8_value;
        }
    }

    return null;
}


function daily_exposure(string $hand): ?string
{

    $activities = Activity::get_all();
    $has_measurements = false;
    foreach ($activities as $activity) {
        if (strtolower($activity->hand) === strtolower($hand) && !empty($activity->measurements)) {
            $has_measurements = true;
            break;
        }
    }

    if ($has_measurements) {
        $exposure_time = hand_exposure_time($hand);

        if ($exposure_time !== null && $exposure_time > 30) {
            $value_a8_hand = value_a8($hand);
            $two_x_uca8_hand = _2xuca8($hand);

            if ($value_a8_hand !== null && $two_x_uca8_hand !== null) {
                $first_value = round($value_a8_hand, RES_ROUND_UP_TO);
                $second_value = round($two_x_uca8_hand, RES_ROUND_UP_TO);
                return "{$first_value} ± {$second_value}";
            }
        }

        return "Nie dotyczy";
    }

    return null;
}


function uahv(Activity $activity): ?float
{
    $time = $activity->measurement_time_Ti;

    if ($time !== null && $time <= 30) {
        return $activity->uhvi;
    }

    return null;
}


function ucahvmax(string $hand): ?float {

    $activities = array_filter(Activity::get_all(), function ($activity) use ($hand) {
        return $activity->hand === $hand
            && $activity->measurement_time_Ti < 30
            && !empty($activity->measurements);
    });

    if (count($activities) === 0) {
        return null;
    }

    $count_impact_lt_30 = num_impact_lt_30($hand);

    if ($count_impact_lt_30 > 1) {

        $max_uahv = null;
        foreach ($activities as $activity) {
            if ($activity->uahv !== null) {
                if ($max_uahv === null || $activity->uahv > $max_uahv) {
                    $max_uahv = $activity->uahv;
                }
            }
        }
        return $max_uahv !== null ? round($max_uahv, 5) : null;

    } elseif ($count_impact_lt_30 === 1) {

        foreach ($activities as $activity) {
            if ($activity->uahv !== null) {
                return round($activity->uahv, 5);
            }
        }
        return null;
    }

    return null;
}


function exposure_30_less(string $hand): ?string {

    $activities = Activity::get_all();
    $filtered = array_filter($activities, function ($a) use ($hand) {
        return $a->hand === $hand && !empty($a->measurements);
    });

    if (empty($filtered)) {
        return null;
    }

    $under_30 = array_filter($filtered, function ($a) {
        return $a->measurement_time_Ti < 30;
    });

    if (empty($under_30)) {
        return "Nie dotyczy";
    }

    $max_vector = max_vector_summ_impact_lt_30($hand);
    $uahv = ucahvmax($hand);

    if ($max_vector !== null && $uahv !== null) {
        return round($max_vector, 2) . " ± " . round($uahv * 2, 2);
    }

    return "Nie dotyczy";
}


