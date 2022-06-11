<?php

use backend\models\Roles;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda backend\models\forms\BusquedaForm */

$this->title = 'Roles y Permisos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (in_array('AltaRol', Yii::$app->session->get('Permisos'))): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/roles/alta']) ?>">
                        <i class="fa fa-plus"></i> Nuevo Rol
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
                                <th>Rol</th>
                                <th>Observaciones</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Rol']) ?></td>
                                <td><?= Html::encode($model['Observaciones']) ?></td>
                                <td><?= Roles::ESTADOS[$model['Estado']] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($model['Estado'] == 'A'): ?>
                                            <?php if (in_array('DarBajaRol', Yii::$app->session->get('Permisos'))): ?>
                                                <button class="btn btn-default" 
                                                        data-ajax="<?= Url::to(['/roles/dar-baja', 'id' => Html::encode($model['IdRol'])]) ?>"
                                                        title="Desactivar">
                                                    <i class="fa fa-minus-circle" style="color: red"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if (in_array('ActivarRol', Yii::$app->session->get('Permisos'))): ?>
                                                <button class="btn btn-default" 
                                                        data-ajax="<?= Url::to(['/roles/activar', 'id' => Html::encode($model['IdRol'])]) ?>"
                                                        title="Activar">
                                                    <i class="fa fa-check-circle" style="color: green"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (in_array('ModificarRol', Yii::$app->session->get('Permisos'))): ?>
                                            <button class="btn btn-default" 
                                                    data-modal="<?= Url::to(['/roles/modificar', 'id' => Html::encode($model['IdRol'])]) ?>"
                                                    title="Modificar">
                                                <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (in_array('BorrarRol', Yii::$app->session->get('Permisos'))): ?>
                                            <button class="btn btn-default" 
                                                    data-ajax="<?= Url::to(['/roles/borrar', 'id' => Html::encode($model['IdRol'])]) ?>"
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (in_array('AsignarPermisosRol', Yii::$app->session->get('Permisos'))): ?>
                                            <a class="btn btn-default" 
                                               href="<?= Url::to(['/roles/permisos', 'id' => Html::encode($model['IdRol'])]) ?>"
                                               title="Ver permisos">
                                                <i class="fa fa-key" style="color: goldenrod"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <strong>No hay roles que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>