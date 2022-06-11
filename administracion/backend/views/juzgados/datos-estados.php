<?php
/* @var $this View */
/* @var $model Juzgados */

use common\models\Juzgados;
use common\models\GestorEstadoAmbitoGestion;
use yii\helpers\Html;
use yii\web\View;

$juzgado = new Juzgados;
$juzgado->IdJuzgado = $model->IdJuzgado;
$juzgado->Dame();
$estados = json_encode($juzgado->EstadoAmbitoGestion);
$listadoEstados = json_encode((new GestorEstadoAmbitoGestion)->Buscar());
$this->registerJs("Juzgados.Estados.init({$model->IdJuzgado}, {$estados}, {$listadoEstados})");
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $titulo ?></h4>
        </div>
        <div class="modal-body" id="Juzgados">
            <div id="errores-modal"> </div>
            <div class="box" v-if="mostrarHeader"> 
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-5" style="font-weight: 900">
                        Estados
                    </div>
                    <div class="col-md-2" style="font-weight: 900">
                        Orden
                    </div>
                    <div class="col-md-5" style="font-weight: 900">
                        Acciones
                    </div>
                </div>
            </div>
            <div class="box" v-for="(e, id) in arrayEstados"> 
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-5">
                        {{ e.EstadoAmbitoGestion }}
                    </div>
                    <div class="col-md-2">
                        {{ e.Orden }}
                    </div>
                    <div class="col-md-5">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-default"
                                    @click='quitarEstado(e.IdEstadoAmbitoGestion)'>
                                <i class="fa fa-minus"></i> Quitar
                            </button>
                        </div>
                        Cambiar Orden:
                        <select v-model="cambiarOrden[e.IdEstadoAmbitoGestion]" @change="editarOrden" style="width: 20%">
                            <option disabled value="">
                                Cambiar Orden
                            </option>
                            <option v-for="o in ordenOpcionesEditar(e.Orden)" :key="o" :value="o">
                                {{ o }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="col-md-12 form-group form-inline">
                    <div class="col-md-4">
                        <select v-model="selectEstado">
                            <option disabled value="">
                                Seleccione un estado
                            </option>
                            <option v-for="e in estadosOpciones" :key="e.IdEstadoAmbitoGestion" :value="e">
                                {{ e.EstadoAmbitoGestion }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select v-model="selectOrden">
                            <option disabled value="">
                                Seleccione un Orden
                            </option>
                            <option v-for="o in ordenOpciones" :key="o" :value="o">
                                {{ o }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-default"
                                @click='agregarEstado()'>
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

