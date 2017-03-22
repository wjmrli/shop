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
                'itemView' => '_post',
            ]); ?>
            <?php if(!isset($_SESSION))session_start();
            if($_SESSION['father']!=''){
                echo '<div class="col-lg-12 basebar">
                    <div style="text-align: center;">'.Html::a('添加新品种', ['create'], ['style'=>'color:black']).'</div>
                </div>';   
            }?>
</div>
<script>
function removecontent(html)
{
    $('#w0').parent().parent().html(html);
}

function item_click(q){
    var i = $(q).parent().parent();
    var number = i.find('div:eq(2)').find('span:first').text();
    price = i.find('div:eq(2)').find('span:last').text();
    $.ajax({
        async:false,
        url:'index.php',
        type:'get',
        data:{
            r:"site/index",
            father_id:i.attr('data-key'),
            number:number,
            price:price
        },
        success:function(msg){
            if(msg!="")
            {
                removecontent(msg);
            }
        }
    });
}

$('.basebar').find('span').click(function(){
    var i = $(this);
    i.parent().prev().removeClass('col-xs-5').addClass('col-xs-3');
    i.parent().removeClass('col-xs-5').addClass('col-xs-7');
    var tmp = i.text();
    i.after("<input style=\"width:70px\" type=\"number\" />").hide();
    $('.basebar input').focus().blur(function(){
        if($(this).val() == ''){
            i.show();
            $(this).hide();
            i.parent().prev().removeClass('col-xs-3').addClass('col-xs-5');
            i.parent().removeClass('col-xs-7').addClass('col-xs-5');
        }
        else if(!isNaN($(this).val())){
            i.text($(this).val()).show();
            $(this).hide();
            i.parent().prev().removeClass('col-xs-3').addClass('col-xs-5');
            i.parent().removeClass('col-xs-7').addClass('col-xs-5');
        }
        else{
            alert("必须是数字");
            $(this).focus();
        }
    })
    
})

$('.basebar').find('div:first').click(function(){
    $(this).next().fadeOut('200',function(){
    $(this).prev().removeClass('col-xs-5').addClass('col-xs-10')});
    var i = $(this);
    setTimeout(function(){
        i.removeClass('col-xs-10').addClass('col-xs-5');
        i.next().fadeIn('200');
    },2000,i);
})
</script>
