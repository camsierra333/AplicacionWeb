<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Lista de medicamentos próximos a vencer entre 4 y 6 meses';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><center>Lista de medicamentos próximos a vencer entre 4 y 6 meses a partir del inventario </center></h1>
<br>
<br>
<p><strong>* </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/ejemplo'>Listado de todas las bodegas</a></p>

<table class="table table-bordered">
    <tr>
        <th><center>IdProducto</center></th>
        <th><center>CodMedicamento</center></th>
        <th><center>Medicamento</center></th>
        <th><center>FechaVencimimento</center></th>
        <th><center>Proveedor</center></th>
        <th><center>FecCorteInventario</center></th>
        <th><center>Meses Faltantes Vencimiento</center></th>
        <th><center>idBodega</center></th>
        <th><center>NomBodega</center></th>
        <th><center>idLote</center></th>
        <th><center>Lote</center></th>
    </tr>

    <?php for($x=0; $x<$a; $x++): ?>
    <tr>
        <td><center><?= $arreglo["idProducto"][$x]?></center></td>
        <td><center><?= $arreglo["CodProducto"][$x]?></center></td>
        <td><center><?= $arreglo["Nomproducto"][$x]?></center></td>
        <td><center><?= $arreglo["fecInvima"][$x]?></center></td>
        <td><center><?= $arreglo["NomTercero"][$x]?></center></td>
        <td><center><?= $arreglo["FecCorte"][$x]?></center></td>
        <td><center><?= $arreglo["MesesFaltantes"][$x]?></center></td>
        <td><center><?= $arreglo["idBodega"][$x]?></center></td>
        <td><center><?= $arreglo["NomBodega"][$x]?></center></td>
        <td><center><?= $arreglo["idLote"][$x]?></center></td>
        <td><center><?= $arreglo["Lote"][$x]?></center></td>
    </tr>
    <?php endfor ?>

</table>