<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Items asociados y frecuencia de repetición';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><center>Listado de medicamentos asociados según orden de compra y su respectiva frecuencia de repetición de la asociación</center></h1>
<br>
<br>

<table class="table table-bordered">
    <tr>
        <th><center>ITEMS ASOCIADOS</center></th>
        <th><center>CANTIDAD ASOCIADOS</center></th>
        <th><center>FRECUENCIA DE REPETICION</center></th>
    </tr>
    
    <?php foreach($objHoja as $iIndice=>$objCelda) {?>
    <tr>
        <td><center><?= $objCelda['A']?></center></td>
        <td><center><?= $objCelda['B']?></center></td>
        <td><center><?= $objCelda['C']?></center></td>
    </tr>
    <?php } ?>

</table>
