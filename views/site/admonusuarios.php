<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Administración de Usuarios';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Página para la administración de usuarios por parte del admin
    </p>
    <p><strong>Menú</strong></p>
    <p><strong>1. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/register'>Registro de Usuarios Nuevos</a></p>
    <p><strong>2. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/recoverpass'>Recuperación de contraseña</a></p>
    <p><strong>3. </strong><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/listarusuarios'>Lista de usuarios y edición de información</a></p>
    
</div>