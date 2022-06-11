<template>
    <q-card>
        <q-item
        style="background-color: black; color: white; display:flex; align-items:center;"
        >
          <q-icon color="white" name="share" size="sm" style="margin-right:1rem;" />
          <span class="q-subheading">Compartir Caso</span>
        </q-item>
        <q-separator />
        <div v-if="!enviando">
          <div style="margin-top: 10px; display: flex; justify-content: center">
            <q-radio v-model="check" val="estudio" label="Por Estudio" />
            <q-radio v-model="check" val="email" label="Por Email o Telefono" />
          </div>
          <div style="padding: 2rem 2rem 0;" v-if="check === 'estudio'">
            Seleccione los Estudios con los que desea compartir el caso:
            <Select
              :multiple="true"
              :label="'Buscar Estudios'"
              :hint="'Seleccione uno o más estudios.'"
              :tooltip="false"
              :opciones="opcionesEstudios"
              @seleccion="estudiosSeleccionados"
            />
            <div v-if="IdsEstudios.length > 0" style="margin-top: 10px; width: 100%">
              Estudios con los que se compartira el caso:
              <br>
              <b>{{ lista }}</b>
            </div>
          </div>
          <div style="padding: 2rem 2rem 0;" v-if="check === 'email'">
            Ingrese el Email y/o el Telefono de la persona con la que desea compartir el caso:
            <q-input
                @keyup.enter="verificar()"
                class="input"
                type="email"
                v-model="emailCompartir"
                :label="validarEmail() ? 'Email' : 'Ingrese un Email válido'"
            />
            <q-input
                @keyup.enter="verificar()"
                class="input"
                type="tel"
                v-model="telCompartir"
                label="Telefono"
            />
          </div>
          <div style="display: flex;justify-content: flex-end; margin-botton:2rem;">
            <q-btn
                style="margin-top: 30px; margin-bottom:2rem;"
                color="accent"
                @click="check === 'email' ? verificar() : compartirPorEstudio()"
            >Compartir</q-btn>
            <q-btn
                flat
                style="margin-right: 15px; margin-top: 30px; margin-bottom:2rem;"
                color="black"
                @click="$emit('compartir')"
            >Cancelar</q-btn>
          </div>
        </div>
        <div v-else style="display: flex; justify-content: center; text-align: center; margin-left: 15px; margin-right: 15px; padding: 35px;">
            Espere unos instantes, se estan cargando los datos.
            <br>
            Esto puede tardar varios segundos.
        </div>
    </q-card>
</template>

<script>
import request from '../../request'
import Select from '../../components/Compartidos/Select'
import { QRadio } from 'quasar'
import auth from '../../auth'
export default {
  name: 'CompartirCaso',
  components: {
    QRadio,
    Select
  },
  data () {
    return {
      emailCompartir: '',
      telCompartir: '',
      enviando: false,
      Estudios: [],
      check: 'estudio',
      opcionesEstudios: [],
      IdsEstudios: [],
      lista: ''
    }
  },
  props: [ 'caso' ],
  created () {
    const IdEstudio = auth.UsuarioLogueado.IdEstudio
    request.Get('/estudios/listar', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.Estudios = r
        const i = this.Estudios.findIndex(e => {
          return e.IdEstudio === IdEstudio
        })
        this.Estudios.splice(i, 1)

        this.opcionesEstudios = this.Estudios.map(e => {
          return {
            label: e.Estudio + ', ' + e.Domicilio + ', ' + e.Ciudad + ', ' + e.Provincia,
            value: e.IdEstudio
          }
        })
      }
    })
  },
  methods: {
    estudiosSeleccionados (estudios) {
      this.IdsEstudios.splice(0)
      estudios.forEach(e => {
        this.IdsEstudios.push(e.value)
      })
      this.lista = this.listarEstudios()
    },
    listarEstudios () {
      let lista = []
      this.Estudios.forEach(e => {
        if (this.IdsEstudios.indexOf(e.IdEstudio) > -1) {
          lista.push(e.Estudio)
        }
      })

      let lista2 = ''
      lista.forEach(l => {
        lista2 = lista2 + l + ', '
      })
      return lista2.slice(0, -2) + '.'
    },
    validarEmail () {
      if (this.emailCompartir === '') { return true }

      let re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      if (!re.test(String(this.emailCompartir).toLowerCase())) {
        return false
      }
      return true
    },
    verificar () {
      if (!this.emailCompartir && !this.telCompartir) {
        this.$q.notify('Debe ingresar un telefono o email válido')
      } else {
        this.emailCompartir ? this.verificarMail() : this.compartirCaso()
      }
    },
    verificarMail () {
      if (this.validarEmail()) {
        this.compartirCaso()
      } else {
        this.$q.notify('Debe ingresar un email válido')
      }
    },
    compartirCaso () {
      let datosCompartir = {
        Email: this.emailCompartir,
        Telefono: this.telCompartir,
        IdCaso: this.caso.IdCaso
      }
      this.enviando = true
      request.Post('/casos/compartir', datosCompartir, (r) => {
        if (r.Error) {
          this.enviando = false
          this.$q.notify(r.Error)
        } else {
          this.$emit('compartir')
          this.$q.notify({
            color: 'green',
            message: `Se compartió el caso '${this.caso.Caratula}' correctamente`
          })
        }
      })
    },
    compartirPorEstudio () {
      if (this.IdsEstudios.length === 0) {
        this.$q.notify({
          color: 'red',
          message: `Debe seleccionar por lo menos un estudio`
        })
      } else {
        request.Post(`/casos/compartir-por-estudio/${this.caso.IdCaso}`, { IdsEstudios: JSON.stringify(this.IdsEstudios) }, r => {
          if (r.length !== 0) {
            let lista = []
            this.Estudios.forEach(e => {
              if (r[e.IdEstudio]) {
                lista.push(e.Estudio)
              }
            })

            let lista2 = ''
            lista.forEach(l => {
              lista2 = lista2 + l + ', '
            })

            this.$q.notify({
              color: 'red',
              message: `Ha ocurrido un error al intentar compartir el caso con los siguientes estudios: ` + lista2.slice(0, -2) + '.'
            })
            this.Estudios.forEach(e => {
              if (r[e.IdEstudio]) {
                this.$q.notify({
                  color: 'red',
                  message: 'Orurrio un error con el estudio ' + e.Estudio + ' al ' + r[e.IdEstudio] + '.'
                })
              }
            })
          } else {
            this.$emit('compartir')
            this.$q.notify({
              color: 'green',
              message: `Se compartió el caso '${this.caso.Caratula}' correctamente`
            })
          }
        })
      }
    }
  }
}
</script>
