<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property integer $ID
 * @property integer $RID
 * @property string $Source
 * @property string $Result
 * @property integer $Blur
 * @property integer $Col
 *
 * @property Record $r
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::getdb().'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['RID', 'Blur', 'Col'], 'required'],
            [['RID', 'Blur', 'Col'], 'integer'],
            [['Source', 'Result'], 'string'],
            [['Col'], 'compare', 'compareValue' => 0, 'operator' => '>'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'RID' => Yii::t('app', 'Rid'),
            'Source' => Yii::t('app', '数据'),
            'Result' => Yii::t('app', '结果'),
            'Blur' => Yii::t('app', '(不完整)游戏ID所在列'),
            'Col' => Yii::t('app', '列'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getR()
    {
        return $this->hasOne(Record::className(), ['ID' => 'RID']);
    }

}
