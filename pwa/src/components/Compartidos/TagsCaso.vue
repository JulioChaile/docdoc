<template>
    <q-card class="tags_card">
      <div class="full-width text-center text-h5 q-py-md">
        Etiquetas del caso
      </div>

      <q-separator />

      <div class="q-pa-sm row tags_container">
        <div
          v-if="!EtiquetasCaso.length"
          class="full-width text-center text-caption q-my-md"
        >
          El caso aun no tiene etiquetas añadidas
        </div>

        <q-badge
          v-for="e in EtiquetasCaso"
          :key="e.IdEtiquetaCaso"
          color="yellow"
          text-color="black"
          class="text-caption"
          @click="eliminarEtiqueta(e.IdEtiquetaCaso)"
        >
          {{ e.Etiqueta }}
          <q-tooltip>Quitar etiqueta al caso</q-tooltip>
        </q-badge>
      </div>

      <q-separator />

      <div class="full-width text-center text-h5 q-py-md">
        Etiquetas usadas anteriormente
      </div>

      <q-separator />

      <div class="q-pa-sm row tags_container">
        <Loading v-if="loading" />

        <div
          v-else-if="!EtiquetasEstudioComputed.length && (!nuevoTag || EtiquetasEstudio.includes(nuevoTag.toLowerCase()))"
          class="full-width text-center text-caption q-my-md"
        >
          No hay etiquetas para mostrar
        </div>

        <q-badge
          v-for="e in EtiquetasEstudioComputed"
          :key="e"
          color="orange"
          text-color="black"
          class="text-caption"
          @click="altaEtiqueta(e)"
        >
          {{ e }}
          <q-tooltip>Añadir etiqueta al caso</q-tooltip>
        </q-badge>

        <q-badge
          v-if="nuevoTag && !EtiquetasEstudio.includes(nuevoTag.toLowerCase())"
          color="orange"
          text-color="black"
          class="text-caption"
          @click="altaEtiqueta(nuevoTag.toLowerCase(), true)"
        >
          {{ nuevoTag.toLowerCase() }}
          <q-tooltip>Añadir etiqueta al caso</q-tooltip>
        </q-badge>
      </div>

      <q-separator />

      <div class="full-width column items-center text-center text-subtitle2 q-pa-sm">
        Busca o agrega una nueva etiqueta
        <q-input
          v-model="nuevoTag"
          color="yellow"
          rounded
          outlined
          dense
        >
          <template v-slot:prepend>
            <q-icon
              name="label"
              color="yellow"
            />
          </template>
        </q-input>
      </div>
    </q-card>
</template>

<script>
import request from '../../request'
import Loading from '../Loading'

export default {
  components: {
    Loading
  },
  props: [ 'IdCaso', 'EtiquetasCaso' ],
  data () {
    return {
      loading: true,
      EtiquetasEstudio: [],
      nuevoTag: ''
    }
  },
  created () {
    request.Get('/casos/listar-etiquetas', {}, r => {
      if (r.Error) {
        this.$q.notify('Hubo un error al traer las etiquetas del estudio')
        console.log(r.Error)
      } else {
        this.EtiquetasEstudio = r.map(e => e.Etiqueta)
        this.loading = false
      }
    })
  },
  computed: {
    EtiquetasEstudioComputed () {
      let etiquetas = []

      this.EtiquetasEstudio.forEach(e => {
        const i = this.EtiquetasCaso.findIndex(ec => ec.Etiqueta === e)

        if (i < 0 && e.includes(this.nuevoTag.toLowerCase())) {
          etiquetas.push(e)
        }
      })

      return etiquetas
    }
  },
  methods: {
    altaEtiqueta (tag, nueva = false) {
      if (!tag) {
        this.$q.notify('La nueva etiqueta no puede estar vacia')
        return
      }

      this.nuevoTag = ''

      request.Post('/casos/alta-etiqueta', {Etiqueta: tag.toLowerCase(), IdCaso: this.IdCaso}, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.EtiquetasCaso.push({
            Etiqueta: tag.toLowerCase(),
            IdEtiquetaCaso: r.IdEtiquetaCaso
          })

          if (nueva) {
            this.EtiquetasEstudio.push(tag)
          }

          this.$q.notify('Se agregó la etiqueta al caso')
        }
      })
    },
    eliminarEtiqueta (id) {
      request.Post('/casos/borrar-etiqueta', {IdEtiquetaCaso: id}, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          const i = this.EtiquetasCaso.findIndex(e => e.IdEtiquetaCaso === id)
          this.EtiquetasCaso.splice(i, 1)

          this.$q.notify('Se quitó la etiqueta al caso')
        }
      })
    }
  }
}
</script>

<style>
.tags_card {
 width: 400px !important;
}

.tags_container .q-badge {
  margin: 5px;
  cursor: pointer;
}
</style>
