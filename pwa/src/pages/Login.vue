<template>
  <q-page>
    <!--Puede parecer al pedo este div, sin embargo es necesario para dar el efecto de degradado-->
    <!--en caso de que esta pagina requiera otro fondo distinto al que hay, ademas asi el modal -->
    <!--no agarra el efecto y solo lo tiene el contenido que se ve-->
    <div class="row nuevo-fondo avenir-next--medium login-container full-width full-height">
      <div
        v-if="paso === 'VerificarUsuario'"
        class="col-sm-6 col-xs-12 container-form column items-center justify-center"
      >
        <InicioSesion
          :titulo="'Iniciar sesión'"
          :continuar="'Ingresar'"
          :error="user.validate.Usuario"
          :loading="loading"
          :execute="verificarUsuario"
          :recuperar="habilitarRecuperarCuenta"
          :helper="true ? '' : 'Ingrese su nombre de usuario para continuar'"
          :subtitle="'Accede con tu cuenta Docdoc! Gestión'"
          :label="'Usuario'"
        >
          <q-input
            dense
            borderless
            style="width: 100%; margin: 0; padding: 0;"
            label-color="dark"
            class="--medium"
            :loading="loading"
            :disabled="loading"
            label="Usuario"
            v-model="user.Usuario"
            @keyup.enter="verificarUsuario"
            autofocus
          >
            <template v-slot:prepend>
              <q-icon name="person" style="margin-left: 10px; color: #1E4B74" />
            </template>
          </q-input>
        </InicioSesion>
      </div>
      <div
        v-if="paso === 'VerificarCodigo'"
        class="col-sm-6 col-xs-12 container-form column items-center justify-center"
      >
        <InicioSesion
          :titulo="'Verificar código'"
          :continuar="'Ingresar'"
          :error="user.validate.Usuario"
          :loading="loading"
          :inicio="inicio"
          :subtitle="'Ingresa el código que enviamos a tu casilla de correo'"
          :codigo="'Ingresa el código que enviamos a tu casilla de correo'"
        >
          <masked-input
            @input="verificarCodigo"
            :loading="loading"
            :disabled="loading"
            type="tel"
            name="codigo"
            class="q-input-target verificacion-codigo"
            v-model="codigoVerificacion"
            :mask="[/\d/, /\d/, /\d/, /\d/, /\d/]"
            :showMask="true"
            placeholderChar="_">
          </masked-input>
        </InicioSesion>
      </div>
      <div
        v-if="paso === 'IngresarPassword'"
        class="col-sm-6 col-xs-12 container-form column items-center justify-center"
      >
        <InicioSesion
          :titulo="'Contraseña'"
          :subtitle="'Ingrese su contraseña para continuar'"
          :continuar="'Ingresar'"
          :error="user.validate.Password"
          :loading="loading"
          :execute="login"
          :inicio="inicio"
        >
          <q-input
            @keyup.enter="login"
            :loading="loading"
            :disabled="loading"
            style="display:none"
            type="text"
            v-model="user.Usuario"
            ref="userInput"
          />
          <q-input
            dense
            borderless
            style="width: 100%; margin: 0; padding: 0;"
            label-color="dark"
            @keyup.enter="login"
            :loading="loading"
            :disabled="loading"
            label="Contraseña"
            v-model="user.Password"
            type="password"
            ref="passInput"
          >
            <template v-slot:prepend>
              <q-icon name="lock" style="margin-left: 10px; color: #1E4B74" />
            </template>
          </q-input>
        </InicioSesion>
      </div>
      <div
        v-if="paso === 'CambiarPassword'"
        class="col-sm-6 col-xs-12 container-form column items-center justify-center"
      >
        <InicioSesion
          :titulo="'Nueva contraseña'"
          :continuar="'Cambiar'"
          :error="errorPassword"
          :loading="loading"
          :execute="cambiarPass"
          :inicio="inicio"
        >
          <q-input
            dense
            style="width: 100%; margin: 0; padding: 0;"
            label-color="dark"
            color="grey-3"
            class="col-12"
            :loading="loading"
            :disabled="loading"
            label="Ingrese su nueva contraseña"
            v-model="user.Password"
            type="password"
          >
            <template v-slot:prepend>
              <q-icon name="lock" style="margin-left: 10px; color: #1E4B74" />
            </template>
          </q-input>
          <q-input
            dense
            style="width: 100%; margin: 0; padding: 0;"
            label-color="dark"
            color="grey-3"
            class="col-12"
            @keyup.enter="cambiarPass"
            :loading="loading"
            :disabled="loading"
            label="Repetir contraseña"
            v-model="nuevoPass"
            type="password"
          >
            <template v-slot:prepend>
              <q-icon name="lock" style="margin-left: 10px; color: #1E4B74" />
            </template>
          </q-input>
        </InicioSesion>
      </div>

      <div class="col-sm-6 col-xs-12 window-height row items-center justify-center img-login">
        <div class="container-img">
          <img src="statics/img/login-img.png" height="auto" width="100%" />
        </div>
      </div>
    </div>

    <!-- Modal Recuperar Cuenta -->
    <q-dialog v-model='recuperarModal' style="background-color: white">
      <q-card style="min-width:400px;">
        <q-item style="background-color: black;">
            <span class="q-subheading" style="color:white;">Recuperar Cuenta</span>
        </q-item>
        <div style="margin-top: 20px; margin-left: 20px; margin-right: 20px; margin-bottom: 20px; display: flex; justify-content: center;">
          Ingrese el email con el que fue registrado y su nueva contraseña.
          <br>
          Se le enviara un mail con su nombre de usuario y su nueva contraseña.
        </div>
        <q-input
          v-model="email"
          autogrow
          label="Email"
          style="padding: 0 1rem 1rem 1rem;"
        />
        <q-input
          v-model="pass"
          label="Contraseña"
          style="padding: 0 1rem 1rem 1rem;"
          type="password"
        />
        <br>
        <q-btn
          @click="cancelar()"
          color="red"
          style="padding-top:0px; float: right; margin-bottom:20px; margin-right: 20px;"
        >Cancelar</q-btn>
        <q-btn
          @click="recuperarCuenta()"
          color="primary"
          style="padding-top:0px; float: right; margin-bottom:20px; margin-right: 20px;"
        >Enviar</q-btn>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<style>
