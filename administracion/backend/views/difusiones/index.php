<?php

use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Difusiones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaDifusion')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/difusiones/alta']) ?>">
                        <i class="fa fa-plus"></i> Nueva campaña
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
                                <th>Campaña</th>
                                <th>Fecha inicio</th>
                                <th>Fecha fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Difusion']) ?></td>
                                <td><?= Html::encode($model['FechaInicio']) ?></td>
                                <td><?= Html::encode($model['FechaFin']) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if (PermisosHelper::tienePermiso('ModificarDifusion')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['difusiones/modificar',
                                                    'id' => $model['IdDifusion']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarDifusion')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['difusiones/borrar',
                                                    'id' => $model['IdDifusion']]) ?>">
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
                    <strong>No hay campañas de difusión que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>