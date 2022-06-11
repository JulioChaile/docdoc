<template>
  <tribunales :preview="preview" />
</template>

<script>
import Tribunales from '../components/Compartidos/Tribunales'

export default {
  name: 'TribunalesHome',
  props: {
    preview: {
      type: String,
      required: false,
      default: ''
    }
  },
  components: { Tribunales }
}
</script>
