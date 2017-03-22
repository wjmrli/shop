<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the valIDation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is valIDated by valIDatePassword()
            ['password', 'valIDatePassword'],
        ];
    }

    /**
     * ValIDates the password.
     * This method serves as the inline valIDation for password.
     *
     * @param string $attribute the attribute currently being valIDated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function valIDatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->valIDatePassword($this->password)) {
                $this->addError($attribute, '密码错误.');
            }
        }
    }

    /**
     * Logs in a user using the provIDed username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->valIDate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*14 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
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
