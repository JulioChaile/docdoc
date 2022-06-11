<?php

use common\components\PermisosHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Roles de: '.$tipoCaso['TipoCaso'];
$this->params['breadcrumbs'] = [
    [
        'label' => 'Tipos de caso',
        'url' => ['/tipos-caso']
    ],
    $this->title
];
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php if (PermisosHelper::tienePermiso('AltaTipoCaso')): ?>
                    <button class="btn btn-primary pull-right" 
                            data-modal="<?= Url::to(['/tipos-caso/alta-rol', 'id' => $tipoCaso['IdTipoCaso']]) ?>">
                        <i class="fa fa-plus"></i> Nuevo Rol
                    </button>
                <?php endif; ?>
                <?php if (count($models) > 0): ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($models as $model): ?>
                        <tr>
                            <td><?= Html::encode($model['Rol']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <?php if (PermisosHelper::tienePermiso('ModificarTipoCaso')): ?>
                                    <button class="btn btn-default"
                                            data-modal="<?= Url::to(['tipos-caso/modificar-rol',
                                                'id' => $model['IdRTC']]) ?>">
                                        <i class="fa fa-pencil" style="color: dodgerblue"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php if (PermisosHelper::tienePermiso('BorrarTipoCaso')): ?>
                                    <button class="btn btn-default"
                                            data-ajax="<?= Url::to(['tipos-caso/borrar-rol',
                                                'id' => $model['IdRTC']]) ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p><strong>El tipo de caso no tiene roles asociados.</strong></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>