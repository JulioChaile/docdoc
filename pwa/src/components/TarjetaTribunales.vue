<template>
  <div class="q-my-xs">
    <q-banner
      inline="true"
      :class="movimiento.Color === 'warning' ? 'bg-warning text-black inner-shadow' : 'bg-' + movimiento.Color + ' text-white inner-shadow'"
      style="padding: 0; border-radius: 15px;"
      link
    >
      <div class="row avenir-next text-white relative-position">
        <q-icon class="cursor-pointer text-white absolute-right q-mt-sm q-mr-sm" name="create_new_folder" color="white" right size="sm" @click="duplicar()">
          <q-tooltip>Duplicar Movimiento</q-tooltip>
        </q-icon>
        <div
          class="col-2 flex-column justify-start items-center text-start q-pl-sm q-pt-sm --medium text-caption"
          :style="movimiento.Color === 'warning' ? 'color: black' : ''"
        >
          <div class="cursor-pointer flex justify-center items-center text-center" @click="mostrarObjetivos()">
            <q-tooltip v-if="!inicio && movimiento.Objetivo" :offset="[10, 0]">
              <q-list v-if="ultimosMovimientos.length > 0">
                <q-item v-for="mov in ultimosMovimientos" :key="mov.IdMovimientoCaso">
                  <div
                    v-if="mov.FechaRealizado"
                  >{{mov.Detalle}} - Realizado el {{parseDate(mov.FechaRealizado)}}</div>
                </q-item>
              </q-list>
              <span v-else>
                Objetivo sin movimientos realizados
              </span>
            </q-tooltip>
            {{ movimiento.Objetivo ? movimiento.Objetivo : 'Sin objetivo' }}
          </div>

          <div
            class="flex justify-center items-center text-center text-h4 --ultra-light"
            :style="movimiento.Color === 'warning' ? 'color: black' : '' "
          >
            <q-tooltip
              anchor="bottom middle"
              self="top middle"
              :offset="[10, 0]"
            >Días desde su Creacion</q-tooltip>
            <!-- Acá necesito recibir los días restantes -->
            {{ diasRestantes(movimiento, 'creacion') }}
          </div>

          <div
            class="cursor-pointer q-mb-sm text-center"
            :style="movimiento.Color === 'warning' ? 'color:black' : ''"
            @click="modalUsuario = true"
          >
            {{usuarioResponsable()}}
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[10, 0]"
            >Responsable: {{movimiento.UsuarioResponsable ? movimiento.UsuarioResponsable : ''}}</q-tooltip>
            <q-dialog v-model="modalUsuario">
              <q-card style="padding:1rem;text-align:center; display:flex; flex-direction:column;">
                <span class="text-weight-light">Usuario responsable:</span>
                <span
                  class="text-weight-light"
                  style="font-size:30px;margin-top:1rem;"
                >{{movimiento.UsuarioResponsable ? movimiento.UsuarioResponsable : 'Sin responsable'}}</span>
                <div style="display: flex; justify-content: flex-end; margin-top:2rem;">
                  <q-btn flat color="primary" label="Cerrar" @click="modalUsuario = false" />
                </div>
              </q-card>
            </q-dialog>
          </div>
        </div>
        <div class="col-2 flex-column justify-center items-center">
          <div
            class="flex justify-center items-center text-center text-h2 --ultra-light"
            :style="movimiento.Color === 'warning' ? 'color: black' : '' "
          >
            <q-tooltip
              anchor="bottom middle"
              self="top middle"
              :offset="[10, 0]"
            >Días desde su edicion</q-tooltip>
            <!-- Acá necesito recibir los días restantes -->
            {{ diasRestantes(movimiento, 'edicion') }}
          </div>

          <div
            class="cursor-pointer q-mb-sm text-center"
            :style="movimiento.Color === 'warning' ? 'color:black' : ''"
            @click="modalUsuario2 = true"
          >
            {{usuarioResponsable(true)}}
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[10, 0]"
            >Editado: {{movimiento.UsuarioEdicion ? movimiento.UsuarioEdicion : ''}}</q-tooltip>
            <q-dialog v-model="modalUsuario2">
              <q-card style="padding:1rem;text-align:center; display:flex; flex-direction:column;">
                <span class="text-weight-light">Usuario Edicion:</span>
                <span
                  class="text-weight-light"
                  style="font-size:30px;margin-top:1rem;"
                >{{movimiento.UsuarioEdicion ? movimiento.UsuarioEdicion : 'Sin usuario'}}</span>
                <div style="display: flex; justify-content: flex-end; margin-top:2rem;">
                  <q-btn flat color="primary" label="Cerrar" @click="modalUsuario2 = false" />
                </div>
              </q-card>
            </q-dialog>
          </div>
        </div>
        <div
          class="col-8 col-sm-5 q-px-lg flex text-start items-start q-pt-sm"
          :style="movimiento.Color === 'warning' ? 'color: black' : ''"
        >
          <!--div style="display:flex; flex-wrap:wrap; align-items: center;">
            <div
              v-if="inicio"
              @click="verCaso()"
              style="cursor: pointer; margin-right:0.3rem"
            >{{ movimiento.Caso }} : {{movimiento.NroExpediente ? movimiento.NroExpediente : 'Sin Expediente'}}</div>
            <div v-if="inicio" class="separadorTribunales" style="margin-right:0.3rem">|</div>
            <div v-if="inicio">{{ movimiento.Juzgado}} - {{movimiento.Nominacion}}</div>
          </div-->
          <div
            v-if="vistaVenc"
            @click="abrirCaso(movimiento.CasoCompleto.IdCaso)"
            class="full-width text-center text-caption cursor-pointer"
          >
            {{ movimiento.CasoCompleto.Caratula }}
          </div>

          <q-separator
            v-if="vistaVenc"
            class="bg-white"
          />

          <div
            v-on:click="editarMovimiento()"
            class="cursor-pointer flex items-start text-start full-width"
            style="white-space:pre-wrap"
          >
            <span
              :class="isMobile() ? 'ellipsis-2-lines' : 'ellipsis-3-lines'"
            >{{ movimiento.Detalle }}</span>
            <q-tooltip
              v-if="!inicio"
              anchor="bottom middle"
              self="top middle"
              :offset="[10, 0]"
            >Editar Movimiento</q-tooltip>
          </div>
        </div>
        <div
          class="col-2 flex justify-center items-center text-center text-h1 --ultra-light"
          :style="movimiento.Color === 'warning' ? 'color: black' : ''"
        >
          <div>
            <q-tooltip v-if="movimiento.IdEvento" anchor="bottom middle" self="top middle" :offset="[10, 0]">
              Días restantes
              <br>
              Evento: {{ movimiento.ComienzoEvento }}
            </q-tooltip>
            <q-tooltip v-else anchor="bottom middle" self="top middle" :offset="[10, 0]">Días restantes</q-tooltip>
            {{ diasRestantes(movimiento, 'vencimiento') }}
          </div>
        </div>
        <div
          class="col-1 flex justify-end items-end"
        >
          <div
            class="cursor-pointer q-mb-sm"
            :style="movimiento.Color === 'warning' ? 'color:black' : ''"
            @click="modalUsuario = true"
          >
            {{usuarioResponsable()}}
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[10, 0]"
            >Responsable: {{movimiento.UsuarioResponsable ? movimiento.UsuarioResponsable : ''}}</q-tooltip>
            <q-dialog v-model="modalUsuario">
              <q-card style="padding:1rem;text-align:center; display:flex; flex-direction:column;">
                <span class="text-weight-light">Usuario responsable:</span>
                <span
                  class="text-weight-light"
                  style="font-size:30px;margin-top:1rem;"
                >{{movimiento.UsuarioResponsable ? movimiento.UsuarioResponsable : 'Sin responsable'}}</span>
                <div style="display: flex; justify-content: flex-end; margin-top:2rem;">
                  <q-btn flat color="primary" label="Cerrar" @click="modalUsuario = false" />
                </div>
              </q-card>
            </q-dialog>
          </div>

          <q-btn
            icon="crop_square"
            :color="movimiento.Color === 'warning' ? 'black' : 'white'"
            round
            flat
            outline
            @click="realizarMovimiento()"
          >
            <q-tooltip
              anchor="bottom middle"
              self="top middle"
              :offset="[10, 0]"
            >
              Marcar como realizado
            </q-tooltip>
          </q-btn>
        </div>
      </div>
    </q-banner>

    <!-- MODALES -->
    <q-dialog v-model="modalEditar" no-esc-dismiss>
      <q-card style="width: 100%">
        <q-item class="relative-position" style="background-color: black;">
          <span class="q-subheading" style="color:white;">Editar Movimiento</span>
          <q-icon
            name="close"
            class="absolute-right cursor-pointer"
            color="negative"
            size="lg"
            @click="cancelarEdicion()"
          />
        </q-item>
        <EditarMovimiento
          v-if="modalEditar"
          :movimiento="this.movimientoEditar"
          :tribunales="true"
          @cancelarEdicion="cancelarEdicion()"
          @edicionTerminada="edicionTerminada"
        />
      </q-card>
    </q-dialog>
    <q-dialog v-model="modalCaso">
      <q-card>
        <TarjetaCaso
          v-if="modalCaso"
          :caso="movimiento.CasoCompleto"
          :perentorio-home="perentorioHome"
        />
        <div style="display: flex; justify-content: flex-end; margin-right: 40px">
          <q-btn flat color="primary" label="Cerrar" @click="modalCaso = false" />
        </div>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import request from '../request'
