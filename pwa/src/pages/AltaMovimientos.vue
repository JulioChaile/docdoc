<template>
  <q-card class="alta-movimiento" style="display:flex; flex-direction:column;">
    <q-select
      v-model="casoSeleccionado"
      v-if="!altaDesdeTarjetaCaso"
      use-input
      hide-selected
      fill-input
      style="color:black;"
      input-debounce="0"
      label="Caso"
      :options="options"
      @filter="filterFn"
    >
      <template v-slot:no-option>
        <q-item>
          <q-item-section class="text-grey">
            No se encontraron resultados
          </q-item-section>
        </q-item>
      </template>
    </q-select>
    <q-input v-model="Detalle" autogrow label="Detalle del movimiento"/>
    <div style="display:flex; justify-content:space-between;">
      <q-select
        style="width:47%;"
        v-model="colorSeleccionado"
        :options="coloresDocDoc"
        label="Estado de Gestión"
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
      <q-input ref="inputFechaEsperada" label="Fecha esperada" v-model="FechaEsperada" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
        <template v-slot:append>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
              <q-date mask="DD-MM-YYYY" v-model="FechaEsperada" @input="() => $refs.qDateProxy1.hide()" />
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>
    </div>
    <div style="display:flex; justify-content:space-between;">
      <q-input ref="inputFechaAlta" label="Fecha de estado de gestión" v-model="FechaAlta" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:47%;">
        <template v-slot:append>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
              <q-date mask="DD-MM-YYYY" v-model="FechaAlta" @input="() => $refs.qDateProxy2.hide()" />
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>
      <q-select
        style="width:47%;"
        v-model="IdResponsable"
        :options="opcionesResponsable"
        label="Responsable del movimiento"
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
    <div style="display:flex; justify-content:space-between;align-items:flex-start;">
      <q-select
        style="width:47%;"
        v-model="TipoMov"
        :options="opcionesTipoMov"
        label="Tipo de movimiento"
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
      <!--q-uploader
        label="Multimedia"
        auto-upload
        multiple
        :factory="factoryFn"
        @uploaded="uploadedFile"
        @removed="removedFile"
        style="width:47%"
      /-->
      <!--
        <q-input v-model="Cuaderno" style="width:30%;" type="textarea" rows="1" :max-height="50" label="Cuaderno"/>
      -->
    </div>
    <q-checkbox
        v-model="TareaPendiente"
        label="Notificación de Tarea Pendiente"
        style="margin-left: 10px; width: 100%"
    />
    <q-checkbox
      v-model="EnviarMensaje"
      label="Enviar mensaje al cliente (se enviara el detalle del movimiento)"
      style="margin-left: 10px"
    />
    <q-input
      class="col-6"
      v-if="EnviarMensaje"
      v-model="FrecuenciaRec"
      label="Frecuencia de días"
      type="number"
      filled
    />
    <div style="display: flex; justify-content: flex-end; margin-top: 35px;">
      <q-btn flat color="primary" label="Guardar" @click="guardarMovimiento"/>
      <q-btn v-if="altaDesdeTarjetaCaso" flat @click="$emit('cancelar')" label="Cancelar" />
    </div>
  </q-card>
</template>

<script>
import request from '../request'
import auth from '../auth'
import { Notify } from 'quasar'

