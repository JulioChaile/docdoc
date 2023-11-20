<template>
  <div>
    <div
      style="background: transparent; padding:1em; margin-top:-.5em;"
    >
      <q-input
        outlined
        rounded
        class="busqueda-input shadow-3"
        label-color="grey-2"
        color="grey-4"
        v-model="busqueda"
        @keyup.enter="reOnLoad"
        :debounce="600"
        placeholder="Busca en mis clientes"
      >
        <template v-slot:prepend>
          <q-icon name="search" />
        </template>
        <template v-slot:append>
          <q-icon
            class="cursor-pointer"
            color="grey"
            name="more_vert"
            size="sm"
            @click="showFiltros()"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Más Filtros</q-tooltip>
          </q-icon>
        </template>
      </q-input>

      <div class="col-12 flex justify-center q-mt-sm">
        <q-btn
          :disable="loading"
          color="primary"
          size="sm"
          @click="reOnLoad"
        >
          Buscar
        </q-btn>
      </div>

      <div class="row opciones-container justify-center items-center">
        <div class="col-4">
          <q-toggle
            size="xs"
            v-model="orden"
          />
          <i style="font-size: 20px; height: 20px" class="fas fa-sort-alpha-down"></i>
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ordenar alfabeticamente</q-tooltip>
          <q-separator
            class="separador_titulo"
            color="black"
          />
        </div>
        <div class="col-4">
          <q-checkbox
            size="xs"
            color="cyan"
            v-model="verSinTel"
            @input="filtrarCasosSinTel()"
          />
          <q-icon
            name="r_phone_disabled"
            size="sm"
          />
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver solo casos sin telefono</q-tooltip>
          <q-separator
            class="separador_titulo"
            color="black"
          />
        </div>
        <div class="col-4">
          <q-toggle
            size="xs"
            v-model="agrupar"
          />
          <q-icon
            name="r_supervisor_account"
            size="md"
          />
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Agrupar por Estado Gestión</q-tooltip>
        </div>
      </div>
      <div class="flex justify-center q-mt-lg">
          <div
            class="cursor-pointer text-caption text-grey"
            @click="exportar"
          >
            <q-icon size="sm" name="file_download" color="positive" />
            Descargar como Excel
          </div>
      </div>
      <div class="row justify-center q-mt-lg" v-if="Object.keys(CasosMensaje).length > 0">
        <q-btn
          style="font-size:12px"
          push
          label="Mensaje Global"
          @click="ModalMensaje = true"
          color="primary"
        />
      </div>
      <q-expansion-item ref="expansionFiltros" class="filtros-container" label="Filtros">
        <q-btn-group flat push>
          <q-btn
            style="font-size:12px"
            push
            label="Todos"
            @click="tipoBusqueda = 't'"
            v-bind:outline="tipoBusqueda === 't'"
            color="primary"
          />
          <q-btn
            style="font-size:12px"
            push
            label="Personas"
            @click="tipoBusqueda = 'p'"
            v-bind:outline="tipoBusqueda === 'p'"
            color="primary"
          />
          <!--q-btn
          style="font-size:12px"
          push
          label="Juzgados"
          @click="tipoBusqueda = 'j'"
          v-bind:outline="tipoBusqueda === 'j'"
          color="primary"
          />
          <q-btn
          style="font-size:12px"
          push
          label="Tipos de Casos"
          @click="tipoBusqueda = 'c'"
          v-bind:outline="tipoBusqueda === 'c'"
          color="primary"
          /-->
          <q-btn
            style="font-size:12px"
            push
            label="N° Expediente"
            @click="tipoBusqueda = 'e'"
            v-bind:outline="tipoBusqueda === 'e'"
            color="primary"
          />
          <q-btn
            style="font-size:12px"
            push
            label="Patente"
            @click="tipoBusqueda = 'd'"
            v-bind:outline="tipoBusqueda === 'd'"
            color="primary"
          />
        </q-btn-group>
        <q-checkbox v-model="verAlta" label="No archivados" @input="filtrarArchivados()" />
        <q-checkbox
            v-model="verArchivados"
            label="Archivados"
            @input="filtrarArchivados()"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verActivos"
            label="Activos"
            @input="filtrar()"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verFinalizados"
            label="Finalizados"
            @input="filtrar()"
            style="margin-left: 10px"
        />
        <br>
        <q-checkbox
            v-model="verOrigen"
            label="Origen"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verCompetencia"
            label="Competencia"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verTipoCaso"
            label="Tipo de Caso"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verAmbito"
            label="Tipo de Proceso"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verNominacion"
            label="Nominacion"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verEstadoAmbitoGestion"
            label="Estado de Proceso"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verDefiende"
            label="Defiende"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verEstado"
            label="Estado"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verId"
            label="N° Expediente"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verEtiquetas"
            label="Etiquetas"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verUltMov"
            label="Ult. Movimiento"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verCiaSeguro"
            label="Compañia Seguro"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verUltMsj"
            label="Ult. Mensaje"
            style="margin-left: 10px"
        />
        <q-checkbox
            v-model="verApp"
            label="App"
            style="margin-left: 10px"
        />
      </q-expansion-item>
      <div style="margin-top: 20px;">
        <div style="display: flex">
          <q-checkbox
            v-model="selectAll"
            class="col-auto check_casilla"
          />
          <div
            class="row titulos_container q-banner"
          >
            <div
              class="col-sm-1 casilla_container"
              v-if="verOrigen"
            >
              Origen
              <q-select
                v-model="Origen"
                multiple
                :options="opcionesOrigenes"
                ref="selectOrigen"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('Origen')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Origen</q-tooltip>
              </q-icon>
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col casilla_container"
              v-if="verCompetencia"
            >
              Competencia
              <q-select
                v-model="Competencia"
                multiple
                :options="opcionesCompetencias"
                ref="selectCompetencia"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('Competencia')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Competencia</q-tooltip>
              </q-icon>
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col casilla_container"
              v-if="verTipoCaso"
            >
              Tipo de Caso
              <q-select
                v-model="TipoCaso"
                multiple
                :options="opcionesTiposCaso"
                ref="selectTipoCaso"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('TipoCaso')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Tipo de Caso</q-tooltip>
              </q-icon>
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col-sm-3 col-xs-12 casilla_container cliente"
            >
              Cliente
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col casilla_container"
              v-if="verAmbito"
            >
              Proceso
              <q-select
                v-model="Ambito"
                multiple
                :options="opcionesAmbitosGestion"
                ref="selectAmbitoGestion"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('AmbitoGestion')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Tipo de Proceso</q-tooltip>
              </q-icon>
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col casilla_container"
              v-if="verNominacion"
            >
              Nominación
              <q-select
                v-model="Nominacion"
                multiple
                :options="opcionesNominaciones"
                ref="selectNominacion"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('Nominacion')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Nominación</q-tooltip>
              </q-icon>
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col-sm-2 casilla_container"
              v-if="verEstadoAmbitoGestion"
            >
              Estado Proceso
              <q-select
                v-model="EstadoAmbitoGestion"
                multiple
                :options="opcionesEstados"
                ref="selectEstadoAmbitoGestion"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('EstadoAmbitoGestion')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Estado de Proceso</q-tooltip>
              </q-icon>
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col-sm-2 casilla_container"
              v-if="verDefiende"
            >
              Defiende
              <q-select
                v-model="Defiende"
                :options="opcionesDefiende"
                ref="selectDefiende"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('Defiende')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Actor / Demandado</q-tooltip>
              </q-icon>
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col casilla_container"
              v-if="verEstado"
            >
              Estado
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col casilla_container"
              v-if="verId"
            >
              N° Expediente
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              class="col-sm-1 casilla_container"
            >
              Derivado
            </div>
            <div
              v-if="verEtiquetas"
              class="col casilla_container"
            >
              Etiquetas
              <q-select
                v-model="Etiqueta"
                multiple
                :options="opcionesEtiquetas"
                ref="selectEtiquetas"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('Etiquetas')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por etiquetas</q-tooltip>
              </q-icon>
            </div>
            <div
              v-if="verUltMov"
              class="col-sm-1 casilla_container"
            >
              Ult. Movimiento
            </div>
            <div
              class="col-sm-2 casilla_container"
              v-if="verCiaSeguro"
            >
              Compañia Seguro
              <q-select
                v-model="CiaSeguro"
                multiple
                :options="opcionesCiaSeguro"
                ref="selectCiaSeguro"
              />
              <q-icon
                rounded
                color="grey"
                name="more_vert"
                size="sm"
                @click="showSelect('CiaSeguro')"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Compañia</q-tooltip>
              </q-icon>
              <!--q-separator
                class="separador_titulo"
                color="black"
              /-->
            </div>
            <div
              v-if="verUltMsj"
              class="col-sm-1 casilla_container"
            >
              Ult. Mensaje
            </div>
            <div
              v-if="verApp"
              class="col-sm-1 casilla_container"
            >
              App
            </div>
          </div>
        </div>
      </div>
      <div v-if="loading">
          <Loading />
      </div>
      <q-infinite-scroll
        :disable="noHayMasCasos || loading"
        @load="onLoad"
        class="full-width casos__container"
        :offset="300"
      >
        <p v-if="buscarCaso.length === 0 && !loading">No hay casos que coincidan con el criterio de busqueda.</p>
        <div
          v-for="caso in buscarCaso"
          :key="caso.IdCaso"
        >
          <q-checkbox
            v-model="caso.model"
            @input="caso.model ? CasosMensaje[caso.IdCaso] = caso : delete CasosMensaje[caso.IdCaso]"
            class="check_casilla"
          />
          <div class="filas_container q-banner">
            <div class="row filas">
              <div
                class="col-sm-1"
                v-if="verOrigen"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Origen</q-tooltip>
                {{caso.Origen ? caso.Origen : "No hay un origen asignado."}}
              </div>
              <div
                class="col-sm-1"
                v-if="verCompetencia"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Competencia</q-tooltip>
                {{caso.Competencia ? caso.Competencia : "No hay una competencia asignada."}}
              </div>
              <div
                class="col"
                v-if="verTipoCaso"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Tipo de Caso</q-tooltip>
                <span class="text-bold" :style="`color: ${caso.ColorTipoCaso}`">{{ caso.TipoCaso }}</span>
              </div>
              <div
                class="col-sm-3 cursor-pointer cliente relative-position"
                @click="abrirCaso(caso.IdCaso)"
              >
                <q-badge v-if="caso.Duplicado === 'S'" color="grey" label="DUPLICADO" style="position: absolute !important;" class="absolute-bottom" />
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">{{ caso.Caratula ? caso.Caratula : 'Sin Caratula' }}</q-tooltip>
                <span
                  class="q-subheading"
                  style="color: #1B43F0"
                >{{ caratula(caso.Caratula) }}</span>
              </div>
              <div
                class="col"
                v-if="verAmbito"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Tipo de Proceso</q-tooltip>
                <span class="text-bold" :style="`color: ${caso.ColorJuzgado}`">{{ caso.Juzgado }}</span>
              </div>
              <div
                class="col"
                v-if="verNominacion"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Nominacion</q-tooltip>
                <span>{{ nominacion(caso.Nominacion) }}</span>
              </div>
              <div
                class="col-sm-2 column"
                v-if="verEstadoAmbitoGestion"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Estado de Proceso</q-tooltip>
                {{caso.EstadoAmbitoGestion ? caso.EstadoAmbitoGestion : 'No hay un estado asignado.'}}
                <br>
                <span style="color: #1B43F0">{{diasCambioEstado(caso.FechaEstado)}}</span>
              </div>
              <div
                class="col-sm-2 column"
                v-if="verDefiende"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Defiende al {{ defiende(caso.Defiende) }}</q-tooltip>
                {{ defiende(caso.Defiende) }}
              </div>
              <div
                class="col"
                v-if="verEstado"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Estado del Caso</q-tooltip>
                {{caso.EstadoCaso}}
              </div>
              <div
                class="col"
                v-if="verId"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">N° Expediente</q-tooltip>
                {{caso.NroExpediente ? caso.NroExpediente : 'Sin expediente'}}
              </div>
              <div
                class="col-sm-1 cursor-pointer"
                @click="verComparticiones(caso)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">{{ caso.Comparticiones ? 'Ver Comparticiones' : 'Caso sin derivar' }}</q-tooltip>
                <q-icon
                  v-if="caso.Comparticiones"
                  name="r_check_circle"
                  size="sm"
                  :style="caso.Comparticiones ? 'color: #49C00F;' : 'color: #49C00F; filter: opacity(25%)'"
                />
                <q-icon
                  v-if="!caso.Comparticiones"
                  name="r_cancel"
                  size="sm"
                  :style="!caso.Comparticiones ? 'color: #B1000E;' : 'color: #B1000E; filter: opacity(25%)'"
                />
                <q-icon
                  v-if="caso.Comparticiones"
                  color="grey"
                  name="more_vert"
                  size="20px"
                  style="position: absolute; right: calc(50% - 35px)"
                />
              </div>
              <div
                v-if="verEtiquetas"
                class="col cursor-pointer"
                @click="verEtiquetasCaso(caso)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver etiquetas del caso</q-tooltip>
                <q-icon
                  name="label"
                  color="orange"
                  size="sm"
                />
              </div>
              <div
                class="col-sm-1 column cursor-pointer"
                v-if="verUltMov"
                @click="editarMovimiento(caso.UltimoMovimiento, caso.Caratula)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ultimo Movimiento</q-tooltip>
                {{caso.UltimoMovimiento ? detalleUltMov(caso.UltimoMovimiento) : 'Sin movimientos'}}
                <br>
                <span v-if="caso.UltimoMovimiento" style="color: #1B43F0">{{diasCambioEstado(caso.UltimoMovimiento.FechaAlta, false)}}</span>
              </div>
              <div
                class="col"
                v-if="verCiaSeguro"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Compañia de Seguro</q-tooltip>
                {{caso.CiaSeguro}}
              </div>
              <div
                class="col-sm-1 column"
                v-if="verUltMsj"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">{{ caso.FechaUltMsj || 'Sin mensajes enviados' }}</q-tooltip>
                <span style="color: #1B43F0">{{ caso.FechaUltMsj ? diasCambioEstado(caso.FechaUltMsj) : 'Sin mensajes enviados' }}</span>
              </div>
              <div
                class="col-sm-1 column"
                v-if="verApp"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">{{ caso.App ? 'Cliente con app instalada' : 'App aun no instalada' }}</q-tooltip>
                <q-icon
                  :name="caso.App ? 'r_check_circle' : 'r_cancel'"
                  size="sm"
                  :color="caso.App ? 'positive' : 'negative'"
                />
              </div>
            </div>
          </div>
        </div>
        <template v-slot:loading>
            <div class="row justify-center q-my-md">
              <q-spinner-dots
                color="primary"
                size="100px"
                style="position: fixed; bottom: 10px; left: 50%"
              />
            </div>
        </template>
      </q-infinite-scroll>
    </div>

    <!-- Modal Comparticiones -->
    <q-dialog v-model="modalComparticiones">
      <Comparticiones
        :comparticiones="casoCompartido.Comparticiones"
        :IdCaso="casoCompartido.IdCaso"
        @cerrar="modalComparticiones = false"
        @eliminarComparticiones="eliminarComparticiones"
      />
    </q-dialog>

    <!-- Modal MensajeGlobal -->
    <q-dialog v-model="ModalMensaje">
      <MensajeGlobal
        :Casos="casosMensajeGlobal()"
        @MensajeEnviado="mensajeEnviado()"
        @cerrar="ModalMensaje = false"
      />
    </q-dialog>

    <!-- Modal Etiquetas -->
    <q-dialog v-model="modalEtiquetas">
      <TagsCaso
        :IdCaso="casoEtiquetas.IdCaso"
        :EtiquetasCaso="casoEtiquetas.EtiquetasCaso || []"
      />
    </q-dialog>

    <!-- Modal Editar Movimiento -->
    <q-dialog v-model="ModalMovimiento">
      <q-card>
        <q-item class="relative-position" style="background-color: black;">
          <span class="q-subheading" style="color:white;">Editar Movimiento</span>
          <q-icon
            name="close"
            class="absolute-right cursor-pointer"
            color="negative"
            size="lg"
            @click="ModalMovimiento = false"
          />
        </q-item>
        <EditarMovimiento
          v-if="ModalMovimiento"
          :movimiento="this.movimientoEditar"
          @cancelarEdicion="ModalMovimiento = false"
          @edicionTerminada="ModalMovimiento = false"
        />
      </q-card>
    </q-dialog>

    <!-- Modal Excel -->
    <q-dialog v-model="ModalExcel">
      <div v-if="loadingExcel" class="column q-pa-lg items-center">
        <Loading />
        Espere unos instantes por favor
      </div>
      <ExportExcel
        v-else
        :ArrayInicial="ArrayExcel"
        :name="'Casos'"
      />
    </q-dialog>
  </div>
