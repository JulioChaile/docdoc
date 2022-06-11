<template>
  <q-card class="q-pa-sm">
    <div class="full-width text-center text-bold q-pt-sm">
      Seleccione las columnas que quiera ver en la hoja de excel y haga click en EXPORTAR.
    </div>

    <q-separator />

    <div class="row justify-start items-center q-pa-sm">
      <q-checkbox
        v-for="c in ArrayColumnas"
        :key="c.key"
        v-model="c.check"
        :label="c.label"
        class="col-6"
      />
    </div>

    <div class="full-width row justify-center q-mb-md">
      <q-btn
        dense
        label="Destildar Todo"
        color="warning"
        @click="destildar"
      />
    </div>

    <q-separator />

    <div class="full-width flex justify-center items-center q-pt-md text-center">
      <div v-if="exportado">
        Su descarga comenzará en breve
      </div>
      <q-btn
        v-else
        label="Exportar"
        color="teal"
        @click="exportar"
      />
    </div>
  </q-card>
</template>

<script>
import XLSX from 'xlsx'

export default {
  name: 'ExportExcel',
  props: ['ArrayInicial', 'name'],
  data () {
    return {
      ArrayColumnas: [],
      exportado: false
    }
  },
  created () {
    const keys = Object.keys(this.ArrayInicial[0])

    this.ArrayColumnas = keys.map(k => {
      return {
        label: k.split('_').join(' '),
        check: true,
        key: k
      }
    })
  },
  methods: {
    destildar () {
      this.ArrayColumnas.forEach(c => { c.check = false })
    },
    exportar () {
      const keys = this.ArrayColumnas.filter(a => a.check).map(a => a.key)
      const nombresColumnas = keys.map(k => k.split('_').join(' '))

      let arrayFinal = []

      arrayFinal.push(nombresColumnas)

      this.ArrayInicial.forEach(r => {
        let a = []

        keys.forEach(k => {
          a.push(r[k])
        })

        arrayFinal.push(a)
      })

      var wb = XLSX.utils.book_new()
      wb.SheetNames.push(this.name)
      var ws = XLSX.utils.aoa_to_sheet(arrayFinal)
      wb.Sheets[this.name] = ws
      XLSX.writeFile(wb, `${this.name}.xlsx`)
      this.exportado = true
    }
  }
}
</script>
