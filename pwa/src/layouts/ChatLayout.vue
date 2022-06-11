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
          <img src="../statics/img/logo.png" width="30" height="25" />
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
        <q-btn-dropdown flat :label="mostrarUsuario()">
          <q-list
            bordered
            padding
            class="rounded-borders"
            style="background-color:#f5f6f7;padding-top:0;max-width: 350px"
          >
            <q-item-label
              header
              style="background-color:white;padding:0 0 0 16px; min-height:auto;font-size:12px;font-weight:bold;"
            >Notificaciones</q-item-label>
            <q-item
              v-if="Notificaciones.length === 0"
              style="color:grey; font-style:italic;font-size:14px"
            >No posee notificaciones recientes</q-item>
            <q-item v-for="notificacion in Notificaciones" :key="notificacion.IdNotificacion">
              <q-item-side icon="notifications" color="primary" />
              <q-item-label>
                <q-item-label header>Photos</q-item-label>
                <q-item-label caption>February 22, 2016</q-item-label>
              </q-item-label>
            </q-item>
            <q-item
              sparse
              style="display:flex; align-items:center; bottom: 0; width: 100%"
              to="/Login"
            >
              <q-item-section side top>
                <q-icon name="exit_to_app" />
              </q-item-section>
              <q-item-section>Cerrar Sesión</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
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
      <q-list no-border link inset-delimiter highlight>
        <q-item-label header>Accesos Directos</q-item-label>
        <q-item to="/#" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="home" />
          </q-item-section>
          <q-item-section>Home</q-item-section>
        </q-item>
        <q-item to="/Vencimientos" style="display:flex; align-items:center;">
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
        <q-item to="/Movimientos/0" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="assignment" />
          </q-item-section>
          <q-item-section>Movimientos</q-item-section>
        </q-item>
        <q-item to="/Compartidos" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="share" />
          </q-item-section>
          <q-item-section>Compartidos</q-item-section>
        </q-item>
        <q-item to="/Utilidades" style="display:flex; align-items:center;">
          <q-item-section side top>
            <q-icon name="build" />
          </q-item-section>
          <q-item-section>Utilidades</q-item-section>
        </q-item>
        <!-- Create a new .css file with these styles? -->
        <q-item
          sparse
          style="display:flex; align-items:center;position: absolute; bottom: 0; width: 100%"
          to="/Login"
        >
          <q-item-section side top>
            <q-icon name="exit_to_app" />
          </q-item-section>
          <q-item-section>Cerrar Sesión</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>

    <!--
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
    -->
  </q-layout>
</template>

<script>
import { openURL, Notify } from 'quasar'
import auth from '../auth'
import request from '../request'
import html2canvas from 'html2canvas'
import LoadingInButton from '../components/LoadingInButton'

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
      loading: false
    }
  },
  components: {
    LoadingInButton
  },
  computed: {
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
    this.isLoggedIn = auth.isLoggedIn
    auth.addCallbackLogin(() => {
      this.isLoggedIn = auth.isLoggedIn
    })
  },
  methods: {
    openURL,
    altaMovimiento () {
      let idCaso = 0
      if (this.$route.params && this.$route.params.idCaso) {
        idCaso = this.$route.params.idCaso
      }
      this.$router.push(`/AltaMovimiento/${idCaso}`)
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
</style>
