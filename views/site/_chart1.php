<?php

use miloschuman\highcharts\Highcharts;
use yii\data\ActiveDataProvider;

/** @var ActiveDataProvider $dataProvider */

$logsModels = $dataProvider->models;
$dates = [];
$countRequests = [];
foreach($logsModels as $logsModel) {
    $dates[] = $logsModel->dateDay;
    $countRequests[] = (int) $logsModel->count;
}

?>
<?=
    Highcharts::widget([
        'options' => [
            'title' => ['text' => 'Число запросов'],
            'xAxis' => [
                'categories' => $dates
            ],
            'yAxis' => [
                'title' => ['text' => 'Число запросов']
            ],
            'series' => [
                ['name' => 'Зависисмость числа запросов от даты', 'data' => $countRequests]
            ]
        ]
    ]);
?>
