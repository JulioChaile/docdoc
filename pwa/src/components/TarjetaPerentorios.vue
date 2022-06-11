<template>
  <div>
    <q-card :style="'background-color: ' + color() + ';'">
      <div class="row">
        <div class="col-12">
            <span>Tarjeta</span>
        </div>
      </div>
    </q-card>
  </div>
</template>

<script>
export default {
  name: 'TarjetaPerentorios',
  data () {
    return {
      colorJudicial: 'rgba(62, 95, 218, 0.8)',
      colorExtrajudicial: 'rgba(232, 70, 70, 0.8)'
    }
  },
  props: {
    perentorio: {
      type: Object,
      default: {}
    },
    ModoGestion: {
      type: String,
      default: ''
    }
  },
  methods: {
    color () {
      switch (this.ModoGestion) {
        case 'J':
          return this.colorJudicial
        case 'E':
          return this.colorExtrajudicial          
      }
    }
  }
}
</script>
