<template>
  <div :class="[!noGutters ? 'q-pa-sm' : '', 'row main_container']">
    <div class="col-12">
      <span :class="'text-weight-bolder text-body2 text-' + labelColor">{{ label }}</span>
    </div>
    <div :class="[textClass, 'col-12']">{{ text }}</div>
  </div>
</template>

<script>
export default {
  name: 'DataItem',
  props: {
    text: {
      type: String,
      default: ''
    },
    textClass: {
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
    },
    noGutters: {
      type: Boolean,
      default: false
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
