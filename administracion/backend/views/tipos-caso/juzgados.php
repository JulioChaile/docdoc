<?php
/* @var $this View */
/* @var $model TipoCaso */

use backend\assets\TiposCasoAsset;
use common\models\TiposCaso;
use common\models\GestorJuzgados;
use common\models\GestorCompetencias;
use yii\helpers\Html;
use yii\web\View;

TiposCasoAsset::register($this);


$listadoJuzgados = json_encode($model->Juzgados);
$juzgados = json_encode((new GestorJuzgados)->Buscar(), true);
$listadoCompetencias = json_encode((new GestorCompetencias)->Buscar(), true);
// selectCompetencias = competencias que contengan el IdTipoCaso indicado
$this->registerJs("TiposCaso.Juzgados.init({$model->IdTipoCaso}, {$juzgados}, {$listadoJuzgados}, {$listadoCompetencias})");
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
        </div>
        <div class="modal-body" id="juzgados">
            <div id="errores-modal"> </div>
            <div class="box" v-if="mostrarHeader"> 
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-3" style="font-weight: 900">
                        Juzgado
                    </div>
                    <div class="col-md-1" style="font-weight: 900">
                        Estado Juzgado
                    </div>
                    <div class="col-md-1" style="font-weight: 900">
                        Modo de Gestion
                    </div>
                    <div class="col-md-3" style="font-weight: 900">
                        Competencia
                    </div>
                    <div class="col-md-1" style="font-weight: 900">
                        Estado Competencia
                    </div>
                    <div class="col-md-1" style="font-weight: 900">
                        Acciones
                    </div>
                </div>
            </div>
            <div class="box" v-for="(j, ix) in listadoJuzgados"> 
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-3">
                        {{ j.Juzgado }}
                    </div>
                    <div class="col-md-1">
                        {{ j.EstadoJuzgado }}
                    </div>
                    <div class="col-md-1">
                        {{ j.ModoGestion }}
                    </div>
                    <div class="col-md-3">
                        {{ j.Competencia }}
                    </div>
                    <div class="col-md-1">
                        {{ j.EstadoCompetencia }}
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-default"
                                @click='quitarJuzgado(ix, j.IdJuzgado, j.IdCompetencia)'>
                            <i class="fa fa-minus"></i> Quitar
                        </button>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-5">
                        <select v-model="selectCompetencias">
                            <option disabled value="">
                                Seleccione una competencia
                            </option>
                            <option v-for="c in competenciasOpciones" :key="c.IdCompetencia" :value="c">
                                {{ c.Competencia }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select v-model="selectJuzgados">
                            <option disabled value="">
                                Seleccione un juzgado
                            </option>
                            <option v-for="j in juzgadosOpciones" :key="j.IdJuzgado" :value="j">
                                {{ j.Juzgado }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-default"
                                @click='agregarJuzgado()'>
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

