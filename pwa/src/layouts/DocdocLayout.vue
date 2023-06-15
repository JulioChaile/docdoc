<template>
  <q-layout view="hHh Lpr lff" class="shadow-2 rounded-borders">
    <q-header elevated class="bg-black">
      <q-toolbar v-if="toolbar" id="contenedorToolbar" color="primary">
        <q-btn
          v-if="isLoggedIn"
          flat
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
          class="logo-menu"
        >
          <img src="statics/img/logo.png" width="30" height="25" />
        </q-btn>

        <q-toolbar-title>{{ $route.name }}</q-toolbar-title>
        {{$route.params.saludo}}
        <q-dialog v-model="screenshotModal" persistent>
          <q-card style="width: 700px; max-width: 80vw;">
            <div class="row">
              <div class="col-12" style="display:flex; justify-content: center;">
                <q-card-section style="padding: 20px 5px">
                  <div id="screenshot_container"></div>
                </q-card-section>
              </div>
              <div class="col-12">
                <q-card-section style="padding: 0 20px;">
                  <q-input
                    filled
                    v-model="screenshot_titulo"
                    label="Escriba el titulo del problema"
                    maxlength="20"
                    :rules="[val => !!val || 'Este campo es requerido']"
                  ></q-input>
                </q-card-section>
              </div>
              <div class="col-12">
                <q-card-section style="padding: 0 20px;">
                  <q-input
                    v-model="screenshot_descripcion"
                    type="textarea"
                    filled
                    clearable
                    label="Escriba aquí una descripción detallada del problema (min. 100 caracteres)"
                    :rules="[
                      val => !!val || 'Este campo es requerido',
                      val => val.length >= 100 || 'Por favor escriba al menos 100 caracteres',
                    ]"
                    :shadow-text="textareaShadowText"
                    @keydown="processTextareaFill"
                    @focus="processTextareaFill"
                  />
                </q-card-section>
              </div>
              <div class="col-12">
                <q-card-actions align="right" class="bg-white">
                  <q-btn flat @click="cerrarScreenshotModal">
                    <span class="text-negative">Cancelar</span>
                  </q-btn>
                  <q-btn
                    v-if="!loading"
                    flat
                    class="text-primary"
                    label="Enviar"
                    @click="enviarScreenshot"
                  />
                  <q-btn v-else flat>
                    <LoadingInButton :width="70" :height="25" />
                  </q-btn>
                </q-card-actions>
              </div>
            </div>
          </q-card>
        </q-dialog>
        <div>
          <q-badge v-if="(cantidadNotificaciones + notificacionesExterno.length) !== 0 || verVencimientos || CasosEtiquetado.length !== 0" color="red">
            <div v-if="(cantidadNotificaciones + notificacionesExterno.length) !== 0">
              {{cantidadNotificaciones + notificacionesExterno.length}}
              <q-icon class="on-center" name="mdi-message-text" />
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 10]">
                Tienes {{ cantidadNotificaciones + notificacionesExterno.length }} nuevos mensajes
              </q-tooltip>
              {{verVencimientos ? "&nbsp;-&nbsp;" : ""}}
            </div>
            <div v-if="verVencimientos">
              <q-icon class="on-center" name="fas fa-exclamation" />
              <q-tooltip>
                Tienes movimientos a punto de vencerse
              </q-tooltip>
              {{CasosEtiquetado !== 0 ? "&nbsp;-&nbsp;" : ""}}
            </div>
            <div v-if="CasosEtiquetado.length !== 0">
              {{CasosEtiquetado.length}}
              <q-icon class="on-center" name="forum" />
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 10]">
                Tienes {{ CasosEtiquetado.length }} casos en los que fuiste etiquetado
              </q-tooltip>
            </div>
          </q-badge>
          <q-btn-dropdown flat :label="mostrarUsuario()">
            <ventana-usuario
              :notificaciones="notificacionesComp"
              :notificacionesMediadores="notificacionesMediadoresComp"
              :notificacionesContactos="notificacionesContactosComp"
              :notificacionesInterno="notificacionesInternoComp"
              :notificacionesExterno="notificacionesExterno"
              :CasosEtiquetado="CasosEtiquetado"
              @comentarioVisto="comentarioVisto"
              @notificacionLeida="confirmarNotificacion"
              :verVencimientos="verVencimientos"
              @vencimientosVistos="verVencimientos = false"
            />
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>
    <q-drawer
      v-model="leftDrawerOpen"
      v-if="isLoggedIn"
      :width="200"
      :breakpoint="500"
      bordered
      content-class="bg-grey-3"
      overlay
    >
      <q-list no-border link inset-delimiter highlight class="text-caption list-menu">
        <q-item-label header>Accesos Directos</q-item-label>
        <q-item to="/#" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="home" />
          </q-item-section>
          <q-item-section>Home</q-item-section>
        </q-item>
        <q-item to="/Vencimientos" target="_blank" style="display:flex; align-items:center;" @click="irPestañaNueva()">
          <q-item-section side top>
            <q-icon name="new_releases" />
          </q-item-section>
          <q-item-section>Vencimientos</q-item-section>
        </q-item>
        <q-item to="/Tribunales" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="account_balance" />
          </q-item-section>
          <q-item-section>Tribunales</q-item-section>
        </q-item>
        <q-item to="/Casos" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="description" />
          </q-item-section>
          <q-item-section>Casos</q-item-section>
        </q-item>
        <q-item to="/GrillaCasos" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="description" />
          </q-item-section>
          <q-item-section>Grilla Casos</q-item-section>
        </q-item>
        <q-item to="/CasosPendientes" target="_blank" style="display:flex; align-items:center;" @click="irPestañaNueva()">
          <q-item-section side top>
            <q-icon name="pending_actions" />
          </q-item-section>
          <q-item-section>Casos Pendientes</q-item-section>
        </q-item>
        <q-item to="/Mediaciones" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="work" />
          </q-item-section>
          <q-item-section>Mediaciones</q-item-section>
        </q-item>
        <q-item to="/Judiciales" target="_blank" style="display:flex; align-items:center;" @click="irPestañaNueva()">
          <q-item-section side top>
            <q-icon name="table_chart" />
          </q-item-section>
          <q-item-section>Judiciales</q-item-section>
        </q-item>
        <q-item to="/Cedulas" target="_blank" style="display:flex; align-items:center;" @click="irPestañaNueva()">
          <q-item-section side top>
            <q-icon name="table_chart" />
          </q-item-section>
          <q-item-section>Cedulas</q-item-section>
        </q-item>
        <q-item to="/CausasPenales" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="gavel" />
          </q-item-section>
          <q-item-section>Causas Penales</q-item-section>
        </q-item>
        <q-item to="/Movimientos/0" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="assignment" />
          </q-item-section>
          <q-item-section>Movimientos</q-item-section>
        </q-item>
        <q-item to="/Contactos" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="group" />
          </q-item-section>
          <q-item-section>Contactos</q-item-section>
        </q-item>
        <q-item to="/Calendario" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="event" />
          </q-item-section>
          <q-item-section>Calendario</q-item-section>
        </q-item>
        <q-item to="/Compartidos" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="share" />
          </q-item-section>
          <q-item-section>Compartidos</q-item-section>
        </q-item>
        <q-item to="/Personas" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="group" />
          </q-item-section>
          <q-item-section>Personas</q-item-section>
        </q-item>
        <q-item to="/CumplePersonas" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="cake" />
          </q-item-section>
          <q-item-section>Cumpleaños</q-item-section>
        </q-item>
        <q-item to="/Padron" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="group" />
          </q-item-section>
          <q-item-section>Padrón</q-item-section>
        </q-item>
        <q-item to="/Utilidades" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="build" />
          </q-item-section>
          <q-item-section>Utilidades</q-item-section>
        </q-item>
        <!-- Create a new .css file with these styles? -->
        <!--q-item
          sparse
          style="display:flex; align-items:center;position: absolute; bottom: 0; width: 100%"
          to="/Login"
        >
          <q-item-section side top>
            <q-icon name="exit_to_app" />
          </q-item-section>
          <q-item-section>Cerrar Sesión</q-item-section>
        </q-item-->
      </q-list>
    </q-drawer>

    <q-page-container>
      <div class="nuevo-fondo window-width window-height fixed-top">
      </div>
      <router-view />
    </q-page-container>

    <q-fab
      v-if="isLoggedIn && toolbar"
      class="fixed"
      style="right: 18px; bottom: 90px"
      color="primary"
      icon="add"
      direction="up"
    >
      <q-fab-action color="primary" @click="$router.push('/AltaCaso')" icon="note_add">
        <q-tooltip anchor="center left" self="center right" :offset="[10, 0]">Nuevo Caso</q-tooltip>
      </q-fab-action>
      <q-fab-action color="primary" @click="altaMovimiento" icon="assignment">
        <q-tooltip anchor="center left" self="center right" :offset="[10, 0]">Nuevo Movimiento</q-tooltip>
      </q-fab-action>
    </q-fab>
    <q-btn
      v-if="isLoggedIn && toolbar"
      round
      class="fixed"
      style="right: 18px; bottom: 18px; width: 56px; height: 56px; z-index: 99999;"
      color="primary"
      icon="camera_alt"
      @click="tomarScreenshot"
    >
      <q-tooltip
        anchor="center left"
        selft="center right"
        :offset="[75, 0]"
      >Tomar captura de pantalla</q-tooltip>
    </q-btn>
  </q-layout>
