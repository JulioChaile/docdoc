<?php

use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use backend\models\Nominaciones;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Nominaciones ('.$juzgado['Juzgado'].')';

$this->params['breadcrumbs'] = [
    [
        'label' => 'Juzgados',
        'url' => ['/juzgados']
    ],
    $this->title
];
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaNominacion')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/juzgados/alta-nominacion',
                                'id' => $juzgado['IdJuzgado']]) ?>">
                        <i class="fa fa-plus"></i> Nueva nominación
                    </button>
                <?php endif; ?>
                <?php $form = ActiveForm::begin(['layout' => 'inline']) ?>
                
                <?= $form->field($busqueda, 'Check')->checkbox(['value' => 'S', 'uncheck' => 'N'])->label('Mostrar desactivados') ?> 
                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscarusuario-button']) ?> 
                
                <?php ActiveForm::end() ?>
                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Nominación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Nominacion']) ?></td>
                                <td><?= Html::encode(Nominaciones::ESTADOS[$model['Estado']]) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($model['Estado'] == 'A'): ?>
                                            <?php if (PermisosHelper::tienePermiso('DarBajaNominacion')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['juzgados/dar-baja-nominacion',
                                                    'id' => $model['IdNominacion']]) ?>"
                                                    title="Dar de baja">
                                                <i class="fa fa-minus-circle" style="color: red"></i>
                                            </button>
                                            <?php endif; ?> 
                                        <?php else: ?>
                                            <?php if (PermisosHelper::tienePermiso('ActivarNominacion')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['juzgados/activar-nominacion',
                                                    'id' => $model['IdNominacion']]) ?>"
                                                    title="Activar">
                                                <i class="fa fa-check-circle" style="color: green"></i>
                                            </button>
                                            <?php endif;?>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('ModificarNominacion')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['juzgados/modificar-nominacion',
                                                    'id' => $model['IdNominacion']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (PermisosHelper::tienePermiso('BorrarNominacion')): ?>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['juzgados/borrar-nominacion',
                                                    'id' => $model['IdNominacion']]) ?>">
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
                    <strong>No hay nominaciones cargadas en el juzgado.</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>