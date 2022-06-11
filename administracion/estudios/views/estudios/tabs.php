<?php

use yii\helpers\Url;
use common\components\PermisosHelper;

/* @var $cliente common\models\Clientes */
?>
<ul class="nav nav-tabs" style="margin-bottom: 1em">
    <li><a href="<?= Url::to(['/estudios/estudio', 'id' => $IdEstudio]) ?>">Información del estudio</a></li>
    
    <li><a href="<?= Url::to(['/estudios/usuarios', 'id' => $IdEstudio]) ?>">Usuarios</a></li>
    
    <li><a href="<?= Url::to(['/estudios/origenes', 'id' => $IdEstudio]) ?>">Origenes</a></li>
    
    <li><a href="<?= Url::to(['/estudios/roles', 'id' => $IdEstudio]) ?>">Roles</a></li>
    
    <li><a href="<?= Url::to(['/estudios/estados-caso', 'id' => $IdEstudio]) ?>">Estados de Casos</a></li>
    
    <li><a href="<?= Url::to(['/estudios/tipos-movimiento', 'id' => $IdEstudio]) ?>">Tipos de Movimiento</a></li>
    
    <li><a href="<?= Url::to(['/estudios/parametros', 'id' => $IdEstudio]) ?>">Parámetros</a></li>
</ul>