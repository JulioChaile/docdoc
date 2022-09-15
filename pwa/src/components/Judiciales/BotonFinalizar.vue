<template>
  <div class="row justify-center q-my-lg">
    <q-btn color="positive" label="Finalizar Casos" @click="$emit('finalizar')" />
  </div>
</template>

<script>
export default {
  props: ['casosCheck'],
  data () {
    return {
      model: false
    }
  }
}
</script>
