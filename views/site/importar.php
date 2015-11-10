<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Importar Datos';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Señor Administrador: Este módulo le permitirá cargar los archivos con extensión .xls generados por la herramienta
        RapidMiner.
    </p>
    <p>
        Una vez se ejecute el proceso, diríjase al menú de esta página e importe el archivo xls dependiendo del proceso de minería
        de datos ejecutado.
    </p>
    <p><strong>Menú</strong></p>
    <p><strong>1. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/importreglas'>Importar resultados RapidMiner para Reglas de Asociación</a></p>
    <p><strong>2. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/importseries'>Importar resultados RapidMiner para Pronóstico - Series de Tiempo</a></p>

</div>