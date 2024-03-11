<?php

use app\models\search\LogsNginxSearch;
use yii\base\View;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use miloschuman\highcharts\Highcharts;

/** @var View $this */
/** @var ActiveDataProvider $dataProvider */
/** @var LogsNginxSearch $searchModel */
/** @var array $dataChart */


$this->title = 'My Yii Application';
?>
<div class="site-index">
    <?= $this->render('_search', ['searchModel' => $searchModel]) ?>

    <h3>Таблица</h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'dateDay',
            'count',
            'urlPopular',
            'browserPopular'
        ]
    ])
    ?>
    <div class="row">
        <div class="col-md-6 col-12">
            <?= $this->render('_chart1', ['dataProvider' => $dataProvider]) ?>
        </div>
        <div class="col-md-6 col-12">
            <?= $this->render('_chart2', ['dataChart' => $dataChart]) ?>
        </div>
    </div>

</div>
