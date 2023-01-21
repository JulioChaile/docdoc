<template>
  <div>
    <q-table
      title="Mensajes sin leer"
      :data="mensajes.filter(m => m.Origen === origen || origen === 'Todos' || origen === 'Sin origen' && !m.Origen)"
      :columns="columnas"
      row-key="caso"
      :rows-per-page-options="[10, 20, 30, 0]"
      :loading="loading"
    >
      <template v-slot:top>
        <h5>Mensajes sin leer</h5>
        <q-select
            style="width:30%;"
            class="q-ml-lg"
            v-model="origen"
            :options="origenes"
            label="Origen"
        />
      </template>
      <template v-slot:body="props">
        <q-tr
          class="relative-position"
          :props="props"
          :style="parseInt(props.row.IdJuzgado) === 8 ? 'color: white' : ''"
        >
          <div :class="'fondo-fila ' + bgFila(props.row)">
          </div>
          <q-td
            class="column"
            style="height: auto !important"
            key="Caratula"
            :props="props"
          >
            <div
              v-if="tipoCaso(props.row)"
              :class="'text-caption ' + bgFila(props.row) ? 'text-white' : 'text-grey'"
            >
              {{ tipoCaso(props.row) }}
            </div>
            <div class="ellipsis" style="max-width:300px">
              {{ props.row.Caratula }}
            </div>
          </q-td>
          <q-td key="Origen" :props="props">
            {{ props.row.Origen }}
          </q-td>
          <q-td key="FechaEnviado" :props="props">
            {{ dias(props.row.FechaEnviado) }} dias
          </q-td>
          <q-td key="Contenido" :props="props">
            <div class="ellipsis" style="max-width:500px">
              {{ props.row.Contenido }}
            </div>
          </q-td>
          <q-td key="Acciones" :props="props">
            <q-icon
                name="clear"
                size="sm"
                color="red"
                style="cursor:pointer;"
                @click="marcarLeido(props.row.IdChat, props.row.IdMensaje, props.row.IdMediador ? props.row.IdMediador : null, props.row.IdContacto ? props.row.IdContacto : null)"
              >
                <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                  <span class="text-body2">Marcar como leido</span>
                </q-tooltip>
              </q-icon>
              <q-icon
                name="chat"
                size="sm"
                :color="tipoCaso(props.row) ? 'white' : 'primary'"
                style="cursor:pointer; margin-left: 30px"
                @click="abrirChat(props.row)"
              >
                <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                  <span class="text-body2">Ir al chat</span>
                </q-tooltip>
              </q-icon>
              <q-icon
                name="reply"
                size="sm"
                color="dark"
                style="cursor:pointer; margin-left: 30px"
                @click="habilitarMensaje(props.row)"
              >
                <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                  <span class="text-body2">Responder</span>
                </q-tooltip>
              </q-icon>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    
    <q-dialog v-model="ModalMensaje">
      <q-card style="min-width:400px;">
        <Select
          :multiple="false"
          :label="'Buscar Mensaje'"
          :hint="'Seleccione un Mensaje'"
          :tooltip="true"
          :opciones="opcionesMensajes"
          @seleccion="mensajeSeleccionado"
        />

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
    </q-dialog>
  </div>
</template>

