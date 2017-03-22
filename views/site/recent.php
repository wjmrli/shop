<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?><div class="record-index"><?php Pjax::begin(); ?><?= GridView::widget([
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
                'label' => '详情',
                'value' => function($model){
                    return $model->Number.'个 - '.$model->Price.'元';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{delete}',
                'buttons'=>[
                    'delete' => function ($url, $model, $key) {
                      $options = [
                        'title' => 'delete',
                        'aria-label' => '删除',
                        'onclick' => 'delete_recent("'.$key.'")',
                      ];
                      return Html::a('删除', null, $options);
                    },
                ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<script>
function delete_recent(id)
{
    $.ajax({
        async:false,
        url:'index.php',
        type:'get',
        data:{
            r:"site/delete",
            id:id
        },
        success:function(msg){
            if(msg!="")
            {
                fill_panel(msg);
            }
        }
    });
}
</script>