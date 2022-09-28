<?php

use backend\assets\CompetenciasAsset;
use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use common\models\Competencias;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

CompetenciasAsset::register($this);

$this->title = 'Resoluciones SMVM';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaTipoCaso')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/resoluciones/alta']) ?>">
                        <i class="fa fa-plus"></i> Nueva Resolucion
                    </button>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>

                <?= $form->field($busqueda, 'Cadena') ?>
                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscar-button']) ?> 

                <?php ActiveForm::end(); ?>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Resolucion</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Resolucion']) ?></td>
                                <td><?= Html::encode($model['FechaResolucion']) ?></td>
                                <td><?= Html::encode($model['MontoResolucion']) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if (PermisosHelper::tienePermiso('ModificarTipoCaso')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['resoluciones/modificar',
                                                    'id' => $model['IdResolucionSMVM']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarTipoCaso')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['resoluciones/borrar',
                                                    'id' => $model['IdResolucionSMVM']]) ?>">
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
                    <strong>No hay resoluciones que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>