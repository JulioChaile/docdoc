<template>
  <div class="q-pa-lg">
    <q-tabs
      v-model="tab"
      dense
      class="text-primary"
      active-color="primary"
      indicator-color="primary"
      align="justify"
      narrow-indicator
      @input="() => { this.anio = new Date().getFullYear(); this.mes = new Date().getMonth() }"
    >
      <q-tab name="Casos" label="Casos" />
      <q-tab name="Calendario" label="Calendario" />
    </q-tabs>

    <q-tab-panels v-model="tab" animated>
      <q-tab-panel name="Casos">
        <q-select
          v-model="estado"
          :options="Estados.map(e => { return { label: e.Estado + ' - Casos : ' + e.Cantidad + ' - Promedio Ult. Mov. Editado: ' + promedio(e.IdEstadoAmbitoGestion) + ' dias', value: e.IdEstadoAmbitoGestion } })"
          label="Estados"
          @input="Casos.forEach(c => c.check = false)"
        />

        <BotonFinalizar v-if="!loading" @finalizar="finalizar" />

        <div class="full-width justify-center" v-if="loading">
            <Loading />
        </div>

        <div v-else>
          <div
            class="row titulos_container q-banner"
          >
            <div style="width: 100px !important">
            </div>
            <div
              class="col casilla_container"
            >
              Caratula
            </div>
            <div
              class="col-1 casilla_container"
            >
              Fecha Estado
            </div>
            <div
              class="col-1 casilla_container"
            >
              Fecha Checkeo
            </div>
            <div
              class="col casilla_container"
            >
              Ambito de Gestion
            </div>
            <div
              class="col casilla_container"
            >
              Ultimo Movimiento
            </div>
            <div
              class="col-1 casilla_container"
            >
              Nro de Expediente
            </div>
          </div>

          <div
            v-for="(caso, i) in filterCasos(estado)"
            :key="caso.IdCaso"
            :class="'filas_container q-banner ' + (caso.Finalizado ? 'bg-positive' : '')"
          >
            <div class="row filas">
              <CheckBoxJ
                :caso="caso"
                :i="i"
                @checkCaso="check => checkCaso(check, caso.IdCaso)"
              />
              <div
                class="col cliente cursor-pointer column"
                @click="abrirCaso(caso.IdCaso)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ir al caso</q-tooltip>
                <span
                  class="q-subheading"
                  style="color: #1B43F0"
                >{{ caso.Caratula }}</span>
                <span class="text-caption">{{dias(caso.FechaAlta)}}</span>
              </div>
              <div
                class="col-1"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Fecha Estado</q-tooltip>
                {{ fecha(caso.FechaEstado) }}
              </div>

              <div
                class="col-1"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Fecha Checkeo</q-tooltip>
                {{ caso.FechaUltFinalizado ? fecha(caso.FechaUltFinalizado) : '-'}}
              </div>
              <div
                class="col"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ambito de Gestion</q-tooltip>
                {{ caso.Juzgado || 'Sin datos' }}
              </div>
              <div
                class="col cursor-pointer"
                @click="editarMovimiento(caso.UltimoMovimientoEditado, caso.Caratula)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Editar movimiento - Ult. Edicion: {{ caso.UltimoMovimientoEditado ? fecha(caso.UltimoMovimientoEditado.FechaEdicion) : '-' }}</q-tooltip>
                {{ caso.UltimoMovimientoEditado ? detalleUltMov(caso.UltimoMovimientoEditado) : 'Sin movimientos' }}
              </div>
              <div
                class="col-1 column"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Nro Expediente</q-tooltip>
                {{ caso.NroExpediente || 'Sin Nro de Expediente' }}
              </div>
            </div>
          </div>
        </div>
      </q-tab-panel>

      <q-tab-panel name="Calendario">
        <div class="row">
          <div class="col-1 row justify-center align-center">
            <q-icon
              class="cursor-pointer"
              name="chevron_left"
              size="sm"
              @click="mes - 1 === -1 ? (()=>{mes = 11; anio--})() : mes--"
            />
          </div>

          <div class="col text-center text-bold">
            {{ nombres[mes] + ' - ' + anio }}
          </div>

          <div class="col-1 row justify-center align-center">
            <q-icon
              v-if="(new Date()).getMonth() !== mes"
              class="cursor-pointer"
              name="chevron_right"
              size="sm"
              @click="mes + 1 === 12 ? (()=>{mes = 0; anio++})() : mes++"
            />
          </div>
        </div>

        <div class="full-width q-mt-lg">
          <table class="full-width" style="border: 1px solid black;">
            <thead>
              <tr>
                <th style="border: 1px solid black; width: 250px !important">Estados</th>
                <th
                  v-for="dia in arrayDias()"
                  :key="dia"
                  style="border: 1px solid black;"
                >
                  {{ dia }}
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="estado in Estados"
                :key="estado.IdEstadoAmbitoGestion"
              >
                <td style="border: 1px solid black; width: 150px !important">{{ estado.Estado }}</td>
                <td
                  v-for="dia in arrayDias()"
                  :key="dia"
                  style="border: 1px solid black;"
                  :class="classDia(dia, estado.IdEstadoAmbitoGestion)"
                >
                  {{ labelDia(dia, estado.IdEstadoAmbitoGestion) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </q-tab-panel>
    </q-tab-panels>

    <!-- Modal Editar Movimiento -->
    <q-dialog v-model="modalMovimiento" no-backdrop-dismiss no-esc-dismiss>
      <q-card>
        <q-item class="relative-position" style="background-color: black;">
          <span class="q-subheading" style="color:white;">Editar Movimiento</span>
          <q-icon
            name="close"
            class="absolute-right cursor-pointer"
            color="negative"
            size="lg"
            @click="modalMovimiento = false"
          />
        </q-item>
        <EditarMovimiento
          v-if="modalMovimiento"
          :movimiento="this.movimientoEditar"
          @cancelarEdicion="modalMovimiento = false"
          @edicionTerminada="modalMovimiento = false"
        />
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import auth from '../auth'
import request from '../request'
import moment from 'moment'
import Loading from '../components/Loading'
import EditarMovimiento from '../components/EditarMovimiento'
import CheckBoxJ from '../components/Judiciales/CheckBoxJ'
import BotonFinalizar from '../components/Judiciales/BotonFinalizar'
import { QTabs, QTab, QTabPanel, QTabPanels, Notify } from 'quasar'

export default {
  components: { EditarMovimiento, QTabs, QTab, QTabPanel, QTabPanels, Loading, CheckBoxJ, BotonFinalizar },
  data () {
    return {
      IdUsuario: auth.UsuarioLogueado.IdUsuario,
      tab: 'Casos',
      force: 1,
      Casos: [],
      JudicialesC: [],
      JudicialesI: [],
      Estados: [],
      movimientoEditar: {},
      modalMovimiento: false,
      estado: { label: '', value: 0 },
      anio: (new Date()).getFullYear(),
      mes: (new Date()).getMonth(),
      nombres: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      loading: true
    }
  },
  created () {
    request.Get('/casos/buscar-judiciales', {}, r => {
      this.loading = false

      this.Casos = r.CasosJudiciales.filter(c => c.IdEstadoAmbitoGestion && c.IdEstadoAmbitoGestion !== 'null' && c.IdEstadoAmbitoGestion !== 0)
      this.JudicialesI = r.JudicialesI.filter(j => {
        const id = j.IdCaso
        const i = this.Casos.findIndex(c => parseInt(c.IdCaso) === parseInt(id))

        return i >= 0
      }).reverse()
      this.JudicialesC = r.JudicialesC.filter(j => {
        const id = j.IdJudicialesC
        const i = this.JudicialesI.findIndex(c => parseInt(c.IdJudicialesC) === parseInt(id))

        return i >= 0
      }).reverse()

      const ids = [ ...new Set(this.Casos.map(c => c.IdEstadoAmbitoGestion)) ]
      this.Estados = ids.map(id => {
        const i = this.Casos.findIndex(c => c.IdEstadoAmbitoGestion === id)
        const Estado = this.Casos[i].EstadoAmbitoGestion
        const Orden = this.Casos[i].Orden
        const Cantidad = this.Casos.filter(c => c.IdEstadoAmbitoGestion === id).length
        const IdEstadoAmbitoGestion = id

        return { Estado, Cantidad, IdEstadoAmbitoGestion, Orden }
      }).sort((a, b) => parseInt(a.Orden) - parseInt(b.Orden))

      this.estado = {
        label: this.Estados[0].Estado + ' - Casos: ' + this.Estados[0].Cantidad + ' - Promedio Ult. Mov. Editado: ' + this.promedio(this.Estados[0].IdEstadoAmbitoGestion) + ' dias',
        value: this.Estados[0].IdEstadoAmbitoGestion
      }

      this.Casos.forEach(c => {
        c.UltimoMovimiento = JSON.parse(c.UltimoMovimiento)
        c.UltimoMovimientoEditado = JSON.parse(c.UltimoMovimientoEditado)
        c.check = false
        c.DiasEstado = this.fecha(c.FechaEstado).split(' ')[0]

        const i = this.JudicialesI.findIndex(j => parseInt(j.IdCaso) === parseInt(c.IdCaso))
        if (i >= 0) {
          const id = this.JudicialesC.filter(j => parseInt(j.IdJudicialesC) === parseInt(this.JudicialesI[i].IdJudicialesC))[0].IdEstadoAmbitoGestion
          const j = this.JudicialesC.filter(j => parseInt(j.IdJudicialesC) === parseInt(this.JudicialesI[i].IdJudicialesC))[0]

          if (parseInt(id) === parseInt(c.IdEstadoAmbitoGestion)) {
            c.Finalizado = true
            c.FechaUltFinalizado = j.Fecha
          }
        }
      })

      this.Casos.sort((a, b) => parseInt(this.fecha(a.FechaEstado).split(' ')[0]) - parseInt(this.fecha(b.FechaEstado).split(' ')[0]))
    })
  },
  methods: {
    filterCasos (estado) {
      let casos = this.Casos.filter(c => parseInt(c.IdEstadoAmbitoGestion) === parseInt(estado.value))
      const IdEstadoAmbitoGestion = parseInt(estado.value)

      const dia = casos.filter(c => c.Finalizado).length === 0 ? '' : parseInt(moment(casos.filter(c => c.Finalizado).sort((a, b) => a.FechaUltFinalizado - b.FechaUltFinalizado)[0].FechaUltFinalizado).format('YYYY-MM-DD').split('-')[2])

      if (dia) {
        const t = this.classDia(dia, IdEstadoAmbitoGestion) === 'bg-positive'
        const p = this.classDia(dia, IdEstadoAmbitoGestion) === 'bg-warning'

        if (t) {
          casos = casos.map(c => {
            c.Finalizado = false

            return c
          })
        } else if (p) {
          casos = casos.map(c => {
            if (!c.Finalizado) return c

            const d = parseInt(moment(c.FechaUltFinalizado).format('YYYY-MM-DD').split('-')[2])

            if (this.classDia(d, IdEstadoAmbitoGestion) !== 'bg-warning') c.Finalizado = false

            return c
          })
        }
      }

      if (casos.filter(c => c.Finalizado).length === casos.length) {
        casos = casos.map(c => {
          c.Finalizado = false

          return c
        })
      }

      return casos.sort((a, b) => moment(b.FechaEstado).format('YYYY-MM-DD') - moment(a.FechaEstado).format('YYYY-MM-DD'))
    },
    classDia (dia, id) {
      const f = this.anio + '-' + ((this.mes + 1) < 10 ? '0' + (this.mes + 1) : (this.mes + 1)) + '-' + (dia < 10 ? '0' + dia : dia)

      const a = this.JudicialesC.filter(c => moment(c.Fecha).format('YYYY-MM-DD') <= f && parseInt(c.IdEstadoAmbitoGestion) === parseInt(id))

      const i = this.JudicialesC.findIndex(c => moment(c.Fecha).format('YYYY-MM-DD') === f && parseInt(c.IdEstadoAmbitoGestion) === parseInt(id))

      if (i < 0) return ''

      const j = this.JudicialesC[i]
      const c = parseInt(j.CantCasos)

      let tot = 0
      let check = true

      a.forEach(s => {
        const r = this.JudicialesI.filter(i => parseInt(i.IdJudicialesC) === parseInt(s.IdJudicialesC)).length

        if ((tot + r <= c) && check) {
          tot = tot + r
        } else {
          check = false
        }
      })

      return c === tot ? 'bg-positive' : (tot === 0 ? '' : 'bg-warning')
    },
    labelDia (dia, id) {
      const f = this.anio + '-' + ((this.mes + 1) < 10 ? '0' + (this.mes + 1) : (this.mes + 1)) + '-' + (dia < 10 ? '0' + dia : dia)

      const a = this.JudicialesC.filter(c => moment(c.Fecha).format('YYYY-MM-DD') <= f && parseInt(c.IdEstadoAmbitoGestion) === parseInt(id))

      const i = this.JudicialesC.findIndex(c => moment(c.Fecha).format('YYYY-MM-DD') === f && parseInt(c.IdEstadoAmbitoGestion) === parseInt(id))

      if (i < 0) return ''

      const j = this.JudicialesC[i]
      const u = j.Apellidos[0].toUpperCase() + j.Nombres[0].toUpperCase()
      const c = j.CantCasos

      let tot = 0
      let check = true

      a.forEach(s => {
        const r = this.JudicialesI.filter(i => parseInt(i.IdJudicialesC) === parseInt(s.IdJudicialesC)).length

        if ((tot + r <= c) && check) {
          tot = tot + r
        } else {
          check = false
        }
      })

      return u + ' - ' + tot + '/' + c
    },
    arrayDias () {
      const dias = new Date(this.anio, this.mes + 1 === 12 ? 0 : this.mes + 1, 0).getDate()
      let array = []

      for (let i = 1; i <= dias; i++) {
        array.push(i)
      }

      return array
    },
    promedio (id) {
      const casos = this.Casos.filter(c => parseInt(c.IdEstadoAmbitoGestion) === parseInt(id))

      let dias = 0

      casos.forEach(c => {
        if (c.UltimoMovimientoEditado) {
          const f = c.UltimoMovimientoEditado.FechaEdicion

          const d = f ? moment().diff(moment(f), 'days') : 0

          dias = dias + d
        }
      })

      return dias ? parseInt(dias / casos.length) : 0
    },
    finalizar () {
      if (this.Casos.filter(c => c.check).length === 0) {
        Notify.create('Debe seleccionar al menos un caso.')
        return
      }

      this.loading = true
      let { Cantidad, IdEstadoAmbitoGestion, Estado } = this.Estados.filter(f => parseInt(f.IdEstadoAmbitoGestion) === parseInt(this.estado.value))[0]
      const Casos = this.Casos.filter(c => c.check).map(c => {
        return {
          IdCaso: c.IdCaso,
          Dias: c.DiasEstado
        }
      })

      // Cantidad = Cantidad - this.Casos.filter(c => parseInt(c.IdEstadoAmbitoGestion) === parseInt(IdEstadoAmbitoGestion) && c.Finalizado).length

      request.Post('/casos/finalizar-casos', { Cantidad, IdEstadoAmbitoGestion, Casos, Estado }, r => {
        this.loading = false
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.Casos.forEach(c => {
            if (c.check) {
              c.Finalizado = true
              c.check = false
            }
          })

          this.force++

          this.JudicialesC.push({
            IdJudicialesC: r.IdJudicialesC,
            Fecha: moment(),
            IdEstadoAmbitoGestion,
            CantCasos: Cantidad,
            IdUsuario: this.IdUsuario,
            Apellidos: auth.UsuarioLogueado.Apellidos,
            Nombres: auth.UsuarioLogueado.Nombres
          })

          Casos.forEach(c => {
            this.JudicialesI.push({
              IdJudicialesC: r.IdJudicialesC,
              IdCaso: c
            })
          })

          Notify.create('Se finalizaron los casos y se envio un mensaje a la persona principal del caso.')
        }
      })
    },
    fecha (f) {
      return moment().diff(moment(f), 'days') + ' dias'
    },
    abrirCaso (id) {
      let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
      window.open(routeData.href, '_blank')
    },
    editarMovimiento (movimiento, caratula) {
      event.stopPropagation()
      if (!movimiento) { return }
      movimiento.Caratula = caratula
      this.movimientoEditar = movimiento
      this.modalMovimiento = true
    },
    dias (Fecha) {
      if (!Fecha) {
        return ''
      }
      var fecha = new Date()
      var hoy = new Date()
      var year = Fecha.split('-')[0]
      var month = Fecha.split('-')[1] - 1
      var day = Fecha.split('-')[2].split(' ')[0] - 1
      fecha.setMonth(month)
      fecha.setFullYear(year)
      fecha.setDate(day)
      var resultado =
        Math.ceil(
          (fecha.getTime() - hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      return resultado * -1 !== 1 ? `${resultado * -1} días` : `${resultado * -1} día`
    },
    detalleUltMov (mov) {
      if (mov) {
        return mov.Detalle
          ? mov.Detalle
          : 'Sin movimientos'
      } else {
        return 'Sin movimientos'
      }
    },
    checkCaso (check, id) {
      const i = this.Casos.findIndex(caso => parseInt(caso.IdCaso) === parseInt(id))
      this.Casos[i].check = check
    }
  }
}
</script>

<style scoped>
.acciones > i {
  cursor: pointer;
  margin: 0 auto;
}

.casilla_container, .filas div {
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  position: relative;
}

.titulos_container {
  font-family: "Avenir Next";
  font-weight: bold;
  height : 70px;
  font-size: 16px;
}

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
  min-height: inherit;
  height: inherit;
  font-family: "Avenir Next";
  font-weight: 600;
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

.filas_container {
  min-height: 60px;
  height: auto !important;
}

.casilla_container i {
  cursor: pointer;
}

.casilla_container .q-select {
  visibility: hidden;
  width: 0px;
}

@media only screen and (max-width: 1023px) {
  .filas > div:not(.cliente), .titulos_container > div:not(.cliente) {
    display: none !important;
  }

  .cliente {
    margin: 10px 0px;
  }

  .filas_container {
    height: 120px;
  }
}
</style>
