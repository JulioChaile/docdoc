<template>
  <q-page>
    <div v-if="seccion" class="map-header q-pl-md q-pr-md flex items-center justify-between">
      <q-btn
        flat
        dense
        round
        type="a"
        href="Maps"
        aria-label="Inicio"
        class="logo-menu"
      >
        <img src="statics/img/logo.png" width="30" height="25" />
      </q-btn>
      <span class="text-white text-bold text-h6">DocDoc! Maps</span>
    </div>

    <div v-if="seccion === 'inicio'" class="position-relative">
      <div class="welcome-container">
        <span class="text1">Bienvenido a</span>
        <span class="text2">Doc Doc Maps®</span>
      </div>

      <div class="buttons-container column items-center">
        <q-btn @click="loading = true; buscarCasos(1, 'visitas', 'N', 10000)" v-if="!loading" size="lg" class="glossy q-mb-lg visitas" rounded color="positive" label="Visitas" />
        <q-spinner
          v-else
          class="q-mb-lg"
          color="teal"
          size="sm"
        />
        <q-btn @click="seccion = 'casos'; buscarCasos()" size="lg" class="glossy buscar" rounded color="negative" label="Buscar Casos" />
      </div>
    </div>

    <div v-if="seccion === 'visitas'" class="position-relative">
      <div id="mapCad"></div>

      <!--div class="buscador_calles">
        <input type="search" class="buscador inline_element" placeholder="Buscar calle...">
        <button class="cursor-pointer boton_buscador inline_element">
          <i class="fas fa-search"></i>
        </button>
      </div-->
    </div>

    <div v-if="seccion === 'casos'" class="q-pr-lg q-pl-lg full-height">
      <q-input
        outlined
        rounded
        class="busqueda-input q-mb-lg shadow-3"
        label-color="grey-2"
        color="grey-4"
        v-model="busqueda"
        :debounce="300"
        placeholder="Buscar"
        @change="loading = true; buscarCasos()"
      >
        <template v-slot:prepend>
          <q-icon name="search" />
        </template>
      </q-input>

      <div v-if="loading">
        <Loading />
      </div>

      <div v-else>
        <div
          class="q-pa-lg flex flex-center"
        >
          <q-pagination
            v-model="pag"
            :max="maxpags"
            input
            @input="buscarCasos"
          />
        </div>

        <q-card
          v-for="(c, i) in casos"
          :key="c.IdCasoPendiente"
          class="full-width text-white q-ma-sm q-pa-sm"
          style="background: radial-gradient(circle, #35a2ff 0%, #014a88 100%)"
        >
          <div class="full-width flex justify-start q-pa-xs">
            Nombre: {{ c.Apellidos }}, {{ c.Nombres }}
          </div>
          <div class="full-width flex justify-start q-pa-xs">
            Domicilio: {{ c.Domicilio }}
          </div>
          <div class="full-width flex justify-start q-pa-xs">
            Telefono: {{ c.Telefono || '---' }}
          </div>
          <q-icon
            v-if="c.Latitud"
            class="cursor-pointer"
            name="where_to_vote"
            size="lg"
            style="color: #49C00F"
            @click="abrirMapa(c, i)"
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
        </q-card>

        <div
          class="q-pa-lg flex flex-center"
        >
          <q-pagination
            v-model="pag"
            :max="maxpags"
            input
            @input="buscarCasos"
          />
        </div>
      </div>
    </div>

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
              v-model="casoAux.IdEstadoCasoPendiente"
              :options="opcionesEstados"
              label="Estado"
              emit-value
              map-options
            />
          </div>
          <div class="col-12 flex justify-start items-center">
            <q-checkbox
              v-model="casoAux.Visitado"
              :true-value="1"
              :false-value="0"
              label="Marcar como Visitado"
            />
          </div>
          <q-input
            class="full-width q-my-md"
            label="Comentario (opcional)"
            v-model="comentario.Detalle"
            filled
            type="textarea"
          />
          <div class="q-mt-md col-12 flex justify-center">
            <q-btn
              push
              no-caps
              class="q-mr-xs"
              label="Confirmar"
              @click="editarCaso(casoAux)"
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

    <!--Modal: Mapa-->
    <q-dialog v-model="modalMap">
      <q-card class="full-width full-height" style="max-width: 800px">
        <MapaGeo
          :Caso="casoAux"
          @cerrar="modalMap = false"
          @confirmado="guardarGeo"
        />
      </q-card>
    </q-dialog>

    <!--Modal: Comentario-->
    <q-dialog v-model="modalComentario">
      <q-card class="q-pa-md flex justify-center" style="width: 400px !important">
        <q-input
          class="full-width q-mb-md"
          label="Comentario"
          v-model="comentario.Detalle"
          filled
          type="textarea"
        />
        <q-btn
          @click="enviarComentario()"
          size="md"
          class="glossy"
          style="margin: auto"
          rounded
          color="primary"
          label="Enviar"
        />
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import auth from '../auth'
import request from '../request'
import { Notify, QSpinner, QPagination } from 'quasar'
import moment from 'moment'
import { Loader } from '@googlemaps/js-api-loader'
import Loading from '../components/Loading'
import MapaGeo from '../components/CasosPendientes/MapaGeo'

