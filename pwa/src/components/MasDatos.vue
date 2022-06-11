<template>
<div class="form__container" style="width: 50vw;">
  <div v-for="(parametros, categoria) in categorias" :key="categoria.Parametros">
    <q-expansion-item :label="categoria" style="margin-top:2vw">
      <div v-for="parametro in parametros" :key="parametro">
        <div v-if="parametro.TipoDato === 'string'">
          <q-input class="input" type="text" :label="parametro.Descripcion" v-model= "parametro.Valor"/>
        </div>
        <div v-if="parametro.TipoDato === 'integer'">
          <q-input class="input" type="num" :label="parametro.Descripcion" v-model= "parametro.Valor"/>
        </div>
      </div>
    </q-expansion-item>
  </div>
</div>
</template>

<script>

export default {
  props: ['categorias', 'IdRTC'],
  name: 'MasDatos',
  data () {
    return {
    }
  }
}
</script>

<style>
.form__container {
  margin-bottom: 15px;
}

.form__container > .input {
  max-width: 50vw;
}
</style>
