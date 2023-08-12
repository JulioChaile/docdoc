<?php

use common\models\ObjetivosEstudio;
use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->registerJs('
    window.altaTipoProcesoUrl = "' . Url::to(['estudios/alta-tipo-proceso-judicial']) . '";
');
$this->title = $estudio['Estudio'].' - Objetivos';
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

                        <div class="box">
                            <div class="col-md-12 form-group form-inline">
                                <div class="col-md-9">
                                    <select id="juzgado-select">
                                        <option disabled value="">
                                            Seleccione un tipo de proceso
                                        </option>
                                        <?php foreach ($Juzgados as $juzgado): ?>
                                            <option value="<?= Html::encode($juzgado['IdJuzgado']) ?>"><?= Html::encode($juzgado['Juzgado']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" id="agregar-button" class="btn btn-default">
                                        <i class="fa fa-plus"></i> Agregar
                                    </button>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Manejador del evento click para el botón "Agregar"
                                            document.getElementById('agregar-button').addEventListener('click', function() {
                                                var selectedJuzgado = document.getElementById('juzgado-select').value;
                                                if (selectedJuzgado) {
                                                    // Realizar la petición para agregar el tipo de proceso judicial
                                                    agregarTipoProcesoJudicial(selectedJuzgado);
                                                }
                                            });

                                            // Función para agregar el tipo de proceso judicial
                                            function agregarTipoProcesoJudicial(idJuzgado) {
                                                var estudioId = <?= $estudio['IdEstudio'] ?>;

                                                $.post(
                                                    "/estudios/alta-tipo-proceso-judicial/" +
                                                    estudioId +
                                                    "?idJuzgado=" +
                                                    idJuzgado
                                                )
                                                    .done(function (r) {
                                                        location.reload();
                                                    })
                                                    .fail(function () {
                                                    vm.showError(
                                                        "Ocurrió un error en la comunicación con el servidor. Intente nuevamente más tarde.",
                                                        "danger"
                                                    );
                                                    });
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>

                        <?php if (count($TiposProcesos) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr class="tabla-header">
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($TiposProcesos as $model): ?>
                                <tr>
                                    <td><?= Html::encode($model['Juzgado']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['estudios/borrar-tipo-proceso-judicial',
                                                    'id' => $model['IdTipoProcesoJudicial']]) ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <strong>No hay tipos de proceso que coincidan con el criterio de búsqueda</strong>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>