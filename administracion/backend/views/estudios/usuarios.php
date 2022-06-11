<?php

use common\components\PermisosHelper;
use common\models\Usuarios;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$array = [
    'T' => 'Todos',
    'M' => 'Matrícula',
    'N' => 'Apellidos, Nombres'
];
$this->title = $estudio['Estudio'].' - Usuarios';
$this->params['breadcrumbs'] = [
    [
        'label' => 'Estudios',
        'url' => ['/estudios']
    ],
    $this->title
];
?>
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <?= $this->render('tabs', ['IdEstudio' => $estudio['IdEstudio']])?>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">                     
                    <div class="box-body">
                        <div id="errores"></div>
                        <?php if (in_array('AltaUsuarioEstudio', Yii::$app->session->get('Permisos'))): ?>
                            <button class="btn btn-primary pull-right" 
                                    data-modal="<?= Url::to(['/estudios/alta-usuario', 'id' => $estudio['IdEstudio']]) ?>">
                                <i class="fa fa-plus"></i> Nuevo Usuario
                            </button>
                            <button class="btn btn-primary pull-right" 
                                    data-modal="<?= Url::to(['/estudios/alta-usuario', 'id' => $estudio['IdEstudio'], 'cadete' => 1]) ?>">
                                <i class="fa fa-plus"></i> Nuevo Cadete
                            </button>
                        <?php endif; ?>
                        <?php $form = ActiveForm::begin(['layout' => 'inline']) ?>

                        <?= $form->field($busqueda, 'Combo')->dropDownList($array) ?>

                        <?= $form->field($busqueda, 'Cadena') ?>

                        <?= $form->field($busqueda, 'Check')->checkbox(['value' => 'S', 'uncheck' => 'N'])->label('Mostrar desactivados') ?>

                        <?= Html::submitButton('Buscar', ['class' => 'btn btn-default']) ?>
                        <?php ActiveForm::end() ?>

                        <div class="clearfix"></div>
                        <?php if (count($models) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Apellidos, Nombres</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Email</th>
                                    <th>Telefono</th>
                                    <th>Estado de Usuario</th>
                                    <th>Estado en Estudio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($models as $model): ?>
                                <tr>
                                    <td><?= Html::encode($model['Apellidos'].', '.$model['Nombres']) ?></td>
                                    <td><?= Html::encode($model['Usuario']) ?></td>
                                    <td><?= Html::encode($model['RolEstudio']) ?></td>
                                    <td><?= Html::encode($model['Email']) ?></td>
                                    <td><?= Html::encode($model['Telefono']) ?></td>
                                    <td><?= Html::encode(Usuarios::ESTADOS[$model['EstadoUsuario']]) ?></td>
                                    <td><?= Html::encode(Usuarios::ESTADOS[$model['Estado']]) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if (PermisosHelper::tienePermiso('ModificarUsuario')): ?>
                                                <button class="btn btn-default" type="button"
                                                        title="Modificar usuario"
                                                        data-modal="<?= Url::to(['/usuarios/modificar', 'id' => $model['IdUsuario']]) ?>">
                                                    <i class="fa fa-edit" style="color: dodgerblue"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($model['EstadoUsuario'] == 'A'): ?>
                                                <?php if (PermisosHelper::tienePermiso('DarBajaUsuario')): ?>
                                                <button class="btn btn-default" type="button"
                                                        title="Dar de baja usuario"
                                                        data-ajax="<?= Url::to(['/usuarios/dar-baja',
                                                            'id' => $model['IdUsuario']]) ?>">
                                                    <i class="fa fa-minus-circle" style="color: red"></i>
                                                </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if ($model['EstadoUsuario'] == 'B' || $model['EstadoUsuario'] == 'S'): ?>
                                                <?php if (PermisosHelper::tienePermiso('ActivarUsuario')): ?>
                                                <button class="btn btn-default" type="button"
                                                        title="Activar usuario"
                                                        data-ajax="<?= Url::to(['/usuarios/activar',
                                                            'id' => $model['IdUsuario']]) ?>">
                                                    <i class="fa fa-check-circle" style="color: green"></i>
                                                </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if ($model['Estado'] == 'A'): ?>
                                                <?php if (PermisosHelper::tienePermiso('DarBajaUsuarioEstudio')): ?>
                                                <button class="btn btn-default" type="button"
                                                        title="Dar de baja de estudio"
                                                        data-ajax="<?= Url::to(['/estudios/dar-baja-usuario',
                                                            'id' => $estudio['IdEstudio'],
                                                            'idUsuario' => $model['IdUsuario']]) ?>">
                                                    <i class="fa fa-minus-circle" style="color: orange"></i>
                                                </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if ($model['Estado'] == 'B' || $model['Estado'] == 'S'): ?>
                                                <?php if (PermisosHelper::tienePermiso('ActivarUsuarioEstudio')): ?>
                                                <button class="btn btn-default" type="button"
                                                        title="Activar usuario de estudio"
                                                        data-ajax="<?= Url::to(['/estudios/activar-usuario',
                                                            'id' => $estudio['IdEstudio'],
                                                            'idUsuario' => $model['IdUsuario']]) ?>">
                                                    <i class="fa fa-check-circle" style="color: lightblue"></i>
                                                </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (PermisosHelper::tienePermiso('BorrarUsuarioEstudio')) :?>
                                            <button class="btn btn-default" type="button"
                                                    data-mensaje="Desea quitar al usuari@ del estudio?"
                                                    data-ajax="<?= Url::to(['/estudios/borrar-usuario',
                                                        'id' => $estudio['IdEstudio'],
                                                        'idUsuario' => $model['IdUsuario']]) ?>">
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
                        <p><strong>No hay usuarios asociados al estudio.</strong></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>