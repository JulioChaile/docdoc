<template>
  <q-table
    title="Mensajes sin leer"
    :data="mensajes"
    :columns="columnas"
    row-key="caso"
    :rows-per-page-options="[10, 20, 30, 0]"
    :loading="loading"
  >
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
        </q-td>
      </q-tr>
    </template>
  </q-table>
</template>

<script>
import request from '../request'
import { Notify } from 'quasar'
export default {
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
      mensajes: []
    }
  },
  created () {
    if (sessionStorage.getItem('mensajes')) {
      this.mensajes = JSON.parse(sessionStorage.getItem('mensajes'))
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
