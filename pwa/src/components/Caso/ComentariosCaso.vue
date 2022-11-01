<template>
    <q-card style="min-width:800px;">
        <q-item class="bg-black flex text-center items-center text-white">
            <q-item-label>
                <span class="q-subheading">Comentarios</span>
            </q-item-label>
        </q-item>
        <div class="contenedor-comentarios">
          <div v-if="loading">
              <Loading />
          </div>
          <div
            v-else-if="Comentarios.length === 0"
            class="q-pa-lg"
          >
              <q-item-label>Este caso no tiene comentarios</q-item-label>
          </div>
          <q-card
            v-for="c in Comentarios"
            :key="c.IdComentarioCaso"
            class="column q-pa-lg q-ma-md relative-position"
          >
            <div class="absolute-right q-mt-sm q-mr-sm text-bold text-negative cursor-pointer" @click="responder(c)">
              Responder
            </div>
            <div class="text-left text-bold">
              {{ c.Apellidos + ' ' + c.Nombres }}
            </div>
            <div>
              <span
                v-for="(u, i) in c.UsuariosEtiquetados"
                :key="u.IdUsuario"
                class="text-caption"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
                  {{ u.FechaVisto ? 'Visto ' + fecha(u.FechaVisto) : 'Sin ver' }}
                </q-tooltip>
                <span :class="u.FechaVisto ? 'text-primary' : 'text-grey'">
                  {{ u.Apellidos + ' ' + u.Nombres }}
                </span>
                {{ (i === c.UsuariosEtiquetados.length - 1 ? '' : ' - ') }}
              </span>
            </div>
            <div class="q-my-xs">
              {{ c.Comentario }}
            </div>
            <div class="text-caption text-positive text-bold self-end">
              {{ c.FechaEnviado }}
            </div>
          </q-card>
        </div>

        <q-separator class="q-my-md" />

        <div>
          <q-input
            v-model="comentario"
            @keyup.enter="send"
            class="q-pr-md q-pl-lg q-pb-sm"
            filled
            type="textarea"
            rows="1"
            placeholder="Escriba su comentario aqui..."
          >
            <template v-slot:after>
              <q-btn
                round
                flat
                icon="people"
                class="send_btn"
              >
                <q-tooltip>Etiquetar usuarios</q-tooltip>
                <q-popup-proxy>
                  <q-select
                    filled
                    v-model="Usuarios"
                    multiple
                    emit-value
                    map-options
                    :options="opcionesUsuarios"
                    label="Etiquetar usuarios"
                    style="width: 250px"
                  />
                </q-popup-proxy>
              </q-btn>
              <q-btn
                round
                flat
                icon="send"
                class="send_btn"
                @click="send()"
              >
                <q-tooltip>Enviar comentario</q-tooltip>
              </q-btn>
            </template>
          </q-input>

          <div v-if="Usuarios.length" class="q-mt-sm q-pl-lg q-pb-lg">
            <span class="text-bold">Usuarios Etiquetados</span>
            <div class="text-primary">
              <span v-for="u in Usuarios" :key="u.IdUsuario"> - {{ u.Apellidos }}, {{ u.Nombres }}</span>
            </div>
          </div>
        </div>
    </q-card>
</template>

<script>
import moment from 'moment'
import Loading from '../../components/Loading'
import request from '../../request'
import auth from '../../auth'
import { Notify } from 'quasar'
export default {
  name: 'MovimientosCaso',
  components: {
    Loading
  },
  data () {
    return {
      loading: true,
      comentario: '',
      Comentarios: [],
      UsuariosEstudio: [],
      Usuarios: []
    }
  },
  props: [ 'IdCaso' ],
  created () {
    const IdEstudio = auth.UsuarioLogueado.IdEstudio

    request.Get(`/estudios/${IdEstudio}/usuarios`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.UsuariosEstudio = r
      }
    })

    if (this.$route.query.modal !== 'comentarios') {
      request.Post('/comentarios-caso/comentario-visto', { IdCaso: this.IdCaso }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        }
      })
    }

    request.Get('/comentarios-caso/', { IdCaso: this.IdCaso }, r => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        r.forEach(c => {
          c.UsuariosEtiquetados = JSON.parse(c.UsuariosEtiquetados)
          c.FechaEnviado = this.fecha(c.FechaEnviado)

          if (c.UsuariosEtiquetados.length === 1 && !c.UsuariosEtiquetados[0].Usuario) c.UsuariosEtiquetados = []
        })

        this.Comentarios = r
        this.loading = false
      }
    })
  },
  computed: {
    opcionesUsuarios () {
      return this.UsuariosEstudio.map(u => {
        return {
          label: u.Apellidos + ' ' + u.Nombres,
          value: {
            Apellidos: u.Apellidos,
            Nombres: u.Nombres,
            IdUsuario: u.IdUsuario,
            FechaVisto: null
          }
        }
      })
    }
  },
  methods: {
    responder (c) {
      this.Usuarios = c.UsuariosEtiquetados.map(u => {
        return { ...u }
      })

      if (this.Usuarios.findIndex(u => parseInt(u.IdUsuario) === parseInt(c.IdUsuario)) === -1) {
        this.Usuarios.push({
          Apellidos: c.Apellidos,
          Nombres: c.Nombres,
          IdUsuario: c.IdUsuario
        })
      }

      this.Usuarios.forEach(u => { u.FechaVisto = null })
    },
    fecha (f) {
      return moment(f).format('DD/MM/YYYY HH:mm')
    },
    send () {
      if (!this.comentario) {
        Notify.create('No puede enviar un comentario vacio')
        return
      }

      const idsUsuarios = this.Usuarios.length
        ? this.Usuarios.map(u => u.IdUsuario)
        : []

      const params = {
        IdCaso: this.IdCaso,
        IdsUsuarios: JSON.stringify(idsUsuarios),
        Comentario: this.comentario
      }

      request.Post('/comentarios-caso/alta', params, r => {
        if (r.Error) {
          Notify.create(r.Error)
          this.enviando = false
        } else {
          Notify.create('Tu comentario se envio con exito.')

          this.Comentarios.unshift({
            IdComentarioCaso: r.IdComentarioCaso,
            Comentario: this.comentario,
            FechaEnviado: moment().format('DD/MM/YYYY HH:mm'),
            IdUsuario: auth.UsuarioLogueado.IdUsuario,
            Apellidos: auth.UsuarioLogueado.Apellidos,
            Nombres: auth.UsuarioLogueado.Nombres,
            UsuariosEtiquetados: this.Usuarios
          })

          if (r.length) {
            let usuarios = []

            r.forEach(id => {
              const i = this.Usuarios.findIndex(u => parseInt(u.IdUsuario) === parseInt(id))

              usuarios.push(this.Usuarios[i].Apellidos + ' ' + this.Usuarios[i].Nombres)
            })

            usuarios.forEach(u => {
              Notify.create('Ocurrio un error al etiquetar a ' + u)
            })
          }

          this.comentario = ''
          this.Usuarios = []
        }
      })
    }
  }
}
</script>

<style>
.contenedor-comentarios {
  height: auto;
  max-height: 60vh;
  overflow: scroll;
}
</style>
