<template>
<q-page class="flex flex-center">
  <q-card class="register_form">
    <div v-if="error" style="display: flex; flex-direction:column; align-items:center; ">
      <object
          type="image/svg+xml"
          data="statics/docdoc-home.svg"
          alt="Docdoc! Gestión"
          width="240"
          height="100"
          style="margin-left: auto; margin-right: auto;">
          DocDoc!
        </object>
        <div style="text-align: center;">
          <h1 style="font-size:3rem;">:(</h1>
          <h2 style="font-size:3rem;">Página no encontrada</h2>
        </div>
    </div>
    <div v-if="!registrar && !error">
      <q-card-section style="display:flex; flex-direction:column; margin: auto; text-align:center;">
        <div>
          <span style="font-size:1.3rem;"> Bienvenido a </span>
        </div>
      <div style="display:flex; justify-content:center;">
        <object
          type="image/svg+xml"
          data="statics/docdoc-home.svg"
          alt="Docdoc! Gestión"
          width="240"
          height="100"
          style="display:flex; justify-content:center; margin-bottom:1rem;">
          DocDoc!
        </object>
      </div>
      <div style="font-size:1.2rem;">
        Para registrarse en la plataforma, haga click en el siguiente botón
      </div>
      <div>
        <q-btn
          color="primary"
          style="margin:2rem;"
          @click="registrar=true"
        >
          REGISTRARSE
        </q-btn>
      </div>
    </q-card-section>
    </div>
    <div v-if="registrar && !error">
    <q-stepper
      v-model="step"
      vertical
      color="primary"
      animated
      contractable
      no-header-navigation
      transition-next="slide-down"
      style="width: 100vw, justify-content: center; background:none;"
    >
      <div style="display:flex; justify-content:center;">
        <object
          type="image/svg+xml"
          data="statics/docdoc-home.svg"
          alt="Docdoc! Gestión"
          width="240"
          height="100">
          DocDoc!
        </object>
      </div>
      <div style="text-align:center; font-size:1.5rem; color: #303f9f">
      Formulario de Registro
      </div>
      <q-step
        :name="1"
        title="Estudio"
        caption="Complete los datos de su estudio"
        icon="settings"
        :done="step > 1"
      >
        <div v-if="!datosCargados">
          <Loading />
        </div>
        <div v-else class="form__container">
          <q-input class="input" v-model="Estudio" label="Nombre del Estudio"/>
          <!--
          <q-input class="input" v-model="IdCiudad" label="Ciudad"/>
          -->
          <q-input class="input" v-model="Domicilio" label="Domicilio del Estudio"/>
          <q-input class="input" v-model="Telefono" label="Teléfono del Estudio"/>
          Escriba en que se especializa su estudio.
          <q-input class="input" v-model="Especialidades" label="Especialidades del Estudio"/>
        </div>
        <q-stepper-navigation>
          <q-btn @click="step = 2"
            color="primary"
            label="Siguiente"
            v-bind:disabled="!Estudio"
            style="margin-top:2rem;"/>
        </q-stepper-navigation>
      </q-step>
      <q-step
        :name="2"
        title="Usuario"
        icon="create_new_folder"
        :done="step > 2"
        caption="Complete los datos de su usuario de DocDoc!"
      >
      <div class="form__container">
        <q-input class="input" v-model="Usuario" label="Usuario"/>
        <q-input class="input" type="password" v-model="Password" label="Crea tu Contraseña"/>
        <q-input class="input" v-model="Nombres" label="Nombres"/>
        <q-input class="input" v-model="Apellidos" label="Apellidos"/>
        <q-input class="input" v-model="Email" label="Email"/>
        <q-input class="input" v-model="TelefonoUsuario" label="Telefono"/>
      </div>
      <q-stepper-navigation>
        <q-btn @click="step = 1"
          flat
          label="Anterior"
          v-bind:disabled="!Estudio"
        />
        <q-btn
          @click="registrarUsuario()"
          label="Finalizar"
          v-bind:disabled="!Usuario && !Password && !Nombres && !Apellidos && !Email"
          color="primary"
        />
      </q-stepper-navigation>
      </q-step>
    </q-stepper>
    </div>
  </q-card>
</q-page>
</template>

<script>
import request from '../request'
import Loading from '../components/Loading.vue'
import { Notify } from 'quasar'
import auth from '../auth'

export default {
  props: ['token'],
  components: {
    Loading
  },
  data () {
    return {
      Estudio: '',
      IdCiudad: 8870,
      Domicilio: '',
      Telefono: '',
      Especialidades: '',
      TelefonoUsuario: '',
      Nombres: '',
      Apellidos: '',
      Usuario: '',
      Password: '',
      Email: '',
      datosCargados: false,
      registrar: false,
      error: false,
      step: 0
    }
  },
  created () {
    request.Get(`/clientes?Token=${this.token}`, {}, r => {
      if (!r.Error) {
        this.Email = r.Email
        this.datosCargados = true
        setTimeout(() => {
          this.step = 1
        }, 600)
      } else {
        this.error = true
      }
    })
  },
  methods: {
    registrarUsuario () {
      let datos = {
        Estudio: this.Estudio,
        IdCiudad: this.IdCiudad,
        Domicilio: this.Domicilio,
        Telefono: this.Telefono,
        Especialidades: this.Especialidades,
        TelefonoUsuario: this.TelefonoUsuario,
        Nombres: this.Nombres,
        Apellidos: this.Apellidos,
        Usuario: this.Usuario,
        Password: this.Password,
        Email: this.Email,
        Token: this.token
      }
      if (this.Usuario && this.Password && this.Nombres && this.Apellidos && this.Email && this.TelefonoUsuario) {
        request.Post('/clientes', datos, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            auth.login(datos, (DebeCambiarPass) => {
              if (DebeCambiarPass === 'S') {
                this.oldPass = this.user.Password
                this.paso = 'CambiarPassword'
              } else if (DebeCambiarPass === 'N') {
                this.$router.push({path: '/Casos'})
              }
            })
          }
        })
      } else {
        Notify.create('Debe ingresar todos los datos requeridos')
      }
    }
  }
}
</script>

<style lang="stylus">
  .register_form{
    width: 900px;
    background: rgba(255, 255, 255, 0.7);
  }
</style>
