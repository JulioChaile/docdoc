<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Casos */

use common\components\FechaHelper;
use common\models\Casos;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Movimientos del caso</h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'movimientoscaso-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdCaso') ?>
            
            <?php if (count($movimientos) > 0): ?>
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Responsable</th>
                        <th>Cuaderno</th>
                        <th>Fecha alta</th>
                        <th>Fecha esperada</th>
                        <th>Fecha realizado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td><?= Html::encode($movimiento['TipoMovimiento']) ?></td>
                        <td><?= Html::encode($movimiento['UsuarioResponsable']) ?></td>
                        <td><?= Html::encode($movimiento['Cuaderno']) ?></td>
                        <td><?= Html::encode(FechaHelper::formatearDatetimeLocal($movimiento['FechaAlta'])) ?></td>
                        <td><?= Html::encode(FechaHelper::formatearDatetimeLocal($movimiento['FechaEsperada'])) ?></td>
                        <td><?= Html::encode(FechaHelper::formatearDatetimeLocal($movimiento['FechaRealizado'])) ?></td>
                        <td>
                            
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p><strong>El caso no tiene movimientos</strong></p>
            <?php endif; ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

