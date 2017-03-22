<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language = 'zh-CN' ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="wIDth=device-wIDth, initial-scale=1"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div id="shadow"></div>
    <div id="sider" title="帮助"><strong>？</strong></div>
    <div id="panel"><pre></pre></div>
    <?php
    NavBar::begin([
        'brandLabel' => isset($_SESSION['Title'])?$_SESSION['Title']:'士多',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    if(Yii::$app->user->isGuest)
    {
        $navItems = [['label' => '未登录',
                    'items' => [
                        ['label' => '登录', 'url' => ['/site/login']],
                        ['label' => '注册', 'url' => ['/site/register']],
                    ]
                ]];
    }
    else{
        $navItems = [];
        if(Yii::getdb()!=null)
        {
            $navItems = [
            ['label' => '历史', 'url' => ['/site/history']],
            ['label' => '最近6条记录', 'options' => ['id' => 'recent']],
            ['label' => '商品列表', 'url' => ['/site/all']],
            ];
        }
        array_push($navItems,['label' => '注销 (' . Yii::$app->user->IDentity->username . ')', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']]);
    }
    echo Nav::wIDget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $navItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container" style="text-align: center;">
    <?php if(!Yii::$app->user->isGuest) { ?><img src="<?=Url::to('@web/refresh.png')?>" style="height:30px;width:auto" onclick="refresh_profit()"/>&nbsp;&nbsp;<strong style="font-size: 18px;">今日利润&nbsp;&nbsp;<span></span>&nbsp;&nbsp;元</strong><?php }?>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<script>
function refresh_profit()
{
    $.ajax({
        async:false,
        url:'index.php',
        type:'get',
        data:{
            r:"site/profit"
        },
        success:function(msg){
            if(msg!="")
            {
                $('.footer').find('span:first').text(msg);
                setTimeout(function(){$('.footer').find('span:first').text('');},5000);
            }
        }
    });
}

function fill_panel(msg)
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
$("#recent").click(function(){
    var i = $(this);
    $.ajax({
        async:false,
        url:'index.php',
        type:'get',
        data:{
            r:"site/recent"
        },
        success:function(msg){
            if(msg!="")
            {
                fill_panel(msg);
            }
        }
    });
})
</script>