<template>
  <div class="relative-position">
    <!--q-table
      flat
      class="bg-transparent"
      title="Personas del caso"
      :data="personas"
      :columns="columnas"
      row-key="apellido"
      v-if="render"
    >
      <template v-slot:top-right>
        <q-btn color="primary" round size="xs" @click="modalAgregar = true">
          +
          <q-tooltip>
            Agregar Persona
          </q-tooltip>
        </q-btn>
      </template>
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td key="apellido" :props="props">{{ props.row.Apellido ? props.row.Apellido : ' - ' }}</q-td>
          <q-td key="nombre" :props="props">{{ props.row.Nombre ? props.row.Nombre : ' - ' }}</q-td>
          <q-td key="documento" :props="props">{{ props.row.Documento ? props.row.Documento : '-' }}</q-td>
          <q-td key="email" :props="props">{{ props.row.Email ? props.row.Email : 'Sin email' }}</q-td>
          <q-td
            key="domicilio"
            :props="props"
          >{{ props.row.Domicilio ? props.row.Domicilio : 'Sin domicilio' }}</q-td>
          <q-td key="rol" :props="props">{{ props.row.Rol ? props.row.Rol : 'Sin rol' }}</q-td>
          <q-td key="telefono" :props="props">
            <span>{{ props.row.TelefonoActivo ? props.row.TelefonoActivo : ' - ' }}</span>
            <q-icon
              right
              name="fas fa-eye"
              color="blue"
              style="cursor:pointer;"
              @click="abrirModalTelefonos(props.row.Id)"
            >
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                <span class="text-body2">Ver todos los teléfonos</span>
              </q-tooltip>
            </q-icon>
          </q-td>
          <q-td key="principal" :props="props">
            <q-icon
              :name="props.row.Principal ? 'fas fa-check' : 'fas fa-times'"
              :color="props.row.Principal ? 'green' : 'red'"
            ></q-icon>
          </q-td>
          <q-td key="editar" :props="props">
            <q-icon
              name="edit"
              size="xs"
              color="primary"
              style="cursor:pointer;"
              @click="abrirModalEditar(props.row.Id)"
            >
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                <span class="text-body2">Editar datos de la persona</span>
              </q-tooltip>
            </q-icon>
            <q-icon
              name="delete"
              size="xs"
              color="negative"
              style="cursor:pointer; margin-left: 10px"
              @click="abrirModalEliminar(props.row.Id)"
            >
              <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
                <span class="text-body2">Eliminar persona</span>
              </q-tooltip>
            </q-icon>
          </q-td>
        </q-tr>
      </template>
    </q-table-->

    <q-btn
      class="absolute"
      style="right: 0; top: -20px"
      color="primary"
      round
      size="sm"
      @click="modalAgregar = true"
    >
      +
      <q-tooltip>
        Agregar Persona
      </q-tooltip>
    </q-btn>

    <div
      v-for="p in personas"
      :key="p.Id"
      class="row full-width q-py-md avenir-next text-grey-7"
    >
      <q-icon
        class="q-mr-sm"
        :name="p.Principal ? 'r_check_circle' : 'r_cancel'"
        size="sm"
        :color="p.Principal ? 'green' : 'red'"
        :style="p.Principal ? '' : 'filter: opacity(25%)'"
      />
      <div class="col-1 q-mx-sm text-center">
        <span>
        {{ p.Rol ? p.Rol.slice(0, 1) : 'Sin rol' }}
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
            {{ p.Rol || 'Sin rol' }}
          </q-tooltip>
        </span>
      </div>
      <div class="col-3">
        <span>
          {{ p.Apellido ? p.Apellido + ', ' : '' }}{{ p.Nombre || '···' }}
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
            Persona
          </q-tooltip>
        </span>
      </div>
      <div class="col-5 col-md-3">
        <q-icon
          v-if="!p.CiaSeguro"
          right
          name="fas fa-eye"
          color="blue"
          class="cursor-pointer q-mr-sm"
          @click="abrirModalTelefonos(p.Id)"
        >
          <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
            <span class="text-body2">Ver todos los teléfonos</span>
          </q-tooltip>
        </q-icon>
        <span>
          {{ p.TelefonoActivo || '···' }}
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
            Telefono Principal
          </q-tooltip>
        </span>
      </div>
      <div class="col-11 col-md-3">
        <span>
          {{ p.Domicilio || '···' }}
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
            Domicilio
          </q-tooltip>
        </span>
      </div>
      <q-icon
        v-if="!p.CiaSeguro"
        class="cursor-pointer"
        color="dark"
        name="more_vert"
        size="20px"
      >
        <q-menu>
          <q-list style="min-width: 100px">
            <q-item
              clickable
              v-close-popup
              @click="personaModal = true; personaAux = p"
            >
              <q-item-section>Más datos</q-item-section>
            </q-item>
            <q-separator />
            <q-item
              clickable
              v-close-popup
              @click="abrirModalEditar(p.Id)"
            >
              <q-item-section>Editar</q-item-section>
            </q-item>
            <q-separator />
            <q-item
              clickable
              v-close-popup
              @click="abrirModalEliminar(p.Id)"
            >
              <q-item-section>Eliminar</q-item-section>
            </q-item>
          </q-list>
        </q-menu>

        <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 0]">
          Opciones
        </q-tooltip>
      </q-icon>
    </div>

    <q-dialog
      v-model="personaModal"
    >
      <div class="filas__container q-banner">
        <div class="row filas_ relative-position">
          <div
            class="q-py-sm column full-width items-center"
          >
            <span
              class="q-subheading"
              style="color: #1B43F0"
            >
              {{ personaAux.Apellido ? personaAux.Apellido + ', ' : '' }}{{ personaAux.Nombre || '···' }}
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
                Persona
              </q-tooltip>
            </span>
            <span
              v-if="personaAux.Documento"
              class="text-overline text-grey-14"
            >
              {{ personaAux.Documento }}
            </span>
          </div>
          <q-separator />
          <div class="full-width row">
            <div class="col-6 column text-start q-px-md q-py-sm">
              <span
                class="text-caption text-grey-14"
              >
                Domicilio
              </span>
              <span
                class="text-subtitle2"
              >
                {{ personaAux.Domicilio || "···" }}
              </span>
            </div>
            <div class="col-6 column text-start q-px-md q-py-sm">
              <span
                class="text-caption text-grey-14"
              >
                Telefono
              </span>
              <span
                class="text-subtitle2"
              >
                {{ personaAux.TelefonoActivo || '···' }}
              </span>
            </div>
            <div class="col-6 column text-start q-px-md q-py-xs">
              <span
                class="text-caption text-grey-14"
              >
                Email
              </span>
              <span
                class="text-subtitle2"
              >
                {{ personaAux.Email || '···' }}
              </span>
            </div>
            <div class="col-6 column text-start q-px-md q-py-xs">
              <span
                class="text-caption text-grey-14 row"
              >
                Tipo
              </span>
              <span
                class="text-subtitle2"
              >
                {{ personaAux.Tipo === 'F' ? 'Física' : 'Jurídica' }}
              </span>
            </div>
            <q-separator />
            <div
              class="q-subheading text-center text-grey-9 q-py-sm full-width"
            >
              {{ personaAux.Rol || 'Sin rol' }}
            </div>
          </div>
        </div>
      </div>
    </q-dialog>

    <!-- Modal de telefonos -->
    <q-dialog
      v-model="modalTelefonos"
      transition-show="rotate"
      transition-hide="rotate"
      @before-hide="cerroModalTelefonos()"
      @escape-key="cerroModalTelefonos()"
    >
      <q-card>
        <q-card-section>
          <q-table
            flat
            :title="'Teléfonos de: ' + tituloModal"
            :data="telefonosModal"
            :columns="columnasTelefonos"
            row-key="numero"
            binary-state-sort
          >
            <template v-slot:body="props">
              <q-tr :props="props">
                <q-td key="telefono" :props="props">
                  {{ props.row.Telefono }}
                  <q-popup-edit v-model="props.row.Telefono">
                    <q-input v-model="props.row.Telefono" dense autofocus counter />
                  </q-popup-edit>
                </q-td>
                <q-td key="detalle" :props="props">
                  {{ props.row.Detalle }}
                  <q-popup-edit v-model="props.row.Detalle">
                    <q-input v-model="props.row.Detalle" dense autofocus counter />
                  </q-popup-edit>
                </q-td>
                <q-td key="fecha" :props="props">{{ props.row.FechaAlta ? props.row.FechaAlta.slice(0, 10) : '' }}</q-td>
                <q-td key="principal" :props="props">
                  <q-checkbox
                    v-model="props.row.EsPrincipal"
                    color="accent"
                    keep-color
                    @input="changeTelefonoPrincipal(props.row.Telefono, props.row.Esprincipal)"
                  />
                </q-td>
                <q-td key="eliminar">
                  <q-icon
                    name="fas fa-times"
                    left
                    style="cursor:pointer;"
                    color="red"
                    @click="preguntaSeguridadModal(props.row.Telefono)"
                  >
                    <q-tooltip>Eliminar teléfono</q-tooltip>
                  </q-icon>
                </q-td>
              </q-tr>
            </template>
          </q-table>
        </q-card-section>
        <q-card-section>
          <q-btn flat color="primary" class="text-capitalize" @click="agregarTelModal = true">
            <q-icon name="fas fa-plus" size="13px" left />Agregar Telefono
          </q-btn>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Pregunta de seguridad para eliminar un telefono -->
    <q-dialog v-model="preguntaSeguridad">
      <q-card>
        <q-card-section style="display:flex; justify-content:center;">
          <q-icon name="fas fa-exclamation-circle" size="33px" color="red" center />
        </q-card-section>
        <q-card-section>
          <span>
            ¿Está seguro que desea eliminar el teléfono
            <span
              class="text-weight-bold"
            >{{ numeroTelEliminar }}</span>?
          </span>
        </q-card-section>
        <q-card-actions style="display:flex; justify-content:center;">
          <q-btn flat class="text-capitalize" @click="cancelarEliminar()">Cancelar</q-btn>
          <q-btn dark color="red" class="text-capitalize" @click="eliminarTelefono()">Si, eliminar</q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Modal para agregar un nuevo teléfono -->
    <q-dialog v-model="agregarTelModal" position="right">
      <q-card class="modal_nuevo_tel">
        <q-card-section style="display:flex; justify-content:center;">
          <span class="text-h5">Nuevo teléfono</span>
        </q-card-section>
        <q-card-section class="input_container">
          <q-input class="q-py-sm" v-model="nuevoTelefono.numero" label="Número" />
          <q-input class="q-py-sm" v-model="nuevoTelefono.detalle" label="Detalle" />
          <q-checkbox class="q-py-sm" v-model="nuevoTelefono.activo" label="Es activo?" />
        </q-card-section>
        <q-card-section style="display:flex; justify-content:center;">
          <q-btn color="primary" class="text-capitalize" @click="agregarTelefono()">Guardar</q-btn>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Modal para editar persona -->
    <q-dialog v-model="modalEditar" style="padding:2rem;">
      <q-card>
        <div class="form__container" style="padding:1rem 2rem 0 2rem;">
          <q-input
            v-if="datosEditar.Tipo === 'F'"
            class="input"
            type="number"
            v-model="datosEditar.Documento"
            label="Documento"
          />
          <q-btn
            v-if="datosEditar.Tipo === 'F' && datosEditar.Documento.toString().length === 8"
            style="margin-top: 10px"
            color="accent"
            @click="llenarPersona(true)"
          >
            Traer datos
          </q-btn>
          <q-input
            class="input"
            type="text"
            v-model="datosEditar.Nombres"
            v-bind:label="datosEditar.Tipo === 'F' ? 'Nombres/s' : 'Razón Social'"
          />
          <q-input
            class="input"
            type="text"
            v-model="datosEditar.Apellidos"
            label="Apellidos/s"
            v-if="datosEditar.Tipo === 'F'"
          />
          <q-input
            class="input"
            type="email"
            v-model="datosEditar.Email"
            label="Email"
          />
          <q-input
            class="input"
            type="tel"
            v-model="datosEditar.Cuit"
            label="CUIT"
          />
          <!--q-input
            class="input"
            type="tel"
            v-model="datosEditar.Telefono"
            label="Teléfono"
          /-->
          <q-input class="input" v-model="datosEditar.Domicilio" label="Domicilio" />
          <q-checkbox v-model="datosEditar.Principal" label="¿Es principal?" />
        </div>
        <div style="display: flex;justify-content: flex-end; margin-botton:2rem;">
          <q-btn
            flat
            style="margin-right: 15px; margin-top: 30px; margin-bottom:2rem;"
            color="primary"
            @click="modalEditar = false"
          >Cancelar</q-btn>
          <q-btn
            style="margin-top: 30px; margin-right:2rem; margin-bottom:2rem;"
            color="accent"
            @click="editarPersona(datosEditar)"
          >Listo</q-btn>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal agregar persona -->
    <q-dialog v-model="modalAgregar" style="padding:2rem;">
      <q-card>
        <q-toggle
          style="margin-top: 30px"
          v-model="personaNueva.EsJuridica"
          label="Persona Jurídica"
          color="primary"
          true-value="S"
          false-value="N"
        />
        <div>
          <q-radio v-model="personaNueva.Observaciones" val="Actor" label="Actor" />
          <q-radio v-model="personaNueva.Observaciones" val="Demandado" label="Demandado" />
        </div>
        <div style="padding:1rem 2rem 0 2rem;">
          <q-input class="input" type="number" v-model.number="personaNueva.Documento" label="Documento" v-if="personaNueva.EsJuridica === 'N'" />
          <q-btn
            v-if="personaNueva.EsJuridica === 'N' && personaNueva.Documento.toString().length === 8"
            style="margin-top: 10px"
            color="accent"
            @click="llenarPersona(false)"
          >
            Traer datos
          </q-btn>
          <q-input v-if="personaNueva.EsJuridica === 'N'" class="input" type="text" v-model="personaNueva.Nombres" label="Nombres"/>
          <q-select
            v-else
            :value="model"
            use-input
            hide-selected
            fill-input
            @filter="filterFn"
            @input-value="setModel"
            @input="setPersona"
            label="Razón Social"
            :options="opcionesPersonas"
          />
          <q-input class="input" type="text" v-model="personaNueva.Apellidos" label="Apellidos/s" v-if="personaNueva.EsJuridica === 'N'" />
          <q-input class="input" type="email" v-model="personaNueva.Email" label="Email" />
          <q-input class="input" type="tel" v-model="personaNueva.CUIT" label="CUIT" />
          <!-- div style="display: flex; align-items:center;" v-for="t in Telefonos" :key="t.id" >
            <q-input class="input" type="tel" v-model="t.tel" label="Teléfono" />
            <q-input class="input" type="text" v-model="t.Detalle" label="Detalle" />
            <q-checkbox
              v-if="Telefonos.length > 1"
              v-model="t.check"
              label="Principal"
              @input="telefonoPrincipal(t.id)"
              style="margin-left: 10px"
            />
            <q-btn color="primary" round style="max-heigth:1rem; margin-left:2rem;" @click="agregarTelefono" v-if="t.id === Telefonos.length - 1">
              +
              <q-tooltip>
                Agregar Teléfono
              </q-tooltip>
            </q-btn>
            <q-btn color="red" round style="max-heigth:1rem; margin-left:1rem;" @click="quitarTelefono" v-if="t.id === Telefonos.length - 1 && Telefonos.length > 1">
              -
              <q-tooltip>
                Quitar Teléfono
              </q-tooltip>
            </q-btn>
          </div-->
          <q-input class="input" v-model="personaNueva.Domicilio" label="Domicilio" />
        </div>
        <div style="display: flex;justify-content: flex-end; margin-botton:2rem;">
          <q-btn
            flat
            style="margin-right: 15px; margin-top: 30px; margin-bottom:2rem;"
            color="primary"
            @click="cancelarAgregar()"
          >Cancelar</q-btn>
          <q-btn
            style="margin-top: 30px; margin-right:2rem; margin-bottom:2rem;"
            color="accent"
            @click="agregarPersona()"
          >Listo</q-btn>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal Eliminar Persona -->
    <q-dialog
      v-model="modalEliminar"
      prevent-close
    >
      <q-card style="padding:1rem;">
        <div style="text-align: center;">
          <span>
            <p>
              ¿Esta seguro que desea eliminar esta persona?
              <br>
              Esta accion es permanente.
            </p>
          </span>
          <div style="display: flex;justify-content: center; margin-botton:2rem;">
            <q-btn
              color="negative"
              label="Eliminar"
              @click="eliminarPersona()"
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
import request from '../../request'
import auth from '../../auth'
import { Notify, QRadio } from 'quasar'
export default {
  name: 'Personas',
  props: {
    personas: {
      type: Array,
      default: () => []
    }
  },
  components: { QRadio },
  data () {
    return {
      render: true,
      personaAux: {},
      personaModal: false,
      modalTelefonos: false,
      tituloModal: '',
      telefonosModal: [],
      telefonosEditados: [],
      backupTelefonos: [],
      numeroTelEliminar: '',
      huboCambioTelefonos: false,
      preguntaSeguridad: false,
      agregarTelModal: false,
      nuevoTelefono: {
        numero: '',
        detalle: '',
        activo: false
      },
      guardarButton: false,
      idPersonaModal: '',
      datosEditar: {
        Nombres: '',
        Apellidos: '',
        Documento: '',
        EsJuridica: 'N',
        Domicilio: '',
        Cuit: ''
      },
      datosAgregar: {},
      modalEditar: false,
      modalEliminar: false,
      modalAgregar: false,
      model: null,
      personaNueva: {
        Nombres: '',
        Apellidos: '',
        CUIT: '',
        Email: '',
        Documento: '',
        EsJuridica: 'N',
        Domicilio: '',
        Observaciones: 'Actor'
      },
      opcionesPersonas: [],
      // Columnas para la tabla de la vista
      columnas: [
        {
          name: 'apellido',
          required: true,
          label: 'Apellido',
          align: 'left',
          field: (row) => row.Apellido,
          format: (val) => `${val}`,
          sortable: true
        },
        {
          name: 'nombre',
          align: 'left',
          label: 'Nombre',
          field: 'Nombre',
          sortable: true
        },
        {
          name: 'documento',
          align: 'left',
          label: 'Documento',
          field: 'Documento'
        },
        {
          name: 'email',
          align: 'left',
          label: 'Email',
          field: 'Email',
          sortable: true
        },
        {
          name: 'domicilio',
          align: 'left',
          label: 'Domicilio',
          field: 'Domicilio'
        },
        {
          name: 'rol',
          align: 'left',
          label: 'Rol',
          field: 'Rol'
        },
        {
          name: 'telefono',
          align: 'left',
          label: 'Telefono activo',
          field: 'TelefonoActivo'
        },
        {
          name: 'principal',
          align: 'center',
          label: 'Principal',
          field: 'Principal'
        },
        {
          name: 'editar',
          align: 'center',
          label: 'Editar / Eliminar',
          field: 'Editar / Eliminar'
        }
      ],
      // Columnas para el modal de telefonos
      columnasTelefonos: [
        {
          name: 'telefono',
          required: true,
          label: 'Número',
          align: 'left',
          field: (row) => row.Telefono,
          format: (val) => `${val}`
        },
        {
          name: 'detalle',
          label: 'Detalle',
          align: 'left',
          field: 'Detalle'
        },
        {
          name: 'fecha',
          align: 'center',
          label: 'Fecha de alta',
          field: 'FechaAlta'
        },
        {
          name: 'principal',
          align: 'center',
          label: 'Activo',
          field: 'EsPrincipal'
        },
        {
          name: 'eliminar',
          align: 'left',
          label: ''
        }
      ]
    }
  },
  methods: {
    abrirModalTelefonos (id) {
      this.telefonosModal = (this.personas || []).filter(
        (persona) => persona.Id === id
      )[0].Telefonos

      this.telefonosModal.forEach((telefono) => {
        let telBackup = {}
        Object.assign(telBackup, telefono)
        this.backupTelefonos.push(telBackup)
      })

      this.tituloModal =
        (this.personas || []).filter((persona) => persona.Id === id)[0]
          .Apellido +
        ', ' +
        (this.personas || []).filter((persona) => persona.Id === id)[0].Nombre

      this.idPersonaModal = id
      this.modalTelefonos = true
    },
    abrirModalEliminar (id) {
      const i = this.personas.findIndex(p => p.Id === id)

      if (this.personas[i].Principal) {
        Notify.create('No es posible eliminar a la persona principal del caso.')
      } else {
        this.modalEliminar = true
        this.IdPersonaEliminar = id
      }
    },
    eliminarPersona () {
      console.log(this.IdPersonaEliminar)
      request.Post(`/casos/${this.$route.query.id}/borrar-persona/${this.IdPersonaEliminar}`, {}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          const i = this.personas.findIndex(p => p.Id === this.IdPersonaEliminar)
          this.personas.splice(i, 1)

          this.modalEliminar = false
          this.IdPersonaEliminar = 0

          Notify.create('Se elimino a la persona con exito.')

          this.render = false

          this.$nextTick().then(() => {
            this.render = true
          })
        }
      })
    },
    abrirModalEditar (id) {
      const i = this.personas.findIndex(p => p.Id === id)

      this.datosEditar = {
        Id: this.personas[i].Id,
        Apellidos: this.personas[i].Apellido,
        Nombres: this.personas[i].Nombre,
        Email: this.personas[i].Email,
        Documento: this.personas[i].Documento ? this.personas[i].Documento : '',
        Domicilio: this.personas[i].Domicilio,
        Tipo: this.personas[i].Tipo,
        Principal: this.personas[i].Principal
      }

      this.modalEditar = true
    },
    editarPersona (persona) {
      const idEstudio = auth.UsuarioLogueado.IdEstudio
      const idPersona = persona.Id
      const i = this.personas.findIndex(p => p.Id === idPersona)
      if (this.personas[i].Principal && !persona.Principal) {
        Notify.create('El caso debe tener una persona principal.')
        return
      }
      persona.Telefonos = this.personas[i].Telefonos
      request.Put(`/estudios/${idEstudio}/modificar-persona/${idPersona}`, {persona: persona, IdCaso: this.$route.query.id, EsPrincipal: persona.Principal ? 'S' : 'N'}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.personas.forEach(p => {
            if (p.Principal) { p.Principal = false }
          })

          const i = this.personas.findIndex(p => p.Id === idPersona)

          const p = {
            Id: persona.Id,
            Apellido: persona.Apellidos,
            Nombre: persona.Nombres,
            Email: persona.Email,
            Principal: persona.Principal,
            Domicilio: persona.Domicilio,
            Documento: persona.Documento,
            Tipo: persona.Tipo,
            Rol: this.personas[i].Rol,
            Telefonos: this.personas[i].Telefonos,
            TelefonoActivo: this.personas[i].TelefonoActivo
          }

          this.personas[i] = p

          Notify.create('Se editaron los datos con exito.')

          this.render = false

          this.$nextTick().then(() => {
            this.render = true
          })
        }
      })
      this.datosEditar = {}
      this.modalEditar = false
      this.$forceUpdate()
    },
    agregarTelefono () {
      this.agregarTelModal = false
      const data = {
        idPersona: this.idPersonaModal,
        NuevoTelefono: this.nuevoTelefono
      }
      this.$emit('agregarTelefono', data)
    },
    preguntaSeguridadModal (numtel) {
      this.numeroTelEliminar = numtel
      this.preguntaSeguridad = true
    },
    cancelarEliminar () {
      this.numeroTelEliminar = ''
      this.preguntaSeguridad = false
    },
    eliminarTelefono () {
      const data = {
        numeroTelEliminar: this.numeroTelEliminar,
        idPersona: this.idPersonaModal
      }
      this.telefonosModal.forEach((item, index) => {
        if (this.telefonosModal[index].Telefono === data.numeroTelEliminar) {
          this.telefonosModal.splice(index, 1)
          this.backupTelefonos.splice(index, 1)
        }
      })
      this.personas.forEach((persona) => {
        if (persona.Id === data.idPersona) {
          persona.Telefonos.forEach((item, index) => {
            if (item.Telefono === data.numeroTelEliminar) {
              persona.Telefonos.splice(index, 1)
            }
          })

          if (persona.TelefonoActivo === data.numeroTelEliminar) {
            persona.TelefonoActivo = 'Sin tel. activo'
          }
        }
      })
      this.$emit('eliminarTelefono', data)
      this.preguntaSeguridad = false
    },
    cerroModalTelefonos () {
      if (this.compararArraysTelefonos(this.backupTelefonos, this.telefonosModal)) {
        this.telefonosModal.forEach((telefono) => {
          let copia = {}
          Object.assign(copia, telefono)
          this.telefonosEditados.push(copia)
        })

        for (let index = 0; index < this.telefonosEditados.length; index++) {
          // eslint-disable-next-line standard/computed-property-even-spacing
          this.telefonosEditados[index].TelefonoOld = this.backupTelefonos[
            index
          ].Telefono
        }
        this.$emit(
          'updateTelefonos',
          this.telefonosEditados,
          this.idPersonaModal
        )
        this.telefonosModal = []
        this.backupTelefonos = []
      } else {
        this.telefonosModal = []
        this.backupTelefonos = []
      }
    },
    compararArraysTelefonos (a1, a2) {
      var result = false

      for (let index = 0; index < a1.length; index++) {
        if (
          a1[index].Detalle !== a2[index].Detalle ||
          a1[index].Telefono !== a2[index].Telefono ||
          a1[index].EsPrincipal !== a2[index].EsPrincipal
        ) {
          result = true
        }
      }
      return result
    },
    filterFn (val, update, abort) {
      val = this.accentFold(val)

      if (val.length < 3) {
        abort()
        return
      }

      request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/buscar-personas`, {Cadena: val, Tipo: 'J'}, r => {
        if (!r.Error) {
          update(() => {
            this.opcionesPersonas.splice(0)

            r.forEach(p => {
              this.opcionesPersonas.push({
                label: p.Nombres,
                value: p
              })
            })
          })
        }
      })
    },
    accentFold (s) {
      const accent = {'á': 'a', 'é': 'e', 'è': 'e', 'í': 'i', 'ó': 'o', 'ú': 'u', 'Á': 'a', 'É': 'e', 'Í': 'i', 'Ó': 'o', 'Ú': 'u'}

      if (!s) { return '' }
      var ret = ''
      for (var i = 0; i < s.length; i++) {
        ret += accent[s.charAt(i)] || s.charAt(i)
      }

      return ret
    },
    setModel (val) {
      this.model = val
      this.personaNueva.Nombres = val
    },
    setPersona (val) {
      const p = val.value

      this.personaNueva.Nombres = p.Nombres
      this.personaNueva.Email = p.Email
      this.personaNueva.CUIT = p.Cuit
      this.personaNueva.Telefonos = p.Telefonos
      this.personaNueva.Domicilio = p.Domicilio
    },
    cancelarAgregar () {
      this.personaNueva = {
        Nombres: '',
        Apellidos: '',
        Documento: '',
        EsJuridica: 'N',
        Domicilio: '',
        CUIT: '',
        Email: ''
      }
      this.modalAgregar = false
    },
    changeTelefonoPrincipal (tel, p) {
      const i = this.telefonosModal.findIndex(t => t.Telefono === tel)
      if (!p) {
        this.telefonosModal.forEach((t, j) => {
          if (parseInt(i) !== parseInt(j)) {
            t.EsPrincipal = false
          }
        })
      } else {
        this.$nextTick(() => {
          this.telefonosModal[i].EsPrincipal = true
        })
      }
    },
    agregarPersona () {
      const persona = [{
        Nombres: this.personaNueva.Nombres,
        Apellidos: this.personaNueva.Apellidos,
        CUIT: this.personaNueva.CUIT,
        Documento: this.personaNueva.Documento,
        Domicilio: this.personaNueva.Domicilio,
        Telefonos: [],
        Parametros: {},
        TipoPersona: this.personaNueva.EsJuridica === 'N' ? 'F' : 'J',
        EsPrincipal: 'N',
        Email: this.personaNueva.Email,
        Observaciones: this.personaNueva.Observaciones
      }]

      this.modalAgregar = false

      request.Post(`/casos/${this.$route.query.id}/alta-persona`, {Persona: JSON.stringify(persona)}, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.personas.push({
            Id: r.IdPersona,
            Apellido: persona[0].Apellidos,
            Nombre: persona[0].Nombres,
            Email: persona[0].Email,
            Principal: 'N',
            Domicilio: persona[0].Domicilio,
            Documento: persona[0].Documento,
            Tipo: persona[0].TipoPersona,
            Rol: persona[0].Observaciones,
            Telefonos: [],
            TelefonoActivo: ''
          })
          this.personaNueva = {
            Nombres: '',
            Apellidos: '',
            Documento: '',
            EsJuridica: 'N',
            Domicilio: '',
            CUIT: '',
            Email: ''
          }

          Notify.create('Se agrego la nueva persona con exito.')

          this.render = false

          this.$nextTick().then(() => {
            this.render = true
          })
        }
      })
    },
    llenarPersona (editar) {
      let documento = editar ? this.datosEditar.Documento : this.personaNueva.Documento
      if (documento) {
        if (documento.toString().length === 8) {
          request.Get('/personas/padron', {documento: documento}, r => {
            if (r.Error) {
              Notify.create(r.Error)
            } else {
              if (editar) {
                this.datosEditar.Nombres = r.Nombres
                this.datosEditar.Apellidos = r.Apellidos
                this.datosEditar.Domicilio = r.Domicilio
              } else {
                this.personaNueva.Nombres = r.Nombres
                this.personaNueva.Apellidos = r.Apellidos
                this.personaNueva.Domicilio = r.Domicilio
              }
            }
          })
        } else {
          this.$q.notify({
            color: 'primary',
            timeout: 800,
            message: 'El documento ingresado no es valido'
          })
          this.datosEditar.Nombres = ''
          this.personaNueva.Nombres = ''
        }
      }
    }
  }
}
</script>

<style scoped>
.modal_nuevo_tel {
  height: 60vh;
}
.input_container {
  padding: 2em;
  width: 100%;
}
</style>
