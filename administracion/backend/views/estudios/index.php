<?php

use common\models\Estudios;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda common\models\forms\BusquedaForm */

$this->title = 'Estudios Jurídicos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (in_array('AltaEstudio', Yii::$app->session->get('Permisos'))): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/estudios/alta']) ?>">
                        <i class="fa fa-plus"></i> Nuevo Estudio
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
                                <th>Estudio</th>
                                <th>Ciudad</th>
                                <th>Domicilio</th>
                                <th>Teléfono</th>
                                <th>Especialidades</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Estudio']) ?></td>
                                <td><?= Html::encode($model['Ciudad']) ?></td>
                                <td><?= Html::encode($model['Domicilio']) ?></td>
                                <td><?= Html::encode($model['Telefono']) ?></td>
                                <td><?= Html::encode($model['Especialidades']) ?></td>
                                <td><?= Estudios::ESTADOS[$model['Estado']] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= Url::to(['estudios/estudio', 'id' => $model['IdEstudio']]) ?>"
                                           class="btn btn-default"
                                           title="Detalles del estudio">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <?php if ($model['Estado'] == 'A'): ?>
                                            <?php if (in_array('DarBajaEstudio', Yii::$app->session->get('Permisos'))): ?>
                                                <button class="btn btn-default" 
                                                        data-ajax="<?= Url::to(['/estudios/dar-baja',
                                                            'id' => Html::encode($model['IdEstudio'])]) ?>"
                                                        title="Desactivar">
                                                    <i class="fa fa-minus-circle" style="color: red"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if (in_array('ActivarEstudio', Yii::$app->session->get('Permisos'))): ?>
                                                <button class="btn btn-default" 
                                                        data-ajax="<?= Url::to(['/estudios/activar',
                                                            'id' => Html::encode($model['IdEstudio'])]) ?>"
                                                        title="Activar">
                                                    <i class="fa fa-check-circle" style="color: green"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (in_array('BorrarEstudio', Yii::$app->session->get('Permisos'))): ?>
                                            <button class="btn btn-default" 
                                                    data-ajax="<?= Url::to(['/estudios/borrar',
                                                        'id' => Html::encode($model['IdEstudio'])]) ?>">
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
                    <strong>No hay estudios que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div> 
</div>
