<template>
  <q-page style="padding:1em 2em 2em 2em; position: relative">
    <q-btn
      v-if="!tareas"
      color="white"
      text-color="black"
      label="Tareas Pendientes"
      class="fixed-top-right"
      style="top: 70px; right: 20px; z-index: 3000"
      @click="tareas = true"
    />

    <div>
      <div style="display:flex; flex-direction: column; justify-content: space-evenly;">
        <div>
          <GrillaCasos />
        </div>
      </div>
    </div>

    <!-- Modal Tareas Pendientes -->
    <q-dialog v-model="tareas" position="right">
      <q-card class="modal_nuevo_tel">
        <q-card-section style="display:flex; justify-content:center;">
          <span class="text-h5">Tienes tareas pendientes</span>
        </q-card-section>
        <q-card-section>
          <span v-if="movimientos.length === 0">
            No tienes tareas pendientes
          </span>

          <TarjetaMovimiento
            v-for="m in movimientos"
            @borrar = "eliminarMovimiento($event)"
            :key="m.IdMovimientoCaso"
            :movimiento="m"
            :tarea="true"
            class="tarjeta-tarea full-width"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<style>
</style>

<script>
import request from '../request'
import auth from '../auth'
import Loading from '../components/Loading.vue'
import TarjetaMovimiento from '../components/TarjetaMovimiento.vue'
import GrillaCasos from '../components/GrillaCasos'
/*
import auth from '../auth'
import Gestionometro from '../components/Gestionometro.vue'
import TarjetaTribunales from '../components/TarjetaTribunales.vue'
import TarjetaCaso from '../components/TarjetaCaso.vue'
import barChart from '../components/UI/barChart.vue'
import InfoCard from '../components/UI/InfoCard.vue'
import { Notify, QDialog } from 'quasar'
import MovimientosPendientes from '../components/MovimientosPendientes.vue'
import CasosPorTipo from '../components/CasosPorTipo'
*/

