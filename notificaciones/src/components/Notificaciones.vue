<template>
  <v-container fluid style="padding: 0px;">

    <v-dialog
      v-if="mensajeNotificaciones.length"
      v-model="dialogoMensaje"
      width="500"
    >
      <v-card>
        <v-card-title
          class="headline grey lighten-2"
          primary-title
        >
          {{ mensajeNotificaciones[0] }}
        </v-card-title>

        <v-card-text>
          <div
            v-for="(m, i) in mensajeNotificaciones"
            :key="i"
            v-if="i !== 0" 
            :style="`${m === 'ATENCIÓN' ? 'font-weight: 700;' : ''} font-size: 16px; margin-top: 5px;`">
            {{ m }}
          </div>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            flat
            @click="dialogoMensaje = false"
          >
            Entendido
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Lista principal de notificaciones -->
    <v-slide-y-transition v-if="hayNotificaciones" mode="out-in">
      <v-layout column align-center>
        <v-card style="max-width: 100vw; width: 100vw;">
          <v-list subheader>
            <v-subheader style="font-family: Lobster, cursive;">{{ mensajeNotificaciones[0] }}</v-subheader>
            <v-btn
              v-if="loading"
              :disabled="loading"
              :loading="loading"
              class="white--text"
              color="purple darken-2"
            >
            Cargando...
            </v-btn>
            <div v-else v-for="(notif, nombre) in notificaciones" :key="nombre">
              <v-divider/>
              <v-list-tile ripple avatar @click="verNotificacion(nombre)">
                <v-list-tile-content>
                  <v-list-tile-title style="font-family: 'Montserrat', sans-serif;">
                    {{ nombre }}
                  </v-list-tile-title>
                </v-list-tile-content>
              </v-list-tile>
            </div>
            
          </v-list>
        </v-card>
      </v-layout>
    </v-slide-y-transition>

    <!-- Mensaje por defecto si no hay notificaciones -->
    <v-slide-y-transition v-else mode="out-in">
      <v-layout column align-center>
        <v-btn
          v-if="loading"
          :disabled="loading"
          :loading="loading"
          class="white--text"
          color="purple darken-2"
        >
        Cargando...
        </v-btn>
        <div v-else style="max-width: 100vw; width: 100vw; padding: 10px; margin-left: 10px;font-family: 'Montserrat', sans-serif;">
          <div class="headline" style="font-family: 'Montserrat', sans-serif !important;font-weight: 600;">No hay notificaciones</div>
          <div style="margin-top: 10px">
            Para buscar notificaciones, cargue campos <strong>pulsando la lupa</strong> en la esquina
            inferior derecha.
          </div>
          <div style="margin-top: 10px">
            Los campos pueden ser <strong>Nombres, Apellidos, Juzgados, Nominaciones o Expedientes</strong>.
          </div>
          <div style="margin-top: 10px">
            Se pueden escribir varios campos separados por espacios. Por ejemplo: <strong>Gonzalo civil 2da</strong>
            buscará todas las notificaciones de Gonzalo para la segunda nominación de civil.
          </div>
        </div>
      </v-layout>
    </v-slide-y-transition>

    <!-- Dialogo de busqueda de personas -->
    <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
      <v-card>
        <v-toolbar dark>
          <v-btn icon dark @click.native="dialog=false">
            <v-icon>arrow_back</v-icon>
          </v-btn>
          <v-toolbar-title>Búsqueda</v-toolbar-title>
        </v-toolbar>
        <v-text-field
          style="margin-left: 10px; margin-right: 10px;"
          v-model="newNombre"
          @keyup.enter="agregarNombre"
          placeholder="Escribí el campo que querés buscar"
          autofocus
        >
        </v-text-field>
        <v-list subheader>
          <v-subheader>
            <div>
              Campos filtrados
            </div>
            <v-spacer/>
          </v-subheader>
          <v-list-tile avatar v-for="(nombre, i) in nombres" :key="i">
            <v-list-tile-content>
              <v-list-tile-title>{{ nombre }}</v-list-tile-title>
            </v-list-tile-content>
            <v-list-tile-action @click="eliminarNombre(i)">
              <v-list-tile-action-text>Eliminar</v-list-tile-action-text>
              <v-icon
                color="red darken-2"
              >
                delete
              </v-icon>
            </v-list-tile-action>
          </v-list-tile>
          <v-list-tile v-if="!nombres.length">
            No hay campos filtrados
          </v-list-tile>
        </v-list>
      </v-card>
      <v-btn flat color="green" @click="guardarNombres" style="position: fixed; bottom: 0; right: 0;">
        <v-icon>search</v-icon>
        Buscar
      </v-btn>
    </v-dialog>

    <!-- Dialogo de detalle de la notificacion -->
    <v-dialog
      v-model="dialog2"
      width="500"
    >
      <v-card>
        <v-card-title
          class="headline grey lighten-2"
          primary-title
        >
          {{ notifDialog }}
        </v-card-title>

        <v-card-text>
          <div class="paper" v-for="(notif, i) in notificaciones[notifDialog]" :key="i">
            <hr v-if="i !== 0" style="margin-bottom: 20px;">
            <div style="font-size: 20px;font-family: 'Montserrat', sans-serif;" v-for="(n, k) in mapNotificacion(notif)" :key="k">
              <strong>{{ k }}:</strong> {{ n }}
            </div>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>
