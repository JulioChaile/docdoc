<template>
  <div>
    <q-item style="background-color: #E8EAF6;">
      <q-item-section color="primary" icon="person">
      </q-item-section>
      <span class="q-subheading">Permisos del Caso</span>
    </q-item>
    <div style="display: flex">
      <q-field>
        <q-select
          filled
          v-model="usuario"
          label="Agregar usuario"
          :options="opciones"
          use-input
          clearable
          input-debounce="0"
          @filter="search"
          @input="selected"
        />
      </q-field>
      <q-btn icon="edit" size="sm" color="primary">
        <q-menu>
          <q-list separator link style="min-width: 180px">
            <q-item v-close-popup @click.native="permiso = 'A'">
              <q-item-section>Administrador</q-item-section>
              <div v-if="permiso === 'A'">
                <q-item-section avatar>
                  <q-avatar icon="check" />
                </q-item-section>
              </div>
            </q-item>
            <q-item v-close-popup @click.native="permiso = 'E'">
              <q-item-section>Edición</q-item-section>
              <q-item-section avatar v-if="permiso === 'E'">
                <q-avatar icon="check" />
              </q-item-section>
            </q-item>
            <q-item v-close-popup @click.native="permiso = 'L'">
              <q-item-section>Sólo Lectura</q-item-section>
              <q-item-section avatar v-if="permiso === 'L'">
                <q-avatar icon="check" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-menu>
      </q-btn>
    </div>
    <q-list highlight>
      <q-item-label header>Usuarios del Caso</q-item-label>
      <q-item
        v-for="usuario in UsuariosCaso" :key="usuario.IdUsuario"
      >
      <q-item-label>
        {{usuario.Apellidos}}, {{usuario.Nombres}}
      </q-item-label>
      <q-item-section right>
        <q-btn icon="edit" size="sm" color="primary" style="width: 30px; position: absolute; right: 10px">
          <q-menu>
            <q-list>
              <q-item v-close-popup @click.native="setPermiso('A', usuario)">
                <q-item-section>Administrador</q-item-section>
                <div v-if="usuario.Permiso === 'A'">
                  <q-item-section avatar>
                    <q-avatar icon="check" />
                  </q-item-section>
                </div>
              </q-item>
              <q-item v-close-popup @click.native="setPermiso('E', usuario)">
                <q-item-section>Edición</q-item-section>
                <q-item-section avatar v-if="usuario.Permiso === 'E'">
                  <q-avatar icon="check" />
                </q-item-section>
              </q-item>
              <q-item v-close-popup @click.native="setPermiso('L', usuario)">
                <q-item-section>Sólo Lectura</q-item-section>
                <q-item-section avatar v-if="usuario.Permiso === 'L'">
                  <q-avatar icon="check" />
                </q-item-section>
              </q-item>
              <q-item v-close-popup @click.native="eliminarUsuario(usuario)">
                <q-item-section color="red">Eliminar</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-item-section>
      </q-item>
      </q-list>
  </div>
</template>

<script>
import request from '../request'
import auth from '../auth'
export default {
  props: ['caso', 'value', 'idcaso'],
  data () {
    return {
      UsuariosEstudio: [],
      UsuariosCaso: [],
      IdResponsable: 0,
      IdEstudio: 0,
      usuario: '',
      permiso: 'L',
      opciones: []
    }
  },
  created () {
    if (typeof this.value === 'string') {
      this.UsuariosCaso = JSON.parse(this.value)
    } else {
      this.UsuariosCaso = this.value
    }
    this.IdResponsable = auth.UsuarioLogueado.IdUsuario
    this.IdEstudio = auth.UsuarioLogueado.IdEstudio
    request.Get(`/estudios/${this.IdEstudio}/usuarios`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.UsuariosEstudio = r
      }
    })
  },
  computed: {
    usuariosEstudioSearch () {
      const usuariosMap = {}
      this.UsuariosEstudio.forEach(u => {
        usuariosMap[`${u.Apellidos}, ${u.Nombres} (${u.Usuario})`] = u
      })
      return usuariosMap
    }
  },
  watch: {
    UsuariosCaso (val) {
      if (val) {
        this.$emit('input', val)
      }
    },
    UsuariosEstudio () {
      this.opciones = this.UsuariosEstudio.map(u => {
        return `${u.Apellidos}, ${u.Nombres} (${u.Usuario})`
      })
    }
  },
  methods: {
    search (val, update) {
      if (val === '') {
        update(() => {
          this.opciones = this.UsuariosEstudio.map(u => {
            return `${u.Apellidos}, ${u.Nombres} (${u.Usuario})`
          })
        })
        return
      }
      const usuarios = this.opciones.filter(usuario => {
        return usuario.toLowerCase().includes(val.toLowerCase())
      })
      update(() => {
        this.opciones = usuarios
      })
    },
    selected (item) {
      if (!this.usuariosEstudioSearch[item]) {
        return
      }
      const usuario = this.usuariosEstudioSearch[item]
      usuario.Permiso = this.permiso
      this.setPermiso(this.permiso, usuario)
    },
    setPermiso (permiso, usuario) {
      request.Post('/casos/usuarios', { Permiso: permiso, IdUsuario: usuario.IdUsuario, IdCaso: this.idcaso }, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          if (this.UsuariosCaso.filter(u => String(u.IdUsuario) === String(usuario.IdUsuario)).length) {
            this.UsuariosCaso.filter(u => String(u.IdUsuario) === String(usuario.IdUsuario)).forEach(u => {
              u.Permiso = permiso
            })
            return
          }
          const usuariocaso = { ...usuario, IdUsuarioCaso: r.IdUsuario }
          this.UsuariosCaso.push(usuariocaso)
          this.terms = ''
        }
      })
    },
    eliminarUsuario (usuario) {
      request.Post('/casos/eliminar-usuario', { IdUsuario: usuario.IdUsuario, IdCaso: this.idcaso }, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.UsuariosCaso.splice(this.UsuariosCaso.indexOf(usuario), 1)
        }
      })
    },
    verificarPermiso (permiso, usuario) {
      if (this.UsuariosCaso[this.UsuariosCaso.indexOf(usuario)].Permiso === permiso) {
        return true
      }
    }
  }
}
</script>

<style>

</style>
