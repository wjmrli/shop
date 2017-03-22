<?php

namespace app\models;

use Yii;
use app\models\Item;
use yii\helpers\ArrayHelper;
use app\models\Result;
/**
 * This is the model class for table "wwshop.record".
 *
 * @property integer $ID
 * @property integer $LID
 * @property integer $Time
 * @property integer $Oprtor
 */
class Record extends \yii\db\ActiveRecord
{
    
    public $Prices;
    public $Numbers;
    public $not_empty;
    public $not_empty_pri;
    public function detail($time = 1475823935)
    {
        $start = $time;
        $end = $time + 85399;
        $All = self::find()->where(['>','Time',$start])->andWhere(['<','Time',$end])->groupBy('LID')->select('LID, sum(Price) as Prices, sum(Number) as Numbers')->all();
        return $All;
    }
    
    public static function calc($time = 1475853935,$del = true)
    {
        $start = $time;
        $end = $time + 86400;
        $All = self::find()->joinWith('item')->where(['>=','Time',$start])->andWhere(['<','Time',$end])->groupBy('LID')->indexBy('LID')->select(['LID', 'Prices' => 'sum(Price)', 'Numbers' => 'sum(Number)', 'not_empty' => 'sum(if(Price=0,0,Number))', Item::tableName().'.*'])->all();
        //$All = Record::find()->where(['>','Time',$start])->andWhere(['<','Time',$end])->groupBy('LID')->select('LID, sum(Price) as Prices, sum(Number) as Numbers')->all();
        if(!empty($All))
        foreach($All as $key => $item){
            $model = new Result();
            $model->LID = $key;
            $model->Time = $time;
            $model->Price = $item->Prices;
            $model->Number = $item->Numbers;
            if(!empty($item->item->Cost)&&$item->not_empty!=0){
                $model->Profit = $model->Price - round($item->item->Cost*$item->not_empty,2);
            }
            if($model->save()){
                if($del)
                self::deleteAll('LID = '.$key.' and Time>='.$start.' and Time<'.$end);
            }
        }
    }
    
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['Son_ID' => 'LID']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::getdb().'record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['LID', 'Time', 'Oprtor'], 'integer'],
            [['Number', 'Price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => Yii::t('app', 'ID'),
            'LID' => Yii::t('app', 'Lid'),
            'Time' => Yii::t('app', 'Time'),
            'Oprtor' => Yii::t('app', 'Oprtor'),
            'Number' => '数量',
            'Price' => '价格',
        ];
    }
}
