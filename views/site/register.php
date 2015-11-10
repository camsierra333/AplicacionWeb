<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<h3><?= $msg ?></h3>

<?php 
$this->title = 'Registro Nuevo Usuario';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Registro de un nuevo usuario</h1>
<?php $form = ActiveForm::begin([
    'method' => 'post',
    'id' => 'formulario',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
]);
?>


<div class="form-group">
 <?= $form->field($model, "username")->input("text") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "email")->input("email") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "role")->input("role") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "password")->input("password") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "password_repeat")->input("password") ?>   
</div>

<?= Html::submitButton("Registrar", ["class" => "btn btn-primary"]) ?>

<?php $form->end() ?>