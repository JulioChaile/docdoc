<template>
  <div class="row container">
    <!-- Botones y modales -->
    <div class="col-12" style="display:flex; justify-content:flex-end;">
      <div v-if="!modoEdicion">
        <!-- Editar -->
        <!--q-icon right color="primary" class="icono_editar" name="fas fa-edit" size="20px" @click="habilitarEdicion()">
          <q-tooltip>Editar datos</q-tooltip>
        </q-icon-->
        <!-- Compartir -->
        <!--q-icon name="share" color="primary" class="icono_compartir" right size="20px" @click="compartir=true">
          <q-tooltip>Compartir</q-tooltip>
        </q-icon-->
      <!-- Modal Comparticiones -->
      <q-dialog v-model="modal.comparticiones">
        <Comparticiones
          v-if="datos.Comparticiones"
          :comparticiones="datos.Comparticiones"
          :IdCaso="datos.IdCaso"
          @cerrar="modal.comparticiones = false"
          @eliminarComparticiones="eliminarComparticiones"
        />
        <q-card v-else class="q-pa-lg">
          Sin Comparticiones
        </q-card>
      </q-dialog>
        <q-dialog v-model="modal.compartir" style="width: auto; height:auto;">
          <CompartirCaso
            :caso="datos"
            @compartir="modal.compartir = false"
          />
        </q-dialog>
        <!-- Objetivos -->
        <!--q-icon name="timeline" color="primary" class="icono_objetivos" right size="20px" @click="verObjetivos=true">
          <q-tooltip>Ver Objetivos</q-tooltip>
        </q-icon-->
        <q-dialog v-model="modal.verObjetivos" style="width: auto; height:auto;">
          <ObjetivosCaso
            :IdCaso="datos.IdCaso"
            @cerrar="modal.verObjetivos = false"
          />
        </q-dialog>
        <!-- Movimientos -->
        <!--q-icon name="assignment" color="primary" class="icono_movimientos" right size="20px" @click="verMovimientos=true">
          <q-tooltip>Ver Movimientos</q-tooltip>
        </q-icon-->
        <q-dialog v-model="modal.verMovimientos" style="width: auto; height:auto;">
          <MovimientosCaso
            :IdCaso="datos.IdCaso"
            @cerrar="modal.verMovimientos = false"
          />
        </q-dialog>
        <!-- Archivar -->
        <!--q-icon name="archive" color="primary" class="icono_archivar" right size="20px" @click="archivar=true">
          <q-tooltip>Archivar Caso</q-tooltip>
        </q-icon-->
        <q-dialog v-model="modal.archivar" style="width: auto; height:auto;">
          <ArchivarCaso
            :IdCaso="datos.IdCaso"
            @archivado="archivarCaso"
            @cerrar="modal.archivar = false"
          />
        </q-dialog>
        <!-- Eliminar -->
        <!--q-icon name="delete" color="primary" class="icono_eliminar" right size="20px" @click="eliminar=true">
          <q-tooltip>Eliminar Caso</q-tooltip>
        </q-icon-->
        <q-dialog v-model="modal.eliminar" style="width: auto; height:auto;">
          <EliminarCaso
            :IdCaso="datos.IdCaso"
            @eliminado="eliminarCaso()"
            @cerrar="modal.eliminar = false"
          />
        </q-dialog>
        <q-dialog v-model="modal.duplicar" style="width: auto; height:auto;">
          <q-card style="padding:1rem;">
            <span class="text-h6">Duplicar Caso</span>
            <span>
                <p>
                ¿Está seguro que desea duplicar este caso?
                </p>
            </span>

            <div style="float:right;">
                <q-btn color="primary" label="Duplicar" @click="duplicarCaso()" />
                <q-btn flat label="Cancelar" @click="modal.duplicar = false" />
            </div>
          </q-card>
        </q-dialog>
        <q-dialog v-model="modalHistorialEstados" style="width: auto; height:auto;">
          <q-card style="padding:1rem;">
            <div v-if="loadingHE" class="full-width q-pa-lg">
              <Loading />
            </div>
            <div v-else class="full-width q-pa-lg">
              <ul>
                <li
                  v-for="(e, i) in HistorialEstados"
                  :key="e.IdEstadoAmbitoGestion"
                >
                  {{ e.EstadoAmbitoGestion }} - {{ diasEstado(i - 1, i) }}
                </li>
              </ul>
            </div>
          </q-card>
        </q-dialog>
      </div>
      <div
        v-else
        class="q-mt-sm"
      >
        <q-btn flat color="" class="q-mr-sm">
          <span class="text-capitalize" @click="cancelarEdicion()">
            Cancelar
          </span>
        </q-btn>
        <q-btn color="secondary" :loading="loading" @click="guardarEdicion()">
          <span class="text-capitalize">
            Guardar
          </span>
        </q-btn>
      </div>
    </div>
    <div class="col-12">
      <!-- Datos del caso como archivo -->
      <!--div v-if="!modoEdicion" class="row">
        <div class="col-12 col-sm-6">
          <data-item
            :text="caratulaVer()"
            text-class="text-weight-medium text-h4"
            label="Caratula"
            labelColor="accent"
          />
          <q-tooltip>{{ datos.Caratula }}</q-tooltip>
        </div>
        <q-space></q-space>
        <div class="col-12 col-sm-6 contenedor_badges">
          <div class="row q-pt-md">
            <div class="col-6 col-sm-12 col-lg-10 estado">
              <q-badge :color="colorEstado()" class="text-uppercase text-body2 q-px-xl badge">{{ estado() }}</q-badge>
            </div>
            <div class="col-6 col-sm-12 col-lg-2 expediente">
              <q-badge
                color="black"
                outline
                class="text-uppercase text-body2 q-px-lg badge"
              >
                Nro: {{ datos.NroExpediente ? datos.NroExpediente : '--' }}
              </q-badge>
            </div>
          </div>
        </div>
      </div-->
      <div
        v-if="!modoEdicion"
        class="avenir-next--bold col-12 column q-mt-lg"
      >
        <div class="row">
          <img class="q-mr-lg rounded-borders" :src="'https://io.docdoc.com.ar/api/multimedia?file=' + (datos.FotoCaso || 'bW992DkMPjuvd5v6EyGfENWDLLwumLbL.jpg')" width="75px" height="75px" />
          <div class="--bold" style="color: #333333;">
            <span class="relative-position q-pr-xs">
              <q-separator
                class="separador_vista_caso"
              />
              {{ datos.Defiende === 'A' ? 'Actor' : (datos.Defiende === 'D' ? 'Demandado' : '-') }}
            </span>
            <span class="relative-position q-pl-xs">
              {{ datos.TipoCaso || 'Sin tipo de caso' }}
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
                Tipo de Caso
              </q-tooltip>
            </span>
            <div
              class="text-h4 --bold"
            >
              {{ caratulaVer() }}
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
                {{ datos.Caratula }}
              </q-tooltip>
            </div>
            <div>
              <span class="relative-position q-pr-sm cursor-pointer" @click="verHistorialEstados">
                {{ datos.EstadoAmbitoGestion || 'Sin estado' }}
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
                  Estado de Proceso - Ver Historial
                </q-tooltip>
              </span>
              <span class="relative-position q-pl-sm">
                {{ datos.Juzgado || 'Sin Tipo de Proceso' }}
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
                  Tipo de Proceso
                </q-tooltip>
              </span>
            </div>
          </div>
        </div>
      </div>
      <!-- Edicion de los datos del caso -->
      <div v-else class="row">
        <div class="col-12 col-sm-6">
          <!--<editable-input :text="datos.Caratula ? datos.Caratula : ''" label="Caratula" labelColor="secondary" @modifico="modificoCaratula" />-->
          <div class="row main_container q-pa-sm">
            <div class="col-8">
              <span class='text-weight-bolder text-body2 text-secondary'>
                Carátula
              </span>
            </div>
            <div class="col-4">
              <span class='text-weight-bolder text-body2 text-secondary'>
                Nro Expediente
              </span>
            </div>
            <div class="col-8">
              <q-input v-model="caratulaEditable" outlined dense />
            </div>
            <div class="col-4">
              <q-input v-model="nroExpedienteEditable" outlined dense />
            </div>
            <div class="col-6 q-my-sm">
              <div class='text-weight-bolder text-body2 text-secondary'>
                Defiende:
              </div>
              <q-radio v-model="defiende" val="A" label="Actor" />
              <q-radio v-model="defiende" val="D" label="Demandado" />
            </div>
          </div>
        </div>
        <div v-if="!datos.IdCasoEstudio" class="col-12 col-sm-6 q-pa-sm">
          <span class='text-weight-bolder text-body2 text-secondary'>
            ID
          </span>
          <div class="col-4">
            <q-input
              v-model="idEditable"
              outlined
              dense
              mask='##############'
            />
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="row">
            <!-- Fechas -->
            <!--div class="col-12 col-sm-5">
              <div class="row q-pl-sm">
                <div class="col-12">
                  <div class="contenedor_fecha">
                    <span class="text-body2 text-weight-light">
                      Creado <span v-if="casoViejo" class="text-weight-light">hace </span><span class="text-body1 text-weight-bold">{{ diasDesdeCreacion() }}
                        <q-tooltip content-class="bg-teal" anchor="center right" self="center left" :offset="[10, 10]">
                          <span class="text-body2">
                            {{ datos.FechaAlta ? datos.FechaAlta.slice(0, 10) : 'Sin fecha' }}
                          </span>
                        </q-tooltip>
                      </span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <q-space></q-space>
            <div v-if="!modoEdicion" class="col-12 col-sm-6">
              <div class="row">
                <div class="col-6 q-pl-sm">
                  <data-item
                    :text="datos.Origen ? datos.Origen : 'Sin origen'"
                    text-class="text-weight-light text-h6"
                    label="Origen"
                    labelColor="accent"
                    no-gutters
                  />
                </div>
                <div class="col-6 q-pl-sm">
                  <data-item
                    :text="datos.Nominacion ? datos.Nominacion : 'Sin nominación'"
                    text-class="text-weight-light text-h6"
                    label="Nominación"
                    labelColor="accent"
                    no-gutters
                  />
                </div>
              </div>
            </div-->
            <div v-if="modoEdicion" class="col-12 col-sm-6">
              <div class="row items-end">
                <div class="col-6">
                  <editable-select :opcionesProp="opcionesOrigenes" label="Origen" labelColor="secondary" :valor="datosEditar.Origen" @selecciono="seleccionoOrigen" />
                </div>
                <div class="col-6">
                  <editable-select :opcionesProp="opcionesNominaciones" label="Nominación" :valor="datosEditar.Nominacion" @selecciono="seleccionoNominacion" labelColor="secondary" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Datos del ambito -->
      <!--div v-if="!modoEdicion" class="row q-pt-sm">
        <-- Competencia -->
        <!--div class="col-6 col-sm-3 col-md-6 col-lg-3">
          <data-item
            :text="datos.Competencia ? datos.Competencia : 'Sin competencia'"
            text-class="text-weight-light text-h6"
            label="Competencia"
            labelColor="accent"
          />
        </div>
        <-- Tipo de caso -->
        <!--div class="col-6 col-sm-3 col-md-6 col-lg-3">
          <data-item
            :text="datos.TipoCaso ? datos.TipoCaso : 'Sin tipo de caso'"
            text-class="text-weight-light text-h6"
            label="Tipo de Caso"
            labelColor="accent"
          />
        </div>
        <-- Ambito de Gestion -->
        <!--div class="col-6 col-sm-3 col-md-6 col-lg-3">
          <data-item
            :text="datos.Juzgado ? datos.Juzgado : 'Sin ámbito de gestión'"
            text-class="text-weight-light text-h6"
            label="Ámbito de Gestión"
            labelColor="accent"
          />
        </div>
        <-- Nominación -->
        <!--div class="col-6 col-sm-3 col-md-6 col-lg-3">
          <data-item
            :text="datos.EstadoAmbitoGestion ? datos.EstadoAmbitoGestion : 'Sin estado'"
            text-class="text-weight-light text-h6"
            label="Estado de Ámbito de Gestión"
            labelColor="accent"
          />
        </div>
      </div-->
      <!-- Edicion de los datos del ambito -->
      <div v-if="modoEdicion" class="row">
        <!-- Competencia -->
        <div class="col-6 col-sm-3 col-md-6 col-lg-3">
          <editable-select :opcionesProp="opcionesCompetencias" label="Competencia" :valor="datosEditar.Competencia" labelColor="secondary" @selecciono="seleccionoCompetencia" />
        </div>
        <!-- Tipo de caso -->
        <div class="col-6 col-sm-3 col-md-6 col-lg-3">
          <editable-select :opcionesProp="opcionesTiposCaso" label="Tipo de caso" :valor="datosEditar.TipoCaso" @selecciono="seleccionoTipoCaso" labelColor="secondary" />
        </div>
        <!-- Ambito de Gestion -->
        <div class="col-6 col-sm-3 col-md-6 col-lg-3">
          <editable-select :opcionesProp="opcionesAmbitosGestion" label="Tipo de Proceso" :valor="datosEditar.Juzgado" @selecciono="seleccionoAmbitoGestion" labelColor="secondary" />
        </div>
        <!-- Nominación -->
        <div class="col-6 col-sm-3 col-md-6 col-lg-3">
          <editable-select :opcionesProp="opcionesEstadoAmbitoGestion" label="Estado de Proceso" :valor="datosEditar.EstadoAmbitoGestion" @selecciono="seleccionoEstadoAmbitoGestion" labelColor="secondary" />
        </div>
        <div class="col-12 q-pl-sm">
          <q-input label="Fecha Estado de Proceso" v-model="FechaEstado" mask="##-##-####" style="width: 100%;">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                    <q-date
                      v-model="FechaEstado"
                      mask="DD-MM-YYYY"
                      label="Fecha Estado de Proceso"
                      @input="() => $refs.qDateProxy.hide()"
                    />
                  </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import request from '../../request'
