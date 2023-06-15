<?php

use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use common\models\TiposCaso;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Tipos de caso';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaTipoCaso')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/tipos-caso/alta']) ?>">
                        <i class="fa fa-plus"></i> Nuevo Tipo de Caso
                    </button>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>

                <?= $form->field($busqueda, 'Cadena') ?>
                
                <?= $form->field($busqueda, 'Check')->checkbox(['value' => 'S', 'uncheck' => 'N'])->label('Mostrar desactivados') ?> 

                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscarusuario-button']) ?> 

                <?php ActiveForm::end(); ?>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Tipo de caso</th>
                                <th>Estado</th>
                                <th>Color</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['TipoCaso']) ?></td>                               
                                <td><?= TiposCaso::ESTADOS[$model['Estado']] ?></td>
                                <td style="background-color: <?= Html::encode($model['Color']) ?>"></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if (PermisosHelper::tienePermiso('ModificarTipoCaso')): ?>
                                            <a class="btn btn-default"
                                            title="Juzgados"
                                                data-modal="<?= Url::to(['tipos-caso/juzgados',
                                                    'id' => $model['IdTipoCaso']]) ?>">
                                                <i class="fa fa-university" style="color: green"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a class="btn btn-default"
                                           href="<?= Url::to(['tipos-caso/roles',
                                               'id' => $model['IdTipoCaso']]) ?>">
                                            <i class="fa fa-tag" style="color: #d4d52c"></i>
                                        </a>
                                        <?php if ($model['Estado'] == 'A'): ?>
                                            <?php if (PermisosHelper::tienePermiso('DarBajaTipoCaso')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['tipos-caso/dar-baja',
                                                    'id' => $model['IdTipoCaso']]) ?>"
                                                    title="Dar de baja">
                                                <i class="fa fa-minus-circle" style="color: red"></i>
                                            </button>
                                            <?php endif; ?> 
                                        <?php else: ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['tipos-caso/activar',
                                                    'id' => $model['IdTipoCaso']]) ?>"
                                                    title="Activar">
                                                <i class="fa fa-check-circle" style="color: green"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('ModificarTipoCaso')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['tipos-caso/modificar',
                                                    'id' => $model['IdTipoCaso']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarTipoCaso')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['tipos-caso/borrar',
                                                    'id' => $model['IdTipoCaso']]) ?>">
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
                    <strong>No hay tipos de caso que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>