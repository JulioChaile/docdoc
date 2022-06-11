<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\LinkPager;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $busqueda backend\models\forms\BusquedaForm */

$opciones = [
    'T' => 'Todos',
    'D' => 'Dni',
    'P' => 'Nombre y Apellido'
];

$this->title = 'Padron';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div id="errores"></div>
                <?php $form = ActiveForm::begin(['layout' => 'inline', 'method' => 'GET']); ?>

                <?= $form->field($busqueda, 'Combo')->dropDownList($opciones) ?> 
                
                <?= $form->field($busqueda, 'Cadena') ?> 

                <?= Html::submitButton('Buscar', ['class' => 'btn btn-default', 'name' => 'buscarusuario-button']) ?> 

                <?php ActiveForm::end(); ?>

                <br>
                <?php if (count($models) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr class="tabla-header">
                                <th>Persona</th>
                                <th>Dni</th>
                                <th>Domicilio</th>
                                <th>Circuito</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($models as $model): ?>
                            <tr>
                                <td><?= Html::encode($model['PERSONA']) ?></td>
                                <td><?= Html::encode($model['DNI']) ?></td>
                                <td><?= Html::encode($model['DOMICILIO']) ?></td>
                                <td><?= Html::encode($model['CIRCUITO']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="pull-right">
                        <?=
                        LinkPager::widget([
                            'pagination' => $paginado,
                            'firstPageLabel' => '<<',
                            'lastPageLabel' => '>> ',
                            'nextPageLabel' => '>',
                            'prevPageLabel' => '<'
                        ]);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>