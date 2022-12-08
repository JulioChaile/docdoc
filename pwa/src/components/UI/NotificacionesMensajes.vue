<template>
  <div>
    <q-list v-if="notificacionesPorInterno.length" class="notificaciones_container">
      <q-expansion-item>
        <template v-slot:header>
          <q-item-section avatar>
            <q-icon color="warning" name="mdi-message-text" />
          </q-item-section>
          <q-item-section class="text-warning">
            Mensajes Nuevos: Chat Interno
          </q-item-section>
        </template>
        <q-item v-for="(item, index) in notificacionesPorInterno" :key="index" class="notificacion">
          <q-item-section @click="abrirChat(item, index, 'interno')">
            <div class="row">
              <div class="col-12 max-width-msj">
                <q-icon class="on-left" name="mdi-message-text" color="primary" size="xs" />
                <span class="text-caption">
                  Mensaje de {{ item.Apellidos + ' ' + item.Nombres }} {{ item.Caratula && `, Caso: ${item.Caratula}` }} {{ item.Origen && `, Origen: ${item.Origen}` }}
                </span>
              </div>
            </div>
          </q-item-section>
        </q-item>
        <!--q-item style="display:flex; text-align:center; cursor: pointer">
          <q-item-section @click="verMensajes('Caso')">
            Ver Mensajes
          </q-item-section>
        </q-item-->
      </q-expansion-item>
    </q-list>

    <q-list v-if="notificacionesPorCaso.length" class="notificaciones_container">
      <q-expansion-item>
        <template v-slot:header>
          <q-item-section avatar>
            <q-icon color="primary" name="mdi-message-text" />
          </q-item-section>
          <q-item-section>
            Mensajes Nuevos: Casos
          </q-item-section>
        </template>
        <q-item v-for="(item, index) in notificacionesPorCaso" :key="index" class="notificacion">
          <q-item-section @click="abrirChat(item, index, 'caso')">
            <div class="row">
              <div class="col-12 max-width-msj">
                <q-icon class="on-left" name="mdi-message-text" color="primary" size="xs" />
                <span class="text-caption">
                  Mensaje de {{ item.Caratula ? item.Caratula : 'N/D' }} {{ item.NroExpediente ? `, Caso ${item.NroExpediente}` : '' }} {{ item.Origen && `, Origen: ${item.Origen}` }}
                </span>
              </div>
            </div>
          </q-item-section>
        </q-item>
        <q-item style="display:flex; text-align:center; cursor: pointer">
          <q-item-section @click="verMensajes('Caso')">
            Ver Mensajes
          </q-item-section>
        </q-item>
      </q-expansion-item>
    </q-list>

    <q-list v-if="notificacionesPorMediador.length" class="notificaciones_container">
      <q-expansion-item>
        <template v-slot:header>
          <q-item-section avatar>
            <q-icon color="primary" name="mdi-message-text" />
          </q-item-section>
          <q-item-section>
            Mensajes Nuevos: Mediadores
          </q-item-section>
        </template>
        <q-item v-for="(item, index) in notificacionesPorMediador" :key="index" class="notificacion">
          <q-item-section @click="abrirChat(item, index, 'mediador')">
            <div class="row">
              <div class="col-12 max-width-msj">
                <q-icon class="on-left" name="mdi-message-text" color="primary" size="xs" />
                <span class="text-caption">
                  Mensaje de {{ item.Caratula }}
                </span>
              </div>
            </div>
          </q-item-section>
        </q-item>
        <q-item style="display:flex; text-align:center; cursor: pointer">
          <q-item-section @click="verMensajes('Mediador')">
            Ver Mensajes
          </q-item-section>
        </q-item>
      </q-expansion-item>
    </q-list>

    <q-list v-if="notificacionesPorContacto.length" class="notificaciones_container">
      <q-expansion-item>
        <template v-slot:header>
          <q-item-section avatar>
            <q-icon color="primary" name="mdi-message-text" />
          </q-item-section>
          <q-item-section>
            Mensajes Nuevos: Contactos
          </q-item-section>
        </template>
        <q-item v-for="(item, index) in notificacionesPorContacto" :key="index" class="notificacion">
          <q-item-section @click="abrirChat(item, index, 'contacto')">
            <div class="row">
              <div class="col-12 max-width-msj">
                <q-icon class="on-left" name="mdi-message-text" color="primary" size="xs" />
                <span class="text-caption">
                  Mensaje de {{ item.Caratula }}
                </span>
              </div>
            </div>
          </q-item-section>
        </q-item>
        <q-item style="display:flex; text-align:center; cursor: pointer">
          <q-item-section @click="verMensajes('Contacto')">
            Ver Mensajes
          </q-item-section>
        </q-item>
      </q-expansion-item>
    </q-list>

    <q-list v-if="CasosEtiquetado.length" class="notificaciones_container">
      <q-expansion-item>
        <template v-slot:header>
          <q-item-section avatar>
            <q-icon color="primary" name="forum" />
          </q-item-section>
          <q-item-section>
            Casos donde se te etiquetó
          </q-item-section>
        </template>
        <q-item
          v-for="c in CasosEtiquetado"
          :key="c.IdCaso"
          class="notificacion"
        >
          <q-item-section>
            <div class="row">
              <div class="col-12 max-width-msj">
                <q-icon class="on-left" name="forum" color="primary" size="xs" />
                <q-icon class="on-left" name="close" color="negative" size="xs" @click="$emit('comentarioVisto', c.IdCaso)">
                  <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 10]">
                    Eliminar de la lista
                  </q-tooltip>
                </q-icon>
                <span class="text-caption" @click="abrirCaso(c.IdCaso)">
                  Fuiste etiquetado en un comentario del caso {{ c.Caratula }}. Haz click para ir al caso
                </span>
              </div>
            </div>
          </q-item-section>
        </q-item>
      </q-expansion-item>
    </q-list>

    <span v-if="notificacionesPorInterno.length === 0 && notificacionesPorCaso.length === 0 && notificacionesPorMediador.length === 0 && notificacionesPorContacto.length === 0 && CasosEtiquetado.length === 0">
      <i>No tienes notificaciones nuevas.</i>
    </span>
  </div>
