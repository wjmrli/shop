<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Item */

$this->title = '修改条目: ';
$this->params['breadcrumbs'][] = ['label' => '商品列表', 'url' => ['all']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
