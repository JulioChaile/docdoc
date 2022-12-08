<template>
  <div class="container_card">
    <div class="botones_container column" v-if="MultimediaSeleccionado.length > 0">
      <q-btn
        label="Eliminar"
        color="negative"
        @click="modalEliminar = true"
        push
      />
      <q-btn
        label="Enviar por Mail"
        color="teal"
        class="boton_alinear"
        @click="modalMail = true"
        push
      />
      <q-btn
        v-if="MultimediaSeleccionado.filter(m => m.Tipo !== 'I').length === 0"
        label="Generar PDF"
        color="teal"
        @click="modalPDF = true"
        push
      />
    </div>

    <div class="row justify-between">
      <div v-if="Multimedia.length === 0">
        No hay archivos añadidos aún.
      </div>
      <div
        v-else
        v-for="m in Multimedia"
        :key="m.URL"
        class="col-grow-4 column container__multimedia"
        :style="m.check ? 'border-color: red !important' : ''"
      >
        <q-checkbox
          v-model="m.check"
          class="check_multimedia"
          @input="selectArchive(m)"
          color="red"
        />
        <div class="img-container" >
          <div
            v-if="m.Tipo === 'A'"
            class="column"
          >
            <q-avatar
              size="200px"
              color="black"
              text-color="yellow"
              icon="graphic_eq"
              class="icon_mult"
            />
            <audio controls>
              <source :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`">
            </audio>
          </div>

          <video
            v-else-if="m.Tipo === 'V'"
            class="class-multimedia"
            :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`"
            controls
          ></video>

          <img
            v-else-if="m.Tipo === 'I'"
            class="class-multimedia"
            :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`"
          />

          <q-avatar
            v-else
            square
            rounded
            size="200px"
            color="white"
            text-color="black"
            class="icon_mult"
          >
            {{ format(m.URL) }}
          </q-avatar>
        </div>
        <q-item
          class="nombre_multimedia"
          clickable
        >
          {{ m.Nombre }}
          <q-menu v-model="m.showing">
            <q-list style="min-width: 100px">
              <q-item clickable v-close-popup @click="descargarArchivo(m.URL, m.Nombre)">
                <q-item-section>Descargar</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="abrirVisor(m)">
                <q-item-section>Ver Archivo</q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable v-close-popup @click="editarNombre(false, m.Nombre, m.IdMultimedia)">
                <q-item-section>Editar Nombre</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-item>
      </div>
    </div>

    <!-- Modal Visor -->
    <q-dialog v-model="modalVisor">
      <VisorArchivo
        :archivo="archivoVer"
        @cerrar="modalVisor = false"
      />
    </q-dialog>

    <!-- Modal Editar Nombre -->
    <q-dialog v-model="modalNombre">
      <q-card class="card_modal_nombre" style="padding: 1em; width: 70%; display: grid">
        <q-input
          class="input"
          type="text"
          v-model="nombreEditar"
        />
        <q-btn
          style="justify-self: center; margin-top: 10px"
          class="boton_alinear"
          color="teal"
          label="Aceptar"
          @click="editarNombre(true)"
        />
      </q-card>
    </q-dialog>

    <!-- Modal: Eliminar archivos -->
    <q-dialog
      v-model="modalEliminar"
      prevent-close
    >
      <q-card style="padding:1rem;">
        <div v-if="!eliminando">
          <span class="text-h6">Eliminar Archivos</span>
          <span>
            <p>
              Al eliminar archivos, estos no podrán ser recuperados en el futuro.
              <br>
              ¿Está seguro que desea eliminar estos archivos?
            </p>
          </span>
          <div class="botones_eliminar_container">
            <q-btn color="negative" label="Eliminar" @click="eliminarArchivos()" />
            <q-btn flat label="Cancelar" @click="modalEliminar = false" />
          </div>
        </div>
        <div v-else class="eliminando">
          Espere unos instantes, se estan eliminando los archivos.
          <br>
          Esto puede tardar algunos segundos.
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal: Enviar Mail -->
    <q-dialog v-model="modalMail">
      <EnviarMail
        :Multimedia="MultimediaSeleccionado"
        :ContenidoPDF="ContenidoPDF"
        :asunto="datosCaso.Caratula ? datosCaso.Caratula : ''"
        @mailEnviado="mailEnviado"
        @cerrar="modalMail = false"
      />
    </q-dialog>

    <!-- Modal: Generar PDF -->
    <q-dialog v-model="modalPDF">
      <GenerarPDF
        :Multimedia="MultimediaSeleccionado"
        @enviarMail="enviarPDF"
        @cerrar="modalPDF = false"
      />
    </q-dialog>
  </div>
</template>

<script>
import request from '../../request'
import VisorArchivo from '../../components/Caso/VisorArchivo'
import EnviarMail from '../../components/Compartidos/EnviarMail'
import GenerarPDF from '../../components/Archivos/GenerarPDF'

export default {
  name: 'Archivos',
  props: [ 'Archivos', 'IdCaso' ],
  components: {
    VisorArchivo,
    EnviarMail,
    GenerarPDF
  },
  data () {
    return {
      Multimedia: [],
      MultimediaSeleccionado: [],
      IdMultimedia: 0,
      datosCaso: {},
      personas: [],
      modalVisor: false,
      archivoVer: {},
      modalNombre: false,
      nombreEditar: '',
      modalEliminar: false,
      eliminando: false,
      modalMail: false,
      modalPDF: false,
      asunto: '',
      ContenidoPDF: ''
    }
  },
  created () {
    this.Multimedia = this.Archivos.slice(0)

    if (this.IdCaso) {
      request.Get(`/casos`, { id: this.IdCaso }, (r) => {
        if (!r.Error) {
          this.caso = r
          // Datos del caso:
          this.datosCaso = {
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
            IdMediacion: r.IdMediacion
          }
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
        } else {
          console.log('Hubo un error al traer el caso.')
        }
      })
    }
  },
  methods: {
    selectArchive (m) {
      if (m.check) {
        this.MultimediaSeleccionado.push({
          IdMultimedia: m.IdMultimedia,
          URL: m.URL,
          OrigenMultimedia: m.OrigenMultimedia,
          Tipo: m.Tipo
        })
      } else {
        const i = this.MultimediaSeleccionado.findIndex(f => f.IdMultimedia === m.IdMultimedia)
        this.MultimediaSeleccionado.splice(i, 1)
      }
    },
    format (name) {
      return name.split('.').reverse()[0]
    },
    descargarArchivo (url, name) {
      location.href = `https://io.docdoc.com.ar/api/multimedia?file=${url}&download=true&name=${name}`
    },
    editarNombre (editar, nombre, id) {
      if (editar) {
        request.Post('/multimedia-caso/editar-nombre', {IdCaso: this.IdCaso, IdMultimedia: this.IdMultimedia, Nombre: this.nombreEditar}, r => {
          if (!r.Error) {
            const i = this.Multimedia.findIndex(m => m.IdMultimedia === this.IdMultimedia)
            this.Multimedia[i].Nombre = this.nombreEditar

            this.$q.notify('Se ha editado el archivo con exito.')
          } else {
            this.$q.notify(r.Error)
          }
          this.modalNombre = false
          this.nombreEditar = ''
          this.IdMultimedia = 0
        })
      } else {
        this.modalNombre = true
        this.nombreEditar = nombre
        this.IdMultimedia = id
      }
    },
    abrirVisor (m) {
      this.archivoVer = {
        URL: m.URL,
        Tipo: m.Tipo,
        Formato: this.format(m.URL)
      }

      this.modalVisor = true
    },
    mailEnviado () {
      this.Multimedia.forEach(m => { m.check = false })
      this.modalMail = false
      this.MultimediaSeleccionado = []
      this.ContenidoPDF = ''
    },
    enviarPDF (multimedia, contenido) {
      this.MultimediaSeleccionado = multimedia
      this.ContenidoPDF = contenido
      this.modalMail = true
      this.modalPDF = false
    },
    eliminarArchivos () {
      const Multimedia = this.MultimediaSeleccionado
      const IdCaso = this.IdCaso

      this.eliminando = true

      request.Post('/multimedia-caso/eliminar', {IdCaso: IdCaso, Multimedia: JSON.stringify(Multimedia)}, r => {
        if (!r.Error) {
          for (let i = this.Multimedia.length - 1; i >= 0; i--) {
            const IdMultimedia = this.Multimedia[i].IdMultimedia

            const index = Multimedia.findIndex(m => m.IdMultimedia === IdMultimedia)

            if (index >= 0) { this.Multimedia.splice(i, 1) }
          }

          this.$q.notify('Los archivos se han eliminado con exito.')
        } else {
          this.Multimedia.forEach(m => { m.check = false })

          this.$q.notify(r.Error)
        }
        this.eliminando = false
        this.modalEliminar = false

        this.MultimediaSeleccionado = []
      })
    }
  }
}
</script>

<style>
.class-multimedia {
  height: auto;
  width: auto;
  max-height: 240px;
  max-width: 320px;
  margin: 0px auto;
}

.img-container {
  height: 240px;
  width: 320px;
  margin: 10px auto;
  display: flex;
  align-items: flex-end;
}

.container_card {
  max-width: 1500px !important;
  padding: 0 5%;
  position: relative;
}

.container__multimedia {
  height: 320px;
  width: 410px;
  border: 10px solid transparent !important;
  border-radius: 25px !important;
  position: relative;
  margin: 2px
}

.icon_mult {
  margin: 0px auto;
}

.botones_container {
  position: absolute;
  top: 25px;
  right: 0px;
  z-index: 100;
}

.eliminando {
  text-align: center;
  padding: 35px;
}

.botones_eliminar_container {
  display: flex;
  justify-content: space-evenly;
}

.boton_alinear {
  margin: 10px auto;
}

.card_modal_nombre {
  padding: 1em;
  width: 70%;
}
</style>
