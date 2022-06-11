<template>
  <div>
    <div v-if="loading">
      <Loading />
    </div>
    <div
      class="row items-end justify-center"
    >
      <div
        :class="isMobile() ? 'col-3' : 'col'"
        v-for="casos in !limit ? Casos : CasosPorTipo"
        :key="casos.TipoCaso"
        style="margin-bottom: 2em; justify-content: center; text-align: center;"
      >
        <span style="font-weight:bold">{{casos.TipoCaso}}</span>
        <br>
        <q-avatar
          square
          rounded
          size="5em"
          color="accent"
          text-color="white"
        >
          {{casos.Cantidad}}
        </q-avatar>
      </div>
    </div>
    <div class="row justify-center" v-if="CasosPorTipo.length > 4">
      <q-toggle v-model="limit" label="Ver más" />
    </div>
    <div v-if="CasosPorTipo.length === 0 && !loading">
      No existen casos cargados aún.
    </div>
  </div>
</template>

<script>
import request from '../request'
import Loading from '../components/Loading'

export default {
  data () {
    return {
      CasosPorTipo: [],
      Casos: [],
      loading: true,
      limit: false
    }
  },
  components: { Loading },
  created () {
    request.Get('/casos/numero-casos', {}, r => {
      r.forEach(c => {
        this.CasosPorTipo.push(c)
      })
      this.CasosPorTipo.sort((a, b) => {
        return b.Cantidad - a.Cantidad
      })
      this.Casos = this.CasosPorTipo.slice(0, 4)
      this.loading = false
    })
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

</style>
