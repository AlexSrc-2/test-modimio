<?php

use app\models\search\LogsNginxSearch;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var LogsNginxSearch $searchModel */

?>
<div class="site-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]) ?>
    <h3>Фильтры</h3>
    <div class="row">
        <div class="col-md-4 col-12">
            <div class="input-group drp-container d-block">
                <?= $form->field($searchModel, 'rangeDate')->widget(
                    DateRangePicker::className(),
                    [
                        'attribute' => 'rangeDate',
                        'useWithAddon' => false,
                        'convertFormat' => true,
                        'startAttribute' => 'startDate',
                        'endAttribute' => 'endDate',
                        'pluginOptions'=>[
                            'locale'=>['format' => 'Y-m-d'],
                        ]
                    ]
                ) ?>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <?= $form->field($searchModel, 'platform')->textInput() ?>
        </div>
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>
