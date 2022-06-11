<?php

use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use common\models\Jurisdicciones;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Jurisdicciones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaJurisdiccion')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/jurisdicciones/alta']) ?>">
                        <i class="fa fa-plus"></i> Nueva jurisdicción
                    </button>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>

                <?= $form->field($busqueda, 'Cadena') ?>
                
                <?= $form->field($busqueda, 'Check')->checkbox(['value' => 'S', 'uncheck' => 'N'])->label('Mostrar desactivados') ?> 
                                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscarjurisdicciones-button']) ?> 

                <?php ActiveForm::end(); ?>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Jurisdicción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Jurisdiccion']) ?></td>
                                <td><?= Html::encode(Jurisdicciones::ESTADOS[$model['Estado']]) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($model['Estado'] == 'A'): ?>
                                            <?php if (PermisosHelper::tienePermiso('DarBajaJurisdiccion')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['jurisdicciones/dar-baja',
                                                    'id' => $model['IdJurisdiccion']]) ?>"
                                                    title="Dar de baja">
                                                <i class="fa fa-minus-circle" style="color: red"></i>
                                            </button>
                                            <?php endif; ?> 
                                        <?php else: ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['jurisdicciones/activar',
                                                    'id' => $model['IdJurisdiccion']]) ?>"
                                                    title="Activar">
                                                <i class="fa fa-check-circle" style="color: green"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('ModificarJurisdiccion')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['jurisdicciones/modificar',
                                                    'id' => $model['IdJurisdiccion']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarJurisdiccion')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['jurisdicciones/borrar',
                                                    'id' => $model['IdJurisdiccion']]) ?>">
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
                    <strong>No hay jurisdicciones que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>