</template>

<script>
import request from '../../request'
export default {
  name: 'NotificacionesMensajes',
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
    CasosEtiquetado: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      notificacionesPorCaso: [],
      notificacionesPorInterno: [],
      notificacionesPorMediador: [],
      notificacionesPorContacto: [],
      notificacionesLeidas: []
    }
  },
  created () {
    this.notificaciones.forEach(c => {
      this.notificacionesPorCaso.push({
        Caratula: c.Caratula,
        NroExpediente: c.NroExpediente,
        IdChat: c.IdChat,
        IdCaso: c.IdCaso,
        IdPersona: c.IdPersona,
        Telefono: c.Telefono,
        Contenido: c.Contenido,
        IdMensaje: c.IdMensaje,
        IdMediador: null,
        Estado: c.Estado,
        IdJuzgado: c.IdJuzgado,
        Origen: c.Origen
      })
    })

    var hash = {}
    this.notificacionesPorCaso = this.notificacionesPorCaso.filter(c => {
      var exists = !hash[c.IdChat]
      hash[c.IdChat] = true
      return exists
    })

    this.notificacionesMediadores.forEach(c => {
      this.notificacionesPorMediador.push({
        Caratula: c.Nombre,
        IdChat: c.IdChatMediador,
        Telefono: c.Telefono,
        Contenido: c.Contenido,
        IdMensaje: c.IdMensaje,
        IdMediador: c.IdMediador
      })
    })

    var hash2 = {}
    this.notificacionesPorMediador = this.notificacionesPorMediador.filter(c => {
      var exists = !hash2[c.IdChat]
      hash2[c.IdChat] = true
      return exists
    })

    this.notificacionesContactos.forEach(c => {
      this.notificacionesPorContacto.push({
        Caratula: c.Nombre,
        IdChat: c.IdChatContacto,
        Telefono: c.Telefono,
        Contenido: c.Contenido,
        IdMensaje: c.IdMensaje,
        IdContacto: c.IdContacto
      })
    })

    var hash3 = {}
    this.notificacionesPorContacto = this.notificacionesPorContacto.filter(c => {
      var exists = !hash3[c.IdChat]
      hash3[c.IdChat] = true
      return exists
    })

    this.notificacionesInterno.forEach(c => {
      this.notificacionesPorInterno.push(c)
    })

    var hash4 = {}
    this.notificacionesPorInterno = this.notificacionesPorInterno.filter(c => {
      var exists = !hash4[c.IdCaso]
      hash4[c.IdCaso] = true
      return exists
    })
  },
  methods: {
    abrirCaso (id) {
      const routeData = this.$router.resolve({
        name: 'Caso',
        query: {
          id,
          modal: 'comentarios'
        }
      })
      // this.$emit('comentarioVisto', id)
      window.open(routeData.href, '_blank')
    },
    abrirChat (item, index, abrir) {
      const data = { item, index }

      switch (abrir) {
        case 'caso':
          this.notificacionesPorCaso.splice(index, 1)
          this.notificacionesLeidas = this.notificaciones.filter(notificacion => notificacion.IdChat === item.IdChat)
          break

        case 'mediador':
          this.notificacionesPorMediador.splice(index, 1)
          this.notificacionesLeidas = this.notificacionesMediadores.filter(notificacion => notificacion.IdChatMediador === item.IdChat)
          break

        case 'contacto':
          this.notificacionesPorContacto.splice(index, 1)
          this.notificacionesLeidas = this.notificacionesContactos.filter(notificacion => notificacion.IdChatContacto === item.IdChat)
          break

        case 'interno':
          this.notificacionesPorInterno.splice(index, 1)
          this.notificacionesLeidas = this.notificacionesPorInterno.filter(notificacion => notificacion.IdCaso === item.IdCaso)

          const routeData = this.$router.resolve({
            name: 'Caso',
            query: {
              id: item.IdCaso,
              tabChat: 'int'
            }
          })
          window.open(routeData.href, '_blank')
          request.Post(`/mensajes-interno/update`, { IdCaso: item.IdCaso, Cliente: 'S' }, p => {
            if (!p.Error) {
              console.log('UltimoMensajeLeido actualizado correctamente.')
            }
          })
          setTimeout(() => this.$emit('notificacionLeida', data, abrir), 500)
          return
      }

      const routeData = this.$router.resolve({
        name: 'Chat',
        query: {
          id: item.IdChat,
          idCaso: item.IdCaso,
          idPersona: item.IdPersona,
          telefono: item.Telefono,
          caratula: item.Caratula,
          idMediacion: item.IdMediador,
          idContacto: item.IdContacto
        }
      })
      window.open(routeData.href, '_blank')
      this.actualizarLeidos()
      setTimeout(() => this.$emit('notificacionLeida', data, abrir), 500)
    },
    actualizarLeidos () {
      this.notificacionesLeidas.forEach(item => {
        const IdChat = item.IdChat ? item.IdChat : (item.IdChatMediador ? item.IdChatMediador : item.IdChatContacto)
        request.Post(`/chats/${IdChat}/actualizar`, { IdUltimoLeido: item.IdMensaje, mediador: item.IdMediador ? item.IdMediador : null, contacto: item.IdContacto ? item.IdContacto : null }, p => {
          console.log('RESPUESTA:', p)
        })
      })
    },
    verMensajes (ver) {
      const mensajes = this['notificacionesPor' + ver].map(n => {
        return {
          IdChat: n.IdChat,
          IdCaso: n.IdCaso,
          IdMediador: n.IdMediador,
          IdContacto: n.IdContacto,
          Telefono: n.Telefono,
          Caratula: n.Caratula,
          IdMensaje: n.IdMensaje,
          Contenido: n.Contenido,
          Estado: n.Estado,
          IdJuzgado: n.IdJuzgado,
          Origen: n.Origen
        }
      })
      console.log(this['notificacionesPor' + ver])
      localStorage.setItem('DOCDOCTOKENPWA', sessionStorage.getItem('DOCDOCTOKENPWA'))
      localStorage.setItem('DOCDOCDEBECAMBIARPASS', sessionStorage.getItem('DOCDOCDEBECAMBIARPASS'))
      localStorage.setItem('DOCDOCUSUARIOLOGUEADO', sessionStorage.getItem('DOCDOCUSUARIOLOGUEADO'))
      localStorage.setItem('mensajes', JSON.stringify(mensajes))
      localStorage.setItem('toRoute', true)
      const routeData = this.$router.resolve({
        name: 'Chat Notificaciones'
      })
      window.open(routeData.href, '_blank')
    }
  }
}
</script>

<style scoped>
.notificaciones_container {
  max-height: 25em;
  overflow-x: hidden;
  overflow-y: auto;
}

.notificacion {
  cursor:pointer;
  margin: 5px;
  -webkit-box-shadow: 0px 0px 15px 0px rgba(221,221,221,1);
  -moz-box-shadow: 0px 0px 15px 0px rgba(221,221,221,1);
  box-shadow: 0px 0px 15px 0px rgba(221,221,221,1);
  transition: all 0.4s ease;
}

.notificacion:hover {
  background-color: rgba(0, 0, 0, 0.1);
  transform: scale(1.015);
}

.max-width-msj {
  max-width: 300px;
}

/* width */
::-webkit-scrollbar {
  width: 3px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #aaa;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
