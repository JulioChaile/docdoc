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

    <li><a href="<?= Url::to(['/estudios/objetivos', 'id' => $IdEstudio]) ?>">Objetivos por Defecto</a></li>

    <li><a href="<?= Url::to(['/estudios/tipos-proceso-judiciales', 'id' => $IdEstudio]) ?>">Tipos de Procesos Judiciales</a></li>

    <li><a href="<?= Url::to(['/estudios/cuadernos', 'id' => $IdEstudio]) ?>">Cuadernos</a></li>

    <li><a href="<?= Url::to(['/estudios/mensajes', 'id' => $IdEstudio]) ?>">Mensajes por Defecto</a></li>

    <li><a href="<?= Url::to(['/estudios/eventos', 'id' => $IdEstudio]) ?>">Eventos</a></li>
    
    <li><a href="<?= Url::to(['/estudios/parametros', 'id' => $IdEstudio]) ?>">Parámetros</a></li>
</ul>