export default {
  components: {
    /*
    MovimientosPendientes,
    TarjetaTribunales,
    TarjetaCaso,
    Gestionometro,
    QDialog,
    barChart,
    InfoCard,
    CasosPorTipo,
    */
    TarjetaMovimiento,
    Loading,
    GrillaCasos
  },
  name: 'PageIndex',
  data () {
    return {
      // Data home nuevo
      TiposMov: [],
      filtrar: false,
      cantJudiciales: 0,
      cantExtrajudiciales: 0,
      numCasosJudiciales: [],
      numCasosExtrajudiciales: [],
      labelsJudiciales: [],
      labelsExtrajudiciales: [],
      coloresJudiciales: [],
      coloresExtrajudiciales: [],
      movimientosJudicialesPerentorios: [],
      movimientosJudicialesOtros: [],
      movimientosExtrajudicialesPerentorios: [],
      movimientosExtrajudicialesOtros: [],
      loadingGraphic: true,
      // Data home viejo
      Perentorios: [],
      GestionEstudio: [],
      Juzgados: [],
      movimientoAlta: {},
      cargandoMovimientos: true,
      cargandoCasos: true,
      Casos: [],
      alta: false,
      nuevoMovimiento: {
        IdUsuario: 0,
        TiposMov: [],
        TipoMov: 0,
        UsuariosEstudio: [],
        IdEstudio: 0,
        Detalle: '',
        FechaEsperada: '',
        FechaAlta: new Date().toISOString(),
        colorSeleccionado: '',
        Caso: 0,
        Objetivo: ''
      },
      modalAlta: false,
      modalCasos: false,
      coloresDocDoc: [
        {
          label: 'Perentorio',
          value: 'negative'
        },
        {
          label: 'Gestión Estudio',
          value: 'primary'
        },
        {
          label: 'Gestión Externa',
          value: 'warning'
        },
        {
          label: 'Finalizado',
          value: 'positive'
        }
      ],
      tareas: false,
      movimientos: []
    }
  },
  created () {
    if (typeof auth.UsuarioLogueado.IdRol !== 'undefined') {
      this.$router.push({ path: '/Maps' })
    }
    this.tareasPendientes()
    /*
    this.Responsable = auth.UsuarioLogueado
    const r = auth.UsuarioLogueado
    this.IdResponsable = r.IdUsuario
    this.nuevoMovimiento.Responsable = {
      label: `${r.Apellidos}, ${r.Nombres}`,
      value: r.IdUsuario
    }
    this.IdEstudio = r.IdEstudio
    // Pido los tipos de movimiento del estudio
    request.Get(`/estudios/${this.IdEstudio}/tipos-movimiento`, {}, (r) => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.TiposMov = r
      } else {
        this.$q.notify(
          'No hay tipos de movimiento disponibles para este estudio'
        )
      }
    })
    request.Get('/movimientos/movimientos-del-dia', {}, (r) => {
      if (!r.Error) {
        // Guardo los numeros de los casos por juzgados
        const CantCasos = Object.values(
          JSON.parse(r[r.length - 1].JuzgadosCasos)
        )
        // Ordeno CantCasos por Cantidad de mayor a menor
        CantCasos.sort(function (a, b) {
          if (a.Cantidad < b.Cantidad) {
            return 1
          } else if (a.Cantidad > b.Cantidad) {
            return -1
          } else return 0
        })

        // Asigno los valores para cada array necesario para los graficos
        CantCasos.forEach((item) => {
          if (item.Modo === 'J') {
            // Va al grafico judicial
            this.numCasosJudiciales.push(item.Cantidad)
            this.labelsJudiciales.push(item.Juzgado.toString())
            this.coloresJudiciales.push(this.randomRGBA('J'))
          } else {
            // Va al grafico extrajudicial
            this.numCasosExtrajudiciales.push(item.Cantidad)
            this.labelsExtrajudiciales.push(item.Juzgado.toString())
            this.coloresExtrajudiciales.push(this.randomRGBA('E'))
          }
        })

        // Cargo en dos variables la cantidad de casos judiciales y extrajudiciales, que vienen en el ultimo elemento del array respuesta
        this.cantJudiciales = r[r.length - 1].CantJudiciales
        this.cantExtrajudiciales = r[r.length - 1].CantExtrajudiciales

        // Ya cargo la cantidad de casos, doy permiso a mostrar el gráfico
        this.loadingGraphic = false
        // Proceso data por cada movimiento con vencimiento en la fecha actual
        r.slice(0, r.length - 1).forEach((item) => {
          // Creo el objeto CasoCompleto
          const CasoCompleto = {
            Caratula: item.Caratula,
            Carpeta: item.Carpeta,
            Estado: item.Estado,
            FechaAlta: item.FechaAlta,
            IdCaso: item.IdCaso,
            IdEstadoCaso: item.IdEstadoCaso,
            IdJuzgado: item.IdJuzgado,
            IdNominacion: item.IdNominacion,
            IdOrigen: item.IdOrigen,
            IdTipoCaso: item.IdTipoCaso,
            Juzgado: item.Juzgado,
            ModoGestion: item.ModoGestion,
            Movimientos: {}, // Se llena en TarjetaCaso
            Nominacion: item.Nominacion,
            NroExpediente: item.NroExpediente,
            Observaciones: item.Observaciones,
            Tipo: 'P',
            UsuariosCaso: {} // Hay que corregir en el dsp porque no esta trayendo los UsuariosCaso
          }
          // Creo el objeto movimiento
          const movimiento = {
            IdMovimientoCaso: item.IdMovimientoCaso,
            Color: item.Color,
            Objetivo: item.Objetivo,
            Caso: item.Caratula,
            NroExpediente: item.NroExpediente,
            Juzgado: item.Juzgado,
            Nominacion: item.Nominacion,
            Detalle: item.Detalle,
            UsuarioResponsable: item.Apellidos + ', ' + item.Nombres,
            CasoCompleto: CasoCompleto,
            FechaAlta: item.FechaAlta,
            FechaEsperada: item.FechaEsperada,
            IdUsuarioResponsable: item.IdResponsable,
            IdTipoMov: item.IdTipoMov,
            TipoMovimiento: '',
            IdUsuario: item.IdUsuario
          }
          // Como el dsp me devuelve solo el id del TipoMov, cargo el TipoMovimiento desde el array de TiposMov comparando con el id
          this.TiposMov.forEach((tm) => {
            if (tm.IdTipoMov === movimiento.IdTipoMov) {
              movimiento.TipoMovimiento = tm.TipoMovimiento
            }
          })
          // Evaluo si el nuevo objeto movimiento va en perentorios judiciales o en perentorios extrajudiciales
          if (!item.FechaRealizado) {
            if (item.ModoGestion === 'J') {
              if (item.Color === 'negative') {
                this.movimientosJudicialesPerentorios.push(movimiento)
              } else {
                this.movimientosJudicialesOtros.push(movimiento)
              }
            } else {
              if (item.Color === 'negative') {
                this.movimientosExtrajudicialesPerentorios.push(movimiento)
              } else {
                this.movimientosExtrajudicialesOtros.push(movimiento)
              }
            }
          }
        })

        this.movimientosJudicialesOtros.sort(function (a, b) {
          if (a.Color > b.Color) {
            return 1
          } else if (a.Color < b.Color) {
            return -1
          } else {
            return 0
          }
        })
        this.movimientosJudicialesOtros.sort(function (a, b) {
          if (a.Nominacion > b.Nominacion) {
            return 1
          } else if (a.Nominacion < b.Nominacion) {
            return -1
          } else {
            return 0
          }
        })
        this.movimientosExtrajudicialesOtros.sort(function (a, b) {
          if (a.Color > b.Color) {
            return 1
          } else if (a.Color < b.Color) {
            return -1
          } else {
            return 0
          }
        })
        this.movimientosExtrajudicialesOtros.sort(function (a, b) {
          if (a.Nominacion > b.Nominacion) {
            return 1
          } else if (a.Nominacion < b.Nominacion) {
            return -1
          } else {
            return 0
          }
        })
      }
    })
    */
  },
  watch: {
    'nuevoMovimiento.FechaEsperada' (val) {
      this.$nextTick(() => {
        if (!this.$refs.inputFechaEsperada.innerErrorMessage) {
          this.$refs.inputFechaEsperada.innerError = false
        }
      })
    },
    'nuevoMovimiento.FechaAlta' (val) {
      this.$nextTick(() => {
        if (!this.$refs.inputFechaAlta.innerErrorMessage) {
          this.$refs.inputFechaAlta.innerError = false
        }
      })
    }
  },
  computed: {
    /*
    pieData() {
      const data = {
        datasets: [
          {
            data: this.numCasosJuzgados,
            backgroundColor: this.coloresJuzgados,
          },
        ],
        labels: this.labelsJuzgados,
      };
      return data;
    },
    barOptions () {
      return {
        responsive: true,
        plugins: {
          datalabels: {
            color: '#fff'
          }
        }
      }
    },
    barDataJudiciales () {
      const data = {
        datasets: [
          {
            data: this.numCasosJudiciales,
            backgroundColor: this.coloresJudiciales
          }
        ],
        labels: this.labelsJudiciales
      }
      return data
    },
    barDataExtrajudiciales () {
      const data = {
        datasets: [
          {
            data: this.numCasosExtrajudiciales,
            backgroundColor: this.coloresExtrajudiciales
          }
        ],
        labels: this.labelsExtrajudiciales
      }
      return data
    },
    opcionesTipoMov () {
      let result = []
      if (
        this.modalAlta &&
        this.nuevoMovimiento.TiposMov &&
        this.nuevoMovimiento.TiposMov.length
      ) {
        result = this.nuevoMovimiento.TiposMov.map((t) => ({
          label: t.TipoMovimiento,
          value: t.IdTipoMov
        }))
      }
      return result
    },
    opcionesResponsable () {
      let result = []
      if (
        this.nuevoMovimiento.UsuariosEstudio &&
        this.nuevoMovimiento.UsuariosEstudio.length
      ) {
        result = this.nuevoMovimiento.UsuariosEstudio.map((t) => ({
          label: `${t.Apellidos}, ${t.Nombres}`,
          value: t.IdUsuario
        }))
      }
      return result
    }
    */
  },
  methods: {
    /*
    llamarCambioGrafico(payload) {
      this.loadingGraphic = true;

      let cambiarVistaGrafico = new Promise((resolve, reject) => {
        if (!this.huboCambioGrafico) {
          this.huboCambioGrafico = true;
          const temp_data = [];
          const temp_labels = [];
          const temp_colores = [];

          if (payload === "J") {
            // Utilizando el registro de datos del grafico, elimino los elementos extrajudiciales del grafico
            for (
              let index = 0;
              index < this.registroDatosGrafico.length;
              index++
            ) {
              if (this.registroDatosGrafico[index] === "J") {
                temp_data.push(this.numCasosJuzgados[index]);
                temp_labels.push(this.labelsJuzgados[index]);
                temp_colores.push(this.coloresJuzgados[index]);
              }
            }
            this.numCasosJuzgados = temp_data;
            this.labelsJuzgados = temp_labels;
            this.coloresJuzgados = temp_colores;
            resolve("OK");
          } else {
            // Utilizando el registro de datos del grafico, elimino los elementos judiciales del grafico
            for (
              let index = 0;
              index < this.registroDatosGrafico.length;
              index++
            ) {
              if (this.registroDatosGrafico[index] === "E") {
                temp_data.push(this.numCasosJuzgados[index]);
                temp_labels.push(this.labelsJuzgados[index]);
                temp_colores.push(this.coloresJuzgados[index]);
              }
            }
            this.numCasosJuzgados = temp_data;
            this.labelsJuzgados = temp_labels;
            this.coloresJuzgados = temp_colores;
            resolve("OK");
          }
        } else {
          this.huboCambioGrafico = false;

          this.numCasosJuzgados = this.copiaNumCasosJuzgados;
          this.labelsJuzgados = this.copiaLabelsJuzgados;
          this.coloresJuzgados = this.copiaColoresJuzgados;

          resolve("OK");
        }
      });

      cambiarVistaGrafico.then((res) => {
        if (res === "OK") {
          this.loadingGraphic = false;
        } else {
          console.log("Hubo algun error");
        }
      });
    },
    */
    tareasPendientes () {
      request.Get(`/casos/0/movimientos?Offset=0&Cadena=dWz6H78mpQ`, {}, t => {
        if (t.Error) {
          this.$q.notify(t.Error)
        } else {
          if (t.length === 0) {
            return
          }
          let idcasos = []
          t.forEach(m => {
            m.Acciones = JSON.parse(m.Acciones || '[]')
            console.log(m)
            this.movimientos.push(m)
            if (idcasos.indexOf(m.IdCaso) === -1) {
              idcasos.push(m.IdCaso)
            }
          })
          if (!idcasos.length) {
            return
          }
          this.tareas = true
          request.Get(`/objetivos?IdsCaso=[${idcasos}]`, {}, r => {
            if (!r.Error) {
              this.movimientos.forEach(c => {
                c.ObjetivosCaso = r[c.IdCaso]
              })
            }
          })
        }
      })
    }/* ,
    randomNumber (sup, inf) {
      const num = Math.random() * sup + inf
      const result = Math.floor(num)
      return result
    },
    randomRGBA (modo) {
      if (modo === 'J') {
        // base: 46, 134, 193
        const red = this.randomNumber(30, 0) + ', '
        const green = this.randomNumber(30, 0) + ', '
        const blue = this.randomNumber(255, 120) + ', '
        return 'rgba(' + red + green + blue + '0.8)'
      } else {
        // base 203, 67, 53
        const red = this.randomNumber(255, 120) + ', '
        const green = this.randomNumber(30, 0) + ', '
        const blue = this.randomNumber(30, 0) + ', '
        return 'rgba(' + red + green + blue + '0.8)'
      }
    },
    abrirTribunales (payload) {
      if (payload === 'J') {
        this.$router.push('/Tribunales?p=1')
      } else {
        this.$router.push('/Tribunales?p=2')
      }
    },
    filtrarGestionEstudio (juzgado) {
      return this.GestionEstudio.filter((m) => m.Juzgado === juzgado)
    },
    formatearFecha (fecha) {
      if (fecha) {
        return fecha.split('T')[0]
      } else return null
    },
    parseDateTime (fecha) {
      if (fecha !== null) {
        const fh = fecha.split(' ')
        const amd = fh[0].split('-')
        const hms = fh[1].split(':')
        return `${amd[2]}/${amd[1]}/${amd[0]} ${hms[0]}:${hms[1]}`
      } else return null
    },
    parseDate (fecha) {
      if (fecha !== null) {
        return this.parseDateTime(fecha).split(' ')[0]
      } else return null
    },
    isMobile () {
      if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
          navigator.userAgent
        )
      ) {
        return true
      } else {
        return false
      }
    },
    realizarMovimiento (movimiento, modo) {
      request.Put(
        `/movimientos/${movimiento.IdMovimientoCaso}/realizar`,
        {},
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            switch (modo) {
              case 'PJ':
                this.movimientosJudicialesPerentorios.splice(
                  this.movimientosJudicialesPerentorios.indexOf(movimiento),
                  1
                )
              // eslint-disable-next-line no-fallthrough
              case 'GJ':
                this.movimientosJudicialesOtros.splice(
                  this.movimientosJudicialesOtros.indexOf(movimiento),
                  1
                )
              // eslint-disable-next-line no-fallthrough
              case 'PE':
                this.movimientosExtrajudicialesPerentorios.splice(
                  this.movimientosExtrajudicialesPerentorios.indexOf(
                    movimiento
                  ),
                  1
                )
              // eslint-disable-next-line no-fallthrough
              case 'GE':
                this.movimientosExtrajudicialesOtros.splice(
                  this.movimientosExtrajudicialesOtros.indexOf(movimiento),
                  1
                )
            }
            this.$q.notify({
              color: 'green',
              message: `Se marcó como realizado el movimiento '${movimiento.Detalle}'`
            })
            this.movimientoAlta = movimiento
            this.altaMovimiento()
          }
        }
      )
    },
    altaMovimiento () {
      this.modalAlta = true
      this.nuevoMovimiento.IdEstudio = auth.UsuarioLogueado.IdEstudio
      request.Get(
        `/estudios/${this.nuevoMovimiento.IdEstudio}/usuarios`,
        {},
        (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else if (r.length) {
            this.nuevoMovimiento.UsuariosEstudio = r
          }
        }
      )
      request.Get(
        `/estudios/${this.nuevoMovimiento.IdEstudio}/tipos-movimiento`,
        {},
        (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else if (r.length) {
            this.nuevoMovimiento.TiposMov = r
            r.forEach((tm) => {
              if (Number(tm.IdTipoMov) === this.movimientoAlta.IdTipoMov) {
                this.nuevoMovimiento.TipoMov = {
                  label: tm.TipoMovimiento,
                  value: tm.IdTipoMov
                }
              }
            })
          } else {
            this.$q.notify(
              'No hay tipos de movimiento disponibles para este estudio'
            )
          }
        }
      )
    },
    cancelarAlta () {
      this.modalAlta = false
      this.nuevoMovimiento = {
        IdUsuario: 0,
        TiposMov: [],
        TipoMov: 0,
        UsuariosEstudio: [],
        IdEstudio: 0,
        Detalle: '',
        FechaEsperada: '',
        FechaAlta: '',
        colorSeleccionado: { label: 'Gestión Estudio', value: 'primary' },
        Caso: 0,
        Objetivo: '',
        Responsable: {}
      }
    },
    guardarMovimiento () {
      if (!this.nuevoMovimiento.Detalle) {
        this.$q.notify('Debe ingresar el detalle del movimiento.')
      }
      if (!this.nuevoMovimiento.TipoMov) {
        this.$q.notify('Debe elegir un tipo de movimiento.')
      }
      if (this.nuevoMovimiento.colorSeleccionado === '') {
        this.$q.notify('Debe seleccionar un estado de gestión')
      } else {
        this.Casos.forEach((c) => {
          if (this.movimientoAlta.Caso === c.Caratula) {
            this.nuevoMovimiento.IdCaso = c.IdCaso
            this.nuevoMovimiento.NroExpediente = c.NroExpediente
          }
        })
        let fEsperada = this.nuevoMovimiento.FechaEsperada
        let fAlta = this.nuevoMovimiento.FechaAlta
        if (
          this.nuevoMovimiento.FechaEsperada &&
          this.nuevoMovimiento.FechaEsperada.split('-')[2].length === 4
        ) {
          fEsperada = `${this.nuevoMovimiento.FechaEsperada.split('-')[2]}-${
            this.nuevoMovimiento.FechaEsperada.split('-')[1]
          }-${this.nuevoMovimiento.FechaEsperada.split('-')[0]}`
        }
        if (
          this.nuevoMovimiento.FechaAlta &&
          this.nuevoMovimiento.FechaAlta.split('-')[2].length === 4
        ) {
          fAlta = `${this.nuevoMovimiento.FechaAlta.split('-')[2]}-${
            this.nuevoMovimiento.FechaAlta.split('-')[1]
          }-${this.nuevoMovimiento.FechaAlta.split('-')[0]}`
        }
        const movimiento = {
          IdResponsable: this.nuevoMovimiento.Responsable.value,
          Detalle: this.nuevoMovimiento.Detalle,
          IdCaso: this.nuevoMovimiento.IdCaso,
          FechaEsperada: this.formatearFecha(fEsperada),
          FechaAlta: this.formatearFecha(fAlta),
          FechaRealizado: null,
          IdTipoMov: this.nuevoMovimiento.TipoMov.value,
          Cuaderno: this.movimientoAlta.Cuaderno,
          Color: this.nuevoMovimiento.colorSeleccionado.value,
          Multimedia: this.Multimedia
        }
        request.Post('/movimientos', movimiento, (r) => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            this.modalAlta = false
            this.$q.notify({
              color: 'green',
              message: `El movimiento "${movimiento.Detalle}" se dio de alta correctamente`
            })
            if (this.caso) {
              if (this.CasosPendientes.indexOf(this.caso) - -1 === 0) {
                this.CasosPendientes.push(this.caso)
              }
            }
            movimiento.TipoMovimiento = this.nuevoMovimiento.TipoMov.label
            movimiento.Caso = this.movimientoAlta.Caso
            movimiento.CasoCompleto = this.caso
            request.Get(
              `/casos/${this.nuevoMovimiento.IdCaso}/movimientos`,
              {},
              (r) => {
                if (!r.Error) {
                  var contador = true
                  r.forEach((m) => {
                    if (m.Detalle === movimiento.Detalle) {
                      movimiento.IdMovimientoCaso = m.IdMovimientoCaso
                      if (contador === true && !movimiento.FechaRealizado) {
                        if (this.movimientoAlta.Objetivo) {
                          var objetivo = {}
                          this.movimientoAlta.CasoCompleto.Objetivos.forEach(
                            (o) => {
                              if (o.Objetivo === this.movimientoAlta.Objetivo) {
                                objetivo = o
                              }
                            }
                          )
                          request.Post(
                            `/movimientos/${movimiento.IdMovimientoCaso}/asociar-objetivo/${objetivo.IdObjetivo}`,
                            {},
                            (r) => {
                              movimiento.Objetivo = objetivo.Objetivo
                              this.MovimientosPendientes.push(movimiento)
                              this.Movimientos.push(movimiento)
                              contador = false
                            }
                          )
                        } else {
                          this.MovimientosPendientes.push(movimiento)
                          this.Movimientos.push(movimiento)
                          contador = false
                        }
                      }
                    }
                  })
                  this.altaFinalizada = true
                  this.alta = false
                  this.movimientoAlta = {}
                  this.nuevoMovimiento.IdUsuario = 0
                  this.nuevoMovimiento.TiposMov = []
                  this.nuevoMovimiento.TipoMov = 0
                  this.nuevoMovimiento.UsuariosEstudio = []
                  this.nuevoMovimiento.IdEstudio = 0
                  this.nuevoMovimiento.IdResponsable = 0
                  this.nuevoMovimiento.Detalle = ''
                  this.nuevoMovimiento.FechaAlta = new Date().toISOString()
                  this.nuevoMovimiento.FechaEsperada = null
                  this.nuevoMovimiento.colorSeleccionado = 'primary'
                  this.nuevoMovimiento.Caso = 0
                }
              }
            )
          }
        })
      }
    },
    verCasos () {
      this.modalCasos = true
    }
    */
  }
}
</script>

