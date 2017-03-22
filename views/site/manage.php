<?php
use yii\helpers\Html;
$this->title = $base['title'];
?>
<div class="col-lg-3">
    <div class="bar">
        <div class="bar_pa">帮派管理</div>
        <a href="/index.php?r=site/manage&id=1"><div class="bar_son">帮派信息编辑</div></a>
        <a href="/index.php?r=site/manage&id=2"><div class="bar_son">管理人员设置</div></a>
    </div>
</div>
<div class="col-lg-9">
    <div id="page">
        <h2><strong><?=$base['Head']?></strong></h2>
        <p><?=$base['introduce']?></p>
        <?php if(isset($level))
                echo $this->render('page-manage',[
                    'level' => $level,
                    'persons' => $persons,
                ]);
            else if(isset($model))
                echo $this->render('_form', [
                    'model' => $model,
                    'daqv' => $daqv,
                    'fuwuqi' => $fuwuqi,
                ]); ?>
    </div>
</div>