</template>

<script>
import moment from 'moment'
import request from '../request'
import Loading from '../components/Loading'
import MensajeGlobal from '../components/MensajeGlobal'
import Comparticiones from '../components/GrillaCasos/Comparticiones'
import TagsCaso from '../components/Compartidos/TagsCaso'
import EditarMovimiento from '../components/EditarMovimiento'
import ExportExcel from '../components/Compartidos/ExportExcel'
import { Notify } from 'quasar'
export default {
  components: {
    Loading,
    Comparticiones,
    MensajeGlobal,
    TagsCaso,
    EditarMovimiento,
    ExportExcel
  },
  name: 'GrillaCasos',
  data () {
    return {
      casos: [],
      casosAux: [],
      casosTodos: [],
      casosSinTel: [],
      casoCompartido: {},
      show: {},
      busqueda: '',
      tipoBusqueda: 't', // t (Todos) - p (Personas) - j (Juzgados) - c (Tipo de Caso) - d (Patente)
      estado: 'A',
      verAlta: true,
      verArchivados: false,
      noHayMasCasos: false,
      modo: 'casos__grilla',
      loading: true,
      objetivos: [],
      idCasos: [],
      filtros: false,
      orden: false,
      agrupar: false,
      verId: false,
      verCompetencia: true,
      verTipoCaso: true,
      verEstado: false,
      verOrigen: true,
      verEstadoAmbitoGestion: true,
      verAmbito: true,
      verNominacion: true,
      verActivos: true,
      verPendientes: false,
      verFinalizados: false,
      verSinTel: false,
      verCiaSeguro: false,
      verEtiquetas: false,
      verUltMov: false,
      verUltMsj: false,
      verApp: false,
      verDefiende: false,
      selectAll: false,
      ModalMensaje: false,
      CasosMensaje: {},
      estadoCaso: 'AP',
      TiposCaso: [],
      Origenes: [],
      Competencias: [],
      Estados: [],
      CiaSeguro: [],
      AmbitosGestion: [],
      Nominaciones: [],
      Etiquetas: [],
      TipoCaso: ['Todos'],
      Competencia: ['Todos'],
      Origen: ['Todos'],
      EstadoAmbitoGestion: ['Todos'],
      CiasSeguro: ['Todos'],
      Ambito: ['Todos'],
      Nominacion: ['Todos'],
      Etiqueta: ['Todos'],
      modalComparticiones: false,
      EstadosTodos: [],
      Defiende: {
        label: 'Todos',
        value: 'Todos'
      },
      opcionesDefiende: [
        {
          label: 'Actor',
          value: 'A'
        },
        {
          label: 'Demandado',
          value: 'D'
        },
        {
          label: 'Sin Anunciar',
          value: ''
        },
        {
          label: 'Todos',
          value: 'Todos'
        }
      ],
      modalEtiquetas: false,
      casoEtiquetas: {
        IdCaso: 0,
        EtiquetasCaso: []
      },
      movimientoEditar: {},
      ModalMovimiento: false,
      loadingExcel: false,
      ArrayExcel: [],
      ModalExcel: false
    }
  },
  created () {
    request.Get('/casos/buscar', { Offset: this.casosTodos.length, Cadena: this.busqueda, Tipo: this.tipoBusqueda.toUpperCase(), Limit: 50 }, (r) => {
      if (!r.Error) {
        r.forEach((c) => {
          this.idCasos.push(c.IdCaso)
          try {
            c.PersonasCaso = JSON.parse(c.PersonasCaso)
          } catch (error) {
            console.log(error)
            c.PersonasCaso = []
          }

          c.PersonasCaso.forEach(p => {

                if (p.Parametros && !Array.isArray(p.Parametros)) {
                  if (!c.CiaSeguro && p.Parametros.Seguro) {
                    c.CiaSeguro = p.Parametros.Seguro.CiaSeguro
                  }
                }
              })

          c.Comparticiones = JSON.parse(c.Comparticiones)
          c.EtiquetasCaso = JSON.parse(c.EtiquetasCaso)
          c.UltimoMovimiento = JSON.parse(c.UltimoMovimiento)

          c.model = false

          this.casos.push(c)
          this.casosTodos.push(c)

          let check = false
          c.PersonasCaso.forEach(p => {
            if (p.Telefonos) { check = true }
          })

          if (!check) { this.casosSinTel.push(c) }
        })
        this.casos = this.casos.sort((a, b) => {
          if (a.FechaAlta < b.FechaAlta) {
            return 1
          }
          if (a.FechaAlta > b.FechaAlta) {
            return -1
          }
          return 0
        })
      }
      this.loading = false
    })
    request.Get('/tipos-caso', {}, r => {
      if (!r.Error) {
        r.forEach(c => {
          this.TiposCaso.push(c.TipoCaso)
        })
      }
    })
    request.Get('/casos/listar-etiquetas', {}, r => {
      if (!r.Error) {
        r.forEach(c => {
          this.Etiquetas.push(c.Etiqueta)
        })
      }
    })
    request.Get('/origenes', {}, r => {
      if (!r.Error) {
        r.forEach(c => {
          this.Origenes.push(c.Origen)
        })
      }
    })
    request.Get('/competencias', {}, r => {
      if (!r.Error) {
        r.forEach(c => {
          this.Competencias.push(c.Competencia)
        })
      }
    })
    request.Get('/estado-ambito-gestion', {}, r => {
      if (!r.Error) {
        this.Estados = r
        this.Estados.forEach(e => {
          e.Juzgados = JSON.parse(e.Juzgados)
        })
      }
    })
    request.Get(`/casos/buscar-contacto-parametros`, {offset: 0, limit: 1000, cadena: '', tipo: 'CS'}, r => {
        this.CiasSeguro = r.map(c => c.CiaSeguro)
    })
    let IdsJuzgados = []
    request.Get('/juzgados', {IncluyeBajas: 'S'}, r => {
      if (!r.Error) {
        r.forEach(c => {
          this.AmbitosGestion.push(c.Juzgado)
          IdsJuzgados.push(c.IdJuzgado)
        })
        request.Get('/juzgados/estados', {}, r => {
          if (!r.Error) {
            this.EstadosTodos = r
          }
        })
        request.Get(`/nominaciones?IdsJuzgado=${JSON.stringify(IdsJuzgados)}`, {}, t => {
          if (!t.Error) {
            this.Nominaciones = ['0 Pendiente', '1°', '2°', '3°', '4°', '5°', '6°', '7°', '8°', '9°']
            Object.values(t).forEach(c => {
              if (c.length > 0) {
                c.forEach(p => {
                  if (isNaN(parseInt(p.Nominacion[0]))) {
                    this.Nominaciones.push(p.Nominacion)
                  }
                })
              }
            })
          }
        })
      }
    })
  },
  computed: {
    opcionesEstados () {
      let result = []
      if (this.Estados && this.Estados.length) {
        if (this.Ambito.includes('Todos')) {
          result = this.Estados.map(e => e.EstadoAmbitoGestion)
          result.push('Sin estado', 'Todos')
        } else {
          this.Ambito.forEach(a => {
            this.Estados.filter(e => {
              const i = e.Juzgados.findIndex(j => j === a)

              if (i >= 0) {
                const estado = e.EstadoAmbitoGestion

                if (!result.includes(estado)) {
                  result.push(estado)
                }
              }
            })
          })
          result.push('Sin estado', 'Todos')
        }
      }
      return result
    },
    opcionesCiaSeguro () {
      let result = []
      if (this.CiasSeguro && this.CiasSeguro.length) {
        result = this.CiasSeguro
        result.push('Sin compañia', 'Todos')
      }
      return result
    },
    opcionesTiposCaso () {
      let result = []
      if (this.TiposCaso && this.TiposCaso.length) {
        result = this.TiposCaso
        result.push('Todos')
      }
      return result
    },
    opcionesEtiquetas () {
      let result = []
      if (this.Etiquetas && this.Etiquetas.length) {
        result = this.Etiquetas
        result.push('Todos')
      }
      return result.sort()
    },
    opcionesOrigenes () {
      let result = []
      if (this.Origenes && this.Origenes.length) {
        result = this.Origenes
        result.push('Sin origen', 'Todos')
      }
      return result
    },
    opcionesCompetencias () {
      let result = []
      if (this.Competencias && this.Competencias.length) {
        result = this.Competencias
        result.push('Sin competencia', 'Todos')
      }
      return result
    },
    opcionesAmbitosGestion () {
      let result = []
      if (this.AmbitosGestion && this.AmbitosGestion.length) {
        result = this.AmbitosGestion
        result.push('Todos')
      }
      return result
    },
    opcionesNominaciones () {
      let result = []
      if (this.Nominaciones && this.Nominaciones.length) {
        result = this.Nominaciones
        result.push('Sin nominacion', 'Todos')
      }
      return result
    },
    buscarCaso () {
      let filter = this.casos.filter(
        (c) => c.Estado === this.estado || this.estado === 'T' && this.estado !== 'E'
      )
      filter = this.filtrarDatos(filter)
      filter.forEach(c => {
        if (c.Caratula) {
          let arrayCaratula = []
          arrayCaratula = c.Caratula.split(' ')

          if (arrayCaratula.indexOf(',') >= 0) {
            const i = arrayCaratula.indexOf(',')
            const p = arrayCaratula[i - 1]
            arrayCaratula.splice(i, 1)
            arrayCaratula.splice(i - 1, 0, p + ',')
          }

          c.Caratula = arrayCaratula.map(word => {
            return word ? word[0].toUpperCase() + word.slice(1).toLowerCase() : ''
          }).join(' ')
        }
      })
      var hash = {}
      filter = filter.filter(c => {
        var exists = !hash[c.IdCaso]
        hash[c.IdCaso] = true
        return exists
      })
      let casos = []
      switch (this.estadoCaso) {
        case 'T':
          casos = filter
          break
        case 'AP':
          filter.forEach(c => {
            if (c.EstadoCaso !== 'Finalizado') {
              casos.push(c)
            }
          })
          break
        case 'PF':
          filter.forEach(c => {
            if (c.EstadoCaso === 'Finalizado' || c.EstadoCaso === 'Cliente pendiente') {
              casos.push(c)
            }
          })
          break
        case 'AF':
          filter.forEach(c => {
            if (c.EstadoCaso !== 'Cliente pendiente') {
              casos.push(c)
            }
          })
          break
        case 'A':
          filter.forEach(c => {
            if (c.EstadoCaso !== 'Finalizado' && c.EstadoCaso !== 'Cliente pendiente') {
              casos.push(c)
            }
          })
          break
        case 'P':
          filter.forEach(c => {
            if (c.EstadoCaso === 'Cliente pendiente') {
              casos.push(c)
            }
          })
          break
        case 'F':
          filter.forEach(c => {
            if (c.EstadoCaso === 'Finalizado') {
              casos.push(c)
            }
          })
          break
      }

      if (this.agrupar && Object.keys(this.EstadosTodos).length !== 0) {
        let casosAux = []
        let agrupacion = {}

        this.AmbitosGestion.forEach(j => {
          agrupacion[j] = []
          agrupacion[j].push({
            Nulo: [],
            Orden: 100
          })
        })
        agrupacion['Nulo'] = []
        agrupacion['Nulo'].push({
          Nulo: [],
          Orden: 100
        })
        this.EstadosTodos.forEach(e => {
          if (e.EstadoAmbitoGestion) {
            agrupacion[e.Juzgado].push({
              [e.EstadoAmbitoGestion]: [],
              Orden: e.Orden
            })
          }
        })

        this.casos.forEach(c => {
          const i = agrupacion[c.Juzgado ? c.Juzgado : 'Nulo'].findIndex(e => e[c.EstadoAmbitoGestion])
          const j = agrupacion[c.Juzgado ? c.Juzgado : 'Nulo'].findIndex(e => e['Nulo'])

          if (i >= 0) {
            agrupacion[c.Juzgado ? c.Juzgado : 'Nulo'][i][c.EstadoAmbitoGestion].push(c)
          } else if (!c.EstadoAmbitoGestion) {
            agrupacion[c.Juzgado ? c.Juzgado : 'Nulo'][j]['Nulo'].push(c)
          }
        })

        this.AmbitosGestion.forEach(j => {
          agrupacion[j].sort((a, b) => a.Orden - b.Orden)

          agrupacion[j].forEach(a => {
            const estado = Object.keys(a).filter(c => c !== 'Orden')[0]

            a[estado].sort((a, b) => { return new Date(b.FechaEstado).getTime() > new Date(a.FechaEstado).getTime() ? 1 : -1 })

            for (var i = a[estado].length - 1; i >= 0; i--) {
              if (a[estado].indexOf(a[estado][i]) !== i) { a[estado].splice(i, 1) }
            }

            casosAux = casosAux.concat(a[estado])
          })
        })
        agrupacion['Nulo'].forEach(a => {
          const estado = Object.keys(a).filter(c => c !== 'Orden')[0]

          a[estado].sort((a, b) => { return new Date(b.FechaEstado).getTime() > new Date(a.FechaEstado).getTime() ? 1 : -1 })

          for (var i = a[estado].length - 1; i >= 0; i--) {
            if (a[estado].indexOf(a[estado][i]) !== i) { a[estado].splice(i, 1) }
          }

          casosAux = casosAux.concat(a[estado])
        })

        for (var i = casosAux.length - 1; i >= 0; i--) {
          if (casosAux.indexOf(casosAux[i]) !== i) { casosAux.splice(i, 1) }
        }

        return this.filtrarDatos(casosAux)
      }

      return casos
    }
  },
  watch: {
    orden () {
      if (this.orden) {
        this.casos = this.casos.sort((a, b) => {
          if (a.Caratula.toLowerCase() > b.Caratula.toLowerCase()) {
            return 1
          }
          if (a.Caratula.toLowerCase() < b.Caratula.toLowerCase()) {
            return -1
          }
          return 0
        })
      } else {
        this.casos = this.casos.sort((a, b) => {
          if (a.FechaAlta < b.FechaAlta) {
            return 1
          }
          if (a.FechaAlta > b.FechaAlta) {
            return -1
          }
          return 0
        })
      }
    },
    selectAll () {
      this.buscarCaso.forEach(c => {
        c.model = this.selectAll
        this.selectAll ? this.CasosMensaje[c.IdCaso] = c : delete this.CasosMensaje[c.IdCaso]
      })
    }
  },
  methods: {
    reOnLoad () {
      this.casos = []
      this.casosTodos = []
      this.casosSinTel = []
      this.loading = true
      this.onLoad(0, () => {})
    },
    defiende (value) {
      if (!value) return 'Sin Anunciar'

      return value === 'A' ? 'Actor' : 'Demandado'
    },
    isMobile () {
      if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
          navigator.userAgent
        )
      ) {
        return true
      } else {
        return false
      }
    },
    onLoad (index, done) {
      request.Get(
        '/casos/buscar',
        {
          Offset: this.casosTodos.length,
          Cadena: this.busqueda,
          Tipo: this.tipoBusqueda.toUpperCase(),
          Limit: 50
        },
        (r) => {
          if (!r.Error) {
            r.forEach((c) => {
              try {
                c.PersonasCaso = JSON.parse(c.PersonasCaso)
              } catch (error) {
                console.log(error)
                c.PersonasCaso = []
              }

              

              c.PersonasCaso.forEach(p => {

                if (p.Parametros) {
                  if (!c.CiaSeguro && p.Parametros.Seguro) {
                    c.CiaSeguro = p.Parametros.Seguro.CiaSeguro
                  }
                }
              })

              c.Comparticiones = JSON.parse(c.Comparticiones)
              c.EtiquetasCaso = JSON.parse(c.EtiquetasCaso)
              c.UltimoMovimiento = JSON.parse(c.UltimoMovimiento)

              c.model = false

              c.App = false

              c.PersonasCaso.forEach(p => {
                if (p.TokenApp) c.App = true
              })

              this.casos.push(c)
              this.casosTodos.push(c)

              let check = false
              c.PersonasCaso.forEach(p => {
                if (p.Telefonos) { check = true }
              })

              if (!check) { this.casosSinTel.push(c) }
            })
            this.filtrarCasosSinTel()
            this.casos = this.casos.sort((a, b) => {
              if (a.FechaAlta < b.FechaAlta) {
                return 1
              }
              if (a.FechaAlta > b.FechaAlta) {
                return -1
              }
              return 0
            })
          }
          for (var i = this.casos.length - 1; i >= 0; i--) {
            if (this.casos.indexOf(this.casos[i]) !== i) { this.casos.splice(i, 1) }
          }
          for (var j = this.casosTodos.length - 1; j >= 0; j--) {
            if (this.casosTodos.indexOf(this.casos[j]) !== j) { this.casosTodos.splice(j, 1) }
          }
          this.loading = false
          this.noHayMasCasos = r.length === 0
          done()
        }
      )
    },
    abrirCaso (id) {
      let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
      window.open(routeData.href, '_blank')
    },
    filtrarArchivados () {
      if (this.verAlta && this.verArchivados) {
        this.estado = 'T'
      } else if (this.verArchivados) {
        this.estado = 'R'
      } else if (this.verAlta) {
        this.estado = 'A'
      } else {
        this.estado = 'A'
      }
    },
    filtrar () {
      switch (true) {
        case this.verActivos && this.verPendientes && this.verFinalizados:
          this.estadoCaso = 'T'
          break
        case this.verActivos && this.verPendientes && !this.verFinalizados:
          this.estadoCaso = 'AP'
          break
        case this.verActivos && !this.verPendientes && this.verFinalizados:
          this.estadoCaso = 'AF'
          break
        case !this.verActivos && this.verPendientes && this.verFinalizados:
          this.estadoCaso = 'PF'
          break
        case this.verActivos && !this.verPendientes && !this.verFinalizados:
          this.estadoCaso = 'A'
          break
        case !this.verActivos && this.verPendientes && !this.verFinalizados:
          this.estadoCaso = 'P'
          break
        case !this.verActivos && !this.verPendientes && this.verFinalizados:
          this.estadoCaso = 'F'
          break
        case !this.verActivos && !this.verPendientes && !this.verFinalizados:
          this.estadoCaso = 'T'
          break
      }
    },
    filtrarDatos (casos) {
      let filter = casos
      filter = this.filtrarTipoCaso(filter)
      filter = this.filtrarEstadoAmbitoGestion(filter)
      filter = this.filtrarCompetencia(filter)
      filter = this.filtrarOrigen(filter)
      filter = this.filtrarAmbito(filter)
      filter = this.filtrarNominacion(filter)
      filter = this.filtrarEtiqueta(filter)
      filter = this.filtrarCiaSeguro(filter)
      filter = this.filtrarDefiende(filter)
      return filter
    },
    filtrarDefiende (filter) {
      const { value } = this.Defiende

      if (!value) return filter.filter(caso => !caso.Defiende)

      if (value === 'Todos') return filter

      return filter.filter(caso => caso.Defiende === value)
    },
    filtrarEtiqueta (filter) {
      if (this.Etiqueta.length === 0 || this.Etiqueta[this.Etiqueta.length - 1] === 'Todos') {
        this.Etiqueta = ['Todos']
      }
      if (this.Etiqueta.length > 1 && this.Etiqueta.includes('Todos')) {
        const i = this.TipoCaso.indexOf('Todos')
        this.Etiqueta.splice(i, 1)
      }
      if (!this.Etiqueta.includes('Todos')) {
        filter = filter.filter(f => {
          const etiquetasCaso = f.EtiquetasCaso || []
          let check = false

          etiquetasCaso.forEach(e => {
            if (this.Etiqueta.includes(e.Etiqueta)) {
              check = true
            }
          })

          return check
        })
      }
      return filter
    },
    filtrarTipoCaso (filter) {
      if (this.TipoCaso.length === 0 || this.TipoCaso[this.TipoCaso.length - 1] === 'Todos') {
        this.TipoCaso = ['Todos']
      }
      if (this.TipoCaso.length > 1 && this.TipoCaso.includes('Todos')) {
        const i = this.TipoCaso.indexOf('Todos')
        this.TipoCaso.splice(i, 1)
      }
      if (!this.TipoCaso.includes('Todos')) {
        filter = filter.filter(f => this.TipoCaso.includes(f.TipoCaso))
      }
      return filter
    },
    filtrarCiaSeguro (filter) {
      if (this.CiaSeguro.length === 0 || this.CiaSeguro[this.CiaSeguro.length - 1] === 'Todos') {
        this.CiaSeguro = ['Todos']
      }
      if (this.CiaSeguro.length > 1 && this.CiaSeguro.includes('Todos')) {
        const i = this.CiaSeguro.indexOf('Todos')
        this.CiaSeguro.splice(i, 1)
      }
      if (!this.CiaSeguro.includes('Todos')) {
        filter = filter.filter(f => this.CiaSeguro.includes(f.CiaSeguro))
      }
      return filter
    },
    filtrarEstadoAmbitoGestion (filter) {
      if (this.EstadoAmbitoGestion.length === 0 || this.EstadoAmbitoGestion[this.EstadoAmbitoGestion.length - 1] === 'Todos') {
        this.EstadoAmbitoGestion = ['Todos']
      }
      if (this.EstadoAmbitoGestion.length > 1 && this.EstadoAmbitoGestion.includes('Todos')) {
        const i = this.EstadoAmbitoGestion.indexOf('Todos')
        this.EstadoAmbitoGestion.splice(i, 1)
      }
      if (!this.EstadoAmbitoGestion.includes('Todos')) {
        this.EstadoAmbitoGestion.includes('Sin estado')
          ? filter = filter.filter(f => this.EstadoAmbitoGestion.includes(f.EstadoAmbitoGestion) || !f.EstadoAmbitoGestion)
          : filter = filter.filter(f => this.EstadoAmbitoGestion.includes(f.EstadoAmbitoGestion))
      }
      return filter
    },
    filtrarOrigen (filter) {
      if (this.Origen.length === 0 || this.Origen[this.Origen.length - 1] === 'Todos') {
        this.Origen = ['Todos']
      }
      if (this.Origen.length > 1 && this.Origen.includes('Todos')) {
        const i = this.Origen.indexOf('Todos')
        this.Origen.splice(i, 1)
      }
      if (!this.Origen.includes('Todos')) {
        this.Origen.includes('Sin origen')
          ? filter = filter.filter(f => this.Origen.includes(f.Origen) || !f.Origen)
          : filter = filter.filter(f => this.Origen.includes(f.Origen))
      }
      return filter
    },
    filtrarCompetencia (filter) {
      if (this.Competencia.length === 0 || this.Competencia[this.Competencia.length - 1] === 'Todos') {
        this.Competencia = ['Todos']
      }
      if (this.Competencia.length > 1 && this.Competencia.includes('Todos')) {
        const i = this.Competencia.indexOf('Todos')
        this.Competencia.splice(i, 1)
      }
      if (!this.Competencia.includes('Todos')) {
        this.Competencia.includes('Sin origen')
          ? filter = filter.filter(f => this.Competencia.includes(f.Competencia) || !f.Competencia)
          : filter = filter.filter(f => this.Competencia.includes(f.Competencia))
      }
      return filter
    },
    filtrarAmbito (filter) {
      if (this.Ambito.length === 0 || this.Ambito[this.Ambito.length - 1] === 'Todos') {
        this.Ambito = ['Todos']
      }
      if (this.Ambito.length > 1 && this.Ambito.includes('Todos')) {
        const i = this.Ambito.indexOf('Todos')
        this.Ambito.splice(i, 1)
      }
      if (!this.Ambito.includes('Todos')) {
        filter = filter.filter(f => this.Ambito.includes(f.Juzgado))
      }
      return filter
    },
    filtrarNominacion (filter) {
      if (this.Nominacion.length === 0 || this.Nominacion[this.Nominacion.length - 1] === 'Todos') {
        this.Nominacion = ['Todos']
      }
      if (this.Nominacion.length > 1 && this.Nominacion.includes('Todos')) {
        const i = this.Nominacion.indexOf('Todos')
        this.Nominacion.splice(i, 1)
      }
      if (!this.Nominacion.includes('Todos')) {
        this.Nominacion.includes('Sin nominacion')
          ? filter = filter.filter(f => this.filtroNom(f.Nominacion) || !f.Nominacion || f.Nominacion === 'Sin nom')
          : filter = filter.filter(f => this.filtroNom(f.Nominacion))
      }
      return filter
    },
    filtroNom (nominacion) {
      if (nominacion) {
        if (isNaN(parseInt(nominacion[0]))) {
          return this.Nominacion.includes(nominacion)
        } else if (parseInt(nominacion[0]) === 0) {
          return this.Nominacion.includes(nominacion) || this.Nominacion.includes(nominacion.slice(0, -1))
        } else {
          return this.Nominacion.includes(nominacion[0] + '°')
        }
      } else {
        return false
      }
    },
    diasCambioEstado (FechaEstado, grilla = true) {
      if (!FechaEstado) {
        return grilla ? 'No hay un estado asignado.' : ''
      }
      var fecha = new Date()
      var hoy = new Date()
      var year = FechaEstado.split('-')[0]
      var month = FechaEstado.split('-')[1] - 1
      var day = FechaEstado.split('-')[2].split(' ')[0] - 1
      fecha.setMonth(month)
      fecha.setFullYear(year)
      fecha.setDate(day)
      var resultado =
        Math.ceil(
          (fecha.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      return resultado * -1 !== 1 ? `${resultado * -1} días` : `${resultado * -1} día`
    },
    filtrarCasosSinTel () {
      this.verSinTel
        ? this.casos = this.casosSinTel
        : this.casos = this.casosTodos
    },
    verComparticiones (caso) {
      if (caso.Comparticiones) {
        this.casoCompartido = caso
        this.modalComparticiones = true
      }
    },
    eliminarComparticiones (IdsEstudios, IdCaso) {
      const i = this.casos.findIndex(c => c.IdCaso === IdCaso)

      if (i > 0) {
        let indexes = []

        IdsEstudios.forEach(r => {
          const index = this.casos.Comparticiones.EstudiosDestino.findIndex(e => e.IdEstudio === r)

          if (index > 0) { indexes.push(index) }
        })

        if (indexes.length > 0) {
          indexes.forEach(r => {
            this.casos.Comparticiones.EstudiosDestino.splice(r, 0)
          })
        }
      }
    },
    casosMensajeGlobal () {
      const keys = Object.keys(this.CasosMensaje)
      let casos = []

      keys.forEach(i => {
        let check = false
        this.CasosMensaje[i].PersonasCaso.forEach(p => {
          if (p.Telefonos) { check = true }
        })

        if (check) { casos.push(this.CasosMensaje[i]) }
      })

      if (casos.length < keys.length && this.ModalMensaje) {
        Notify.create('Algunos de los casos seleccionados no tienen telefonos asociados.')
        return casos
      }

      return casos
    },
    mensajeEnviado () {
      this.selectAll = false
      this.CasosMensaje = []
      this.casos.forEach(c => {
        c.model = false
      })

      this.ModalMensaje = false
    },
    showSelect (ref) {
      const s = 'select' + ref
      if (!this.show[ref]) {
        this.$refs[s].showPopup()
        this.show[ref] = true
      } else {
        this.$refs[s].hidePopup()
        this.show[ref] = false
      }
    },
    showFiltros () {
      this.filtros
        ? this.$refs.expansionFiltros.hide()
        : this.$refs.expansionFiltros.show()

      this.filtros = !this.filtros
    },
    editarMovimiento (movimiento, caratula) {
      event.stopPropagation()
      if (!movimiento) { return }
      movimiento.Caratula = caratula
      this.movimientoEditar = movimiento
      this.ModalMovimiento = true
    },
    nominacion (nom) {
      const n = {
        0: 'Pendiente',
        1: 'I',
        2: 'II',
        3: 'III',
        4: 'IV',
        5: 'V',
        6: 'VI',
        7: 'VII',
        8: 'VIII',
        9: 'IX'
      }
      const i = nom ? nom.slice(0, 1) : '-'
      return n[i] ? n[i] : '···'
    },
    detalleUltMov (mov) {
      if (mov) {
        return mov.Detalle
          ? mov.Detalle.slice(0, 15) + (mov.Detalle.length > 15 ? '...' : '')
          : 'Sin movimientos'
      } else {
        return 'Sin movimientos'
      }
    },
    caratula (c) {
      if (!c) {
        return 'Sin Caratula'
      }

      return c.length > 25
        ? c.slice(0, 23) + '...'
        : c
    },
    verEtiquetasCaso (c) {
      this.casoEtiquetas = {
        IdCaso: c.IdCaso,
        EtiquetasCaso: c.EtiquetasCaso.filter(e => e.Etiqueta)
      }
      this.modalEtiquetas = true
    },
    exportar () {
      let array = []
      this.loadingExcel = true
      this.ModalExcel = true
      request.Get(
        '/casos/buscar',
        {
          Offset: this.casosTodos.length,
          Cadena: this.busqueda,
          Tipo: this.tipoBusqueda.toUpperCase(),
          Limit: 9999
        }, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            r.forEach((c) => {
              try {
                c.PersonasCaso = JSON.parse(c.PersonasCaso)
              } catch (error) {
                console.log(error)
                c.PersonasCaso = []
              }
              c.Comparticiones = JSON.parse(c.Comparticiones)
              c.EtiquetasCaso = JSON.parse(c.EtiquetasCaso)

              c.model = false

              c.App = false

              c.PersonasCaso.forEach(p => {
                if (p.TokenApp) c.App = true
              })

              this.casos.push(c)
              this.casosTodos.push(c)

              let check = false
              c.PersonasCaso.forEach(p => {
                if (p.Telefonos) { check = true }
              })

              if (!check) { this.casosSinTel.push(c) }
            })
            this.filtrarCasosSinTel()
            this.casos = this.casos.sort((a, b) => {
              if (a.FechaAlta < b.FechaAlta) {
                return 1
              }
              if (a.FechaAlta > b.FechaAlta) {
                return -1
              }
              return 0
            })
            for (var i = this.casos.length - 1; i >= 0; i--) {
              if (this.casos.indexOf(this.casos[i]) !== i) { this.casos.splice(i, 1) }
            }
            for (var j = this.casosTodos.length - 1; j >= 0; j--) {
              if (this.casosTodos.indexOf(this.casos[j]) !== j) { this.casosTodos.splice(j, 1) }
            }

            this.buscarCaso.forEach(m => {
              let actores = []
              let demandados = []

              m.PersonasCaso.forEach(p => {
                if (p.Observaciones === 'Actor') actores.push(p.Apellidos + ', ' + p.Nombres)
                if (p.Observaciones === 'Demandado') demandados.push(p.Apellidos + ', ' + p.Nombres)
              })

              m.Actores = actores
              m.Demandados = demandados

              array.push({
                Id: m.IdCasoEstudio,
                Caratula: m.Caratula,
                Fecha_de_Alta: m.FechaAlta ? moment(m.FechaAlta).format('DD/MM/YYYY HH:mm') + 'hs' : '',
                Dias_Alta: this.diasCambioEstado(m.FechaAlta, false),
                Competencia: m.Competencia || '',
                Nro_Expediente: m.NroExpediente || '',
                Estado_de_Ambito_Gestion: m.EstadoAmbitoGestion || '',
                Fecha_Estado: m.FechaEstado ? moment(m.FechaEstado).format('DD/MM/YYYY HH:mm') + 'hs' : '',
                Dias_Estado: this.diasCambioEstado(m.FechaEstado, false),
                Tipo_Caso: m.TipoCaso || '',
                Origen: m.Origen || '',
                Ambito_Gestion: m.Juzgado || '',
                Actores: m.Actores.length > 0 ? m.Actores.join(' - ') : '',
                Demandados: m.Demandados.length > 0 ? m.Demandados.join(' - ') : '',
                Ultimo_Movimiento: m.UltimoMovimiento ? m.UltimoMovimiento.Detalle : '',
                Fecha_Ultimo_Mensaje: m.FechaUltMsj ? moment(m.FechaUltMsj).format('DD/MM/YYYY HH:mm') + 'hs' : '',
                Dias_Ultimo_Mensaje: this.diasCambioEstado(m.FechaUltMsj, false)
              })
            })

            this.ArrayExcel = array

            this.loadingExcel = false
          }
        })
    }
  }
}
</script>

