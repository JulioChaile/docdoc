<template>
    <q-page class="q-px-lg">
        <div class="row q-mt-sm">
            <q-input
                v-model="busqueda"
                dense
                label="Buscar por Nombre, DNI o CUIT"
                style="padding: 0 1rem 1rem 1rem; width: 500px"
            />
            <q-btn
                @click="buscar()"
                color="primary"
                style="padding-top:0px; margin-left: 20px;"
                dense
            >Buscar</q-btn>
        </div>

        <Loading v-if="loading" />

        <q-table
            v-else
            flat
            :data="Personas"
            :columns="columnas"
            row-key="name"
        >
            <template v-slot:header="props">
                <q-tr :props="props">
                    <q-th
                        v-for="col in props.cols"
                        :key="col.name"
                        :props="props"
                    >
                        {{ col.label }}
                    </q-th>

                    <q-th>
                        Tipo
                    </q-th>

                    <q-th>
                        Acciones
                    </q-th>
                </q-tr>
            </template>

            <template v-slot:body="props">
                <q-tr :props="props" :key="props.row.IdPersona">
                    <q-td
                        v-for="col in props.cols"
                        :key="col.name"
                        :props="props"
                    >
                        {{ col.value }}
                    </q-td>

                    <q-td align="center">
                        {{ props.row.Tipo === 'F' ? 'Física' : 'Jurídica' }}
                    </q-td>

                    <q-td align="center" class="flex">
                        <q-icon
                            name="edit"
                            size="sm"
                            color="green"
                            style="cursor:pointer; margin: auto"
                            @click="habilitarAccion(props.row, 'Editar')"
                        >
                            <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                                <span class="text-body2">Editar</span>
                            </q-tooltip>
                        </q-icon>
                        <q-icon
                            name="delete"
                            size="sm"
                            color="red"
                            style="cursor:pointer; margin: auto"
                            @click="habilitarAccion(props.row, 'Borrar')"
                        >
                            <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                                <span class="text-body2">Borrar</span>
                            </q-tooltip>
                        </q-icon>
                    </q-td>
                </q-tr>
            </template>
        </q-table>

        <!-- Modal para editar persona -->
        <q-dialog v-model="ModalEditar" style="padding:2rem;">
            <q-card>
                <div class="form__container" style="padding:1rem 2rem 0 2rem;">
                    <q-input
                        v-if="PersonaSeleccionada.Tipo === 'F'"
                        class="input"
                        type="number"
                        v-model="PersonaSeleccionada.Documento"
                        label="Documento"
                    />
                    <q-btn
                        v-if="PersonaSeleccionada.Tipo === 'F' && PersonaSeleccionada.Documento.toString().length >= 8"
                        style="margin-top: 10px"
                        color="accent"
                        @click="llenarPersona(true)"
                    >
                        Traer datos
                    </q-btn>
                    <q-input
                        class="input"
                        type="text"
                        v-model="PersonaSeleccionada.Nombres"
                        v-bind:label="PersonaSeleccionada.Tipo === 'F' ? 'Nombres/s' : 'Razón Social'"
                    />
                    <q-input
                        class="input"
                        type="text"
                        v-model="PersonaSeleccionada.Apellidos"
                        label="Apellidos/s"
                        v-if="PersonaSeleccionada.Tipo === 'F'"
                    />
                    <q-input
                        class="input"
                        type="email"
                        v-model="PersonaSeleccionada.Email"
                        label="Email"
                    />
                    <q-input
                        class="input"
                        type="tel"
                        v-model="PersonaSeleccionada.Cuit"
                        label="CUIT"
                    />
                    <!--q-input
                        class="input"
                        type="tel"
                        v-model="datosEditar.Telefono"
                        label="Teléfono"
                    /-->
                    <q-input class="input" v-model="PersonaSeleccionada.Domicilio" label="Domicilio" />
                </div>
                <div style="display: flex;justify-content: flex-end; margin-botton:2rem;">
                    <q-btn
                        flat
                        style="margin-right: 15px; margin-top: 30px; margin-bottom:2rem;"
                        color="primary"
                        @click="ModalEditar = false"
                    >Cancelar</q-btn>
                    <q-btn
                        style="margin-top: 30px; margin-right:2rem; margin-bottom:2rem;"
                        color="accent"
                        @click="editarPersona(PersonaSeleccionada)"
                    >Listo</q-btn>
                </div>
            </q-card>
        </q-dialog>

        <q-dialog v-model="ModalBorrar" style="padding:2rem; max-width: 3000px;">
            <q-card style="padding:2rem; max-width: 3000px;">
                <Loading v-if="loadingCasos" />

                <div v-else-if="Casos.length > 0" class="text-center">
                    La persona se encuentra en los siguientes casos, debe eliminarla del caso para poder eliminar la persona del estudio.

                    <ul>
                        <li v-for="c in Casos" style="cursor:pointer" @click="abrirCaso(c.IdCaso)">
                            {{ c.Caratula }}
                        </li>
                    </ul>
                </div>

                <div v-else class="text-center">
                    ¿Esta seguro que desea eliminar esta persona?
                    <br>

                    <div style="display: flex;justify-content: flex-end; margin-botton:2rem;">
                        <q-btn
                            flat
                            style="margin-right: 15px; margin-top: 30px; margin-bottom:2rem;"
                            color="primary"
                            @click="ModalBorrar = false"
                        >Cancelar</q-btn>
                        <q-btn
                            style="margin-top: 30px; margin-right:2rem; margin-bottom:2rem;"
                            color="accent"
                            @click="borrarPersona(PersonaSeleccionada.IdPersona)"
                        >Eliminar</q-btn>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    </q-page>
