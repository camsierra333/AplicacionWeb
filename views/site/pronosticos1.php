<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\Pagination;
use yii\widgets\LinkPager;
$total_alumnos = count($model->student);
?>

<h1><center>Listado de Estudiantes</center></h1>
<br>
<br>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>NOMBRE</th>
        <th>EDAD</th>
    </tr>
    <?php for($x=0; $x<$total_alumnos;$x++): ?>
    <tr>
        <th><?= $model->student[$x]->id ?></th>
        <th><?= $model->student[$x]->name?></th>
        <th><?= $model->student[$x]->edad?></th>  
    </tr>
    <?php endfor ?>
</table>
<?=LinkPager::widget([
   "pagination" =>$pages 
]);
