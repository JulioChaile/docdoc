<template>
  <div>
    <div class="text-bold">
      Agregar Movimientos
      <q-btn
        color="primary"
        round
        size="sm"
        @click="altaMovimiento(caso)"
      >
        +
        <q-tooltip>
          Nuevo Movimiento
        </q-tooltip>
      </q-btn>
    </div>

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
      <div
        v-for="movimiento in movimientosDelCaso(Cuadernos[c].Movimientos)"
        :key="movimiento.IdMovimientoCaso"
        class="flex"
      >
        <q-icon
          v-if="movimiento.Color === 'positive'"
          class="cursor-pointer"
          color="green"
          name="arrow_downward"
          size="lg"
          @click="cambiarPosicion(movimiento.IdMovimientoCaso, 'D')"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Enviar Abajo</q-tooltip>
        </q-icon>

        <div style="width: 90%;">
          <TarjetaTribunales
            :movimiento="movimiento"
            :idChat="parseInt(idChat)"
            :datosChat="datosChat"
            :ultimosMovimientos="ultimosMovimientos(movimiento).slice(0, 3)"
            @mostrarObjetivos="mostrarObjetivos(movimiento)"
            @realizarMovimiento="realizarMovimiento(movimiento, caso.IdCaso)"
            style="margin-bottom:0.6rem;"
          />
        </div>
      </div>
      

      <q-separator />

      <div
        v-if="Cuadernos[c].MovimientosRealizados.length"
        class="full-width text-center"
      >
        Movimientos Finalizados
      </div>

      <div
          v-for="movimiento in Cuadernos[c].MovimientosRealizados"
          :key="movimiento.IdMovimientoCaso"
          class="flex"
        >
          <q-icon
            v-if="movimiento.Color === 'positive'"
            class="cursor-pointer"
            color="green"
            name="arrow_upward"
            size="lg"
            @click="cambiarPosicion(movimiento.IdMovimientoCaso, 'U')"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Enviar Arriba</q-tooltip>
          </q-icon>

          <div style="width: 90%;">
            <TarjetaTribunales
              :movimiento="movimiento"
              :idChat="parseInt(idChat)"
              :datosChat="datosChat"
              :ultimosMovimientos="ultimosMovimientos(movimiento).slice(0, 3)"
              @mostrarObjetivos="mostrarObjetivos(movimiento)"
              @realizarMovimiento="realizarMovimiento(movimiento, caso.IdCaso)"
              style="margin-bottom:0.6rem;"
            />
          </div>
        </div>
    </q-expansion-item>

    <q-separator
      v-if="Object.keys(Cuadernos).length"
      size="5px"
      color="grey"
    />

    <div
      v-if="!movimientosDelCaso(computedMovimientos).length && !loading"
      class="text-center"
    >
      Aun no hay movimientos en este caso
    </div>

    <div v-if="loading" class="full-width row justify-center">
      <Loading />
    </div>

    <!-- Contenido del caso (Movimientos) -->

    <div
      v-if="movimientosDelCaso(computedMovimientos).filter(m => !m.Cuaderno && m.check).length"
      class="q-my-sm flex"
    >
      <q-btn class="q-mx-lg" dense color="positive" @click="masivo('fin')">Finalizar</q-btn>

      <q-select
        style="width:20%;"
        v-model="Cuaderno"
        :options="cuadernos"
        label="Cuaderno"
      />

      <q-btn class="q-ml-sm q-mr-lg" dense color="positive" @click="masivo('cuad')">Cambiar Cuaderno</q-btn>

      <q-select
        label="Tipo de Movimiento"
        v-model="TipoMovimiento"
        :options="opcionesTipoMov"
        style="width:20%;"
      />

      <q-btn class="q-ml-sm" dense color="positive" @click="masivo('tipo')">Cambiar Tipo</q-btn>
    </div>

    <div
      v-for="movimiento in movimientosDelCaso(computedMovimientos).filter(m => !m.Cuaderno && (m.Posicion === 'U' || m.Color !== 'positive'))"
      :key="movimiento.IdMovimientoCaso"
      class="flex"
    >
      <div class="q-mr-sm column">
        <q-checkbox
          v-model="movimiento.check"
          :false-value="undefined"
        >
        </q-checkbox>

        <q-icon
          v-if="movimiento.Color === 'positive'"
          class="cursor-pointer"
          color="green"
          name="arrow_downward"
          size="lg"
          @click="cambiarPosicion(movimiento.IdMovimientoCaso, 'D')"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Enviar Abajo</q-tooltip>
        </q-icon>
      </div>

      <div style="width: 90%;">
        <TarjetaTribunales
          :movimiento="movimiento"
          :idChat="parseInt(idChat)"
          :datosChat="datosChat"
          :ultimosMovimientos="ultimosMovimientos(movimiento).slice(0, 3)"
          @mostrarObjetivos="mostrarObjetivos(movimiento)"
          @realizarMovimiento="realizarMovimiento(movimiento, caso.IdCaso)"
          @duplicar="mov => movimientos.unshift(mov)"
          style="margin-bottom:0.6rem;"
        />
      </div>
    </div>

    <div
      v-for="movimiento in movimientosDelCaso(computedMovimientos).filter(m => !m.Cuaderno && m.Color === 'positive' && m.Posicion === 'D')"
      :key="movimiento.IdMovimientoCaso"
      class="flex"
    >
      <div class="q-mr-sm column">
        <q-checkbox
          v-model="movimiento.check"
          :false-value="undefined"
        >
        </q-checkbox>

        <q-icon
          v-if="movimiento.Color === 'positive'"
          class="cursor-pointer"
          color="green"
          name="arrow_upward"
          size="lg"
          @click="cambiarPosicion(movimiento.IdMovimientoCaso, 'U')"
        >
          <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Enviar Arriba</q-tooltip>
        </q-icon>
      </div>

      <div style="width: 90%;">
        <TarjetaTribunales
          :movimiento="movimiento"
          :idChat="parseInt(idChat)"
          :datosChat="datosChat"
          :ultimosMovimientos="ultimosMovimientos(movimiento).slice(0, 3)"
          @mostrarObjetivos="mostrarObjetivos(movimiento)"
          @realizarMovimiento="realizarMovimiento(movimiento, caso.IdCaso)"
          @duplicar="mov => movimientos.unshift(mov)"
          style="margin-bottom:0.6rem;"
        />
      </div>
    </div>

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
import auth from '../../auth'
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
import Loading from '../Loading'

