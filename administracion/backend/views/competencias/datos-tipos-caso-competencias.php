<?php
/* @var $this View */
/* @var $model Competencias */

use common\models\Competencias;
use common\models\GestorTiposCaso;
use yii\helpers\Html;
use yii\web\View;

$tiposCaso = json_encode($model->TiposCaso);
$listadoTipos = json_encode((new GestorTiposCaso)->Buscar(), true);
$this->registerJs("Competencias.TiposCaso.init({$model->IdCompetencia}, {$tiposCaso}, {$listadoTipos})");
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
        </div>
        <div class="modal-body" id="competencias">
            <div id="errores-modal"> </div>
            <div class="box" v-if="mostrarHeader"> 
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-6" style="font-weight: 900">
                        Tipo de Caso
                    </div>
                    <div class="col-md-3" style="font-weight: 900">
                        Estado
                    </div>
                    <div class="col-md-1" style="font-weight: 900">
                        Acciones
                    </div>
                </div>
            </div>
            <div class="box" v-for="(t, id) in tiposCaso"> 
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-6">
                        {{ t.TipoCaso }}
                    </div>
                    <div class="col-md-3">
                        {{ t.Estado }}
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-default"
                                @click='quitarTipoCaso(id)'>
                            <i class="fa fa-minus"></i> Quitar
                        </button>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-9">
                        <select v-model="selectTipoCaso">
                            <option disabled value="">
                                Seleccione un tipo de caso
                            </option>
                            <option v-for="t in tiposCasoOpciones" :key="t.IdTipoCaso" :value="t">
                                {{ t.TipoCaso }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-default"
                                @click='agregarTipoCaso()'>
                            <i class="fa fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
</div>

