<template>
  <q-page id="casos_pendientes_id">
    <q-drawer
      v-model="mostrar"
      :width="300"
      :breakpoint="700"
      overlay
      content-class="bg-grey-3 text-black "
      content-style="border-style: solid; border-width: 1px; height: auto !important"
      id="drawer_casos_pendientes"
    >
      <q-expansion-item
        expand-separator
        icon="assignment_late"
        label="Estados"
      >
        <div class="checks-container bg-white">
          <Loading v-if="Estados.length === 0" />
          <q-checkbox
            v-else
            class="full-width"
            v-for="e in Estados"
            :key="e.value"
            :label="`${e.label} (${CantEstados[e.value] || 0})`"
            v-model="e.check"
            @input="filtrar(e.check, e.value, 'Estados')"
          />
        </div>
      </q-expansion-item>

      <q-separator />

      <q-expansion-item
        expand-separator
        icon="list"
        label="Fecha de Carga"
        default-opened
      >
        <div class="checks-container bg-white">
          <Loading v-if="FechasAlta.length === 0" />
          <q-checkbox
            v-else
            class="full-width"
            v-for="f in FechasAlta"
            :key="f.value"
            :label="`${f.label} (${CantFechasAlta[f.value] || 0})`"
            v-model="f.check"
            @input="filtrar(f.check, f.value, 'FechasAlta')"
          />
        </div>
      </q-expansion-item>

      <q-separator />

      <q-expansion-item
        expand-separator
        icon="list"
        label="Fecha de Visita"
      >
        <div class="checks-container bg-white">
          <Loading v-if="FechasVisitado.length === 0" />
          <q-checkbox
            v-else
            class="full-width"
            v-for="f in FechasVisitado"
            :key="f.value"
            :label="`${f.label} (${CantFechasVisitado[f.value] || 0})`"
            v-model="f.check"
            @input="filtrar(f.check, f.value, 'FechasVisitado')"
          />
        </div>
      </q-expansion-item>

      <q-separator />

      <q-expansion-item
        expand-separator
        icon="delivery_dining"
        label="Cadetes"
      >
        <div class="checks-container bg-white">
          <Loading v-if="Cadetes.length === 0" />
          <q-option-group
            class="full-width"
            :options="Cadetes"
            type="radio"
            v-model="cadete"
            @input="buscarCaso(1)"
          />
        </div>
      </q-expansion-item>
      <q-separator />
    </q-drawer>

    <q-input
      outlined
      rounded
      bottom-slots
      class="busqueda-input busqueda-input-caso-pendiente shadow-3"
      label-color="grey-2"
      color="grey-4"
      v-model="busqueda"
      :debounce="400"
      placeholder="Buscar"
      @change="buscarCaso(1)"
    >
      <template v-slot:hint>
        <div class="row inline items-center full-width q-mt-lg">
          <q-radio class="col-4" dense v-model="tipoBusqueda" val="nom" label="Nombre o Domicilio" />
          <q-radio class="col-4" dense v-model="tipoBusqueda" val="doc" label="Documento" />
          <q-radio class="col-4" dense v-model="tipoBusqueda" val="tel" label="Telefono" />
          <div class="col-12 flex justify-center items-center">
            <q-checkbox
              v-model="finalizados"
              label="Ver finalizados"
              @input="buscarCaso()"
            />
          </div>
        </div>
      </template>

      <template v-slot:prepend>
        <q-icon name="search" />
      </template>

      <template v-slot:append>
        <q-icon
          class="cursor-pointer"
          color="positive"
          name="add_circle_outline"
          size="sm"
          @click="modalAlta = true"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Agregar Caso</q-tooltip>
        </q-icon>
        <q-icon
          class="cursor-pointer"
          color="negative"
          name="explore"
          size="sm"
          @click="mapaCompleto()"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver mapa completo {{ finalizados ? 'de finalizados' : '' }}</q-tooltip>
        </q-icon>
        <q-icon
          class="cursor-pointer"
          color="grey"
          name="r_menu"
          size="sm"
          id="id_icon_filtro"
          @click="mostrar = !mostrar"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtros</q-tooltip>
        </q-icon>
      </template>
    </q-input>

    <div class="q-mt-xl" v-if="loading">
      <Loading />
    </div>
    <GrillaCasosPendientes
      v-else
      :Casos="Casos"
      :pag="pag"
      :maxpags="maxpags"
      :IdEstadoCasoPendiente="IdEstadoCasoPendiente"
      :opcionesEstados="opcionesEstados"
      :opcionesOrigenes="opcionesOrigenes"
      :opcionesLesiones="opcionesLesiones"
      @page="buscarCaso"
      @eliminar="buscarCaso()"
    />

    <!--Modal Alta Caso Pendiente -->
    <q-dialog v-model="modalAlta">
      <AltaCasoPendiente
        :opcionesEstados="opcionesEstados"
        :opcionesOrigenes="opcionesOrigenes"
        :opcionesLesiones="opcionesLesiones"
        @cerrar="buscarCaso()"
      />
    </q-dialog>
  </q-page>
