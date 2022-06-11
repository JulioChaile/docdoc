<?php

use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Roles de estudio';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        
        <div class="nav-tabs-custom">
            <?= $this->render('tabs', ['IdEstudio' => $estudio['IdEstudio']])?>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <?php
                    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                        echo '<div class="alert alert-' . $key . ' alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                        . $message . '</div>';
                    }
                    ?>
                    <div class="box-body">
                        <div id="errores"></div>
                        <?php if (PermisosHelper::tienePermiso('AltaRolEstudio')): ?>
                            <button class="btn btn-primary pull-right" 
                                    data-modal="<?= Url::to(['/estudios/alta-rol',
                                        'id' => $estudio['IdEstudio']]) ?>">
                                <i class="fa fa-plus"></i> Nuevo Rol
                            </button>
                        <?php endif; ?> 
                        <br>
                        <?php if (count($models) > 0): ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr class="tabla-header">
                                        <th>Rol</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($models as $model): ?>
                                    <tr>
                                        <td><?= Html::encode($model['RolEstudio']) ?></td>                               
                                        <td>
                                            <div class="btn-group">
                                                <?php if (PermisosHelper::tienePermiso('BorrarRolEstudio')): ?>
                                                    <button class="btn btn-default"
                                                        data-mensaje="Segur@ desea eliminar el rol?" 
                                                        data-ajax="<?= Url::to(['estudios/borrar-rol',
                                                            'id' => $model['IdRolEstudio']]) ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                <?php endif; ?> 
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <strong>Aún no se cargaron roles dentro del estudio.</strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
