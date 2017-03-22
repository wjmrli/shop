<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Userdata; 
use app\models\Record;
/**
 * This is the model class for table "encourage".
 *
 * @property integer $ID
 */
class Encourage extends \yii\db\ActiveRecord
{
    public static $gift;
    /**
     * @author 
     * @author 
     * */
    public static function operate($params = [])
    {
        $Time = time();
        $ID = isset($params['player_ID'])? $params['player_ID']:[];
        $ID_State = isset($params['ID_State'])? $params['ID_State']:[];
        $player_ID = [];
        foreach($ID as $key => $val)
        {
            $player_ID[$val] = $ID_State[$key];
        }
        if(count($player_ID)==0)return;
        $record_ID = isset($params['record_ID'])? $params['record_ID']:'';
        $reg_and_tmp = isset($params['reg_and_tmp'])? $params['reg_and_tmp']:[];
        $str = "共为 ".count($reg_and_tmp)." 个玩家发放奖励：(已按50汉字一段分行)\n";
        $insert = '';
        $count = 0;
        foreach($player_ID as $val => $exist)
        {
            if($exist != '1')//游戏ID状态为已激活
            {
                $insert .= "('$val','$record_ID','$Time'),";
            }
        }
        if(strlen($insert)==0)return;
        $sql = "INSERT INTO ".self::tableName()."(UID, EID, Time) Values ".substr($insert,0,-1);
        $effectRows = Yii::$app->db->createCommand($sql)->execute();
        if($effectRows == count($reg_and_tmp))
        {
            foreach($reg_and_tmp as $val)
            {
                if(!isset($ins_rep))$ins_rep = $val." ";
                else 
                {
                    $que = preg_split("/\n/",$ins_rep);
                    $numque = count($que);
                    if(strlen($que[$numque-1].$val)>100)
                    $ins_rep .= "\n".$val." ";
                    else
                    $ins_rep .= $val." ";
                }
            }
        }
        if(isset($ins_rep))
        return $str.substr($ins_rep,0,-1);
        else return $str;
    }
    