import { Notify, QRadio } from 'quasar'
import moment from 'moment'
import Loading from '../Loading'
import DataItem from '../Compartidos/DataItem'
import EditableInput from '../Compartidos/EditableInput'
import EditableSelect from '../Compartidos/EditableSelect'
import CompartirCaso from '../Caso/CompartirCaso'
import Comparticiones from '../GrillaCasos/Comparticiones'
import ObjetivosCaso from '../Caso/ObjetivosCaso'
import MovimientosCaso from '../Caso/MovimientosCaso'
import ArchivarCaso from '../Caso/ArchivarCaso'
import EliminarCaso from '../Caso/EliminarCaso'
export default {
  name: 'Datos',
  props: {
    datos: {
      type: Object,
      default: () => {}
    },
    modal: {
      type: Object,
      default: () => {}
    },
    editar: {
      type: Boolean,
      default: false
    }
  },
  components: {
    QRadio,
    DataItem,
    EditableInput,
    EditableSelect,
    CompartirCaso,
    ObjetivosCaso,
    MovimientosCaso,
    ArchivarCaso,
    EliminarCaso,
    Loading,
    Comparticiones
  },
  data () {
    return {
      modoEdicion: false,
      badgeEstado: {
        A: {
          color: 'positive',
          label: 'ACTIVO'
        },
        P: {
          color: 'negative',
          label: 'PENDIENTE'
        },
        R: {
          color: 'brown',
          label: 'ARCHIVADO'
        },
        E: {
          color: 'brown',
          label: 'ARCHIVO EX-ACTIVO'
        },
        F: {
          color: 'primary',
          label: 'FINALIZADO'
        }
      },
      origenes: [],
      nominaciones: [],
      competencias: [],
      tiposCaso: [],
      ambitosGestion: [],
      estadosAmbitoGestion: [],
      caratulaEditable: '',
      idEditable: '',
      defiende: '',
      DataOpcionesOrigenes: [],
      DataOpcionesNominaciones: [],
      DataOpcionesCompetencias: [],
      DataOpcionesTiposCaso: [],
      DataOpcionesAmbitosGestion: [],
      DataOpcionesEstadoAmbitoGestion: [],
      mensajes: [],
      loading: false,
      compartir: false,
      archivar: false,
      eliminar: false,
      opcionesDummy: ['a', 'b'],
      casoViejo: false,
      origenDefault: {},
      datosEditar: {},
      checkEdicion: false,
      verObjetivos: false,
      verMovimientos: false,
      FechaEstado: '',
      modalHistorialEstados: false,
      HistorialEstados: [],
      loadingHE: false,

      // valores elegidos en la edicion:
      origenSeleccionado: '',
      idOrigenSeleccionado: '',

      nominacionSeleccionada: '',
      idNominacionSeleccionada: '',

      competenciaSeleccionada: '',
      idCompetenciaSeleccionada: '',

      tipoCasoSeleccionado: '',
      idTipoCasoSeleccionado: '',

      ambitoGestionSeleccionado: '',
      idAmbitoGestionSeleccionado: '',

      estadoAmbitoGestionSeleccionado: '',
      idEstadoAmbitoGestionSeleccionado: ''
    }
  },
  watch: {
    'editar' () {
      if (this.editar) {
        this.habilitarEdicion()
      }
    }
  },
  computed: {
    opcionesOrigenes () {
      return this.DataOpcionesOrigenes
    },
    opcionesNominaciones () {
      return this.DataOpcionesNominaciones
    },
    opcionesCompetencias () {
      return this.DataOpcionesCompetencias
    },
    opcionesTiposCaso () {
      return this.DataOpcionesTiposCaso
    },
    opcionesAmbitosGestion () {
      return this.DataOpcionesAmbitosGestion
    },
    opcionesEstadoAmbitoGestion () {
      return this.DataOpcionesEstadoAmbitoGestion
    }
  },
  methods: {
    diasEstado (i, j) {
      const FechaFin = i === -1 ? moment().format('YYYY-MM-DD') : this.HistorialEstados[i].FechaEstado
      const FechaInicio = this.HistorialEstados[j].FechaEstado

      return moment(FechaFin).diff(FechaInicio, 'days') + ' días'
    },
    verHistorialEstados () {
      this.modalHistorialEstados = true
      this.loadingHE = true

      request.Get('/casos/historial-estados', { IdCaso: this.datos.IdCaso }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.HistorialEstados = r
          this.loadingHE = false
        }
      })
    },
    duplicarCaso () {
      request.Post('/casos/duplicar-caso', { IdCaso: this.datos.IdCaso }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.modal.duplicar = false

          let routeData = this.$router.resolve({ path: `/Caso?id=${r.IdCaso}` })
          window.open(routeData.href, '_blank')
        }
      })
    },
    habilitarEdicion () {
      this.modoEdicion = true
      this.checkEdicion = true
      this.caratulaEditable = this.datos.Caratula
      this.nroExpedienteEditable = this.datos.NroExpediente
      this.idEditable = this.datos.IdCasoEstudio
      this.defiende = this.datos.Defiende || 'A'

      this.datosEditar.Origen = this.datos.Origen
      this.datosEditar.Competencia = this.datos.Competencia
      this.datosEditar.TipoCaso = this.datos.TipoCaso
      this.datosEditar.Juzgado = this.datos.Juzgado
      this.datosEditar.Nominacion = this.datos.Nominacion
      this.datosEditar.EstadoAmbitoGestion = this.datos.EstadoAmbitoGestion
      this.FechaEstado = this.datos.FechaEstado.split(' ')[0].split('-').reverse().join('-')

      // lleno todas las opciones
      this.traerCompetencias()
      this.traerOrigenes()
      this.traerAmbitosGestion(this.datos.IdTipoCaso)
      if (this.datos.IdJuzgado) this.traerEstadosAmbitoGestion(this.datos.IdJuzgado)
      if (this.datos.IdJuzgado) this.traerNominaciones(this.datos.IdJuzgado)

      this.origenSeleccionado = this.datos.Origen ? this.datos.Origen : this.origenDefault.Origen
      this.idOrigenSeleccionado = this.datos.IdOrigen ? this.datos.IdOrigen : this.origenDefault.IdOrigen
      this.nominacionSeleccionada = this.datos.Nominacion
      this.idNominacionSeleccionada = this.datos.IdNominacion
      this.competenciaSeleccionada = this.datos.Competencia
      this.idCompetenciaSeleccionada = this.datos.IdCompetencia
      this.tipoCasoSeleccionado = this.datos.TipoCaso
      this.idTipoCasoSeleccionado = this.datos.IdTipoCaso
      this.ambitoGestionSeleccionado = this.datos.Juzgado
      this.idAmbitoGestionSeleccionado = this.datos.IdJuzgado
      this.estadoAmbitoGestionSeleccionado = this.datos.EstadoAmbitoGestion
      this.idEstadoAmbitoGestionSeleccionado = this.datos.IdEstadoAmbitoGestion
    },
    traerCompetencias () {
      request.Get('/competencias', {}, (r) => {
        if (!r.Error) {
          this.competencias = r
          r.forEach(competencia => {
            this.DataOpcionesCompetencias.push(competencia.Competencia)
          })
          const CompetenciaObject = this.competencias.filter(item => item.Competencia === this.datos.Competencia)[0]
          for (var key in CompetenciaObject.TiposCaso) {
            this.DataOpcionesTiposCaso.push(CompetenciaObject.TiposCaso[key].TipoCaso) // voy insertando los tipos caso en el array de las opciones para la edicion posterior
          }
          // Guardo los tipos caso de la competencia seleccionada para utilizarlos luego
          Object.entries(CompetenciaObject.TiposCaso).forEach(
            ([IdTipoCaso, TipoCaso]) => {
              if (TipoCaso.Estado === 'A') {
                let tipocaso = {
                  IdTipoCaso: IdTipoCaso,
                  TipoCaso: TipoCaso.TipoCaso
                }
                this.tiposCaso.push(tipocaso)
              }
            }
          )
        } else {
          Notify.create(r.Error)
        }
      })
    },
    traerOrigenes () {
      request.Get('/origenes', {}, (r) => {
        if (!r.Error) {
          this.origenes = r
          r.forEach(item => {
            this.opcionesOrigenes.push(item.Origen)
          })
          this.origenDefault = this.origenes[0]
        } else {
          Notify.create(r.Error)
        }
      })
    },
    traerAmbitosGestion (id) {
      request.Get(`/tipos-caso/${id}/juzgados`, {}, r => {
        if (!r.Error) {
          this.ambitosGestion = r.Juzgados
          r.Juzgados.forEach(item => {
            this.DataOpcionesAmbitosGestion.push(item.Juzgado)
          })
        } else {
          console.log('hubo error al traer ambitos de gestion')
        }
      })
    },
    traerEstadosAmbitoGestion (id) {
      request.Get(`/estado-ambito-gestion/estados-juzgado?id=${id}`, {}, r => {
        if (!r.Error) {
          this.estadosAmbitoGestion.splice(0)
          this.mensajes.splice(0)
          if (r.EstadoAmbitoGestion) {
            Object.entries(r.EstadoAmbitoGestion).forEach(
              ([orden, estadoambitogestion, idestado]) => {
                let e = {
                  Orden: estadoambitogestion.Orden,
                  EstadoAmbitoGestion: estadoambitogestion.EstadoAmbitoGestion,
                  IdEstadoAmbitoGestion: estadoambitogestion.IdEstadoAmbitoGestion
                }
                let m = {
                  IdEstadoAmbitoGestion: estadoambitogestion.IdEstadoAmbitoGestion,
                  Mensaje: estadoambitogestion.Mensaje
                }
                this.estadosAmbitoGestion.push(e)
                this.mensajes.push(m)
              }
            )
            this.estadosAmbitoGestion.sort((a, b) => {
              if (a.Orden > b.Orden) {
                return 1
              }
              if (a.Orden < b.Orden) {
                return -1
              }
              if (a.Orden === b.Orden) {
                return 0
              }
            })
            this.DataOpcionesEstadoAmbitoGestion.splice(0)
            this.estadosAmbitoGestion.forEach(item => {
              this.DataOpcionesEstadoAmbitoGestion.push(item.EstadoAmbitoGestion)
            })

            if (!this.checkEdicion) { this.datosEditar.EstadoAmbitoGestion = this.estadosAmbitoGestion[0].EstadoAmbitoGestion }
            this.seleccionoEstadoAmbitoGestion(this.datosEditar.EstadoAmbitoGestion)
          } else {
            let e = {
              Orden: 1,
              EstadoAmbitoGestion: 'Sin estado',
              IdEstadoAmbitoGestion: 0
            }
            let m = {
              IdEstadoAmbitoGestion: 0,
              Mensaje: ''
            }
            this.estadosAmbitoGestion.push(e)
            this.mensajes.push(m)
            this.DataOpcionesEstadoAmbitoGestion.splice(0)
            this.estadosAmbitoGestion.forEach(item => {
              this.DataOpcionesEstadoAmbitoGestion.push(item.EstadoAmbitoGestion)
            })

            if (!this.checkEdicion) { this.datosEditar.EstadoAmbitoGestion = this.estadosAmbitoGestion[0].EstadoAmbitoGestion }
            this.seleccionoEstadoAmbitoGestion(this.datosEditar.EstadoAmbitoGestion)
          }
        } else {
          Notify.create(r.Error)
        }
      })
    },
    eliminarComparticiones (IdsEstudios, IdCaso) {
      const i = this.casos.findIndex(c => c.IdCaso === IdCaso)

      if (i > 0) {
        let indexes = []

        IdsEstudios.forEach(r => {
          const index = this.datos.Comparticiones.EstudiosDestino.findIndex(e => e.IdEstudio === r)

          if (index > 0) { indexes.push(index) }
        })

        if (indexes.length > 0) {
          indexes.forEach(r => {
            this.datos.Comparticiones.EstudiosDestino.splice(r, 0)
          })
        }
      }
    },
    traerNominaciones (id) {
      request.Get(`/nominaciones?IdsJuzgado=${JSON.stringify([id])}&IncluyeBajas=N`, {}, r => {
        if (!r.Error) {
          this.nominaciones.splice(0)
          for (var item in r) {
            r[item].forEach(nom => {
              this.DataOpcionesNominaciones.push(nom.Nominacion)
              this.nominaciones.push(nom)
            })
          }
          if (this.nominaciones.length === 0) {
            this.DataOpcionesNominaciones.push('Sin Nom')
            if (!this.checkEdicion) { this.datosEditar.Nominacion = 'Sin nom' }
          } else {
            if (!this.checkEdicion) {
              if (this.nominaciones.filter(n => n.Nominacion === this.datosEditar.Nominacion).length === 0) {
                if (this.nominaciones.findIndex(n => n.Nominacion[0] === this.datosEditar.Nominacion[0]) >= 0) {
                  const i = this.nominaciones.findIndex(n => n.Nominacion[0] === this.datosEditar.Nominacion[0])
                  this.datosEditar.Nominacion = this.nominaciones[i].Nominacion
                } else {
                  this.datosEditar.Nominacion = this.nominaciones[0].Nominacion
                }
              }
            }
          }
          this.seleccionoNominacion(this.datosEditar.Nominacion)
        } else {
          Notify.create(r.Error)
        }

        setTimeout(() => { this.checkEdicion = false }, 500)
      })
    },

    seleccionoOrigen (origen) {
      const id = this.origenes.filter(item => item.Origen === origen)[0].IdOrigen
      this.origenSeleccionado = origen
      this.idOrigenSeleccionado = id
    },
    seleccionoCompetencia (competencia) {
      // Dejo vacios los datos seleccionados para volver a llenarlos
      this.tipoCasoSeleccionado = ''
      this.idTipoCasoSeleccionado = ''

      this.ambitoGestionSeleccionado = ''
      this.idAmbitoGestionSeleccionado = ''

      this.estadoAmbitoGestionSeleccionado = ''
      this.idEstadoAmbitoGestionSeleccionado = ''

      this.nominacionSeleccionada = ''
      this.idNominacionSeleccionada = ''

      // Busco el id correspondiente a la competencia que eligió el usuario
      const id = this.competencias.filter(item => item.Competencia === competencia)[0].IdCompetencia

      // Armo el objeto Competencia con la Competencia seleccionada por el usuario
      const CompetenciaObject = this.competencias.filter(item => item.Competencia === competencia)[0]
      // Guardo los tipos caso de la competencia seleccionada para utilizarlos luego
      this.tiposCaso.splice(0)
      Object.entries(CompetenciaObject.TiposCaso).forEach(
        ([IdTipoCaso, TipoCaso]) => {
          if (TipoCaso.Estado === 'A') {
            let tipocaso = {
              IdTipoCaso: IdTipoCaso,
              TipoCaso: TipoCaso.TipoCaso
            }
            this.tiposCaso.push(tipocaso)
          }
        }
      )

      // Recorro la propiedad TiposCaso de ese objeto Competencia (TiposCaso tiene los tipos casos relacionados a esa competencia)
      this.DataOpcionesTiposCaso.splice(0)
      for (var key in CompetenciaObject.TiposCaso) {
        this.DataOpcionesTiposCaso.push(CompetenciaObject.TiposCaso[key].TipoCaso) // voy insertando los tipos caso en el array de las opciones para la edicion posterior
      }
      this.competenciaSeleccionada = competencia
      this.idCompetenciaSeleccionada = id

      this.datosEditar.TipoCaso = this.tiposCaso[0].TipoCaso
      this.seleccionoTipoCaso(this.tiposCaso[0].TipoCaso)
    },
    seleccionoTipoCaso (tipoCaso) {
      // Dejo vacios los datos seleccionados para volver a llenarlos
      this.ambitoGestionSeleccionado = ''
      this.idAmbitoGestionSeleccionado = ''

      this.estadoAmbitoGestionSeleccionado = ''
      this.idEstadoAmbitoGestionSeleccionado = ''

      this.nominacionSeleccionada = ''
      this.idNominacionSeleccionada = ''

      const id = this.tiposCaso.filter(item => item.TipoCaso === tipoCaso)[0].IdTipoCaso
      this.DataOpcionesAmbitosGestion.splice(0)
      this.traerAmbitosGestion(id)
      this.tipoCasoSeleccionado = tipoCaso
      this.idTipoCasoSeleccionado = id

      if (this.ambitosGestion.filter(a => a.Juzgado === this.datosEditar.Juzgado).length === 0) {
        this.datosEditar.Juzgado = this.ambitosGestion[0].Juzgado
      }
      this.seleccionoAmbitoGestion(this.datosEditar.Juzgado)
    },
    seleccionoAmbitoGestion (ambitoGestion) {
      this.datosEditar.Juzgado = ambitoGestion

      // Dejo vacios los datos seleccionados para volver a llenarlos
      this.estadoAmbitoGestionSeleccionado = ''
      this.idEstadoAmbitoGestionSeleccionado = ''

      this.nominacionSeleccionada = ''
      this.idNominacionSeleccionada = ''

      // Busco estados ambito de gestion
      this.ambitoGestionSeleccionado = ambitoGestion
      this.idAmbitoGestionSeleccionado = this.ambitosGestion.filter(item => item.Juzgado === ambitoGestion)[0].IdJuzgado

      this.DataOpcionesEstadoAmbitoGestion.splice(0)
      this.traerEstadosAmbitoGestion(this.idAmbitoGestionSeleccionado)

      // Traigo nominaciones
      this.DataOpcionesNominaciones.splice(0)
      this.traerNominaciones(this.idAmbitoGestionSeleccionado)
    },
    seleccionoEstadoAmbitoGestion (estadoAmbitoGestion) {
      const id = this.estadosAmbitoGestion.filter(item => item.EstadoAmbitoGestion === estadoAmbitoGestion)[0].IdEstadoAmbitoGestion
      this.idEstadoAmbitoGestionSeleccionado = id
      this.estadoAmbitoGestionSeleccionado = estadoAmbitoGestion
      if (this.estadoAmbitoGestionSeleccionado === this.datos.EstadoAmbitoGestion) {
        this.FechaEstado = this.datos.FechaEstado.split(' ')[0].split('-').reverse().join('-')
      } else {
        this.FechaEstado = moment().format('DD-MM-YYYY')
      }
    },
    seleccionoNominacion (nominacion) {
      const id = nominacion !== 'Sin nom' ? this.nominaciones.filter(item => item.Nominacion === nominacion)[0].IdNominacion : 0
      this.idNominacionSeleccionada = id
      this.nominacionSeleccionada = nominacion
    },
    guardarEdicion () {
      if (this.caratulaEditable === '' || this.competenciaSeleccionada === '' || this.tipoCasoSeleccionado === '' || this.ambitoGestionSeleccionado === '') {
        Notify.create('Por favor, complete todos los campos para poder realizar la edición')
      } else {
        const Mensaje = this.mensajes.filter(e => e.IdEstadoAmbitoGestion === parseInt(this.idEstadoAmbitoGestionSeleccionado)).length === 0
          ? null
          : this.mensajes.filter(e => e.IdEstadoAmbitoGestion === parseInt(this.idEstadoAmbitoGestionSeleccionado))[0].Mensaje

        this.modoEdicion = false
        this.competencias = []
        this.tiposCaso = []
        this.ambitosGestion = []
        this.nominaciones = []
        this.origenes = []
        this.estadosAmbitoGestion = []
        this.DataOpcionesOrigenes = []
        this.DataOpcionesNominaciones = []
        this.DataOpcionesCompetencias = []
        this.DataOpcionesTiposCaso = []
        this.DataOpcionesAmbitosGestion = []
        this.DataOpcionesEstadoAmbitoGestion = []

        const datosModificados = {

          Caratula: this.caratulaEditable,

          NroExpediente: this.nroExpedienteEditable,

          Origen: this.origenSeleccionado,
          IdOrigen: this.idOrigenSeleccionado,

          Nominacion: this.nominacionSeleccionada,
          IdNominacion: this.idNominacionSeleccionada,

          Competencia: this.competenciaSeleccionada,
          IdCompetencia: this.idCompetenciaSeleccionada,

          TipoCaso: this.tipoCasoSeleccionado,
          IdTipoCaso: this.idTipoCasoSeleccionado,

          AmbitoGestion: this.ambitoGestionSeleccionado,
          IdJuzgado: this.idAmbitoGestionSeleccionado,

          EstadoAmbitoGestion: this.estadoAmbitoGestionSeleccionado,
          IdEstadoAmbitoGestion: this.idEstadoAmbitoGestionSeleccionado,

          Mensaje: Mensaje,

          FechaEstado: this.FechaEstado,

          IdCasoEstudio: this.idEditable,

          Defiende: this.defiende
        }
        this.datos.Caratula = datosModificados.Caratula
        this.datos.NroExpediente = datosModificados.NroExpediente
        this.datos.Origen = datosModificados.Origen
        this.datos.IdOrigen = datosModificados.IdOrigen
        this.datos.Competencia = datosModificados.Competencia
        this.datos.IdCompetencia = datosModificados.IdCompetencia
        this.datos.TipoCaso = datosModificados.TipoCaso
        this.datos.IdTipoCaso = datosModificados.IdTipoCaso
        this.datos.Juzgado = datosModificados.AmbitoGestion
        this.datos.IdJuzgado = datosModificados.IdJuzgado
        this.datos.Nominacion = datosModificados.Nominacion
        this.datos.IdNominacion = datosModificados.IdNominacion
        this.datos.EstadoAmbitoGestion = datosModificados.EstadoAmbitoGestion
        this.datos.IdEstadoAmbitoGestion = datosModificados.IdEstadoAmbitoGestion
        this.datos.FechaEstado = this.FechaEstado
        this.datos.IdCasoEstudio = this.idEditable
        this.datos.Defiende = this.defiende

        this.$emit('guardarDatosEditados', datosModificados)
      }
    },
    cancelarEdicion () {
      this.modoEdicion = false
      this.origenes = []
      this.nominaciones = []
      this.competencias = []
      this.tiposCaso = []
      this.ambitosGestion = []
      this.estadosAmbitoGestion = []
      this.caratulaEditable = this.datos.Caratula
      this.DataOpcionesOrigenes = []
      this.DataOpcionesNominaciones = []
      this.DataOpcionesCompetencias = []
      this.DataOpcionesTiposCaso = []
      this.DataOpcionesAmbitosGestion = []
      this.DataOpcionesEstadoAmbitoGestion = []
      this.disabled = 1
      this.datosEditar.Origen = this.datos.Origen
      this.datosEditar.Competencia = this.datos.Competencia
      this.datosEditar.TipoCaso = this.datos.TipoCaso
      this.datosEditar.Juzgado = this.datos.Juzgado
      this.datosEditar.Nominacion = this.datos.Nominacion
      this.datosEditar.EstadoAmbitoGestion = this.datos.EstadoAmbitoGestion
      this.$emit('cancelarEdicion')
    },
    diasDesdeCreacion () {
      if (this.datos.FechaAlta) {
        var anoActual = new Date().getFullYear()
        var mesActual = new Date().getUTCMonth()
        var diaActual = new Date().getDate()
        const fechaActual = moment([anoActual, mesActual, diaActual])

        var anoFechaAlta = this.datos.FechaAlta ? this.datos.FechaAlta.slice(0, 4) : '--'
        var mesFechaAlta = this.datos.FechaAlta ? this.datos.FechaAlta.slice(5, 7) - 1 : '--'
        var diaFechaAlta = this.datos.FechaAlta ? this.datos.FechaAlta.slice(8, 10) : '--'
        const fechaAlta = moment([anoFechaAlta, mesFechaAlta, diaFechaAlta])

        if (fechaActual.diff(fechaAlta, 'days') > 0) {
          this.casoViejo = true
        }

        return `${fechaActual.diff(fechaAlta, 'days') > 0 ? fechaActual.diff(fechaAlta, 'days') : ''} ${this.calcularMensajeDias(fechaActual.diff(fechaAlta, 'days'))}`
      } else {
        return 'Sin fecha'
      }
    },
    diasDesdeVisita () {
      return '4'
    },
    calcularMensajeDias (diferencia) {
      switch (diferencia) {
        case 0:
          return 'HOY'
        case 1:
          return 'día'
        default:
          return 'días'
      }
    },
    colorEstado () {
      switch (this.datos.Estado) {
        case 'A':
          return 'green'
        case 'P':
          return 'warning'
        case 'R':
          return 'brown'
        default:
          return 'primary'
      }
    },
    estado () {
      switch (this.datos.Estado) {
        case 'A':
          return 'activo'
        case 'P':
          return 'pendiente'
        case 'R':
          return 'archivado'
        default:
          return 'sin estado'
      }
    },
    archivarCaso (e) {
      this.datos.Estado = e
      this.modal.archivar = false
    },
    eliminarCaso () {
      this.$router.push({
        name: 'Inicio'
      })
    },
    caratulaVer () {
      if (!this.datos.Caratula) { return 'Sin Caratula' }

      let caratula
      this.datos.Caratula.length > 35
        ? caratula = this.datos.Caratula.slice(0, 30) + '...'
        : caratula = this.datos.Caratula

      return caratula
    }
  }
}
</script>

