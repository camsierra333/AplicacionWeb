<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Índice costo Almacenamiento unidad';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><center>Índice de costo de almacenamiento por unidad, por mes y año desde el 2012 a la fecha</center></h1>
<br>
<br>

<table class="table table-bordered">
    <tr>
        <th><center>AÑO</center></th>
        <th><center>MES</center></th>
        <th><center>DINERO PROMEDIO EN INV</center></th>
        <th><center>PROMEDIO UNIDADES ALMACENADAS</center></th>
        <th><center>COSTO DE ALMACENAMIENTO POR UNIDAD</center></th>
    </tr>
    <?php for($x=0; $x<$a; $x++): ?>
    <tr>
        <td><center><?= $arreglo["Annio"][$x]?></center></td>
        <td><center><?= $arreglo["Mes"][$x]?></center></td>
        <td><center><?= $arreglo["promInvDinero"][$x]?></center></td>
        <td><center><?= $arreglo["UniAlmacenadasProm"][$x]?></center></td>
        <td><center><?= $arreglo["CostoAlmaUni"][$x]?></center></td>
    </tr>
    <?php endfor ?>

</table>