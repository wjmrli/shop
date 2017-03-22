<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use app\models\Record;
$sale = Record::find()->where(['LID'=>$model->Son_ID])->sum('Number');
?>
<div class="col-xs-12 basebar">
    <div class="col-xs-5"><?=$model->Name?></div><div class="col-xs-5" style="text-align: center;color: gray"><span>1</span>个&nbsp;-&nbsp;<span><?=$model->Def?></span>元</div><div class="col-xs-2 item_button" onclick="item_click(this)">+</div>
</div>    
<div style="font-size: 20px;">今日累计：<?=$sale?></div>