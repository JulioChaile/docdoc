<template>
  <div>
    <q-page>
      <q-input
        v-model="busqueda"
        :debounce="600"
        placeholder="Buscar"
        style="background:white; padding:1em"
        class="shadow-1"
      />
      <div
        style="padding:1rem; margin-top: 10px; display: flex; justify-content: space-between; align-items:center; flex-wrap: wrap;"
      >
        <q-btn-group flat push style="background-color: white; margin:.5em  ">
          <q-btn
            push
            label="Todo"
            @click="tipoBusqueda = 't'"
            v-bind:outline="tipoBusqueda === 't'"
            color="primary"
          />
          <q-btn
            push
            label="Movimientos"
            @click="tipoBusqueda = 'm'"
            v-bind:outline="tipoBusqueda === 'm'"
            color="primary"
          />
          <q-btn
            push
            label="Casos"
            @click="tipoBusqueda = 'c'"
            v-bind:outline="tipoBusqueda === 'c'"
            color="primary"
          />
        </q-btn-group>
        <div style="display: flex; justify-content: space-evenly; flex-wrap: wrap">
          <q-checkbox v-model="verPerentorios" label="Perentorios" style="margin-left: 10px" />
          <q-checkbox
            v-model="verGestionEstudio"
            label="Gestión Estudio"
            style="margin-left: 10px"
          />
          <q-checkbox v-model="verExternos" label="Gestión Externa" style="margin-left: 10px" />
          <q-checkbox v-model="verFinalizados" label="Finalizados" style="margin-left: 10px" />
        </div>
      </div>
      <div v-if="Juzgados.length === 0">
        <Loading />
      </div>
      <div v-else>
        <q-expansion-item
          v-model="abrirColapsableJuzgado[juzgado.IdJuzgado]"
          v-for="juzgado in computedJuzgados"
          :key="juzgado.IdJuzgado"
          class="whitebg shadow-1"
        >
          <template v-slot:header>
            <q-item-section avatar>
              <q-icon name="adjust" color="primary" size="md" />
            </q-item-section>
            <q-item-section>
              <div style="display: flex; align-items: center;">
                <span style="font-weight:700;">{{juzgado.Juzgado}}</span>
                <span style="margin-left: 0.5rem;">-</span>
                <span v-if="loadingCasos" style="margin-left: 0.5rem;">Cargando casos...</span>
                <span
                  v-else
                  style="margin-left: 0.5rem;"
                >{{casosJuzgado(juzgado.IdJuzgado)}} {{casosJuzgado(juzgado.IdJuzgado) === 1 ? 'Caso' : 'Casos'}}</span>
              </div>
            </q-item-section>
          </template>
          <div v-if="Nominaciones.length === 0">
            <Loading />
          </div>
          <div>
            <q-expansion-item
              v-model="abrirColapsableNominacion[nominacion.IdNominacion]"
              v-for="nominacion in filtrarNominaciones(juzgado.IdJuzgado)"
              :key="nominacion.IdNominacion"
            >
              <template v-slot:header>
                <q-item-section avatar>
                  <q-avatar
                    :icon="nominacion.Nominacion[0] === '0' ? 'filter_none' : 'filter_' + nominacion.Nominacion[0]"
                    text-color="primary"
                  />
                </q-item-section>
                <q-item-section>
                  <span>{{nominacion.Nominacion}}</span>
                  <span v-if="loadingCasos">Cargando casos...</span>
                  <span
                    v-else
                  >({{casosNominacion(nominacion.IdNominacion).length}} {{casosNominacion(nominacion.IdNominacion).length === 1 ? 'Caso' : 'Casos'}})</span>
                </q-item-section>
              </template>
              <div v-if="Casos.length === 0">
                <Loading />
              </div>
              <q-expansion-item
                v-for="caso in casosNominacion(nominacion.IdNominacion)"
                :key="caso.IdCaso"
                icon="description"
                expand-icon-toggle
                class="expansion-caso"
                v-model="abrirColapsableCaso[caso.IdCaso]"
              >
                <template v-slot:header>
                  <q-item-section avatar @click="verCaso(caso)">
                    <q-avatar class="caso" icon="description" color="primary" text-color="white">
                      <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 0]">Ver Caso</q-tooltip>
                    </q-avatar>
                  </q-item-section>

                  <q-item-section>
                    <span
                      style="font-size:16px; font-weight:700;"
                    >{{caso.Caratula}} | {{caso.NroExpediente ? `Expte: ${caso.NroExpediente}` : 'Sin expediente' }}</span>
                    <div style="max-height:26px; overflow: hidden; margin-top: 0.3rem;">
                      <q-select
                        v-model="caso.EstadoCaso"
                        :options="EstadosCaso"
                        @input="modificarEstadoCaso(caso)"
                        square
                        filled
                        style="width: fit-content; background: #D5D5D5;font-size: 12px; margin-top:-7px; display:flex; align-items: flex-start;"
                        :dense="true"
                        :options-dense="true"
                      />
                    </div>
                  </q-item-section>

                  <q-item-section side>
                    <q-btn
                      icon="add"
                      flat
                      round
                      outline
                      size="md"
                      @click="altaMovimiento(caso)"
                      style="margin-left: auto;"
                    >
                      <q-tooltip
                        anchor="top middle"
                        self="bottom middle"
                        :offset="[10, 0]"
                      >Nuevo Movimiento</q-tooltip>
                    </q-btn>
                  </q-item-section>
                </template>
                <div v-if="caso.SinMovimientos">Este caso aún no posee movimientos.</div>
                <TarjetaTribunales
                  v-for="movimiento in movimientoPerteneceCaso(caso)"
                  :key="movimiento.IdMovimientoCaso"
                  :movimiento="movimiento"
                  :ultimosMovimientos="ultimosMovimientos(movimiento).slice(0, 3)"
                  @mostrarObjetivos="mostrarObjetivos(movimiento)"
                  @realizarMovimiento="realizarMovimiento(movimiento, caso.IdCaso)"
                  style="margin-bottom:0.6rem;"
                />
                <div
                  v-if="caso.ObjetivosSinMovs.length > 0"
                  style="display: flex; align-items: center; flex-wrap: wrap; padding: 0 1rem 1rem 1rem;"
                >
                  <q-icon icon="timeline" />
                  <h1 style="font-size: 1.1rem; font-weight: 400;">Objetivos sin Movimientos:</h1>
                  <q-chip
                    color="secondary"
                    v-for="objetivo in objetivosNuevosSinMovimientos(caso.ObjetivosSinMovs)"
                    :key="objetivo.IdObjetivo"
                    style="margin-left: 1rem;"
                  >{{ objetivo.Objetivo }}</q-chip>
                </div>
              </q-expansion-item>
              <div></div>
              <q-banner
                v-for="caso in casosSinMovPendientes(juzgado, nominacion)"
                :key="caso.IdCaso"
                class="text-white caso-sin-pendiente"
                style="margin: 1rem;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); padding:1.5rem;"
              >
                <div
                  style="display: flex; justify-content: space-between;font-size:16px; font-weight:900px;"
                >
                  <span class="float-left">
                    <a v-on:click="verCaso(caso)" style="cursor:pointer">
                      <strong></strong>
                      {{ caso.Caratula }} |
                    </a>
                    <strong></strong>
                    Exp: {{ caso.NroExpediente ? caso.NroExpediente : 'Sin Expediente' }}
                  </span>
                  <q-btn
                    icon="add"
                    color="white"
                    rounded
                    flat
                    outline
                    @click="altaMovimiento(caso)"
                  >
                    <q-tooltip
                      anchor="bottom middle"
                      self="top middle"
                      :offset="[10, 0]"
                    >Nuevo Movimiento</q-tooltip>
                  </q-btn>
                </div>
                <div class="float-right">{{ diasSinMovimientos(caso) }}</div>
              </q-banner>
            </q-expansion-item>
          </div>
        </q-expansion-item>
      </div>
      <!-- Modales de Interacción -->
      <!-- Tarjeta de caso -->
      <q-dialog v-model="modalCaso" v-if="modalCaso">
        <q-card>
          <q-item style="background-color: black;">
            <span class="q-subheading" style="color:white;">Vista previa del caso</span>
          </q-item>
          <TarjetaCaso v-if="modalCaso" :caso="casoVer" :datos="datosCaso" />
          <div style="display: flex; justify-content: flex-end; margin-right: 40px">
            <q-btn flat color="primary" label="Cerrar" @click="modalCaso = false" />
          </div>
        </q-card>
      </q-dialog>
      <!-- Ver Objetivos -->
      <q-dialog v-model="modalObjetivos" v-if="modalObjetivos">
        <q-card>
          <q-list highlight>
            <q-item-label
              header
              class="q-subheading"
              style="background:black; color:white;"
            >Objetivos del caso "{{ movimientoMostrarObjetivos.Caso }}"</q-item-label>
            <q-item
              v-for="objetivo in filtrarObjetivos()"
              :key="objetivo.IdObjetivo"
              style="display:flex; align-items:center; justify-content:space-between;"
            >
              <q-item-label>{{ objetivo.Objetivo}}</q-item-label>
              <div>
                <q-btn
                  icon="edit"
                  color="primary"
                  flat
                  rounded
                  outline
                  @click="edicionObjetivo(objetivo)"
                >
                  <q-tooltip
                    anchor="bottom middle"
                    self="top middle"
                    :offset="[10, 0]"
                  >Editar Objetivo</q-tooltip>
                </q-btn>
                <q-btn
                  icon="add"
                  color="primary"
                  round
                  size="xs"
                  style="cursor: pointer"
                  @click="asociarObjetivo(objetivo)"
                  v-if="movimientoMostrarObjetivos.Objetivo !== objetivo.Objetivo"
                >
                  <q-tooltip
                    anchor="bottom middle"
                    self="top middle"
                    :offset="[10, 0]"
                  >Asociar Objetivo</q-tooltip>
                </q-btn>
                <q-btn
                  icon="clear"
                  round
                  size="xs"
                  color="primary"
                  style="cursor: pointer"
                  @click="desasociarObjetivo(objetivo)"
                  v-if="movimientoMostrarObjetivos.Objetivo === objetivo.Objetivo"
                >
                  <q-tooltip
                    anchor="bottom middle"
                    self="top middle"
                    :offset="[10, 0]"
                  >Desasociar Objetivo</q-tooltip>
                </q-btn>
              </div>
            </q-item>
          </q-list>
          <q-item
            v-if="!movimientoMostrarObjetivos.Objetivo"
            style="display:flex; justify-content:space-between; align-items:center;"
          >
            <q-item-label class="text-primary">Nuevo Objetivo</q-item-label>
            <q-btn
              icon="add"
              color="warning"
              round
              size="xs"
              style="cursor: pointer"
              @click="modalNuevoObjetivo = true"
            />
          </q-item>
          <div style="display: flex; justify-content: flex-end; margin-right: 40px">
            <q-btn
              flat
              color="accent"
              label="Ver movimientos"
              @click="modalMovimientosDeObjetivo = true"
            />
            <q-btn flat color="primary" label="Cerrar" @click="modalObjetivos = false" />
          </div>
        </q-card>
      </q-dialog>
      <q-dialog v-model="editarObjetivo" v-if="editarObjetivo">
        <q-card>
          <q-item style="background-color:black; display:flex; align-items:center; color:white;">
            <q-icon color="white" name="timeline" size="sm" style="margin-right: 1rem" />
            <span class="q-subheading">Editar Objetivo</span>
          </q-item>
          <q-separator />
          <q-item>
            <q-input
              v-model="objetivoEditar.Objetivo"
              type="textarea"
              rows="1"
              :max-height="50"
              label="Objetivo"
              style="margin: 2rem"
            />
          </q-item>
          <q-card-actions :align="'right'">
            <q-btn flat color="primary" @click="finalizarEdicionObjetivo()">Guardar</q-btn>
            <q-btn flat @click="editarObjetivo = false">Cancelar</q-btn>
          </q-card-actions>
        </q-card>
      </q-dialog>
      <!-- Ver Movimientos de un Objetivo -->
      <q-dialog v-model="modalMovimientosDeObjetivo" v-if="modalMovimientosDeObjetivo">
        <q-card>
          <q-list highlight>
            <q-item style="background-color: black;" v-if="movimientoMostrarObjetivos.Objetivo">
              <span
                class="q-subheading"
                style="color:white;"
              >Movimientos del objetivo "{{ movimientoMostrarObjetivos.Objetivo }}"</span>
            </q-item>
            <q-item style="background-color: black;" v-else>
              <span class="q-subheading" style="color:white;">Movimientos sin objetivo asociado</span>
            </q-item>
            <q-item
              v-for="m in movimientosObjetivo()"
              :key="m.IdMovimientoCaso"
              style="display:flex; align-items:center;"
            >
              <q-icon name="assignment" :color="m.Color" size="sm" />
              <q-item-label>{{m.Detalle}}</q-item-label>
              <div
                v-if="m.FechaRealizado"
                style="display:flex; align-items:center;flex-direction:row;margin-left:auto; justify-content:space-between;"
              >
                <q-item-label>{{diasRestantes(m, 'realizado')}}</q-item-label>
                <q-icon name="check_circle" size="sm" color="positive" style="margin-left:0.5rem;" />
              </div>
              <div
                v-else-if="m.FechaEsperada"
                style="display:flex; align-items:center;flex-direction:row;margin-left:auto; justify-content:space-between;"
              >{{parseDate(m.FechaEsperada)}}</div>
            </q-item>
          </q-list>
          <div style="display: flex; justify-content: flex-end; margin-right: 40px">
            <q-btn flat color="primary" label="Cerrar" @click="modalMovimientosDeObjetivo = false" />
          </div>
        </q-card>
      </q-dialog>
      <!-- Nuevo Objetivo -->
      <q-dialog v-model="modalNuevoObjetivo" v-if="modalNuevoObjetivo">
        <q-card>
          <q-item style="background-color: black; color: white; display:flex; align-items:center;">
            <q-icon color="white" name="timeline" size="sm" style="margin-right:1rem;" />
            <span class="q-subheading">Nuevo Objetivo</span>
          </q-item>
          <q-item>
            <q-input v-model="nuevoObjetivo.Objetivo" label="Nombre del objetivo" />
          </q-item>
          <q-card-actions align="end">
            <q-btn color="primary" label="Aceptar" @click="asociarANuevoObjetivo()" />
            <q-btn flat color="primary" label="Cancelar" @click="modalNuevoObjetivo = false" />
          </q-card-actions>
        </q-card>
      </q-dialog>
      <!-- Enviar Whatsapp -->
      <q-dialog v-model="modalWhatsapp" v-if="modalWhatsapp">
        <q-card>
          <q-item style="background-color: #E8EAF6;">
            <q-item-section side color="primary" icon="timeline"></q-item-section>
            <span class="q-subheading">Enviar Whatsapp</span>
          </q-item>
          <q-item style="display:flex;flex-direction:column;">
            <q-input v-model="whatsapp.Numero" label="Numero" type="textarea" style="width:90%;" />
            <q-input
              v-model="whatsapp.Contenido"
              label="Mensaje"
              type="textarea"
              rows="6"
              style="width:90%;"
            />
          </q-item>
          <q-card-actions align="end">
            <q-btn flat color="primary" label="Aceptar" @click="enviarWhatsapp()" />
            <q-btn flat color="primary" label="Cancelar" @click="modalWhatsapp = false" />
          </q-card-actions>
        </q-card>
      </q-dialog>
      <!-- Alta de Movimiento -->
      <q-dialog v-model="modalAlta" v-if="modalAlta" no-backdrop-dismiss no-esc-dismiss>
        <NuevoMovimiento
          v-if="modalAlta"
          :movimientoAlta="this.movimientoAlta"
          :Casos="this.Casos"
          :caso="this.caso"
          :IdCaso="this.caso.IdCaso"
          @cancelarAlta="cancelarAlta"
          @guardarmovimiento="guardarMovimiento"
          @nuevochat="nuevoChat"
          @enviarmensaje="enviarMensaje"
        />
        <!-- q-card style="min-width:400px;">
        <q-item style="background-color: black;">
          <span class="q-subheading" style="color:white;">Nuevo Movimiento</span>
        </q-item>
        <div style="padding: 0 1rem 1rem 1rem;">
          <div style="display:flex; flex-direction:row;justify-content: space-between;margin-top: 20px;">
            <q-field
              label= "Caso:"
              stack-label
              :style="movimientoAlta.Objetivo ? 'width:45%' : 'width: 100%'"
            >
              {{ movimientoAlta.Caso}}
            </q-field>
            <q-field
              style="width: 50%"
              label= "Objetivo: "
              stack-label
              v-if="!alta"
            >
              {{ movimientoAlta.Objetivo ? movimientoAlta.Objetivo : 'Sin Objetivo'}}
            </q-field>
          </div>
          <q-input v-model="nuevoMovimiento.Detalle" autogrow label="Detalle del movimiento"/>
          <div style="display:flex; justify-content:space-between; align-items:end">
            <q-select
              style="width:47%;"
              v-model="nuevoMovimiento.colorSeleccionado"
              :options="coloresDocDoc"
              label="Estado de Gestión"
            />
            <q-input ref="inputFechaEsperada" label="Fecha Esperada" v-model="nuevoMovimiento.FechaEsperada" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                    <q-date
                      mask="DD-MM-YYYY"
                      v-model="nuevoMovimiento.FechaEsperada"
                      @input="() => $refs.qDateProxy1.hide()"
                      @change="editarFechaEsperada()"
                    />
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
          </div>
          <div style="display:flex; justify-content:space-between; align-items:end;">
            <q-input ref="inputFechaAlta" label="Fecha de estado de gestión" v-model="nuevoMovimiento.FechaAlta" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
              <template v-slot:append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
                    <q-date
                      mask="DD-MM-YYYY"
                      label="Fecha de estado de gestión"
                      v-model="nuevoMovimiento.FechaAlta"
                      @input="() => $refs.qDateProxy2.hide()"
                      @change="editarFechaEsperada()"
                    />
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>
            <q-select
              style="width:47%;"
              v-model="nuevoMovimiento.Responsable"
              :options="opcionesResponsable"
              label="Responsable del movimiento"
            />
          </div>
          <div style="display:flex; justify-content:space-between;align-items:end">
            <q-select
              style="width:47%;"
              v-model="nuevoMovimiento.TipoMov"
              :options="opcionesTipoMov"
              label="Tipo de movimiento"
            />
            <q-uploader
              label="Multimedia"
              auto-upload
              multiple
              :factory="factoryFn"
              @uploaded="uploadedFile"
              style="width:47%"
            />
          </div>
          <div style="padding-top:30px; text-align:right">
            <q-btn color="primary" @click="guardarMovimiento()">Guardar</q-btn>
            <q-btn flat @click="cancelarAlta()">Cancelar</q-btn>
          </div>
        </div>
        </q-card-->
      </q-dialog>
    </q-page>
  </div>
