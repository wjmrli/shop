<?php

namespace app\models;

use Yii;
use app\models\Record;
/**
 * This is the model class for table "wwshop.item".
 *
 * @property integer $Son_ID
 * @property string $Name
 * @property integer $Father_ID
 * @property integer $Number
 * @property double $Cost
 * @property double $Price
 */
class Item extends \yii\db\ActiveRecord
{
    public $Father_Name;
    
    public function getR()
    {
        return $this->hasMany(Record::className(),['LID' => 'Son_ID']);
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::getdb().'item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Father_ID'], 'integer'],
            [['Cost', 'Def'], 'number'],
            [['Name'], 'string', 'max' => 20],
            [['Name'], 'unique', 'message' => '{value} 已存在'],
            [['Name'], 'required','message' => '品名不能为空'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Son_ID' => 'Son_ID',
            'Name' => '品名',
            'Father_ID' => '分类目录',
            'Cost' => '进货价',
            'Def' => '默认售价',
        ];
    }

    /**
     * @inheritdoc
     * @return ItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ItemQuery(get_called_class());
    }
}
