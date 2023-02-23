<template>
    <q-card style="min-width:400px;">
        <q-item class="relative-position" style="background-color: black;">
            <span class="q-subheading" style="color:white;">Nuevo Movimiento</span>
            <q-icon
              name="close"
              class="absolute-right cursor-pointer"
              color="negative"
              size="lg"
              @click="$emit('cancelarAlta')"
            />
        </q-item>
        <div style="padding: 0 1rem 1rem 1rem;">
            <div style="display:flex; flex-direction:row;justify-content: space-between;margin-top: 20px;">
            <!--q-field
                label= "Caso:"
                stack-label
                :style="movimientoAlta.Objetivo ? 'width:45%' : 'width: 100%'"
            >
                {{ movimientoAlta.Caso}}
            </q-field-->
            </div>
            <q-select
                style="width:100%;"
                v-model="nuevoMovimiento.TipoMov"
                :options="opcionesTipoMov"
                label="Tipo de movimiento"
            />

            <div class="q-mt-sm text-bold">
              Titulo
            </div>
            <q-input
              v-model="nuevoMovimiento.Detalle"
              type="textarea"
              :rows="1"
              dense
              @input="habilitarMensaje()"
            />

            <div class="q-mt-sm text-bold">
              Nuevo Movimiento
            </div>

            <div class="flex">
              <q-input
                v-model="accionNew"
                type="text"
                dense
                style="width: 80%;"
              />
              <div class="q-ml-sm">
                <q-btn
                  color="primary"
                  round
                  size="sm"
                  @click="accionNew && acciones.unshift({ Accion: accionNew }); accionNew = ''; habilitarMensaje()"
                >
                  +
                  <q-tooltip>
                    Agregar Movimiento
                  </q-tooltip>
                </q-btn>
              </div>
            </div>

            <div id="acciones" style="height: 100px; overflow: scroll">
              <li v-for="(a, i) in acciones" :key="a.Accion">
                <q-icon name="delete" color="negative" size="15px" class="cursor-pointer q-mx-xs" @click="acciones.splice(i, 1)">
                  <q-tooltip>Eliminar</q-tooltip>
                </q-icon>
                {{ a.Accion }}
              </li>
            </div>
            <div id="resizer" class="full-width text-center bg-primary text-bold cursor-pointer">
              v
            </div>

            <div style="display:flex; justify-content:space-between; align-items:end">
              <q-select
                  style="width:47%;"
                  v-model="nuevoMovimiento.colorSeleccionado"
                  :options="coloresDocDoc"
                  label="Estado de Gestión"
              />
              <q-select
                  style="width:47%;"
                  v-model="nuevoMovimiento.Responsable"
                  :options="opcionesResponsable"
                  label="Responsable del movimiento"
              />
            </div>
            <q-select
              style="width:100%;"
              v-model="nuevoMovimiento.Cuaderno"
              :options="cuadernos"
              label="Cuaderno"
            />
            <div style="display:flex; justify-content:space-between; align-items:end;">
              <q-input ref="inputFechaAlta" label="Fecha de estado de gestión" v-model="nuevoMovimiento.FechaAlta" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
                        <q-date
                          mask="DD-MM-YYYY"
                          label="Fecha de estado de gestión"
                          v-model="nuevoMovimiento.FechaAlta"
                          @input="() => $refs.qDateProxy2.hide()"
                        />
                      </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
              <q-input ref="inputFechaEsperada" label="Fecha Esperada" v-model="nuevoMovimiento.FechaEsperada" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                        <q-date
                          mask="DD-MM-YYYY"
                          v-model="nuevoMovimiento.FechaEsperada"
                          @input="() => $refs.qDateProxy1.hide()"
                        />
                      </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
            <div style="display:flex; align-items:start">
              <!--q-uploader
                label="Multimedia"
                auto-upload
                multiple
                :factory="factoryFn"
                @uploaded="uploadedFile"
                @removed="removedFile"
                style="width:47%"
              /-->
              <div class="row" style="display:flex">
                <q-input v-if="CrearEvento" label="Hora del evento" v-model="EventTime" mask="##:##" style="width:47%;">
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
                <q-checkbox
                  v-if="IdCalendario !== 0"
                  v-model="CrearEvento"
                  style="margin-left: 10px; margin-top: 10px"
                >
                  Crear evento en calendario
                  <br>
                  (debera especificar la hora del evento)
                </q-checkbox>
              </div>
            </div>
            <q-checkbox
                v-model="TareaPendiente"
                label="Notificación de Tarea Pendiente"
                style="margin-left: 10px; width: 100%"
            />
            <div class="row">
              <q-checkbox
                class="col-6"
                v-model="EnviarMensaje"
                @input="habilitarMensaje()"
                label="Enviar mensaje al cliente"
                style="margin-left: 10px"
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
              autogrow
            />
            <div style="padding-top:30px; text-align:right">
            <q-btn color="primary" @click="guardarMovimiento()">Guardar</q-btn>
            <q-btn flat @click="$emit('cancelarAlta')">Cancelar</q-btn>
            </div>
        </div>
    </q-card>
