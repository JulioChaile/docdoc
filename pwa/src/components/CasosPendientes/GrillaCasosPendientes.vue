<template>
  <div class="full-width q-pa-lg q-mt-xl">
    <div
      v-if="Casos.length"
      class="flex flex-center"
    >
      <q-pagination
        v-model="pag"
        :max="maxpags"
        input
        @input="$emit('page', pag)"
      />
    </div>

    <div
      class="row titulos_container q-banner"
    >
      <q-checkbox
        v-model="selectAll"
        class="col-auto check_casilla"
        @input="$emit('selectAll', selectAll)"
      />
      <div
        class="col-sm-1 casilla_container"
      >
        Origen
      </div>
      <div
        class="col-sm-2 col-xs-12 casilla_container cliente"
      >
        Cliente
      </div>
      <div
        class="col-sm-1 casilla_container"
      >
        Domicilio
      </div>
      <!--div
        class="col-sm-1 casilla_container"
      >
        Documento
      </div-->
      <div
        class="col-sm-1 casilla_container"
      >
        Telefono
      </div>
      <div
        class="col-sm-2 casilla_container"
      >
        Ult. Mov.
      </div>
      <div
        class="col-sm-1 casilla_container"
      >
        Estado
        <!--q-select
          v-model="IdEstadoCasoPendienteAux"
          :options="opcionesEstadosAux"
          emit-value
          map-options
          ref="selectEstado"
          @input="filtrar('Estado', IdEstadoCasoPendienteAux)"
        />
        <q-icon
          rounded
          color="grey"
          name="more_vert"
          size="sm"
          @click="showSelect('Estado')"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Origen</q-tooltip>
        </q-icon-->
      </div>
      <div
        class="col-sm-1 casilla_container"
      >
        Lesión
      </div>
      <!--div
        class="col-sm-1 casilla_container"
      >
        Prioridad
      </div-->
      <div
        class="col-sm-1 casilla_container"
      >
        Visitado
      </div>
      <div
        class="col-sm-1 casilla_container"
      >
        Acciones
      </div>
    </div>
    <p v-if="Casos.length === 0">No hay casos que coincidan con el criterio de busqueda.</p>
    <div
      v-else
      v-for="(caso, i) in Casos"
      :key="caso.IdCasoPendiente"
      class="filas_container q-banner"
    >
      <div class="row filas">
        <q-checkbox
          v-model="caso.model"
          @input="$emit('select', caso)"
          class="check_casilla"
        />
        <div
          class="col-sm-1"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Origen</q-tooltip>
          {{caso.Origen || "No hay un origen asignado."}}
        </div>
        <div
          class="col-sm-2 col-xs-12 cliente cursor-pointer column"
          @click="(caso.IdCaso !== 0 && caso.IdCaso !== '0') ? abrirCaso(caso.IdCaso) : crearCasoActivo(caso, i)"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ir al caso</q-tooltip>
          <span
            class="q-subheading"
            style="color: #1B43F0"
          >{{ caso.Apellidos + ', ' + caso.Nombres }}</span>
          <span class="text-caption">{{dias(caso.FechaAlta)}}</span>
        </div>
        <div
          class="col-sm-1"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Domicilio</q-tooltip>
          {{ caso.Domicilio }}
        </div>
        <!--div
          class="col-sm-1"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">DNI</q-tooltip>
          {{ caso.Documento }}
        </div-->
        <div
          class="col-sm-1"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Telefono</q-tooltip>
          {{ caso.Telefono || 'Sin datos' }}
        </div>
        <div
          class="col-sm-2 cursor-pointer"
          @click="editarMovimiento(caso.UltimoMovimiento, caso.Apellidos + ', ' + caso.Nombres)"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Edita movimiento</q-tooltip>
          {{ caso.UltimoMovimiento ? caso.UltimoMovimiento.Detalle : 'Sin movimientos cargados' }}
        </div>
        <div
          class="col-sm-1 column"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Estado pendiente</q-tooltip>
          {{ caso.EstadoCasoPendiente }}
          <span class="text-caption" style="color: #1B43F0">{{dias(caso.FechaEstado)}}</span>
        </div>
        <div
          class="col-sm-1"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Lesión</q-tooltip>
          {{ caso.Lesion || 'Sin datos' }}
        </div>
        <!--div
          class="col-sm-1"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Prioridad</q-tooltip>
          {{ opcionesPrioridad.filter(o => o.value === parseInt(caso.Prioridad))[0].label }}
        </div-->
        <div
          class="col-sm-1 acciones column"
        >
          <q-icon
            v-if="parseInt(caso.Visitado)"
            name="r_check_circle"
            size="sm"
            style="color: #49C00F"
            @click="marcarVisitado(caso, i, false)"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Marcar como "Sin visitar"</q-tooltip>
          </q-icon>
          <q-icon
            v-else
            name="r_cancel"
            size="sm"
            style="color: #B1000E"
            @click="marcarVisitado(caso, i, true)"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Marcar como "Visitado"</q-tooltip>
          </q-icon>
          <span
            v-if="caso.UsuarioVisita"
            class="q-mt-xs"
          >
            {{ caso.UsuarioVisita }}
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Realizo la visita</q-tooltip>
          </span>
        </div>
        <div
          class="col-sm-1 col-xs-12 acciones cliente"
        >
          <q-icon
            v-if="caso.Latitud"
            name="where_to_vote"
            size="sm"
            style="color: #49C00F"
            @click="abrirMapa(caso, i)"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver en el mapa</q-tooltip>
          </q-icon>
          <q-icon
            v-else
            name="location_off"
            size="sm"
            style="color: #B1000E"
            @click="abrirMapa(caso, i)"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Geolocalizar</q-tooltip>
          </q-icon>
          <q-icon
            name="edit"
            size="sm"
            color="primary"
            @click="habilitarEdicion(caso, i)"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Editar datos</q-tooltip>
          </q-icon>
          <q-icon
            name="check_circle_outline"
            size="sm"
            color="positive"
            @click="habilitarActivar(caso)"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Activar caso</q-tooltip>
          </q-icon>
          <q-icon
            name="delete"
            size="sm"
            color="negative"
            @click="habilitarEliminar(caso)"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Eliminar</q-tooltip>
          </q-icon>
        </div>
      </div>
    </div>

    <div
      v-if="Casos.length"
      class="q-pa-lg flex flex-center"
    >
      <q-pagination
        v-model="pag"
        :max="maxpags"
        input
        @input="$emit('page', pag)"
      />
    </div>

    <!--Modal: Mapa-->
    <q-dialog v-model="modalMap">
      <q-card class="full-width full-height" style="max-width: 800px">
        <MapaGeo
          :Caso="casoAux"
          :desdeDocDoc="true"
          @cerrar="modalMap = false"
          @confirmado="guardarGeo"
        />
      </q-card>
    </q-dialog>

    <!--Modal: Editar Caso-->
    <q-dialog v-model="modalEditar">
      <q-card>
        <div class="row justify-between text-center q-pa-lg relative-position">
          <span class="col-6 text-weight-bold text-subtitle1">{{ casoAux.Apellidos + ', ' + casoAux.Nombres }}</span>
          <span class="col-6 text-weight-bold text-subtitle1">{{ casoAux.Documento }}</span>
          <q-input
            class="col-12"
            v-model="casoAux.Domicilio"
            label="Domicilio"
            type="text"
          />
          <div class="col-6 q-pr-sm">
            <q-input
              class="casillas"
              v-model="casoAux.Telefono"
              label="Telefono"
              type="number"
            />
          </div>
          <div class="col-6 q-pl-sm">
            <q-select
              class="casillas"
              v-model="casoAux.Prioridad"
              :options="opcionesPrioridad"
              label="Prioridad"
              emit-value
              map-options
            />
          </div>
          <div class="col-6 q-pr-sm">
            <q-select
              class="casillas"
              v-model="casoAux.IdOrigen"
              :options="opcionesOrigenes"
              label="Origen"
              emit-value
              map-options
            />
          </div>
          <div class="col-6 q-pl-sm">
            <q-select
              class="casillas"
              v-model="casoAux.IdEstadoCasoPendiente"
              :options="opcionesEstados"
              label="Estado"
              emit-value
              map-options
            />
          </div>
          <div class="col-6 q-pr-sm">
            <q-select
              class="casillas"
              v-model="casoAux.Lesion"
              :options="opcionesLesiones"
              label="Lesión"
              emit-value
              map-options
            />
          </div>
          <div class="col-6 flex justify-start items-center">
            <q-checkbox
              v-model="casoAux.Visitado"
              :true-value="1"
              :false-value="0"
              label="Marcar como Visitado"
            />
          </div>
          <div class="q-mt-md col-12 flex justify-center">
            <q-btn
              push
              no-caps
              class="q-mr-xs"
              label="Confirmar"
              @click="editarCaso(); modalEditar = false"
              color="positive"
            />
            <q-btn
              push
              no-caps
              class="q-ml-xs"
              label="Cancelar"
              @click="modalEditar = false"
              color="negative"
            />
          </div>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal: Eliminar caso -->
    <q-dialog v-model="modalEliminar" prevent-close>
      <q-card style="padding:1rem;">
        <div class="text-center">
          <span>
            <p>
              ¿Esta seguro que desea eliminar este caso?
              <br>
              Esta accion es permanente y tambien sera eliminado el caso asociado junto con sus movimientos, chat, personas, etc.
            </p>
          </span>
          <div style="display: flex;justify-content: center; margin-botton:2rem;">
            <q-btn
              color="negative"
              label="Eliminar"
              @click="eliminarCaso()"
              style="padding-top:0px; float: center; margin-right: 10px"
            />
            <q-btn
              color="primary"
              label="Cancelar"
              @click="modalEliminar = false"
              style="padding-top:0px; float: center"
            />
          </div>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal: Activar caso -->
    <q-dialog v-model="modalActivar" prevent-close>
      <q-card style="padding:1rem;">
        <div class="text-center">
          <span>
            <p>
              ¿Esta seguro que desea activar este caso?
              <br>
              Si lo activa el caso dejara de aparecer como caso pendiente y se visualizará en la grilla principal
            </p>
          </span>
          <div style="display: flex;justify-content: center; margin-botton:2rem;">
            <q-btn
              color="negative"
              label="Activar"
              @click="activarCaso(casoAux.IdCasoPendiente, casoAux.IdCaso)"
              style="padding-top:0px; float: center; margin-right: 10px"
            />
            <q-btn
              color="primary"
              label="Cancelar"
              @click="modalActivar = false"
              style="padding-top:0px; float: center"
            />
          </div>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal: Alta Caso Activo -->
    <q-dialog v-model="subiendo">
      <q-card style="min-width:400px;">
        <div style="display: flex; justify-content: center; text-align: center; margin-left: 15px; margin-right: 15px; padding: 35px;">
            Espere unos instantes, se generando la vista del caso.
            <br>
            Esto puede tardar varios segundos.
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal Editar Movimiento -->
    <q-dialog v-model="modalMovimiento" no-backdrop-dismiss no-esc-dismiss>
      <q-card>
        <q-item class="relative-position" style="background-color: black;">
          <span class="q-subheading" style="color:white;">Editar Movimiento</span>
          <q-icon
            name="close"
            class="absolute-right cursor-pointer"
            color="negative"
            size="lg"
            @click="modalMovimiento = false"
          />
        </q-item>
        <EditarMovimiento
          v-if="modalMovimiento"
          :movimiento="this.movimientoEditar"
          @cancelarEdicion="modalMovimiento = false"
          @edicionTerminada="modalMovimiento = false"
        />
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import request from '../../request'
import auth from '../../auth'
import { Notify, QPagination } from 'quasar'
import MapaGeo from '../../components/CasosPendientes/MapaGeo'
import EditarMovimiento from '../../components/EditarMovimiento'

