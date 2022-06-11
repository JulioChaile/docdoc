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

$this->title = $estudio['Estudio'].' - Eventos';
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

                        <?php if (!empty($calendario)): ?>
                            <?php /*<button class="btn btn-primary pull-right" 
                                    data-modal="<?= Url::to(['/estudios/modificar-calendario', 'id' => $calendario['IdCalendario']]) ?>">
                                <i class="fa fa-plus"></i> Modificar calendario
                            </button>
                            */
                            ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr class="tabla-header">
                                        <th>Titulo</th>
                                        <th>Descripcion</th>
                                        <!--th>Acciones</th-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= Html::encode($calendario['Titulo']) ?></td>
                                        <td><?= Html::encode($calendario['Descripcion']) ?></td>
                                        <?php /*<td>
                                            <div class="btn-group">
                                                <button class="btn btn-default"
                                                        data-modal="<?= Url::to(['estudios/modificar-calendario',
                                                            'id' => $calendario['IdCalendario']]) ?>"
                                                            title="Modificar">
                                                    <i class="fa fa-users" style="color: dodgerblue"></i>
                                                </button>
                                                <button class="btn btn-default"
                                                        data-modal="<?= Url::to(['estudios/usuarios-calendario',
                                                            'id' => $calendario['IdCalendario']]) ?>"
                                                            title="Modificar">
                                                    <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                                </button>
                                                <button class="btn btn-default"
                                                    data-ajax="<?= Url::to(['estudios/borrar-calendario',
                                                        'id' => $calendario['IdCalendario']]) ?>">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                            */
                                            ?>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if (count($eventos) > 0): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr class="tabla-header">
                                            <th>Color en App</th>
                                            <th>Titulo</th>
                                            <th>Descripcion</th>
                                            <th>Comienzo</th>
                                            <th>Finalizacion</th>
                                            <!--th>Acciones</th-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($eventos as $model): ?>
                                        <tr>
                                            <td bgcolor=<?php echo $colores['event'][$model['IdColor']] ?> >****</td>
                                            <td><?= Html::encode($model['Titulo']) ?></td>
                                            <td><?= Html::encode($model['Descripcion']) ?></td>
                                            <td><?= Html::encode($model['Comienzo']) ?></td>
                                            <td><?= Html::encode($model['Fin']) ?></td>
                                            <?php /*<td>
                                                <div class="btn-group">
                                                    <button class="btn btn-default"
                                                            data-modal="<?= Url::to(['estudios/modificar-evento',
                                                                'id' => $model['IdEvento']]) ?>"
                                                                title="Modificar">
                                                        <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                                    </button>
                                                    <button class="btn btn-default"
                                                        data-ajax="<?= Url::to(['estudios/borrar-evento',
                                                            'id' => $model['IdEvento']]) ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            */
                                            ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <strong>No hay eventos agregados aun</strong>
                            <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-primary pull-center" 
                                data-modal="<?= Url::to(['/estudios/alta-calendario', 'id' => $estudio['IdEstudio']]) ?>">
                            <i class="fa fa-plus"></i> Crear calendario
                        </button>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>