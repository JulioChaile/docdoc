<template>
  <q-page>
    <div v-if="loading">
      <Loading />
    </div>
    <div v-else class="q-pa-lg">
      <q-select v-model="estudio" label="Filtrar Estudio" :options="opcionesEstudios" />
      <div
        class="row titulos_container q-banner"
      >
        <div
          class="col casilla_container"
        >
          Tipo de Caso
          <!--q-select
            v-model="TipoCaso"
            multiple
            :options="opcionesTiposCaso"
            ref="selectTipoCaso"
          />
          <q-icon
            rounded
            color="grey"
            name="more_vert"
            size="sm"
            @click="showSelect('TipoCaso')"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Tipo de Caso</q-tooltip>
          </q-icon-->
          <!--q-separator
            class="separador_titulo"
            color="black"
          /-->
        </div>
        <div
          class="col-sm-3 col-xs-12 casilla_container cliente"
        >
          Cliente
          <!--q-separator
            class="separador_titulo"
            color="black"
          /-->
        </div>
        <div
          class="col casilla_container"
        >
          Gestión
          <!--q-select
            v-model="Ambito"
            multiple
            :options="opcionesAmbitosGestion"
            ref="selectAmbitoGestion"
          />
          <q-icon
            rounded
            color="grey"
            name="more_vert"
            size="sm"
            @click="showSelect('AmbitoGestion')"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Ambito de Gestión</q-tooltip>
          </q-icon-->
          <!--q-separator
            class="separador_titulo"
            color="black"
          /-->
        </div>
        <div
          class="col casilla_container"
        >
          Nominación
          <!--q-select
            v-model="Nominacion"
            multiple
            :options="opcionesNominaciones"
            ref="selectNominacion"
          />
          <q-icon
            rounded
            color="grey"
            name="more_vert"
            size="sm"
            @click="showSelect('Nominacion')"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Nominación</q-tooltip>
          </q-icon-->
          <!--q-separator
            class="separador_titulo"
            color="black"
          /-->
        </div>
        <div
          class="col-sm-2 casilla_container"
        >
          Estado Gestión
          <!--q-select
            v-model="EstadoAmbitoGestion"
            multiple
            :options="opcionesEstados"
            ref="selectEstadoAmbitoGestion"
          />
          <q-icon
            rounded
            color="grey"
            name="more_vert"
            size="sm"
            @click="showSelect('EstadoAmbitoGestion')"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Filtrar por Estado de Ambito de Gestión</q-tooltip>
          </q-icon-->
          <!--q-separator
            class="separador_titulo"
            color="black"
          /-->
        </div>
        <div
          class="col casilla_container"
        >
          N° Expediente
          <!--q-separator
            class="separador_titulo"
            color="black"
          /-->
        </div>
        <div
          class="col-sm-1 casilla_container"
        >
          Comparticiones
        </div>
        <div
          class="col-sm-1 casilla_container"
        >
          Ult. Movimiento
        </div>
      </div>
      <div
        v-for="caso in buscarCaso"
        :key="caso.IdCaso"
      >
        <div class="filas_container q-banner">
          <div class="row filas">
            <div
              class="col"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Tipo de Caso</q-tooltip>
              {{caso.TipoCaso}}
            </div>
            <div
              class="col-sm-3 cursor-pointer cliente"
              @click="abrirCaso(caso.IdCaso)"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">{{ caso.Caratula ? caso.Caratula : 'Sin Caratula' }}</q-tooltip>
              <span
                class="q-subheading"
                style="color: #1B43F0"
              >{{ caratula(caso.Caratula) }}</span>
            </div>
            <div
              class="col"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Tipo de Proceso</q-tooltip>
              {{ caso.Juzgado }}
            </div>
            <div
              class="col"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Nominacion</q-tooltip>
              <span>{{ nominacion(caso.Nominacion) }}</span>
            </div>
            <div
              class="col-sm-2 column"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Estado de Proceso</q-tooltip>
              {{caso.EstadoAmbitoGestion ? caso.EstadoAmbitoGestion : 'No hay un estado asignado.'}}
              <br>
              <span style="color: #1B43F0">{{diasCambioEstado(caso.FechaEstado)}}</span>
            </div>
            <div
              class="col"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">N° Expediente</q-tooltip>
              {{caso.NroExpediente ? caso.NroExpediente : 'Sin expediente'}}
            </div>
            <div
              class="col-sm-1 cursor-pointer"
              @click="verComparticiones(caso)"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">{{ caso.Comparticiones ? 'Ver Comparticiones' : 'Caso sin derivar' }}</q-tooltip>
              <q-icon
                v-if="caso.Comparticiones"
                name="r_check_circle"
                size="sm"
                :style="caso.Comparticiones ? 'color: #49C00F;' : 'color: #49C00F; filter: opacity(25%)'"
              />
              <q-icon
                v-if="!caso.Comparticiones"
                name="r_cancel"
                size="sm"
                :style="!caso.Comparticiones ? 'color: #B1000E;' : 'color: #B1000E; filter: opacity(25%)'"
              />
              <q-icon
                v-if="caso.Comparticiones"
                color="grey"
                name="more_vert"
                size="20px"
                style="position: absolute; right: calc(50% - 35px)"
              />
            </div>
            <div
              class="col-sm-1 column cursor-pointer"
              @click="editarMovimiento(caso.UltimoMovimiento, caso.Caratula)"
            >
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ultimo Movimiento</q-tooltip>
              {{caso.UltimoMovimiento ? detalleUltMov(caso.UltimoMovimiento) : 'Sin movimientos'}}
              <br>
              <span v-if="caso.UltimoMovimiento" style="color: #1B43F0">{{diasCambioEstado(caso.UltimoMovimiento.FechaEdicion, false)}}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--div v-else class="casos__container">
      <TarjetaCaso
      v-for= "caso in buscarCaso" :key="caso.IdCaso"
      v-if="casos.length !== 0"
      :caso="caso"
      :clases="modo"
      @quitar = "quitarCaso($event)"
      style="margin-top: 2rem; margin-bottom: 2rem;  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);"
      />
      <span v-if="casos.length === 0" style="margin-left: auto; margin-right:auto; margin-top:3rem;font-style: italic;" class="text-h6">Usted aún no compartió ningun caso</span>
    </div-->

    <!-- Modal Comparticiones -->
    <q-dialog v-model="modalComparticiones">
      <Comparticiones
        :comparticiones="casoCompartido.Comparticiones"
        :IdCaso="casoCompartido.IdCaso"
        @cerrar="modalComparticiones = false"
        @eliminarComparticiones="eliminarComparticiones"
      />
    </q-dialog>

    <!-- Modal Editar Movimiento -->
    <q-dialog v-model="ModalMovimiento">
      <q-card>
        <q-item class="relative-position" style="background-color: black;">
          <span class="q-subheading" style="color:white;">Editar Movimiento</span>
          <q-icon
            name="close"
            class="absolute-right cursor-pointer"
            color="negative"
            size="lg"
            @click="ModalMovimiento = false"
          />
        </q-item>
        <EditarMovimiento
          v-if="ModalMovimiento"
          :movimiento="this.movimientoEditar"
          @cancelarEdicion="ModalMovimiento = false"
          @edicionTerminada="ModalMovimiento = false"
        />
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import Comparticiones from '../components/GrillaCasos/Comparticiones'
import TarjetaCaso from '../components/TarjetaCaso.vue'
import request from '../request'
import EditarMovimiento from '../components/EditarMovimiento'
import Loading from '../components/Loading'
export default {
  components: {
    TarjetaCaso,
    Comparticiones,
    EditarMovimiento,
    Loading
  },
  name: 'Casos',
  data () {
    return {
      casos: [],
      busqueda: '',
      tipoBusqueda: 't', // t (Todos) - p (Personas) - j (Juzgados) - tp (Tipo de Caso),
      estado: 'A',
      verAlta: true,
      verArchivados: false,
      modo: 'casos__listado',
      loading: true,
      casoCompartido: {},
      modalComparticiones: false,
      movimientoEditar: null,
      ModalMovimiento: false,
      estudios: [],
      estudio: ''
    }
  },
  created () {
    request.Get('/casos/compartidos', {}, r => {
      if (!r.Error) {
        r.forEach(c => {
          c.PersonasCaso = JSON.parse(c.PersonasCaso)
          c.Comparticiones = JSON.parse(c.Comparticiones)
          c.UltimoMovimiento = JSON.parse(c.UltimoMovimiento)

          if (c.Comparticiones) {
            this.casos.push(c)

            c.Comparticiones.forEach(comp => {
              if (comp.IdEstudioOrigen) {
                const i = this.estudios.findIndex(e => e.IdEstudio === comp.IdEstudioOrigen)

                if (i === -1) {
                  this.estudios.push({
                    IdEstudio: comp.IdEstudioOrigen,
                    Estudio: comp.EstudioOrigen
                  })
                }
              }

              if (comp.EstudiosDestino) {
                comp.EstudiosDestino.forEach(f => {
                  const j = this.estudios.findIndex(e => e.IdEstudio === f.IdEstudio)

                  if (j === -1) {
                    this.estudios.push(f)
                  }
                })
              }
            })
          }
        })

        this.casos = this.casos.sort((a, b) => {
          if (a.FechaAlta < b.FechaAlta) {
            return 1
          }
          if (a.FechaAlta > b.FechaAlta) {
            return -1
          }
          return 0
        })
        this.loading = false
      }
    })
  },
  computed: {
    buscarCaso () {
      return this.casos.filter(c => {
        let check = false

        c.Comparticiones.forEach(comp => {
          if (comp.IdEstudioOrigen === this.estudio.value) check = true

          if (comp.EstudiosDestino) {
            comp.EstudiosDestino.forEach(e => {
              if (e.IdEstudio === this.estudio.value) check = true
            })
          }
        })

        if (!this.estudio.value) check = true

        return check
      })

      /*
      const casos = this.casos.filter(c => c.Estado === this.estado || this.estado === 'T')

      switch (this.tipoBusqueda) {
        case 't' :
          return casos.filter(caso => {
            return caso.Caratula.toLowerCase().includes(this.busqueda.toLowerCase()) ||
          caso.Juzgado.toLowerCase().includes(this.busqueda.toLowerCase()) ||
          caso.TipoCaso.toLowerCase().includes(this.busqueda.toLowerCase()) ||
          caso.PersonasCaso.filter(p =>
            (p.Nombres && p.Nombres.toLowerCase().includes(this.busqueda.toLowerCase())) ||
            (p.Apellidos && p.Apellidos.toLowerCase().includes(this.busqueda.toLowerCase())) ||
            (p.Documento && p.Documento.toLowerCase().includes(this.busqueda.toLowerCase()))
          ).length
          })
        case 'p' :
          return casos.filter(caso => {
            return caso.PersonasCaso.filter(p =>
              (p.Nombres && p.Nombres.toLowerCase().includes(this.busqueda.toLowerCase())) ||
              (p.Apellidos && p.Apellidos.toLowerCase().includes(this.busqueda.toLowerCase())) ||
              (p.Documento && p.Documento.includes(this.busqueda))
            ).length
          })
        case 'j' :
          return casos.filter(caso => {
            return caso.Juzgado.toLowerCase().includes(this.busqueda.toLowerCase())
          })
        case 'tp' :
          return casos.filter(caso => {
            return caso.TipoCaso.toLowerCase().includes(this.busqueda.toLowerCase())
          })
      }
      */
    },
    opcionesEstudios () {
      let result = []
      if (this.estudios && this.estudios.length) {
        result = this.estudios.map((e) => ({
          label: e.Estudio,
          value: e.IdEstudio
        }))
      }
      result.unshift({
        label: 'TODOS',
        value: ''
      })
      return result
    }
  },
  methods: {
    isMobile () {
      if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true
      } else {
        return false
      }
    },
    quitarCaso (caso) {
      this.casos.splice(this.casos.indexOf(caso), 1)
    },
    filtrarArchivados () {
      if (this.verAlta && this.verArchivados) {
        this.estado = 'T'
      } else if (this.verArchivados) {
        this.estado = 'R'
      } else if (this.verAlta) {
        this.estado = 'A'
      } else {
        this.estado = 'A'
      }
    },
    abrirCaso (id) {
      let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
      window.open(routeData.href, '_blank')
    },
    caratula (c) {
      if (!c) {
        return 'Sin Caratula'
      }

      return c.length > 25
        ? c.slice(0, 23) + '...'
        : c
    },
    verComparticiones (caso) {
      if (caso.Comparticiones) {
        this.casoCompartido = caso
        this.modalComparticiones = true
      }
    },
    editarMovimiento (movimiento, caratula) {
      event.stopPropagation()
      if (!movimiento) { return }
      movimiento.Caratula = caratula
      this.movimientoEditar = movimiento
      this.ModalMovimiento = true
    },
    detalleUltMov (mov) {
      if (mov) {
        return mov.Detalle
          ? mov.Detalle.slice(0, 15) + (mov.Detalle.length > 15 ? '...' : '')
          : 'Sin movimientos'
      } else {
        return 'Sin movimientos'
      }
    },
    diasCambioEstado (FechaEstado, grilla = true) {
      if (!FechaEstado) {
        return grilla ? 'No hay un estado asignado.' : ''
      }
      var fecha = new Date()
      var hoy = new Date()
      var year = FechaEstado.split('-')[0]
      var month = FechaEstado.split('-')[1] - 1
      var day = FechaEstado.split('-')[2].split(' ')[0] - 1
      fecha.setMonth(month)
      fecha.setFullYear(year)
      fecha.setDate(day)
      var resultado =
        Math.ceil(
          (fecha.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      return resultado * -1 !== 1 ? `${resultado * -1} días` : `${resultado * -1} día`
    },
    nominacion (nom) {
      const n = {
        0: 'Pendiente',
        1: 'I',
        2: 'II',
        3: 'III',
        4: 'IV',
        5: 'V',
        6: 'VI',
        7: 'VII',
        8: 'VIII',
        9: 'IX'
      }
      const i = nom ? nom.slice(0, 1) : '-'
      return n[i] ? n[i] : '···'
    },
    eliminarComparticiones (IdsEstudios, IdCaso) {
      const i = this.casos.findIndex(c => c.IdCaso === IdCaso)

      if (i > 0) {
        let indexes = []

        IdsEstudios.forEach(r => {
          const index = this.casos.Comparticiones.EstudiosDestino.findIndex(e => e.IdEstudio === r)

          if (index > 0) { indexes.push(index) }
        })

        if (indexes.length > 0) {
          indexes.forEach(r => {
            this.casos.Comparticiones.EstudiosDestino.splice(r, 0)
          })
        }
      }
    }
  }
}
</script>

<style>
.casos__container {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  padding: 0px;
  margin: 0px;
}

.casilla_container, .filas div, .opciones-container > div {
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  position: relative;
}

.opciones-container {
  margin: 20px auto 0px auto;
  width: 21%;
}

.opciones-container .separador_titulo {
  height: 100%;
}

.casilla_container i {
  cursor: pointer;
}

.casilla_container .q-select {
  visibility: hidden;
  width: 0px;
  height: 0px;
  overflow: hidden;
}

.casos__container > div {
  margin: 0;
  padding: 0;
  display: flex;
  width: 100%;
}

.filas_container, .titulos_container {
  background-color: #ffffff;
  width: 100%;
  color: #000 !important;
  height : 60px;
  margin-top:15px;
  padding: 0;
  border-radius : 7px;
  -moz-border-radius : 7px;
  -webkit-border-radius : 7px;
}

.titulos_container {
  font-family: "Avenir Next";
  font-weight: bold;
  height : 70px;
  font-size: 16px;
}

.filtros-container {
  margin-bottom: none;
}

.filtros-container .q-item {
  display: none;
}

.busqueda-input {
  border-radius: 28px;
  background-color: white;
  width: auto;
  max-width: 584px;
  height: 44px;
  margin: 60px auto 0px auto;
}

.busqueda-input .q-field__control, .busqueda-input .q-field__marginal {
  height: 44px !important;
}

/*
.busqueda-input .q-field__control::before {
  content: '';
  background: -moz-linear-gradient(50% 100% 90deg,rgba(255, 255, 255, 0.5) 0%,rgba(253, 253, 253, 0.5) 44.8%,rgba(246, 246, 246, 0.5) 60.93%,rgba(235, 235, 235, 0.5) 72.43%,rgba(218, 218, 218, 0.5) 81.73%,rgba(196, 196, 196, 0.5) 89.7%,rgba(169, 169, 169, 0.5) 96.62%,rgba(153, 153, 153, 0.5) 100%);
  background: -webkit-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 44.8%, rgba(246, 246, 246, 0.5) 60.93%, rgba(235, 235, 235, 0.5) 72.43%, rgba(218, 218, 218, 0.5) 81.73%, rgba(196, 196, 196, 0.5) 89.7%, rgba(169, 169, 169, 0.5) 96.62%, rgba(153, 153, 153, 0.5) 100%);
  background: -webkit-gradient(linear,50% 100% ,50% 0% ,color-stop(0,rgba(255, 255, 255, 0.5) ),color-stop(0.448,rgba(253, 253, 253, 0.5) ),color-stop(0.6093,rgba(246, 246, 246, 0.5) ),color-stop(0.7243,rgba(235, 235, 235, 0.5) ),color-stop(0.8173,rgba(218, 218, 218, 0.5) ),color-stop(0.897,rgba(196, 196, 196, 0.5) ),color-stop(0.9662,rgba(169, 169, 169, 0.5) ),color-stop(1,rgba(153, 153, 153, 0.5) ));
  background: -o-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 44.8%, rgba(246, 246, 246, 0.5) 60.93%, rgba(235, 235, 235, 0.5) 72.43%, rgba(218, 218, 218, 0.5) 81.73%, rgba(196, 196, 196, 0.5) 89.7%, rgba(169, 169, 169, 0.5) 96.62%, rgba(153, 153, 153, 0.5) 100%);
  background: -ms-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 44.8%, rgba(246, 246, 246, 0.5) 60.93%, rgba(235, 235, 235, 0.5) 72.43%, rgba(218, 218, 218, 0.5) 81.73%, rgba(196, 196, 196, 0.5) 89.7%, rgba(169, 169, 169, 0.5) 96.62%, rgba(153, 153, 153, 0.5) 100%);
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#999999' ,GradientType=0)";
  background: linear-gradient(0deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 44.8%, rgba(246, 246, 246, 0.5) 60.93%, rgba(235, 235, 235, 0.5) 72.43%, rgba(218, 218, 218, 0.5) 81.73%, rgba(196, 196, 196, 0.5) 89.7%, rgba(169, 169, 169, 0.5) 96.62%, rgba(153, 153, 153, 0.5) 100%);
  filter: alpha(opacity=50) progid:DXImageTransform.Microsoft.Alpha(opacity=50) progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999',endColorstr='#FFFFFF' , GradientType=0);
}
*/

.filas {
  background : -moz-linear-gradient(50% 100% 90deg,rgba(255, 255, 255, 0.5) 0%,rgba(253, 253, 253, 0.5) 30.79%,rgba(244, 244, 244, 0.5) 49.03%,rgba(230, 230, 230, 0.5) 64%,rgba(210, 210, 210, 0.5) 77.19%,rgba(184, 184, 184, 0.5) 89.1%,rgba(153, 153, 153, 0.5) 100%);
  background : -webkit-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  background : -webkit-gradient(linear,50% 100% ,50% 0% ,color-stop(0,rgba(255, 255, 255, 0.5) ),color-stop(0.3079,rgba(253, 253, 253, 0.5) ),color-stop(0.4903,rgba(244, 244, 244, 0.5) ),color-stop(0.64,rgba(230, 230, 230, 0.5) ),color-stop(0.7719,rgba(210, 210, 210, 0.5) ),color-stop(0.891,rgba(184, 184, 184, 0.5) ),color-stop(1,rgba(153, 153, 153, 0.5) ));
  background : -o-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  background : -ms-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#999999' ,GradientType=0)";
  background : linear-gradient(0deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%);
  filter: alpha(opacity=50) progid:DXImageTransform.Microsoft.Alpha(opacity=50) progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999',endColorstr='#FFFFFF' , GradientType=0);
  padding: inherit;
  border-radius : inherit;
  -moz-border-radius : inherit;
  -webkit-border-radius : inherit;
  width: inherit;
  height: inherit;
  font-family: "Avenir Next";
  font-weight: 600;
}

.check_casilla {
  margin-left: -3px;
  margin-right: 4px;
}

.separador_titulo {
  height: 45%;
  width: 2px;
  position: absolute;
  right: -1px;
}

.opciones-container .q-toggle__thumb::after {
  background-color: #81E2CE !important;
}

.opciones-container .q-toggle__inner--truthy {
  color: #81E2CE !important;
}

@media only screen and (max-width: 1023px) {
  .filas > div:not(.cliente), .titulos_container > div:not(.cliente) {
    display: none !important;
  }

  .cliente {
    width: 100% !important;
  }

  .opciones-container {
    width: 50%;
  }
}

@media only screen and (max-device-width: 600px) {
  .q-btn-group {
    margin: 0.5em -0.5em 0.5em -0.5em !important;
  }

  .opciones-container {
    width: 75%;
  }
}
</style>
