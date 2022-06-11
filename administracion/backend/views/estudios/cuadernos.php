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

$this->title = $estudio['Estudio'].' - Cuadernos';
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
                                data-modal="<?= Url::to(['/estudios/alta-cuaderno', 'id' => $estudio['IdEstudio']]) ?>">
                            <i class="fa fa-plus"></i> Nuevo cuaderno
                        </button>

                        <?php if (count($cuadernos) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr class="tabla-header">
                                    <th>Cuaderno</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cuadernos as $model): ?>
                                <tr>
                                    <td><?= Html::encode($model['Cuaderno']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default"
                                                    data-modal="<?= Url::to(['estudios/modificar-cuaderno',
                                                        'id' => $model['IdCuaderno']]) ?>"
                                                        title="Modificar">
                                                <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                            </button>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['estudios/borrar-cuaderno',
                                                    'id' => $model['IdCuaderno']]) ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <strong>No hay cuadernos agregados a este estudio</strong>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>