export default {
  name: 'CasoTribunales',
  props: {
    caso: {
      type: Object,
      default: () => {}
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
      movimientos: [],
      Casos: {},
      modalObjetivos: false,
      modalObjetivosMovimiento: false,
      modalNuevoObjetivo: false,
      modalEditar: false,
      modalObjetivoLibre: false,
      objetivoEditar: {},
      modalCaso: false,
      IdObjetivoLibre: 0,
      loading: true,
      Cuaderno: '',
      TiposMov: [],
      TipoMovimiento: '',
      cuadernos: []
    }
  },
  components: {
    Loading,
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
    request.Get(`/casos/${this.caso.IdCaso}/movimientos-realizados`, {}, (r) => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.movimientosRealizados = r
        this.movimientosRealizados.forEach(m => {
          let acciones = []

          m.Acciones = JSON.parse(m.Acciones).filter(a => a.Accion)

          m.Acciones.forEach(a => {
            const i = acciones.findIndex(ac => ac.IdMovimientoAccion === a.IdMovimientoAccion)

            if (i === -1) {
              acciones.push(a)
            }
          })

          m.Acciones = acciones.reverse()
        })
      }
    })

    
    request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/tipos-movimiento`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.TiposMov = r
      } else {
        this.$q.notify('No hay tipos de movimiento disponibles para este estudio')
      }
    })

    request.Get(`/casos/${this.caso.IdCaso}/movimientos-sin-realizar`, {}, (r) => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.loading = false
        this.movimientos = r
        this.movimientos.forEach(m => {
          let acciones = []

          m.Acciones = JSON.parse(m.Acciones).filter(a => a.Accion)

          m.Acciones.forEach(a => {
            const i = acciones.findIndex(ac => ac.IdMovimientoAccion === a.IdMovimientoAccion)

            if (i === -1) {
              acciones.push(a)
            }
          })

          m.Acciones = acciones.reverse()
        })
        setTimeout(() => {
          this.traerObjetivos()
        }, 50)
      }
    })

    const IdEstudio = auth.UsuarioLogueado.IdEstudio

    request.Get(`/estudios/${IdEstudio}/cuadernos`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.cuadernos = r.map(c => c.Cuaderno)
      }
    })
  },
  computed: {
    opcionesTipoMov () {
      let result = []
      if (this.TiposMov && this.TiposMov.length) {
        result = this.TiposMov.map(t => ({ label: t.TipoMovimiento, value: t.IdTipoMov }))
      }
      return result
    },
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
            if (m.Color === 'positive' && m.Posicion === 'D') {
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
            if (m.Color === 'positive' && m.Posicion === 'D') {
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
        if (m.Color === 'positive') {
          m.Orden = 4
        }
        respuesta.push(m)
      })
      return respuesta.sort(function (a, b) {
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
    masivo (accion) {
      const ids = this.movimientosDelCaso(this.computedMovimientos).filter(m => m.check).map(m => m.IdMovimientoCaso)

      request.Post('/movimientos/masivo-accion', { ids, accion, cuaderno: this.Cuaderno, idTipoMov: this.TipoMovimiento.value }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          if (accion === 'fin') {
            this.$q.notify({
              color: 'green',
              message: `Se marcó como realizado los movimientos`
            })

            ids.forEach(id => {
              const movimiento = this.movimientos.filter(m => m.IdMovimientoCaso === id)
              const i = this.movimientos.findIndex(m => m.IdMovimientoCaso === id)
              this.movimientos.splice(i, 1)
              movimiento.check = false
              this.movimientosRealizados.push(movimiento)
            })
          }

          if (accion === 'cuad') {
            this.movimientos.forEach(m => {
              if (m.check) {
                m.Cuaderno = this.Cuaderno
                m.check = false
              }
            })
            this.$q.notify({
              color: 'green',
              message: `Se cambiaron de cuaderno los movimientos`
            })

            this.Cuaderno = ''
          }

          if (accion === 'tipo') {
            ids.forEach(id => {
              const i = this.movimientos.findIndex(m => m.IdMovimientoCaso === id)
              this.movimientos[i].IdTipoMov = this.TipoMovimiento.value
              this.movimientos[i].check = undefined
            })
            this.$q.notify({
              color: 'green',
              message: `Se cambiaron de tipo los movimientos`
            })
          }
        }
      })
    },
    cambiarPosicion (id, p) {
      const i = this.movimientos.findIndex(m => m.IdMovimientoCaso === id)
      this.movimientos[i].Posicion = p

      request.Post('/movimientos/posicion', { id, posicion: p }, r => {
        this.$q.notify({
          color: 'green',
          message: `Posicion Cambiada`
        })
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