<style>
.tarjeta-tarea .q-card > .q-item {
  background : -moz-linear-gradient(50% 100% 90deg,rgba(255, 255, 255, 0.5) 0%,rgba(253, 253, 253, 0.5) 30.79%,rgba(244, 244, 244, 0.5) 49.03%,rgba(230, 230, 230, 0.5) 64%,rgba(210, 210, 210, 0.5) 77.19%,rgba(184, 184, 184, 0.5) 89.1%,rgba(153, 153, 153, 0.5) 100%) !important;
  background : -webkit-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%) !important;
  background : -webkit-gradient(linear,50% 100% ,50% 0% ,color-stop(0,rgba(255, 255, 255, 0.5) ),color-stop(0.3079,rgba(253, 253, 253, 0.5) ),color-stop(0.4903,rgba(244, 244, 244, 0.5) ),color-stop(0.64,rgba(230, 230, 230, 0.5) ),color-stop(0.7719,rgba(210, 210, 210, 0.5) ),color-stop(0.891,rgba(184, 184, 184, 0.5) ),color-stop(1,rgba(153, 153, 153, 0.5) )) !important;
  background : -o-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%) !important;
  background : -ms-linear-gradient(90deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%) !important;
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#999999' ,GradientType=0)";
  background : linear-gradient(0deg, rgba(255, 255, 255, 0.5) 0%, rgba(253, 253, 253, 0.5) 30.79%, rgba(244, 244, 244, 0.5) 49.03%, rgba(230, 230, 230, 0.5) 64%, rgba(210, 210, 210, 0.5) 77.19%, rgba(184, 184, 184, 0.5) 89.1%, rgba(153, 153, 153, 0.5) 100%) !important;
  filter: alpha(opacity=50) progid:DXImageTransform.Microsoft.Alpha(opacity=50) progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999',endColorstr='#FFFFFF' , GradientType=0) !important;
  color: black !important;
}

.titulo {
  padding: 1em;
  color: white;
  font-weight: 500;
  border-radius: 10px 10px 0 0;
}
.tarjeta {
  background-color: white;
  border-radius: 10px;
  /* margin-bottom:.5em */
}
.contenidoTarjeta {
  padding: 0 1em 1em 1em;
}
.perentorios_container {
  background-color: rgba(0, 0, 0, 0.1);
  width: 48% !important;
  margin: 0 auto;
  border-radius: 20px;
}
.movimiento_container {
  width: 45vw;
}
.section_title {
  margin-bottom: 20px;
  transition: all 0.18s ease-in-out;
}
.section_title:hover {
  color: teal;
  border-radius: 50px;
}
@media screen and (max-width: 1000px) {
  .perentorios_container {
    background-color: rgba(0, 0, 0, 0.1);
    width: 100% !important;
    margin: 10px 0;
    border-radius: 20px;
  }
  .movimiento_container {
    width: 94vw;
    margin: 10px 0;
  }
}
@media screen and (max-width: 700px) {
  .movimiento_container {
    width: 80vw;
    margin: 10px 0;
  }
}

</style>
