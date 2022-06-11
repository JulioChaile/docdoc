<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Casos */

use common\models\Casos;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Personas involucradas</h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'actorescaso-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdCaso') ?>
            
            
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Apellido/s, Nombres/s</th>
                        <th>Documento</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($actores as $actor): ?>
                    <tr>
                        <td><?= Html::encode($actor['Rol']) ?></td>
                        <td><?= Html::encode($actor['Apellidos'].', '.$actor['Nombres']) ?></td>
                        <td><?= Html::encode($actor['Documento']) ?></td>
                        <td><?= Html::encode($actor['Observaciones']) ?></td>
                        <td>
                            
                        </td>
                    </tr>
                    
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

