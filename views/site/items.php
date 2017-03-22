<?php

use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品列表';
?>
<div class="record-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => '品名',
                'attribute' => 'Name',
            ],
            [
                'label' => '进货价',
                'attribute' => 'Cost',
            ],
            [
                'label' => '单价',
                'attribute' => 'Def',
            ],

            ['class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{update}',
            'buttons'=>[
                'update' => function ($url, $model, $key) {
                  $options = [
                    'title' => 'update',
                    'aria-label' => '修改',
                  ];
                  return Html::a('修改', $url, $options);
                },
            ],
            ],
        ],
    ]); ?>
</div>