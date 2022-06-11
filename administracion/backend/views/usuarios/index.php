<?php

use common\models\forms\BusquedaForm;
use common\models\Usuarios;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Usuarios';
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (in_array('AltaUsuario', Yii::$app->session->get('Permisos'))) : ?>
                    <button type="button" class="btn btn-primary pull-right"
                            data-modal="<?= Url::to(['usuarios/alta']) ?>">
                        <i class="fa fa-plus"></i> Nuevo usuario
                    </button>
                <?php endif; ?>
                <?php $form = ActiveForm::begin(['id' => 'buscarusuarios-form', 'layout' => 'inline']) ?>
                
                <?= $form->field($busqueda, 'Combo')->dropDownList(ArrayHelper::merge(['T' => 'Todos'], Usuarios::ESTADOS)) ?>
                
                <?= $form->field($busqueda, 'Cadena') ?>
                
                <?= $form->field($busqueda, 'Check')->checkbox(['value' => 'S', 'uncheck' => 'N'])->label('Mostrar desactivados') ?>

                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default']) ?>

                <?php ActiveForm::end() ?>
                <br>
                <?php if (count($models) > 0) : ?>
                <table class="table table-hover">
                    <thead>
                        <tr class="tabla-header">
                            <th>Apellido/s</th>
                            <th>Nombre/s</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($models as $model) : ?>
                            <tr>
                                <td><?= Html::encode($model['Apellidos']) ?></td>
                                <td><?= Html::encode($model['Nombres']) ?></td>
                                <td><?= Html::encode($model['Usuario']) ?></td>
                                <td><?= Html::encode($model['Rol']) ?></td>
                                <td><?= Usuarios::ESTADOS[$model['Estado']] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($model['Estado'] == 'B' || $model['Estado'] == 'S') : ?>
                                            <?php if (in_array('ActivarUsuario', Yii::$app->session->get('Permisos'))) : ?>
                                                <button type="button" class="btn btn-default"
                                                        data-ajax="<?= Url::to(['usuarios/activar', 'id' => $model['IdUsuario']]) ?>"
                                                        title="Activar">
                                                    <i class="fa fa-check-circle" style="color: green"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <?php if (in_array('DarBajaUsuario', Yii::$app->session->get('Permisos'))) : ?>
                                                <button type="button" class="btn btn-default"
                                                        data-ajax="<?= Url::to(['usuarios/dar-baja', 'id' => $model['IdUsuario']]) ?>"
                                                        title="Dar baja">
                                                    <i class="fa fa-minus-circle" style="color: red"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (in_array('ModificarUsuario', Yii::$app->session->get('Permisos'))) : ?>
                                            <button type="button" class="btn btn-default"
                                                    data-modal="<?= Url::to(['usuarios/modificar', 'id' => $model['IdUsuario']]) ?>"
                                                    title="Modificar">
                                                <i class="fa fa-edit" style="color: dodgerblue"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (in_array('BorrarUsuario', Yii::$app->session->get('Permisos'))) : ?>
                                            <button type="button" class="btn btn-default"
                                                    data-ajax="<?= Url::to(['usuarios/borrar', 'id' => $model['IdUsuario']]) ?>"
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
                    <p><strong>No existen usuarios con el criterio de búsqueda indicado.</strong></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
