<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Lista de usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><center>Listado de Usuarios en el Sistema</center></h1>
<br>
<br>

<?php $f = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("site/listarusuarios"),
    "enableClientValidation" => true,
]);
?>

<div class="form-group">
    <?= $f->field($form, "q")->input("search") ?>
</div>

<?= Html::submitButton("Buscar", ["class" => "btn btn-primary"]) ?>

<?php $f->end() ?>

<br>
<p>Patron de búsqueda: <?= $search ?></p>
<br>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>USERNAME</th>
        <th>E-MAIL</th>
        <th>ROL</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach($model as $row): ?>
    <tr>
        <td><?= $row->id?></td>
        <td><?= $row->username?></td>
        <td><?= $row->email?></td>
        <td><?= $row->role?></td>
        <td><a href="<?= Url::toRoute(["site/update", "id" => $row->id]) ?>">Editar</a></td>
        <td>
            <a href="#" data-toggle="modal" data-target="#id_<?= $row->id?>">Eliminar</a>
            <div class="modal fade" role="dialog" aria-hidden="true" id="id_<?= $row->id ?>">
                      <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title">Eliminar usuario</h4>
                              </div>
                              <div class="modal-body">
                                    <p>¿Realmente deseas eliminar al usuario con id <?= $row->id ?>?</p>
                              </div>
                              <div class="modal-footer">
                              <?= Html::beginForm(Url::toRoute("site/delete"), "POST") ?>
                                    <input type="hidden" name="id" value="<?= $row->id ?>">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Eliminar</button>
                              <?= Html::endForm() ?>
                              </div>
                            </div><!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </td>    
    </tr>
    <?php endforeach ?>
</table>

<?= LinkPager::widget([
    "pagination" =>$pages
]);