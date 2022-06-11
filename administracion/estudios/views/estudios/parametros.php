<?php

use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Parámetros';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <?= $this->render('tabs', ['IdEstudio' => $estudio['IdEstudio']])?>
            <div class="tab-content" >
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
                        <br>
                        <table class="table table-hover">
                            <thead>
                                <tr class="tabla-header">
                                    <th>Parámetro</th>
                                    <th>Descripción</th>
                                    <th>Rango</th>
                                    <th>Valor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($models as $model): ?>
                                <tr>
                                    <td><?= Html::encode($model['Parametro']) ?></td>
                                    <td><?= Html::encode($model['Descripcion']) ?></td>
                                    <td><?= Html::encode($model['Rango']) ?></td>
                                    <td><?= Html::encode($model['Valor']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if (PermisosHelper::tienePermiso('ModificarParametro')): ?>
                                            <button class="btn btn-default"
                                                    data-modal="<?= Url::to(['estudios/modificar-parametro',
                                                        'id' => $estudio->IdEstudio,
                                                        'parametro' => $model['Parametro']]) ?>"
                                                    title="Modificar"
                                                    >
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
