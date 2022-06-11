<template>
  <div>
    <q-table
      title="Contactos del estudio"
      :data="contactos"
      :columns="ver === 'contactos' ? columnas : columnasMediadores"
      row-key="contacto"
      :loading="loading"
      :rows-per-page-options="[25]"
      hide-pagination
    >
      <template v-slot:top-right>
        <div class="row">
          <div class="column" style="margin-right: 20px">
            <q-radio v-model="ver" val="contactos" label="Contactos" />
            <q-radio v-model="ver" val="mediadores" label="Mediadores" />
          </div>
          <q-select
            v-if="ver === 'contactos'"
            class="self-end"
            v-model="Tipo"
            label="Tipo"
            :options="opcionesTipo"
            @input="update(1)"
          />
          <q-input class="self-end" dense debounce="300" v-model="filter" placeholder="Buscar" style="margin-left: 7px">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
          <q-btn color="primary self-center" style="margin-right: 7px" round size="l" @click="modalContacto = true">
            +
            <q-tooltip>
              Agregar {{ ver === 'contactos' ? 'Contacto' :'Mediador' }}
            </q-tooltip>
          </q-btn>
        </div>
      </template>
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td key="contacto" :props="props">
            {{ props.row.Contacto }}
          </q-td>
          <q-td key="telefono" :props="props">
            {{ props.row.Telefono }}
          </q-td>
          <q-td key="email" :props="props">
            {{ props.row.Email }}
          </q-td>
          <q-td v-if="ver === 'mediadores'" key="registro" :props="props">
            {{ props.row.Registro }}
          </q-td>
          <q-td v-if="ver === 'mediadores'" key="mp" :props="props">
            {{ props.row.MP }}
          </q-td>
          <q-td v-if="ver === 'contactos'" key="tipo" :props="props">
            {{ tipoContacto(props.row.Tipo) }}
          </q-td>
          <q-td key="Acciones" :props="props">
            <q-icon
              name="chat"
              size="xs"
              color="primary"
              style="cursor:pointer"
              @click="ver === 'contactos' ? abrirChatContacto(props.row) : abrirChatMediador(props.row)"
            >
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                <span class="text-body2">Ir al chat</span>
              </q-tooltip>
            </q-icon>
            <q-icon
              name="edit"
              size="xs"
              color="primary"
              style="cursor:pointer; margin-left: 5px"
              @click="habilitarEdicion(props.row)"
            >
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                <span class="text-body2">{{ props.row.IdContacto ? 'Editar contacto' : 'Editar mediador' }}</span>
              </q-tooltip>
            </q-icon>
            <q-icon
              name="clear"
              size="xs"
              color="red"
              style="cursor:pointer; margin-left: 5px"
              @click="modalEliminar = true; idEliminar = props.row.IdContacto"
            >
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                <span class="text-body2">Eliminar</span>
              </q-tooltip>
            </q-icon>
          </q-td>
        </q-tr>
      </template>
      <template v-slot:bottom>
        <div class="justify-center" style="width: 100%">
          <div class="row justify-center">
            <q-pagination
              v-model="page"
              color="grey-8"
              :max="pagesNumber"
              size="sm"
              @input="update"
            />
          </div>
        </div>
      </template>
    </q-table>

    <!-- Modal: Alta / Editar contacto -->
    <q-dialog v-model="modalContacto">
      <ModalContacto
        :editar="editar"
        :datosEditar="datosEditar"
        :mediador="ver === 'mediadores'"
        @altaContacto="altaContacto"
        @editarContacto="editarContacto"
        @cerrar="cerrarModal"
      />
    </q-dialog>

    <!-- Modal: Eliminar contacto -->
    <q-dialog
      v-model="modalEliminar"
      prevent-close
    >
      <q-card style="padding:1rem;">
        <div style="text-align: center;">
          <span>
            <p>
              ¿Esta seguro que desea eliminar este contacto?
              <br>
              Esta accion es permanente.
            </p>
          </span>
          <div style="display: flex;justify-content: center; margin-botton:2rem;">
            <q-btn
              color="negative"
              label="Eliminar"
              @click="eliminarContacto()"
              style="padding-top:0px; float: center; margin-right: 10px"
            />
            <q-btn
              color="primary"
              label="Cancelar"
              @click="modalEliminar = false"
              style="padding-top:0px; float: center"
            />
          </div>
        </div>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import request from '../request'
