<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Medicamentos en inventario próximos a vencer';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        De acuerdo a las fechas y a los cortes de inventario que se hacen en bodega, el sistema extrae el medicamento registrado y muestra
        la fecha de vencimiento del producto asociado al corte de inventario.
    </p>
    <p><strong>Menú</strong></p>
    <p><strong>1. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/vencimiento3meses'>Listar Medicamentos proximos a vencer en 3 meses apartir de la fecha de inventario</a></p>
    <p><strong>2. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/vencimiento6meses'>Listar Medicamentos proximos a vencer en 6 meses apartir de la fecha de inventario</a></p>
    <!--<p><strong>3. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/listarusuarios'>Descargar informe proximos a vencer en 3 meses</a></p>
    <p><strong>4. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/listarusuarios'>Descargar informe proximos a vencer en 6 meses</a></p>-->
</div>