export default {
  components: {
    MapaGeo,
    Loading,
    QSpinner,
    QPagination
  },
  data () {
    return {
      seccion: this.$route.query.sec || 'inicio',
      loading: false,
      modalEditar: false,
      modalMap: false,
      modalComentario: false,
      busqueda: '',
      casos: [],
      pag: 1,
      maxpags: 1,
      loaderMap: null,
      map: null,
      opcionesEstados: [],
      opPrioridad: {
        1: {
          icon: 'red-circle.png',
          class: 'prioridad_alta',
          prioridad: 'ALTA'
        },
        2: {
          icon: 'ylw-circle.png',
          class: 'prioridad_media',
          prioridad: 'MEDIA'
        },
        3: {
          icon: 'blu-circle.png',
          class: 'prioridad_baja',
          prioridad: 'BAJA'
        }
      },
      casoAux: {},
      comentario: {}
    }
  },
  created () {
    if (!auth.isLoggedIn) {
      this.seccion = null
      this.$router.push({ path: '/Login' })
    }

    request.Get('/casos-pendientes/estados', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.opcionesEstados = r.map(e => {
          return {
            label: e.EstadoCasoPendiente,
            value: parseInt(e.IdEstadoCasoPendiente)
          }
        })
      }
    })
  },
  mounted () {
    if (this.seccion === 'visitas') { this.buscarCasos(1, 'visitas', 'N', 10000) }
  },
  methods: {
    enviarComentario () {
      const movimiento = {
        Detalle: 'Cadete: ' + this.comentario.Detalle,
        IdCaso: this.comentario.IdCaso,
        FechaAlta: moment().format('YYYY-MM-DD HH:mm:ss'),
        IdTipoMov: 14,
        Color: 'primary'
      }
      request.Post('/movimientos', movimiento, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.$q.notify('Se agrego un comentario al caso')
        }
      })
      this.modalComentario = false
      this.comentario = {}
    },
    guardarGeo (coords, domicilio) {
      const i = this.casoAux.i
      this.casos[i].Domicilio = domicilio
      this.casos[i] = {...this.casos[i], ...coords}

      this.casoAux.Domicilio = domicilio
      this.casoAux = {...this.casoAux, ...coords}

      this.modalMap = false

      this.editarCaso(this.casoAux)
    },
    abrirMapa (caso, index) {
      caso.i = index
      this.casoAux = caso

      this.modalMap = true
    },
    buscarCasos (p = 1, seccion = 'casos', visitado = '', limit = 30) {
      const offset = (p - 1) * 30
      this.pag = p
      this.casos = []
      let finalizado = ''
      let estados = JSON.stringify([])

      if (this.$route.query.mode === 'F') {
        visitado = ''
        finalizado = 'S'
        estados = JSON.stringify(['FINALIZADO'])
      }

      const params = {
        Cadena: this.busqueda,
        Documento: '',
        Offset: offset,
        Limit: limit,
        Visitado: visitado,
        Finalizado: finalizado,
        Estados: estados
      }

      request.Get('/casos-pendientes', params, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          r.forEach(c => {
            r.Visitado = 0
            r.IdEstadoCasoPendiente = parseInt(r.IdEstadoCasoPendiente)
          })
          this.casos = r
          const cant = r.length ? r[0].Cant : 0
          this.maxpags = Math.ceil(parseInt(cant) / 30)
          this.loading = false
          this.seccion = seccion

          if (seccion === 'visitas') {
            this.$nextTick(() => {
              this.generarMapa()
            })
          }
        }
      })
    },
    editarCaso (caso) {
      const c = {
        IdCasoPendiente: caso.IdCasoPendiente,
        Nombres: caso.Nombres,
        Apellidos: caso.Apellidos,
        Domicilio: caso.Domicilio,
        Documento: caso.Documento,
        Telefono: caso.Telefono,
        Prioridad: caso.Prioridad,
        IdEstadoCasoPendiente: caso.IdEstadoCasoPendiente,
        IdOrigen: caso.IdOrigen,
        Visitado: caso.Visitado,
        Latitud: caso.Latitud,
        Longitud: caso.Longitud,
        Lesion: caso.Lesion
      }
      this.modalEditar = false
      request.Post('/casos-pendientes/editar', {caso: JSON.stringify(c)}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          const i = this.casos.findIndex(c => c.IdCasoPendiente === caso.IdCasoPendiente)
          this.casos[i] = caso
          this.casoAux = {}
          Notify.create('Los datos se actualizaron correctamente')
          if (caso.Visitado === 1) {
            caso.marker.setMap(null)
          }
          if (this.comentario.Detalle) {
            this.enviarComentario()
          }
        }
      })
    },
    generarMapa () {
      this.loaderMap = new Loader({
        apiKey: 'AIzaSyDw8NFG-Llw1Kytr1SqaJ0aT7G4lRlP38Y',
        version: 'weekly'
      })
      this.loaderMap.load().then(() => {
        // eslint-disable-next-line no-undef
        this.map = new google.maps.Map(document.getElementById('mapCad'), {
          zoom: 13,
          center: {
            lat: -26.8241400,
            lng: -65.2226000
          }
        })
        this.casos.forEach((c, i) => {
          if (c.Latitud) {
            this.addMarker(c, i)
          }
        })
      })
    },
    addMarker (c, i) {
      const iconBase = 'http://maps.google.com/mapfiles/kml/paddle/'
      // eslint-disable-next-line no-undef
      c.marker = new google.maps.Marker({
        position: {
          lat: parseFloat(c.Latitud),
          lng: parseFloat(c.Longitud)
        },
        map: this.map,
        icon: iconBase + (this.$route.query.mode === 'F' ? 'wht-square-lv.png' : this.opPrioridad[parseInt(c.Prioridad)].icon)
      })
      // Creo la ventana con su respectivo contenido
      // eslint-disable-next-line no-undef
      c.infoWindow = new google.maps.InfoWindow({
        content: `<div class="ventana">
                    <table class="tabla-ventana">  
                      <th colspan="2"><span class="titulo-tabla">${c.Domicilio.split(',').slice(0, -2).join(',').toUpperCase()}<br></span></th>
                      <tr><td><b>Nombre:</b></td><td class="celda-value">${c.Apellidos}, ${c.Nombres}</td></tr>
                      <tr><td><b>Teléfono:</b></td><td class="celda-value">${c.Telefono || ' --- '}</td></tr>
                      <tr><td><b>Prioridad:</b></td><td class="celda-value ${this.opPrioridad[parseInt(c.Prioridad)].class}">${this.opPrioridad[parseInt(c.Prioridad)].prioridad}</td></tr>
                    </table>
                    <div class="row row-center">
                      <button id="editar-${c.IdCasoPendiente}" class="btn btn-blue btn-ventana col-6 cursor-pointer" type="button">Editar datos</button>
                      <button id="visitado-${c.IdCasoPendiente}" class="btn btn-green btn-ventana col-6 cursor-pointer" type="button"><b>Visitado</b></button>
                    </div>
                  </div>`
      })

      // Evento para abrir ventana del marcador al hacerle click
      c.marker.addListener('click', () => {
        c.infoWindow.open(this.map, c.marker)
        this.$nextTick(() => {
          c.botonEditar = document.getElementById(`editar-${c.IdCasoPendiente}`)
          c.botonVisitado = document.getElementById(`visitado-${c.IdCasoPendiente}`)
          c.botonComentario = document.getElementById(`comentario-${c.IdCasoPendiente}`)

          c.botonEditar.addEventListener('click', () => {
            this.modalEditar = true
            this.casoAux = this.casos[i]
            this.casoAux.Visitado = 0
            this.casoAux.IdEstadoCasoPendiente = parseInt(this.casoAux.IdEstadoCasoPendiente)
            this.comentario.IdCaso = this.casos[i].IdCaso
            this.comentario.Detalle = ''
          })
          c.botonVisitado.addEventListener('click', () => {
            this.casos[i].Visitado = 1
            this.editarCaso(this.casos[i])
          })
        })
      })

      this.casos[i] = c
    }
  }
}
</script>

