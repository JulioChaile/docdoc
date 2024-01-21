<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model EstadosCaso */

use common\models\ObjetivosEstudio;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'objetivosestudio-form',]) ?>

        <div class="modal-body">
            <div id="errores-modal"> </div>

            <?= Html::activeHiddenInput($combo, 'IdComboObjetivos') ?>
            
            <?= Html::activeHiddenInput($combo, 'IdEstudio') ?>
            
            <?= $form->field($combo, 'ComboObjetivos', ['options' => ['id' => 'combo-objetivo']]) ?>

            <!-- SELECT -->
            <?php
            // Agrega un elemento select con opciones del array de objetos
                echo $form->field($combo, 'Objetivos')->dropDownList(
                    \yii\helpers\ArrayHelper::map($objetivos, 'IdObjetivoEstudio', 'ObjetivoEstudio'),
                    ['prompt' => 'Selecciona un objetivo', 'id' => 'select-objetivos']
                );
            ?>

            <button type="button" class="btn btn-default" id="agregar-btn">Agregar</button>

            <ul id="objetivos-seleccionados-list">
                <?php foreach ($objetivosSeleccionados as $objetivo): ?>
                    <li id='<?= $objetivo['IdObjetivoEstudio'] ?>'>
                        <?= Html::encode($objetivo['ObjetivoEstudio']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button
                class="btn btn-primary" 
                id="guardar-btn"
            >
                Guardar
            </button>
            <script>
                document.getElementById('guardar-btn').addEventListener('click', function() {
                    const lis = document.querySelectorAll('#objetivos-seleccionados-list li')
                    const idsObjetivos = []

                    for (let i = 0; i < lis.length; i++) {
                        idsObjetivos.push(lis[i].id);
                    }
                    
                    $.post(
                        "/estudios/alta-combo-objetivos/" +
                        <?= $combo['IdEstudio'] ?>,
                        {
                            idsObjetivos,
                            comboObjetivos: document.getElementById('comboobjetivos-comboobjetivos').value,
                            alta: true
                        }
                    )
                        .done(function (r) {
                            if (!r.Error) location.reload();
                        })
                        .fail(function () {
                        vm.showError(
                            "Ocurrió un error en la comunicación con el servidor. Intente nuevamente más tarde.",
                            "danger"
                        );
                        });
                });
            </script>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs("
    $('#agregar-btn').click(function(e) {
        e.preventDefault()
        var IdObjetivoEstudio = $('#select-objetivos').val();
        var ObjetivoEstudio = $('#select-objetivos option:selected').text();

        if ($('#objetivos-seleccionados-list li:contains(' + ObjetivoEstudio + ')').length === 0) {
            var liId = IdObjetivoEstudio;
            $('#objetivos-seleccionados-list').append('<li id=\"' + liId + '\">' + ObjetivoEstudio + '</li>')
        }
    });
");
