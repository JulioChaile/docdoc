<?php

use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = 'Empresa';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>
                
                <?= $form->field($busqueda, 'Cadena')->textInput(['placeholder' => 'Ingrese búsqueda']) ?>
                                                        
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscarconsultas-button']) ?> 

                <?php ActiveForm::end(); ?>
                <div class="clearfix"></div>
                <br>
                <?php if (count($models) > 0): ?>
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
                                        <?php if (PermisosHelper::tienePermiso('ModificarConsulta')): ?>
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['empresa/modificar',
                                                    'id' => $model['Parametro']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <strong>No hay variables de empresa que cumplan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>