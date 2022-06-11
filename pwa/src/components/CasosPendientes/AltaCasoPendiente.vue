<template>
  <q-card style="min-width:400px;">
    <q-item class="bg-black">
      <span class="q-subheading text-h6" style="color:white;">Nuevo Caso Pendiente</span>
    </q-item>
    <div class="casos-pendientes-container" v-if="casosPendientes.length">
      <div
        v-for="(c, i) in casosPendientes"
        :key="c.Documento + '-' + i"
        class="row justify-between text-center q-pa-lg relative-position"
      >
        <q-separator v-if="i !== 0" class="absolute bg-black" style="width: 90%; top: 0px" />

        <span id="inicio" style="height: 0px; display: block"></span>

        <span :class="`${c.Documento ? 'col-6' : 'col-12'} text-weight-bold text-subtitle1`">{{ c.Apellidos + ', ' + c.Nombres }}</span>
        <span class="col-6 text-weight-bold text-subtitle1">{{ c.Documento }}</span>
        <q-input
          class="col-12"
          v-model="c.Domicilio"
          label="Domicilio"
          type="text"
          autofocus
        />
        <div class="col-6 q-pr-sm">
          <q-input
            class="casillas"
            v-model="c.Telefono"
            label="Telefono"
            type="number"
          />
        </div>
        <div class="col-6 q-pl-sm">
          <q-select
            class="casillas"
            v-model="c.Prioridad"
            :options="opcionesPrioridad"
            label="Prioridad"
            emit-value
            map-options
          />
        </div>
        <div class="col-6 q-pr-sm">
          <q-select
            class="casillas"
            v-model="c.IdOrigen"
            :options="opcionesOrigenes"
            label="Origen"
            emit-value
            map-options
          />
        </div>
        <div class="col-6 q-pl-sm">
          <q-select
            class="casillas"
            v-model="c.IdEstadoCasoPendiente"
            :options="opcionesEstados"
            label="Estado"
            emit-value
            map-options
          />
        </div>
        <div class="col-6 q-pr-sm">
          <q-select
            class="casillas"
            v-model="c.Lesion"
            :options="opcionesLesiones"
            label="Lesión"
            emit-value
            map-options
          />
        </div>
        <div class="col-6 flex justify-end items-center">
          <q-icon
            v-if="c.Latitud"
            class="cursor-pointer"
            name="where_to_vote"
            size="lg"
            style="color: #49C00F"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver en el mapa</q-tooltip>
          </q-icon>
          <q-icon
            v-else
            class="cursor-pointer"
            name="location_off"
            size="lg"
            style="color: #B1000E"
            @click="abrirMapa(c, i)"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Geolocalizar</q-tooltip>
          </q-icon>
        </div>
      </div>

      <span id="fin" style="height: 0px; display: block"></span>
    </div>

    <q-separator class="bg-black" />

    <div class="column items-center">
      <div
        v-if="casoSinDni"
        class="row justify-between text-center q-pa-lg relative-position"
      >
        <q-input
          class="col-6 q-pr-sm"
          v-model="casoNuevo.Apellidos"
          label="Apellidos"
          type="text"
        />
        <q-input
          class="col-6 q-pl-sm"
          v-model="casoNuevo.Nombres"
          label="Nombres"
          type="text"
        />
        <q-btn
          outline
          rounded
          no-caps
          style="margin: 10px auto"
          color="positive"
          label="Agregar"
          @click="agregarCaso"
        />
      </div>
      <q-checkbox
        v-else
        label="Agregar caso sin DNI"
        v-model="casoSinDni"
      />
      <q-input
        outlined
        rounded
        class="busqueda-input shadow-3"
        label-color="grey-2"
        color="grey-4"
        v-model="busqueda"
        placeholder="O ingrese el DNI para buscar en el padrón"
        @keyup.enter="buscar"
      >
        <template v-slot:append>
          <q-icon
            v-if="!buscando"
            class="cursor-pointer"
            name="search"
            @click="buscar"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Buscar en el padrón</q-tooltip>
          </q-icon>
          <q-spinner
            v-else
            color="teal"
            size="sm"
          />
        </template>
      </q-input>
      <q-btn
        v-if="casosPendientes.length && !subiendo"
        outline
        rounded
        no-caps
        style="margin: 10px"
        color="grey-14"
        label="Cargar Casos"
        @click="alta"
      />
      <q-spinner
        v-else-if="subiendo"
        style="margin: 10px"
        color="teal"
        size="sm"
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

    <!--Modal: Casos Repetidos-->
    <q-dialog v-model="modalRepetidos">
      <q-card style="max-width: 1500px">
        <q-item class="bg-black">
          <span class="q-subheading text-h6" style="color:white;">Casos Repetidos</span>
        </q-item>
        <div class="text-center q-mt-xs">
          <span class="q-subheading">Este documento ya ha sido cargado en DocDoc!</span>
        </div>
        <div class="q-mt-md flex justify-center">
          <q-btn
            push
            no-caps
            class="q-mr-xs"
            label="Cargar Nuevamente"
            @click="cargarCaso"
            color="positive"
          />
          <q-btn
            push
            no-caps
            class="q-ml-xs"
            label="Cancelar"
            @click="modalRepetidos = false"
            color="negative"
          />
        </div>
        <div class="text-center q-mt-lg">
          <span class="text-h6 text-bold">Casos Pendientes</span>
        </div>
        <GrillaCasosPendientes
          :Casos="CasosPendientesRepetidos"
          :maxpags="1"
          :pag="1"
          :opcionesEstados="[]"
        />
        <div class="text-center q-mt-lg">
          <span class="text-h6 text-bold">Casos Activos</span>
        </div>
        <div class="row justify-center items-center">
          <div
            v-for="caso in CasosActivos"
            :key="caso.IdCaso"
            class="q-pa-md col-3"
          >
            <div
              class="filas_container q-banner cursor-pointer full-width"
              @click="abrirCaso(caso.IdCaso)"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ir al caso</q-tooltip>
              <div class="filas text-center">
                {{ caso.Caratula }}
              </div>
            </div>
          </div>
        </div>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script>
