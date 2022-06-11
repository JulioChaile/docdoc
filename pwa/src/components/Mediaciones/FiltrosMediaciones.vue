<template>
  <q-drawer
    v-model="mostrar"
    :width="300"
    :breakpoint="700"
    content-class="bg-grey-3 text-black "
    content-style="border-style: solid; border-width: 1px"
  >
    <q-expansion-item
      expand-separator
      icon="assignment_late"
      label="Estados"
      default-opened
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="Estados.length === 0" />
        <q-checkbox
          v-else
          v-for="e in Estados"
          :key="e.value"
          :label="`${e.label} (${e.cantidad})`"
          v-model="e.check"
          @input="seleccionar(e.check, e.value, 'Estados')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="list"
      label="Grilla"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="Grilla.length === 0" />
        <q-checkbox
          v-else
          v-for="g in Grilla"
          :key="g.value"
          :label="g.label"
          v-model="g.check"
          @input="seleccionar(g.check, g.value, 'Grilla')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="attach_money"
      label="Bonos"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="Bonos.length === 0" />
        <q-checkbox
          v-else
          v-for="bo in Bonos"
          :key="bo.value"
          :label="bo.label"
          v-model="bo.check"
          @input="seleccionar(bo.check, bo.value, 'Bonos')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="info"
      label="Tipos de Beneficio"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="Beneficios.length === 0" />
        <q-checkbox
          v-else
          v-for="be in Beneficios"
          :key="be.value"
          :label="be.label"
          v-model="be.check"
          @input="seleccionar(be.check, be.value, 'Beneficios')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="o_info"
      label="Estados de Beneficio"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="EstadosBeneficio.length === 0" />
        <q-checkbox
          v-else
          v-for="ebe in EstadosBeneficio"
          :key="ebe.value"
          :label="ebe.label"
          v-model="ebe.check"
          @input="seleccionar(ebe.check, ebe.value, 'EstadosBeneficio')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="people"
      label="Origenes"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="Origenes.length === 0" />
        <q-checkbox
          v-else
          v-for="o in Origenes"
          :key="o.value"
          :label="o.label"
          v-model="o.check"
          @input="seleccionar(o.check, o.value, 'Origenes')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="cases"
      label="Tipos de Caso"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="TiposCaso.length === 0" />
        <q-checkbox
          v-else
          v-for="tc in TiposCaso"
          :key="tc.value"
          :label="tc.label"
          v-model="tc.check"
          @input="seleccionar(tc.check, tc.value, 'TiposCaso')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="gavel"
      label="Causa Penal"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="EstadosCP.length === 0" />
        <q-checkbox
          v-else
          v-for="ecp in EstadosCP"
          :key="ecp.value"
          :label="`${ecp.label} (${ecp.cantidad})`"
          v-model="ecp.check"
          @input="seleccionar(ecp.check, ecp.value, 'EstadosCP')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="folder_open"
      label="Documentacion del Caso"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="EstadosDoc.length === 0" />
        <q-checkbox
          v-else
          v-for="ed in EstadosDoc"
          :key="ed.value"
          :label="`${ed.label} (${ed.cantidad})`"
          v-model="ed.check"
          @input="seleccionar(ed.check, ed.value, 'EstadosDoc')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="local_hospital"
      label="Historia Clínica"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <Loading v-if="EstadosHC.length === 0" />
        <q-checkbox
          v-else
          v-for="ehc in EstadosHC"
          :key="ehc.value"
          :label="`${ehc.label} (${ehc.cantidad})`"
          v-model="ehc.check"
          @input="seleccionar(ehc.check, ehc.value, 'EstadosHC')"
        />
      </div>
    </q-expansion-item>
    <q-separator />
    <q-expansion-item
      expand-separator
      icon="reorder"
      label="Ordenar"
    >
      <div class="column bg-white" style="padding-left: 1em">
        <q-option-group
          :options="Orden"
          type="radio"
          v-model="OrdenSeleccionado"
          @input="ordenar"
        />
      </div>
    </q-expansion-item>
  </q-drawer>
</template>

<script>
import Loading from '../../components/Loading'
import QRadio from 'quasar'

export default {
  name: 'FiltrosMediaciones',
  props: [
    'mostrar',
    'Grilla',
    'Bonos',
    'Beneficios',
    'EstadosBeneficio',
    'Origenes',
    'TiposCaso',
    'Estados',
    'EstadosCP',
    'EstadosDoc',
    'EstadosHC',
    'Orden'
  ],
  components: { Loading, QRadio },
  data () {
    return {
      GrillaSeleccionados: [],
      BonosSeleccionados: [],
      BeneficiosSeleccionados: [],
      EstadosBeneficioSeleccionados: [],
      OrigenesSeleccionados: [],
      TiposCasoSeleccionados: [],
      EstadosSeleccionados: [],
      EstadosCPSeleccionados: [],
      EstadosDocSeleccionados: [],
      EstadosHCSeleccionados: [],
      OrdenSeleccionado: 'FechaAlta'
    }
  },
  created () {
    this.Grilla.forEach(g => {
      if (g.check) { this.GrillaSeleccionados.push(g.value) }
    })
  },
  watch: {
    Grilla: {
      handler: function () {
        this.GrillaSeleccionados = []
        this.Grilla.forEach(g => {
          if (g.check) { this.GrillaSeleccionados.push(g.value) }
        })
      },
      deep: true
    }
  },
  methods: {
    seleccionar (check, id, cat) {
      const objeto = `${cat}Seleccionados`

      if (check) {
        this[objeto].push(id)
      } else {
        this[objeto] = this[objeto].filter(g => g !== id)
      }

      this.$emit('seleccionarFiltro', this[objeto], objeto)
    },
    ordenar () {
      this.$emit('ordenar', this.OrdenSeleccionado)
    }
  }
}
</script>

<style lang="scss">
.q-page-container .q-layout .q-drawer-container .q-drawer {
  background-color: transparent !important;
}
.fit {
  height: auto !important
}
</style>
