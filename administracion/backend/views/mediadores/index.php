<?php

use backend\models\forms\BusquedaForm;
use common\models\Mediadores;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Mediadores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <button class="btn btn-primary pull-right" 
                        data-modal="<?= Url::to(['/mediadores/alta']) ?>">
                    <i class="fa fa-plus"></i> Nueva Mediador
                </button>

                <?php $form = ActiveForm::begin(['layout' => 'inline',]); ?>

                <?= $form->field($busqueda, 'Cadena') ?>
                
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscar-button']) ?> 

                <?php ActiveForm::end(); ?>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Nombre</th>
                                <th>Registro</th>
                                <th>MP</th>
                                <th>Domicilio</th>
                                <th>Telefono</th>
                                <th>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['Nombre']) ?></td>
                                <td><?= Html::encode($model['Registro']) ?></td>
                                <td><?= Html::encode($model['MP']) ?></td>
                                <td><?= Html::encode($model['Domicilio']) ?></td>
                                <td><?= Html::encode($model['Telefono']) ?></td>
                                <td><?= Html::encode($model['Email']) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-default"
                                                data-modal="<?= Url::to(['mediadores/modificar',
                                                    'id' => $model['IdMediador']]) ?>"
                                                    title="Modificar">
                                            <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                        </button>
                                        <button class="btn btn-default"
                                            data-ajax="<?= Url::to(['mediadores/borrar',
                                                'id' => $model['IdMediador']]) ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <strong>No hay mediadores que coincidan con el criterio de búsqueda</strong>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>