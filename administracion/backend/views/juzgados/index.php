<?php

use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use backend\assets\JuzgadosAsset;
use common\models\Juzgados;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Juzgados';
$this->params['breadcrumbs'][] = $this->title;
JuzgadosAsset::register($this);
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaJuzgado')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/juzgados/alta']) ?>">
                        <i class="fa fa-plus"></i> Nuevo juzgado
                    </button>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>
                
                <?= $form->field($busqueda, 'Id')->widget(Select2::className(), [
                    'options' => [
                        'placeholder' => 'Buscar jurisdicción'
                    ],
                    'data' => ArrayHelper::map($jurisdicciones, 'IdJurisdiccion', 'Jurisdiccion'),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]);
                ?>

                <?= $form->field($busqueda, 'Cadena') ?>
                
                <?= $form->field($busqueda, 'Check')->checkbox(['value' => 'S', 'uncheck' => 'N'])->label('Mostrar desactivados') ?> 
                                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscarjuzgados-button']) ?> 

                <?php ActiveForm::end(); ?>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Juzgado</th>
                                <th>Jurisdicción</th>
                                <th>Modo Gestión</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Juzgado']) ?></td>
                                <td><?= Html::encode($model['Jurisdiccion']) ?></td>
                                <td><?= Html::encode(Juzgados::MODOS_GESTION[$model['ModoGestion']]) ?></td>
                                <td><?= Html::encode(Juzgados::ESTADOS[$model['Estado']]) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-default"
                                            title="Estados de Ambito de Gestion"
                                            data-modal="<?= Url::to(['juzgados/estados',
                                                'id' => $model['IdJuzgado']]) ?>">
                                            <i class="fa fa-tag" style="color: #d4d52c"></i>
                                        </a>
                                        <a class="btn btn-default"
                                           href="<?= Url::to(['juzgados/nominaciones', 'id' => $model['IdJuzgado']]) ?>"
                                           title="Nominaciones">
                                            <i class="fa fa-gavel"></i>
                                        </a>
                                        <?php if ($model['Estado'] == 'A'): ?>
                                            <?php if (PermisosHelper::tienePermiso('DarBajaJuzgado')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['juzgados/dar-baja',
                                                    'id' => $model['IdJuzgado']]) ?>"
                                                    title="Dar de baja">
                                                <i class="fa fa-minus-circle" style="color: red"></i>
                                            </button>
                                            <?php endif; ?> 
                                        <?php else: ?>
                                            <?php if (PermisosHelper::tienePermiso('ActivarJuzgado')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['juzgados/activar',
                                                    'id' => $model['IdJuzgado']]) ?>"
                                                    title="Activar">
                                                <i class="fa fa-check-circle" style="color: green"></i>
                                            </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('ModificarJuzgado')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['juzgados/modificar',
                                                    'id' => $model['IdJuzgado']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarJuzgado')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['juzgados/borrar',
                                                    'id' => $model['IdJuzgado']]) ?>">
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
                    <strong>No hay juzgados que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>