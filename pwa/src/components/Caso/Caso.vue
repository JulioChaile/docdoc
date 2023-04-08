<template>
  <div class="q-py-xl q-px-md row" style="padding-top: 0px;">
    <!-- Seccion de datos del caso -->
    <div
      v-if="!loading"
      class="col-12 col-md-7"
      style="min-height: 180px"
    >
      <div class="col-12 col-md-7">
        <Datos
          :datos="datos"
          :editar="editar"
          :modal="modal"
          @guardarDatosEditados="guardarDatosEditados"
          @cancelarEdicion="$emit('cancelarEdicion')"
        />
      </div>

      <!-- Seccion de personas del caso -->
      <div
        v-if="!editar"
        class="col-12 col-md-7 contenedor_personas q-mt-md"
      >
        <Personas
          :personas="dataPersonas()"
          @agregarTelefono="agregarTelefono"
          @updateTelefonos="updateTelefonos"
          @eliminarTelefono="eliminarTelefono"
        />
      </div>
    </div>

    <div
      v-if="!loading"
      class="col-12 col-md-5 column avenir-next text-h3 justify-center text-center q-pa-md"
    >
      <div class="height-90px column --bold">
        <span class="text-caption text-gris --bold">
          Total Demanda
        </span>
        <span
          style="color: #DB3DA9"
        >
          {{ totalDemanda() }}
        </span>
      </div>
      <div class="height-90px column">
        <span class="text-caption text-gris --bold">
          Días desde la creación del caso
        </span>
        <div>
          <span
            style="color: #DB3DA9"
          >
            {{ diasDesdeCreacion() }}
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">
              {{ datos.FechaAlta ? datos.FechaAlta.split(' ')[0].split('-').reverse().join('/') : '···' }}
            </q-tooltip>
          </span>
        </div>
      </div>
    </div>

    <div
      class="col-md-12 justify-center"
      style="min-height: 180px"
      v-else
    >
      <Loading />
    </div>

    <!--div class="col-12 q-pt-md" style="display:flex; justify-content:center;">
      <q-btn style="min-width: 100px" class="text-capitalize" color="primary" @click="abrirChat()">
        Chat
      </q-btn>
      <q-btn class="text-capitalize" style="margin-left: 10px; min-width: 100px" color="negative" @click="abrirMediacion()">
        Mediacion
      </q-btn>
    </div-->

    <!-- Componente Tribunales -->
    <div v-if="!loading" class="col-12 col-md-7 q-pt-lg">
      <caso-tribunales :caso="caso" :datosChat="datosChat" :idChat="caso.IdChat" />
    </div>

    <div v-if="!loading2" class="col-12 col-md-5">
      <q-tabs
        v-model="tabChat"
        class="rounded-borders q-ml-sm"
        style="border: 1px solid black"
        align="justify"
        active-color="primary"
        indicator-color="skyblue"
      >
        <q-tab name="cel" @click="scrollAcomodo('cel')">
          <q-icon name="phone" size="md" />
        </q-tab>
        <q-tab name="int" @click="scrollAcomodo('docdoc')">
          <q-badge v-if="mensajesNuevos !== 0" color="red" floating>{{ mensajesNuevos }}</q-badge>
          <img src="statics/img/logo.png" width="30" height="25">
        </q-tab>
      </q-tabs>

      <q-tab-panels
        class="bg-transparent q-ml-sm"
        v-model="tabChat"
        animated
        keep-alive
      >
        <q-tab-panel
          class="rounded-borders"
          style="border: 1px solid black !important; padding: 0"
          name="cel"
        >
          <Chat
            v-if="caso.IdChat"
            :id="caso.IdChat"
          />
          <div
            v-else
            class="q-mt-xl text-center column items-center q-mb-sm"
          >
            Puedes iniciar un chat por WhatsApp con la persona principal del caso
            <q-btn
              v-if="!spinner"
              style="max-width: 200px; margin-top: 20px"
              class="text-capitalize"
              color="primary"
              @click="abrirChat()"
            >
              Iniciar Chat
            </q-btn>
            <q-spinner
              v-else
              color="teal"
              class="self-center"
              size="2em"
            />
          </div>
        </q-tab-panel>

        <q-tab-panel
          class="rounded-borders"
          style="border: 1px solid black !important; padding: 0"
          name="int"
        >
          <ChatCaso
            :id="id"
            :tab="tabChat"
            @nuevosMensajes="e => mensajesNuevos = mensajesNuevos + e"
          />
        </q-tab-panel>
      </q-tab-panels>
    </div>

    <!-- Modal Mensaje -->
    <q-dialog v-model='ModalMensaje' style="background-color: white">
      <q-card style="min-width:800px;">
        <q-item style="background-color: black;">
            <span class="q-subheading" style="color:white;">¿Desea comunicar el cambio de estado?</span>
        </q-item>
        <q-input
          v-model="Mensaje"
          autogrow
          label="Mensaje"
          style="padding: 0 1rem 1rem 1rem;"
        />
        <Loading v-if="LoadingImagenes" />
        
        <div class="q-mt-sm text-bold" v-if="!LoadingImagenes && Imagenes.length === 0">
          No hay imagenes en este caso
        </div>

        <q-checkbox
          v-if="Imagenes.length > 0"
          v-model="checkMultimedia"
          @input="BuscarImagen = ''; Multimedia = ''; Imagenes.forEach(img => img.check = false)"
          color="red"
          label="Enviar Imagen"
        />
        <q-input
          v-if="checkMultimedia"
          v-model="BuscarImagen"
          autogrow
          label="Buscar Imagen"
          style="padding: 0 1rem 1rem 1rem;"
        />

        <div class="row" v-if="checkMultimedia" style="max-height: 200px; overflow-y: scroll;">
          <div
            v-for="m in Imagenes.filter(img => img.Nombre.includes(BuscarImagen))"
            :key="m.URL"
            class="col-grow-3 container_multimedia items-end cursor-pointer"
            :style="m.check ? 'border-color: red' : ''"
            @click="Imagenes.forEach(img => img.check = false); m.check = !m.check; Multimedia = m.URL"
          >
            <q-item class="column" clickable>
              <img class="img--multimedia" :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`">
              <q-item
                class="nombre_multimedia"
              >
                {{ m.Nombre }}
              </q-item>
            </q-item>
          </div>
        </div>

        <br>
        <q-btn
          v-if="!LoadingImagenes"
          @click="cancelarMensaje()"
          color="red"
          style="padding-top:0px; float: right; margin-bottom:20px; margin-right: 20px;"
        >Cancelar</q-btn>
        <q-btn
          v-if="!LoadingImagenes"
          @click="enviarMensaje(Mensaje)"
          color="primary"
          style="padding-top:0px; float: right; margin-bottom:20px; margin-right: 20px;"
        >Enviar</q-btn>
      </q-card>
    </q-dialog>

    <!-- Modal Mediacion -->
    <q-dialog v-model="ModalMediacion" style="background-color: white">
      <Mediacion
        :IdMediacion="caso.IdMediacion"
        :IdCaso="id"
        :IdChat="caso.IdChat"
        :CaratulaCaso="caso.Caratula"
        :Personas="dataPersonas()"
        @alta="altaMediacion"
        @cerrar="ModalMediacion = false"
      />
    </q-dialog>

    <!-- Modal Chat Existente -->
    <q-dialog v-model='ModalChat' style="background-color: white">
      <q-card style="min-width:400px;">
        <q-item style="background-color: black;">
            <span class="q-subheading" style="color:white;">Chat Existente</span>
        </q-item>
        <div class="text-center" style="margin-top: 15px; margin-bottom: 15px">
          Ya existe un chat con este numero en el caso <b>{{ CaratulaCasoChat }}</b>
          <br>
          ¿Desea que el chat se inicie en el caso actual de ahora en más?
        </div>
        <div style="display: flex; justify-content: center; margin-bottom:20px">
          <q-btn
            @click="reemplazarCaso()"
            color="primary"
            style="padding-top:0px; float: left; margin-right: 20px;"
          >Aceptar</q-btn>
          <q-btn
            @click="ModalChat = false"
            color="red"
            style="padding-top:0px; float: left"
          >Cancelar</q-btn>
        </div>
      </q-card>
    </q-dialog>

    <!-- Seccion de movimientos del caso
    <div class="col-12">
      <Movimientos :movimientos="movimientos" />
    </div>
    -->
  </div>
</template>

<script>
import request from '../../request'
import Loading from '../Loading'
import Datos from '../../components/Caso/Datos'
import Personas from '../../components/Caso/Personas'
import Movimientos from '../../components/Caso/Movimientos'
import Mediacion from '../../components/Caso/Mediacion'
import CasoTribunales from '../../components/Compartidos/CasoTribunales'
import ChatCaso from '../../components/Caso/ChatCaso'
import Chat from '../../pages/Chat'
import { Notify, QSpinner, QTabs, QTab, QTabPanels, QTabPanel, QBadge } from 'quasar'
import moment from 'moment'
export default {
  name: 'Caso',
  props: ['editar', 'modal'],
  components: {
    ChatCaso,
    Loading,
    Datos,
    Personas,
    Movimientos,
    Mediacion,
    CasoTribunales,
    Chat,
    QSpinner,
    QTabs,
    QTab,
    QTabPanels,
    QTabPanel,
    QBadge
  },
  data () {
    return {
      id: 0,
      caso: {},
      loading: true,
      loading2: true,
      casoModificado: {},
      datos: {},
      personas: [],
      telefonos: {},
      casoEditado: {},
      Objetivos: [],
      ModalMensaje: false,
      Mensaje: '',
      ModalMediacion: false,
      ModalChat: false,
      CaratulaCasoChat: '',
      IdExternoChat: '',
      spinner: false,
      tabChat: this.$route.query.tabChat ? this.$route.query.tabChat : 'cel',
      mensajesNuevos: 0,
      Multimedia: '',
      Imagenes: [],
      checkMultimedia: false,
      LoadingImagenes: false,
      BuscarImagen: ''
    }
  },
  created () {
    console.log(this.tabChat)
    if (!this.$route.query.id) {
      this.$router.push('GrillaCasos')
      return
    }
    this.id = this.$route.query.id

    // Busco el caso correspondiente al id que recibo por parametro:
    request.Get(`/casos`, { id: this.id, movs: 'N', archivos: 'S' }, (r) => {
      if (!r.Error) {
        console.log(r)
        this.caso = r
        // Datos del caso:
        this.datos = {
          FotoCaso: r.FotoCaso,
          Caratula: r.Caratula,
          Carpeta: r.Carpeta,
          IdCaso: r.IdCaso,
          Estado: r.Estado,
          FechaAlta: r.FechaAlta,
          FechaEstado: r.FechaEstado,
          FechaUltVisita: r.FechaUltVisita,
          NroExpediente: r.NroExpediente,
          Competencia: r.Competencia,
          IdCompetencia: r.IdCompetencia,
          TipoCaso: r.TipoCaso,
          IdTipoCaso: r.IdTipoCaso,
          Juzgado: r.Juzgado,
          IdJuzgado: r.IdJuzgado,
          Nominacion: r.Nominacion,
          IdNominacion: r.IdNominacion,
          EstadoAmbitoGestion: r.EstadoAmbitoGestion,
          IdEstadoAmbitoGestion: r.IdEstadoAmbitoGestion,
          Origen: r.Origen,
          IdOrigen: r.IdOrigen,
          IdMediacion: r.IdMediacion,
          IdCasoEstudio: r.IdCasoEstudio,
          IdChat: r.IdChat,
          EtiquetasCaso: r.EtiquetasCaso,
          App: false,
          Comparticiones: r.Comparticiones
        }

        this.loading = false

        setTimeout(() => {
          this.loading2 = false
        }, 3000);

        this.IdMediacion = r.IdMediacion
        /*
        // Movimientos del caso:
        this.movimientos = r.MovimientosCaso
        console.log(this.movimientos)
        if (this.Objetivos.length > 0) {
          console.log(this.Objetivos)
          this.Objetivos.forEach(e => {
            const i = this.movimientos.findIndex(f => f.IdMovimientoCaso === parseInt(e.IdMovimientoCaso))
            if (i !== -1) {
              this.movimientos[i].IdObjetivo = e.IdObjetivo
              this.movimientos[i].Objetivo = e.Objetivo
            }
          })
        }
        */

        // Personas del caso:
        this.personas = r.PersonasCaso ? r.PersonasCaso : []

        this.personas.forEach(p => {
          if (p.TokenApp) this.datos.App = true
        })

        setTimeout(() => {
          this.$emit('datos', this.datos)

          this.$emit('personas', this.dataPersonas())

          request.Get(`/mensajes-interno/nuevos-mensajes`, { IdCaso: this.id, Cliente: 'S' }, r => {
            if (!r.Error) {
              console.log('Request de mensajes exitoso.')
              if (r.length) {
                this.mensajesNuevos = r.length
                Notify.create(`Tienes ${this.mensajesNuevos} mensajes nuevos`)
              } else {
                console.log('Respuesta vacía')
              }
            } else {
              console.log('Error en el loop.')
              console.log(r)
            }
          })
        }, 50)
      } else {
        console.log('Hubo un error al traer el caso.')
      }
    })
  },
  computed: {
    datosChat () {
      return {
        IdCaso: this.caso.IdCaso,
        IdPersona: this.personaPrincipal().IdPersona,
        Telefono: this.telefonoPrincipal(this.personaPrincipal().Telefonos)
      }
    }
  },
  methods: {
    scrollAcomodo (t) {
      if (t === 'docdoc') this.mensajesNuevos = 0

      const s = document.getElementsByTagName('html')[0].scrollTop

      this.$router.push({ path: this.$route.path, query: { id: this.id, tabChat: t } })

      this.$nextTick(() => { document.getElementsByTagName('html')[0].scrollTop = s })
    },
    totalDemanda () {
      let total = 0

      this.personas.forEach(p => {
        if (p.Parametros !== null && p.Parametros.check && p.Observaciones === 'Actor') {
          const gc = p.Parametros.Cuantificacion.GastosCuracion
          const dm = p.Parametros.Cuantificacion.DañoMoral
          const vm = p.Parametros.Cuantificacion.FormulaVM
          const vr = p.Parametros.Vehiculo.ValorReparacion
          total = total + (parseInt(gc) || 0) + (parseInt(dm) || 0) + (parseInt(vm) || 0) + (parseInt(vr) || 0)
        }
      })

      return total || 'Sin datos'
    },
    diasDesdeCreacion () {
      if (this.datos.FechaAlta) {
        var anoActual = new Date().getFullYear()
        var mesActual = new Date().getUTCMonth()
        var diaActual = new Date().getDate()
        const fechaActual = moment([anoActual, mesActual, diaActual])

        var anoFechaAlta = this.datos.FechaAlta ? this.datos.FechaAlta.slice(0, 4) : '--'
        var mesFechaAlta = this.datos.FechaAlta ? this.datos.FechaAlta.slice(5, 7) - 1 : '--'
        var diaFechaAlta = this.datos.FechaAlta ? this.datos.FechaAlta.slice(8, 10) : '--'
        const fechaAlta = moment([anoFechaAlta, mesFechaAlta, diaFechaAlta])

        if (fechaActual.diff(fechaAlta, 'days') > 0) {
          this.casoViejo = true
        }

        return `${fechaActual.diff(fechaAlta, 'days') > 0 ? fechaActual.diff(fechaAlta, 'days') : '0'}`
      } else {
        return 'Sin fecha'
      }
    },
    volver () {
      window.history.back()
    },
    estado () {
      switch (this.caso.Estado) {
        case 'A':
          return {
            nombre: 'activo',
            color: 'light-green-14'
          }
        case 'P':
          return {
            nombre: 'pendiente',
            color: 'warning'
          }
        default:
          return {
            nombre: 'archivado',
            color: 'secondary'
          }
      }
    },
    dataPersonas () {
      const temp = []
      if (this.personas.length) {
        this.personas.forEach((persona) => {
          const data = {
            Id: persona.IdPersona,
            Apellido: persona.Apellidos,
            Nombre: persona.Nombres,
            Email: persona.Email,
            Principal: persona.EsPrincipal === 'S',
            Domicilio: persona.Domicilio,
            Documento: persona.Documento,
            Tipo: persona.Tipo,
            Rol: persona.RolTipoCaso ? persona.RolTipoCaso : persona.Observaciones,
            Telefonos: this.personaTelefonos(persona.Telefonos),
            TelefonoActivo: this.telefonoPrincipal(persona.Telefonos)
          }

          temp.push(data)

          if (persona.Parametros !== null && persona.Parametros.check) {
            if (persona.Parametros.Seguro.check) {
              const dataCia = {
                Id: 'ciaSeguro' + persona.IdPersona,
                Apellido: '',
                Nombre: persona.Parametros.Seguro.CiaSeguro,
                Email: '',
                Principal: false,
                Domicilio: persona.Parametros.Seguro.Direccion,
                Documento: persona.Documento,
                Tipo: persona.Tipo,
                Rol: persona.RolTipoCaso ? persona.RolTipoCaso : persona.Observaciones,
                Telefonos: [{
                  Detalle: 'Telefono Cia. Seguro',
                  EsPrincipal: true,
                  FechaAlta: '',
                  Telefono: persona.Parametros.Seguro.Telefono
                }],
                TelefonoActivo: persona.Parametros.Seguro.Telefono,
                CiaSeguro: true
              }
              temp.push(dataCia)
            }
          }
        })
      }
      return temp.sort((a, b) => {
        switch (true) {
          case a.Rol === 'Actor' && b.Rol === 'Actor':
            return a.Principal
              ? -1
              : 1

          case a.Rol === 'Actor' && b.Rol !== 'Actor':
            return -1

          default:
            return 1
        }
      })
    },
    personaPrincipal () {
      if (this.personas.length) {
        return this.personas.filter(persona => persona.EsPrincipal === 'S')[0]
      }
    },
    personaTelefonos (telefonos) {
      const temp = []
      if (telefonos) {
        telefonos.forEach((item) => {
          const ob = {
            Detalle: item.Detalle,
            EsPrincipal: item.EsPrincipal === 'S',
            FechaAlta: item.FechaAlta,
            Telefono: item.Telefono
          }
          temp.push(ob)
        })
      }
      return temp
    },
    telefonoPrincipal (telefonos) {
      if (telefonos) {
        const TelefonosPrincipales = (telefonos || []).filter(
          (telefono) => telefono.EsPrincipal === 'S'
        )
        if (!TelefonosPrincipales.length) {
          return telefonos.length ? telefonos[0].Telefono : 'Sin telefono'
        } else {
          return TelefonosPrincipales[0].Telefono
        }
      } else {
        return ''
      }
    },
    agregarTelefono (data) {
      let tel = {
        Telefono: data.NuevoTelefono.numero,
        Detalle: data.NuevoTelefono.detalle,
        EsPrincipal: data.NuevoTelefono.activo ? 'S' : 'N'
      }
      request.Post(`/personas/${data.idPersona}/alta-telefono`, tel, (r) => {
        if (!r.Error) {
          const i = this.personas.findIndex(p => parseInt(p.IdPersona) === parseInt(data.idPersona))
          this.personas[i].Telefonos.push(tel)

          this.$q.notify('Se guardo el nuevo teléfono correctamente.')
          const persona = this.dataPersonas().filter(p => parseInt(p.Id) === parseInt(data.idPersona))[0]
          if (persona.Principal && tel.EsPrincipal === 'S' && this.caso.IdChat) {
            request.Post(`/chats/${this.caso.IdChat}/actualizar-telefono`, {Telefono: tel.Telefono, IdPersona: data.idPersona}, c => {
              if (c.Error) {
                this.$q.notify('No fue posible modificar el telefono del chat. Razon: ' + c.Error)
              } else {
                this.$q.notify('Chat modificado con exito.')
              }
            })
          }
        } else {
          this.$q.notify(
            'Ocurrio un error al guardar el teléfono. Por favor, intente nuevamente.'
          )
        }
      })
      request.Get(`/casos`, { id: this.id }, (r) => {
        if (!r.Error) {
          // Personas del caso:
          this.personas = r.PersonasCaso ? r.PersonasCaso : []
        } else {
          console.log('Hubo un error al actualizar los telefonos.')
        }
      })
      this.dataPersonas()
    },
    updateTelefonos (telefonos, idPersona) {
      if (telefonos) {
        telefonos.forEach((item) => {
          let tel = {
            Telefono: item.Telefono,
            TelefonoOld: item.TelefonoOld,
            Detalle: item.Detalle,
            EsPrincipal: item.EsPrincipal ? 'S' : 'N'
          }
          request.Post(`/personas/${idPersona}/modificar`, tel, (r) => {
            if (!r.Error) {
              const i = this.personas.findIndex(p => parseInt(p.IdPersona) === parseInt(idPersona))
              const j = this.personas[i].Telefonos.findIndex(t => t.Telefono === tel.TelefonoOld)
              this.personas[i].Telefonos[j] = tel

              this.$q.notify('Los cambios se guardaron correctamente.')
              const persona = this.dataPersonas().filter(p => parseInt(p.Id) === parseInt(idPersona))[0]
              if (persona.Principal && tel.EsPrincipal === 'S' && this.caso.IdChat) {
                request.Post(`/chats/${this.caso.IdChat}/actualizar-telefono`, {Telefono: tel.Telefono, IdPersona: idPersona}, c => {
                  if (c.Error) {
                    this.$q.notify('No fue posible modificar el telefono del chat. Razon: ' + c.Error)
                  } else {
                    this.$q.notify('Chat modificado con exito.')
                  }
                })
              }
            } else {
              this.$q.notify(r.Error)
            }
          })
        })

        request.Get(`/casos`, { id: this.id }, (r) => {
          if (!r.Error) {
            // Personas del caso:
            this.personas = r.PersonasCaso ? r.PersonasCaso : []
          } else {
            console.log('Hubo un error al actualizar los telefonos.')
          }
        })
        this.dataPersonas()
      }
    },
    eliminarTelefono (data) {
      let tel = {
        Telefono: data.numeroTelEliminar
      }
      request.Post(
        `/personas/${data.idPersona}/eliminar-telefono`,
        tel,
        (r) => {
          if (!r.Error) {
            this.$q.notify('Se ha eliminado correctamente el teléfono')
          } else {
            this.$q.notify('Hubo un error al intentar borrar el telefono')
          }
        }
      )
    },
    guardarDatosEditados (datosModificados) {
      this.casoModificado = {
        Caratula: datosModificados.Caratula
          ? datosModificados.Caratula
          : 'Sin carátula',
        Carpeta: this.caso.Carpeta, // ver si se debe editar carpeta
        NroExpediente: datosModificados.NroExpediente,
        Observaciones: this.caso.Observaciones,
        // Competencia: datosModificados.Competencia,
        // Estado: this.caso.Estado, // ver si se debe editar estado
        // FechaAlta: this.caso.FechaAlta,
        // FechaUltVisita: this.caso.FechaUltVisita,
        IdCaso: this.caso.IdCaso,
        IdCompetencia: datosModificados.IdCompetencia,
        IdEstadoCaso: this.caso.IdEstadoCaso,
        IdJuzgado: datosModificados.IdJuzgado,
        IdNominacion: datosModificados.IdNominacion,
        IdOrigen: datosModificados.IdOrigen,
        IdTipoCaso: datosModificados.IdTipoCaso,
        // Origen: datosModificados.Origen,
        // Juzgado: datosModificados.AmbitoGestion,
        // NroExpediente: this.caso.NroExpediente,
        // Observaciones: this.caso.Observaciones,
        // TipoCaso: datosModificados.TipoCaso,
        // EstadoAmbitoGestion: datosModificados.EstadoAmbitoGestion,
        IdEstadoAmbitoGestion: datosModificados.IdEstadoAmbitoGestion,
        FechaEstado: datosModificados.FechaEstado,
        IdCasoEstudio: datosModificados.IdCasoEstudio
      }
      this.$emit('datos', datosModificados)
      request.Put(`/casos/${this.casoModificado.IdCaso}`, this.casoModificado, r => {
        if (!r.Error) {
          this.$q.notify({
            color: 'primary',
            timeout: 800,
            message: '¡Los cambios se guardaron correctamente!'
          })
          this.enviarMensaje(datosModificados.Mensaje)
        } else {
          this.$q.notify({
            color: 'warning',
            timeout: 800,
            message:
                // 'Se produjo un error al editar el caso, por favor, intente nuevamente.'
                r.Error
          })
        }
      }
      )
    },
    enviarMensaje (mensaje) {
      if (this.ModalMensaje) {
      const Multimedia = this.Multimedia
        ? JSON.stringify([{ URL: this.Multimedia }])
        : ''

        const Mensaje = {
          IdChat: this.caso.IdChat ? this.caso.IdChat : null,
          Contenido: mensaje,
          Multimedia
        }

        if (!Mensaje.IdChat) {
          const NuevoChat = {
            IdCaso: this.caso.IdCaso,
            IdPersona: this.personaPrincipal().IdPersona,
            Telefono: this.telefonoPrincipal(this.personaPrincipal().Telefonos)
          }
          if (NuevoChat.Telefono) {
            request.Post(`/chats/crear`, NuevoChat, r => {
              if (!r.Error) {
                Notify.create('Nuevo chat creado!')
                Mensaje.IdChat = r.IdChat
                request.Post(`/mensajes/enviar`, Mensaje, q => {
                  if (!q.Error) {
                    Notify.create('Cambio de estado comunicado correctamente')
                    const UltMsjLeido = q.IdMensaje
                    request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {
                      if (p.Error) {
                        Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
                      }
                    })
                  } else {
                    Notify.create('Falló al comunicar el cambio de estado. Razon: ' + q.Error)
                  }
                })
              } else {
                Notify.create('Falló al comunicar el cambio de estado. Razon: ' + r.Error)
              }
            })
          } else {
            Notify.create('Falló al comunicar el cambio de estado. Razon: no existe un telefono asociado')
          }
        } else {
          request.Post(`/mensajes/enviar`, Mensaje, r => {
            if (!r.Error) {
              Notify.create('Cambio de estado comunicado correctamente')
              const UltMsjLeido = r.IdMensaje
              request.Post(`/chats/${Mensaje.IdChat}/actualizar`, { IdUltimoLeido: UltMsjLeido }, p => {
                if (p.Error) {
                  Notify.create('Falló al actualizar el ultimo mensaje leído. Razon:' + p.Error)
                }
              })
            } else {
              Notify.create('Falló al comunicar el cambio de estado. Razon: ' + r.Error)
            }
          })
        }
        this.Mensaje = ''
        this.ModalMensaje = false
      } else {
        this.Mensaje = mensaje
        this.ModalMensaje = true
        this.LoadingImagenes = true
        this.checkMultimedia = false
        this.BuscarImagen = ''

        if (this.Imagenes.length === 0) {
          request.Get('/multimedia-caso', {IdCaso: this.id}, r => {
            this.LoadingImagenes = false
            if (r.Error) {
              this.$q.notify(r.Error)
            } else {
              this.LoadingImagenes = false

              r.forEach(m => {
                m.check = false

                if (m.Tipo === 'I') {
                  this.Imagenes.push(m)
                }
              })

              if (this.Imagenes.length === 0) this.Imagenes.sort((function(a, b){
                  if(a.Nombre.toLowerCase() < b.Nombre.toLowerCase()) { return -1; }
                  if(a.Nombre.toLowerCase() > b.Nombre.toLowerCase()) { return 1; }
                  return 0;
              }))
            }
          })
        } else {
          this.Imagenes.forEach(img => img.check = false)
          this.LoadingImagenes = false
        }
      }
    },
    cancelarMensaje () {
      this.Mensaje = ''
      this.ModalMensaje = false
    },
    abrirChat () {
      this.spinner = true
      const telActual = this.telefonoPrincipal(this.personaPrincipal().Telefonos)
      const idPersonaActual = this.personaPrincipal().IdPersona
      if (this.caso.IdChat) {
        request.Get(`/chats/${this.caso.IdChat}`, {}, r => {
          if (r.Error) {
            Notify.create('Falló al iniciar el chat. Razon: ' + r.Error)
          } else if (r.Telefono === telActual || !telActual || telActual === 'Sin telefono') {
            this.$router.push({
              name: 'Chat',
              query: {
                id: this.caso.IdChat,
                caratula: this.caso.Caratula ? this.caso.Caratula : 'Sin Carátula',
                telefono: r.Telefono
              }
            })
          } else {
            request.Post(`/chats/${this.caso.IdChat}/actualizar-telefono`, {Telefono: telActual, IdPersona: idPersonaActual}, c => {
              if (c.Error) {
                Notify.create('Falló al iniciar el chat. Razon: ' + r.Error)
              } else {
                this.$router.push({
                  name: 'Chat',
                  query: {
                    id: this.caso.IdChat,
                    caratula: this.caso.Caratula ? this.caso.Caratula : 'Sin Carátula',
                    telefono: c.Telefono
                  }
                })
              }
            })
          }
        })
      } else {
        const nuevoChat = {
          IdCaso: this.caso.IdCaso,
          IdPersona: this.personaPrincipal().IdPersona,
          Telefono: this.telefonoPrincipal(this.personaPrincipal().Telefonos)
        }
        request.Post(`/chats/crear`, nuevoChat, r => {
          if (r.Caratula) {
            this.ModalChat = true
            this.CaratulaCasoChat = r.Caratula
            this.IdExternoChat = r.IdExternoChat
          } else if (!r.Error) {
            Notify.create('Nuevo chat creado!')
            this.caso.IdChat = r.IdChat
          } else {
            Notify.create('Falló al iniciar un nuevo chat. Razon: ' + r.Error)
          }
          this.spinner = false
        })
      }
    },
    abrirMediacion () {
      this.ModalMediacion = true
    },
    altaMediacion (id) {
      this.caso.IdMediacion = id
    },
    reemplazarCaso () {
      request.Post('/chats/reemplazar-caso', {IdExternoChat: this.IdExternoChat, IdCaso: this.caso.IdCaso, IdPersona: this.personaPrincipal().IdPersona}, r => {
        if (r.Error) {
          Notify.create('Falló al iniciar el chat. Razon: ' + r.Error)
        } else {
          this.$router.push({
            name: 'Chat',
            query: {
              id: r.IdChat,
              caratula: this.caso.Caratula ? this.caso.Caratula : 'Sin Carátula',
              telefono: this.telefonoPrincipal(this.personaPrincipal().Telefonos)
            }
          })
        }
      })
    }
  }
}
</script>

<style scoped>
.height-90px {
  height: 90px;
}

.contenedor_personas {
  padding-left: 10px;
}

@media screen and (max-width: 600px) {
  .contenedor_personas {
    padding-left: 0 !important;
  }
}

.img--multimedia {
    height: auto;
    width: auto;
    max-width: 320px;
    max-height: 240px;
  }

  .container_multimedia {
    height: 300px;
    width: 390px;
    display: flex;
    position: relative;
    margin: 2px auto;
    justify-content: center;
    text-align: center;
    border: 10px solid;
    border-color: transparent;
    border-radius: 25px;
  }

  .nombre_multimedia {
    padding: 0px;
    min-height: 40px;
    align-items: end;
    justify-content: center;
    font-weight: bold;
    color: teal;
    font-size: 16px;
    margin-bottom: -10px;
  }
</style>
