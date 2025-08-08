<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arkusz Drgań Miejscowych</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .yellow-header {
            background-color: #ffff8933;
        }
        .table-top {
            background-color: #81d4fa44;
        }
        .table-down {
            background-color: #81d4fa22;
        }
        .results {
            background-color: #F5F5F5;
        }
        .results-top {
            background-color: #D3D3D3;
        }
        .green-btn {
            background-color: #28a745;
            color: white;
        }
        .red-btn {
            background-color: #dc3545;
            color: white;
        }
        .orange-btn {
            background-color: rgba(236, 146, 80, 0.93);
            color: white;
        }
        .btn-custom {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .measurement-row {
            border-top: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">

        <!-- Table -->
        <table class="table table-bordered">
            <thead>
                <tr class="yellow-header">
                    <th class="text-center" rowspan="3">Wykonywana czynność / źródło drgań / warunki pomiarów</th>
                    <th class="text-center" rowspan="3">Miejsce, orientacja osi oraz metoda mocowania przetwornika</th>
                    <th class="text-center" rowspan="3">Czas ekspozycji Ti [min]:</th>
                    <th colspan="3" class="text-center">Odczyty jednostkowe</th>
                    <th class="text-center" rowspan="3">Ilości miejsc po przecinku</th>
                    <th colspan="4" class="text-center">Skuteczne ważone częstotliwościowo przyspieszenie drgań</th>
                    <th class="text-center" rowspan="3">Cząstkowa ekspozycja  Ai (8) [m/s2]</th>
                    <th colspan="2" rowspan="3"></th>

                </tr>
                <tr class="yellow-header">
                    <th style="font-size: 14px;" class="text-center" colspan="3">Składowe kierunkowe<br>[m/s²]</th>
                    <th style="font-size: 14px;" class="text-center" colspan="3">Składowe kierunkowe<br>[m/s²]</th>
                    <th style="font-size: 11px;" class="text-center" rowspan="2">Suma wektorowa ahv [m/s2]</th>
                </tr>
                <tr class="yellow-header">
                    <th style="font-size: 11px;" class="text-center">Ax<br>[m/s²]</th>
                    <th style="font-size: 11px;" class="text-center">Ay<br>[m/s²]</th>
                    <th style="font-size: 11px;" class="text-center">Az<br>[m/s²]</th>
                    <th style="font-size: 11px;" class="text-center">ahwx<br>[m/s²]</th>
                    <th style="font-size: 11px;" class="text-center">ahwy<br>[m/s²]</th>
                    <th style="font-size: 11px;" class="text-center">ahwz<br>[m/s²]</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activity_list)): ?>
                    <tr>
                        <td colspan="16" class="text-center">Brak danych</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($activity_list as $activity): ?>

                        <tr class="measurement-row">
                            <td colspan="16"></td>
                        </tr>

                        <tr class="measurement-row table-top" style="font-weight: bold;">
                            <td class="fa-align-right" rowspan="<?php echo count($activity->measurements) + 1; ?>" style="font-size: 14px;">
                                Nazwa: <?php echo htmlspecialchars($activity->act_name); ?><br>
                                Czas trwania pomiaru: <?php echo htmlspecialchars($activity->time_Tp); ?><br>
                                <?php echo htmlspecialchars($activity->description_source_measuring); ?><br>
                            </td>
                            <td rowspan="<?php echo count($activity->measurements) + 1; ?>" class="align-middle text-center">
                                <?php echo ucfirst($activity->hand); ?>
                            </td>
                            </td>
                            <td rowspan="<?php echo count($activity->measurements) + 1; ?>" class="align-middle text-center">
                                <?php echo htmlspecialchars($activity->measurement_time_Ti); ?><br>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="align-middle text-center"><?php echo htmlspecialchars($activity->round_up_to); ?></td>
                            <td class="align-middle text-center">
                                <?php echo $activity->rounded_ahwx !== null ? $activity->rounded_ahwx : '-'; ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php echo $activity->rounded_ahwy !== null ? $activity->rounded_ahwy : '-'; ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php echo $activity->rounded_ahwz !== null ? $activity->rounded_ahwz : '-'; ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php echo $activity->rounded_vector_summ !== null ? $activity->rounded_vector_summ : '-'; ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php echo $activity->partial_exposure !== null ? $activity->partial_exposure : '-'; ?>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg('edit', $activity->id, site_url('/activity'))); ?>" class="btn btn-warning btn-custom">Akt.</a>
                            </td>
                            <td>
                                <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>" onsubmit="return confirm('Czy na pewno chcesz usunąć tę czynność?');">
                                    <input type="hidden" name="action" value="delete_activity">
                                    <?php wp_nonce_field('delete_activity_' . $activity->id, 'delete_activity_nonce'); ?>
                                    <input type="hidden" name="id" value="<?= esc_attr($activity->id) ?>">
                                    <button type="submit" class="btn btn-danger btn-custom">Usuń</button>
                                </form>
                            </td>
                        </tr>

                        <?php if (empty($activity->measurements)): ?>
                            <tr>
                                <td colspan="16" class="text-center">Brak odczytów</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($activity->measurements as $measurement): ?>
                                <tr class="measurement-row text-center table-down">
                                    <td><?php echo $measurement->ax !== null ? $measurement->ax : '-'; ?></td>
                                    <td><?php echo $measurement->ay !== null ? $measurement->ay : '-'; ?></td>
                                    <td><?php echo $measurement->az !== null ? $measurement->az : '-'; ?></td>
                                    <td colspan="6"></td>
                                    <td>
                                        <a href="<?php echo esc_url(add_query_arg('edit', $measurement->id, site_url('/measurement'))); ?>" class="btn btn-warning btn-custom" style="font-size: 12px;">Akt.</a>
                                    </td>
                                    <td>
                                        <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>" onsubmit="return confirm('Czy na pewno chcesz usunąć ten odczyt?');">
                                            <input type="hidden" name="action" value="delete_measurement">
                                            <?php wp_nonce_field('delete_measurement_' . $measurement->id, 'delete_measurement_nonce'); ?>
                                            <input type="hidden" name="id" value="<?= esc_attr($measurement->id) ?>">
                                            <button type="submit" class="btn btn-danger btn-custom">Usuń</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="measurement-row table-top">
                            <td colspan="16" class="text-center">
                                 <a href="<?= esc_url(site_url('/measurement')) ?>" class="btn btn-success btn-custom">Dodaj kolejny odczyt</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Buttons -->
        <div class="mt-4">
            <a href="<?= esc_url(site_url('/activity')) ?>" class="btn btn-primary btn-custom">Dodaj kolejną czynność</a>
        </div>

        <br>
            <!-- Table -->
        <table class="table table-bordered">
            <thead>
                <tr class ="results-top">
                    <th class="text-center">Wyniki</th>
                    <th class="text-center">Lewa ręka</th>
                    <th class="text-center">Prawa ręka</th>

                </tr>
            </thead>
            <tbody>
                <tr class="measurement-row results " style="font-weight: bold;">
                    <td>
                        Dzienna ekspozycja na drgania A (8) ±U* [m/s2]
                        Wartość dopuszczalna NDN = 2.8 m/s2
                    </td>
                    <td><?php echo !empty($indicator->daily_exposure_lh) ? $indicator->daily_exposure_lh : '-'; ?></td>
                    <td><?php echo !empty($indicator->daily_exposure_rh) ? $indicator->daily_exposure_rh : '-'; ?></td>
                </tr>
                <tr class="measurement-row results " style="font-weight: bold;">
                    <td>
                        Ekspozycja trwająca 30 min i krócej ahv max ±U* [m/s2]
                        Wartość dopuszczalna NDN = 11,2 m/s2
                    </td>
                    <td><?php echo !empty($indicator->exposure_30_less_lh) ? $indicator->exposure_30_less_lh : '-'; ?></td>
                    <td><?php echo !empty($indicator->exposure_30_less_rh) ? $indicator->exposure_30_less_rh : '-'; ?></td>
                </tr>
                <tr class="measurement-row results " style="font-weight: bold;">
                    <td>
                        Krotność wartości dopuszczalnych (NDN) wg  Dz.U. z 2018 r., poz. 1286
                    </td>
                    <td><?php echo !empty($indicator->multiplicity_NDN_lh) ? $indicator->multiplicity_NDN_lh : '-'; ?></td>
                    <td><?php echo !empty($indicator->multiplicity_NDN_rh) ? $indicator->multiplicity_NDN_rh : '-'; ?></td>
                </tr>
                <tr class="measurement-row results " style="font-weight: bold;">
                    <td>
                        Krotność  progu działania wg Dz.U. z 2005 r. nr 157, poz. 1318
                        Wartość progu działania wynosi 2,5 m/s2
                    </td>
                    <td><?php echo !empty($indicator->action_threshold_multiplicity_lh) ? $indicator->action_threshold_multiplicity_lh : '-'; ?></td>
                    <td><?php echo !empty($indicator->action_threshold_multiplicity_rh) ? $indicator->action_threshold_multiplicity_rh : '-'; ?></td>
                </tr>
                <tr class="measurement-row results " style="font-weight: bold;">
                    <td>
                        Krotność  wartości dopuszczalnych wg Dz.U. z 2017 r., poz. 796
                        (kobiety w ciąży i karmiące piersią)
                    </td>
                    <td><?php echo !empty($indicator->multiplicity_pregnant_breastfeeding_lh) ? $indicator->multiplicity_pregnant_breastfeeding_lh : '-'; ?></td>
                    <td><?php echo !empty($indicator->multiplicity_pregnant_breastfeeding_rh) ? $indicator->multiplicity_pregnant_breastfeeding_rh : '-'; ?></td>
                </tr>
                <tr class="measurement-row results " style="font-weight: bold;">
                    <td>
                        Krotność wartości dopuszczalnej wg 2016 poz.1509 (młodociani)
                    </td>
                    <td><?php echo !empty($indicator->multiplicity_young_wg_2016_poz_1509_lh) ? $indicator->multiplicity_young_wg_2016_poz_1509_lh : '-'; ?></td>
                    <td><?php echo !empty($indicator->multiplicity_young_wg_2016_poz_1509_rh) ? $indicator->multiplicity_young_wg_2016_poz_1509_rh : '-'; ?></td>
                </tr>
                <tr class="measurement-row results " style="font-weight: bold;">
                    <td>
                        Krotność wartości dopuszczalnych wg Dz.U. z 2023 , poz. 1240 (młodociani)
                    </td>
                    <td><?php echo !empty($indicator->multiplicity_young_lh) ? $indicator->multiplicity_young_lh : '-'; ?></td>
                    <td><?php echo !empty($indicator->multiplicity_young_rh) ? $indicator->multiplicity_young_rh : '-'; ?></td>
                </tr>
                <tr>
                    <td colspan="3">
                        *U - złożona niepewność  rozszerzona przy poziomie ufności 95% i współczynniku rozszerzenia k=2
                        Dzienna ekspozycja na drgania A (8) – wyrażona w postaci równoważnej energetycznie dla 8
                        godzin działania sumy wektorowej skutecznych, ważonych częstotliwościowo przyspieszeń drgań,
                        wyznaczonych dla trzech składowych kierunkowych (ahwx, ahwy, ahwz).
                        Ekspozycja trwająca 30 min i krócej ahv max – wyrażona w postaci największej sumy wektorowej
                        skutecznych, ważonych częstotliwościowo przyspieszeń drgań wyznaczonych dla trzech składowych
                        kierunkowych  (ahwx, ahwy, ahwz) wyznaczonej spośród sum wektorowych dla poszczególnych czynności
                        trwających nie dłużej niż 30 minut.
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        Uwagi:<br>
                        <?php foreach ($activity_list as $activity): ?>
                            <?php echo $activity->act_name; ?> : <?php echo $activity->comments; ?><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

</body>
</html>