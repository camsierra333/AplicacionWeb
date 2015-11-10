<?php

namespace app\models;
use Yii;
use yii\base\model;
use app\models\Users;

class FormActualizar extends model{
 
    public $id;
    public $username;
    public $email;
    public $role;

   public function rules()
    {
        return [
            
            ['id', 'integer', 'message' => 'Id incorrecto'],
            ['username', 'required', 'message' => 'Campo requerido'],
            ['username', 'match', 'pattern' => "/^.{3,50}$/", 'message' => 'Mínimo 3 y máximo 50 caracteres'],
            ['username', 'match', 'pattern' => "/^[0-9a-z]+$/i", 'message' => 'Sólo se aceptan letras y números'],
            ['email','required', 'message' => 'Campo requerido'],
            ['email', 'match', 'pattern' => "/^.{5,80}$/", 'message' => 'Mínimo 5 y máximo 80 caracteres'],
            ['email', 'email', 'message' => 'Formato no válido'],
            ['role', 'required', 'message' => 'Campo requerido'],
            ['role', 'integer', 'message' => 'role incorrecto'],
        ];
    }
      
}