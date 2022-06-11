<template>
  <q-card style="background-color: white; max-width: 2600px !important; position: relative">
    <span class="btn_salir" @click="$emit('cerrar')">
      <q-tooltip>Salir</q-tooltip>
      X
    </span>
    <div class='_container column'>
      <div class="column align-center" v-if="archivo.Tipo === 'A'">
        <q-avatar
          size="200px"
          color="black"
          text-color="yellow"
          icon="graphic_eq"
          class="icon_mult"
        />
        <audio controls>
          <source :src="src">
        </audio>
      </div>
      <video v-else-if="archivo.Tipo === 'V'" style="max-height: 600px" :src="src" controls></video>
      <img  v-else-if="archivo.Tipo === 'I'" :src="src">
      <pdf
        v-else-if="archivo.Formato === 'pdf' || archivo.Formato === 'PDF'"
        v-for="i in numPagesComp"
        :key="i"
        :src="src"
        :page="i"
        @num-pages="numPages = $event && $event > numPages ? $event : numPages"
        style="display: inline-block; width: 600px"
      ></pdf>
      <iframe v-else :width="isMobile() ? '500px' : '2480px'" :height="isMobile() ? '500px' : '800px'"  scrolling="auto" :src="src" frameborder="0"></iframe>
    </div>
  </q-card>
</template>

<script>
import pdf from 'vue-pdf'

export default {
  name: 'VisorArchivo',
  props: ['archivo'],
  components: { pdf },
  data () {
    return {
      src: '',
      numPages: 1
    }
  },
  async created () {
    this.src = await this.archivo.Formato === 'pdf'
      ? pdf.createLoadingTask(`https://io.docdoc.com.ar/api/multimedia?file=${this.archivo.URL}`)
      : `https://io.docdoc.com.ar/api/multimedia?file=${this.archivo.URL}`
  },
  computed: {
    numPagesComp () {
      let array = []
      for (let i = 1; i <= this.numPages; i++) {
        array[i - 1] = i
      }
      return array
    }
  },
  methods: {
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
    }
  }
}
</script>

<style>
  ._container {
    display: flex;
    justify-content: center;
    height: auto;
    width: auto;
    margin: 1em 1em 1em 1em;
    max-width: 2480px !important;
    max-height: none !important;
  }

  .btn_salir {
    cursor: pointer;
    position: absolute;
    color: red;
    font-weight: bold;
    top: 15px;
    right: 15px;
    z-index: 100;
  }
</style>
