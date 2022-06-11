<template>
    <q-card style="min-width:400px;">
      <q-item style="background-color: black;">
        <span class="q-subheading" style="color:white;">{{ editar ? 'Editar' : 'Nuevo' }} {{ mediador ? 'Contacto' : 'Mediador'}}</span>
      </q-item>
      <div style="margin: 15px 15px 15px 15px">
        <q-input v-if="!mediador" label="Apellidos" v-model="datos.Apellidos" />
        <q-input :label="mediador ? 'Apellido y Nombre' : 'Nombres'" v-model="datos.Nombres" />
        <q-input v-if="mediador" label="Registro" v-model="datos.Registro" />
        <q-input v-if="mediador" label="MP" v-model="datos.MP" />
        <q-input label="Telefono" type="tel" v-model="datos.Telefono" />
        <q-input label="Email" type='email' v-model="datos.Email" />
        <q-select
          v-if="!mediador"
          v-model="Tipo"
          label="Tipo"
          :options="opcionesTipo"
        />
        <div style="padding-top:30px; text-align:right">
          <q-btn color="primary" @click="editar ? editarContacto() : altaContacto()">Guardar</q-btn>
          <q-btn flat @click="$emit('cerrar')">Cancelar</q-btn>
        </div>
      </div>
    </q-card>
</template>

<script>
import request from '../../request'
import { Notify } from 'quasar'
export default {
  props: ['editar', 'datosEditar', 'mediador'],
  name: 'ModalContacto',
  data () {
    return {
      datos: {
        Apellidos: '',
        Nombres: '',
        Telefono: '',
        Email: '',
        Registro: '',
        MP: '',
        Tipo: 'A'
      },
      opcionesTipo: [
        {
          label: 'Abogado',
          value: 'A'
        },
        {
          label: 'Medico',
          value: 'M'
        },
        {
          label: 'Proveedor',
          value: 'P'
        },
        {
          label: 'Otros',
          value: 'O'
        }
      ],
      Tipo: {
        label: 'Abogado',
        value: 'A'
      }
    }
  },
  created () {
    if (this.editar) {
      const d = this.datosEditar

      if (this.mediador) {
        this.datos = {
          IdContacto: d.IdContacto,
          Nombres: d.Contacto,
          Telefono: d.Telefono,
          Email: d.Email,
          Registro: d.Registro,
          MP: d.MP
        }
      } else {
        const i = d.Contacto.indexOf(',')
        this.datos = {
          IdContacto: d.IdContacto,
          Apellidos: d.Contacto.slice(0, i),
          Nombres: d.Contacto.slice(i + 2),
          Telefono: d.Telefono,
          Email: d.Email,
          Tipo: d.Tipo
        }
      }

      this.Tipo = this.opcionesTipo.filter(o => o.value === d.Tipo)[0]
    }
  },
  methods: {
    altaContacto () {
      if (this.mediador) {
        if (!this.datos.Nombres) {
          Notify.create('Se debe ingresar un nombre.')
          return
        }
        if (this.datos.Telefono && !this.validarTelefono(this.datos.Telefono)) {
          Notify.create('Ingrese el telefono correctamente.')
          return
        }
        request.Post('/mediadores/alta-contacto', this.datos, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.datos.IdContacto = r.IdMediador
            this.$emit('altaContacto', this.datos)
          }
        })
      } else {
        this.datos.Tipo = this.Tipo.value
        if (!this.datos.Apellidos || !this.datos.Nombres) {
          Notify.create('Se debe ingresar nombre y apellido.')
        } else {
          request.Post('/contactos/alta-contacto', this.datos, r => {
            if (r.Error) {
              Notify.create(r.Error)
            } else {
              this.datos.IdContacto = r.IdContacto
              this.$emit('altaContacto', this.datos)
            }
          })
        }
      }
    },
    editarContacto () {
      if (this.mediador) {
        if (!this.datos.Nombres) {
          Notify.create('Se debe ingresar un nombre.')
          return
        }
        if (this.datos.Telefono && !this.validarTelefono(this.datos.Telefono)) {
          Notify.create('Ingrese el telefono correctamente.')
          return
        }
        this.datos.id = this.datos.IdContacto
        request.Post('/mediadores/editar-contacto', this.datos, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.$emit('editarContacto', this.datos)
          }
        })
      } else {
        this.datos.Tipo = this.Tipo.value
        if (!this.datos.Apellidos || !this.datos.Nombres) {
          Notify.create('Se debe ingresar nombre y apellido.')
        } else {
          request.Post('/contactos/editar-contacto', this.datos, r => {
            if (r.Error) {
              Notify.create(r.Error)
            } else {
              this.$emit('editarContacto', this.datos)
            }
          })
        }
      }
    },
    validarTelefono (telefono) {
      telefono = telefono.replace(/[- +]/g, '')

      switch (true) {
        case telefono.startsWith('54') && telefono.length === 12:
          return true

        case !telefono.startsWith('54') && telefono.length === 10:
          return true

        default:
          return false
      }
    }
  }
}
</script>
