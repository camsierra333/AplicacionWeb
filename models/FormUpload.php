<?php
 
namespace app\models;
use yii\base\model;
 
class FormUpload extends model{
  
    public $file;
     
    public function rules()
    {
        return [
            ['file', 'file', 
            'skipOnEmpty' => false,
            'uploadRequired' => 'No has seleccionado ningún archivo',
            'maxSize' => 1024*1024*5, 
            'tooBig' => 'El tamaño máximo permitido es 20MB', 
            'minSize' => 10, //10 Bytes
            'tooSmall' => 'El tamaño mínimo permitido son 10 BYTES', 
            'extensions' => 'xml,xls,xlsx',
            'wrongExtension' => 'El archivo {file} no contiene una extensión permitida {extensions}', //Error
            'maxFiles' => 2,
            'tooMany' => 'El máximo de archivos permitidos son {limit}', //Error
            ],
        ]; 
    } 
 
 public function attributeLabels()
 {
  return [
   'file' => 'Seleccionar archivos (para reglas reglas: xls , para forecasting: ) :',
  ];
 }
}