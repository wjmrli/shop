<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Userinfo;
use app\models\RecordSearch;
use app\models\Record;
use app\models\ItemSearch;
use app\models\Item;
use app\models\Result;
use app\models\ResultSearch;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class SiteController extends Controller
{
    public static $level = [
                        'owner' => '帮主',
                        'm' => '管理员', ];
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'history', 'all', 'profit', 'detail', 'set', 'create', 'update', 'login', 'logout', 'register', 'recent', 'delete', 'contact', 'about', 'setmanage', 'manage', 'intro', 'principle'],
                'rules' => [
                    [
                        'actions' => ['index', 'history', 'all', 'profit', 'detail', 'set', 'create', 'update', 'logout', 'recent', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['register', 'login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'delete' => ['get'],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex($father_id = '',$number = 1,$price = 0)
    {
        if(!Yii::$app->user->isGuest)
        {
            if(Yii::getdb() == '')$this->actionSet(Yii::$app->user->IDentity->id);
            if(Item::find()->count()==0)$this->first_visit();
            $this->Calc();
            $searchModel = new ItemSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            if(!isset($_SESSION))session_start();
            $_SESSION['Title'] = '首页';
            if($father_id == ''){
                $_SESSION['father'] = $father_id;
                $dataProvider->query->where(['Father_ID' => null]);
                if (Yii::$app->getRequest()->isAjax)
                return $this->renderPartial('index1', [
                    'dataProvider' => $dataProvider,
                ]);
                else return $this->render('index1', [
                    'dataProvider' => $dataProvider,
                ]);
            }
            elseif(Item::find()->where(['Father_ID'=>$father_id])->exists()){
                $_SESSION['father'] = $father_id;
                $dataProvider->query->where(['Father_ID' => $father_id]);
                if (Yii::$app->getRequest()->isAjax)
                return $this->renderPartial('index', [
                    'dataProvider' => $dataProvider,
                ]);
                else return $this->render('index', [
                    'dataProvider' => $dataProvider,
                ]);
            }else{
                $model = new Record();
                $model->LID = $father_id;
                $model->Time = time();
                $model->Number = $number;
                if($price == 0){
                    $model->Price = Item::findOne($father_id)->Def;
                }else $model->Price = $price;
                $model->Oprtor = Yii::$app->user->IDentity->id;
                if($model->save()){
                    $pa = Item::findOne($father_id)->Father_ID;
                    $dataProvider->query->where(['Father_ID' => $pa]);
                    if($pa != '')
                        return $this->renderPartial('index', [
                            'dataProvider' => $dataProvider,
                        ]);
                    elseif (Yii::$app->getRequest()->isAjax) 
                    return $this->renderPartial('index1', [
                        'dataProvider' => $dataProvider,
                    ]);
                    return $this->render('index1', [
                        'dataProvider' => $dataProvider,
                    ]);
                }
            }
            /*$searchModel = new BangpaiSearch();
            $BID = Relate::get_BID();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams,['ID' => $BID]);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);*/
        }
        else return $this->redirect(['login']);
        //$this->redirect(['create']);
        
    }
    
    public function actionHistory()
    {
        if(!isset($_SESSION))session_start();
        $_SESSION['Title'] = '<&nbsp;&nbsp;&nbsp;查看历史';
        $searchModel = new ResultSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->select(['Prices' => 'sum(Price)', 'Profits' => 'sum(Profit)', 'Time'])->groupBy('Time')->orderBy('Time desc');
        return $this->render('history', [
            'dataProvider' => $dataProvider,
        ]);
        
    }
    
    public function actionRecent()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Record::find()->joinWith('item')->orderBy('Time desc'),
            'pagination' => [
                'pageSize' => 6,
            ],
        ]);
        return $this->renderPartial('recent', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionDelete($id)
    {
        Record::findOne($id)->delete();
        return $this->actionRecent();
    }
    
    public function Calc($del = true)
    {
        $str_time = date('Y-m-d',time());
        $today = strtotime($str_time);
        $pass_time = Record::find()->where(['<','Time',$today])->select('Time')->indexBy('Time')->all();
        if(!empty($pass_time)){
            $timestamps = [];
            foreach($pass_time as $time_tmp => $val){
                $str_time = date('Y-m-d',$time_tmp);
                $day = strtotime($str_time);
                $timestamps[$day] = '';
            }
            $timestamps = array_keys($timestamps);
            foreach($timestamps as $timestamp){
                Record::calc($timestamp,true);
            }
        }
    }
    
    public function actionAll()
    {
        if(!isset($_SESSION))session_start();
        $_SESSION['Title'] = '<&nbsp;&nbsp;&nbsp;商品列表';
        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy('Father_ID');
        return $this->render('items', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionProfit()
    {
        $str_time = date('Y-m-d',time());
        $today = strtotime($str_time);
        Record::calc($today,false);
        $Profit = Result::find()->where(['Time' => $today])->sum('Profit');
        Result::deleteAll(['Time' => $today]);
        return round($Profit,2);
    }
    
    public function actionDetail($id)
    {
        $id = strtotime($id);
        $searchModel = new ResultSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->joinWith('item')->where(['Time' => $id]);
        return $this->renderPartial('detail', [
            'date' => $id,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionPrinciple()
    {
        return $this->render('principle');
    }
    
    public function actionSet($id)
    {
        /*$BID = Relate::get_BID();
        if(in_array($id,$BID))
        {*/
            $db = "wwshop.s".$id."_";
            Yii::setdb($db);
            //$accessRoute = Relate::get_Route(Yii::$app->user->IDentity->id,$id);
            //\app\models\User::setaccessRoute($accessRoute);
            $_SESSION['Title'] = Userinfo::findOne($id)->username;
        //}
        return $this->redirect(['index']);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Item();
        $model->Cost = 0;
        $model->Def = 0;
        if(!isset($_SESSION))session_start();
        $model->Father_ID = $_SESSION['father'];
        $father = Item::findOne($_SESSION['father']);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['create']);
        } else {
            return $this->render('create', [
                'father' => $father,
                'model' => $model,
            ]);
        }
    }
    
    
    public function CreateTable($lastID)
    {
        $sql = "";
        $head = "CREATE TABLE IF NOT EXISTS wwshop.s".$lastID."_";
        $foot = "ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $body = "item (
            Son_ID int not null auto_increment,
            primary key(Son_ID),
            Name varchar(20),
            Father_ID int,
            Cost double not null default 0,
            Def double not null default 0
        )";
        $sql .= $head.$body.$foot;
        $body = "record (
            ID int not null auto_increment,
            primary key(ID),
            LID int,
            Time int,
            Oprtor int,
            Number double not null default 1,
            Price double not null default 0
        )";
        $sql .= $head.$body.$foot;
        $body = "result (
            ID int not null auto_increment,
            primary key(ID),
            LID int,
            Time int,
            Number double,
            Price double not null default 0,
            Profit double not null default 0
        )";
        $sql .= $head.$body.$foot;
        $result = Yii::$app->db->createCommand($sql)->execute();
    }
    
    private function first_visit()
    {
        $model = new Item();
        $model->Name = '水';
        $model->Cost = '3';
        $model->Def = '4';
        $model->save();
        
        $model = new Item();
        $model->Name = '烟';
        $model->save();
        
        $model = new Item();
        $model->Name = '茶';
        $model->save();
        
        $model = new Item();
        $model->Name = '酒';
        $model->save();
        
        $model = new Item();
        $model->Name = '双喜(软经典)';
        $model->Father_ID = 2;
        $model->Cost = '8.586';
        $model->Def = '11';
        $model->save();
        
        $model = new Item();
        $model->Name = '大红袍';
        $model->Father_ID = 3;
        $model->save();
        
        $model = new Item();
        $model->Name = '长城干红';
        $model->Father_ID = 4;
        $model->save();
    }
    
    private function merge($arr)
    {
        $return = '';
        if(is_array($arr)){
            foreach($arr as $brr){
                $return .= $this->merge($brr);
            }
        }else $return .= 
'
'.$arr;
        return $return;
    }
    
    public function actionIntro($id)
    {
        preg_match_all("/(?<=\?r=)(\w+)(\%2F|\/)(\w+)((&id=(\d))|(\/(\d+)))*/",$id,$matches);
        if(empty($matches[1][0])||empty($matches[3][0]))return;
        $i = $matches[1][0]."/".$matches[3][0];
        $return = '';
        if(isset(self::$introduce[$i])){
            if(!empty($matches[6][0])){
                if(isset(self::$introduce[$i][$matches[6][0]])){
                    if(!empty($matches[8][0])){
                        if(isset(self::$introduce[$i][$matches[6][0]][$matches[8][0]]))
                            $return = $this->merge(self::$introduce[$i][$matches[6][0]][$matches[8][0]]);
                    }else $return = $this->merge(self::$introduce[$i][$matches[6][0]]);
                }else $return = $this->merge(self::$introduce[$i]);
            }else $return = $this->merge(self::$introduce[$i]);
        }
        if(strlen($return)>1)
        return substr($return,1,strlen($return)-1);
        else return;
    }
    
     /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Item::findOne($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['all']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['set','id'=>Yii::$app->user->IDentity->id]);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['set','id'=>Yii::$app->user->IDentity->id]);
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::setdb("");
        return $this->goHome();
    }

    /**
     * Creates a new Userinfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRegister()
    {
        $model = new Userinfo();

        $model->Time = time();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $last_id = $model->id;
            $this->CreateTable($last_id);
            $login = new LoginForm();
            $login->username = $model->username;
            $login->password = $model->password;
            if($login->login())
            {
                $this->redirect(['set','id'=>$last_id]);
            }
        } else {
            return $this->render('reg', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionManage($id=0)
    {
        if(Yii::getdb()==null)throw new ForbiddenHttpException('请先选择要操作的帮派！');
        preg_match("((\d)*+(?=_))",Yii::getdb(),$match);
        $BID = $match[0];
        
        switch($id){
            case 1:{
                $model = Bangpai::find()->where(['`bangpai`.`ID`' => $BID])->one();
                
                if ($model->load(Yii::$app->request->post()) && $model->save()){
                    $base['title'] = '管理设置';
                    $base['Head'] = '帮派信息编辑';
                    $base['introduce'] = '保存成功！';
                    return $this->render('manage',[
                        'base' => $base,
                    ]);
                }
                else{
                    $daqv = ArrayHelper::getColumn(Daqv::find()->indexBy('ID')->All(),'Daqv');
                    $fuwuqi = ArrayHelper::map(ArrayHelper::getColumn(Fuwuqi::find()->All(),['ID','DID','Fuwuqi']),'ID','Fuwuqi','DID');
                    foreach($fuwuqi as $key => $fenqv)
                    {
                        $data[$key] = "";
                        foreach($fenqv as $keypart => $val){
                            $keypart == $model->Fuwuqi?$selected="selected":$selected=null;
                            $data[$key] .= "<option value='".$keypart."' ".$selected.">".$val."</option>";
                        }
                        
                    }
                    $base['title'] = '管理设置';
                    $base['Head'] = '帮派信息编辑';
                    $base['introduce'] = '修改帮派信息时，请遵守<a href="/index.php?r=site/principle">天刀帮管使用协议</a>';
                    return $this->render('manage',[
                        'base' => $base,
                        'model' => $model,
                        'daqv' => $daqv,
                        'fuwuqi' => $data,
                    ]);
                }
            }
            case 2:{
                $base['title'] = '管理设置';
                $base['Head'] = '管理人员设置';
                $base['introduce'] = '设置人员权限，需注意帮主级别权限只能有一人，设置他人为帮主级别权限意味着转让帮主，自己降级为管理员，之后无法继续访问此页面';
                $route_id = Relate::find()->joinWith('userinfo')->where(['BID' => $BID])->all();
                foreach($route_id as $row){
                    $persons[$row->UID][$row->Function] = 1;
                    $persons[$row->UID]['Name'] = $row->userinfo->username;
                }
                return $this->render('manage',[
                    'base' => $base,
                    'level' => self::$level,
                    'persons' => isset($persons)?$persons:null,
                ]);
            }   
            default:{
                
                $base['title'] = '管理设置';
                $base['Head'] = Yii::$app->user->IDentity->username.'，你好！';
                $base['introduce'] = '请在左侧选择你想要进行的操作';
                return $this->render('manage',[
                    'base' => $base,
                ]);
            }
        }
    }
    
    public function actionSetmanage($id,$inner='',$con)
    {
        $result = Userinfo::find()->where(['username'=>$id])->one();
        if(!empty($result)){
            if(Yii::getdb()==null)throw new ForbiddenHttpException('请先选择要操作的帮派！');
            preg_match("((\d)*+(?=_))",Yii::getdb(),$match);
            $BID = $match[0];
            $inner = str_replace($id,'',$inner);
            if(!empty($inner)){
                preg_match_all("/(Yes)|(No)/",$inner,$match);
                $route = $match[0];
                $level = array_keys(self::$level);
                Relate::deleteAll(['UID'=>$result->id,'BID'=>$BID]);
                if(!empty($route))
                foreach($route as $key => $val){
                    $model = new Relate();
                    if($val == 'Yes'){
                        if($level[$key]=='owner'&&$id!=Yii::$app->user->IDentity->username){
                            if($con==''){
                                $return = 'setUser(i,i2,conf("确定将帮主转让给 '.$id.' 吗？转让后你将无法访问本页面！"));add_no(i2);';
                                continue;
                            }elseif($con=='yes'){
                                Relate::deleteAll(['BID'=>$BID,'Function'=>'owner']);
                                $accessRoute = Relate::get_Route(Yii::$app->user->IDentity->id,$BID);
                                \app\models\User::setaccessRoute($accessRoute);
                                $return = 'alert("转让帮主成功，你已无权接受该页面！");window.location.href="/index.php"';
                            }elseif($con=='no')continue;
                        }
                        $model->UID = $result->id;
                        $model->BID = $BID;
                        $model->Function = $level[$key];
                        $model->save();
                    }
                    elseif($level[$key]=='owner' && $val == 'No' && $id==Yii::$app->user->IDentity->username){
                        $model->UID = $result->id;
                        $model->BID = $BID;
                        $model->Function = $level[$key];
                        $model->save();
                        $return = 'add_yes(i2);alert("帮不可一日无主！");';
                    }
                }
                if(isset($return))
                return $return;
                else return;
            }
            if(!isset($return))//添加新成员
                return 'newLine(i.parent(),"<tr data-key=\"'.$result->id.'\"><td>'.$id.'</td><td class=\"no_c\">No</td><td class=\"no_c\">No</td></tr>")';
        }else return 'alert("找不到该用户");$(i).addClass("new_place").text("{虚席以待}")';
    }
    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        $str = 'adminYesNoYes)';
        $str = str_replace("admin",'',$str);
        preg_match_all("/(Yes)|(No)/",$str,$match);
        die(var_dump($str));
        return $this->render('about');
    }
    public static $introduce = [
'userdata/valid'    =>  [
    '<strong>激活审核</strong>',
'1' =>
'一、名单来源：
    1、帮众自己从帮派公众页面输入游戏ID，申请激活；
    2、管理员在 批量录入 界面录入奖励，其中未被激活的游戏ID；',
'2' =>  
'二、操作：
    1、本帮帮众，同意激活。点击 注册( 操作 列第一个按钮)，如果该游戏ID此前获得过奖励并且还未发放，会有选择框出现询问是否保留之前未发放，点击确定则保留，取消则仅激活游戏ID，不保留奖励；
    2、非帮众，拒绝激活。点击 删除( 操作 列第二个按钮)，则将该游戏ID列入 已注销 行列；',
'3' =>
'三、游戏ID状态解释：
    1、未激活和存在于奖励表中。如果该游戏ID不曾出现在本系统中，或者存在于奖励表中但未注册，那么该游戏ID在本系统的数据表格中显示时，底色为淡黄色；
    2、已注册。如果该游戏ID在 激活审核 中通过，或者在 查看结果 中完成一次奖励发放(因为能成功发放，所以认定为本帮帮众)，则视为已注册游戏ID，在本系统数据表格中底色为淡蓝色；
    3、已注销。如果该游戏ID在 激活审核 中被删除，或者在 查看结果 时被 注销(无法发放奖励，认定为非帮众)，则视为已注销游戏ID，在数据表格中不会出现。',
],//userdata/valid

'encouragelevel/gift'   =>  [
    '<strong>查看奖品</strong>',
'1' =>
'一、定义。奖品就是指存在的东西，比如天涯明月刀游戏中的金质帮派箱子，帮派资源箱子等；要发放某种奖励之前，应先在本页面进行设置，在 设置奖项 和 批量录入 页面中选择的奖品均在此处设置，如之后没有改动，则只要第一次设置，之后不再需要设置；',
'2' =>
'二、每周上限数量。指游戏中系统限制的每个星期可以获得的该类奖励的数量，如金质帮派箱子上限是6个，帮派资源箱子是8个，流通金无限制可不填。设置该项的作用是当某个游戏ID一周内获得的奖励超过上限时，在 查看结果 页面完成发放后会把超额部分保留至下周，未设置上限的道具则没有此功能；',
],//encouragelevel/gift

'encouragelevel/level'  =>  [
    '<strong>查看奖项</strong>',
'1' =>
'该功能暂无用处，可不做理会。',
],//encouragelevel/level

'encourage/inload'  =>  [
    '<strong>批量录入</strong>',
'1' =>
'一、功能介绍。本功能为天刀帮管核心功能和初始创作目的，为天涯明月刀量身定做的数据录入功能。能够实现大量数据一键录入和多人协助管理，摆脱单机excel，记录出错或遗漏的烦恼；',
'2' =>
'二、使用方法。把excel表格数据，天刀掠夺结果名单或者类似排版方式的名单，甚至只是一行一个游戏ID的名单粘贴到 数据 框内，在下面填写数据中游戏ID所在列(比如掠夺结果名单中，游戏ID在第二列，即有n个空格就有n+1列)，要发放的 奖品 或 进行的操作，以及发放奖品的数量，日志记录(如 “9.26掠夺” 或 “帮众名单”)，点击提交，即可完成录入，并会自动跳转到结果页面；',
'3' =>
'三、模糊录入。即勾选 (不完整)游戏ID所在列 后将进行类似游戏ID的匹配，匹配范围是 <a onclick=help_looking("?r=userdata/valid&id=3")>已注册 或 已存在</a> 于奖励表中的游戏ID，如用户名单中有“天刀帮管”输入：“天刀”，就会返回“天刀帮管 是否等于 天刀”，如果匹配结果准确，去掉(不完整)勾选，选择第一列再一次提交数据就可以完成“天刀帮管”的奖励录入，而不在匹配范围的游戏ID将被忽略，不添加奖励；',
'4' =>
'四、事件日志。每次录入数据都需要进行记录，事件名称建议按日期或活动类型命名(如：9.26掠夺，探秘海河州)；点击 事件日志 中的记录名可以查看记录的数据和录入的结果；',
'5' =>
'五、录入规则。根据 <a onclick=help_looking("?r=userdata/valid&id=3")>游戏ID状态</a>，已注销 游戏ID不会被录入。',
],//encourage/inload

'encourage/index'   =>  [
    '<strong>查看结果</strong>',
'1' =>
'一、发放奖品选择。在列表上方有所有待发奖品的选择显示按钮，选择其中某一种奖品可以查看所有获得该奖品的游戏ID及其数量；',
'2' =>
'二、未发XX列。应发放给该玩家的XX奖品数量；',
'3' =>
'三、建议发XX列。建议发放XX列是建议帮主在发放建立时发放给该玩家的数量，结合了该玩家应获得奖品数量，以及本周已经获得的该奖品数量和该奖品 <a onclick=help_looking("?r=encouragelevel/gift&id=2")>每周获取上限数量</a>。',
'4' =>
'四、查看。点击操作列 查看 按钮能查看该游戏ID所有待发和本周已发放的奖品数量；',
'5' =>
'五、发放和删除。在游戏中完成奖励发放之后，点击 发放 按钮，系统会自动在 未发XX列 减去 建议发XX列 的数量，如果能完全发放(=0)，将会消去该列，如果有超出 每周上限数量，将自动以淡红底色保存，每周日晚十二点过后可以继续发放；如果该游戏ID非本帮帮众，点击 删除 将把该游戏ID标记为 已注销；',
'6' =>
'六、行底色。参考 <a onclick=help_looking("?r=userdata/valid&id=3")>游戏ID状态</a>。',
],//encourage/index

'encourage/record'  =>  [
    '<strong>事件记录</strong>',
'1' =>
'显示所有该次事件录入所输入的数据，以及执行的结果。帮主有权如果删除该次录入记录。如果删除记录，则会同时删除该事件涉及的奖励;',
],//encourage/record

'userdata/index'    =>  [
    '<strong>查看历史</strong>',
'1' =>
'显示所有 已注册 的游戏ID，点击删除按钮能把该用户注销。'
],//userdata/index

'site/manage'     => [
    '2' =>  [
        '<strong>管理人员设置</strong>',
        '1' =>  '一、添加新管理员：单击 {虚席以待} ，在出现的文本框中输入用户名，如果用户名存在，则会生成空白权限行；',
        '2' =>  '二、修改权限：单击对应权限列名下某一行的Yes/No，将添加或取消该用户拥有的权限，帮主权限仅限一人拥有，默认是创始人；',
        '3' =>  '三、本界面只限拥有本帮帮主权限的用户访问，转让帮主权限之后将无法再进行管理设置操作。',
    ],//2
],//site/manage   
];//终行
}
