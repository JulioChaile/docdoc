<template>
    <q-page class="q-px-lg">
        <div class="row q-mt-sm">
            <q-input
                v-model="busqueda"
                dense
                label="Buscar por Nombre o DNI"
                style="padding: 0 1rem 1rem 1rem; width: 500px"
            />
            <q-btn
                @click="buscar()"
                color="primary"
                style="padding-top:0px; margin-left: 20px;"
                dense
            >Buscar</q-btn>

            <q-toggle v-model="ordenarPorFecha" label="Ordenar por Cumpleaños / Fecha mas Proxima" color="primary" />

            <q-toggle
                v-model="mostrarNullPrimero"
                label="Mostrar Sin Fecha Primero"
                color="primary"
            />
        </div>

        <Loading v-if="loading" />

        <q-table
            v-else
            flat
            :data="personas"
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
                    </q-td>
                </q-tr>
            </template>
        </q-table>

        <!-- Modal para editar persona -->
        <q-dialog v-model="ModalEditar" style="padding:2rem;">
            <q-card>
                <div class="form__container" style="padding:1rem 2rem 0 2rem;">
                    <q-input
                        v-model="PersonaSeleccionada.FechaNacimiento"
                        ref="inputFechaNacimiento"
                        label="Fecha de Nacimiento"
                        mask="####-##-##"
                        :rules="[v => /^[\d]{4}-[0-1]\d-[0-3]\d$/.test(v) || 'Fecha invalida']"
                    >
                        <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                            <q-popup-proxy
                                ref="qDateProxy1"
                                transition-show="scale"
                                transition-hide="scale"
                            >
                                <q-date
                                v-model="PersonaSeleccionada.FechaNacimiento"
                                mask="YYYY-MM-DD"
                                label="Fecha de Nacimiento"
                                @input="() => $refs.qDateProxy1.hide()"
                                />
                            </q-popup-proxy>
                        </q-icon>
                        </template>
                    </q-input>
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
        ordenarPorFecha: false,
      busqueda: '',
      loading: false,
      mostrarNullPrimero: false,
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
          name: 'FechaNacimiento',
          label: 'Fecha Nacimiento',
          field: 'FechaNacimiento',
          align: 'left'
        },
      ],
      ModalEditar: false,
      ModalBorrar: false,
      PersonaSeleccionada: {},
      loadingCasos: false,
      Casos: []
    }
  },
  created () {
    this.buscar()
  },
  computed: {
    personas () {
        function compararFechas(a, b) {
            const fechaA = new Date(a.FechaNacimiento);
            const fechaB = new Date(b.FechaNacimiento);
            
            if (fechaA.getMonth() < fechaB.getMonth()) {
                return -1;
            } else if (fechaA.getMonth() > fechaB.getMonth()) {
                return 1;
            } else {
                if (fechaA.getDate() < fechaB.getDate()) {
                return -1;
                } else if (fechaA.getDate() > fechaB.getDate()) {
                return 1;
                } else {
                return 0;
                }
            }
        }

        function compararFechasProximas(a, b) {
            const fechaActual = new Date();
            const fechaA = new Date(a.FechaNacimiento);
            const fechaB = new Date(b.FechaNacimiento);
            
            const diferenciaA = Math.abs(fechaActual - fechaA);
            const diferenciaB = Math.abs(fechaActual - fechaB);
            
            if (diferenciaA < diferenciaB) {
                return -1; // Si fechaA es más cercana a la fecha actual que fechaB
            } else if (diferenciaA > diferenciaB) {
                return 1; // Si fechaA es más lejana a la fecha actual que fechaB
            } else {
                return 0; // Si ambas fechas están a la misma distancia de la fecha actual
            }
        }

        const personasConFecha = this.Personas.filter(persona => persona.FechaNacimiento !== null);
        const personasNull = this.Personas.filter(
            (persona) => persona.FechaNacimiento === null
        );
        
        if (this.ordenarPorFecha) {
            personasConFecha.sort(compararFechasProximas);
        } else {
            personasConFecha.sort(compararFechas);
        }

        let personas;
        if (this.mostrarNullPrimero) {
            personas = personasNull.concat(personasConFecha);
        } else {
            personas = personasConFecha.concat(personasNull);
        }

        return personas
    }
  },
  methods: {
    buscar () {
        this.loading = true

        request.Get('/personas/buscar', { Cadena: this.busqueda }, r => {
            this.Personas = r
            this.loading = false
        })
    },
    habilitarAccion (p, accion) {
        this.PersonaSeleccionada = p
        this['Modal' + accion] = true
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