<style scoped>
.separador_vista_caso {
  height: 100%;
  width: 2px;
  position: absolute;
  right: -1px;
  top: 0;
  background: #999999;
}
.no_padding {
  padding: 0 !important;
}
.fecha_container {
  border: 2px solid #1289a7;
  display: flex;
  justify-content: center;
  padding: 5px 0;
  border-radius: 0.4em;
}

.estado {
  display: flex;
  justify-content: flex-end;
}

.contenedor_fecha {
  background-image: linear-gradient(to right, #00605B, transparent);
  margin: 2px 0;
  padding: 8px;
  color: white;
}
@media screen and (max-width: 600px) {
  .estado {
    display: flex;
    justify-content: flex-start;
  }
  .expediente {
    display: flex;
    justify-content: flex-start;
  }
  .badge {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-bottom: 10px;
  }
}

.icono_editar {
  position: absolute;
  top: 12px;
  transition: all 0.2s linear;
}

.icono_editar:hover {
  color:deepskyblue !important;
  cursor: pointer;
}

.icono_compartir {
  top: 10px;
  right: 30px;
  transition: all 0.2s linear;
}

.icono_compartir:hover {
  color:deepskyblue !important;
  cursor: pointer;
}

.icono_objetivos {
  top: 10px;
  right: 90px;
  transition: all 0.2s linear;
}

.icono_objetivos:hover {
  color:deepskyblue !important;
  cursor: pointer;
}

.icono_movimientos {
  top: 10px;
  right: 150px;
  transition: all 0.2s linear;
}

.icono_movimientos:hover {
  color:deepskyblue !important;
  cursor: pointer;
}

.icono_archivar {
  top: 10px;
  right: 210px;
  transition: all 0.2s linear;
}

.icono_archivar:hover {
  color:deepskyblue !important;
  cursor: pointer;
}

.icono_eliminar {
  top: 10px;
  right: 270px;
  transition: all 0.2s linear;
}

.icono_eliminar:hover {
  color:deepskyblue !important;
  cursor: pointer;
}

@media screen and (min-width: 1439px) {
  .contenedor_badges {
    padding-right: 100px;
  }
  .icono_editar {
    padding-right: 20px !important;
  }
}

@media screen and (min-width: 601px) and (max-width: 1439px) {
  .expediente {
    display: flex;
    justify-content: flex-end;
  }
}
</style>
