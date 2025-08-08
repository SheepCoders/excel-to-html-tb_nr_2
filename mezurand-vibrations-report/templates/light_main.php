<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arkusz Drgań Miejscowych</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
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
                <tr class="yellow-header" >
                    <th class="text-center" rowspan="2">Nazwa/logotyp użytkownika arkusza <i>(mój klient subskrybujący arkusz)</i></th>
                    <th class="text-center" colspan="6">Dane Klienta: nazwa, adres</th>
                    <th class="text-center" colspan="2" rowspan="8"></th>
                </tr>
                <tr class="yellow-header">
                    <th class="text-center" colspan="6">Miejsce wykonywania pomiarów/pobierania próbek: nazwa, adres.</th>

                </tr>
                <tr class="yellow-header">
                    <th class="text-center" colspan="3">Numer zlecenia:</th>
                    <th class="text-center" colspan="3">Numer sprawozdania:</th>
                    <th class="text-center">Data wykonywania pomiarów/pobierania próbek:</th>

                </tr>
                <tr class="yellow-header">
                    <th class="text-center" colspan="7">Karta pomiarów oświetlenia numer 1</th>

                </tr>
                <tr class="yellow-header">
                    <th colspan="7">Metodyka badań:</th>

                </tr>
                <tr class="yellow-header">
                    <th class="text-center" colspan="2"></th>
                    <th class="text-center" colspan="3"><i class="header">Wartości zmierzone</i></th>
                    <th class="text-center" colspan="2"><i class="header">Wartości normatywne</i></th>

                </tr>
                <tr class="yellow-header">
                    <th class="text-center"><i class="header">Lokalizacja stanowiska pracy</i></th>
                    <th class="text-center" rowspan="2"><i class="header">Płaszczyzna pomiarowa</i></th>
                    <th class="text-center" rowspan="2"><i class="header">Odczyty jednostkowe natężenia oświetlenia [lx]</i></th>
                    <th class="text-center" rowspan="2"><i class="header">Średnie natężenie oświetlenia [lx] X±U</i></th>
                    <th class="text-center" rowspan="2"><i class="header">Równomierność oświetlenia* δ±U</i></th>
                    <th class="text-center" rowspan="2"><i class="header">Natężenie oświetlenia [lx]</i></th>
                    <th class="text-center" rowspan="2"><i class="header">Równomierność oświetlenia δ</i></th>

                </tr>
                <tr class="yellow-header">
                    <th class="text-center"><i class="header">Rodzaj/ilość oświetlenia</i></th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($location_list)): ?>
                    <tr>
                        <td colspan="16" class="text-center">Brak danych</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($location_list as $location): ?>

                        <tr class="measurement-row">
                            <td colspan="16"></td>
                        </tr>


                        <tr class="measurement-row table-top" style="font-weight: bold;">
                            <th class="fa-align-right" rowspan="<?php echo count($location->obrazy) + 1; ?>" style="font-size: 14px;">
                               <?php echo htmlspecialchars($location->description_location ?? '')
                                        . ' / ' .
                                        htmlspecialchars($location->description_light ?? '');?>
                            </th>
                            <th colspan="6"></th>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg('edit', $location->id, site_url('/location'))); ?>" class="btn btn-warning btn-custom">Akt.</a>
                            </td>
                            <td>
                                <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>" onsubmit="return confirm('Czy na pewno chcesz usunąć tę lokalizacja ?');">
                                    <input type="hidden" name="action" value="delete_location">
                                    <?php wp_nonce_field('delete_location_' . $location->id, 'delete_location_nonce'); ?>
                                    <input type="hidden" name="id" value="<?= esc_attr($location->id) ?>">
                                    <button type="submit" class="btn btn-danger btn-custom">Usuń</button>
                                </form>
                            </td>
                        </tr>

                        </tr>
                            <?php if (empty($location->obrazy)): ?>
                                <tr>
                                    <td colspan="16" class="text-center">Brak pól/obszarów</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($location->obrazy as $obraz): ?>
                                    <tr class="measurement-row text-center table-down">
                                        <td><?php echo $obraz->obraz_plane !== null ? $obraz->obraz_plane : '-'; ?></td>
                                        <?php
                                        $validReadings = [];
                                        for ($i = 1; $i <= 30; $i++) {
                                            $reading = $obraz->{'reading_' . $i};
                                            if ($reading !== null && $reading != 0) { // Пропускаем null и 0
                                                $validReadings[] = $reading;
                                            }
                                        }
                                        ?>
                                        <td><?= !empty($validReadings) ? implode(', ', $validReadings) : '-' ?></td>
                                        <td><?php echo $obraz->average_illuminance !== null ? $obraz->average_illuminance : '-'; ?></td>
                                        <td><?php echo $obraz->illumination_uniformity !== null ? $obraz->illumination_uniformity : '-'; ?></td>
                                        <td><?php echo $obraz->normative_illuminance !== null ? $obraz->normative_illuminance : '-'; ?></td>
                                        <td><?php echo $obraz->normative_illumination_uniformity !== null ? $obraz->normative_illumination_uniformity : '-'; ?></td>

                                        <td>
                                            <a href="<?php echo esc_url(add_query_arg('edit', $obraz->id, site_url('/obraz'))); ?>" class="btn btn-warning btn-custom" style="font-size: 12px;">Akt.</a>
                                        </td>
                                        <td>
                                            <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>" onsubmit="return confirm('Czy na pewno chcesz usunąć te pole/obszar?');">
                                                <input type="hidden" name="action" value="delete_obraz">
                                                <?php wp_nonce_field('delete_obraz_' . $obraz->id, 'delete_obraz_nonce'); ?>
                                                <input type="hidden" name="id" value="<?= esc_attr($obraz->id) ?>">
                                                <button type="submit" class="btn btn-danger btn-custom">Usuń</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr class="measurement-row table-top">
                                <td colspan="16" class="text-center">
                                     <a href="<?= esc_url(site_url('/obraz')) ?>" class="btn btn-success btn-custom">Dodaj kolejne pole/obszar</a>
                                </td>
                            </tr>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Buttons -->
        <div class="mt-4">
            <a href="<?= esc_url(site_url('/location')) ?>" class="btn btn-primary btn-custom">Dodaj kolejną lokalizacja </a>
        </div>

        <br>
            <!-- Table -->
        <table class="table table-bordered">


            <tr class="measurement-row results ">
                <td colspan="5"><i class="header">wynik sprawdzenia luksomierza przed serią pomiarów [lx]</i></td>
                <td colspan="5">
                    <?php echo $location->luks_before; ?><br>
                </td>
            </tr>
            <tr class="measurement-row results ">
                <td colspan="5"><i class="header">wynik sprawdzenia luksomierza po serii pomiarów [lx]</i></td>
                <td colspan="5">
                    <?php echo $location->luks_after; ?><br>
                </td>
            </tr>
            <tr class="measurement-row results ">
                <td colspan="10">
                    Legenda do opisu oświetlenia stanowiska pracy:<br>
                    <?php foreach ($location_list as $location): ?>
                            <?php echo $location->description_location; ?> : <?php echo $location->legend; ?><br>
                        <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <td colspan="10">* U - niepewność rozszerzona przy jednostronnym poziomie ufności p=95% i współczynniku rozszerzenia k=2</td>
            </tr>
            <tr>
                <td>
                    Uwagi:<br>
                    <?php foreach ($location_list as $location): ?>
                        <?php echo $location->description_location; ?> : <?php echo $location->uwagi; ?><br>
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>