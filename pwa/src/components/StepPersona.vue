<template>
  <div style="width: 90vw;">
    <div class="form__container">
      <q-toggle
        style="margin-right: 15px; margin-top: 30px"
        v-model="persona.EsJuridica"
        label="Persona Jurídica"
        color="primary"
        true-value="S"
        false-value="N"
        @click="mostrarPersona()"
      />
      <q-toggle
        v-if="!persona.EsJuridica || persona.EsJuridica === 'N'"
        style="margin-top: 30px"
        v-model="mostrarDatosAdicionales"
        label="Mostrar Datos Adicionales"
        color="primary"
      />
    </div>
    <div class="form__container">
    <!-- q-select
        class="input"
        v-model="rolSeleccionado"
        label="Rol Tipo Caso"
        :options="roles"
        radio
        @blur="seleccionRol()"
      / -->
      <q-input class="input" type="number" v-model.number="persona.Documento" label="Documento" v-if="persona.EsJuridica === 'N'" :color="!persona.validate.Documento() ? '' : 'red'"/>
      <div v-if="true"> <!--div v-if="persona.Documento" esto se usaba para poner el dni y despues traer los datos de la persona -->
        <q-btn
          v-if="persona.EsJuridica === 'N'"
          style="margin-top: 30px"
          color="accent"
          @click="llenarPersona()"
        >
          Traer datos
        </q-btn>
        <div v-if="false"> <!-- div v-if="persona.Nombres" verifica que se hayan llenado los datos -->
          <Loading />
        </div>
        <div v-else>
          <q-input v-if="persona.EsJuridica === 'N'" class="input" type="text" v-model="persona.Nombres" label="Nombres" :color="!persona.validate.Nombres() ? '' : 'red'"/>
          <q-select
            v-else
            :value="model"
            use-input
            hide-selected
            fill-input
            @filter="filterFn"
            @input-value="setModel"
            @input="setPersona"
            label="Razón Social"
            :options="opcionesPersonas"
            :color="!persona.validate.Nombres() ? '' : 'red'"
          />
          <q-input class="input" type="text" v-model="persona.Apellidos" label="Apellidos/s" v-if="persona.EsJuridica === 'N'" :color="!persona.validate.Apellidos() ? '' : 'red'"/>
          <q-input class="input" type="email" v-model="persona.Email" label="Email" :color="!persona.validate.Email() ? '' : 'red'" />
          <q-input class="input" type="tel" v-model="persona.CUIT" label="CUIT" :color="!persona.validate.CUIT() ? '' : 'red'"/>
          <div style="display: flex; align-items:center;" v-for="t in Telefonos" :key="t.id" >
            <q-input class="input" type="tel" v-model="t.tel" label="Teléfono" :color="!persona.validate.Telefonos() ? '' : 'red'"/>
            <q-input class="input" type="text" v-model="t.Detalle" label="Detalle" />
            <q-checkbox
              v-if="Telefonos.length > 1 && rol === 'actor'"
              v-model="t.check"
              label="Principal"
              @input="telefonoPrincipal(t.id)"
              style="margin-left: 10px"
            />
            <q-btn color="primary" round style="max-heigth:1rem; margin-left:2rem;" @click="agregarTelefono" v-if="t.id === Telefonos.length - 1">
              +
              <q-tooltip>
                Agregar Teléfono
              </q-tooltip>
            </q-btn>
            <q-btn color="red" round style="max-heigth:1rem; margin-left:1rem;" @click="quitarTelefono" v-if="t.id === Telefonos.length - 1 && Telefonos.length > 1">
              -
              <q-tooltip>
                Quitar Teléfono
              </q-tooltip>
            </q-btn>
          </div>
          <q-input class="input" v-model="persona.Domicilio" label="Domicilio" :color="!persona.validate.Domicilio() ? '' : 'red'"/>
        </div>
      </div>
    </div>
    <div v-if="mostrarDatosAdicionales" class="form__container" style="max-width: 50vw">
      <q-expansion-item v-if="persona.EsJuridica === 'N'"
        label=" Más Datos Personales"
      >
      <q-input v-model="persona.FechaNacimiento" type="date" color="Fecha de Nacimiento"/>
      <q-input class="input" v-model="persona.Oficio" label="Oficio" />
      <p style="margin-top: 40px">Sexo
      <q-option-group
        type="radio"
        color="primary"
        v-model="persona.Sexo"
        :options="[
          { label: 'Masculino', value: 'Masculino' },
          { label: 'Femenino', value: 'Femenino' },
          { label: 'Otro', value: 'Otro' }
        ]"
      />
      </p>
      <p style="margin-top: 40px">Estado Civil
        <q-option-group
          type="radio"
          color="primary"
          v-model="persona.EstadoCivil"
          :options="[
            { label: 'Soltero/a', value: 'Soltero' },
            { label: 'Casado/a', value: 'Casado' },
            { label: 'Otro', value: 'Otro' }
          ]"
          />
      </p>
      </q-expansion-item>
      <MasDatos
        :categorias="categorias"
      />
      <!--
        :IdRTC="persona.IdRTC"
      /-->
    </div>
    <div style="display: flex; flex-flow: row-nowrap; align-items: center;">
      <q-btn
        flat
        style="margin-right: 15px; margin-top: 30px"
        color="primary"
        @click="$emit('volver')"
      >
        Cancelar
      </q-btn>
      <q-btn
        style="margin-top: 30px"
        color="accent"
        @click="control_persona"
      >
        Listo
      </q-btn>
    </div>
  </div>