<style>
.logo {
  height: 35px;
  width: 35px;
  margin-left: 0.7em;
}

.map-header {
  background-color: black;
  background-size: cover;
  background-position: center;
  height: 50px;
}

.welcome-container {
    text-align: center;
    position: absolute;
    top: 25%;
    left: 50%;
    transform: translate(-50%,-50%);
    width: 100%;
}

.welcome-container span {
    display: block;
}

.text1 {
    text-transform: uppercase;
    font-size: 35px;
    font-weight: 700;
    letter-spacing: 2px;
    margin-bottom: 20px;
    position: relative;
    margin-bottom: 40px;
    animation: text 1.1s 1;
}

.text2 {
    text-transform: capitalize;
    font-weight: 700;
    font-size: 25px;
    color: #3742fa;
    animation: text 1.1s 1;
}

@keyframes text {
  0%{
    color: #ced6e0;
  }
  30%{
    letter-spacing: 7px;
  }
  85%{
    letter-spacing: 4px;
  }
}

.buttons-container {
    text-align: center;
    position: absolute;
    top: 65%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.buttons-container > button {
  width: 250px;
}

.visitas {
    animation: show 0.5s ease-in-out;
}

.buscar {
    animation: show 0.5s ease-in-out;
}

@keyframes show {
  0%{
    width: 0px;
    letter-spacing: 0px !important;
  }
  50%{
    width: 125px;
    letter-spacing: 0px !important;
  }
  100%{
    width: 250px;
  }
}

#mapCad {
  height: calc(100vh - 50px);
  width: 100%;
}