</template>

<script>
import { Notify } from 'quasar'
import request from '../request'
import auth from '../auth'
import Loading from '../components/Loading'

export default {
    components: { Loading },
  data () {
    return {
        Personas: [],
      busqueda: '',
      loading: false,
      columnas: [
        {
          name: 'id',
          label: 'Id',
          field: 'IdPersona',
          align: 'left'
        },
        {
          name: 'Apellidos',
          label: 'Apellidos',
          field: 'Apellidos',
          align: 'left'
        },
        {
          name: 'Nombres',
          label: 'Nombres',
          field: 'Nombres',
          align: 'left'
        },
        {
          name: 'Documento',
          label: 'Documento',
          field: 'Documento',
          align: 'left'
        },
        {
          name: 'Cuit',
          label: 'Cuit',
          field: 'Cuit',
          align: 'left'
        },
        {
          name: 'Domicilio',
          label: 'Domicilio',
          field: 'Domicilio',
          align: 'left'
        }
      ],
      ModalEditar: false,
      ModalBorrar: false,
      PersonaSeleccionada: {},
      loadingCasos: false,
      Casos: []
    }
  },
  methods: {
    buscar () {
        if (!this.busqueda) {
            Notify.create('El campo de busqueda no puede estar vacio')
            return
        }

        this.loading = true

        request.Get('/personas/buscar', { Cadena: this.busqueda }, r => {
            this.Personas = r
            this.loading = false
        })
    },
    habilitarAccion (p, accion) {
        this.PersonaSeleccionada = p
        this['Modal' + accion] = true

        if (accion === 'Borrar') {
            this.loadingCasos = true
            request.Get(`/personas/casos/`, {IdPersona: p.IdPersona}, r => {
                this.loadingCasos = false
                if (r.Error) {
                    Notify.create(r.Error)
                } else {
                    this.Casos = r
                }
            })
        }
    },
    llenarPersona () {
      let documento = this.PersonaSeleccionada.Documento
      if (documento) {
        if (documento.toString().length === 8) {
          request.Get('/personas/padron', {documento: documento}, r => {
            if (r.Error) {
              Notify.create(r.Error)
            } else {
                this.PersonaSeleccionada.Nombres = r.Nombres
                this.PersonaSeleccionada.Apellidos = r.Apellidos
                this.PersonaSeleccionada.Domicilio = r.Domicilio
            }
          })
        } else {
          this.$q.notify({
            color: 'primary',
            timeout: 800,
            message: 'El documento ingresado no es valido'
          })
        }
      }
    },
    editarPersona (persona) {
      const idEstudio = auth.UsuarioLogueado.IdEstudio
      const idPersona = persona.IdPersona
      const i = this.Personas.findIndex(p => p.IdPersona === idPersona)
      request.Put(`/estudios/${idEstudio}/modificar-persona/${idPersona}`, {persona: persona, IdCaso: null}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.Personas[i] = this.PersonaSeleccionada

          Notify.create('Se editaron los datos con exito.')
        }
      })
      this.ModalEditar = false
      this.$forceUpdate()
    },
    abrirCaso (id) {
      let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
      window.open(routeData.href, '_blank')
    },
    borrarPersona (id) {
        const i = this.Personas.findIndex(p => p.IdPersona === id)
        request.Post(`/estudios/borrar-persona`, { IdPersona: id }, r => {
            if (r.Error) {
                Notify.create(r.Error)
            } else {
                this.Personas.splice(i, 1)

                Notify.create('Se borro la persona con exito')
            }
        })
    }
  }
}
</script>