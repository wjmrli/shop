<?php

namespace app\models;

use Yii;
use app\models\Item;
/**
 * This is the model class for table "wwshop.result".
 *
 * @property integer $ID
 * @property integer $LID
 * @property integer $Time
 * @property double $Number
 * @property double $Price
 * @property double $Profit
 */
class Result extends \yii\db\ActiveRecord
{
    public $Prices;
    public $Profits;
    public function getItem()
    {
        return $this->hasOne(Item::className(),['Son_ID' => 'LID']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::getdb().'result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['LID', 'Time'], 'integer'],
            [['Number', 'Price', 'Profit'], 'number'],
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
            'Time' => Yii::t('app', '日期'),
            'Number' => Yii::t('app', '销量'),
            'Price' => Yii::t('app', '销售额'),
            'Profit' => Yii::t('app', '利润'),
        ];
    }
}
