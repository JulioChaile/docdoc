<template>
  <div class="container">
    <div class="row">
      <div class="col-12 q-pt-md q-pb-md" style="display:grid; justify-content:center;">
        <span class="text-h6 text-weight-regular">Movimientos del caso</span>
      </div>
    </div>
    <div v-if="movimientosComputados.length" class="row">
      <div
        v-for="(movimiento, index) in movimientosComputados"
        :key="index"
        :class="'bg-' + movimiento.Color + ' col-12 movimiento_container'"
      >
        <!-- Detalle -->
        <div class="seccion_inline">
          <div class="row">
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span class="text-caption text-weight-light label_movimiento">Detalle</span>
            </div>
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span
                class="text-weight-bold text-body1"
              >{{ movimiento.Detalle ? movimiento.Detalle : 'Sin detalle' }}</span>
            </div>
          </div>
        </div>
        <!-- Tipo de movimiento -->
        <div class="seccion_inline">
          <div class="row">
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span class="text-caption text-weight-light label_movimiento">Tipo de movimiento</span>
            </div>
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span
                class="text-weight-bold text-body1"
              >{{ movimiento.TipoMovimiento ? movimiento.TipoMovimiento : 'Sin tipo de movimiento' }}</span>
            </div>
          </div>
        </div>
        <!-- Fecha de alta -->
        <div class="seccion_inline">
          <div class="row">
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span class="text-caption text-weight-light label_movimiento">Fecha de alta</span>
            </div>
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span
                class="text-weight-bold text-body1"
              >{{ movimiento.FechaAlta ? movimiento.FechaAlta.slice(0, 10) : 'Sin fecha de alta' }}</span>
            </div>
          </div>
        </div>
        <!-- Fecha esperada -->
        <div class="seccion_inline">
          <div class="row">
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span class="text-caption text-weight-light label_movimiento">Fecha esperada</span>
            </div>
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span
                class="text-weight-bold text-body1"
              >{{ movimiento.FechaEsperada ? movimiento.FechaEsperada.slice(0, 10) : 'Sin fecha esperada' }}</span>
            </div>
          </div>
        </div>
        <!-- Cuaderno -->
        <div class="seccion_inline">
          <div class="row">
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span class="text-caption text-weight-light label_movimiento">Cuaderno</span>
            </div>
            <div class="col-12" style="display:grid; justify-content:flex-end;">
              <span
                class="text-weight-bold text-body1"
              >{{ movimiento.Cuaderno ? movimiento.Cuaderno: 'Sin cuaderno' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="row">
      <div class="col-12 q-pt-sm" style="display:flex; justify-content:center;">
        <span class="text-body1 text-weight-light">Este caso no posee movimientos.</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Movimientos",
  props: {
    movimientos: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    movimientosComputados() {
      return this.movimientos;
    },
  },
};
</script>

<style scoped>
.movimiento_container {
  color: white;
  padding: 5px;
  margin: 5px auto;
  display: flex;
  justify-content: center;
}
.seccion_inline {
  display: inline-block;
  min-width: 15%;
}
.label_movimiento {
  opacity: 0.75;
}
</style>
