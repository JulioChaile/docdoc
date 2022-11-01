<template>
    <div style="margin:1rem;">
      <q-input
        v-model="movimiento.Detalle"
        label="Detalle del movimiento"
        @input="habilitarMensaje()"
        type="textarea"
        :rows="6"
      />
      <div style="display:flex; justify-content:space-between; align-items:end;">
        <q-select
          style="width:47%;"
          label="Estado de gestión"
          v-model="Color"
          :options="coloresDocDoc"
        >
          <template v-slot:option="scope">
          <q-item
            v-bind="scope.itemProps"
            v-on="scope.itemEvents"
          >
            <q-item-section>
              <q-item-label v-html="scope.opt.label" />
              <q-item-label caption>{{ scope.opt.description }}</q-item-label>
            </q-item-section>
          </q-item>
        </template>
        </q-select>
        <q-input ref="inputFechaEsperada" label="Fecha Esperada" v-model="movimiento.FechaEsperada" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
          <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                <q-date mask="DD-MM-YYYY" v-model="movimiento.FechaEsperada" @input="() => $refs.qDateProxy1.hide()" @change="editarFechaEsperada()" />
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:end">
      <q-input ref="inputFechaAlta" label="Fecha de Estado de Gestión" v-model="movimiento.FechaAlta" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
        <template v-slot:append>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
              <q-date mask="DD-MM-YYYY" v-model="movimiento.FechaAlta" @input="() => $refs.qDateProxy2.hide()" @change="editarFechaAlta()"/>
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>
      <q-select
        label="Responsable del movimiento"
        v-model="Responsable"
        :options="opcionesResponsable"
        style="width:47%;"
      >
        <template v-slot:option="scope">
          <q-item
            v-bind="scope.itemProps"
            v-on="scope.itemEvents"
          >
            <q-item-section>
              <q-item-label v-html="scope.opt.label" />
              <q-item-label caption>{{ scope.opt.description }}</q-item-label>
            </q-item-section>
          </q-item>
        </template>
      </q-select>
      </div>
      <div style="display:flex; justify-content:space-between;align-items:end;">
        <q-select
          label="Tipo de Movimiento"
          v-model="TipoMovimiento"
          :options="opcionesTipoMov"
          style="width:47%;"
        />
        <q-select
          style="width:47%;"
          v-model="Cuaderno"
          :options="cuadernos"
          label="Cuaderno"
        />
      </div>
      <div class="row" style="display:flex; align-items:start; margin-top: 20px">
        <q-checkbox
          v-if="IdCalendario !== 0"
          v-model="CrearEvento"
          :style="CrearEvento ? 'width:47%' : ''"
        >
          Crear evento en calendario
          <br>
          (debera especificar la hora del evento)
        </q-checkbox>
        <q-input v-if="CrearEvento" label="Hora del evento" v-model="EventTime" mask="##:##" style="margin-left: 30px; width:47%;">
          <template v-slot:append>
            <q-icon name="access_time" class="cursor-pointer">
                <q-popup-proxy ref="qTimeProxy1" transition-show="scale" transition-hide="scale">
                  <q-time
                    format24h
                    mask="HH:mm"
                    v-model="EventTime"
                    @input="() => $refs.qTimeProxy1.hide()"
                  />
                </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>
      <div class="column">
        <q-checkbox
            v-model="TareaPendiente"
            label="Notificación de Tarea Pendiente"
        />
        <div class="row">
          <q-checkbox
            class="col-6"
            v-model="EnviarMensaje"
            @input="habilitarMensaje()"
            label="Enviar mensaje al cliente"
          />
          <q-input
            class="col-4"
            v-if="EnviarMensaje"
            v-model="FrecuenciaRec"
            label="Frecuencia de días"
            type="number"
            filled
          />
        </div>
        <q-input
          v-if="EnviarMensaje"
          v-model="mensaje"
          label="Mensaje"
          type="textarea"
          :rows="4"
        />
      </div>
    <div style="padding-top:30px; text-align:right">
      <q-btn color="primary" @click="guardarMovimiento()">Guardar</q-btn>
      <q-btn flat @click="$emit('cancelarEdicion')">Cancelar</q-btn>
    </div>
  </div>
</template>