import { QDialog, Notify } from 'quasar'
import moment from 'moment'
import EditarMovimiento from '../components/EditarMovimiento.vue'

import TarjetaCaso from '../components/TarjetaCaso'

export default {
  components: {
    QDialog,
    EditarMovimiento,
    TarjetaCaso
  },
  data () {
    return {
      movimientoEditar: {},
      modalEditar: false,
      modalCaso: false,
      modalUsuario: false,
      modalUsuario2: false,
      idUltimoMensaje: '',
      nuevoIdChat: ''
    }
  },
  props: ['movimiento', 'ultimosMovimientos', 'inicio', 'perentorioHome', 'idChat', 'datosChat', 'vistaVenc'],
  created () {
    /*
    request.Get(`/casos/${this.movimiento.CasoCompleto.IdCaso}`, {}, r => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        this.movimiento.CasoCompleto.PersonasCaso = r.PersonasCaso
      }
    })
  */
  },
  methods: {
    duplicar () {
      request.Post('/movimientos/duplicar', { IdMovimientoCaso: this.movimiento.IdMovimientoCaso, IdObjetivo: this.movimiento.IdObjetivo }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          Notify.create('Movimiento Duplicado')

          const mov = { ...this.movimiento, IdMovimientoCaso: r.IdMovimientoCaso, FechaAlta: moment(new Date()).format('YYYY-MM-DD'), FechaEdicion: moment(new Date()).format('YYYY-MM-DD') }

          this.$emit('duplicar', mov)
        }
      })
    },
    realizarMovimiento () {
      // Verifico si existe chat, si no existe lo creo y envio, sino solo envio
      /** TODO ESTE CODIGO NO ES REQUERIDO AHORA. QUEDA PARA UN POSIBLE USO EN EL FUTURO */
      /* if (!this.idChat) {
        request.Post(`/chats/crear`, this.datosChat, r => {
          if (!r.Error) {
            Notify.create('Nuevo chat creado!')
            this.nuevoIdChat = r.IdChat
          } else {
            Notify.create('Falló al iniciar un nuevo chat. Razon: ' + r.Error)
            this.$emit('realizarMovimiento')
          }
        })
      }

      const mensaje = {
        IdChat: this.idChat ? this.idChat : this.nuevoIdChat,
        Contenido: `Movimiento realizado: ${this.movimiento.Detalle}`
      }

      if (!mensaje.IdChat) { return }

      request.Post(`/mensajes/enviar`, mensaje, r => {
        if (!r.Error) {
          Notify.create('Movimiento notificado por Whatsapp correctamente')
          this.idUltimoMensaje = r.IdMensaje
        } else {
          Notify.create(r.Error)
        }
      })
      request.Post(`/chats/${this.movimiento.CasoCompleto.IdChat}/actualizar`, { IdUltimoLeido: this.idUltimoMensaje }, p => {
        if (!p.Error) {
          console.log('UltimoMensajeLeido actualizado correctamente.')
        } else {
          console.log(p.Error)
        }
      })
      */
      this.$emit('realizarMovimiento')
    },
    mostrarObjetivos () {
      this.$emit('mostrarObjetivos')
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
    abrirCaso (id) {
      let routeData = this.$router.resolve({ path: `/Caso?id=${id}` })
      window.open(routeData.href, '_blank')
    },
    usuarioResponsable (e = false) {
      if (e) {
        if (this.movimiento.UsuarioEdicion) {
          let nom = this.movimiento.UsuarioEdicion[0]
          let ap = ''
          for (let i = 1; i < this.movimiento.UsuarioEdicion.length; i++) {
            if (this.movimiento.UsuarioEdicion[i] == this.movimiento.UsuarioEdicion[i].toUpperCase()) {
              ap = this.movimiento.UsuarioEdicion[i]
            }
          }
          return `${nom}${ap}`
        }
        return '-'
      }

      if (this.movimiento.UsuarioResponsable) {
        let ap = this.movimiento.UsuarioResponsable[0]
        let nom = ''
        for (let i = 0; i < this.movimiento.UsuarioResponsable.length; i++) {
          if (this.movimiento.UsuarioResponsable[i] === ',') {
            nom = this.movimiento.UsuarioResponsable[i + 2]
          }
        }
        return `${nom}${ap}`
      }
      return '-'
    },
    diasRestantes (movimiento, texto) {
      if (!movimiento.FechaEsperada && texto === 'vencimiento') {
        return '-'
      }
      /*
      this.fecha2 = new Date()
      var year = ''
      var month = ''
      var day = ''
      if (texto === 'vencimiento') {
        if (movimiento.FechaEsperada.split('-')[0].length === 4) {
          year = movimiento.FechaEsperada.split('-')[0]
          month = movimiento.FechaEsperada.split('-')[1] - 1
          day = movimiento.FechaEsperada.split('-')[2].split(' ')[0] - 1
        } else {
          year = movimiento.FechaEsperada.split('-')[2]
          month = movimiento.FechaEsperada.split('-')[1] - 1
          day = movimiento.FechaEsperada.split('-')[0].split(' ')[0] - 1
        }
      } else if (texto === 'creacion') {
        if (movimiento.FechaAlta.split('-')[0].length === 4) {
          year = movimiento.FechaAlta.split('-')[0]
          month = movimiento.FechaAlta.split('-')[1] - 1
          day = movimiento.FechaAlta.split('-')[2].split(' ')[0] - 1
        } else {
          year = movimiento.FechaAlta.split('-')[2]
          month = movimiento.FechaAlta.split('-')[1] - 1
          day = movimiento.FechaAlta.split('-')[0].split(' ')[0] - 1
        }
      } else if (movimiento.FechaRealizado) {
        if (movimiento.FechaRealizado.split('-')[0].length === 4) {
          year = movimiento.FechaRealizado.split('-')[0]
          month = movimiento.FechaRealizado.split('-')[1] - 1
          day = movimiento.FechaRealizado.split('-')[2].split(' ')[0]
        } else {
          year = movimiento.FechaRealizado.split('-')[2]
          month = movimiento.FechaRealizado.split('-')[1] - 1
          day = movimiento.FechaRealizado.split('-')[0].split(' ')[0]
        }
      }
      this.fecha2.setMonth(month)
      this.fecha2.setFullYear(year)
      this.fecha2.setDate(day)
      this.hoy = new Date()
      var resultado =
        Math.ceil(
          (this.fecha2.getTime() - this.hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      */
      switch (texto) {
        case 'vencimiento':
          let fecha = movimiento.FechaEsperada.split(' ')[0]
          if (fecha.split('-')[2].length === 4) {
            fecha = fecha.split('-').reverse().join('-')
          }
          const resultado = moment().diff(moment(fecha), 'days')

          if (resultado === 0 && fecha !== moment().format('YYYY-MM-DD')) {
            return -1
          }

          return resultado >= 0 ? resultado : resultado - 1

        case 'creacion':
          let fecha2 = movimiento.FechaAlta.split(' ')[0]
          if (fecha2.split('-')[2].length === 4) {
            fecha2 = fecha2.split('-').reverse().join('-')
          }
          const resultado2 = moment().diff(moment(fecha2), 'days')

          if (resultado === 0 && fecha2 !== moment().format('YYYY-MM-DD')) {
            return -1
          }

          return resultado2 >= 0 ? resultado2 : resultado2 - 1

        case 'edicion':
          let fecha3 = movimiento.FechaEdicion.split(' ')[0]
          if (fecha3.split('-')[2].length === 4) {
            fecha3 = fecha3.split('-').reverse().join('-')
          }
          const resultado3 = moment().diff(moment(fecha3), 'days')

          if (resultado === 0 && fecha3 !== moment().format('YYYY-MM-DD')) {
            return -1
          }

          return resultado3 >= 0 ? resultado3 : resultado3 - 1

        default:
          let fecha4 = movimiento.FechaRealizado.split(' ')[0]
          if (fecha4.split('-')[2].length === 4) {
            fecha4 = fecha4.split('-').reverse().join('-')
          }
          const resultado4 = moment().diff(moment(fecha4), 'days')

          if (resultado === 0 && fecha4 !== moment().format('YYYY-MM-DD')) {
            return -1
          }

          return resultado4 >= 0 ? resultado4 : resultado4 - 1
      }
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
    editarMovimiento () {
      this.movimientoEditar = this.movimiento
      if (!this.movimiento.IdCaso) {
        this.movimientoEditar.IdCaso = this.movimiento.CasoCompleto.IdCaso ? this.movimiento.CasoCompleto.IdCaso : null
      }
      this.modalEditar = true
    },
    cancelarEdicion () {
      this.modalEditar = false
      this.movimientoEditar = {}
    },
    edicionTerminada (m) {
      this.movimientoEditar.Caso = this.movimiento.Caso
      this.movimientoEditar.NroExpediente = this.movimiento.NroExpediente
      this.movimiento = { ...this.movimientoEditar, ...m }
      console.log(this.movimiento)
      this.modalEditar = false
    },
    verCaso () {
      if (this.movimiento.CasoCompleto.IdEstadoCaso) {
        request.Get(
          `/estados-caso/${this.movimiento.CasoCompleto.IdEstadoCaso}`,
          {},
          (r) => {
            if (r.Error) {
              Notify.create(r.Error)
            } else {
              this.movimiento.CasoCompleto.EstadoCaso = r.EstadoCaso
            }
          }
        )
      }
      request.Get(
        `/tipos-caso/${this.movimiento.CasoCompleto.IdTipoCaso}`,
        {},
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.movimiento.CasoCompleto.TipoCaso = r.TipoCaso
            this.modalCaso = true
          }
        }
      )
      request.Get(`/casos/${this.movimiento.CasoCompleto.IdCaso}`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.movimiento.CasoCompleto.PersonasCaso = r.PersonasCaso
        }
      })
    }
  }
}
</script>

<style>
@media (max-width: 600px) {
  .separadorTribunales {
    display: none;
  }
}
.inner-shadow {
  -webkit-box-shadow: inset 0px 0px 59px -35px rgba(0, 0, 0, 0.33);
  -moz-box-shadow: inset 0px 0px 59px -35px rgba(0, 0, 0, 0.33);
  box-shadow: inset 0px 0px 59px -35px rgba(0, 0, 0, 0.33);
}
</style>