import request from '../../request'
import MapaGeo from '../../components/CasosPendientes/MapaGeo'
import GrillaCasosPendientes from '../../components/CasosPendientes/GrillaCasosPendientes'
import { Notify, QSpinner } from 'quasar'

export default {
  name: 'AltaCasoPendiente',
  components: {
    MapaGeo,
    GrillaCasosPendientes,
    QSpinner
  },
  props: [
    'opcionesEstados',
    'opcionesOrigenes',
    'opcionesLesiones'
  ],
  data () {
    return {
      busqueda: '',
      buscando: false,
      subiendo: false,
      modalMap: false,
      modalRepetidos: false,
      casoSinDni: false,
      casosPendientes: [],
      CasosPendientesRepetidos: [],
      CasosActivos: [],
      casoAux: {},
      casoNuevo: {
        Apellidos: '',
        Nombres: '',
        Documento: '',
        Telefono: '',
        Domicilio: '',
        Prioridad: 3,
        IdOrigen: 1,
        IdEstadoCasoPendiente: 17,
        Lesion: ''
      },
      opcionesPrioridad: [
        {
          label: 'Alta',
          value: 1
        },
        {
          label: 'Media',
          value: 2
        },
        {
          label: 'Baja',
          value: 3
        }
      ]
    }
  },
  created () {
  },
  methods: {
    buscar () {
      if (this.buscando) { return }
      if (!this.busqueda) {
        Notify.create('Debe ingresar un numero de documento')
        return
      }
      if (this.casosPendientes.length === 15) {
        Notify.create('Solo se pueden cargar 15 casos a la vez.')
        return
      }
      this.buscando = true
      request.Get('/casos-pendientes/buscar-caso', {Documento: this.busqueda}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          if (r.CasosPendientes.length || r.CasosActivos.length) {
            this.CasosPendientesRepetidos = r.CasosPendientes
            this.CasosActivos = r.CasosActivos
            this.PersonaPadron = r.PersonaPadron

            this.modalRepetidos = true
          } else {
            this.PersonaPadron = r.PersonaPadron

            this.cargarCaso()
          }
        }
        this.buscando = false
      })
    },
    cargarCaso () {
      const i = this.casosPendientes.length
      let idEstado = this.PersonaPadron.Domicilio.includes('CAPITAL') ? 17 : 22

      const barrio = ['Barrio', 'BARRIO', 'B°', 'barrio']
      barrio.forEach(b => {
        idEstado = this.PersonaPadron.Domicilio.includes(b) ? 19 : idEstado
      })

      this.casosPendientes.push({
        Apellidos: this.PersonaPadron.Apellidos,
        Nombres: this.PersonaPadron.Nombres,
        Documento: this.busqueda,
        Telefono: '',
        Domicilio: this.PersonaPadron.Domicilio,
        Prioridad: 3,
        IdOrigen: i
          ? this.casosPendientes[i - 1].IdOrigen
          : this.opcionesOrigenes[0].value,
        IdEstadoCasoPendiente: idEstado,
        Lesion: ''
      })

      this.modalRepetidos = false
      this.busqueda = ''
      this.scroll('fin')
    },
    alta () {
      this.subiendo = true
      const casos = JSON.stringify(this.casosPendientes)
      request.Post('/casos-pendientes/alta', {casos: casos}, r => {
        if (r.length > 0 || r.Error) {
          let arrayAux = []

          r.length > 0
            ? r.forEach(e => {
              Notify.create(`Hubo un error al subir los datos de ${e.persona}: ${e.error}`)
              const caso = this.casosPendientes.filter(c => c.Apellidos + ', ' + c.Nombres === e.persona)[0]
              arrayAux.push(caso)
            })
            : Notify.create(r.Error)
          this.casosPendientes = arrayAux

          this.subiendo = false
          this.scroll('inicio')
        } else {
          this.casosPendientes = []
          Notify.create('Todos los datos se cargaron exitosamente.')
          this.$emit('cerrar')
        }
      })
    },
    scroll (id) {
      this.$nextTick(() => {
        document.getElementById(id).scrollIntoView(true)
      })
    },
    abrirMapa (caso, index) {
      caso.i = index
      this.casoAux = caso

      this.modalMap = true
    },
    guardarGeo (coords, domicilio) {
      const i = this.casoAux.i
      this.casosPendientes[i].Domicilio = domicilio
      this.casosPendientes[i] = {...this.casosPendientes[i], ...coords}

      this.modalMap = false
    },
    abrirCaso (id) {
      let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
      window.open(routeData.href, '_blank')
    },
    agregarCaso () {
      if (this.casoNuevo.Apellidos && this.casoNuevo.Nombres) {
        this.casoSinDni = false
        this.casosPendientes.push(this.casoNuevo)
        this.casoNuevo = {
          Apellidos: '',
          Nombres: '',
          Documento: '',
          Telefono: '',
          Domicilio: '',
          Prioridad: 3,
          IdOrigen: 1,
          IdEstadoCasoPendiente: 17,
          Lesion: ''
        }
      } else {
        Notify.create('Debe ingresar nombre y apellido')
      }
    }
  }
}
</script>

<style scoped>
.busqueda-input {
  width: 90% !important;
  margin: 10px auto !important;
}

.casillas {
  margin: 5px auto;
}

.casos-pendientes-container {
  max-height: 59vh;
  overflow: scroll;
}
</style>