</template>

<script>
import request from '../../request'
import { each } from 'lodash'
import { Notify, QDialog } from 'quasar'
import TarjetaCaso from '../TarjetaCaso.vue'
import TarjetaTribunales from '../TarjetaTribunales.vue'
import Loading from '../Loading.vue'
import NuevoMovimiento from '../NuevoMovimiento.vue'

import auth from '../../auth'

export default {
  name: 'TribunalesComponent',
  components: {
    TarjetaCaso,
    TarjetaTribunales,
    QDialog,
    Loading,
    NuevoMovimiento
  },
  props: {
    preview: {
      type: String,
      required: false,
      default: ''
    },
    individual: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      objetivoEditar: {},
      editarObjetivo: false,
      busqueda: '',
      tipoBusqueda: 't', // t (Todos) - m (Movimientos) - c (Casos)
      verPerentorios: true,
      verGestionEstudio: true,
      verExternos: true,
      verFinalizados: true,
      UsuarioResponsable: '',
      estado: 'A',
      alta: false,
      altaFinalizada: false,
      Juzgados: [],
      Nominaciones: [],
      Casos: [],
      CasosPendientes: [],
      caso: {},
      Movimientos: [],
      MovimientosPendientes: [],
      movimientoEditar: {},
      movimientoAlta: {},
      movimientoMostrarObjetivos: {},
      casoVer: {},
      modalCaso: false,
      modalObjetivos: false,
      modalNuevoObjetivo: false,
      modalEditar: false,
      modalAlta: false,
      modalMovimientosDeObjetivo: false,
      modalWhatsapp: false,
      Multimedia: [],
      loadingCasos: true,
      cantidad: 15,
      offset: 10,
      popover: false,
      idCasoRecibido: 0,
      Responsable: {},
      IdResponsable: null,
      abrirColapsableJuzgado: [],
      abrirColapsableNominacion: [],
      abrirColapsableCaso: [],
      colorMostrar: '',
      objetivosMostrar: [],
      nuevoObjetivo: {},
      nuevoMovimiento: {
        IdUsuario: 0,
        TiposMov: [],
        TipoMov: 0,
        UsuariosEstudio: [],
        IdEstudio: 0,
        Detalle: '',
        FechaEsperada: '',
        FechaAlta: '',
        colorSeleccionado: { label: 'Gestión Estudio', value: 'primary' },
        Caso: 0,
        Objetivo: '',
        Responsable: {}
      },
      coloresDocDoc: [
        {
          label: 'Perentorio',
          value: 'negative'
        },
        {
          label: 'Gestión Estudio',
          value: 'primary'
        },
        {
          label: 'Gestión Externa',
          value: 'warning'
        },
        {
          label: 'Finalizado',
          value: 'positive'
        }
      ],
      whatsapp: {
        Numero: '',
        Contenido: ''
      },
      EstadosCaso: [],
      objetivosCreadosSinMovimientos: []
    }
  },
  created () {
    // Logica para mostrar Judiciales o Extrajudiciales
    switch (this.preview) {
      case 1:
    }

    var tzoffset = new Date().getTimezoneOffset() * 60000
    this.nuevoMovimiento.FechaAlta = this.formatearFecha(
      new Date(Date.now() - tzoffset).toISOString().slice(0, -1)
    )
      .split(' ')[0]
      .split('-')
      .reverse()
      .join('-')
    const r = auth.UsuarioLogueado
    this.IdResponsable = r.IdUsuario
    this.nuevoMovimiento.Responsable = {
      label: `${r.Apellidos}, ${r.Nombres}`,
      value: r.IdUsuario
    }
    this.IdEstudio = r.IdEstudio
    request.Get(`/estudios/${r.IdEstudio}/usuarios`, {}, (r) => {
      if (!r.Error) {
        this.UsuariosEstudio = r
      }
    })
    request.Get(`/estudios/${r.IdEstudio}/estados-caso`, {}, (r) => {
      if (!r.Error) {
        r.forEach((ec) => {
          this.EstadosCaso.push({
            label: ec.EstadoCaso,
            value: ec.IdEstadoCaso
          })
        })
      }
    })
    request.Get('/juzgados?IncluyeBajas=N', {}, (r) => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        this.Juzgados = r
        const IdsJuzgado = []
        this.Juzgados.forEach((j) => {
          IdsJuzgado.push(j.IdJuzgado)
        })
        request.Get(
          `/nominaciones?IdsJuzgado=${JSON.stringify(
            IdsJuzgado
          )}&IncluyeBajas=N`,
          {},
          (n) => {
            if (n.Error) {
              Notify.create(n.Error)
            } else {
              each(n, (nominaciones) => {
                this.Nominaciones = this.Nominaciones.concat(nominaciones)
              })
            }
          }
        )
        /*
        if (this.$route.params.idJuzgado) {
          this.abrirColapsableJuzgado[this.$route.params.idJuzgado] = true
          this.abrirColapsableNominacion[this.$route.params.idNominacion] = true
          request.Get('/casos', {Estado: 'T'}, r => {
            if (!r.Error) {
              r.forEach(c => {
                if (c.Caratula === this.$route.params.Caratula && c.IdNominacion === this.$route.params.idNominacion &&
                c.IdJuzgado === this.$route.params.idJuzgado) {
                  c.esNuevo = true
                  this.idCasoRecibido = c.IdCaso
                  if (this.Casos.indexOf(c) < 0) {
                    c.SinMovimientos = true
                    c.ObjetivosSinMovs = []
                    this.Casos.push(c)
                    this.CasosPendientes.push(c)
                  }
                }
              })
            }
          })
        } */
        request.Get('/movimientos/vista-tribunales', {}, (r) => {
          if (!r.Error) {
            let objetivosPendientes = []
            let idCasos = []
            r.forEach((c) => {
              let EstadoCaso = this.EstadosCaso.find(
                (ec) => ec.value === c.IdEstadoCaso
              )
              c.EstadoCaso = EstadoCaso
              c.ObjetivosSinMovs = []
              idCasos.push(c.IdCaso)
              let pendiente = false
              /*
              if (c.IdCaso - this.idCasoRecibido === 0) {
                this.IdNominacion = c.IdNominacion
                this.IdJuzgado = c.IdJuzgado
                this.abrirColapsableJuzgado[this.IdJuzgado] = true
                this.abrirColapsableNominacion[this.IdNominacion] = true
              }
              */
              if (
                this.Casos.indexOf(c) < 0 &&
                this.CasosPendientes.indexOf(c) < 0 &&
                c.Estado !== 'R'
              ) {
                c.Movimientos = JSON.parse(c.Movimientos)
                if (c.Movimientos.length > 0) {
                  c.SinMovimientos = false
                } else {
                  c.SinMovimientos = true
                }
                c.Movimientos.forEach((m) => {
                  m.Detalle = m.Detalle.replace(/<\s*\/?\s*br\s*.*?>/g, '\n')
                  if (this.diasRestantes(m, 'vencimiento') < 1) {
                    m.ColorMostrar = 'negative'
                  }
                  if (c.Tipo === 'N') {
                    m.Mostrar = true
                  }
                  m.IdUsuario = m.IdUsuarioResponsable
                  m.CasoCompleto = c
                  if (!m.FechaRealizado) {
                    this.MovimientosPendientes.push(m)
                    objetivosPendientes.push(m.Objetivo)
                    pendiente = true
                  }
                  this.Movimientos.push(m)
                })
                this.Casos.push(c)
                if (pendiente) {
                  this.CasosPendientes.push(c)
                }
              }
            })
            this.loadingCasos = false
            request.Get(`/objetivos?IdsCaso=[${idCasos}]`, {}, (r) => {
              if (!r.Error) {
                this.Casos.map((c) => {
                  c.Objetivos = r[c.IdCaso]
                  r[c.IdCaso].forEach((o) => {
                    if (objetivosPendientes.indexOf(o.Objetivo) === -1) {
                      c.ObjetivosSinMovs.push(o)
                    }
                  })
                })
              }
            })
          }
        })
      }
    })
  },
  computed: {
    computedJuzgados () {
      if (this.preview === '1') {
        return this.Juzgados.filter((juzgado) => juzgado.ModoGestion === 'J')
      } else if (this.preview === '2') {
        return this.Juzgados.filter((juzgado) => juzgado.ModoGestion === 'E')
      } else {
        return this.Juzgados
      }
    },
    ObjetivosSinMovimientos (item) {
      return item.concat(this.objetivosCreadosSinMovimientos)
    },
    opcionesTipoMov () {
      let result = []
      if (
        this.modalAlta &&
        this.nuevoMovimiento.TiposMov &&
        this.nuevoMovimiento.TiposMov.length
      ) {
        result = this.nuevoMovimiento.TiposMov.map((t) => ({
          label: t.TipoMovimiento,
          value: t.IdTipoMov
        }))
      }
      return result
    },
    opcionesResponsable () {
      let result = []
      if (
        this.modalAlta &&
        this.nuevoMovimiento.UsuariosEstudio &&
        this.nuevoMovimiento.UsuariosEstudio.length
      ) {
        result = this.nuevoMovimiento.UsuariosEstudio.map((t) => ({
          label: `${t.Apellidos}, ${t.Nombres}`,
          value: t.IdUsuario
        }))
      }
      return result
    }
  },
  methods: {
    edicionObjetivo (objetivo) {
      this.objetivoEditar = objetivo
      this.editarObjetivo = true
    },
    finalizarEdicionObjetivo () {
      let objetivo = {
        Objetivo: this.objetivoEditar.Objetivo
      }
      request.Put(
        `/objetivos/${this.objetivoEditar.IdObjetivo}`,
        objetivo,
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            Object.defineProperty(
              this.filtrarObjetivos().filter(
                (obj) => obj.IdObjetivo === this.objetivoEditar.IdObjetivo
              ),
              'Objetivo',
              {
                value: this.objetivoEditar.Objetivo
              }
            )

            this.editarObjetivo = false
            this.objetivoEditar = {}
          }
        }
      )
    },
    objetivosNuevosSinMovimientos (item) {
      return item.concat(this.objetivosCreadosSinMovimientos)
    },
    modificarEstadoCaso (caso) {
      let modificado = {
        IdJuzgado: caso.IdJuzgado,
        IdNominacion: caso.IdNominacion,
        IdEstadoCaso: caso.EstadoCaso.value,
        Caratula: caso.Caratula,
        NroExpediente: caso.NroExpediente,
        Carpeta: caso.Carpeta,
        Observaciones: caso.Observaciones
      }
      request.Put(`/casos/${caso.IdCaso}`, modificado, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        }
      })
    },
    casosJuzgado (idJuzgado) {
      let sumaCasos = 0
      this.filtrarNominaciones(idJuzgado).forEach((n) => {
        sumaCasos = sumaCasos + this.casosNominacion(n.IdNominacion).length
      })
      return sumaCasos
    },
    formatearFecha (fecha) {
      if (fecha) {
        return fecha.split('T')[0]
      } else return null
    },
    parseDateTime (fecha) {
      if (fecha) {
        const fh = fecha.split(' ')
        const amd = fh[0].split('-')
        const hms = fh[1].split(':')
        return `${amd[2]}/${amd[1]}/${amd[0]} ${hms[0]}:${hms[1]}`
      } else return null
    },
    parseDate (fecha) {
      if (fecha) {
        return this.parseDateTime(fecha).split(' ')[0]
      } else return null
    },
    verCaso (caso) {
      this.casoVer = caso
      if (this.casoVer.IdEstadoCaso) {
        request.Get(`/estados-caso/${this.casoVer.IdEstadoCaso}`, {}, (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.casoVer.EstadoCaso = r.EstadoCaso
          }
        })
      }
      request.Get(`/tipos-caso/${this.casoVer.IdTipoCaso}`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.casoVer.TipoCaso = r.TipoCaso
          this.modalCaso = true
        }
      })
      request.Get(`/casos/${this.casoVer.IdCaso}`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.casoVer.PersonasCaso = r.PersonasCaso
          this.modalCaso = true
        }
      })
    },
    realizarMovimiento (movimiento, IdCaso) {
      request.Put(
        `/movimientos/${movimiento.IdMovimientoCaso}/realizar`,
        {},
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.MovimientosPendientes.splice(
              this.MovimientosPendientes.indexOf(movimiento),
              1
            )
            let movimientosRestantes = false
            this.MovimientosPendientes.forEach((m) => {
              if (
                m.Caso === movimiento.Caso &&
                m.IdMovimientoCaso !== movimiento.IdMovimientoCaso
              ) {
                movimientosRestantes = true
              }
            })
            if (movimientosRestantes === false) {
              this.CasosPendientes.forEach((c) => {
                if (c.Caratula === movimiento.Caso) {
                  c.Tipo = 'N'
                  this.CasosPendientes.splice(
                    this.CasosPendientes.indexOf(c),
                    1
                  )
                }
              })
            }
            this.$q.notify({
              color: 'green',
              message: `Se marcó como realizado el movimiento '${movimiento.Detalle}'`
            })
            this.movimientoAlta = movimiento
            this.caso.IdCaso = IdCaso
            this.altaMovimiento(null)
          }
        }
      )
    },
    altaMovimiento (caso) {
      this.modalAlta = true
      if (caso) {
        caso.SinMovimientos = false
        this.movimientoAlta.Caso = caso.Caratula
        this.alta = true
        this.caso = caso
        this.nuevoMovimiento.Objetivo = this.movimientoAlta.Objetivo
      }
      this.nuevoMovimiento.IdEstudio = auth.UsuarioLogueado.IdEstudio
      request.Get(
        `/estudios/${this.nuevoMovimiento.IdEstudio}/usuarios`,
        {},
        (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else if (r.length) {
            this.nuevoMovimiento.UsuariosEstudio = r
          }
        }
      )
      request.Get(
        `/estudios/${this.nuevoMovimiento.IdEstudio}/tipos-movimiento`,
        {},
        (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else if (r.length) {
            this.nuevoMovimiento.TiposMov = r
            this.nuevoMovimiento.TipoMov = {
              label: r[0].TipoMovimiento,
              value: r[0].IdTipoMov
            }
          } else {
            this.$q.notify(
              'No hay tipos de movimiento disponibles para este estudio'
            )
          }
        }
      )
    },
    guardarMovimiento (movimiento) {
      this.modalAlta = false
      this.altaFinalizada = true
      this.alta = false
      this.Casos.forEach((c) => {
        if (this.movimientoAlta.Caso === c.Caratula) {
          movimiento.IdCaso = c.IdCaso
          movimiento.NroExpediente = c.NroExpediente
        }
      })
      if (this.caso) {
        if (this.CasosPendientes.indexOf(this.caso) - -1 === 0) {
          const c = Object.assign({}, this.caso)
          this.CasosPendientes.push(c)
        }
      }
      request.Get(`/casos/${movimiento.IdCaso}/movimientos`, {}, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          var contador = true
          r.forEach((m) => {
            if (m.Detalle === movimiento.Detalle) {
              movimiento.IdMovimientoCaso = m.IdMovimientoCaso
              if (contador === true && !movimiento.FechaRealizado) {
                if (this.movimientoAlta.Objetivo) {
                  var objetivo = {}
                  this.movimientoAlta.CasoCompleto.Objetivos.forEach((o) => {
                    if (o.Objetivo === this.movimientoAlta.Objetivo) {
                      objetivo = o
                    }
                  })
                  request.Post(
                    `/movimientos/${movimiento.IdMovimientoCaso}/asociar-objetivo/${objetivo.IdObjetivo}`,
                    {},
                    (r) => {
                      movimiento.Objetivo = objetivo.Objetivo
                      this.MovimientosPendientes.push(movimiento)
                      this.Movimientos.push(movimiento)
                      contador = false
                    }
                  )
                } else {
                  this.MovimientosPendientes.push(movimiento)
                  this.Movimientos.push(movimiento)
                  contador = false
                }
              }
            }
          })
          this.nuevoMovimiento.IdUsuario = 0
          this.nuevoMovimiento.TiposMov = []
          this.nuevoMovimiento.TipoMov = 0
          this.nuevoMovimiento.UsuariosEstudio = []
          this.nuevoMovimiento.IdEstudio = 0
          this.nuevoMovimiento.IdResponsable = 0
          this.nuevoMovimiento.Detalle = ''
          this.nuevoMovimiento.FechaAlta = new Date().toISOString()
          this.nuevoMovimiento.FechaEsperada = null
          this.nuevoMovimiento.colorSeleccionado = {
            label: 'Gestión Estudio',
            value: 'primary'
          }
          this.nuevoMovimiento.Caso = 0
          this.movimientoAlta = {}
          this.caso = {}
        }
        /*
      if (this.nuevoMovimiento.FechaEsperada) {
        const fE = new Date(this.nuevoMovimiento.FechaEsperada.split(' ')[0].split('-').reverse().join('-'))
        console.log('Fecha esperada: ' + fE)
        const fA = new Date(this.nuevoMovimiento.FechaAlta.split(' ')[0].split('-').reverse().join('-'))
        console.log('Fecha de alta: ' + fA)
        if (fE < fA && fE) {
          this.$q.notify('No se puede ingresar una fecha esperada anterior a la fecha de alta.')
          return
        }
      }
      if (!this.nuevoMovimiento.Detalle) {
        this.$q.notify('Debe ingresar el detalle del movimiento.')
      }
      if (!this.nuevoMovimiento.TipoMov) {
        this.$q.notify('Debe elegir un tipo de movimiento.')
      }
      if (this.nuevoMovimiento.colorSeleccionado === '') {
        this.$q.notify('Debe seleccionar un estado de gestión')
      } else {
        this.Casos.forEach(c => {
          if (this.movimientoAlta.Caso === c.Caratula) {
            this.nuevoMovimiento.IdCaso = c.IdCaso
            this.nuevoMovimiento.NroExpediente = c.NroExpediente
          }
        })
        let fEsperada = this.nuevoMovimiento.FechaEsperada
        let fAlta = this.nuevoMovimiento.FechaAlta
        if (this.nuevoMovimiento.FechaEsperada && this.nuevoMovimiento.FechaEsperada.split('-')[2].length === 4) {
          fEsperada = `${this.nuevoMovimiento.FechaEsperada.split('-')[2]}-${this.nuevoMovimiento.FechaEsperada.split('-')[1]}-${this.nuevoMovimiento.FechaEsperada.split('-')[0]}`
        }
        if (this.nuevoMovimiento.FechaAlta && this.nuevoMovimiento.FechaAlta.split('-')[2].length === 4) {
          fAlta = `${this.nuevoMovimiento.FechaAlta.split('-')[2]}-${this.nuevoMovimiento.FechaAlta.split('-')[1]}-${this.nuevoMovimiento.FechaAlta.split('-')[0]}`
        }
        const movimiento = {
          IdResponsable: this.nuevoMovimiento.Responsable.value,
          Detalle: this.nuevoMovimiento.Detalle,
          IdCaso: this.nuevoMovimiento.IdCaso,
          FechaEsperada: this.formatearFecha(fEsperada),
          FechaAlta: this.formatearFecha(fAlta),
          FechaRealizado: null,
          IdTipoMov: this.nuevoMovimiento.TipoMov.value,
          Cuaderno: this.movimientoAlta.Cuaderno,
          Color: this.nuevoMovimiento.colorSeleccionado.value,
          Multimedia: this.Multimedia
        }
        request.Post('/movimientos', movimiento, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            console.log(movimiento)
            this.modalAlta = false
            this.$q.notify({
              color: 'green',
              message: `El movimiento "${movimiento.Detalle}" se dio de alta correctamente`})
            if (this.caso) {
              if (this.CasosPendientes.indexOf(this.caso) - -1 === 0) {
                this.CasosPendientes.push(this.caso)
              }
            }
            movimiento.TipoMovimiento = this.nuevoMovimiento.TipoMov.label
            movimiento.Caso = this.movimientoAlta.Caso
            movimiento.CasoCompleto = this.caso
            movimiento.UsuarioResponsable = this.nuevoMovimiento.Responsable.label
            request.Get(`/casos/${this.nuevoMovimiento.IdCaso}/movimientos`, {}, r => {
              if (r.Error) {
                this.$q.notify(r.Error)
              } else {
                var contador = true
                r.forEach(m => {
                  if (m.Detalle === movimiento.Detalle) {
                    movimiento.IdMovimientoCaso = m.IdMovimientoCaso
                    if (contador === true && !movimiento.FechaRealizado) {
                      if (this.movimientoAlta.Objetivo) {
                        var objetivo = {}
                        this.movimientoAlta.CasoCompleto.Objetivos.forEach(o => {
                          if (o.Objetivo === this.movimientoAlta.Objetivo) {
                            objetivo = o
                          }
                        })
                        request.Post(`/movimientos/${movimiento.IdMovimientoCaso}/asociar-objetivo/${objetivo.IdObjetivo}`, {}, r => {
                          movimiento.Objetivo = objetivo.Objetivo
                          this.MovimientosPendientes.push(movimiento)
                          this.Movimientos.push(movimiento)
                          contador = false
                        })
                      } else {
                        this.MovimientosPendientes.push(movimiento)
                        this.Movimientos.push(movimiento)
                        contador = false
                      }
                    }
                  }
                })
                this.altaFinalizada = true
                this.alta = false
                this.movimientoAlta = {}
                this.nuevoMovimiento.IdUsuario = 0
                this.nuevoMovimiento.TiposMov = []
                this.nuevoMovimiento.TipoMov = 0
                this.nuevoMovimiento.UsuariosEstudio = []
                this.nuevoMovimiento.IdEstudio = 0
                this.nuevoMovimiento.IdResponsable = 0
                this.nuevoMovimiento.Detalle = ''
                this.nuevoMovimiento.FechaAlta = new Date().toISOString()
                this.nuevoMovimiento.FechaEsperada = null
                this.nuevoMovimiento.colorSeleccionado = {label: 'Gestión Estudio', value: 'primary'}
                this.nuevoMovimiento.Caso = 0
              }
            })
          }
        })
      }
      */
      })
    },
    filtrarNominaciones (idJuzgado) {
      let respuesta = []
      this.Nominaciones.forEach((nominacion) => {
        if (nominacion.IdJuzgado === idJuzgado) {
          if (respuesta.indexOf(nominacion) === -1) {
            respuesta.push(nominacion)
          }
        }
      })
      return respuesta
    },
    ultimosMovimientos (movimiento) {
      let respuesta = []
      this.Movimientos.forEach((m) => {
        if (m.Objetivo === movimiento.Objetivo && m.Caso === movimiento.Caso && m.FechaRealizado) {
          respuesta.push(m)
        }
      })
      return respuesta.sort(function (m1, m2) {
        if (m1.FechaRealizado > m2.FechaRealizado) {
          return -1
        } else if (m1.FechaRealizado < m2.FechaRealizado) {
          return 1
        } else return 0
      })
    },
    movimientosObjetivo () {
      var respuesta = this.ultimosMovimientos(this.movimientoMostrarObjetivos)
      return respuesta
    },
    casosNominacion (nominacion) {
      var respuesta = []
      this.CasosPendientes.forEach((c) => {
        let retornar = false
        if (c.IdNominacion === nominacion && c.Estado === 'A') {
          if (!this.busqueda) {
            respuesta.push(c)
          } else if (this.tipoBusqueda === 'c' || this.tipoBusqueda === 't') {
            if (
              c.Caratula.toLowerCase().includes(this.busqueda.toLowerCase())
            ) {
              retornar = true
            } else if (
              this.busqueda.length >= 3 &&
              c.NroExpediente.includes(this.busqueda)
            ) {
              retornar = true
            } else {
              if (this.tipoBusqueda === 't') {
                c.Movimientos.forEach((m) => {
                  if (
                    m.Detalle.toLowerCase().includes(
                      this.busqueda.toLowerCase()
                    )
                  ) {
                    if (
                      (this.verPerentorios && m.Color === 'negative') ||
                      (this.verExternos && m.Color === 'warning') ||
                      (this.verGestionEstudio && m.Color === 'primary') ||
                      (this.verFinalizados && m.Color === 'positive')
                    ) {
                      retornar = true
                    }
                  }
                })
              }
            }
            if (retornar) {
              this.abrirColapsableJuzgado[c.IdJuzgado] = true
              this.abrirColapsableNominacion[c.IdNominacion] = true
              respuesta.push(c)
            }
          } else if (this.tipoBusqueda === 'm') {
            c.Movimientos.forEach((m) => {
              if (
                m.Detalle.toLowerCase().includes(this.busqueda.toLowerCase())
              ) {
                retornar = true
              }
            })
            if (retornar) {
              respuesta.push(c)
              this.abrirColapsableJuzgado[c.IdJuzgado] = true
              this.abrirColapsableNominacion[c.IdNominacion] = true
            }
          }
        }
      })
      return respuesta.sort(function (a, b) {
        if (a.Caratula > b.Caratula) {
          return 1
        }
        if (a.Caratula < b.Caratula) {
          return -1
        }
        return 0
      })
    },
    movimientoPerteneceCaso (caso) {
      var respuesta = []
      this.MovimientosPendientes.forEach((m) => {
        if (m.CasoCompleto.IdCaso === caso.IdCaso && caso.Estado === 'A') {
          m.NroExpediente = caso.NroExpediente
          if (!m.FechaRealizado) {
            if (!m.Mostrar) {
              if (m.Color === 'negative') {
                m.Orden = 1
              }
              if (m.Color === 'primary') {
                m.Orden = 2
              }
              if (m.Color === 'warning') {
                m.Orden = 3
              }
              if (this.busqueda) {
                if (this.tipoBusqueda === 'm' || this.tipoBusqueda === 't') {
                  if (
                    m.Detalle.toLowerCase().includes(
                      this.busqueda.toLowerCase()
                    ) ||
                    m.Caso.toLowerCase().includes(this.busqueda.toLowerCase())
                  ) {
                    if (
                      (this.verPerentorios && m.Color === 'negative') ||
                      (this.verExternos && m.Color === 'warning') ||
                      (this.verGestionEstudio && m.Color === 'primary') ||
                      (this.verFinalizados && m.Color === 'positive')
                    ) {
                      respuesta.push(m)
                    }
                  }
                } else {
                  if (
                    m.Caso.toLowerCase().includes(this.busqueda.toLowerCase())
                  ) {
                    if (
                      (this.verPerentorios && m.Color === 'negative') ||
                      (this.verExternos && m.Color === 'warning') ||
                      (this.verGestionEstudio && m.Color === 'primary') ||
                      (this.verFinalizados && m.Color === 'positive')
                    ) {
                      respuesta.push(m)
                    }
                  }
                }
              } else {
                if (
                  (this.verPerentorios && m.Color === 'negative') ||
                  (this.verExternos && m.Color === 'warning') ||
                  (this.verGestionEstudio && m.Color === 'primary') ||
                  (this.verFinalizados && m.Color === 'positive')
                ) {
                  respuesta.push(m)
                }
              }
            }
          }
        }
      })
      return respuesta.sort(function (a, b) {
        if (a.Color === 'positive') {
          return 1
        }
        if (b.Color === 'positive') {
          return -1
        }
        if (!a.Objetivo && !b.Objetivo) {
          if (a.Orden > b.Orden) {
            return 1
          }
          if (a.Orden < b.Orden) {
            return -1
          } else return 0
        }
        if (!a.Objetivo) {
          return 1
        }
        if (!b.Objetivo) {
          return -1
        }
        if (a.Objetivo > b.Objetivo) {
          return 1
        }
        if (a.Objetivo < b.Objetivo) {
          return -1
        } else {
          if (a.Orden > b.Orden) {
            return 1
          }
          if (a.Orden < b.Orden) {
            return -1
          }
        }
        return 0
      })
    },
    factoryFn () {
      return {
        url: 'https://io.docdoc.com.ar/api/multimedia',
        method: 'POST',
        headers: [{ name: 'Authorization', value: `Bearer ${auth.Token}` }]
      }
    },
    uploadedFile ({ files, xhr }) {
      const data = JSON.parse(xhr.response)
      for (let i = 0; i < files.length; i++) {
        this.Multimedia.push({
          Url: data.Urls[0],
          Tipo: files[i].type.substring(0, 1).toUpperCase()
        })
      }
    },
    casosSinMovPendientes (juzgado, nominacion) {
      var respuesta = []
      this.Casos.forEach((c) => {
        if (
          c.Tipo === 'N' &&
          c.Juzgado === juzgado.Juzgado &&
          c.Nominacion === nominacion.Nominacion
        ) {
          if (!this.busqueda) {
            respuesta.push(c)
          } else if (this.tipoBusqueda === 'c' || this.tipoBusqueda === 't') {
            if (
              c.Caratula.toLowerCase().includes(this.busqueda.toLowerCase())
            ) {
              this.abrirColapsableJuzgado[c.IdJuzgado] = true
              this.abrirColapsableNominacion[c.IdNominacion] = true
              respuesta.push(c)
            } else if (
              this.busqueda.length >= 3 &&
              c.NroExpediente.includes(this.busqueda)
            ) {
              this.abrirColapsableJuzgado[c.IdJuzgado] = true
              this.abrirColapsableNominacion[c.IdNominacion] = true
              respuesta.push(c)
            }
          }
          if (c.IdCaso === this.nuevoMovimiento.IdCaso && this.altaFinalizada) {
            this.altaFinalizada = false
            respuesta.splice(c, 1)
            this.Casos[this.Casos.indexOf(c)].Tipo = 'S'
          }
        }
      })
      return respuesta.sort(function (a, b) {
        if (a.Caratula > b.Caratula) {
          return 1
        }
        if (a.Caratula < b.Caratula) {
          return -1
        }
        return 0
      })
    },
    diasRestantes (movimiento, texto) {
      if (!movimiento.FechaEsperada && texto === 'vencimiento') {
        return '-'
      }
      this.fecha2 = new Date()
      var year = ''
      var month = ''
      var day = ''
      if (texto === 'vencimiento') {
        year = movimiento.FechaEsperada.split('-')[0]
        month = movimiento.FechaEsperada.split('-')[1] - 1
        day = movimiento.FechaEsperada.split('-')[2].split(' ')[0] - 1
      } else if (texto === 'creacion') {
        year = movimiento.FechaAlta.split('-')[0]
        month = movimiento.FechaAlta.split('-')[1] - 1
        day = movimiento.FechaAlta.split('-')[2].split(' ')[0] - 1
      } else if (movimiento.FechaRealizado) {
        year = movimiento.FechaRealizado.split('-')[0]
        month = movimiento.FechaRealizado.split('-')[1] - 1
        day = movimiento.FechaRealizado.split('-')[2].split(' ')[0] - 1
      }
      this.fecha2.setMonth(month)
      this.fecha2.setFullYear(year)
      this.fecha2.setDate(day)
      this.hoy = new Date()
      var resultado =
        Math.ceil(
          (this.fecha2.getTime() - this.hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      return resultado * -1
    },
    diasSinMovimientos (caso) {
      this.fecha2 = new Date()
      var year = ''
      var month = ''
      var day = ''
      this.hoy = new Date()
      year = caso.FechaAlta.split('-')[0]
      month = caso.FechaAlta.split('-')[1] - 1
      day = caso.FechaAlta.split('-')[2].split(' ')[0]
      this.fecha2.setMonth(month)
      this.fecha2.setFullYear(year)
      this.fecha2.setDate(day)
      var resta = Math.ceil(
        (this.hoy.getTime() - this.fecha2.getTime()) / (1000 * 60 * 60 * 24)
      )
      if (resta === 0) {
        return 'Sin movimientos desde hoy'
      } else {
        return 'Sin movimientos hace ' + resta + ' días'
      }
    },
    mostrarObjetivos (movimiento) {
      this.Casos.forEach((c) => {
        if (c.IdCaso === movimiento.CasoCompleto.IdCaso) {
          this.modalObjetivos = true
          this.movimientoMostrarObjetivos = movimiento
          this.objetivosMostrar = c.Objetivos
        }
      })
    },
    filtrarObjetivos () {
      let respuesta = []
      this.objetivosMostrar.forEach((objetivo) => {
        if (!this.movimientoMostrarObjetivos.Objetivo) {
          respuesta.push(objetivo)
        } else {
          if (objetivo.Objetivo === this.movimientoMostrarObjetivos.Objetivo) {
            respuesta.push(objetivo)
          }
        }
      })
      return respuesta
    },
    asociarObjetivo (objetivo) {
      request.Post(
        `/movimientos/${this.movimientoMostrarObjetivos['IdMovimientoCaso']}/asociar-objetivo/${objetivo.IdObjetivo}`,
        {},
        (r) => {
          this.movimientoMostrarObjetivos.Objetivo = objetivo.Objetivo
          this.modalObjetivos = false
        }
      )
    },
    desasociarObjetivo (objetivo) {
      request.Put(
        `/movimientos/${this.movimientoMostrarObjetivos['IdMovimientoCaso']}/desasociar-objetivo/${objetivo.IdObjetivo}`,
        {},
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.movimientoMostrarObjetivos.Objetivo = null
          }
          this.modalObjetivos = false
        }
      )
    },
    asociarANuevoObjetivo () {
      this.nuevoObjetivo.IdCaso = this.movimientoMostrarObjetivos.CasoCompleto.IdCaso
      request.Post('/objetivos', this.nuevoObjetivo, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.objetivosMostrar.push(Object.assign({}, this.nuevoObjetivo))
          this.objetivosCreadosSinMovimientos.push({
            Objetivo: this.nuevoObjetivo.Objetivo
          })
          this.modalNuevoObjetivo = false
          request.Post(
            `/movimientos/${this.movimientoMostrarObjetivos['IdMovimientoCaso']}/asociar-objetivo/${r.IdObjetivo}`,
            {},
            (r) => {
              if (r.Error) {
                this.$q.notify(r.Error)
              } else {
                this.movimientoMostrarObjetivos.Objetivo = this.nuevoObjetivo.Objetivo
                this.nuevoObjetivo.Objetivo = ''
              }
            }
          )
        }
      })
    },
    enviarWhatsapp () {
      request.Post('/mensajes', this.whatsapp, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.modalWhatsapp = false
          this.$q.notify({
            color: 'green',
            message: 'El mensaje fue enviado satisfactoriamente'
          })
        }
      })
    },
    cancelarAlta () {
      this.modalAlta = false
      this.nuevoMovimiento = {
        IdUsuario: 0,
        TiposMov: [],
        TipoMov: 0,
        UsuariosEstudio: [],
        IdEstudio: 0,
        Detalle: '',
        FechaEsperada: '',
        FechaAlta: '',
        colorSeleccionado: { label: 'Gestión Estudio', value: 'primary' },
        Caso: 0,
        Objetivo: '',
        Responsable: {}
      }
    },
    enviarMensaje (Mensaje) {
      request.Post(`/mensajes/enviar`, Mensaje, r => {
        if (!r.Error) {
          Notify.create('Movimiento comunicado correctamente')
          const UltMsjLeido = r.IdMensaje
          request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {
            if (p.Error) {
              Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
            }
          })
        } else {
          Notify.create('Falló al comunicar el movimiento. Razon: ' + r.Error)
        }
      })
    },
    nuevoChat (NuevoChat, Mensaje) {
      request.Post(`/chats/crear`, NuevoChat, r => {
        if (!r.Error) {
          Notify.create('Nuevo chat creado!')
          Mensaje.IdChat = r.IdChat
          request.Post(`/mensajes/enviar`, Mensaje, q => {
            if (!q.Error) {
              Notify.create('Movimiento comunicado correctamente')
              const UltMsjLeido = q.IdMensaje
              request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {
                if (p.Error) {
                  Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
                }
              })
            } else {
              Notify.create('Falló al comunicar el movimiento. Razon: ' + q.Error)
            }
          })
        } else {
          Notify.create('Falló al comunicar el movimiento. Razon: ' + r.Error)
        }
      })
    }
  }
}
</script>

