<template>
    <q-card style="min-width:400px;">
        <q-item style="background-color: black;">
            <span class="q-subheading" style="color:white;">Mensaje Global</span>
        </q-item>
        <div v-if="!Enviando">
          <div class="column" style="display: flex; justify-content: center; text-align: center; margin-left: 15px; margin-right: 15px; margin-top: 15px">
              {{
                checkMensajesDefault
                  ? 'Escriba un mensaje o seleccione uno de los mensajes de la lista para enviar a las personas de los casos seleccionados (se recomienda no seleccionar mas de 20 casos).'
                  : 'Escriba el mensaje que desee enviar a las personas de los casos seleccionados (se recomienda no seleccionar mas de 20 casos).'
              }}
              <Select
                :multiple="false"
                :label="'Buscar Mensaje'"
                :hint="'Seleccione un Mensaje'"
                :tooltip="true"
                :opciones="opcionesMensajes"
                @seleccion="mensajeSeleccionado"
              />
              <q-input
                v-model="mensaje"
                label="Mensaje"
                style="width: 100%; margin-top: 15px"
                type="textarea"
                :rows="4"
              />
              <q-checkbox
                  v-model="enviarArchivo"
                  label="Enviar Archivo"
                  style="margin-top: 10px"
              />
              <q-uploader
                v-if="enviarArchivo"
                label="Multimedia"
                auto-upload
                max-files="1"
                :factory="factoryFn"
                @uploaded="uploadedFile"
                style="width: 100%; margin-top: 10px"
              />
          </div>
          <div style="display: flex;justify-content: center; margin-botton:2rem;">
              <q-btn
                  @click="enviarMensaje()"
                  color="positive"
                  style="padding-top:0px; float: center; margin-bottom:20px; margin-top: 20px;"
              >Enviar</q-btn>
              <q-btn
                  @click="$emit('cerrar')"
                  color="primary"
                  style="padding-top:0px; float: center; margin-bottom:20px; margin-top: 20px; margin-left: 30px"
              >Cerrar</q-btn>
          </div>
          <div style=" margin-botton:2rem; text-align: left; margin-left: 15px">
              El mensaje se enviara a la persona principal de los siguientes casos:
              <ul>
                <li
                  v-for="caso in Casos"
                  :key="caso.IdCaso"
                >
                  {{ caso.Caratula }}
                </li>
              </ul>
          </div>
        </div>
        <div v-else style="display: flex; justify-content: center; text-align: center; margin-left: 15px; margin-right: 15px; padding: 35px;">
            Espere unos instantes, se estan enviando los mensajes.
            <br>
            Esto puede tardar varios segundos.
        </div>

        <q-dialog v-model="template">
          <q-card class="q-pa-sm text-center">
            <span clasS="text-h5 text-weight-bold">
              Vista Previa
            </span>

            <div class="full-width text-center q-px-sm q-py-lg" id="caja-template">
            </div>

            <div class="q-py-sm" style="width: 50%">
              <q-input dense class="q-my-sm" v-for="(p, i) in paramsTemplate" :key="p.key"  v-model="p.param" @input="reemplazarParam(p.param, p.key, i)">
                <template v-slot:prepend>
                  {{p.key}}
                </template>
              </q-input>
            </div>

            <div class="full-width row justify-center">
              <q-btn
                color="primary"
                class="q-subheading q-mr-xs"
                size="sm"
                style="color:black;"
                @click="enviarTemplate()"
              >
                Enviar Mensaje
              </q-btn>

              <q-btn
                color="negative"
                class="q-subheading q-ml-xs"
                size="sm"
                @click="template = false"
              >
                Cancelar
              </q-btn>
            </div>
          </q-card>
        </q-dialog>
    </q-card>
</template>

