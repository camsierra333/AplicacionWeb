<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Importar Reglas de Asociación';

$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<br>
<br>
<h3>1. Importar resultados RapidMiner para Reglas de Asociación</h3>
<br>
<?= $msg ?>

<?php $form = ActiveForm::begin([
     "method" => "post",
     "enableClientValidation" => true,
     "options" => ["enctype" => "multipart/form-data"],
     ]);
?>

<?= $form->field($model, "file[]")->fileInput(['multiple' => true]) ?>

<?= Html::submitButton("Subir", ["class" => "btn btn-primary"]) ?>

<?php $form->end() ?>