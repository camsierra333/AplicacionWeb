<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Rotación invetario de medicamentos';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><center>Indicador rotación de inventario de medicamentos por mes y año desde el 2012 a la fecha</center></h1>
<br>
<br>

<table class="table table-bordered">
    <tr>
        <th><center>AÑO</center></th>
        <th><center>MES</center></th>
        <th><center>COSTO TOTAL VENDIDO</center></th>
        <th><center>PROMEDIO MEDICAMENTOS EN INV</center></th>
        <th><center>INDICE DE ROTACION DE MEDICAMENTOS</center></th>
    </tr>

    <?php for($x=0; $x<$a; $x++): ?>
    <tr>
        <td><center><?= $arreglo["Annio"][$x]?></center></td>
        <td><center><?= $arreglo["Mes"][$x]?></center></td>
        <td><center><?= $arreglo["CostoTotalVendido"][$x]?></center></td>
        <td><center><?= $arreglo["promMedInv"][$x]?></center></td>
        <td><center><?= $arreglo["IndiceRotacionMedicamentos"][$x]?></center></td>
    </tr>
    <?php endfor ?>

</table>