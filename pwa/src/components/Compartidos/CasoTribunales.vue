<template>
  <div>
    <q-item>
      <q-item-section side>
        <q-btn
          icon="add"
          color="gray"
          rounded
          flat
          outline
          @click="altaMovimiento(caso)"
        >
          <q-tooltip anchor="top middle" self="bottom middle" :offset="[10, 10]">
            Nuevo Movimiento
          </q-tooltip>
        </q-btn>
      </q-item-section>
    </q-item>

    <!-- Alta de Movimiento -->
    <q-dialog v-model="modalAlta" v-if="modalAlta" no-backdrop-dismiss no-esc-dismiss>
      <NuevoMovimiento
        v-if="modalAlta"
        :movimientoAlta="this.movimientoAlta"
        :Casos="this.Casos"
        :caso="this.caso"
        :IdCaso="this.caso.IdCaso"
        @cancelarAlta="cancelarAlta"
        @guardarmovimiento="guardarMovimiento"
        @nuevochat="nuevoChat"
        @enviarmensaje="enviarMensaje"
      />
    </q-dialog>

    <q-expansion-item
      v-for="c of Object.keys(Cuadernos)"
      :key="c"
      expand-separator
      icon="folder"
      :label="c"
      :caption="`Activos: ${Cuadernos[c].Movimientos ? Cuadernos[c].Movimientos.length : 0} | Realizados: ${Cuadernos[c].MovimientosRealizados ? Cuadernos[c].MovimientosRealizados.length : 0}`"
    >
      <TarjetaTribunales
        v-for="movimiento in movimientosDelCaso(Cuadernos[c].Movimientos)"
        :key="movimiento.IdMovimientoCaso"
        :movimiento="movimiento"
        :idChat="parseInt(idChat)"
        :datosChat="datosChat"
        :ultimosMovimientos="ultimosMovimientos(movimiento).slice(0, 3)"
        @mostrarObjetivos="mostrarObjetivos(movimiento)"
        @realizarMovimiento="realizarMovimiento(movimiento, caso.IdCaso)"
        style="margin-bottom:0.6rem;"
      />

      <q-separator />

      <div
        v-if="Cuadernos[c].MovimientosRealizados.length"
        class="full-width text-center"
      >
        Movimientos Finalizados
      </div>

      <TarjetaTribunales
        v-for="movimiento in movimientosDelCaso(Cuadernos[c].MovimientosRealizados)"
        :key="movimiento.IdMovimientoCaso"
        :movimiento="movimiento"
        :idChat="parseInt(idChat)"
        :datosChat="datosChat"
        :ultimosMovimientos="ultimosMovimientos(movimiento).slice(0, 3)"
        @mostrarObjetivos="mostrarObjetivos(movimiento)"
        @realizarMovimiento="realizarMovimiento(movimiento, caso.IdCaso)"
        style="margin-bottom:0.6rem;"
      />
    </q-expansion-item>

    <q-separator
      v-if="Object.keys(Cuadernos).length"
      size="5px"
      color="grey"
    />

    <div
      v-if="!movimientosDelCaso(computedMovimientos).length"
      class="text-center"
    >
      Aun no hay movimientos en este caso
    </div>

    <!-- Contenido del caso (Movimientos) -->
    <TarjetaTribunales
      v-for="movimiento in movimientosDelCaso(computedMovimientos).filter(m => !m.Cuaderno)"
      :key="movimiento.IdMovimientoCaso"
      :movimiento="movimiento"
      :idChat="parseInt(idChat)"
      :datosChat="datosChat"
      :ultimosMovimientos="ultimosMovimientos(movimiento).slice(0, 3)"
      @mostrarObjetivos="mostrarObjetivos(movimiento)"
      @realizarMovimiento="realizarMovimiento(movimiento, caso.IdCaso)"
      style="margin-bottom:0.6rem;"
    />
    <div
      v-if="computedObjSinMovs.length"
      style="display: flex; align-items: center; flex-wrap: wrap; padding: 0 1rem 1rem 1rem;"
    >
      <q-icon icon="timeline" />
      <h1 style="font-size: 1.1rem; font-weight: 400;">Objetivos sin Movimientos:</h1>
      <q-chip
        clickable
        color="secondary"
        v-for="objetivo in ObjSinMovs"
        :key="objetivo.IdObjetivo"
        @click="mostrarMovObjLibre(objetivo.IdObjetivo)"
        style="margin-left: 1rem"
      >
        {{ objetivo.Objetivo }}
        <q-tooltip>
          Ver Movimientos
        </q-tooltip>
      </q-chip>
    </div>

    <!-- MODAL OBJETIVOS LIBRES PARA SELECCIONAR -->
    <objetivos
      :dialog.sync="modalObjetivos"
      :objetivos="Objetivos"
      @vincularObjetivo="asociarObjetivo"
      @eliminarObjetivo="eliminarObjetivo"
      @nuevoObjetivo="nuevoObjetivo"
      @editarObjetivo="editarObjetivo"
    />
    <!-- MODAL PARA VER EL OBJETIVO DE UN CASO -->
    <objetivos-movimiento
      :dialog.sync="modalObjetivosMovimiento"
      :objetivo="this.movimientos.filter(mov => mov.IdMovimientoCaso === this.idMovimientoSeleccionado)[0]"
      :id="caso.IdCaso"
      @desasociarObjetivo="desasociarObjetivo"
    />
    <!-- MODAL PARA CREAR NUEVO OBJETIVO -->
    <nuevo-objetivo :dialog.sync="modalNuevoObjetivo" @agregarObjetivo="agregarObjetivo" />
    <!-- MODAL DE EDICION DE UN OBJETIVO -->
    <editar-objetivo :dialog.sync="modalEditar" :objetivo="objetivoEditar" @guardarObjetivo="guardarObjetivo" />
    <!-- MODAL PARA VER MOVIMIENTOS DE UN OBJETIVO LIBRE -->
    <q-dialog v-model="modalObjetivoLibre">
      <MovimientosCaso
        :IdCaso="caso.IdCaso"
        :IdObjetivo="IdObjetivoLibre"
        @cerrar="modalObjetivoLibre = false"
      />
    </q-dialog>
    <!-- Tarjeta de caso -->
    <q-dialog v-model="modalCaso" v-if="modalCaso">
      <q-card>
        <q-item style="background-color: black;">
          <span class="q-subheading" style="color:white;">Vista previa del caso</span>
        </q-item>
        <TarjetaCaso v-if="modalCaso" :caso="caso" />
        <div style="display: flex; justify-content: flex-end; margin-right: 40px">
          <q-btn flat color="primary" label="Cerrar" @click="modalCaso = false" />
        </div>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import request from '../../request'