export default {
  name: 'GrillaCasosPendientes',
  components: {
    MapaGeo,
    EditarMovimiento,
    QPagination
  },
  props: [
    'Casos',
    'maxpags',
    'pag',
    'IdEstadoCasoPendiente',
    'opcionesEstados',
    'opcionesOrigenes',
    'opcionesLesiones'
  ],
  data () {
    return {
      opcionesPrioridad: [
        {
          label: 'Baja',
          value: 3
        },
        {
          label: 'Media',
          value: 2
        },
        {
          label: 'Alta',
          value: 1
        }
      ],
      casoAux: {},
      movimientoEditar: {},
      show: {},
      IdEstadoCasoPendienteAux: '',
      modalMap: false,
      modalEditar: false,
      modalEliminar: false,
      modalMovimiento: false,
      modalActivar: false,
      subiendo: false,
      selectAll: false
    }
  },
  computed: {
    opcionesEstadosAux () {
      let op = this.opcionesEstados.slice(0).filter(o => o.label !== 'TODOS')
      op.push({
        label: 'Todos',
        value: 0
      })

      return op
    }
  },
  methods: {
    resetSelectAll () {
      this.selectAll = false
    },
    dias (Fecha) {
      if (!Fecha) {
        return ''
      }
      var fecha = new Date()
      var hoy = new Date()
      var year = Fecha.split('-')[0]
      var month = Fecha.split('-')[1] - 1
      var day = Fecha.split('-')[2].split(' ')[0] - 1
      fecha.setMonth(month)
      fecha.setFullYear(year)
      fecha.setDate(day)
      var resultado =
        Math.ceil(
          (fecha.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      return resultado * -1 !== 1 ? `${resultado * -1} días` : `${resultado * -1} día`
    },
    abrirMapa (caso, index) {
      caso.i = index
      this.casoAux = caso

      this.modalMap = true
    },
    abrirCaso (id) {
      console.log(id)
      let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
      window.open(routeData.href, '_blank')
    },
    habilitarEliminar (caso) {
      this.casoAux = caso

      this.modalEliminar = true
    },
    habilitarEdicion (caso, index) {
      caso.i = index
      this.casoAux = { ...caso }
      this.casoAux.IdOrigen = parseInt(this.casoAux.IdOrigen)
      this.casoAux.IdEstadoCasoPendiente = parseInt(this.casoAux.IdEstadoCasoPendiente)
      this.casoAux.Prioridad = parseInt(this.casoAux.Prioridad)
      this.casoAux.Visitado = caso.Visitado ? 1 : 0

      this.modalEditar = true
    },
    habilitarActivar (caso) {
      this.casoAux = caso

      this.modalActivar = true
    },
    editarCaso () {
      request.Post('/casos-pendientes/editar', {caso: JSON.stringify(this.casoAux)}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          const i = this.casoAux.i
          this.Casos[i] = this.casoAux
          this.Casos[i].EstadoCasoPendiente = this.opcionesEstados.filter(o => o.value === this.casoAux.IdEstadoCasoPendiente)[0].label
          this.Casos[i].Origen = this.opcionesOrigenes.filter(o => o.value === this.casoAux.IdOrigen)[0].label
          this.casoAux = {}
          Notify.create('Los datos se actualizaron correctamente')
        }
      })
    },
    eliminarCaso () {
      request.Post('/casos-pendientes/eliminar', {id: this.casoAux.IdCasoPendiente}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.casoAux = {}
          this.$emit('eliminar')
          Notify.create('El caso se elimino correctamente')
        }
      })
    },
    guardarGeo (coords, domicilio) {
      const i = this.casoAux.i
      this.Casos[i].Domicilio = domicilio
      this.Casos[i] = {...this.Casos[i], ...coords}

      this.casoAux.Domicilio = domicilio
      this.casoAux = {...this.casoAux, ...coords}

      this.modalMap = false

      this.editarCaso()
    },
    marcarVisitado (caso, index, visitado) {
      this.casoAux = caso
      this.casoAux.Visitado = visitado ? 1 : 0
      this.Casos[index].Visitado = visitado ? 1 : 0

      if (visitado) {
        const u = auth.UsuarioLogueado
        this.Casos[index].IdUsuarioVisita = u.IdUsuario
        this.Casos[index].UsuarioVisita = u.Apellidos + ', ' + u.Nombres
      } else {
        this.Casos[index].IdUsuarioVisita = null
        this.Casos[index].UsuarioVisita = null
      }

      this.editarCaso()
    },
    crearCasoActivo (c, i) {
      this.subiendo = true
      request.Post('/casos-pendientes/alta-caso-activo', {caso: JSON.stringify(c)}, r => {
        if (r.Error) {
          Notify.create(r.Error)
          this.subiendo = false
        } else {
          this.Casos[i].IdCaso = r.IdCaso
          this.subiendo = false
          this.abrirCaso(r.IdCaso)
        }
      })
    },
    activarCaso (id, idCaso) {
      request.Post('/casos-pendientes/activar-caso', {id: id}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.$emit('eliminar')
          this.abrirCaso(idCaso)
        }
      })
    },
    editarMovimiento (movimiento, caratula) {
      event.stopPropagation()
      if (!movimiento) { return }
      movimiento.Caratula = caratula
      this.movimientoEditar = movimiento
      this.modalMovimiento = true
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
    filtrar (f, id) {
      this.show[f] = false
      this.$emit('filtrar', f, id)
    }
  }
}
</script>

<style>
.acciones > i {
  cursor: pointer;
  margin: 0 auto;
}

.casilla_container, .filas div {
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  position: relative;
}

.titulos_container {
  font-family: "Avenir Next";
  font-weight: bold;
  height : 70px;
  font-size: 16px;
}

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
  min-height: inherit;
  height: inherit;
  font-family: "Avenir Next";
  font-weight: 600;
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

.filas_container {
  min-height: 60px;
  height: auto !important;
}

.casilla_container i {
  cursor: pointer;
}

.casilla_container .q-select {
  visibility: hidden;
  width: 0px;
}

@media only screen and (max-width: 1023px) {
  .filas > div:not(.cliente), .titulos_container > div:not(.cliente) {
    display: none !important;
  }

  .cliente {
    margin: 10px 0px;
  }

  .filas_container {
    height: 120px;
  }
}
</style>
