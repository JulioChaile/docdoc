<template>
  <div class="full-width full-height column">
    <div class="col-9 row">
      <div
        :class="verSimilares ? 'col-6' : 'col-12'"
        id="mapGeo"
      >
      </div>
      <div
        v-if="verSimilares"
        class="col-6 full-height domicilios-class"
      >
        <div class="full-width text-center text-subtitle1 text-bold q-pa-md">
          Domicilios Similares
        </div>

        <q-separator />

        <div class="q-pa-sm column items-center full-width">
          <div
            v-for="c in domiciliosSimilares"
            :key="c.IdCasoPendiente"
            class="full-width row"
          >
            <div class="q-pt-sm col-12 text-center text-bold q-subheading">
              {{ c.Apellidos + ', ' + c.Nombres }}
            </div>
            <div class="col-6 column text-start q-px-md q-py-sm">
              <span
                class="text-caption text-grey-14"
              >
                Domicilio
              </span>
              <span
                class="text-subtitle2"
              >
                {{ c.Domicilio }}
              </span>
            </div>
            <div class="col-6 column text-start q-px-md q-py-sm">
              <span
                class="text-caption text-grey-14"
              >
                Telefono
              </span>
              <span
                class="text-subtitle2"
              >
                {{ c.Telefono }}
              </span>
            </div>
            <q-separator />
          </div>
        </div>
      </div>
    </div>
    <div class="q-pa-md col-3">
      <q-input
        class="full-width q-mb-sm"
        v-model="domicilio"
        label="Domicilio"
        type="text"
        @keyup.enter="localizar"
      />
      <div
        v-if="desdeDocDoc"
        class="row full-width items-center"
      >
        <div class="col-3 flex justify-center">
          <q-btn
            v-if="!buscando"
            dense
            no-caps
            class="q-mt-sm q-mr-md full-width"
            label="Buscar similitudes"
            @click="buscarSimilitudes()"
            color="primary"
          />
          <q-spinner
            v-else
            color="primary"
            class="self-center"
            size="2em"
          />
        </div>
        <q-input
          class="col-3 q-ml-md q-mr-xs"
          dense
          v-model="distancia"
          label="Distancia"
          type="number"
        />
        m
      </div>
      <div class="column items-center justify-center">
        <q-btn
          push
          no-caps
          class="q-mt-sm"
          label="Geolozalicar Nuevamente"
          @click="localizar"
          color="primary"
        />
        <div class="q-mt-sm">
          <q-btn
            push
            no-caps
            class="q-mr-xs"
            label="Confirmar"
            @click="$emit('confirmado', coords, domicilio)"
            color="positive"
          />
          <q-btn
            push
            no-caps
            class="q-ml-xs"
            label="Cancelar"
            @click="$emit('cerrar')"
            color="negative"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Loader } from '@googlemaps/js-api-loader'
import { Notify, QSpinner } from 'quasar'
import axios from 'axios'
import Loading from '../../components/Loading'
import request from '../../request'