.buscador_calles {
    position: absolute;
    bottom: 40px;
    left: 5px;
    width: 95%;
    flex-direction: row;
}

.buscador {
    border-radius: 5px;
    height: 35px;
    padding: 0 10px;
    background-color: white;
    width: 62vw;
}

.boton_buscador {
    background-color: dodgerblue;
    border-radius: 10px;
    width: 18vw;
    height: 35px;
    border: none;
}

.inline_element {
    display: inline-block;
}

.fa-search {
    color: white;
}

.titulo-tabla {
    color: darkblue;
    margin: 0 auto;
}

.celda-value {
    font-weight: 400;
}

.prioridad_alta {
   background-color: #EA2027;
   color: white;
   font-weight: 600;
   text-align: center;
}

.prioridad_media {
    background-color: #FFC312;
    color: white;
    font-weight: 600;
    text-align: center;
}

.prioridad_baja {
    background-color: #1289A7;
    color: white;
    font-weight: 600;
    text-align: center;
}

.btn {
    border: none;
    border-radius: 10em;
    padding: 0.5em 1em;
    margin-top: 1.3em;
}

.btn-blue {
    background-color: #0a3d62;
    color: white;
}

.btn-green {
    background-color: #27ae60;
    color: white;
    font-weight: 500;
}

.btn-yellow {
    background-color: yellow;
    color: black;
    font-weight: 500;
}
</style>
