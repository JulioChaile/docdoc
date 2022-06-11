<template>
  <object
    type="image/svg+xml"
    data="statics/docdoc-small-loading.svg"
    alt="Docdoc! Gestión"
    :width="width"
    :height="height"
  ></object>
</template>

<script>
export default {
  props: {
    width: {
      Type: Number,
      default: 0
    },
    height: {
      Type: Number,
      default: 0
    }
  },
  data() {
    return {
      isMobile: false
    };
  },
  created() {
    if (
      /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent
      )
    ) {
      this.isMobile = true;
    }
  }
};
</script>