<style>
.casos__container {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  padding: 0px;
  margin: 0px;
}

.casilla_container, .filas div, .opciones-container > div {
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  position: relative;
}

.opciones-container {
  margin: 20px auto 0px auto;
  width: 21%;
}

.opciones-container .separador_titulo {
  height: 100%;
}

.casilla_container i {
  cursor: pointer;
}

.casilla_container .q-select {
  visibility: hidden;
  width: 0px;
  height: 0px;
  overflow: hidden;
}

.casos__container > div {
  margin: 0;
  padding: 0;
  display: flex;
  width: 100%;
}

.filas_container, .titulos_container {
  background-color: #ffffff;
  width: 100%;
  color: #000 !important;
  height : 60px;
  margin-top:15px;
  padding: 0;
  border-radius : 7px;
  -moz-border-radius : 7px;
  -webkit-border-radius : 7px;
}

.titulos_container {
  font-family: "Avenir Next";
  font-weight: bold;
  height : 70px;
  font-size: 16px;
}

.filtros-container {
  margin-bottom: none;
}

.filtros-container .q-item {
  display: none;
}

.busqueda-input {
  border-radius: 28px;
  background-color: white;
  width: auto;
  max-width: 584px;
  height: 44px;
  margin: 60px auto 0px auto;
}

