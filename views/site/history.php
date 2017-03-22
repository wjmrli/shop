<?php

use yii\helpers\Html;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '历史记录';
?>
<div class="record-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [

            [
                'label' => '日期',
                //'attribute' => 'Time',
                'value' => function($model){
                    return date('Y/m/d',$model->Time);
                },
            ],
            [
                'label' => '销售额',
                'attribute' => 'Prices',
            ],
            [
                'label' => '利润',
                'attribute' => 'Profits',
            ],

            ['class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{view}',
            'buttons'=>[
                'view' => function ($url, $model, $key) {
                  $options = [
                    'title' => 'detail',
                    'aria-label' => '详情',
                  ];
                  return Html::a('详情', null, $options);
                },
            ],
            ],
        ],
    ]); ?>
</div>
<script>

function fillpanel(msg)
{
    
}

$("a[title='detail']").click(function(){
    var i = $(this);
    $.ajax({
        async:false,
        url:'index.php',
        type:'get',
        data:{
            r:"site/detail",
            id:i.parent().parent().find('td:first').text()
        },
        success:function(msg){
            if(msg!="")
            {
                $('#shadow').css('display','block').css('height','100%').css('width','100%');
                $('#panel').find('pre').html(msg);
                $('#panel').css('margin-top',$('#shadow').height()*0.2).css('width','100%').css('height','auto <600').fadeIn(500);
                $('#shadow').click(function(){
                    $('#panel').fadeOut(200);
                    $('#shadow').css('display','none');
                    $('#panel').find('pre').html('');
                });
            }
        }
    });
})
</script>