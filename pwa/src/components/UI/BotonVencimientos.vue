<template>
    <q-item class="notificacion" style="align-items:center">
        <q-item-section @click="irVencimientos()">
        <div class="row items-end">
            <q-icon class="on-left" name="new_releases" color="red" size="sm" />
            <div style="justify-content: center; text-align: center">
                Vencimientos
            </div>
        </div>
        </q-item-section>
    </q-item>
</template>

<script>
export default {
  name: 'BotonVencimientos',
  methods: {
    irVencimientos () {
      const routeData = this.$router.resolve({
        name: 'Vencimientos'
      })
      window.open(routeData.href, '_blank')
      this.$emit('vencimientosVistos')
    }
  }
}
</script>

<style scoped>
    .notificacion {
        cursor:pointer;
        margin: 5px;
        -webkit-box-shadow: 0px 0px 15px 0px rgba(221,221,221,1);
        -moz-box-shadow: 0px 0px 15px 0px rgba(221,221,221,1);
        box-shadow: 0px 0px 15px 0px rgba(221,221,221,1);
        transition: all 0.4s ease;
    }
</style>