export default {
  props: ['casoRecibido', 'altaDesdeTarjetaCaso', 'movimientoRecibido'],
  data () {
    return {
      FrecuenciaRec: 0,
      IdCaso: this.$route.params.idCaso,
      Casos: [{
        Caso: 'Apapa',
        Id: 1
      },
      {
        Caso: 'Aparto',
        Id: 2
      }],
      casoSeleccionado: null,
      Detalle: '',
      Cuaderno: '',
      Usuarios: [],
      UsuariosEstudio: [],
      IdResponsable: null,
      FechaAlta: '',
      FechaEsperada: '',
      TipoMov: null,
      TiposMov: [],
      IdEstudio: 0,
      colorSeleccionado: {label: 'Gestión Estudio', value: 'primary'},
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
      Multimedia: [],
      files: [],
      opcionesCasos: {},
      options: this.Casos,
      EnviarMensaje: true,
      TareaPendiente: false
    }
  },
  /*
  watch: {
    'FechaEsperada' (val) {
      this.$nextTick(() => {
        if (!this.$refs.inputFechaEsperada.innerErrorMessage) {
          this.$refs.inputFechaEsperada.innerError = false
        }
      })
    },
    'FechaAlta' (val) {
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
  mounted () {
    var tzoffset = (new Date()).getTimezoneOffset() * 60000
    this.FechaAlta = this.formatearFecha((new Date(Date.now() - tzoffset)).toISOString().slice(0, -1)).split(' ')[0].split('-').reverse().join('-')
    this.IdResponsable = {label: `${auth.UsuarioLogueado.Apellidos}, ${auth.UsuarioLogueado.Nombres}`, value: auth.UsuarioLogueado.IdUsuario}
    this.IdEstudio = auth.UsuarioLogueado.IdEstudio
    request.Get(`/estudios/${this.IdEstudio}/usuarios`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.UsuariosEstudio = r
      }
    })
    request.Get(`/estudios/${this.IdEstudio}/tipos-movimiento`, {}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else if (r.length) {
        this.TiposMov = r
        this.TipoMov = {label: r[0].TipoMovimiento, value: r[0].IdTipoMov}
      } else {
        this.$q.notify('No hay tipos de movimiento disponibles para este estudio')
      }
    })
    if (!this.altaDesdeTarjetaCaso) {
      const params = {}
      if (this.IdCaso !== '0') {
        params.id = this.IdCaso
      }
      request.Get('/casos', params, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          if (typeof r.length === 'number') {
            this.Casos = r
          } else {
            this.Casos = [r]
          }
          this.Casos.forEach(c => {
            let caso = {
              label: c.Caratula,
              value: c.IdCaso
            }
            this.opcionesCasos.push(caso)
          })
        }
      })
    } else {
      this.casoSeleccionado = {label: '', value: this.casoRecibido}
    }
  },
  methods: {
    formatearFecha (fecha) {
      if (fecha !== '') {
        return fecha.split('T')[0]
      } else {
        return null
      }
    },
    filterFn (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        let casos = []
        this.Casos.forEach(c => {
          let caso = {
            label: c.Caratula,
            value: c.IdCaso
          }
          casos.push(caso)
        })
        this.options = casos.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
      })
    },
    guardarMovimiento () {
      if (this.FechaEsperada) {
        const fE = new Date(this.FechaEsperada.split(' ')[0].split('-').reverse().join('-'))
        const fA = new Date(this.FechaAlta.split(' ')[0].split('-').reverse().join('-'))
        if (fE < fA && fE) {
          this.$q.notify('No se puede ingresar una fecha esperada anterior a la fecha de alta.')
          return
        }
      }
      if (!this.Detalle) {
        this.$q.notify('Debe ingresar el detalle del movimiento.')
      }
      if (!this.casoSeleccionado) {
        this.$q.notify('Debe elegir un caso.')
      }
      if (!this.TipoMov) {
        this.$q.notify('Debe elegir un tipo de movimiento.')
      }
      if (!this.colorSeleccionado) {
        this.$q.notify('Debe seleccionar el estado de gestión del movimiento.')
      }
      let fEsperada = this.FechaEsperada
      let fAlta = this.FechaAlta
      if (this.FechaEsperada && this.FechaEsperada.split('-')[2].length === 4) {
        fEsperada = `${this.FechaEsperada.split('-')[2]}-${this.FechaEsperada.split('-')[1]}-${this.FechaEsperada.split('-')[0]}`
      }
      if (this.FechaAlta && this.FechaAlta.split('-')[2].length === 4) {
        fAlta = `${this.FechaAlta.split('-')[2]}-${this.FechaAlta.split('-')[1]}-${this.FechaAlta.split('-')[0]}`
      }
      const movimiento = {
        IdResponsable: this.IdResponsable.value,
        Detalle: this.Detalle,
        IdCaso: this.casoSeleccionado.value,
        FechaEsperada: this.formatearFecha(fEsperada),
        FechaAlta: this.formatearFecha(fAlta),
        Escrito: this.TareaPendiente ? 'dWz6H78mpQ' : null,
        IdTipoMov: this.TipoMov.value,
        Cuaderno: this.Cuaderno,
        Color: this.colorSeleccionado.value,
        Multimedia: this.Multimedia,
        Cliente: this.EnviarMensaje ? 'S' : ''
      }
      this.hoy = new Date().toISOString()
      request.Post('/movimientos', movimiento, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          if (!this.altaDesdeTarjetaCaso) {
            this.$router.push({ name: 'Tribunales', params: {caso: this.casoSeleccionado.value} })
          } else {
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
            }
            this.$emit('exito', movimiento)
          }
        }
      })
      if (this.EnviarMensaje) {
        const IdCaso = this.casoSeleccionado.value
        let CasoCompleto = ''
        request.Get(`/casos/${IdCaso}`, {}, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          } else {
            CasoCompleto = r
            const Mensaje = {
              IdChat: CasoCompleto.IdChat ? CasoCompleto.IdChat : null,
              Contenido: `Se creó un nuevo movimiento. Detalle: ${movimiento.Detalle}`
            }
            if (!CasoCompleto.IdChat) {
              const Persona = CasoCompleto.PersonasCaso.find(p => p.EsPrincipal === 'S')
              const NuevoChat = {
                IdCaso: CasoCompleto.IdCaso,
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
        this.Multimedia.push({
          Url: data.Urls[0],
          Tipo: files[i].type.substring(0, 1).toUpperCase()
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
    }
  }
}
</script>

<style>
.alta-movimiento {
  background-color: white;
  margin: 10px;
  padding: 10px;
}
</style>
