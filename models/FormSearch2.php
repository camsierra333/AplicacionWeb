<?php
namespace app\models;
use Yii;
use yii\base\model;

class FormSearch2 extends model{
    public $q;
    
    public function rules()
    {
        return [
            ["q", "match", "pattern" => "/^[0-9a-záéíóúñ\s]+$/i", "message" => "Sólo se aceptan letras y números"]
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'q' => "Buscar (Medicamento o IdProducto o IdMedicamento):",
        ];
    }
}