</template>

<script>
import moment from 'moment'
import request from '../request'
import auth from '../auth'
import { Notify } from 'quasar'
export default {
  components: {
    Notify
  },
  data () {
    return {
      FrecuenciaRec: 0,
      Multimedia: [],
      accionNew: '',
      acciones: [],
      nuevoMovimiento: {
        IdUsuario: 0,
        TiposMov: [],
        TipoMov: {
          label: 'Sin identificar'
        },
        UsuariosEstudio: [],
        IdEstudio: 0,
        Detalle: '',
        FechaEsperada: '',
        FechaAlta: '',
        colorSeleccionado: {label: 'Gestión Estudio', value: 'primary', IdColorCalendarApi: '9'},
        Caso: 0,
        Objetivo: '',
        Responsable: {},
        Cuaderno: {
          label: 'Sin cuaderno',
          value: ''
        }
      },
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
      EnviarMensaje: true,
      CrearEvento: false,
      EventTime: '',
      IdCalendario: 0,
      IdCalendarioAPI: '',
      mensaje: '',
      estudio: '',
      TareaPendiente: false,
      cuadernos: []
    }
  },
  props: ['movimientoAlta', 'Casos', 'caso', 'IdCaso'],
  created () {
    var tzoffset = (new Date()).getTimezoneOffset() * 60000
    this.nuevoMovimiento.FechaAlta = this.formatearFecha((new Date(Date.now() - tzoffset)).toISOString().slice(0, -1)).split(' ')[0].split('-').reverse().join('-')
    this.nuevoMovimiento.Objetivo = this.movimientoAlta.Objetivo ? this.movimientoAlta.Objetivo : ''
    this.nuevoMovimiento.IdEstudio = auth.UsuarioLogueado.IdEstudio
    this.nuevoMovimiento.Responsable = {
      label: `${auth.UsuarioLogueado.Apellidos}, ${auth.UsuarioLogueado.Nombres}`,
      value: auth.UsuarioLogueado.IdUsuario
    }
    request.Get(`/estudios/${this.nuevoMovimiento.IdEstudio}/usuarios`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.nuevoMovimiento.UsuariosEstudio = r
      }
    })
    request.Get(`/estudios/${this.nuevoMovimiento.IdEstudio}/cuadernos`, {}, r => {
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
    request.Get(`/estudios/${this.nuevoMovimiento.IdEstudio}/calendario`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r) {
        this.IdCalendario = r.IdCalendario
        this.IdCalendarioAPI = r.IdCalendarioAPI
      }
    })
    request.Get(`/estudios/${this.nuevoMovimiento.IdEstudio}/tipos-movimiento`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.nuevoMovimiento.TiposMov = r
      } else {
        this.$q.notify('No hay tipos de movimiento disponibles para este estudio')
      }
    })
    request.Get(`/estudios/${this.nuevoMovimiento.IdEstudio}`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.estudio = r.Estudio
      }
    })
    request.Get(`/casos/${this.IdCaso}`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        this.CasoCompleto = r
      }
    })
  },
  mounted () {
    const resizer = document.getElementById('resizer')
    const elemento = document.getElementById('acciones')

    resizer.addEventListener("mousedown", e => {
      let original_height = parseFloat(
        getComputedStyle(elemento, null)
          .getPropertyValue("height")
          .replace("px", "")
      )

      let original_mouse_y = e.pageY

      const resize = e => {
        const height = original_height + (e.pageY - original_mouse_y)
        elemento.style.height = height + "px"
      }

      window.addEventListener("mousemove", resize)
      window.addEventListener("mouseup", e => window.removeEventListener("mousemove", resize))
    })
  },
  computed: {
    ObjetivosSinMovimientos (item) {
      return item.concat(this.objetivosCreadosSinMovimientos)
    },
    opcionesTipoMov () {
      let result = []
      if (this.nuevoMovimiento.TiposMov && this.nuevoMovimiento.TiposMov.length) {
        result = this.nuevoMovimiento.TiposMov.map(t => ({ label: t.TipoMovimiento, value: t.IdTipoMov }))
      }
      return result
    },
    opcionesResponsable () {
      let result = []
      if (this.nuevoMovimiento.UsuariosEstudio && this.nuevoMovimiento.UsuariosEstudio.length) {
        result = this.nuevoMovimiento.UsuariosEstudio.map(t => ({ label: `${t.Apellidos}, ${t.Nombres}`, value: t.IdUsuario }))
      }
      return result
    }
  },
  methods: {
    currentDateTime () {
      var now = new Date()
      const date = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`
      const hour = `${now.getHours()}:${now.getMinutes()}`
      return `${date} - ${hour}`
    },
    factoryFn () {
      return {
        url: 'https://io.docdoc.com.ar/api/multimedia',
        method: 'POST',
        headers: [
          { name: 'Authorization', value: `Bearer ${auth.Token}` }
        ]
      }
    },
    uploadedFile ({ files, xhr }) {
      const data = JSON.parse(xhr.response)
      for (let i = 0; i < files.length; i++) {
        const Tipo = files[i].type

        this.Multimedia.push({
          Url: data.Urls[0],
          Tipo: Tipo.includes('application') ? 'O' : Tipo.substring(0, 1).toUpperCase()
        })
      }
    },
    removedFile (files) {
      for (let i = 0; i < files.length; i++) {
        const file = files[i]
        const r = JSON.parse(file.xhr.response)
        for (let j = 0; j < this.Multimedia.length; j++) {
          if (this.Multimedia[j].Url === r.Urls[0]) {
            this.Multimedia.splice(j, 1)
          }
        }
      }
    },
    formatearFecha (fecha) {
      if (fecha) {
        return fecha.split('T')[0]
      } else return null
    },
    guardarMovimiento () {
      if (this.nuevoMovimiento.FechaEsperada) {
        const fE = new Date(this.nuevoMovimiento.FechaEsperada.split(' ')[0].split('-').reverse().join('-'))
        const fA = new Date(this.nuevoMovimiento.FechaAlta.split(' ')[0].split('-').reverse().join('-'))
        if (fE < fA && fE) {
          this.$q.notify('No se puede ingresar una fecha esperada anterior a la fecha de alta.')
          return
        }
      }
      if (this.CrearEvento) {
        if (!this.nuevoMovimiento.FechaEsperada || !this.EventTime) {
          this.$q.notify('Debe ingresar fecha esperada y hora del evento.')
          return
        }
      }
      if (!this.nuevoMovimiento.Detalle) {
        this.$q.notify('Debe ingresar el detalle del movimiento.')
      } else if (!this.nuevoMovimiento.TipoMov) {
        this.$q.notify('Debe elegir un tipo de movimiento.')
      } else if (this.nuevoMovimiento.colorSeleccionado === '') {
        this.$q.notify('Debe seleccionar un estado de gestión')
      } else {
        const id = this.IdCaso
        this.nuevoMovimiento.IdCaso = id
        this.nuevoMovimiento.NroExpediente = this.caso.NroExpediente
        let fEsperada = this.nuevoMovimiento.FechaEsperada
        let fAlta = this.nuevoMovimiento.FechaAlta
        if (this.nuevoMovimiento.FechaEsperada && this.nuevoMovimiento.FechaEsperada.split('-')[2].length === 4) {
          fEsperada = `${this.nuevoMovimiento.FechaEsperada.split('-')[2]}-${this.nuevoMovimiento.FechaEsperada.split('-')[1]}-${this.nuevoMovimiento.FechaEsperada.split('-')[0]}`
        }
        if (this.nuevoMovimiento.FechaAlta && this.nuevoMovimiento.FechaAlta.split('-')[2].length === 4) {
          fAlta = `${this.nuevoMovimiento.FechaAlta.split('-')[2]}-${this.nuevoMovimiento.FechaAlta.split('-')[1]}-${this.nuevoMovimiento.FechaAlta.split('-')[0]}`
        }
        const movimiento = {
          IdResponsable: this.nuevoMovimiento.Responsable.value,
          Detalle: this.nuevoMovimiento.Detalle,
          IdCaso: this.nuevoMovimiento.IdCaso,
          FechaEsperada: this.formatearFecha(fEsperada),
          FechaAlta: this.formatearFecha(fAlta),
          FechaEdicion: this.formatearFecha(fAlta),
          FechaRealizado: null,
          IdTipoMov: this.nuevoMovimiento.TipoMov.value,
          Cuaderno: this.nuevoMovimiento.Cuaderno.value,
          Color: this.nuevoMovimiento.colorSeleccionado.value,
          Multimedia: this.Multimedia,
          Escrito: this.TareaPendiente ? 'dWz6H78mpQ' : '',
          Cliente: this.EnviarMensaje ? 'S' : '',
          Acciones: JSON.stringify(this.acciones.reverse())
        }
        request.Post('/movimientos', movimiento, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            if (this.CrearEvento) {
              const fecha = this.nuevoMovimiento.FechaEsperada.split(' ')[0].split('-').reverse().join('-')
              const hora = this.EventTime

              let fechadb = `${fecha}T${hora}`

              const datetimeCalendar = `${fecha}T${hora}:00-03:00`

              const datosEvento = {
                IdCalendario: this.IdCalendario,
                IdCalendarioAPI: this.IdCalendarioAPI,
                IdMovimientoCaso: r.IdMovimientoCaso,
                Titulo: `${this.CasoCompleto.Caratula} - DocDoc!`,
                Descripcion: movimiento.Detalle,
                Comienzo: datetimeCalendar,
                Fin: datetimeCalendar,
                IdColor: this.nuevoMovimiento.colorSeleccionado.IdColorCalendarApi,
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
            this.$q.notify({
              color: 'green',
              message: `El movimiento "${movimiento.Detalle}" se dio de alta correctamente`
            })
            movimiento.TipoMovimiento = this.nuevoMovimiento.TipoMov.label
            movimiento.Caso = this.movimientoAlta.Caso ? this.movimientoAlta.Caso : null
            movimiento.CasoCompleto = this.caso
            movimiento.UsuarioResponsable = this.nuevoMovimiento.Responsable.label
            movimiento.Acciones = JSON.parse(movimiento.Acciones).map(a => {
              return {
                Accion: a.Accion,
                FechaAccion: moment().format('YYYY-MM-DD'),
                Apellidos: auth.UsuarioLogueado.Apellidos,
                Nombres: auth.UsuarioLogueado.Nombres
              }
            })
            this.$emit('guardarmovimiento', movimiento)
            if (this.EnviarMensaje) {
              request.Post('/movimientos/alta-recordatorio', { IdMovimientoCaso: r.IdMovimientoCaso, Frecuencia: this.FrecuenciaRec }, t => {
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
                  this.$emit('nuevochat', NuevoChat, Mensaje)
                } else {
                  Notify.create('Falló al comunicar el movimiento. Razon: no existe un telefono asociado')
                }
              } else {
                const Objeto = {
                  template: 'multi_uso',
                  language: {
                    policy: 'deterministic',
                    code: 'es'
                  },
                  namespace: 'ed2267b7_c376_4b90_90ae_233fb7734eb9'
                }

                const Contenido = this.mensaje

                const body = {}
                body.type = 'body'
                body.parameters = [{
                  type: 'text',
                  text: this.mensaje
                }]

                Objeto.params = [body]

                const mensajeTemporal = {
                  IdUsuario: true,
                  Contenido,
                  FechaEnviado: this.currentDateTime()
                }

                const mensajePost = {
                  IdChat: this.CasoCompleto.IdChat,
                  Contenido,
                  Objeto,
                  mediador: '',
                  contacto: ''
                }

                request.Post(`/mensajes/enviar-template`, mensajePost, r => {
                  if (!r.Error) {
                    console.log('Mensaje enviado correctamente!')
                    const idUltimoMensaje = r.IdMensaje
                    request.Post(`/chats/${this.idChat}/actualizar`, { IdUltimoLeido: idUltimoMensaje, mediador: '', contacto: '' }, p => {
                      if (!p.Error) {
                        console.log('UltimoMensajeLeido actualizado correctamente.')
                      } else {
                        Notify.create(p.Error)
                      }
                    })
                  } else {
                    Notify.create(r.Error)
                  }
                })
              }
            }
            /*
                    request.Get(`/casos/${this.nuevoMovimiento.IdCaso}/movimientos`, {}, r => {
                    if (r.Error) {
                        this.$q.notify(r.Error)
                    } else {
                        var contador = true
                        r.forEach(m => {
                        if (m.Detalle === movimiento.Detalle) {
                            movimiento.IdMovimientoCaso = m.IdMovimientoCaso
                            if (contador === true && !movimiento.FechaRealizado) {
                                if (this.movimientoAlta.Objetivo) {
                                    var objetivo = {}
                                    this.movimientoAlta.CasoCompleto.Objetivos.forEach(o => {
                                    if (o.Objetivo === this.movimientoAlta.Objetivo) {
                                        objetivo = o
                                    }
                                    })
                                    request.Post(`/movimientos/${movimiento.IdMovimientoCaso}/asociar-objetivo/${objetivo.IdObjetivo}`, {}, r => {
                                        movimiento.Objetivo = objetivo.Objetivo
                                        this.MovimientosPendientes.push(movimiento)
                                        this.Movimientos.push(movimiento)
                                        contador = false
                                    })
                                } else {
                                    this.MovimientosPendientes.push(movimiento)
                                    this.Movimientos.push(movimiento)
                                    contador = false
                                }
                            }
                        }
                        })
                        this.nuevoMovimiento.IdUsuario = 0
                        this.nuevoMovimiento.TiposMov = []
                        this.nuevoMovimiento.TipoMov = 0
                        this.nuevoMovimiento.UsuariosEstudio = []
                        this.nuevoMovimiento.IdEstudio = 0
                        this.nuevoMovimiento.IdResponsable = 0
                        this.nuevoMovimiento.Detalle = ''
                        this.nuevoMovimiento.FechaAlta = new Date().toISOString()
                        this.nuevoMovimiento.FechaEsperada = null
                        this.nuevoMovimiento.colorSeleccionado = {label: 'Gestión Estudio', value: 'primary'}
                        this.nuevoMovimiento.Caso = 0
                    }
                    })
                    */
          }
        })
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
    habilitarMensaje () {
      this.mensaje = this.CasoCompleto.Caratula + ', desde ' + this.estudio + ' te contamos que estamos trabajando en tu caso. Gestion de hoy: ' + this.nuevoMovimiento.Detalle + ' ,' + (this.acciones.length > 0 ? this.acciones[0].Accion : '')
    }
  }
}
</script>

<style>

</style>
