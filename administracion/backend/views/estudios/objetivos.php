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
                        <button class="btn btn-primary pull-right" 
                                data-modal="<?= Url::to(['/estudios/alta-objetivo', 'id' => $estudio['IdEstudio']]) ?>">
                            <i class="fa fa-plus"></i> Nuevo objetivo
                        </button>
                        <button class="btn btn-primary pull-right" 
                                data-modal="<?= Url::to(['/estudios/alta-combo-objetivos', 'id' => $estudio['IdEstudio']]) ?>">
                            <i class="fa fa-plus"></i> Nuevo combo
                        </button>

                        <h3>Combos</h3>

                        <?php if (count($combos) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr class="tabla-header">
                                    <th>Combo</th>
                                    <th>Objetivos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($combos as $combo): ?>
                                <tr>
                                    <td><?= Html::encode($combo['ComboObjetivos']) ?></td>
                                    <td>
                                        <ul>
                                            <?php foreach ($combo['Objetivos'] as $objetivo): ?>
                                                <li>
                                                    <div style="width: 100% !important">
                                                        <?= Html::encode($objetivo['ObjetivoEstudio']) ?>
                                                        <button class="btn btn-default" style="margin-left: auto !important"
                                                            data-ajax="<?= Url::to(['estudios/borrar-objetivo-combo-objetivos',
                                                                'id' => $objetivo['IdObjetivoEstudio']]) ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['estudios/borrar-combo-objetivos',
                                                    'id' => $combo['IdComboObjetivos']]) ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <strong>No hay combos agregados en el estudio</strong>
                        <?php endif; ?>

                        <h3>Objetivos</h3>

                        <?php if (count($objetivos) > 0): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr class="tabla-header">
                                    <th>Nombre</th>
                                    <th>Tipo de Movimiento</th>
                                    <th>Estado de Gestión</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($objetivos as $model): ?>
                                <tr>
                                    <td><?= Html::encode($model['ObjetivoEstudio']) ?></td>
                                    <td><?= Html::encode($model['TipoMovimiento']) ?></td>
                                    <td><?= Html::encode($model['EstadoGestion']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default"
                                                    data-modal="<?= Url::to(['estudios/modificar-objetivo',
                                                        'id' => $model['IdObjetivoEstudio'],
                                                        'idEstudio' => $estudio['IdEstudio']]) ?>"
                                                        title="Modificar">
                                                <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                            </button>
                                            <button class="btn btn-default"
                                                data-ajax="<?= Url::to(['estudios/borrar-objetivo',
                                                    'id' => $model['IdObjetivoEstudio']]) ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <strong>No hay objetivos que coincidan con el criterio de búsqueda</strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>