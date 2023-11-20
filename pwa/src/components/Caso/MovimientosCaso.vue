<template>
    <q-card style="min-width:95vw;">
        <q-item
            style="background-color: black; color: white;display:flex; justify-content:space-between; align-items:center;"
        >
            <q-icon color="white" name="assignment" size="sm" />
            <q-item-label>
                <span class="q-subheading">Movimientos del {{ IdObjetivo? 'Objetivo' : 'Caso' }}</span>
            </q-item-label>
            <q-btn
                round
                color="primary"
                class="q-subheading"
                size="sm"
                style="color:black;"
                v-if="!IdObjetivo"
                @click="modalAlta = true"
            >
                <q-icon name="add" color="black;"></q-icon>
                <q-tooltip>Nuevo Movimiento</q-tooltip>
            </q-btn>
            <q-dialog v-model="modalAlta" v-if="modalAlta" no-backdrop-dismiss no-esc-dismiss>
                <NuevoMovimiento
                    v-if="modalAlta"
                    :caso="this.caso"
                    :IdCaso="this.caso.IdCaso"
                    @cancelarAlta="modalAlta = false"
                    @guardarmovimiento="guardarMovimiento"
                    @nuevochat="nuevoChat"
                    @enviarmensaje="enviarMensaje"
                />
            </q-dialog>
        </q-item>
        <div v-if="cargandoMovimientos">
            <Loading />
        </div>
        <div v-else-if="movimientosCaso.length > 0">
            <TarjetaMovimiento
                v-for="(m, i) in movimientosCaso"
                :key="i"
                :movimiento="m"
                :tarjetaCaso="true"
                @borrar="borrarMovimiento(m)"
                style="margin-right:auto; margin-left:auto;"
            />
        </div>
        <q-item v-else>
            <q-item-label>No se asignaron movimientos al {{ IdObjetivo ? 'objetivo' : 'caso' }}</q-item-label>
        </q-item>
        <q-btn
            flat
            @click="$emit('cerrar')"
            style="float:right; margin:10px 10px 20px"
        >Cancelar</q-btn>
    </q-card>
</template>

<script>
import Loading from '../../components/Loading'
import TarjetaMovimiento from '../../components/TarjetaMovimiento'
import NuevoMovimiento from '../../components/NuevoMovimiento'
import request from '../../request'
import { Notify } from 'quasar'
export default {
  name: 'MovimientosCaso',
  components: {
    Loading,
    TarjetaMovimiento,
    NuevoMovimiento
  },
  data () {
    return {
      modalAlta: false,
      cargandoMovimientos: true,
      movimientosCaso: []
    }
  },
  props: [ 'IdCaso', 'IdObjetivo' ],
  created () {
    if (!this.IdObjetivo) {
      request.Get(`/casos/${this.IdCaso}/movimientos`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.movimientosCaso = r

          this.movimientosCaso.sort(this.ordenarMovs)

          this.movimientosCaso.forEach(m =>{
            m.Acciones = JSON.parse(m.Acciones).filter(a => a.IdMovimientoAccion)
          })

          this.cargandoMovimientos = false
        }
      })
      request.Get(`/casos/${this.IdCaso}`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.caso = r
        }
      })
    } else {
      request.Get(`/casos/${this.IdCaso}/movimientos-realizados`, {}, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.cargandoMovimientos = false
          this.movimientosCaso = r.filter(f => f.IdObjetivo === this.IdObjetivo)
        }
      })
    }
  },
  methods: {
    ordenarMovs (a, b) {
      const fechaAltaA = new Date(a.FechaAlta).getTime()
      const fechaAltaB = new Date(b.FechaAlta).getTime()

      switch (true) {
        case !!a.FechaEdicion && !!b.FechaEdicion:
          const A = new Date(a.FechaEdicion).getTime()
          const B = new Date(b.FechaEdicion).getTime()

          if (A < B) {
            return -1
          } else {
            return 1
          }

        case !!a.FechaEdicion && !b.FechaEdicion:
          const fechaEdicionA = new Date(a.FechaEdicion).getTime()

          if (fechaEdicionA < fechaAltaB) {
            return -1
          } else {
            return 1
          }

        case !a.FechaEdicion && !!b.FechaEdicion:
          const fechaEdicionB = new Date(b.FechaEdicion).getTime()

          if (fechaAltaA < fechaEdicionB) {
            return -1
          } else {
            return 1
          }

        case !a.FechaEdicion && !b.FechaEdicion:
          if (fechaAltaA < fechaAltaB) {
            return -1
          } else {
            return 1
          }
      }
    },
    borrarMovimiento (movimiento) {
      this.movimientosCaso.splice(this.movimientosCaso.indexOf(movimiento), 1)
    },
    guardarMovimiento (movimiento) {
      this.modalAlta = false
      this.movimientosCaso.push(movimiento)
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
    }
  }
}
</script>
