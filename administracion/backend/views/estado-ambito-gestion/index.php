<?php

use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use common\models\EstadoAmbitoGestion;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Estados de Ambito de Gestion';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaTipoCaso')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/estado-ambito-gestion/alta']) ?>">
                        <i class="fa fa-plus"></i> Nuevo Estado
                    </button>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>

                <?= $form->field($busqueda, 'Cadena') ?>

                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscarusuario-button']) ?> 

                <?php ActiveForm::end(); ?>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Estados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['EstadoAmbitoGestion']) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if (PermisosHelper::tienePermiso('ModificarTipoCaso')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['estado-ambito-gestion/modificar',
                                                    'id' => $model['IdEstadoAmbitoGestion']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarTipoCaso')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['estado-ambito-gestion/borrar',
                                                    'id' => $model['IdEstadoAmbitoGestion']]) ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        <?php endif; ?> 
                                        <?php if (PermisosHelper::tienePermiso('ModificarTipoCaso')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['estado-ambito-gestion/modificar-mensaje',
                                                    'id' => $model['IdEstadoAmbitoGestion']]) ?>"
                                                    title="Modificar Mensaje">
                                            <i class="fa fa-envelope" style="color: green"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <strong>No hay estados que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>