<script>
import moment from 'moment'
import auth from '../auth'
import request from '../request'
import { Notify } from 'quasar'
import Select from '../components/Compartidos/Select'
export default {
  components: { Select },
  data () {
    return {
      loading: false,
      columnas: [
        {
          name: 'Caratula',
          required: true,
          label: 'Caso',
          align: 'left',
          sortable: true
        },
        {
          name: 'Origen',
          required: true,
          label: 'Origen',
          align: 'left',
          sortable: true
        },
        {
          name: 'FechaEnviado',
          required: true,
          label: 'Dias',
          align: 'left',
          sortable: true
        },
        {
          name: 'Contenido',
          required: true,
          label: 'Ultimo Mensaje',
          align: 'left',
          sortable: true
        },
        {
          name: 'Acciones',
          required: true,
          label: 'Acciones',
          align: 'left',
          sortable: true
        }
      ],
      mensajes: [],
      origenes: [],
      origen: 'Todos',
      ModalMensaje: false,
      opcionesMensajes: [],
      template: false,
      paramsTemplate: [],
      Templates: [],
      templateSeleccionado: {}
    }
  },
  created () {
    request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/mensajes-estudio`, {}, r => {
      if (r.length > 0) {
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

    if (sessionStorage.getItem('mensajes')) {
      this.mensajes = JSON.parse(sessionStorage.getItem('mensajes'))
      this.origenes = [ ...new Set(this.mensajes.map(m => m.Origen).filter(o => o)) ]
      this.origenes.push('Todos')
      this.origenes.unshift('Sin origen')
      sessionStorage.removeItem('mensajes')
      if (this.mensajes[0].IdMediador) {
        this.columnas[0].label = 'Mediador'
      }
      if (this.mensajes[0].IdContacto) {
        this.columnas[0].label = 'Contacto'
      }
    }
  },
  methods: {
    dias (f) {
      return moment().diff(moment(f), 'days')
    },
    marcarLeido (IdChat, IdMensaje, IdMediador, IdContacto) {
      this.loading = true
      request.Post(`/chats/${IdChat}/actualizar`, { IdUltimoLeido: IdMensaje, mediador: IdMediador, contacto: IdContacto }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          const i = this.mensajes.findIndex(m => m.IdChat === IdChat)
          this.mensajes.splice(i, 1)

          this.$nextTick(() => {
            this.loading = false
          })

          Notify.create('Ultimo mensaje leido actualizado')
        }
      })
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

      this.template = false
      this.ModalMensaje = false

      const mensajePost = {
        IdChat: this.IdChat,
        Contenido,
        Objeto,
        mediador: '',
        contacto: ''
      }

      request.Post(`/mensajes/enviar-template`, mensajePost, r => {
        if (!r.Error) {
          Notify.create('Mensaje enviado correctamente!')
          request.Post(`/chats/${this.IdChat}/actualizar`, { IdUltimoLeido: r.IdMensaje, mediador: '', contacto: '' }, p => {
            if (!p.Error) {
              console.log('UltimoMensajeLeido actualizado correctamente.')
            } else {
              Notify.create(p.Error)
            }
          })
        } else {
          Notify.create(r.Error)
        }
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
    abrirChat (item) {
      const routeData = this.$router.resolve({
        name: 'Chat',
        query: {
          id: item.IdChat,
          idCaso: item.IdCaso,
          telefono: item.Telefono,
          caratula: item.Caratula,
          idContacto: item.IdContacto,
          idMediacion: item.IdMediador
        }
      })
      window.open(routeData.href, '_blank')
      this.marcarLeido(item.IdChat, item.IdMensaje, item.IdMediador ? item.IdMediador : null, item.IdContacto ? item.IdContacto : null)
    },
    habilitarMensaje (item) {
      this.IdChat = item.IdChat
      this.ModalMensaje = true
    },
    bgFila (f) {
      if (f.Estado === 'P') {
        return 'bg-positive'
      }

      const id = parseInt(f.IdJuzgado)

      if (id === 8) {
        return 'bg-negative'
      } else if (id !== 8 && id !== 12) {
        return 'bg-primary'
      } else if (id === 12) {
        return 'bg-teal'
      } else {
        return ''
      }
    },
    tipoCaso (f) {
      if (f.Estado === 'P') {
        return 'Pendiente'
      }

      const id = parseInt(f.IdJuzgado)

      if (id === 8) {
        return 'Mediacion'
      } else if (id !== 8 && id !== 12) {
        return 'Judicial'
      } else if (id === 12) {
        return 'Consulta'
      } else {
        return ''
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
    }
  }
}
</script>

<style>
.fondo-fila {
  position: absolute;
  width: inherit;
  height: inherit;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  opacity: 0.7;
}
</style>
