<template>
  <object
    type="image/svg+xml"
    data="statics/docdoc-loading.svg"
    alt="Docdoc! Gestión"
    width="200"
    height="100"
    :style="isMobile ? 'position: relative; display: block; margin-left: 35%;' : 'position: relative; display: block; margin:auto;'"
    >
  </object>
</template>

<script>

export default {
  data () {
    return {
      isMobile: false
    }
  },
  created () {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
      this.isMobile = true
    }
  }
}
</script>