    /**
     * @param if isset Timestamp['from'=> {type:time},'to'=>{type:time}],return an array limit the Time in this circle,unset Timestamp,return all result
     * @param if isset Player{type:string or type:array},return an array about these Player,else,return all
     * */
    public static function read($params = [])
    {
        $Timestamp = isset($params['Timestamp'])? $params['Timestamp']:['from'=> 0, 'to'=>time()];
        if(isset($params['Player'])&&!empty($params['Player']))
        {
            $sql = '';
            if(is_string($params['Player']))
                $sql = "Player='".$params['Player']."'~~~";
            else if(is_array($params['Player']))
                foreach($params['Player'] as $val)
                $sql .= "Player='".$val."' or ";
        }
        else
        {
            if(isset($params['UID']))$UID = $params['UID'];
            else{
                $sql = "select UID from ".self::tableName()." group by UID"; 
                $UID = ArrayHelper::getColumn(self::findBySql($sql)->All(),'UID');//获取encourage表中所有涉及到的用户ID
            }
            $sql = '';
            foreach($UID as $val)
                $sql .= "ID=".$val." or ";//获取查询语句
        }
        if($sql != '')$sql = "(".substr($sql,0,-3).") and ";
        $sql = "select ID, Here from ".Yii::getdb()."userdata where ".$sql."(Here=0 or Here=2)";
        $ID_State = ArrayHelper::getColumn(Userdata::findBySql($sql)->indexBy('ID')->All(),'Here');//获取所有0和2用户状态 ID=>Here
        if(empty($ID_State))return "";
        $ID = array_keys($ID_State);
        $sql = '';
        foreach($ID as $val)
            $sql.= "UID=".$val." or ";
        if($sql !='')$sql = " and (".substr($sql,0,-3).")";
        $sql = "select ID, UID, EID from ".self::tableName()." where (Time>".$Timestamp['from']." and Time<".$Timestamp['to'].")".$sql;
        $encouragequery = self::findBySql($sql)->All();//查询所有事件记录
        $EID_ALL = array_keys(ArrayHelper::index(ArrayHelper::getColumn($encouragequery,['ID','EID']),'EID'));//查询所有Event_ID
        $sqlf = '';
        foreach($EID_ALL as $val)
            $sqlf.= "ID=".$val." or ";
        if($sqlf != '')$sqlf = "(".substr($sqlf,0,-3).") and ";
        $sql = "select ID, Record, Gift, Number, Origin, Time from ".Yii::getdb()."record where ".$sqlf."(Origin='inload' or Origin='picture' or Origin='done' or Origin='rest')";
        $event = ArrayHelper::getColumn(Record::findBySql($sql)->indexBy('ID')->All(),['Record','Gift','Number','Origin','Time']);//获取所有encourage表中用户涉及到的事件详情 ID=>[ID,Record,Gift,Number,Origin]
        $sql = "select Gift, Origin from ".Yii::getdb()."record where ".$sqlf."(Origin='inload' or Origin='picture' or Origin='rest')";//不收集done，因为没有需要发放的奖励，不需做比较
        self::$gift = array_keys(Record::findBySql($sql)->indexBy('Gift')->All());
        $EID =  ArrayHelper::map(ArrayHelper::getColumn($encouragequery,['ID','UID','EID']),'ID','EID','UID');//UID=>[ID=>EID]
        $sql = '';
        $sdefaultDate = date("Y-m-d");
        $weekday = date('w');
        $Monday = strtotime($sdefaultDate) - $weekday * 24 * 3600;
        $limit = ArrayHelper::getColumn(Gift::find()->indexBy('Realname')->all(),'Number');
        //die(var_dump($EID));
        foreach($EID as $key => $ID)//UID=>[ID=>EID]
        {
            foreach($ID as $keypart => $val)
            {
                $EID[$key][$keypart] = $event[$val]['Gift'];
                //die(var_dump($EID[$key][$keypart]));
            }
            $EID[$key]['Record'] = '';
            $EID[$key]['Gifts'] = [];
            $EID[$key]['Done'] = [];
            foreach($ID as $keypart => $val)
            {
                if($event[$val]['Origin']=='done'&&$event[$val]['Time']>=$Monday)
                {
                    if(isset($EID[$key]['Done'][$event[$val]['Gift']]))$EID[$key]['Done'][$event[$val]['Gift']] += $event[$val]['Number'];
                    else $EID[$key]['Done'][$event[$val]['Gift']] = $event[$val]['Number'];
                }
                else
                {
                    $EID[$key]['Record'] .= $event[$val]['Record']."/";
                    if(isset($EID[$key]['Gifts'][$event[$val]['Gift']]))$EID[$key]['Gifts'][$event[$val]['Gift']] += $event[$val]['Number'];
                    else $EID[$key]['Gifts'][$event[$val]['Gift']] = $event[$val]['Number'];
                }
            }
            $EID[$key]['Record'] = substr($EID[$key]['Record'],0,-1);
            if(!isset($EID[$key]['Here']))$EID[$key]['Here'] = $ID_State[$key];
        }
        return $EID;
    }
    
    public static function deal_blur($extra_name = [],$inner_name = [])
    {
        $sql = "select Player,ID from ".Yii::getdb()."userdata where Here=0 or Here=2 order by Time desc";
        $userdata = Userdata::findBySql($sql)->indexBy('ID')->All();
        $Player = ArrayHelper::getColumn($userdata,'Player');
        $return = '';
        foreach($inner_name as $val)
        {
            unset($Player[array_search($val,$Player)]);
        }
        foreach($extra_name as $val)
        {
            $mark = '0';
            foreach($Player as $cname)
            {
                if(strpos('~~'.$cname,$val)!=false)
                {
                    $return .= $cname." ";
                    $mark = '1';
                }
            }
            if($mark == '1')$return .= "是否等同于 ".$val."\n";
            else $return = $val." 匹配失败\n";
        }
        return $return;
    }
    /**
     * @author 转换encourage column为数组
     * */
    public static function ECtoArray($param)
    {
        $return = [];
        if(empty($param))return;
        $items = explode(";",$param);
        foreach($items as $val)
        {
            $tmp = preg_split(":", $val);
            $return = array_merge($return, [$tmp[0] => $tmp[1]]);
        }
        return $return;
    }
    
    /**
     * @author 转换数组为 encourage column
     * */
    public static function ArraytoEC($param = [])
    {
        $return = '';
        foreach($param as $key => $val)
        {
            $return .= $key.":".$val.";";
        }
        return substr($return,0,-1);
    }
    
    public function getUserdata()
    {
        return $this->hasOne(Userdata::className(), ['Realname' => 'Realname']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::getdb().'encourage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UID', 'Time', 'EID'], 'integer'],
            [['UID', 'EID'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'UID' => '玩家ID',
            'EID' => '事件ID',
            'Time' => '最后活跃时间',
        ];
    }
}
