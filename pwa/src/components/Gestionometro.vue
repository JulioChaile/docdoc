<template>
  <div class="contenedor shadow-2" v-bind:style="{'border' : '1px solid '+ this.borde}">
    <div class="temperatura" v-bind:style="{'background-color' : this.temperatura , 'height': this.altura + '%', 'border' : '2px solid '+ this.borde}">
    </div>
    <p class="verticalP">Gestionómetro</p>
    <q-tooltip
      self="top left"
      anchor="bottom right"
      v-bind:style="{'background-color' : this.temperatura,'border' : '1px solid '+ this.borde}"
    >
      <p>Puntuación de su estudio: {{this.indigestion}}</p>
      <p>Puntuación promedio de su competencia: 0.9</p>
      <p>Si mejora su puntuación recibirá mas y mejores casos!</p>
    </q-tooltip>
  </div>
</template>
<script>
export default {
  methods: {
    colorear: function (indigestion) {
      var hue = ((1 - indigestion) * 120).toString(10)
      this.temperatura = ['hsl(', hue, ',100%,43%)'].join('')
      this.borde = ['hsl(', hue, ',100%,40%)'].join('')
    }
  },
  data () {
    return {
      // indigestión es el indice de gestión del 0 a 1
      // lo inicializo en 0.5
      indigestion: 0.5,
      // color es el color que toma el gestionometro en hsl
      temperatura: '',
      // altura.. es la altura del gestionometro de 0 a 100%
      altura: 100,
      // borde es el contorno coloreado de la barrita
      borde: ''
    }
  },
  mounted () {
    // Aca habria que disparar el ajax que trae info de verdad
    // Se redefine el valor del Indice de Gestión
    // y luego en el callback llamar a la funcion colorear()
    this.indigestion = 0.7
    this.altura = 100 * this.indigestion
    this.colorear(this.indigestion)
  }
}
</script>
<style>
.contenedor{
  border-radius: 10px;
  min-height:150px;
  min-width:40px;
  position: relative;
  vertical-align: bottom;
  margin-bottom: .5em;
  background-color:white
}
.temperatura{
  border-radius:10px;
  vertical-align: bottom;
  position: absolute;
  width:100%;
  bottom: -1px;
  left:0px;
}
.verticalP{
    transform: rotate(-90deg);
    transform-origin: right, top;
    -ms-transform: rotate(-90deg);
    -ms-transform-origin:right, top;
    -webkit-transform: rotate(-90deg);
    -webkit-transform-origin:right, top;
    position: absolute;
    bottom: 22%;
    left: -90%;
    font-weight: 500;
    color: #ddd
}
</style>