.busqueda-input .q-field__control, .busqueda-input .q-field__marginal {
  height: 44px !important;
}

/*
.busqueda-input .q-field__control::before {
  content: '';
  background: -moz-linear-gradient(50% 100% 90deg,rgba(255, 255, 255, 0.5) 0%,rgba(253, 253, 253, 0.5) 44.8%,rgba(246, 246, 246, 0.5) 60.93%,rgba(235, 235, 235, 0.5) 72.43%,rgba(218, 218, 218, 0.5) 81.73%,rgba(196, 196, 196, 0.5) 89.7%,rgba(169, 169, 169, 0.5) 96.62%,rgba(153, 153, 153, 0.5) 100%);
  background: -webkit-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 44.8%, rgba(246, 246, 246, 0.5) 60.93%, rgba(235, 235, 235, 0.5) 72.43%, rgba(218, 218, 218, 0.5) 81.73%, rgba(196, 196, 196, 0.5) 89.7%, rgba(169, 169, 169, 0.5) 96.62%, rgba(153, 153, 153, 0.5) 100%);
  background: -webkit-gradient(linear,50% 100% ,50% 0% ,color-stop(0,rgba(255, 255, 255, 0.5) ),color-stop(0.448,rgba(253, 253, 253, 0.5) ),color-stop(0.6093,rgba(246, 246, 246, 0.5) ),color-stop(0.7243,rgba(235, 235, 235, 0.5) ),color-stop(0.8173,rgba(218, 218, 218, 0.5) ),color-stop(0.897,rgba(196, 196, 196, 0.5) ),color-stop(0.9662,rgba(169, 169, 169, 0.5) ),color-stop(1,rgba(153, 153, 153, 0.5) ));
  background: -o-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 44.8%, rgba(246, 246, 246, 0.5) 60.93%, rgba(235, 235, 235, 0.5) 72.43%, rgba(218, 218, 218, 0.5) 81.73%, rgba(196, 196, 196, 0.5) 89.7%, rgba(169, 169, 169, 0.5) 96.62%, rgba(153, 153, 153, 0.5) 100%);
  background: -ms-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 44.8%, rgba(246, 246, 246, 0.5) 60.93%, rgba(235, 235, 235, 0.5) 72.43%, rgba(218, 218, 218, 0.5) 81.73%, rgba(196, 196, 196, 0.5) 89.7%, rgba(169, 169, 169, 0.5) 96.62%, rgba(153, 153, 153, 0.5) 100%);
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#999999' ,GradientType=0)";
  background: linear-gradient(0deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 44.8%, rgba(246, 246, 246, 0.5) 60.93%, rgba(235, 235, 235, 0.5) 72.43%, rgba(218, 218, 218, 0.5) 81.73%, rgba(196, 196, 196, 0.5) 89.7%, rgba(169, 169, 169, 0.5) 96.62%, rgba(153, 153, 153, 0.5) 100%);
  filter: alpha(opacity=50) progid:DXImageTransform.Microsoft.Alpha(opacity=50) progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999',endColorstr='#FFFFFF' , GradientType=0);
}
*/