<script>
import request from '../request'
import { Notify } from 'quasar'
import auth from '../auth'
export default {
  data () {
    return {
      FrecuenciaRec: 0,
      TiposMov: [],
      FechaRealizado: this.movimiento.FechaRealizado,
      FechaEsperada: new Date().toISOString(),
      FechaAlta: new Date().toISOString(),
      UsuariosEstudio: [],
      editado: false,
      Color: {},
      Responsable: {},
      TipoMovimiento: {label: this.movimiento.TipoMovimiento, value: this.movimiento.IdTipoMov},
      Cuaderno: {},
      coloresDocDoc: [
        {
          label: 'Perentorio',
          value: 'negative',
          IdColorCalendarApi: '11'
        },
        {
          label: 'Gestión Estudio',
          value: 'primary',
          IdColorCalendarApi: '9'
        },
        {
          label: 'Gestión Externa',
          value: 'warning',
          IdColorCalendarApi: '5'
        },
        {
          label: 'Finalizado',
          value: 'positive',
          IdColorCalendarApi: '10'
        }
      ],
      CrearEvento: false,
      EnviarMensaje: true,
      EventTime: '',
      estudio: '',
      mensaje: '',
      IdCalendario: '',
      IdCalendarioAPI: '',
      CasoCompleto: {},
      TareaPendiente: this.movimiento.Escrito === 'dWz6H78mpQ',
      cuadernos: []
    }
  },
  props: ['movimiento', 'tribunales'],
  mounted () {
    this.movimiento.FechaAlta = this.movimiento.FechaAlta.split(' ')[0].split('-').reverse().join('-')
    if (this.movimiento.FechaEsperada) {
      this.movimiento.FechaEsperada = this.movimiento.FechaEsperada.split(' ')[0].split('-').reverse().join('-')
    }
    if (this.movimiento.Cuaderno) {
      this.Cuaderno = {
        label: this.movimiento.Cuaderno,
        value: this.movimiento.Cuaderno
      }
    } else {
      this.Cuaderno = {
        label: 'Sin cuaderno',
        value: ''
      }
    }
    // Creación de Fechas
    this.hoy = new Date()
    var dd = this.hoy.getDate()
    var mm = this.hoy.getMonth() + 1
    var yyyy = this.hoy.getFullYear()
    if (dd < 10) {
      dd = '0' + dd
    }
    if (mm < 10) {
      mm = '0' + mm
    }
    this.hoy = mm + '/' + dd + '/' + yyyy
    this.FechaRealizado = dd + '/' + mm + '/' + yyyy
    this.coloresDocDoc.forEach(c => {
      if (this.movimiento.Color === c.value) {
        this.Color = {
          label: c.label,
          value: c.value,
          IdColorCalendarApi: c.IdColorCalendarApi
        }
      }
    })
    this.Responsable = {label: this.movimiento.UsuarioResponsable, value: this.movimiento.IdUsuarioResponsable}
    this.TipoMovimiento = {label: this.movimiento.TipoMovimiento, value: this.movimiento.IdTipoMov}
    // ESTO DESPUES HAY QUE SACAR
    this.IdEstudio = auth.UsuarioLogueado.IdEstudio
    request.Get(`/estudios/${this.IdEstudio}/usuarios`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.UsuariosEstudio = r
      }
    })
    request.Get(`/estudios/${this.IdEstudio}/cuadernos`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.cuadernos = r.map(c => {
          return {
            label: c.Cuaderno,
            value: c.Cuaderno
          }
        })
        this.cuadernos.unshift({
          label: 'Sin cuaderno',
          value: ''
        })
      }
    })
    request.Get(`/estudios/${this.IdEstudio}/calendario`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r) {
        this.IdCalendario = r.IdCalendario
        this.IdCalendarioAPI = r.IdCalendarioAPI
      }
    })
    request.Get(`/casos/${this.movimiento.IdCaso}`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.CasoCompleto = r
      }
    })
    request.Get(`/estudios/${this.IdEstudio}`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.estudio = r.Estudio
      }
    })
    request.Get(`/estudios/${this.IdEstudio}/tipos-movimiento`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.TiposMov = r
      } else {
        this.$q.notify('No hay tipos de movimiento disponibles para este estudio')
      }
    })
    this.habilitarMensaje()
  },
  destroyed () {
    this.movimiento.FechaAlta = this.movimiento.FechaAlta.split(' ')[0].split('-').reverse().join('-')
    if (this.movimiento.FechaEsperada) {
      this.movimiento.FechaEsperada = this.movimiento.FechaEsperada.split(' ')[0].split('-').reverse().join('-')
    }
  },
  /*
  watch: {
    'movimiento.FechaEsperada' (val) {
      this.$nextTick(() => {
        if (!this.$refs.inputFechaEsperada.innerErrorMessage) {
          this.$refs.inputFechaEsperada.innerError = false
        }
      })
    },
    'movimiento.FechaAlta' (val) {
      this.$nextTick(() => {
        if (!this.$refs.inputFechaAlta.innerErrorMessage) {
          this.$refs.inputFechaAlta.innerError = false
        }
      })
    }
  },
  */
  computed: {
    opcionesTipoMov () {
      let result = []
      if (this.TiposMov && this.TiposMov.length) {
        result = this.TiposMov.map(t => ({ label: t.TipoMovimiento, value: t.IdTipoMov }))
      }
      return result
    },
    opcionesResponsable () {
      let result = []
      if (this.UsuariosEstudio && this.UsuariosEstudio.length) {
        result = this.UsuariosEstudio.map(t => ({ label: `${t.Apellidos}, ${t.Nombres}`, value: t.IdUsuario }))
      }
      return result
    }
  },
  methods: {
    formatearFecha (fecha) {
      return fecha.split('T')[0]
    },
    parseDateTime (fecha) {
      const fh = fecha.split(' ')
      const amd = fh[0].split('-')
      const hms = fh[1].split(':')
      return `${amd[2]}/${amd[1]}/${amd[0]} ${hms[0]}:${hms[1]}`
    },
    parseDate (fecha) {
      if (fecha === this.hoy || this.editado) {
        return fecha
      }
      return this.parseDateTime(fecha).split(' ')[0]
    },
    editarFechaEsperada () {
      if (this.movimiento.FechaRealizado) {
        this.$q.notify({
          color: 'red',
          timeout: 800,
          message: 'No se puede editar la fecha esperada de un movimiento ya realizado'})
      } else {
        this.editado = true
        var fechaIngresada = new Date(this.movimiento.FechaEsperada)
        var fechaActual = new Date(this.hoy)
        if (fechaIngresada < fechaActual) {
          this.$q.notify({
            color: 'red',
            timeout: 800,
            message: 'No puede ingresar una fecha esperada pasada'})
        } else {
          this.movimiento.FechaEsperada = this.formatearFecha(this.movimiento.FechaEsperada)
        }
      }
    },
    editarFechaAlta () {
      if (this.movimiento.FechaRealizado) {
        this.$q.notify({
          color: 'red',
          timeout: 800,
          message: 'No se puede editar la fecha de gestión de un movimiento ya realizado'})
      } else {
        this.editado = true
        this.movimiento.FechaAlta = this.formatearFecha(this.movimiento.FechaAlta)
      }
    },
    habilitarMensaje () {
      this.mensaje = this.movimiento.Caratula + ', desde ' + this.estudio + ' te contamos que estamos trabajando en tu caso. Gestion de hoy: ' + this.movimiento.Detalle
    },
    guardarMovimiento () {
      if (this.movimiento.FechaEsperada) {
        const fE = new Date(this.movimiento.FechaEsperada.split(' ')[0].split('-').reverse().join('-'))
        const fA = new Date(this.movimiento.FechaAlta.split('-').reverse().join('-'))
        if (fE < fA && fE) {
          this.$q.notify('No se puede ingresar una fecha esperada anterior a la fecha de alta.')
          return
        }
      }
      let fEsperada = this.movimiento.FechaEsperada
      let fAlta = this.movimiento.FechaAlta
      if (this.movimiento.Detalle && this.Responsable.value && this.Color.value && this.TipoMovimiento.value) {
        if (this.movimiento.FechaEsperada && this.movimiento.FechaEsperada.split('-')[2].length === 4) {
          fEsperada = `${this.movimiento.FechaEsperada.split('-')[2]}-${this.movimiento.FechaEsperada.split('-')[1]}-${this.movimiento.FechaEsperada.split('-')[0]}`
        }
        if (this.movimiento.FechaAlta && this.movimiento.FechaAlta.split('-')[2].length === 4) {
          fAlta = `${this.movimiento.FechaAlta.split('-')[2]}-${this.movimiento.FechaAlta.split('-')[1]}-${this.movimiento.FechaAlta.split('-')[0]}`
        }
        let movimiento = {
          FechaEsperada: (fEsperada && fEsperada.includes(' ')) ? fEsperada.split(' ')[0] : fEsperada,
          FechaRealizado: this.movimiento.FechaRealizado,
          FechaAlta: (fAlta && fAlta.includes(' ')) ? fAlta.split(' ')[0] : fAlta,
          Detalle: this.movimiento.Detalle,
          Cuaderno: this.Cuaderno.value,
          IdTipoMov: this.TipoMovimiento.value,
          IdResponsable: this.Responsable.value,
          Color: this.Color.value,
          Escrito: this.TareaPendiente ? 'dWz6H78mpQ' : null
        }
        this.editado = true
        request.Put(`/movimientos/${this.movimiento['IdMovimientoCaso']}`, movimiento, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            if (this.CrearEvento) {
              const fecha = movimiento.FechaEsperada
              const hora = this.EventTime

              let fechadb = `${fecha}T${hora}`

              const datetimeCalendar = `${fecha}T${hora}:00-03:00`

              const datosEvento = {
                IdCalendario: this.IdCalendario,
                IdCalendarioAPI: this.IdCalendarioAPI,
                IdMovimientoCaso: this.movimiento.IdMovimientoCaso,
                Titulo: `${this.movimiento.Caratula} - DocDoc!`,
                Descripcion: movimiento.Detalle,
                Comienzo: datetimeCalendar,
                Fin: datetimeCalendar,
                IdColor: this.Color.IdColorCalendarApi,
                ComienzoDb: fechadb,
                FinDb: fechadb
              }

              request.Post('/movimientos/eventos', datosEvento, r => {
                if (r.Error) {
                  this.$q.notify(r.Error)
                } else {
                  this.$q.notify({
                    color: 'green',
                    message: `Se añadio un evento al calendario del estudio.`
                  })
                }
              })
            }

            if (this.EnviarMensaje) {
              request.Post('/movimientos/alta-recordatorio', { IdMovimientoCaso: this.movimiento.IdMovimientoCaso, Frecuencia: this.FrecuenciaRec }, t => {
                if (r.Error) {
                  this.$q.notify(t.Error)
                } else {
                  this.$q.notify({
                    color: 'green',
                    message: `Se añadio un mensaje recurrente.`
                  })
                }
              })
              const Mensaje = {
                IdChat: this.CasoCompleto.IdChat ? this.CasoCompleto.IdChat : null,
                Contenido: this.mensaje
              }
              if (!this.CasoCompleto.IdChat) {
                const Persona = this.CasoCompleto.PersonasCaso.find(p => p.EsPrincipal === 'S')
                const NuevoChat = {
                  IdCaso: this.CasoCompleto.IdCaso,
                  IdPersona: Persona.IdPersona,
                  Telefono: this.telefonoPrincipal(Persona.Telefonos)
                }
                if (NuevoChat.Telefono) {
                  this.nuevoChat(NuevoChat, Mensaje)
                } else {
                  Notify.create('Falló al comunicar el movimiento. Razon: no existe un telefono asociado')
                }
              } else {
                this.enviarMensaje(Mensaje)
              }
            }

            this.$emit('edicionTerminada', movimiento)
          }
        })
      } else {
        if (!this.movimiento.Detalle) {
          this.$q.notify('Debe escribir el detalle del movimiento')
        }
        if (!this.Responsable.value) {
          this.$q.notify('Debe seleccionar un usuario responsable')
        }
        if (!this.Color.value) {
          this.$q.notify('Debe seleccionar el estado de gestión del movimiento')
        }
        if (!this.TipoMovimiento.value) {
          this.$q.notify('Debe seleccionar el tipo de movimiento')
        }
      }
    },
    telefonoPrincipal (telefonos) {
      if (telefonos) {
        const TelefonosPrincipales = (telefonos || []).filter(
          (telefono) => telefono.EsPrincipal === 'S'
        )
        if (!TelefonosPrincipales.length) {
          return telefonos.length ? telefonos[0].Telefono : ''
        } else {
          return TelefonosPrincipales[0].Telefono
        }
      } else {
        return ''
      }
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
    }
  }
}
</script>

<style lang="stylus">
.q-subheading {
  font-size: 18px;
}
.edicion {
  margin: 1rem;
}
.docdoc-title {
  font-size: 15px;
  text-align: left;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: flex-start;
}
span.q-title {
  color: $primary;
}
textarea::-webkit-resizer {
  border: 9px solid rgba(0,0,0,.1);
  border-bottom-color: rgba(0,0,0,.5);
  border-right-color: rgba(0,0,0,.5);
  outline: 1px solid rgba(0,0,0,.2);
}
</style>
