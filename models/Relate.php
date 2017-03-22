<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "outline.relate".
 *
 * @property integer $ID
 * @property integer $UID
 * @property integer $BID
 * @property string $Function
 */
class Relate extends \yii\db\ActiveRecord
{
    /**
     * @author
     * */
    public static function findByUID()
    {
        //$sql = "select * from outline.relate where UID=".Yii::$app->user->IDentity->id;
        //$All = self::findBySql($sql)->indexBy('ID')->All();
        $All = self::find()
        ->where(['UID' => Yii::$app->user->IDentity->id,'Function'=>'owner'])
        ->orWhere(['UID' => Yii::$app->user->IDentity->id,'Function'=>'m'])
        ->all();
        //die(var_dump($All));
        return $All;
    }
    
    /**
     * @author
     * */
    public static function get_BID()
    {
        $return = ArrayHelper::getColumn(self::findByUID(),'BID');
        return $return;
    }
    
    public static function get_Route($uid,$bid)
    {
        $All = self::findAll(['UID'=>$uid,'BID'=>$bid]);
        $accessRoute = ArrayHelper::getColumn($All,'Function');
        return $accessRoute;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outline.relate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UID', 'BID'], 'integer'],
            [['Function'], 'string', 'max' => 20],
        ];
    }

    public function getUserinfo()
    {
        return $this->hasOne(Userinfo::className(), ['id' => 'UID']);
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'UID' => 'Uid',
            'BID' => 'Bid',
            'Function' => 'Function',
        ];
    }
}