export default {
  name: 'MapaGeo',
  components: {
    Loading,
    QSpinner
  },
  props: [
    'Caso',
    'desdeDocDoc'
  ],
  data () {
    return {
      loaderMap: null,
      map: null,
      apiKey: 'AIzaSyDw8NFG-Llw1Kytr1SqaJ0aT7G4lRlP38Y',
      coords: {},
      marker: null,
      markersSimilares: [],
      domiciliosSimilares: [],
      infoWindow: null,
      domicilio: '',
      distancia: 500,
      buscando: false,
      verSimilares: false
    }
  },
  mounted () {
    this.domicilio = this.Caso.Domicilio
    this.loaderMap = new Loader({
      apiKey: this.apiKey,
      version: 'weekly'
    })
    this.loaderMap.load().then(() => {
      // eslint-disable-next-line no-undef
      this.map = new google.maps.Map(document.getElementById('mapGeo'), {
        zoom: 15,
        center: {
          lat: -26.8241400,
          lng: -65.2226000
        }
      })
      if (this.Caso.Latitud) {
        this.coords = {
          Latitud: parseFloat(this.Caso.Latitud),
          Longitud: parseFloat(this.Caso.Longitud)
        }
        this.map.setCenter({
          lat: this.coords.Latitud,
          lng: this.coords.Longitud
        })
        this.addMarker()
      } else {
        this.localizar()
      }
    })
  },
  methods: {
    localizar () {
      axios.get('https://maps.googleapis.com/maps/api/geocode/json', {
        params: {
          address: this.domicilio + ', Tucuman',
          key: this.apiKey
        }
      })
        .then(res => {
          if (res.data.status === 'OK') {
            this.coords = {
              Latitud: res.data.results[0].geometry.location.lat,
              Longitud: res.data.results[0].geometry.location.lng
            }
            this.map.setCenter({
              lat: res.data.results[0].geometry.location.lat,
              lng: res.data.results[0].geometry.location.lng
            })

            this.addMarker()
          } else {
            Notify.create('No se encontro la direccion buscada.')
          }
        })
    },
    addMarker (props) {
      if (this.marker) {
        this.marker.setMap(null)
      }
      // eslint-disable-next-line no-undef
      this.marker = new google.maps.Marker({
        position: {
          lat: this.coords.Latitud,
          lng: this.coords.Longitud
        },
        map: this.map
      })
      // Creo la ventana con su respectivo contenido
      // eslint-disable-next-line no-undef
      this.infoWindow = new google.maps.InfoWindow({
        content: `<div class="ventana">
                    <table class="tabla-ventana">
                        <th colspan="2"><span>${this.domicilio.split(',').slice(0, -2).join(',').toUpperCase()}<br></span></th>
                        <tr><td><b>DNI:</b></td><td class="celda-value">${this.Caso.Documento}</td></tr>
                        <tr><td><b>Apellido:</b></td><td class="celda-value">${this.Caso.Apellidos}</td></tr>
                        <tr><td><b>Nombre:</b></td><td class="celda-value">${this.Caso.Nombres}</td></tr>
                    </table>
                  </div>`
      })

      // Evento para abrir ventana del marcador al hacerle click
      this.marker.addListener('click', () => {
        this.infoWindow.open(this.map, this.marker)
      })
    },
    addMarkerSimilares (u) {
      this.markersSimilares.forEach(m => {
        m.marker.setMap(null)
      })

      this.markersSimilares = []

      u.forEach(c => {
        // eslint-disable-next-line no-undef
        const marker = new google.maps.Marker({
          position: {
            lat: parseFloat(c.Latitud),
            lng: parseFloat(c.Longitud)
          },
          map: this.map,
          icon: 'http://maps.google.com/mapfiles/kml/paddle/blu-circle-lv.png'
        })

        // eslint-disable-next-line no-undef
        const infoWindow = new google.maps.InfoWindow({
          content: `<div class="ventana">
                      <table class="tabla-ventana">
                          <th colspan="2"><span>${c.Domicilio.split(',').slice(0, -2).join(',').toUpperCase()}<br></span></th>
                          <tr><td><b>DNI:</b></td><td class="celda-value">${c.Documento}</td></tr>
                          <tr><td><b>Apellido:</b></td><td class="celda-value">${c.Apellidos}</td></tr>
                          <tr><td><b>Nombre:</b></td><td class="celda-value">${c.Nombres}</td></tr>
                          <tr><td><b>Telefono:</b></td><td class="celda-value">${c.Telefono}</td></tr>
                          <tr><td><b>Domicilio:</b></td><td class="celda-value">${c.Domicilio}</td></tr>
                      </table>
                    </div>`
        })

        marker.addListener('click', () => {
          infoWindow.open(this.map, marker)
        })

        this.markersSimilares.push({ marker, infoWindow })
      })
    },
    buscarSimilitudes () {
      this.buscando = true

      const d = parseFloat(this.distancia) / 1000
      const params = {
        Latitud: this.coords.Latitud,
        Longitud: this.coords.Longitud,
        Distancia: d,
        Domicilio: this.domicilio.toLowerCase().replace('san miguel', '').replace('tucuman', '').replace('capital', '')
      }

      request.Get('/casos-pendientes/similitudes', params, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.buscando = false

          this.addMarkerSimilares(r.Ubicaciones)

          this.domiciliosSimilares = [ ...r.Domicilios, ...r.DomiciliosPersonas ]

          this.verSimilares = true
        }
      })
    }
  }
}
</script>

<style scoped>
.loading {
  width: 100px;
  overflow: hidden;
}

.ventana {
    height: auto;
}

.titulo-tabla {
    color: darkblue;
    margin: 0 auto;
}

.celda-value {
    font-weight: 400;
}

.domicilios-class {
  overflow: scroll;
  height: 100%;
}
</style>
