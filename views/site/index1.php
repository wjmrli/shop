<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BangpaiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = isset($_SESSION['Title'])?$_SESSION['Title']:'士多';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-lg-12">
            <?php if(isset($dataProvider)) echo ListView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'itemView' => '_postfat',
            ]); ?>
</div>
<script>
function removecontent(html)
{
    $('#w0').parent().parent().html(html);
}

function item_click(q){
    var i = $(q).parent().parent();
    $.ajax({
        async:false,
        url:'index.php',
        type:'get',
        data:{
            r:"site/index",
            father_id:i.attr('data-key')
        },
        success:function(msg){
            if(msg!="")
            {
                removecontent(msg);
            }
        }
    });
}
</script>