<?php
?>

<h1><center>Listado de bodegas de medicamentos</center></h1>
<br>
<br>

<table class="table table-bordered">
    <tr>
        <th><center>IdBodega</center></th>
        <th><center>CodBodega</center></th>
        <th><center>NomBodega</center></th>
    </tr>
    
    <?php for($x=0; $x<$row_count; $x++): ?>
    <tr>
        <td><center><?= $arreglo["IdBodega"][$x]?></center></td>
        <td><center><?= $arreglo["CodBodega"][$x]?></center></td>
        <td><center><?= $arreglo["NomBodega"][$x]?></center></td>
    </tr>
    <?php endfor ?>
</table>

