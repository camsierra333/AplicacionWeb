<?php 
use yii\helpers\Url; 
use yii\helpers\Html;
?>

<?php

$this->title = 'Descargar archivos guardados para análisis de datos';

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        A continuación se listan los archivos que se encuentran en el servidor usados en el análsis de datos. Haga Click en el nombre del archivo que desea descargar.
    </p>
    
<?php
$directorio = opendir("archivos/"); 
while ($archivo = readdir($directorio)) 
{
    if (is_dir($archivo))
    {
    }
    else
    {
        if (Yii::$app->session->hasFlash('errordownload')): ?>
        <strong class="label label-danger">Error al descargar Archivo</strong>

        <?php else: ?>
        
        <a href="<?= Url::toRoute(["site/descargar", "file" => $archivo]) ?>"><?php echo $archivo?></a><br>

        <?php endif; ?>
        <?php 
    }
}
?>
        
</div>
        
