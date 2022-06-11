<?php
/* @var $this View */
/* @var $form ActiveForm */
/* @var $model RolesTipoCaso */

use backend\assets\TiposCasoAsset;
use common\models\RolesTipoCaso;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

TiposCasoAsset::register($this);
$this->registerJs("TiposCaso.AltaRol.init({$model->Parametros})");
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'rolestipocaso-form',]) ?>
        <?= Html::activeHiddenInput($model, 'IdRTC') ?>
        <?= Html::activeHiddenInput($model, 'IdTipoCaso') ?>
        <div class="modal-body">
            <div id="errores-modal"> </div>
            <div class="col-md-6">    
                <?= $form->field($model, 'Rol') ?>
            </div>
            <div class="col-md-6">
                <button class="btn btn-default pull-right" style="margin-top: 1.7em"
                        type="button"
                        @click="agregarParametro()">
                    <i class="fa fa-plus"></i> Agregar Parámetro
                </button>
            </div>
            <div class="col-md-12" v-if='ArrayParametros.length > 0'>
                <h3>Parámetros del Rol</h3>
            </div>
            <div class="box" v-for="(p,k) in ArrayParametros"> 
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-3">
                        <label for="Parametro">Parámetro</label>
                        <input class="form-control" type="text" required
                               v-bind:name="'RolesTipoCaso[Parametros]['+k+'][Parametro]'"
                               v-bind:id="Parametro" v-model="p.Parametro">
                    </div>
                    <div class="col-md-3">
                        <label for="Descripcion">Descripción</label>
                        <input class="form-control" type="text" required
                               v-bind:name="'RolesTipoCaso[Parametros]['+k+'][Descripcion]'"
                               v-model="p.Descripcion" v-bind:id="Descripcion"/>
                    </div>
                    <div class="col-md-3">
                        <label for="TipoDato">Tipo de Dato</label>
                        <select class="form-control" v-bind:name="'RolesTipoCaso[Parametros]['+k+'][TipoDato]'"
                                v-bind:id="TipoDato" v-model="p.TipoDato"
                                required>
                            <option value="" disabled selected>Seleccione opción</option>
                            <option value="string">Texto</option>
                            <option value="integer">Número</option>
                            <option value="boolean">Booleano (Si/No)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="Obligatorio">Obligatorio</label>
                        <input style="padding-top: -0.5em"
                               v-bind:name="'RolesTipoCaso[Parametros]['+k+'][Obligatorio]'"
                               type="checkbox" v-bind:id="Obligatorio" v-model="p.Obligatorio">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-default"
                                @click='quitarParametro(k)'>
                            <i class="fa fa-minus"></i> Quitar
                        </button>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary',]) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

