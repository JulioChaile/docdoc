<template>
  <div>
    <div v-if="tipoCasos.length === 0">
      <Loading />
    </div>
    <div v-else style="display: flex; flex-flow: row wrap; justify-content: space-evenly; margin-bottom: 100px;">
    <q-btn v-for="tc in tipoCasos" :key='tc.IdTipoCaso'
      rounded
      :icon="ParamsTipoCaso[tc.TipoCaso] ? ParamsTipoCaso[tc.TipoCaso].icon : ''"
      v-bind:color="tipoCaso === tc.TipoCaso ? 'warning' : 'primary'"
      size="lg"
      :label="tc.TipoCaso"
      @click="seleccionar(tc.TipoCaso), $emit('set-tipoCaso', tc)"
      style="margin: 10px;"
    />
  </div>
  </div>
</template>

<script>
import ParamsTipoCaso from '../parameters/TiposCaso'
import Loading from '../components/Loading'
import request from '../request'
export default {
  components: {
    Loading
  },
  name: 'TiposCaso',
  data () {
    return {
      tipoCasos: [],
      ParamsTipoCaso
    }
  },
  created () {
    request.Get('/tipos-caso', {}, r => {
      if (!r.Error) {
        r.forEach(tc => {
          this.tipoCasos.push(tc)
        })
      }
    })
  },
  props: ['tipoCaso'],
  methods: {
    seleccionar (tipoCaso) {
      this.tipoCaso = tipoCaso
    },
    deberia_estar_deshabilitado () {
      this.siguiente_deshabilitado = this.tipoCaso === ''
    }
  }
}
</script>

<style>

</style>