.filas {
  background : -moz-linear-gradient(50% 100% 90deg,rgba(255, 255, 255, 0.5) 0%,rgba(253, 253, 253, 0.5) 30.79%,rgba(244, 244, 244, 0.5) 49.03%,rgba(230, 230, 230, 0.5) 64%,rgba(210, 210, 210, 0.5) 77.19%,rgba(184, 184, 184, 0.5) 89.1%,rgba(153, 153, 153, 0.5) 100%);
  background : -webkit-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  background : -webkit-gradient(linear,50% 100% ,50% 0% ,color-stop(0,rgba(255, 255, 255, 0.5) ),color-stop(0.3079,rgba(253, 253, 253, 0.5) ),color-stop(0.4903,rgba(244, 244, 244, 0.5) ),color-stop(0.64,rgba(230, 230, 230, 0.5) ),color-stop(0.7719,rgba(210, 210, 210, 0.5) ),color-stop(0.891,rgba(184, 184, 184, 0.5) ),color-stop(1,rgba(153, 153, 153, 0.5) ));
  background : -o-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  background : -ms-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#999999' ,GradientType=0)";
  background : linear-gradient(0deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  filter: alpha(opacity=50) progid:DXImageTransform.Microsoft.Alpha(opacity=50) progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999',endColorstr='#FFFFFF' , GradientType=0);
  padding: inherit;
  border-radius : inherit;
  -moz-border-radius : inherit;
  -webkit-border-radius : inherit;
  width: inherit;
  height: inherit;
  font-family: "Avenir Next";
  font-weight: 600;
}

.check_casilla {
  margin-left: -3px;
  margin-right: 4px;
}

.separador_titulo {
  height: 45%;
  width: 2px;
  position: absolute;
  right: -1px;
}

.opciones-container .q-toggle__thumb::after {
  background-color: #81E2CE !important;
}

.opciones-container .q-toggle__inner--truthy {
  color: #81E2CE !important;
}

@media only screen and (max-width: 1023px) {
  .filas > div:not(.cliente), .titulos_container > div:not(.cliente) {
    display: none !important;
  }

  .cliente {
    width: 100% !important;
  }

  .opciones-container {
    width: 50%;
  }
}

@media only screen and (max-device-width: 600px) {
  .q-btn-group {
    margin: 0.5em -0.5em 0.5em -0.5em !important;
  }

  .opciones-container {
    width: 75%;
  }
}
</style>
