<?php

namespace app\models;

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $email;
    public $password;
    public $authKey;
    public $accessToken;
    public $activate;
    public $verification_code;
    public $role;

    public static function isUserAdmin($id)
    {
       if (Users::findOne(['id' => $id, 'activate' => '1', 'role' => 2]))
       {
            return true;
       } else {
           
            return false;
       }
    }

    public static function isUserSimple($id)
    {
       if (Users::findOne(['id' => $id, 'activate' => '1', 'role' => 1]))
       {
            return true;
       } else {

            return false;
       }
    }
    
    public static function findIdentity($id)
    {
        $user = Users::find()
                ->where("activate=:activate", [":activate" => 1])
                ->andWhere("id=:id", ["id" => $id])
                ->one();
        
        return isset($user) ? new static($user) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $users = Users::find()
                ->where("activate=:activate", [":activate" => 1])
                ->andWhere("accessToken=:accessToken", [":accessToken" => $token])
                ->all();
        
        foreach ($users as $user) {
            if ($user->accessToken === $token) {
                return new static($user);
            }
        }

        return null;
    }

    public static function findByUsername($username)
    {
        $users = Users::find()
                ->where("activate=:activate", ["activate" => 1])
                ->andWhere("username=:username", [":username" => $username])
                ->all();
        
        foreach ($users as $user) {
            if (strcasecmp($user->username, $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        if (crypt($password, $this->password) == $this->password)
        {
        return $password === $password;
        }
    }
}