</template>

<script>
import { QRadio } from 'quasar'
import request from '../request'
import auth from '../auth'
import AltaCasoPendiente from '../components/CasosPendientes/AltaCasoPendiente'
import GrillaCasosPendientes from '../components/CasosPendientes/GrillaCasosPendientes'
import Loading from '../components/Loading'

export default {
  components: {
    AltaCasoPendiente,
    GrillaCasosPendientes,
    Loading,
    QRadio
  },
  data () {
    return {
      mostrar: false,
      busqueda: '',
      Casos: [],
      pag: 1,
      maxpags: 5,
      loading: true,
      modalAlta: false,
      IdEstadoCasoPendiente: 0,
      opcionesOrigenes: [],
      opcionesEstados: [],
      opcionesLesiones: [],
      Estados: [],
      CantEstados: {},
      FiltroEstados: [],
      FechasAlta: [],
      CantFechasAlta: {},
      FiltroFechasAlta: [],
      FechasVisitado: [],
      CantFechasVisitado: {},
      FiltroFechasVisitado: [],
      Cadetes: [],
      primerGet: true,
      IdTimeout: null,
      cadete: 0,
      tipoBusqueda: 'nom',
      finalizados: false
    }
  },
  created () {
    request.Get('/origenes', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.opcionesOrigenes = r.map(o => {
          if (o.Origen !== 'TODOS') {
            return {
              label: o.Origen,
              value: parseInt(o.IdOrigen)
            }
          }
        })
      }
    })

    const IdEstudio = auth.UsuarioLogueado.IdEstudio
    request.Get(`/estudios/${IdEstudio}/usuarios`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.Cadetes = r
          .filter(u => u.Observaciones === 'cadete')
          .map(u => {
            return {
              label: `${u.Apellidos}, ${u.Nombres}`,
              value: u.IdUsuario
            }
          })
        this.Cadetes.unshift({
          label: 'Todos',
          value: 0
        })
      }
    })

    request.Get('/casos-pendientes/estados', {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.opcionesEstados = r.map(e => {
          return {
            label: e.EstadoCasoPendiente,
            value: parseInt(e.IdEstadoCasoPendiente)
          }
        })

        this.Estados = r.map(e => {
          return {
            label: e.EstadoCasoPendiente,
            value: e.EstadoCasoPendiente,
            check: false
          }
        })

        this.buscarCaso()
      }
    })

    request.Get('/casos/opciones-parametros', {}, r => {
      r.forEach(o => {
        if (typeof (this[o.Variable]) !== 'undefined') {
          this[o.Variable] = JSON.parse(o.Opciones)
        }
      })
    })
  },
  mounted () {
    const page = document.getElementById('casos_pendientes_id')

    page.addEventListener('click', e => {
      const drawer = document.getElementById('drawer_casos_pendientes')

      if (drawer && !drawer.contains(e.target) && e.target.id !== 'id_icon_filtro') {
        this.mostrar = false
      }
    })
  },
  watch: {
    tipoBusqueda () {
      if (this.busqueda) {
        this.buscarCaso()
      }
    }
  },
  methods: {
    buscarCaso (p = 1) {
      const f = this.finalizados ? 'S' : 'N'

      const offset = (p - 1) * 30
      this.pag = p
      this.loading = true
      this.Casos = []
      this.modalAlta = false

      const fe = JSON.stringify(f === 'S' ? ['FINALIZADO'] : this.FiltroEstados)
      const fa = JSON.stringify(this.FiltroFechasAlta)
      const fv = JSON.stringify(this.FiltroFechasVisitado)

      const tipoBusqueda = this.tipoBusqueda
      let buscar = {
        nom: '',
        doc: '',
        tel: ''
      }

      buscar[tipoBusqueda] = this.busqueda

      const datosBusqueda = {
        Cadena: buscar.nom,
        Documento: buscar.doc,
        Telefono: buscar.tel,
        Offset: offset,
        Estados: fe,
        FechasAlta: fa,
        FechasVisitado: fv,
        Cadete: this.cadete,
        Finalizado: f
      }

      request.Get('/casos-pendientes', datosBusqueda, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.Casos = r
          this.Casos.forEach(c => {
            c.UltimoMovimiento = c.UltimoMovimiento ? JSON.parse(c.UltimoMovimiento) : null
          })
          const cant = r.length ? r[0].Cant : 0
          this.maxpags = Math.ceil(parseInt(cant) / 30)
          this.loading = false

          if (r.length) {
            this.CantEstados = r[0].CantFechasAlta ? JSON.parse(r[0].CantEstados) : {}
            this.CantFechasAlta = r[0].CantFechasAlta ? JSON.parse(r[0].CantFechasAlta) : {}
            this.CantFechasVisitado = r[0].CantFechasVisitado ? JSON.parse(r[0].CantFechasVisitado) : {}

            if (this.primerGet) {
              this.FechasAlta = Object.keys(this.CantFechasAlta).map(f => {
                return {
                  label: this.formatFecha(f),
                  value: f,
                  check: false
                }
              })
              this.FechasAlta.sort((a, b) => {
                const dateA = new Date(a.value)
                const dateB = new Date(b.value)

                if (dateB < dateA) {
                  return -1
                } else {
                  return 1
                }
              })
              this.FechasVisitado = Object.keys(this.CantFechasVisitado).map(f => {
                return {
                  label: this.formatFecha(f),
                  value: f,
                  check: false
                }
              })
              this.FechasVisitado.sort((a, b) => {
                const dateA = new Date(a.value)
                const dateB = new Date(b.value)

                if (dateB < dateA) {
                  return -1
                } else {
                  return 1
                }
              })

              this.primerGet = false
            }
          }
        }
      })
    },
    formatFecha (f) {
      return f.split('-').reverse().join('/')
    },
    mapaCompleto () {
      const path = `/Maps?sec=visitas${this.finalizados ? '&mode=F' : ''}`

      let routeData = this.$router.resolve({ path })
      window.open(routeData.href, '_blank')
    },
    filtrar (c, v, f) {
      const filtro = `Filtro${f}`

      if (c) {
        this[filtro].push(v)
      } else {
        const i = this[filtro].findIndex(f => f === v)
        this[filtro].splice(i, 1)
      }

      if (this.IdTimeout) { clearTimeout(this.IdTimeout) }

      this.IdTimeout = setTimeout(() => {
        this.buscarCaso()
      }, 600)
    }
  }
}
</script>

<style>
#casos_pendientes_id .q-drawer {
  background-color: transparent !important;
  top: 75px !important;
}
.checks-container {
  padding-left: 1em;
  max-height: 600px;
  overflow: auto;
}
.q-radio {
  width: 100%;
}

.busqueda-input-caso-pendiente .q-field__control {
  position: absolute;
  width: 100%;
  top: 0px;
}
</style>
