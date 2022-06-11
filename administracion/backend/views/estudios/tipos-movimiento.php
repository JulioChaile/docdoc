<?php

use common\components\PermisosHelper;
use backend\models\forms\BusquedaForm;
use common\models\TiposMovimiento;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Tipos de movimiento';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        
        <div class="nav-tabs-custom">
            <?= $this->render('tabs', ['IdEstudio' => $estudio['IdEstudio']])?>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <?php
                    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                        echo '<div class="alert alert-' . $key . ' alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                        . $message . '</div>';
                    }
                    ?>
                    <div class="box-body">
                        <div id="errores"></div>
                        <?php if (PermisosHelper::tienePermiso('AltaTipoMovimiento')): ?>
                            <button class="btn btn-primary pull-right" 
                                    data-modal="<?= Url::to(['/estudios/alta-tipo-movimiento', 'id' => $estudio['IdEstudio']]) ?>">
                                <i class="fa fa-plus"></i> Nuevo Tipo de Movimiento
                            </button>
                        <?php endif; ?>

                        <?php $form = ActiveForm::begin(['layout' => 'inline', 'id' => 'buscartiposmov-form']); ?>

                        <?= $form->field($busqueda, 'Cadena') ?>

                        <?= $form->field($busqueda, 'Combo')->dropDownList(ArrayHelper::merge(['T' => 'Todos'], TiposMovimiento::CATEGORIAS)) ?>

                        <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscartiposmov-button']) ?> 

                        <?php ActiveForm::end(); ?>

                        <br>
                        <?php if (count($models) > 0): ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr class="tabla-header">
                                        <th>Tipo de movimiento</th>
                                        <th>Categoría</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($models as $model): ?>
                                    <tr>
                                        <td><?= Html::encode($model['TipoMovimiento']) ?></td>                               
                                        <td><?= TiposMovimiento::CATEGORIAS[$model['Categoria']] ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <?php if (PermisosHelper::tienePermiso('ModificarTipoMovimiento')): ?>
                                                <button class="btn btn-default"
                                                        data-modal="<?= Url::to(['estudios/modificar-tipo-movimiento',
                                                            'id' => $model['IdTipoMov']]) ?>"
                                                            title="Modificar">
                                                    <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                                </button>
                                                <?php endif; ?>
                                                <?php if (PermisosHelper::tienePermiso('BorrarTipoMovimiento')): ?>
                                                    <button class="btn btn-default"
                                                        data-ajax="<?= Url::to(['estudios/borrar-tipo-movimiento',
                                                            'id' => $model['IdTipoMov']]) ?>">
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
                            <strong>No hay tipos de movimiento que coincidan con el criterio de búsqueda</strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
