<?php

namespace app\models;

use Yii;
use app\models\Relate;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "outline.bangpai".
 *
 * @property integer $ID
 * @property string $Name
 * @property string $Daqv
 * @property string $Fuwuqi
 * @property integer $Pay_Time
 * @property integer $Game_ID
 * @property integer $Active_Time
 * @property string $Function
 * @property integer $Balance
 */
class Bangpai extends \yii\db\ActiveRecord
{
    
    public static function findByBID()
    {
        $BID = Relate::get_BID();
        $sql = '';
        foreach($BID as $val)
        {
            $sql .= "ID=".$val." or ";
        }
        $sql = "select * from outline.bangpai where ".substr($sql,0,-4);
        $All = self::findBySql($sql)->indexBy('ID')->All();
        return $All;
    }
    
    public static function get_Bangpai()
    {
        $return = ArrayHelper::getColumn(self::findByBID(),'Name');
        return $return;
    }
    
    public function getDaqv()
    {
        return $this->hasOne(Daqv::className(), ['ID' => 'Daqv']);
    }
    
    public function getFuwuqi()
    {
        return $this->hasOne(Fuwuqi::className(), ['ID' => 'Fuwuqi']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outline.bangpai';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Daqv', 'Fuwuqi', 'Name'], 'required'],
            [['Name'], 'unique', 'message'=> '该帮派已被注册，若有异议，请联系客服'],
            [['Daqv', 'Fuwuqi'], 'number', 'min'=>1, 'tooSmall'=>'请选择{attribute}'],
            [['Daqv', 'Fuwuqi', 'Pay_Time', 'Game_ID', 'Active_Time', 'Balance'], 'integer'],
            [['Name'], 'string', 'max' => 14],
            [['Function'], 'string', 'max' => 20],
            [['Picture'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'Name' => '帮派名称',
            'Daqv' => '大区',
            'Fuwuqi' => '服务器',
            'Pay_Time' => 'Pay  Time',
            'Game_ID' => '帮派ID',
            'Active_Time' => 'Active  Time',
            'Function' => 'Function',
            'Balance' => 'Balance',
            'Picture' => 'Picture',
        ];
    }
}
