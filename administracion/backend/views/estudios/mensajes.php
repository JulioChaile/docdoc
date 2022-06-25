<?php

use common\models\MensajesEstudio;
use common\components\PermisosHelper;
use common\models\forms\BusquedaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda BusquedaForm */

$this->title = $estudio['Estudio'].' - Mensajes';
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
                        <button class="btn btn-primary pull-right" 
                                data-modal="<?= Url::to(['/estudios/alta-mensaje', 'id' => $estudio['IdEstudio']]) ?>">
                            <i class="fa fa-plus"></i> Nuevo mensaje
                        </button>

                        <?php if (count($mensajes) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr class="tabla-header">
                                    <th>Titulo</th>
                                    <th>Mensaje</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mensajes as $model): ?>
                                <tr>
                                    <td><?= Html::encode($model['Titulo']) ?></td>
                                    <td><?= Html::encode($model['MensajeEstudio']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <!--button class="btn btn-default"
                                                    data-modal="<?= Url::to(['estudios/modificar-mensaje',
                                                        'id' => $model['IdMensajeEstudio']]) ?>"
                                                        title="Modificar">
                                                <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                            </button-->
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['estudios/borrar-mensaje',
                                                    'id' => $model['IdMensajeEstudio']]) ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <strong>No hay mensajes que coincidan con el criterio de búsqueda</strong>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>