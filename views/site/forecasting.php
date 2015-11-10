<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Pronóstico generado para medicamentos Clase A';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><center>Pronóstico Medicamentos para cada uno de los meses del año 2014</center></h1>
<br>
<br>

<table class="table table-bordered">
    <tr>
        <th><center>MES</center></th>
        <th><center>FECHA</center></th>
        <th><center>PRONÓSTICO</center></th>
    </tr>
    <?php $arrayPronos = array()?>
    <?php foreach($objHoja as $iIndice=>$objCelda) {?>
    <tr>
        <td><center><?= $objCelda['A']?></center></td>
        <td><center><?= $objCelda['B']?></center></td>
        <td><center><?= $objCelda['C']?></center></td>
        <?php $ultimo = $objCelda['C']?>
        <?php array_push($arrayPronos,$objCelda['C'])?>
    </tr>
    <?php } ?>

</table>
<br>
<br>
<p>Pronóstico y cálculo de Q óptimo a pedir para cada unos de los meses del año 2014</p>

<table class="table table-bordered">
    <tr>
        <th><center>MES</center></th>
        <th><center>FECHA</center></th>
        <th><center>PRONÓSTICO</center></th>
        <th><center>Q ÓPTIMO</center></th>
    </tr>
    
    <?php foreach($objHoja as $iIndice=>$objCelda) {?>
    <tr>
        <td><center><?= $objCelda['A']?></center></td>
        <td><center><?= $objCelda['B']?></center></td>
        <td><center><?= $objCelda['C']?></center></td>
        <td><center>
        
           <?php 
                $T=30;
                $L=5;
                $d = (float)$ultimo/($T+$L);
                $i=0;
                $desv=1.8963;
                //$desv=stats_standard_deviation($arrayPronos);
                $p=1.6448;
                
                $Q=0;
                $Q= ($objCelda['C']/($T+$L))*($T+$L)+$p*$desv;
                
                echo $Q;
           ?>
        
        </center></td>
    </tr>
    <?php } ?>

</table>