</template>

<script>
import { openURL, Notify } from 'quasar'
import auth from '../auth'
import request from '../request'
import html2canvas from 'html2canvas'
import LoadingInButton from '../components/LoadingInButton'
import VentanaUsuario from '../components/UI/VentanaUsuario'

export default {
  name: 'DocdocLayout',
  data () {
    return {
      leftDrawerOpen: false,
      isLoggedIn: false,
      usuarioLogueado: '',
      Notificaciones: [],
      screenshotModal: false,
      screenshot_titulo: '',
      screenshot_descripcion: '',
      inputFillCancelled: false,
      textareaFillCancelled: false,
      screenshotImage: '',
      loading: false,
      cantidadNotificaciones: 0,
      notificaciones: [],
      notificacionesMediadores: [],
      notificacionesContactos: [],
      notificacionesInterno: [],
      notificacionesExterno: [],
      cantidadNotificacionesExterno: 0,
      // notificaciones: sessionStorage.getItem(this.mostrarUsuario()) ? JSON.parse(sessionStorage.getItem(this.mostrarUsuario())) : []
      verVencimientos: false,
      IdInterval: '',
      CasosEtiquetado: []
    }
  },
  components: {
    LoadingInButton,
    VentanaUsuario
  },
  computed: {
    notificacionesComp () {
      if (this.notificaciones.length) {
        return this.notificaciones
      } else {
        return sessionStorage.getItem(this.mostrarUsuario()) ? JSON.parse(sessionStorage.getItem(this.mostrarUsuario())) : []
      }
    },
    notificacionesMediadoresComp () {
      if (this.notificacionesMediadores.length) {
        return this.notificacionesMediadores
      } else {
        return sessionStorage.getItem('msjMediador') ? JSON.parse(sessionStorage.getItem('msjMediador')) : []
      }
    },
    notificacionesContactosComp () {
      if (this.notificacionesContactos.length) {
        return this.notificacionesContactos
      } else {
        return sessionStorage.getItem('msjContacto') ? JSON.parse(sessionStorage.getItem('msjContacto')) : []
      }
    },
    notificacionesInternoComp () {
      if (this.notificacionesInterno.length) {
        return this.notificacionesInterno
      } else {
        return sessionStorage.getItem('msjInterno') ? JSON.parse(sessionStorage.getItem('msjInterno')) : []
      }
    },
    toolbar () {
      if (this.$route.meta) {
        return !this.$route.meta.withoutToolbar
      }
      return true
    },
    textareaShadowText () {
      if (this.textareaFillCancelled === true) {
        return ''
      }
      const t = '',
        empty =
          typeof this.screenshot_descripcion !== 'string' ||
          this.screenshot_descripcion.length === 0

      if (empty === true) {
        return t.split('\n')[0]
      } else if (t.indexOf(this.screenshot_descripcion) !== 0) {
        return ''
      }

      return t
        .split(this.screenshot_descripcion)
        .slice(1)
        .join(this.screenshot_descripcion)
        .split('\n')[0]
    }
  },
  created () {
    if (this.$route.path === '/Caso') {
      setTimeout(() => {
        this.isLoggedIn = auth.isLoggedIn
        if (this.isLoggedIn) {
          request.Get('/movimientos/movimientos-del-dia', {}, (r) => {
            if (!r.Error) {
              if (r.length > 1) {
                this.verVencimientos = true
              }
            }
          })

          // Loop
          if (localStorage.intervalChatGlobal) {
            clearInterval(localStorage.intervalChatGlobal)
          }
          localStorage.intervalChatGlobal = setInterval(this.buscarNotificaciones, 10000)

          this.buscarComentarios()
          // Loop
          if (localStorage.intervalComentariosGlobal) {
            clearInterval(localStorage.intervalComentariosGlobal)
          }
          localStorage.intervalComentariosGlobal = setInterval(this.buscarComentarios, 300000)
        }
        auth.addCallbackLogin(() => {
          this.isLoggedIn = auth.isLoggedIn
        })
        auth.addCallbackLogin(() => {
          if (this.isLoggedIn) {
            request.Get('/movimientos/movimientos-del-dia', {}, (r) => {
              if (!r.Error) {
                if (r.length > 1) {
                  this.verVencimientos = true
                }
              }
            })

            // Loop
            if (localStorage.intervalChatGlobal) {
              clearInterval(localStorage.intervalChatGlobal)
            }
            localStorage.intervalChatGlobal = setInterval(this.buscarNotificaciones, 10000)

            this.buscarComentarios()
            // Loop
            if (localStorage.intervalComentariosGlobal) {
              clearInterval(localStorage.intervalComentariosGlobal)
            }
            localStorage.intervalComentariosGlobal = setInterval(this.buscarComentarios, 300000)
          }
        })

        let arrayNotificaciones = []
        sessionStorage.setItem(this.mostrarUsuario(), JSON.stringify(arrayNotificaciones))
      }, 1500)

      return
    }

    this.isLoggedIn = auth.isLoggedIn
    if (this.isLoggedIn) {
      request.Get('/movimientos/movimientos-del-dia', {}, (r) => {
        if (!r.Error) {
          if (r.length > 1) {
            this.verVencimientos = true
          }
        }
      })

      // Loop
      if (localStorage.intervalChatGlobal) {
        clearInterval(localStorage.intervalChatGlobal)
      }
      localStorage.intervalChatGlobal = setInterval(this.buscarNotificaciones, 10000)

      this.buscarComentarios()
      // Loop
      if (localStorage.intervalComentariosGlobal) {
        clearInterval(localStorage.intervalComentariosGlobal)
      }
      localStorage.intervalComentariosGlobal = setInterval(this.buscarComentarios, 300000)
    }
    auth.addCallbackLogin(() => {
      this.isLoggedIn = auth.isLoggedIn
    })
    auth.addCallbackLogin(() => {
      if (this.isLoggedIn) {
        request.Get('/movimientos/movimientos-del-dia', {}, (r) => {
          if (!r.Error) {
            if (r.length > 1) {
              this.verVencimientos = true
            }
          }
        })

        // Loop
        if (localStorage.intervalChatGlobal) {
          clearInterval(localStorage.intervalChatGlobal)
        }
        localStorage.intervalChatGlobal = setInterval(this.buscarNotificaciones, 10000)

        this.buscarComentarios()
        // Loop
        if (localStorage.intervalComentariosGlobal) {
          clearInterval(localStorage.intervalComentariosGlobal)
        }
        localStorage.intervalComentariosGlobal = setInterval(this.buscarComentarios, 300000)
      }
    })

    let arrayNotificaciones = []
    sessionStorage.setItem(this.mostrarUsuario(), JSON.stringify(arrayNotificaciones))
  },
  methods: {
    openURL,
    comentarioVisto (id) {
      const i = this.CasosEtiquetado.findIndex(ce => parseInt(ce.IdCaso) === parseInt(id))

      this.CasosEtiquetado.splice(i, 1)

      request.Post('/comentarios-caso/comentario-visto', { IdCaso: id }, r => {
        if (r.Error) console.log(r.Error)
      })
    },
    buscarComentarios () {
      request.Get('/comentarios-caso/comentarios-sin-leer', {}, r => {
        let a = r
        let c = 0

        a.forEach(com => {
          const i = this.CasosEtiquetado.findIndex(ce => parseInt(ce.IdCaso) === parseInt(com.IdCaso))

          if (i < 0) {
            c++
            this.CasosEtiquetado.push(com)
          }
        })

        if (c > 0) {
          Notify.create('Se te ha etiquetado en ' + c + ` caso${c === 1 ? '' : 's'} nuevo${c === 1 ? '' : 's'}`)
        }
      })
    },
    altaMovimiento () {
      let idCaso = 0
      if (this.$route.params && this.$route.params.idCaso) {
        idCaso = this.$route.params.idCaso
      }
      this.$router.push(`/AltaMovimiento/${idCaso}`)
    },
    irPestañaNueva () {
      localStorage.setItem('DOCDOCTOKENPWA', sessionStorage.getItem('DOCDOCTOKENPWA'))
      localStorage.setItem('DOCDOCDEBECAMBIARPASS', sessionStorage.getItem('DOCDOCDEBECAMBIARPASS'))
      localStorage.setItem('DOCDOCUSUARIOLOGUEADO', sessionStorage.getItem('DOCDOCUSUARIOLOGUEADO'))
      localStorage.setItem('toRoute', true)
    },
    mostrarUsuario () {
      let usuario = auth.UsuarioLogueado
      return `${usuario.Apellidos ? usuario.Apellidos : 'sin datos'}, ${
        usuario.Nombres ? usuario.Nombres : 'sin datos'
      }`
    },
    cerrarScreenshotModal () {
      this.screenshotModal = false
      this.screenshot_titulo = ''
      this.screenshot_descripcion = ''
    },
    tomarScreenshot () {
      this.screenshotModal = true
      html2canvas(document.getElementsByTagName('body')[0], {
        dpi: 192,
        x: window.scrollX,
        y: window.scrollY
      }).then(function (canvas) {
        document.getElementById('screenshot_container').appendChild(canvas)
      })
    },
    enviarScreenshot () {
      if (
        this.screenshot_titulo.length &&
        this.screenshot_descripcion.length >= 100
      ) {
        const elementoCanvas = document.getElementById('screenshot_container')
          .firstChild
        var image = elementoCanvas.toDataURL('image/jpeg', 1.0)
        const dataImg = {
          title: this.screenshot_titulo,
          desc: this.screenshot_descripcion,
          image: image.slice(23, image.length)
        }
        this.loading = true

        request.Post('/report', dataImg, (res) => {
          if (!res.error) {
            this.$q.notify({
              color: 'primary',
              timeout: 800,
              message: 'Reporte enviado exitosamente!'
            })
            this.loading = false
            this.screenshotModal = false
            this.screenshot_titulo = ''
            this.screenshot_descripcion = ''
          } else {
            this.$q.notify({
              color: 'secondary',
              timeout: 800,
              message: res.error
            })
            this.screenshot_titulo = ''
            this.screenshot_descripcion = ''
          }
        })
      } else {
        Notify.create('Por favor, complete los campos correctamente')
      }
    },
    buscarNotificaciones () {
      request.Get('/mensajes/nuevos-mensajes', { IdCaso: 0, Cliente: 'S' }, r => {
        if (!r.Error) {
          if (r.Externo.length) {
            let count = 0
            const ids = this.notificacionesExterno.map(n => n.IdChatApi)

            r.Externo.forEach(m => {
              if (!ids.includes(m.IdChatApi)) {
                count++
                this.notificacionesExterno.push({ ...m })
              }
            })

            ids.forEach((n, i) => {
              const index = r.Externo.findIndex(m => m.IdChatApi === n)

              if (index < 0) this.notificacionesExterno.splice(i, 1)
            })

            if (count > 0) Notify.create(`Recibiste ${count} mensajes nuevos en Whatsapp.`)
          }

          if (r.Caso.length || r.Mediador.length || r.Contacto.length || r.Interno.length) {
            let notis = {
              Caso: r.Caso.filter(n => !this.notificacionRepetida(n, 'caso')),
              Mediador: r.Mediador.filter(n => !this.notificacionRepetida(n, 'mediador')),
              Contacto: r.Contacto.filter(n => !this.notificacionRepetida(n, 'contacto')),
              Interno: r.Interno.filter(n => !this.notificacionRepetida(n, 'interno'))
            }

            this.enviarNotificacion(notis)
          }
        } else {
          console.log('Error en el loop global.')
          console.log(r.Error)
        }
      })
    },
    enviarNotificacion (mensajes) {
      if (mensajes.Caso.length || mensajes.Mediador.length || mensajes.Contacto.length || mensajes.Interno.length) {
        const getnotis = tipo => {
          let item = ''

          switch (tipo) {
            case this.mostrarUsuario():
              item = 'Caso'
              break

            case 'msjMediador':
              item = 'Mediador'
              break

            case 'msjContacto':
              item = 'Contacto'
              break

            case 'msjInterno':
              item = 'Interno'
              break

            default:
              item = 'Caso'
          }

          const old = sessionStorage.getItem(tipo)
            ? JSON.parse(sessionStorage.getItem(tipo))
            : []

          return old.concat(mensajes[item])
        }

        let notis = {
          Caso: getnotis(this.mostrarUsuario()),
          Mediador: getnotis('msjMediador'),
          Contacto: getnotis('msjContacto'),
          Interno: getnotis('msjInterno')
        }

        sessionStorage.setItem(this.mostrarUsuario(), JSON.stringify(notis.Caso))
        sessionStorage.setItem('msjMediador', JSON.stringify(notis.Mediador))
        sessionStorage.setItem('msjContacto', JSON.stringify(notis.Contacto))
        sessionStorage.setItem('msjInterno', JSON.stringify(notis.Interno))

        this.notificaciones = notis.Caso
        this.notificacionesMediadores = notis.Mediador
        this.notificacionesContactos = notis.Contacto
        this.notificacionesInterno = notis.Interno

        var hash = {}
        const notificacionesPorCaso = this.notificaciones.filter(c => {
          var exists = !hash[c.IdChat]
          hash[c.IdChat] = true
          return exists
        })

        var hash2 = {}
        const notificacionesPorMediador = this.notificacionesMediadores.filter(c => {
          var exists = !hash2[c.IdChat]
          hash2[c.IdChat] = true
          return exists
        })

        var hash3 = {}
        const notificacionesPorContacto = this.notificacionesContactos.filter(c => {
          var exists = !hash3[c.IdChat]
          hash3[c.IdChat] = true
          return exists
        })

        var hash4 = {}
        const notificacionesPorInterno = this.notificacionesInterno.filter(c => {
          var exists = !hash4[c.IdCaso]
          hash4[c.IdCaso] = true
          return exists
        })

        this.cantidadNotificaciones = notificacionesPorCaso.length + notificacionesPorMediador.length + notificacionesPorContacto.length + notificacionesPorInterno.length
        Notify.create(`Recibiste ${mensajes.Caso.length + mensajes.Mediador.length + mensajes.Contacto.length + mensajes.Interno.length} mensajes nuevos`)
      }
    },
    notificacionRepetida (item, tipo) {
      let result = false

      let itemSession = ''

      switch (tipo) {
        case 'caso':
          itemSession = this.mostrarUsuario()
          break

        case 'mediador':
          itemSession = 'msjMediador'
          break

        case 'contacto':
          itemSession = 'msjContacto'
          break

        case 'interno':
          itemSession = 'msjInterno'
          break
      }

      if (sessionStorage.getItem(itemSession)) {
        const arraySesion = JSON.parse(sessionStorage.getItem(itemSession))
        arraySesion.forEach(mensajeSesion => {
          if (mensajeSesion.IdMensaje === item.IdMensaje) {
            result = true
          }
          if (tipo === 'interno' && mensajeSesion.IdMensajeChatInterno === item.IdMensajeChatInterno) {
            result = true
          }
        })
      }

      return result
    },
    async confirmarNotificacion (data, tipo) {
      const backup = await this.borrarNotificacion(data.item, tipo)
      switch (tipo) {
        case 'caso':
          this.notificaciones = backup
          sessionStorage.setItem(this.mostrarUsuario(), JSON.stringify(backup))
          break

        case 'mediador':
          this.notificacionesMediadores = backup
          sessionStorage.setItem('msjMediador', JSON.stringify(backup))
          break

        case 'contacto':
          this.notificacionesContactos = backup
          sessionStorage.setItem('msjContacto', JSON.stringify(backup))
          break

        case 'interno':
          this.notificacionesInterno = backup
          sessionStorage.setItem('msjInterno', JSON.stringify(backup))
          break
      }
      var hash = {}
      const notificacionesPorCaso = this.notificaciones.filter(c => {
        var exists = !hash[c.IdChat]
        hash[c.IdChat] = true
        return exists
      })

      var hash2 = {}
      const notificacionesPorMediador = this.notificacionesMediadores.filter(c => {
        var exists = !hash2[c.IdChat]
        hash2[c.IdChat] = true
        return exists
      })

      var hash3 = {}
      const notificacionesPorContacto = this.notificacionesContactos.filter(c => {
        var exists = !hash3[c.IdChat]
        hash3[c.IdChat] = true
        return exists
      })

      var hash4 = {}
      const notificacionesPorInterno = this.notificacionesInterno.filter(c => {
        var exists = !hash4[c.IdCaso]
        hash4[c.IdCaso] = true
        return exists
      })
      this.cantidadNotificaciones = notificacionesPorCaso.length + notificacionesPorMediador.length + notificacionesPorContacto.length + notificacionesPorInterno.length
    },
    borrarNotificacion (item, tipo) {
      switch (tipo) {
        case 'caso':
          return this.notificaciones.filter(notificacion => notificacion.Caratula !== item.Caratula)

        case 'mediador':
          return this.notificacionesMediadores.filter(notificacion => notificacion.IdMediador !== item.IdMediador)

        case 'contacto':
          return this.notificacionesContactos.filter(notificacion => notificacion.IdContato !== item.IdContato)

        case 'interno':
          return this.notificacionesInterno.filter(notificacion => notificacion.IdCaso !== item.IdCaso)
      }
    },
    processTextareaFill (e) {
      if (e === void 0) {
        return
      }

      if (e.keyCode === 27) {
        if (this.textareaFillCancelled !== true) {
          this.textareaFillCancelled = true
        }
      } else if (e.keyCode === 9) {
        if (
          this.textareaFillCancelled !== true &&
          this.textareaShadowText.length > 0
        ) {
          // eslint-disable-next-line no-undef
          stopAndPrevent(e)
          this.screenshot_descripcion =
            (typeof this.screenshot_descripcion === 'string'
              ? this.screenshot_descripcion
              : '') + this.textareaShadowText
        }
      } else if (this.textareaFillCancelled === true) {
        this.textareaFillCancelled = false
      }
    }
  }
}
</script>

<style>
body {
  width: 100%;
  background: url(../statics/img/pattern.png) #ececec;
}
.list-menu .q-item {
  min-height: auto !important;
}
.toolbar-subheader {
  background-color: #303f9f;
  padding-bottom: 5px;
  font-size: 1.4rem;
  color: white;
  text-align: center;
  font-family: "Lobster", cursive;
}
.logo-menu:hover {
  transform: rotate(90deg);
  transition: all 0.5s;
}
.q-dialog__inner > div {
  background-color: white;
}
#screenshot_container {
  max-width: 600px;
  max-height: 240px;
  overflow: auto;
}
@media (max-width: 600px) {
  #screenshot_container {
    max-width: 250px;
    max-height: 200px;
    overflow: auto;
    border: 1px solid #ccc;
    border-radius: 10px;
  }
}
#screenshot_container canvas {
  border: 2px solid #ccc;
  border-radius: 10px;
}

 /* width */
::-webkit-scrollbar {
  width: 7px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
