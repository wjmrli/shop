<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use app\models\Record;
use app\models\Item;
$num = Record::find()->where(['LID'=>$model->Son_ID])->sum('Number');
if($num == null){
    $sons = Item::find()->where(['Father_ID'=>$model->Son_ID])->indexBy('Son_ID')->all();
    $num = 0;
    if(!empty($sons)){
        foreach($sons as $key => $son){
            $num += Record::find()->where(['LID'=>$key])->sum('Number');
        }
    }
}
?>
<div class="col-lg-12 basebar">
    <div style="text-align: center;" onclick="item_click(this)"><?=$model->Name?></div>
</div>    
<div style="font-size: 20px;">今日累计：<?=$num?></div>
