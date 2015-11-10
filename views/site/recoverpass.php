<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
 
<h3><?= $msg ?></h3>

<?php 
$this->title = 'Recuperar contraseña';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Recuperación de Contraseña</h1>
<?php $form = ActiveForm::begin([
    'method' => 'post',
    'enableClientValidation' => true,
]);
?>
 
<div class="form-group">
 <?= $form->field($model, "email")->input("email") ?>  
</div>
 
<?= Html::submitButton("Recuperar Contraseña", ["class" => "btn btn-primary"]) ?>
 
<?php $form->end() ?>