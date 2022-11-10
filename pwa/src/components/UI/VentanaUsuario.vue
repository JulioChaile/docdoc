<template>
  <q-card>
    <q-list bordered>
      <!-- Notificaciones -->
      <q-item-label header>
          Notificaciones
      </q-item-label>
      <q-item sparse>
        <q-item-section>
          <NotificacionesMensajes
            :notificaciones="notificaciones"
            :notificacionesMediadores="notificacionesMediadores"
            :notificacionesContactos="notificacionesContactos"
            :notificacionesInterno="notificacionesInterno"
            :CasosEtiquetado="CasosEtiquetado"
            @comentarioVisto="comentarioVisto"
            @notificacionLeida="leerNotificacion"
          />
        </q-item-section>
      </q-item>

      <!-- Vencimientos -->
      <q-item sparse v-if="verVencimientos">
        <q-item-section>
        <BotonVencimientos @vencimientosVistos="$emit('vencimientosVistos')" />
        </q-item-section>
      </q-item>
      <q-separator />

      <!-- WhatsApp -->
      <q-item v-if="IdEstudio === 5 || IdEstudio === '5'" to="/Whatsapp" sparse class="ventana_user_btn row justify-center text-positive">
        <q-item-section side top>
          <q-icon name="mail" color="positive" />
        </q-item-section>
        <q-item-section>
          Whatsapp
        </q-item-section>
        <q-badge v-if="notificacionesExterno.length > 0" color="red" text-color="white">
          {{notificacionesExterno.length}}
        </q-badge>
      </q-item>
      <q-separator />

      <!-- Ajustes y cerrar sesión -->
      <q-item sparse class="ventana_user_btn" to="/Login">
        <q-item-section side top>
          <q-icon name="exit_to_app" />
        </q-item-section>
        <q-item-section>
          Cerrar Sesión
        </q-item-section>
      </q-item>
    </q-list>
  </q-card>
</template>

<script>
import auth from '../../auth'
import NotificacionesMensajes from './NotificacionesMensajes'
import BotonVencimientos from './BotonVencimientos'

export default {
  name: 'VentanaUsuario',
  components: { NotificacionesMensajes, BotonVencimientos },
  props: {
    notificaciones: {
      type: Array,
      default: () => []
    },
    notificacionesMediadores: {
      type: Array,
      default: () => []
    },
    notificacionesContactos: {
      type: Array,
      default: () => []
    },
    notificacionesInterno: {
      type: Array,
      default: () => []
    },
    notificacionesExterno: {
      type: Array,
      default: () => []
    },
    CasosEtiquetado: {
      type: Array,
      default: () => []
    },
    verVencimientos: {
      type: Boolean,
      default: () => []
    }
  },
  data () {
    return {
      IdEstudio: auth.UsuarioLogueado.IdEstudio
    }
  },
  methods: {
    leerNotificacion (data, tipo) {
      this.$emit('notificacionLeida', data, tipo)
    },
    comentarioVisto (id) {
      this.$emit('comentarioVisto', id)
    },
    abrirWhatsapp () {
      const routeData = this.$router.resolve({
        name: 'Whatsapp'
      })
      window.open(routeData.href, '_blank')
    }
  }
}
</script>

<style scoped>
.ventana_user_btn {
  display:flex;
  align-items:center;
  bottom: 0;
  width: 100%
}
</style>
