<?php

use miloschuman\highcharts\Highcharts;
use yii\data\ActiveDataProvider;

/** @var array $dataChart */

$dates = [];
$browserRequests = [];
foreach($dataChart as $data) {
    if (!in_array($data['dateDay'], $dates)) {
        $dates[] = $data['dateDay'];
    }

    if (!isset($browserRequests[$data['browser']])) {
        $browserRequests[$data['browser']] = [];
    }

    $browserRequests[$data['browser']][] = (int) $data['precent'];
}

$data = [];
foreach ($browserRequests as $browser => $requests) {
    $data[] = ['name' => $browser, 'data' =>$requests];
}
?>
<?=
    Highcharts::widget([
        'options' => [
            'title' => ['text' => 'Доля запросов'],
            'xAxis' => [
                'categories' => $dates
            ],
            'yAxis' => [
                'title' => ['text' => 'Доля запросов']
            ],
            'series' => $data
        ]
    ]);
?>