</style>

<script>
import models from '../models'
import InicioSesion from '../components/InicioSesion.vue'
import auth from '../auth'
import MaskedInput from 'vue-text-mask'
import request from '../request'

export default {
  name: 'Login',
  components: {
    InicioSesion,
    MaskedInput
  },
  data () {
    return {
      paso: 'VerificarUsuario',
      user: models.Usuarios(),
      codigoVerificacion: '',
      nuevoPass: '',
      oldPass: '',
      loading: false,
      lock: [true],
      recuperarModal: false,
      email: '',
      pass: ''
    }
  },
  created () {
    this.inicio()
  },
  methods: {
    habilitarRecuperarCuenta () {
      this.recuperarModal = true
    },
    inicio () {
      this.paso = 'VerificarUsuario'
      this.user.Usuario = ''
      this.user.Password = ''
      this.codigoVerificacion = ''
      this.nuevoPass = ''
      this.oldPass = ''
      this.lock = [true]
      auth.logout()
    },
    errorPassword () {
      let v1 = this.user.validate.Password()
      if (v1) {
        return v1
      }
      return ((this.nuevoPass.length >= this.user.Password.length && this.nuevoPass !== this.user.Password) && 'Las contraseñas no coinciden') || ''
    },
    redirect () {
      let path = this.$route.params.redirect
      if (!path || path.includes('Login')) {
        path = '/'
      }
      if (typeof auth.UsuarioLogueado.IdRol !== 'undefined') {
        console.log('cadete')
        path = '/Maps'
      }
      this.$router.push({ path: path })
    },
    login () {
      this.loading = true
      auth.login(this.user, (DebeCambiarPass) => {
        this.loading = false
        if (DebeCambiarPass === 'S') {
          this.oldPass = this.user.Password
          this.paso = 'CambiarPassword'
        } else if (DebeCambiarPass === 'N') {
          this.redirect()
        }
      })
    },
    cambiarPass () {
      if (this.user.Password !== this.nuevoPass) {
        return
      }
      this.loading = true
      auth.cambiarPass(this.oldPass, this.nuevoPass, (redirect) => {
        this.loading = false
        if (!auth.isLoggedIn) {
          this.paso = 'VerificarUsuario'
        }
        if (redirect) {
          this.redirect()
        }
      })
    },
    verificarUsuario () {
      this.loading = true
      auth.verificarUsuario(this.user.Usuario, (DebeCambiarPass) => {
        this.loading = false
        if (DebeCambiarPass === 'S') {
          this.paso = 'VerificarCodigo'
        } else if (DebeCambiarPass === 'N') {
          this.paso = 'IngresarPassword'
          this.$nextTick(() => {
            this.$refs.passInput.focus()
          })
        }
      })
    },
    esNumerico () {
      const numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']
      for (let i = 0; i < this.codigoVerificacion.length; i++) {
        if (!numbers.includes(this.codigoVerificacion[i])) {
          return false
        }
      }
      return true
    },
    verificarCodigo () {
      if (!this.esNumerico() || !this.lock.pop()) {
        return
      }
      this.loading = true
      auth.verificarCodigo(this.user.Usuario, this.codigoVerificacion, ok => {
        this.lock.push(true)
        this.loading = false
        if (ok) {
          this.oldPass = this.codigoVerificacion
          this.paso = 'CambiarPassword'
        }
        this.codigoVerificacion = ''
      })
    },
    cancelar () {
      this.email = ''
      this.pass = ''
      this.recuperarModal = false
    },
    recuperarCuenta () {
      if (!this.email || !this.pass) {
        this.$q.notify('Rellene ambos campos para continuar')
      } else {
        request.Post('/usuarios/recuperar-cuenta', {Email: this.email, Pass: this.pass}, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            this.email = ''
            this.pass = ''
            this.recuperarModal = false

            this.$q.notify('Se ha cambiado la contraseña exitosamente, ya puedes loguearte con tu cuenta.')
          }
        })
      }
    }
  }
}
</script>

<style>
.container-form {
  padding-left: 10%;
}

.container-img {
  width: 50%;
  min-width: 300px;
}

.verificacion-codigo {
  letter-spacing: 8px;
  font-size: 20px;
  text-align: center;
}
.volver-inicio:hover {
  cursor: pointer;
}

.login-container {
  min-height: inherit;
}

@media screen and (max-width: 599px) {
  .img-login {
    align-content: flex-start;
    height: auto !important;
  }
}

@media screen and (max-width: 1023px) {
  .container-form {
    padding-left: 0%;
  }
}
</style>
