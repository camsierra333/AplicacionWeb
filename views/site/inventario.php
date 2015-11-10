<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Indicadores de Inventario';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Teniendo en cuenta la información y consumo de los medicamentos en la farmacia, se indican preliminarmente los 
        indicadores de rotación de inventario de medicamentos e índice de costo de almacenamiento por unidad.
    </p>

    <p><strong>Menú</strong></p>
    <p><strong>1. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/rotacioninv'>Informe Indicador rotación de inventario de medicamentos</a></p>
    <p><strong>2. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/costoalmacenamiento'>Informe Indice de costo de almacenamiento por unidad</a></p>


</div>