import { Notify, QPagination, QRadio } from 'quasar'
import ModalContacto from '../components/Contactos/ModalContacto'
export default {
  components: { ModalContacto, QPagination, QRadio },
  data () {
    return {
      loading: true,
      modalEliminar: false,
      idElminar: 0,
      modalContacto: false,
      editar: false,
      datosEditar: {},
      ver: 'contactos',
      contactos: [],
      rows: 25,
      page: 1,
      filter: '',
      Tipo: {
        label: 'Todos',
        value: ''
      },
      opcionesTipo: [
        {
          label: 'Todos',
          value: ''
        },
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
      columnas: [
        {
          name: 'contacto',
          required: true,
          label: 'Contacto',
          align: 'left',
          sortable: true
        },
        {
          name: 'telefono',
          required: true,
          label: 'Telefono',
          align: 'left',
          sortable: true
        },
        {
          name: 'email',
          required: true,
          label: 'Email',
          align: 'left',
          sortable: true
        },
        {
          name: 'tipo',
          required: true,
          label: 'Tipo',
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
      columnasMediadores: [
        {
          name: 'contacto',
          required: true,
          label: 'Contacto',
          align: 'left',
          sortable: true
        },
        {
          name: 'telefono',
          required: true,
          label: 'Telefono',
          align: 'left',
          sortable: true
        },
        {
          name: 'email',
          required: true,
          label: 'Email',
          align: 'left',
          sortable: true
        },
        {
          name: 'registro',
          required: true,
          label: 'Registro',
          align: 'left',
          sortable: true
        },
        {
          name: 'mp',
          required: true,
          label: 'MP',
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
      ]
    }
  },
  created () {
    request.Get(`/contactos/buscar-contacto`, {offset: 0, limit: 25}, r => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        const contactos = r.map(c => {
          return {
            IdContacto: c.IdContacto,
            Contacto: c.Apellidos + ', ' + c.Nombres,
            Telefono: c.Telefono,
            Email: c.Email,
            Tipo: c.Tipo,
            IdChat: c.IdChatContacto
          }
        })
        this.contactos = contactos
        this.loading = false
      }
    })
    request.Get(`/contactos/contar-contactos`, {}, r => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        this.rows = r
      }
    })
  },
  computed: {
    pagesNumber () {
      return Math.ceil(parseInt(this.rows) / 25)
    }
  },
  watch: {
    filter () {
      if (this.filter.length >= 4) {
        this.contactos = []
        this.page = 1
        this.update(1)
      } else if (this.filter === '') {
        this.contactos = []
        this.page = 1
        this.update(1)
      }
    },
    ver () {
      this.contactos = []
      this.page = 1
      this.filter = ''
      this.update(1)
    }
  },
  methods: {
    update (page) {
      const offset = (page - 1) * 25
      const filter = this.filter.length >= 4 ? this.filter : ''
      const tipo = this.Tipo.value
      this.loading = true

      if (this.ver === 'contactos') {
        request.Get(`/contactos/buscar-contacto`, {offset: offset, limit: 25, cadena: filter, tipo: tipo}, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            const contactos = r.map(c => {
              return {
                IdContacto: c.IdContacto,
                Contacto: c.Apellidos + ', ' + c.Nombres,
                Telefono: c.Telefono,
                Email: c.Email,
                Tipo: c.Tipo,
                IdChat: c.IdChatContacto
              }
            })
            this.contactos = contactos
            this.loading = false
          }
        })
        request.Get(`/contactos/contar-contactos`, {Cadena: filter, Tipo: tipo}, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.rows = r
          }
        })
      } else {
        request.Get(`/mediaciones/mediadores`, {offset: offset, limit: 25, cadena: filter}, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            const contactos = r.map(c => {
              return {
                IdContacto: c.IdMediador,
                IdChat: c.IdChatMediador,
                Contacto: c.Nombre,
                Telefono: c.Telefono,
                Email: c.Email,
                Tipo: c.Tipo,
                MP: c.MP,
                Registro: c.Registro
              }
            })
            this.contactos = contactos
            this.loading = false
          }
        })
        request.Get(`/mediadores/contar-mediadores`, {Cadena: filter}, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.rows = r
          }
        })
      }
    },
    habilitarEdicion (datos) {
      this.datosEditar = datos
      this.editar = true
      this.modalContacto = true
    },
    editarContacto (datos) {
      this.loading = true
      setTimeout(() => {
        this.loading = false
      }, 300)

      const nombres = this.ver === 'contactos'
        ? datos.Apellidos + ', ' + datos.Nombres
        : datos.Nombres

      const contacto = {
        Contacto: nombres,
        IdContacto: datos.IdContacto,
        Registro: datos.Registro,
        MP: datos.MP,
        Telefono: datos.Telefono,
        Email: datos.Email,
        Tipo: datos.Tipo
      }

      const i = this.contactos.findIndex(c => parseInt(c.IdContacto) === parseInt(contacto.IdContacto))

      this.contactos[i] = contacto
      this.contactos = this.contactos.sort((a, b) => {
        if (a.Contacto.toLowerCase() > b.Contacto.toLowerCase()) {
          return 1
        } else if (a.Contacto.toLowerCase() < b.Contacto.toLowerCase()) {
          return -1
        } else {
          return 0
        }
      })
      this.modalContacto = false
    },
    altaContacto (datos) {
      const nombres = this.ver === 'contactos'
        ? datos.Apellidos + ', ' + datos.Nombres
        : datos.Nombres

      const contacto = {
        Contacto: nombres,
        IdContacto: datos.IdContacto,
        Registro: datos.Registro,
        MP: datos.MP,
        Telefono: datos.Telefono,
        Email: datos.Email,
        Tipo: datos.Tipo
      }

      this.contactos.push(contacto)
      this.contactos = this.contactos.sort((a, b) => {
        if (a.Contacto.toLowerCase() > b.Contacto.toLowerCase()) {
          return 1
        } else if (a.Contacto.toLowerCase() < b.Contacto.toLowerCase()) {
          return -1
        } else {
          return 0
        }
      })
      if (this.contactos.length > 25) {
        this.contactos.pop()
      }
      this.modalContacto = false
    },
    eliminarContacto () {
      this.modalEliminar = false
      if (this.ver === 'contactos') {
        request.Post(`/contactos/borrar-contacto`, {IdContacto: this.idEliminar}, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            if (this.contactos.length === 1) {
              this.page === 1
                ? this.contactos.pop()
                : this.update(this.page - 1)
              this.page = this.page - 1
            } else {
              this.update(this.page)
            }
            this.idEliminar = 0
            Notify.create('Se elimino el contacto correctamente.')
          }
        })
      } else {
        request.Post(`/mediadores/borrar-contacto`, {IdMediador: this.idEliminar}, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            if (this.contactos.length === 1) {
              this.page === 1
                ? this.contactos.pop()
                : this.update(this.page - 1)
              this.page = this.page - 1
            } else {
              this.update(this.page)
            }
            this.idEliminar = 0
            Notify.create('Se elimino el contacto correctamente.')
          }
        })
      }
    },
    cerrarModal () {
      this.datosEditar = {}
      this.editar = false
      this.modalContacto = false
    },
    abrirChatMediador (item) {
      console.log(item)
      if (item.IdChat) {
        const routeData = this.$router.resolve({
          name: 'Chat',
          query: {
            id: item.IdChat,
            caratula: item.Contacto,
            telefono: item.Telefono,
            idMediacion: item.IdContacto
          }
        })
        window.open(routeData.href, '_blank')
      } else {
        const nuevoChat = {
          Telefono: item.Telefono,
          IdMediador: item.IdContacto
        }
        request.Post(`/chats/crear`, nuevoChat, r => {
          if (!r.Error) {
            Notify.create('Nuevo chat con mediador creado!')
            const i = this.contactos.findIndex(c => c.IdContacto === item.IdContacto)
            this.contactos[i].IdChat = r.IdChat
            const routeData = this.$router.resolve({
              name: 'Chat',
              query: {
                id: r.IdChat,
                caratula: item.Contacto,
                telefono: item.Telefono,
                idMediacion: item.IdContacto
              }
            })
            window.open(routeData.href, '_blank')
          } else {
            Notify.create('Falló al iniciar un nuevo chat. Razon: ' + r.Error)
          }
        })
      }
    },
    abrirChatContacto (item) {
      console.log(item)
      if (item.IdChat) {
        const routeData = this.$router.resolve({
          name: 'Chat',
          query: {
            id: item.IdChat,
            caratula: item.Contacto,
            telefono: item.Telefono,
            idContacto: item.IdContacto
          }
        })
        window.open(routeData.href, '_blank')
      } else {
        const nuevoChat = {
          Telefono: item.Telefono,
          IdContacto: item.IdContacto
        }
        request.Post(`/chats/crear`, nuevoChat, r => {
          if (!r.Error) {
            Notify.create('Nuevo chat con contacto creado!')
            const i = this.contactos.findIndex(c => c.IdContacto === item.IdContacto)
            this.contactos[i].IdChat = r.IdChat
            const routeData = this.$router.resolve({
              name: 'Chat',
              query: {
                id: r.IdChat,
                caratula: item.Contacto,
                telefono: item.Telefono,
                idContacto: item.IdContacto
              }
            })
            window.open(routeData.href, '_blank')
          } else {
            Notify.create('Falló al iniciar un nuevo chat. Razon: ' + r.Error)
          }
        })
      }
    },
    tipoContacto (t) {
      switch (t) {
        case 'A':
          return 'Abogado'

        case 'M':
          return 'Medico'

        case 'P':
          return 'Proveedor'

        default:
          return 'Otros'
      }
    }
  }
}
</script>
