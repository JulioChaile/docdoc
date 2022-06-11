<template>
  <q-card>
    <q-item style="background-color: black;">
      <span class="q-subheading" style="color:white;">Enviar archivos por mail</span>
    </q-item>
    <div style="padding:1rem;">
      <span>
        <p>
          Ingrese el email de la persona con la que desea compartir los archivos o busque en los contactos del estudio.
        </p>
      </span>
      <div class="row">
        <div class="col-10">
          <q-select
            filled
            v-model="contactoEnviar"
            use-input
            hide-selected
            fill-input
            input-debounce="0"
            label="Buscar Contactos"
            :options="contactos"
            @filter="filterFn"
            @input="(val) => { email = val.value }"
            style="margin-top: 10px; width: 100%"
            hint="Escriba 4 letras para comenzar la busqueda. Se mostraran los primeros 10 resultados."
          >
            <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                      No se encontraron elementos que coincidan con la busqueda
                  </q-item-section>
                </q-item>
            </template>
          </q-select>
        </div>
        <div class="col-2 self-center justify-center" style="display: flex">
          <q-btn color="primary self-center" style="margin-right: 7px" round size="l" @click="modalContacto = true; enviando = false">
            +
            <q-tooltip>
              Agregar Contacto
            </q-tooltip>
          </q-btn>
        </div>
      </div>
      <br>
      <br>
      <q-input
        class="input"
        type="email"
        v-model="email"
        :label="validarEmail() ? 'Email' : 'Ingrese un Email válido'"
      />
      <br>
      <br>
      <span>
        <p>
          Ingrese el asunto y el contenido del mail.
        </p>
      </span>
      <q-input
        v-model="asuntoMail"
        label="Asunto"
        style="width: 100%; margin-top: 15px"
        type="text"
      />
      <q-input
        v-model="contenidoMail"
        label="Contenido"
        style="width: 100%; margin-top: 15px; margin-bottom: 10px"
        type="textarea"
        :rows="4"
      />
      <span v-if="hayImagenes()" style="color: #ed1b24">* Se generara un PDF con todas las imagenes en un solo archivo.</span>
      <div style="float:right; margin-top: 15px; margin-bottom: 15px">
        <q-btn v-if="!enviando" color="teal" label="Enviar" @click="compartirArchivos()" />
        <q-spinner
          v-else
          color="primary"
          class="self-center"
          style="margin-right: 10px"
          size="2em"
        />
        <q-btn flat label="Cancelar" @click="$emit('cerrar')" />
      </div>
    </div>

    <!-- Modal: Alta / Editar contacto -->
    <q-dialog v-model="modalContacto">
      <ModalContacto
        @altaContacto="altaContacto"
        @cerrar="modalContacto = false"
      />
    </q-dialog>
  </q-card>
</template>

<script>
import request from '../../request'
import ModalContacto from '../../components/Contactos/ModalContacto'
import { QSpinner } from 'quasar'
export default {
  name: 'EnviarMail',
  components: {
    QSpinner,
    ModalContacto
  },
  props: [ 'Multimedia', 'ContenidoPDF', 'asunto' ],
  data () {
    return {
      contactoEnviar: '',
      asuntoMail: this.asunto ? this.asunto : '',
      contenidoMail: '',
      email: '',
      enviando: false,
      contactos: [],
      modalContacto: false
    }
  },
  methods: {
    filterFn (val, update, abort) {
      if (val.length >= 4) {
        request.Get(`/contactos/buscar-contacto`, {offset: 0, limit: 10, cadena: val}, r => {
          update(
            () => {
              this.contactos = r.map(c => {
                return {
                  label: c.Apellidos + ', ' + c.Nombres,
                  value: c.Email
                }
              })
            },
            ref => {
              if (val.length >= 4 && ref.options.length > 0) {
                ref.setOptionIndex(-1) // reset optionIndex in case there is something selected
                ref.moveOptionSelection(1, true) // focus the first selectable option and do not update the input-value
              }
            }
          )
        })
      } else {
        this.contactos = []
      }
    },
    validarEmail () {
      if (this.email === '') { return true }

      let re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      if (!re.test(String(this.email).toLowerCase())) {
        return false
      }
      return true
    },
    hayImagenes () {
      return this.Multimedia.filter(m => m.Tipo === 'I').length
    },
    altaContacto (c) {
      this.contactoEnviar = {
        label: c.Apellidos + ', ' + c.Nombres,
        value: c.Email
      }
      this.email = c.Email
    },
    compartirArchivos () {
      if (!this.email || !this.validarEmail()) {
        this.$q.notify('Debe ingresar un email válido')
      } else if (!this.asuntoMail || !this.contenidoMail) {
        this.$q.notify('Debe llenar todos los campos para continuar')
      } else {
        this.enviando = true
        this.contenidoMail = this.contenidoMail.replace(/\r?\n/g, '<br>')
        request.Post('/multimedia-caso/enviar-email', {
          Email: this.email,
          Asunto: this.asuntoMail,
          Contenido: this.contenidoMail,
          Multimedia: JSON.stringify(this.Multimedia),
          ContenidoPDF: this.ContenidoPDF
        }, r => {
          if (!r.Error) {
            this.$q.notify('Los archivos se han enviado con exito.')
          } else {
            this.$q.notify(r.Error)
          }
          this.enviando = false
          this.email = ''
          this.asuntoMail = ''
          this.contenidoMail = ''

          this.$emit('mailEnviado')
        })
      }
    }
  }
}
</script>
