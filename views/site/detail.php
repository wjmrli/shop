<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?><div class="record-index"><h3 style="text-align: center;"><strong><?=date('Y/m/d',$date)?></strong></h3><?php Pjax::begin(); ?><?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'pager' => [
            'options' => ['class' => 'hidden'],
        ],
        'columns' => [
            [
                'label' => '品名',
                'attribute' => 'item.Name',
            ],
            [
                'label' => '销量',
                'attribute' => 'Number',
            ],
            [
                'label' => '销售额',
                'attribute' => 'Price',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?><div style="text-align: right;line-height: 16px">累计销售额：<?=$dataProvider->query->sum('Price')?><br />
累计利润：<?=$dataProvider->query->sum('Profit')?></div></div>