<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Userinfo */

$this->title = '注册';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="userinfo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formreg', [
        'model' => $model,
    ]) ?>

</div>
