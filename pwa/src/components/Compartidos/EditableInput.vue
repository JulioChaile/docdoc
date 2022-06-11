<template>
  <div class="row main_container q-pa-sm">
    <div class="col-12">
      <span :class="'text-weight-bolder text-body2 text-' + labelColor">{{ label }}</span>
    </div>
    <div class="col-12">
      <q-input v-model="valor" outlined dense />
    </div>
  </div>
</template>

<script>
export default {
  name: 'EditableInput',
  props: {
    text: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      default: 'dato'
    },
    labelColor: {
      type: String,
      default: 'primary'
    }
  },
  data () {
    return {
      valor: this.text
    }
  }
}
</script>

<style scoped>
.main_container {
  /* background-color: rgba(0, 0, 0, 0.08); */
  border-radius: 10px;
  max-width: fit-content;
}
</style>