import { Notify } from 'quasar'
import TarjetaTribunales from '../TarjetaTribunales'
import NuevoMovimiento from '../NuevoMovimiento'
import Objetivos from './Objetivos'
import ObjetivosMovimiento from './ObjetivosMovimiento'
import NuevoObjetivo from './NuevoObjetivo'
import EditarObjetivo from './EditarObjetivo'
import TarjetaCaso from '../TarjetaCaso.vue'
import MovimientosCaso from '../Caso/MovimientosCaso'

export default {
  name: 'CasoTribunales',
  props: {
    caso: {
      type: Object,
      default: () => {}
    },
    movimientos: {
      type: Array,
      default: () => []
    },
    datosChat: {
      type: Object,
      default: () => {}
    },
    idChat: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      Objetivos: [],
      ObjSinMovs: [],
      movimientosRealizados: [],
      idMovimientoSeleccionado: '',
      modalAlta: false,
      movimientoAlta: {},
      Casos: {},
      modalObjetivos: false,
      modalObjetivosMovimiento: false,
      modalNuevoObjetivo: false,
      modalEditar: false,
      modalObjetivoLibre: false,
      objetivoEditar: {},
      modalCaso: false,
      IdObjetivoLibre: 0
    }
  },
  components: {
    TarjetaTribunales,
    NuevoMovimiento,
    Objetivos,
    NuevoObjetivo,
    ObjetivosMovimiento,
    EditarObjetivo,
    TarjetaCaso,
    MovimientosCaso
  },
  created () {
    this.traerObjetivos()

    request.Get(`/casos/${this.caso.IdCaso}/movimientos-realizados`, {}, (r) => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.movimientosRealizados = r
      }
    })
  },
  computed: {
    computedMovimientos () {
      return this.movimientos
    },
    computedObjSinMovs () {
      return this.ObjSinMovs
    },
    Cuadernos () {
      let cuadernos = {}

      this.movimientos.forEach(m => {
        if (m.Cuaderno) {
          if (!cuadernos[m.Cuaderno]) {
            if (m.Color === 'positive') {
              cuadernos[m.Cuaderno] = {
                MovimientosRealizados: [m],
                Movimientos: []
              }
            } else {
              cuadernos[m.Cuaderno] = {
                MovimientosRealizados: [],
                Movimientos: [m]
              }
            }
          } else {
            if (m.Color === 'positive') {
              cuadernos[m.Cuaderno].MovimientosRealizados.push(m)
            } else {
              cuadernos[m.Cuaderno].Movimientos.push(m)
            }
          }
        }
      })

      return cuadernos
    }
  },
  methods: {
    movimientosDelCaso (movimientos) {
      var respuesta = []
      if (!movimientos) { return [] }
      movimientos.forEach((m) => {
        if (m.Color === 'negative') {
          m.Orden = 1
        }
        if (m.Color === 'primary') {
          m.Orden = 2
        }
        if (m.Color === 'warning') {
          m.Orden = 3
        }
        respuesta.push(m)
      })
      return respuesta.sort(function (a, b) {
        if (a.Color === 'positive') {
          return 1
        }
        if (b.Color === 'positive') {
          return -1
        }
        if (!a.Objetivo && !b.Objetivo) {
          if (a.Orden > b.Orden) {
            return 1
          }
          if (a.Orden < b.Orden) {
            return -1
          } else return 0
        }
        if (!a.Objetivo) {
          return 1
        }
        if (!b.Objetivo) {
          return -1
        }
        if (a.Objetivo > b.Objetivo) {
          return 1
        }
        if (a.Objetivo < b.Objetivo) {
          return -1
        } else {
          if (a.Orden > b.Orden) {
            return 1
          }
          if (a.Orden < b.Orden) {
            return -1
          }
        }
        return 0
      })
    },
    ultimosMovimientos (movimiento) {
      let respuesta = []
      this.movimientosRealizados.forEach((m) => {
        if (m.Objetivo === movimiento.Objetivo && m.FechaRealizado) {
          respuesta.push(m)
        }
      })
      return respuesta.sort(function (m1, m2) {
        if (m1.FechaRealizado > m2.FechaRealizado) {
          return -1
        } else if (m1.FechaRealizado < m2.FechaRealizado) {
          return 1
        } else return 0
      })
    },
    realizarMovimiento (movimiento, IdCaso) {
      this.movimientos = this.movimientos.filter(c => c.IdMovimientoCaso !== movimiento.IdMovimientoCaso)
      this.movimientosRealizados.push(movimiento)
      request.Put(
        `/movimientos/${movimiento.IdMovimientoCaso}/realizar`,
        {},
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.$q.notify({
              color: 'green',
              message: `Se marcó como realizado el movimiento '${movimiento.Detalle}'`
            })
          }
          this.movimientoAlta = movimiento
          this.altaMovimiento(this.caso)
        }
      )
    },
    traerObjetivos () {
      request.Get(`/objetivos?IdsCaso=[${this.caso.IdCaso}]`, {}, r => {
        if (!r.Error) {
          this.Objetivos = r[this.caso.IdCaso]
          this.Objetivos.forEach(objetivo => {
            if (this.movimientos.findIndex(mov => mov.IdObjetivo === objetivo.IdObjetivo) === -1) {
              this.ObjSinMovs.push(objetivo)
            }
          })
        } else {
          Notify.create('Error al traer todos los objetivos')
        }
      })
    },
    mostrarObjetivos (movimiento) {
      this.idMovimientoSeleccionado = movimiento.IdMovimientoCaso
      if (movimiento.Objetivo) {
        this.modalObjetivosMovimiento = true
      } else {
        this.modalObjetivos = true
      }
    },
    asociarObjetivo (objetivo) {
      request.Post(`/movimientos/${this.idMovimientoSeleccionado}/asociar-objetivo/${objetivo.IdObjetivo}`, {}, res => {
        if (!res.Error) {
          Notify.create('Objetivo asociado!')
          this.ObjSinMovs.splice(this.ObjSinMovs.findIndex(o => o.IdObjetivo === objetivo.IdObjetivo), 1)
          const i = this.movimientos.findIndex(f => f.IdMovimientoCaso === this.idMovimientoSeleccionado)
          if (i !== -1) {
            this.movimientos[i].IdObjetivo = objetivo.IdObjetivo
            this.movimientos[i].Objetivo = objetivo.Objetivo
            this.modalObjetivos = false
          }
        } else {
          Notify.create(res.Error)
        }
      })
    },
    desasociarObjetivo (payload) {
      request.Put(`/movimientos/${payload.idMov}/desasociar-objetivo/${payload.idObj}`, {}, res => {
        if (!res.Error) {
          Notify.create('Desasociado correctamente')
          this.ObjSinMovs.push({ IdObjetivo: payload.idObj, Objetivo: payload.obj })

          const i = this.movimientos.findIndex(m => m.IdMovimientoCaso === payload.idMov)
          this.movimientos[i].IdObjetivo = null
          this.movimientos[i].Objetivo = null
        } else {
          Notify.create(res.Error)
        }
      })
    },
    editarObjetivo (objetivo) {
      this.objetivoEditar = objetivo
      this.modalEditar = true
    },
    guardarObjetivo (objetivo) {
      let obj = { Objetivo: objetivo.Objetivo }
      request.Put(`/objetivos/${objetivo.IdObjetivo}`, obj, res => {
        if (!res.Error) {
          Notify.create('Objetivo editado correctamente')
        } else {
          Notify.create(res.Error)
        }
      })
    },
    eliminarObjetivo (objetivo) {},
    nuevoObjetivo () {
      this.modalNuevoObjetivo = true
    },
    agregarObjetivo (objetivo) {
      let nuevoObj = {
        FechaAlta: this.fechaActual(),
        IdCaso: this.caso.IdCaso,
        Objetivo: objetivo
      }
      request.Post(`/objetivos`, nuevoObj, res => {
        if (!res.error) {
          nuevoObj.IdObjetivo = res.IdObjetivo
          this.ObjSinMovs.push(nuevoObj)
          this.Objetivos.push(nuevoObj)
        } else {
          this.$q.notify(res.error)
        }
      })
    },
    altaMovimiento (caso) {
      this.modalAlta = true
      if (caso) {
        this.movimientoAlta.Caso = caso.Caratula
        this.Casos = [caso]
      }
    },
    cancelarAlta () {
      this.modalAlta = false
    },
    guardarMovimiento (movimiento) {
      this.modalAlta = false
      movimiento.IdCaso = this.caso.IdCaso
      movimiento.NroExpediente = this.caso.NroExpediente
      request.Get(`/casos/${movimiento.IdCaso}/movimientos`, {}, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          var contador = true
          r.forEach((m) => {
            if (m.Detalle === movimiento.Detalle) {
              movimiento.IdMovimientoCaso = m.IdMovimientoCaso
              if (contador === true && !movimiento.FechaRealizado) {
                if (this.movimientoAlta.Objetivo) {
                  var objetivo = {}
                  this.Objetivos.forEach((o) => {
                    if (o.Objetivo === this.movimientoAlta.Objetivo) {
                      objetivo = o
                    }
                  })
                  request.Post(
                    `/movimientos/${movimiento.IdMovimientoCaso}/asociar-objetivo/${objetivo.IdObjetivo}`,
                    {},
                    (r) => {
                      movimiento.Objetivo = objetivo.Objetivo
                      // this.MovimientosPendientes.push(movimiento)
                      this.movimientos.push(movimiento)
                      contador = false
                    }
                  )
                } else {
                  // this.MovimientosPendientes.push(movimiento)
                  this.movimientos.push(movimiento)
                  contador = false
                }
              }
            }
          })
          this.movimientoAlta = {}
        }
      })
    },
    enviarMensaje (Mensaje) {
      request.Post(`/mensajes/enviar`, Mensaje, r => {
        if (!r.Error) {
          Notify.create('Movimiento comunicado correctamente')
          const UltMsjLeido = r.IdMensaje
          request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {
            if (p.Error) {
              Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
            }
          })
        } else {
          Notify.create('Falló al comunicar el movimiento. Razon: ' + r.Error)
        }
      })
    },
    nuevoChat (NuevoChat, Mensaje) {
      request.Post(`/chats/crear`, NuevoChat, r => {
        if (!r.Error) {
          Notify.create('Nuevo chat creado!')
          Mensaje.IdChat = r.IdChat
          request.Post(`/mensajes/enviar`, Mensaje, q => {
            if (!q.Error) {
              Notify.create('Movimiento comunicado correctamente')
              const UltMsjLeido = q.IdMensaje
              request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {
                if (p.Error) {
                  Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
                }
              })
            } else {
              Notify.create('Falló al comunicar el movimiento. Razon: ' + q.Error)
            }
          })
        } else {
          Notify.create('Falló al comunicar el movimiento. Razon: ' + r.Error)
        }
      })
    },
    fechaActual () {
      var anoActual = new Date().getFullYear()
      var mesActual = new Date().getUTCMonth() + 1
      var diaActual = new Date().getDate()
      const fechaActual = `${anoActual}-${mesActual}-${diaActual}`
      var hoy = new Date()
      const tiempo = `${hoy.getHours()}:${hoy.getMinutes()}:${hoy.getSeconds()}`
      return `${fechaActual} ${tiempo}`
    },
    verCaso (caso) {
      this.modalCaso = true

      this.caso.Objetivos = this.Objetivos

      let respuesta = []
      this.movimientos.forEach((m) => {
        if (!m.FechaRealizado) {
          respuesta.push(m)
        }
      })
      respuesta.forEach(m => {
        m.ObjetivosCaso = this.Objetivos
      })
      this.caso.UltimosMovimientos = JSON.stringify(respuesta.sort(function (m1, m2) {
        if (m1.FechaAlta > m2.FechaAlta) {
          return -1
        } else if (m1.FechaAlta < m2.FechaAlta) {
          return 1
        } else return 0
      }))
    },
    mostrarMovObjLibre (id) {
      this.modalObjetivoLibre = true
      this.IdObjetivoLibre = id
    }
  }
}
</script>

<style scoped>
.expansion-caso {
  margin: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  transition: 200ms;
}
.caso_header {
  font-size:16px;
  font-weight:700;
}
</style>