<script>
import axios from 'axios'
export default {
  data() {
    return {
      error: '',
      dialog: false,
      dialogoMensaje: true,
      dialog2: false,
      nombres: [],
      notificaciones: {},
      newNombre: '',
      loading: false,
      notifDialog: '',
      hayNotificaciones: false,
      mensajeNotificaciones: [],
      keywords: [
        'DOCUMENTOS Y LOCACIONES',
        'CAM. EN LO CIVIL Y COMERCIAL COMUN',
        'CIVIL Y COMERCIAL COMUN',
        'DE COBROS APREMIOS',
        'CAM. EN LO CONTENCIOSO ADMINISTRATIVO',
        'FAMILIA Y SUCESIONES',
        'EXCMA. CORTE SUPREMA DE JUSTICIA',
        'CONCILIACION Y TRAMITE',
        'CAM. DE TRABAJO',
      ]
    }
  },
  props: ['abrirDialogo'],
  watch: {
    abrirDialogo (n, o) {
      this.dialog = true
    }
  },
  created() {
    let d = new Date()
    let notificacionActual = `NotificacionesDocDoc${d.getDate()}-${d.getMonth()+1}-${d.getFullYear()}`
    if (sessionStorage.getItem('DocDocListarNombres')) {
      this.nombres = JSON.parse(sessionStorage.getItem('DocDocListarNombres'))
    }
    if (sessionStorage.getItem(notificacionActual)) {
      this.calcularNotificaciones()
      return
    }
    for (let n in sessionStorage) {
      if (n.includes('NotificacionesDocDoc')) {
        sessionStorage.removeItem(n)
      }
    }

    this.loading = true
    axios.get('https://io.docdoc.com.ar/api/notificaciones')
      .then(({data}) => {
        this.loading = false
        if (data.Error) {
          this.error = data.Error
        } else {
          sessionStorage.setItem(notificacionActual, data.Mensaje)
          this.calcularNotificaciones()
        }
      })
      .catch(err => {
        this.loading = false
        this.error = 'Ocurrió un error inesperado. Intente de nuevo más tarde.'
      })
  },
  methods: {
    calcularNotificaciones() {
      this.loading = true
      setImmediate(() => {
        let d = new Date()
        let notificacionActual = `NotificacionesDocDoc${d.getDate()}-${d.getMonth()+1}-${d.getFullYear()}`
        let archivo = sessionStorage.getItem(notificacionActual).split('\n')

        // Busco el mensaje de alerta inicial
        let end = 7;
        for (; end < archivo.length; ++end) {
          if (archivo[end+1].trim() !== '' && archivo[end+2].trim() !== '' && archivo[end+3].trim() !== '') {
            break
          }
        }

        this.mensajeNotificaciones = archivo.slice(0, end).map(l => l.trim()).filter(l => l !== '')

        // Inicializacion del vector donde se guardaran las notificaciones temporalmente
        let notifs = []

        // Itero en todas las lineas del archivo
        for (let i = end; i < archivo.length; i++) {
          // Parseo las notificaciones
          let notif = this.parseNotificacion(archivo[i])

          // Itero en todos los campos de busqueda
          for (let k = 0; k < this.nombres.length; k++) {
            let nombre = this.nombres[k]

            // Si el campo de busqueda no lleva expediente -> No lo incluyo en la busqueda
            let notifJoin = (nombre.includes('/') ? notif.join('') : notif.slice(0, 3).join('')).toUpperCase()

            // Si el campo de busqueda es multicampo
            if (nombre.includes(' ')) {
              let campos = nombre.split(' ')
              let includesAll = true

              // Itero entre todos los subcampos
              for (let j = 0; j < campos.length; j++) {

                // Si alguno de los subcampos no esta contenido en la notificacion
                // se setea el flag en false
                if (!notifJoin.includes(campos[j].trim().toUpperCase())) {
                  includesAll = false
                  break
                }
              }

              // Si todos los subcampos aparecen en la notificacion -> Agrego nueva notificacion
              if (includesAll) {
                notifs.push(notif)
              }
            // Si el campo de busqueda es simple y la notificacion lo contiene -> Agrego nueva notificacion
            } else if (notifJoin.includes(nombre.toUpperCase())) {
              notifs.push(notif)
            }
          }
        }

        // Limpio las notificaciones si es que habia alguna
        this.notificaciones = {}

        // Por cada una de las notificaciones que no sea indefinida, agrego una entrada al diccionario
        // de notificaciones
        notifs = notifs.filter(n => n[0])
        notifs.forEach(n => {
          let nombre = n[0]
          if (!this.notificaciones[nombre]) {
            this.notificaciones[nombre] = []
          }

          // Si la notificacion no está agregada ya, entonces la agrego
          if (!this.notificaciones[nombre].filter(noti => noti[2] === n[3]).length) {
            this.notificaciones[nombre].push(n.slice(1, n.length))
          }
        })

      
        this.hayNotificaciones = notifs.length > 0
        this.loading = false
      })
    },
    parseNotificacion(notif) {
      let keyword = ''
      for (let i = 0; i < this.keywords.length; i++) {
        if (notif.includes(this.keywords[i])) {
          keyword = this.keywords[i]
          notif = notif.replace(this.keywords[i], '')
          break
        }
      }
      if (!keyword) {
        return []
      }

      let result = notif.split('  ').map(a => a.trim()).filter(a => a !== '')
      switch (result.length) {
        case 4:
          result = [result[0], keyword, result[1], result[2]]
          break;
        case 5:
          result = [`${result[0]} ${result[1]}`, keyword, result[2], result[3]]
          break;
        case 6:
          result = [`${result[0]} ${result[1]} ${result[2]}`, keyword, result[3], result[4]]
          break;
        case 7:
          result = [`${result[0]} ${result[1]} ${result[2]} ${result[3]}`, keyword, result[4], result[5]]
          break;
        default:
          result = []
      }
      
      return result
    },
    agregarNotificacion() {
      if (sessionStorage.getItem('DocDocListarNombres')) {
        this.nombres = JSON.parse(sessionStorage.getItem('DocDocListarNombres'))
      } else {
        this.nombres = []
      }
      this.dialog = true
    },
    eliminarNombre(i) {
      this.nombres.splice(i, 1)
    },
    agregarNombre() {
      this.nombres.push(this.newNombre)
      this.newNombre = ''
    },
    guardarNombres() {
      if (this.newNombre) {
        this.nombres.push(this.newNombre)
        this.newNombre = ''
      }
      sessionStorage.setItem('DocDocListarNombres', JSON.stringify(this.nombres))
      this.calcularNotificaciones()
      this.dialog = false
    },
    verNotificacion(nombre) {
      this.dialog2 = true
      this.notifDialog = nombre
    },
    mapNotificacion(notif) {
      return {
        Juzgado: notif[0],
        Nominacion: notif[1],
        Expediente: notif[2]
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
.paper {
  background: #fff;
  box-shadow:
    0 1px 1px rgba(0,0,0,0.15),
    0 10px 0 -5px #eee,
    0 10px 1px -4px rgba(0,0,0,0.15),
    0 20px 0 -10px #eee,
    0 20px 1px -9px rgba(0,0,0,0.15);
    padding-left: 30px;
    padding-right: 30px;
    padding-top: 10px;
    padding-bottom: 10px;
}
</style>