<script>
import request from '../request'
import Select from '../components/Compartidos/Select'
import { Notify } from 'quasar'
import auth from '../auth'
export default {
  name: 'MensajeGlobal',
  components: {
    Select
  },
  data () {
    return {
      mensaje: '',
      template: false,
      paramsTemplate: [],
      Templates: [],
      templateSeleccionado: null,
      // errores: [],
      // checkEnvios: [],
      Enviando: false,
      enviarArchivo: false,
      checkMensajesDefault: false,
      opcionesMensajes: [],
      Multimedia: []
    }
  },
  props: [ 'Casos' ],
  created () {
    if (this.Casos.length === 0) {
      Notify.create('Los casos seleccionados no tienen ningun telefono asociado.')
      this.$emit('cerrar')
    }

    request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/mensajes-estudio`, {}, r => {
      if (r.length > 0) {
        this.checkMensajesDefault = true

        this.Templates = r

        this.opcionesMensajes = r.map(m => {
          return {
            label: m.Titulo,
            value: m.IdMensajeEstudio,
            tooltip: m.MensajeEstudio
          }
        })

        this.opcionesMensajes.sort((a, b) => {
          const A = a.label.toLowerCase()
          const B = b.label.toLowerCase()

          if (A < B) return -1
          if (A > B) return 1
          return 0
        })
      }
    })
  },
  /*
  watch: {
    checkEnvios () {
      if (this.checkEnvios.length === this.Casos.length) {
        Notify.create('Se ha enviado un mensaje global a las personas principales de los casos seleccionados.')

        if (this.errores.length > 0) {
          this.errores.forEach(e => {
            Notify.create('No se pudo enviar el mensaje al caso: ' + e.caso)
          })
        }
        console.log('checkEnvios', this.checkEnvios)
        console.log('Errores', this.errores)

        this.$emit('MensajeEnviado')
      }
    }
  },
  */
  methods: {
    factoryFn () {
      return {
        url: 'https://io.docdoc.com.ar/api/multimedia',
        method: 'POST',
        headers: [
          { name: 'Authorization', value: `Bearer ${auth.Token}` }
        ]
      }
    },
    uploadedFile ({ files, xhr }) {
      const data = JSON.parse(xhr.response)
      for (let i = 0; i < files.length; i++) {
        const Tipo = files[i].type

        this.Multimedia.push({
          URL: data.Urls[0],
          Tipo: Tipo.includes('application') ? 'O' : Tipo.substring(0, 1).toUpperCase(),
          OrigenMultimedia: 'R'
        })
      }
    },
    enviarTemplate () {
      let vacio = false

      this.paramsTemplate.forEach(p => {
        if (!p.param) vacio = true
      })

      if (vacio) {
        Notify.create('Debe completar todos los parametros de la plantilla')
        return
      }

      const Contenido = document.getElementById('caja-template').textContent

      const Objeto = {
        template: this.templateSeleccionado.NombreTemplate,
        language: {
          policy: 'deterministic',
          code: 'es'
        },
        namespace: this.templateSeleccionado.NameSpace
      }

      if (this.paramsTemplate.length !== 0) {
        const body = {}
        body.type = 'body'
        body.parameters = this.paramsTemplate.map(p => {
          return {
            type: 'text',
            text: p.param
          }
        })

        Objeto.params = [body]
      }

      let IdsChat = []
      let NuevosChats = []
      this.Enviando = true

      this.Casos.forEach(c => {
        if (c.IdChat) {
          IdsChat.push({
            IdChat: c.IdChat,
            IdCaso: c.IdCaso
          })
        } else {
          const Persona = c.PersonasCaso.find(p => p.EsPrincipal === 'S')
          const NuevoChat = {
            IdCaso: c.IdCaso,
            IdPersona: Persona.IdPersona,
            Telefono: this.telefonoPrincipal(Persona.Telefonos)
          }
          NuevosChats.push(NuevoChat)
        }
      })
      this.template = false

      request.Post(`/mensajes/mensaje-global-template`, { Objeto, Contenido, IdsChat: JSON.stringify(IdsChat), NuevosChats: JSON.stringify(NuevosChats) }, r => {
        Notify.create('Se ha enviado un mensaje global a las personas principales de los casos seleccionados.')
        if (Object.keys(r).length > 0) {
          this.Casos.forEach(c => {
            if (r[c.IdCaso]) {
              Notify.create('No se pudo enviar el mensaje al caso ' + c.Caratula + '. Error: ' + r[c.IdCaso])
              console.log('Caso: ' + c.Caratula + '. Error: ' + r[c.IdCaso])
            }
          })
        }
        this.$emit('MensajeEnviado')
      })
    },
    reemplazarParam (p, key, i) {
      const params = document.querySelectorAll('.param-template')

      for (let index = 0; index < params.length; index++) {
        const element = params[index]

        if (element.innerHTML === key) {
          element.classList.add('param-' + i)

          this.$nextTick().then(() => {
            element.innerHTML = p || key
          })
        } else if (element.classList.contains('param-' + i)) {
          this.$nextTick().then(() => {
            element.innerHTML = p || key
          })
        }
      }
    },
    mensajeSeleccionado (mensaje) {
      this.template = true
      this.paramsTemplate = []

      const m = this.Templates.filter(t => t.IdMensajeEstudio === mensaje.value)[0]
      let crudo = m.MensajeEstudio.replace(/{{/g, `<span class="param-template text-negative text-weight-bold">{{`).replace(/}}/g, '}}</span>')

      let i = 1

      while (m.MensajeEstudio.includes(`{{${i}}}`)) {
        this.paramsTemplate.push({
          key: `{{${i}}}`,
          param: ''
        })

        i++
      }

      this.templateSeleccionado = m

      this.$nextTick().then(() => {
        document.getElementById('caja-template').innerHTML = crudo
      })
    },
    enviarMensaje () {
      if (this.mensaje === '') {
        Notify.create('El mensaje debe tener algun contenido.')
        return
      }

      let IdsChat = []
      let NuevosChats = []
      const Contenido = this.mensaje
      this.Enviando = true

      this.Casos.forEach(c => {
        if (c.IdChat) {
          IdsChat.push({
            IdChat: c.IdChat,
            IdCaso: c.IdCaso
          })
        } else {
          const Persona = c.PersonasCaso.find(p => p.EsPrincipal === 'S')
          const NuevoChat = {
            IdCaso: c.IdCaso,
            IdPersona: Persona.IdPersona,
            Telefono: this.telefonoPrincipal(Persona.Telefonos)
          }
          NuevosChats.push(NuevoChat)
        }
      })

      request.Post(`/mensajes/mensaje-global`, { Contenido: Contenido, IdsChat: JSON.stringify(IdsChat), NuevosChats: JSON.stringify(NuevosChats), Multimedia: JSON.stringify(this.Multimedia) }, r => {
        Notify.create('Se ha enviado un mensaje global a las personas principales de los casos seleccionados.')
        if (Object.keys(r).length > 0) {
          this.Casos.forEach(c => {
            if (r[c.IdCaso]) {
              Notify.create('No se pudo enviar el mensaje al caso ' + c.Caratula + '. Error: ' + r[c.IdCaso])
              console.log('Caso: ' + c.Caratula + '. Error: ' + r[c.IdCaso])
            }
          })
        }
        this.$emit('MensajeEnviado')
      })

      /* Por ahora vamos a probar como funciona el otro metodo pero dejo esto comentado por si todo falla algun dia
      this.Casos.forEach(c => {
        console.log('aver')
        if (c.IdChat) {
          console.log('aver2')
          const Mensaje = {
            IdChat: c.IdChat,
            Contenido: this.mensaje
          }
          request.Post(`/mensajes/enviar`, Mensaje, r => {
            if (!r.Error) {
              const UltMsjLeido = r.IdMensaje
              request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {})
              this.checkEnvios.push({
                IdCaso: c.IdCaso,
                Caso: c.Caratula,
                check: 'Mensaje enviado'
              })
            } else {
              this.checkEnvios.push({
                IdCaso: c.IdCaso,
                Caso: c.Caratula,
                check: 'Mensaje no enviado'
              })
              this.errores.push({
                caso: c.Caratula,
                Error: r.Error
              })
            }
          })
        } else {
          console.log('aver3')
          const Persona = c.PersonasCaso.find(p => p.EsPrincipal === 'S')
          const NuevoChat = {
            IdCaso: c.IdCaso,
            IdPersona: Persona.IdPersona,
            Telefono: this.telefonoPrincipal(Persona.Telefonos)
          }
          request.Post(`/chats/crear`, NuevoChat, r => {
            if (!r.Error) {
              const Mensaje = {
                IdChat: r.IdChat,
                Contendo: this.mensaje
              }
              request.Post(`/mensajes/enviar`, Mensaje, q => {
                if (!q.Error) {
                  const UltMsjLeido = q.IdMensaje
                  request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {})
                  this.checkEnvios.push({
                    IdCaso: c.IdCaso,
                    Caso: c.Caratula,
                    check: 'Mensaje enviado'
                  })
                } else {
                  this.checkEnvios.push({
                    IdCaso: c.IdCaso,
                    Caso: c.Caratula,
                    check: 'Mensaje no enviado'
                  })
                  this.errores.push({
                    caso: c.Caratula,
                    Error: r.Error
                  })
                }
              })
            } else {
              this.checkEnvios.push({
                IdCaso: c.IdCaso,
                Caso: c.Caratula,
                check: 'Chat no creado'
              })
              this.errores.push({
                caso: c.Caratula,
                Error: r.Error
              })
            }
          })
        }
      })
      */
    },
    telefonoPrincipal (telefonos) {
      if (telefonos) {
        const TelefonosPrincipales = (telefonos || []).filter(
          (telefono) => telefono.EsPrincipal === 'S'
        )
        if (!TelefonosPrincipales.length) {
          return telefonos.length ? telefonos[0].Telefono : ''
        } else {
          return TelefonosPrincipales[0].Telefono
        }
      } else {
        return ''
      }
    }
  }
}
</script>
