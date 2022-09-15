<template>
  <div style="width: 100px !important">
    <q-checkbox
      v-if="!caso.Finalizado"
      v-model="model"
      @input="$emit('checkCaso', model)"
      class="align-center"
    />
  </div>
</template>

<script>
export default {
  props: ['caso', 'i'],
  data () {
    return {
      model: false
    }
  }
}
</script>
