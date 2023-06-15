<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Juzgados */

use common\models\Juzgados;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

require_once(__DIR__ . '/yii2-widget-colorinput/src/ColorInput.php');
require_once(__DIR__ . '/yii2-widget-colorinput/src/ColorInputAsset.php');
use kartik\color\ColorInput;
use kartik\color\ColorInputAsset;

?>
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'juzgados-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($model, 'IdJuzgado') ?>
            
            <?= $form->field($model, 'IdJurisdiccion')->widget(Select2::className(), [
                    'options' => [
                        'placeholder' => 'Buscar jurisdicción'
                    ],
                    'data' => ArrayHelper::map($jurisdicciones, 'IdJurisdiccion', 'Jurisdiccion'),
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]);
            ?>
            
            <?= $form->field($model, 'Juzgado') ?>

            <?= $form->field($model, 'ModoGestion')->widget(Select2::className(), [
                    'options' => [
                        'placeholder' => 'Modo de gestión'
                    ],
                    'data' => Juzgados::MODOS_GESTION
                ]);
            ?>

            <?= $form->field($model, 'Color')->widget(ColorInput::className(), [
                    'options' => ['placeholder' => 'Seleccione un color'],
                    'value' => $model->Color || '#000000',
                    'pluginOptions' => [
                        'showInput' => true,
                        'showInitial' => true,
                        'showPalette' => true,
                        'showPaletteOnly' => true,
                        'showSelectionPalette' => true,
                        'allowEmpty' => true,
                        'preferredFormat' => 'name',
                        'palette' => [
                            ['red', 'blue', 'green'],
                            ['yellow', 'cyan', 'magenta'],
                            ['black', 'white']
                        ]
                    ]
                ]);
            ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?> 
    </div>
</div>