</template>

<script>
import MasDatos from '../components/MasDatos.vue'
import models from '../models'
import request from '../request'
import auth from '../auth'
import Loading from './Loading.vue'
import { Notify } from 'quasar'

export default {
  props: ['personaeditar', 'editarPersona', 'idTipoCaso', 'IdCaso', 'index', 'rol'],
  components: {
    MasDatos,
    Loading,
    Notify
  },
  name: 'StepPersona',
  data () {
    return {
      persona: models.Personas(),
      RolTipoCaso: models.RolesTipoCaso(),
      mostrarDatosAdicionales: false,
      roles: [],
      rolSeleccionado: {},
      categorias: {},
      parametros: [],
      Telefonos: [
        {
          id: 0,
          tel: '',
          Detalle: '',
          EsPrincipal: this.rol === 'actor' ? 'S' : 'N',
          check: true
        }
      ],
      opcionesPersonas: [],
      model: ''
    }
  },
  created () {
    request.Get(`/tipos-caso/${this.idTipoCaso}/roles`, {}, r => {
      if (!r.Error) {
        this.roles = r.map(rol => ({ label: rol.Rol, value: rol.IdRTC }))
        if (this.editarPersona) {
          this.persona = this.personaeditar
          this.persona.Tipo === 'J' ? this.persona.EsJuridica = 'S' : this.persona.EsJuridica = 'N'
          // this.rolSeleccionado.value = this.persona.IdRTC
          this.persona.Parametros.forEach(p => {
            if (!this.categorias[p.Categoria]) {
              this.categorias[p.Categoria] = []
            }
            this.categorias[p.Categoria].push(p)
          })
        } else {
          this.persona.EsJuridica = 'N'
          this.rolSeleccionado.value = 0
        }
      }
    })
  },
  methods: {
    llenarPersona () {
      let documento = this.persona.Documento
      if (documento) {
        if (documento.toString().length === 8) {
          request.Get('/personas/padron', {documento: this.persona.Documento}, r => {
            if (r.Error) {
              Notify.create(r.Error)
            } else {
              this.persona.Nombres = r.Nombres
              this.persona.Apellidos = r.Apellidos
              this.persona.Domicilio = r.Domicilio
            }
          })
        } else {
          this.$q.notify({
            color: 'primary',
            timeout: 800,
            message: 'El documento ingresado no es valido'
          })
          this.persona.Nombres = ''
        }
      }
    },
    agregarTelefono () {
      let numero = this.Telefonos[this.Telefonos.length - 1].id + 1
      this.Telefonos.push({
        id: numero,
        tel: '',
        Detalle: '',
        EsPrincipal: 'N',
        check: false
      })
    },
    quitarTelefono () {
      this.Telefonos.splice(-1, 1)
    },
    control_persona () {
      if (this.persona.validate.scenario.Alta()) {
        this.$q.notify({
          color: 'primary',
          timeout: 800,
          message: 'Verifique los datos ingresados'})
        return
      }
      if (this.persona.EsJuridica === 'N') {
        !this.persona.Nombres ? this.$q.notify({
          color: 'primary',
          timeout: 800,
          message: 'Debe ingresar el nombre para poder continuar.'
        }) : this.agregar_persona()
      } else {
        if (!this.persona.Nombres) {
          this.$q.notify({
            color: 'primary',
            timeout: 800,
            message: 'Debe ingresar la Razón Social para poder continuar.'
          })
        } else {
          this.agregar_persona()
        }
      }
    },
    mostrarPersona () {
      let telefonos = []
      this.Telefonos.map(t => {
        telefonos.push(t.tel)
      })
      this.persona.Telefonos = telefonos
    },
    seleccionRol () {
      request.Get('/roles-tipo-caso/', { id: this.rolSeleccionado.value }, r => {
        let result = { Error: null }
        if (r.Error) {
          result.Error = r.Error
        } else {
          this.persona.IdRTC = this.rolSeleccionado.value
          this.RolTipoCaso = r
          this.RolTipoCaso.Parametros = JSON.parse(this.RolTipoCaso.Parametros)
          this.RolTipoCaso.Parametros.forEach(p => {
            p.Valor = ''
          })
          this.RolTipoCaso.Parametros.forEach(p => {
            if (!this.categorias[p.Categoria]) {
              this.categorias[p.Categoria] = []
            }
            this.categorias[p.Categoria].push(p)
          })
        }
      })
    },
    agregar_persona () {
      let telefonos = []
      this.Telefonos.map(t => {
        telefonos.push({
          Telefono: t.tel,
          Detalle: t.Detalle,
          EsPrincipal: t.EsPrincipal
        })
      })
      this.persona.Telefonos = telefonos
      if (this.editarPersona === 'tarjeta') {
        request.Get(`/casos/${this.IdCaso}`, {}, r => {
          if (!r.Error) {
            let personaEditada = r.PersonasCaso[this.index]
            personaEditada.Nombres = this.persona.Nombres
            personaEditada.Apellidos = this.persona.Apellidos
            personaEditada.Domicilio = this.persona.Domicilio
            personaEditada.Email = this.persona.Email
            // personaEditada.IdRTC = this.persona.IdRTC
            personaEditada.Documento = this.persona.Documento
            personaEditada.CUIT = this.persona.CUIT
            personaEditada.Observaciones = this.persona.Observaciones
            personaEditada.Telefonos = this.persona.Telefonos
            personaEditada.Parametros = this.persona.Parametros
          }
        })
        this.editarPersona = false
      } else {
        for (var categoria in this.categorias) {
          this.categorias[categoria].forEach(p => {
            this.parametros.push(p)
          })
        }
        this.persona.Parametros = this.parametros
        this.$emit('agregar-persona', this.persona)
      }
    },
    telefonoPrincipal (id) {
      this.Telefonos.forEach(t => {
        if (t.id === id) {
          t.check = true
          t.EsPrincipal = 'S'
        } else {
          t.check = false
          t.EsPrincipal = 'N'
        }
      })
    },
    filterFn (val, update, abort) {
      val = this.accentFold(val)

      if (val.length < 3) {
        abort()
        return
      }

      request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/buscar-personas`, {Cadena: val, Tipo: 'J'}, r => {
        if (!r.Error) {
          update(() => {
            this.opcionesPersonas.splice(0)

            r.forEach(p => {
              this.opcionesPersonas.push({
                label: p.Nombres,
                value: p
              })
            })
          })
        }
      })
    },
    accentFold (s) {
      const accent = {'á': 'a', 'é': 'e', 'è': 'e', 'í': 'i', 'ó': 'o', 'ú': 'u', 'Á': 'a', 'É': 'e', 'Í': 'i', 'Ó': 'o', 'Ú': 'u'}

      if (!s) { return '' }
      var ret = ''
      for (var i = 0; i < s.length; i++) {
        ret += accent[s.charAt(i)] || s.charAt(i)
      }

      return ret
    },
    setModel (val) {
      this.model = val
      this.persona.Nombres = val
    },
    setPersona (val) {
      const p = val.value

      this.persona.Nombres = p.Nombres
      this.persona.Email = p.Email
      this.persona.CUIT = p.Cuit
      this.persona.Telefonos = p.Telefonos
      this.persona.Domicilio = p.Domicilio
    }
  }
}
</script>

<style>
.form__container {
  margin-bottom: 15px;
}

.form__container > .input {
  max-width: 50vw;
}
</style>