<style>
.whitebg {
  margin-top: 1em;
  background-color: white;
}
.alta-movimiento {
  background-color: white;
  margin: 10px;
  padding: 10px;
}
q-banner {
  margin-top: 0.2em;
}
@keyframes destaco {
  0% {
    transform: scale(1, 1, 1);
  }
  100% {
    transform: scale(1.009, 1.009, 1.009);
  }
}
.q-banner:hover {
  animation: destaco 100ms ease-in;
  transition: 100ms;
  transform: scale(1.009, 1.009);
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
.q-banner-detail {
  float: right;
  float: left;
}
.q-expansion-item__content {
  padding: 0.1rem;
}
.caso-sin-pendiente {
  background: rgba(33, 186, 69, 1);
  background: -moz-linear-gradient(
    left,
    rgba(33, 186, 69, 1) 0%,
    rgba(0, 0, 0, 1) 100%
  );
  background: -webkit-gradient(
    left top,
    right top,
    color-stop(0%, rgba(33, 186, 69, 1)),
    color-stop(100%, rgba(0, 0, 0, 1))
  );
  background: -webkit-linear-gradient(
    left,
    rgba(33, 186, 69, 1) 0%,
    rgba(0, 0, 0, 1) 100%
  );
  background: -o-linear-gradient(
    left,
    rgba(33, 186, 69, 1) 0%,
    rgba(0, 0, 0, 1) 100%
  );
  background: -ms-linear-gradient(
    left,
    rgba(33, 186, 69, 1) 0%,
    rgba(0, 0, 0, 1) 100%
  );
  background: linear-gradient(
    to right,
    rgba(33, 186, 69, 1) 0%,
    rgba(0, 0, 0, 1) 100%
  );
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#21ba45', endColorstr='#000000', GradientType=1 );
}
.expansion-caso {
  margin: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  transition: 200ms;
}
.expansion-caso:hover {
  background: #f0f0f0;
}
.caso {
  cursor: pointer;
}
.caso:hover {
  animation: destaco 100ms ease-in;
  transition: 100ms;
  transform: scale(1.1, 1.1);
}
</style>
