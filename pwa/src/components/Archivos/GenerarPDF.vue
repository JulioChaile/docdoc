<template>
  <q-card style="width: 100%">
    <q-item style="background-color: black;">
      <span class="q-subheading" style="color:white;">Generar PDF</span>
    </q-item>
    <div style="padding:1rem;">
      <span>
        <p>
          Ingrese el texto con el que comenzara el PDF (opcional)
        </p>
      </span>
      <q-input
        v-model="contenidoPDF"
        label="Contenido PDF (opcional)"
        style="width: 100%"
        type="textarea"
        :rows="3"
      />
      <q-carousel
        v-model="slide"
        transition-prev="scale"
        transition-next="scale"
        swipeable
        animated
        control-color="teal"
        navigation
        padding
        arrows
        height="auto"
      >
        <q-carousel-slide
          v-for="m in MultimediaPDF"
          :key="m.URL"
          :name="m.URL"
          class="column no-wrap flex-center"
        >
          <div class="shadow-1 rounded-borders">
            <img class="img--multimedia" style="padding: 1em 1em 1em 1em" :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`">
          </div>
          <q-input
            v-model="m.Titulo"
            label="Titulo de la Imagen (opcional)"
            style="width: 100%"
            type="text"
          />
          <q-input
            v-model="m.Descripcion"
            label="Descripcion de la Imagen (opcional)"
            style="width: 100%"
            type="textarea"
            :rows="3"
          />
        </q-carousel-slide>
      </q-carousel>
      <div class="row justify-center" style="margin-top: 15px; margin-bottom: 15px">
        <q-btn
          color="teal"
          label="Enviar Mail"
          @click="enviarMail()"
        />
        <q-btn
          v-if="!generando"
          style="margin-left: 10px; margin-right: 10px"
          color="teal"
          label="Descargar"
          @click="generarPDF()"
        />
        <div v-else style="margin-left: 30px; margin-right: 30px">
          <q-spinner
            color="teal"
            class="self-center"
            size="2em"
          />
        </div>
        <q-btn
          color="negative"
          label="Cerrar"
          @click="$emit('cerrar')"
        />
      </div>
    </div>
  </q-card>
</template>

<script>
import { QSpinner, QCarousel, QCarouselSlide, QCarouselControl } from 'quasar'
export default {
  name: 'GenerarPDF',
  props: [ 'Multimedia' ],
  components: {
    QSpinner,
    QCarousel,
    QCarouselSlide,
    QCarouselControl
  },
  data () {
    return {
      slide: '',
      contenidoPDF: '',
      generando: false,
      MultimediaPDF: []
    }
  },
  created () {
    this.Multimedia.forEach(m => {
      this.MultimediaPDF.push({
        IdMultimedia: m.IdMultimedia,
        URL: m.URL,
        Tipo: m.Tipo,
        OrigenMultimedia: m.OrigenMultimedia,
        Titulo: '',
        Descripcion: ''
      })
    })

    this.slide = this.MultimediaPDF[0].URL
  },
  methods: {
    generarPDF () {
      this.generando = true
      setTimeout(() => {
        this.generando = false
      }, 1500)
      const contenidoPDF = this.contenidoPDF.replace(/\r?\n/g, '<br>')
      const multimedia = this.MultimediaPDF.map(m => {
        return {
          IdMultimedia: m.IdMultimedia,
          URL: m.URL,
          Tipo: m.Tipo,
          OrigenMultimedia: m.OrigenMultimedia,
          Titulo: m.Titulo,
          Descripcion: m.Descripcion.replace(/\r?\n/g, '<br>')
        }
      })
      location.href = `https://io.docdoc.com.ar/api/multimedia-caso/generar-pdf?Multimedia=${JSON.stringify(multimedia)}&ContenidoPDF=${contenidoPDF}`
    },
    enviarMail () {
      const contenidoPDF = this.contenidoPDF.replace(/\r?\n/g, '<br>')
      const multimedia = this.MultimediaPDF.map(m => {
        return {
          IdMultimedia: m.IdMultimedia,
          URL: m.URL,
          Tipo: m.Tipo,
          OrigenMultimedia: m.OrigenMultimedia,
          Titulo: m.Titulo,
          Descripcion: m.Descripcion.replace(/\r?\n/g, '<br>')
        }
      })

      this.$emit('enviarMail', multimedia, contenidoPDF)
    }
  }
}
</script>

<style>
  .img--multimedia {
    height: auto;
    width: auto;
    max-width: 320px;
    max-height: 240px;
  }
</style>
