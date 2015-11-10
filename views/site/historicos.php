<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Análisis de Historicos';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Por medio de este módulo se pueden generar informes para pronósticos y conocer medicamentos asociados cuando se realizar compras.
    </p>
    <p><strong>Menú</strong></p>
    <p><strong>1. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/pareto'>Informe Pareto y ABC de medicamentos</a></p>
    <p><strong>2. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/forecasting'>Informe Forecasting</a></p>
    <p><strong>4. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/reglas'>Informe reglas de asociación</a></p>
    <p><strong>4. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/descargar'>Descargar archivos almacenados - Históricos</a></p>

</div>