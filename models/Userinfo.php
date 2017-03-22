<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "outline.userinfo".
 *
 * @property integer $id
 * @property integer $Time
 * @property string $username
 * @property string $password
 * @property string $Email
 * @property integer $Balance
 * @property string $accessToken
 * @property string $authKey
 */
class Userinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ww.userinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Time', 'Balance'], 'integer'],
            [['username', 'password', 'Email'], 'required'],
            [['username'], 'unique', 'message' => '用户名已存在'],
            [['username', 'accessToken', 'authKey'], 'string', 'max' => 20],
            [['password'], 'string', 'max' => 16],
            [['Email'], 'string', 'max' => 60],
            ['Email','email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Time' => 'Time',
            'username' => '用户名',
            'password' => '密码',
            'Email' => 'Email',
            'Balance' => 'Balance',
            'accessToken' => 'Access Token',
            'authKey' => 'Auth Key',
        ];
    }
}
