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

$this->title = 'Competencias';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaTipoCaso')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/competencias/alta']) ?>">
                        <i class="fa fa-plus"></i> Nueva Competencia
                    </button>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>

                <?= $form->field($busqueda, 'Cadena') ?>
                
                <?= $form->field($busqueda, 'Check')->checkbox(['value' => 'S', 'uncheck' => 'N'])->label('Mostrar desactivadas') ?> 

                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscar-button']) ?> 

                <?php ActiveForm::end(); ?>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Competencia</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Competencia']) ?></td>                               
                                <td><?= Competencias::ESTADOS[$model['Estado']] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-default"
                                        title="Tipos de caso"
                                           data-modal="<?= Url::to(['competencias/tipos-caso',
                                               'id' => $model['IdCompetencia']]) ?>">
                                            <i class="fa fa-tag" style="color: #d4d52c"></i>
                                        </a>
                                        <?php if ($model['Estado'] == 'A'): ?>
                                            <?php if (PermisosHelper::tienePermiso('DarBajaTipoCaso')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['competencias/dar-baja',
                                                    'id' => $model['IdCompetencia']]) ?>"
                                                    title="Dar de baja">
                                                <i class="fa fa-minus-circle" style="color: red"></i>
                                            </button>
                                            <?php endif; ?> 
                                        <?php else: ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['competencias/activar',
                                                    'id' => $model['IdCompetencia']]) ?>"
                                                    title="Activar">
                                                <i class="fa fa-check-circle" style="color: green"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('ModificarTipoCaso')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['competencias/modificar',
                                                    'id' => $model['IdCompetencia']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarTipoCaso')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['competencias/borrar',
                                                    'id' => $model['IdCompetencia']]) ?>">
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
                    <strong>No hay competencias que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>