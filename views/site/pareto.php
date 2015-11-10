<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Cálculo de Pareto y Clasificación ABC';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><center>Cálculo de Pareto y Clasificación ABC de los medicamentos teniendo en cuenta el costo en Inventario Años 2012-2013-2014</center></h1>
<br>
<br>

<table class="table table-bordered">
    <tr>
        <th><center>NOMBRE DEL MEDICAMENTO</center></th>
        <th><center>VENTAS (uni)</center></th>
        <th><center>COSTO UNITARIO CON FACTOR ($)</center></th>
        <th><center>COSTO TOTAL DE INV ($)</center></th>
        <th><center>PORCENTAJE VALOR TOTAL (%)</center></th>
        <th><center>CLASIFICACIÓN (ABC)</center></th>
    </tr>
    <?php $suma=0?>
    <?php for($x=0; $x<$a; $x++): ?>
    <tr>
        <td><center><?= $arreglo["NomProducto"][$x]?></center></td>
        <td><center><?= $arreglo["ventas"][$x]?></center></td>
        <td><center><?= $arreglo["costoU"][$x]?></center></td>
        <td><center><?= $arreglo["CostoTotal"][$x]?></center></td>
        <td><center><?= $arreglo["PorcentajeValorTotal"][$x]?></center></td>
        <?php 
            $suma=$suma+(double)$arreglo["PorcentajeValorTotal"][$x];
        ?>
        <td><center><?php if($suma>=0 && $suma<80){echo "A";}elseif($suma>=80 && $suma<=95){echo"B";}else{echo"C";}?></center></td>
    </tr>
    <?php endfor ?>

</table>
