/* eslint-disable vue/no-side-effects-in-computed-properties */
<template>
  <q-page class="q-px-lg">
    <q-infinite-scroll :disable="noHayMasComunicados" @load="onLoad" style="width: 100%; padding: 0px; margin: 0px" :offset="2000">
      <div class="full-width flex justify-center q-mt-lg">
        <q-btn @click="habilitarAgregar" color="primary">
          Nuevo Comunicado
        </q-btn>
      </div>

      <q-card
        v-for="c in comunicados"
        :key="c.IdComunicado"
        style="background-color: white;"
        class="q-my-lg q-pb-lg"
      >
        <q-item :class="`bg-primary`">
          <q-item-section>
            <b>{{ c.Titulo }}</b>
          </q-item-section>
          <div class="flex absolute-right q-mt-sm">
            <q-icon @click="habilitarEditar(c)" class="q-mr-sm cursor-pointer" name="edit" color="white" size="sm" />
            <q-icon class="q-mr-sm cursor-pointer" name="delete" color="white" size="sm" />
          </div>
        </q-item>
        <q-separator />
        <!-- Detalle -->
        <q-card-section class="q-pt-lg position-relative" style="display:flex; justify-content:space-between; align-items:center;">
          <!--div class="absolute-left text-caption q-ml-sm text-grey">
            Fecha Alta: {{ formatFecha(c.FechaAlta) }} | Fecha Comunicado: {{ formatFecha(c.Comunicado) }}
          </div-->
          {{ c.Contenido }}
        </q-card-section>

        <div class="full-width flex justify-center">
          <video v-if="c.Tipo === 'V'" style="max-height: 600px" :src="`https://io.docdoc.com.ar/api/multimedia?file=${c.URL}`" controls></video>
          <img  v-if="c.Tipo === 'I'" style="max-height: 600px" :src="`https://io.docdoc.com.ar/api/multimedia?file=${c.URL}`">
        </div>
      </q-card>
    </q-infinite-scroll>

    <q-dialog
      v-model="modal"
    >
      <q-card class="relative-position q-px-sm q-py-sm">
        <q-input class="q-mb-lg" dense v-model="comunicado.Titulo" label="Titulo" />

        Contenido
        <q-input
          v-model="comunicado.Contenido"
          filled
          type="textarea"
        />

        <q-uploader
          v-if="agregar"
          ref="uploader"
          label="Multimedia"
          :factory="factoryFn"
          @added="addedFile"
          @uploaded="uploadedFile"
          style="width: 97%; margin-top: 10px"
        />

        <q-btn @click="guardar" class="q-my-lg" color="primary">
          Guardar
        </q-btn>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import moment from 'moment'
import Loading from '../components/Loading'
import request from '../request'
import auth from '../auth'

export default {
  components: {
    Loading
  },
  name: 'Comunicados',
  data () {
    return {
      comunicados: [],
      noHayMasComunicados: false,
      modal: false,
      editar: false,
      agregar: false,
      url: '',
      file: '',
      comunicado: {
        Titulo: '',
        Contenido: '',
        IdMultimedia: null,
        Tipo: '',
        URL: ''
      }
    }
  },
  created () {
    //this.onLoad(0, () => {})
  },
  methods: {
    uploadedFile ({ files, xhr }) {
      const data = this.url ? null : JSON.parse(xhr.response)
      for (let i = 0; i < files.length; i++) {
        const Tipo = files[i].type

        const formatosDoc = ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'odt', 'pdf']
        const formato =  data ? data.Urls[0].split('.').reverse()[0].toLowerCase() : this.file.split('.').reverse()[0].toLowerCase()

        let origen = this.tab === 'cliente' ? 'R' : 'C'

        origen = formatosDoc.includes(formato) ? 'D' : origen

        this.comunicado.URL = data ? data.Urls[0] : this.file
        this.comunicado.Nombre = data ? data.Names[0] : files[i].name.split('.')[0]
        this.comunicado.Tipo = Tipo.includes('application') ? 'O' : Tipo.substring(0, 1).toUpperCase()
      }
    },
    addedFile (files) {
      const extension = files[0].name.split('.').reverse()[0].toLowerCase()
      
      if (['jpeg', 'jpg', 'png'].includes(extension)) {
        this.url = null
        this.file = null
        this.$refs.uploader.upload()

        return
      }

      request.Post('/multimedia/link', { extension }, r => {
        this.url = r.url
        this.file = r.file

        this.$refs.uploader.upload()
      })
    },
    factoryFn () {
      const method = this.url ? 'PUT' : 'POST'

      const headers = this.url
        ? [{ name: 'Content-Type', value: 'application/octet-stream' }]
        : [{ name: 'Authorization', value: `Bearer ${auth.Token}` }]
      
      return {
        url: this.url || 'https://io.docdoc.com.ar/api/multimedia',
        method,
        headers
      }
    },
    formatFecha (f) {
      return moment(f).format('DD/MM/YYYY')
    },
    onLoad (index, done, limit = 30) {
      request.Get(`/comunicados/listar?Offset=${this.comunicados.length}`, {}, t => {
        if (t.Error) {
          this.$q.notify(t.Error)
        } else {
          this.comunicados = [ ...this.comunicados, ...t ]

          if (t.length === 0) {
            this.noHayMasComunicados = true
            done()
            return
          }
        }
      })
    },
    guardar() {
      if (this.agregar) {
        if (!this.comunicado.Titulo || !this.comunicado.Contenido) {
          this.$q.notify('Debe escribir un titulo y contenido')
          return
        }

        request.Post('/comunicados/alta-comunicado', this.comunicado, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            this.comunicado.IdComunicado = r.IdComunicado

            this.comunicados.unshift(this.comunicado)

            this.$q.notify('Comunicado creado correctamente')
            this.modal = false
          }
        })
      } else {
        if (!this.comunicado.Titulo || !this.comunicado.Contenido) {
          this.$q.notify('Debe escribir un titulo y contenido')
          return
        }

        request.Post('/comunicados/editar-comunicado', this.comunicado, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            this.comunicados = this.comunicados.map(c => c.IdComunicado === this.comunicado.IdComunicado ? this.comunicado : c)

            this.$q.notify('Comunicado editado correctamente')
            this.modal = false
          }
        })
      }
    },
    reOnLoad () {
      this.loading = true
      this.movimientos = []
      this.onLoad(0, () => {})
    },
    habilitarAgregar () {
      this.modal = true
      this.agregar = true
      this.comunicado = {
        Titulo: '',
        Contenido: ''
      }
      this.IdMultimedia = null
    },
    habilitarEditar (c) {
      this.modal = true
      this.editar = true
      this.comunicado = { ...c }
    }
  }
}
</script>

<style>
.movimientos__container {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
}

.contenedor-opciones {
  justify-self: center;
  width: 90%;
  padding: 0em 1em 1em 1em;
  margin-top:10px;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  border-radius: 5px;
  background-color: white
}

.select-clase {
  width: 70%;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  border-radius: 5px;
  padding: 0em 1em 1em 1em;
  margin-bottom: 5px
}

.input-clase {
  margin: .6em .6em 0 .6em;
  width: 90%;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)
}

.tarjeta-mov {
  margin-left: auto;
  margin-right: auto;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
</style>
