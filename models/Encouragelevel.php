<?php

namespace app\models;

use Yii;
use app\models\Gift;
/**
 * This is the model class for table "encouragelevel".
 *
 * @property integer $ID
 * @property string $Markname
 * @property string $Realname
 * @property integer $Number
 */
class Encouragelevel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::getdb().'encouragelevel';
    }

    public function getGift()
    {
        return $this->hasOne(Gift::className(), ['Realname' => 'Realname']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Number'], 'integer'],
            [['Markname', 'Realname'], 'string', 'max' => 20],
            [['Markname', 'Realname', 'Number'], 'unique', 'targetAttribute' => ['Markname', 'Realname', 'Number'], 'message' => '该奖项已存在'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'Markname' => '奖项',
            'Realname' => '奖品',
            'Number' => '数量',
        ];
    }
}
