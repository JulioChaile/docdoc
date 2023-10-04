<template>
    <q-page>
        <div v-if="loading" class="full-width flex justify-center">
            <Loading />
        </div>

        <div v-else class="row">
            <div v-if="tableros.length === 0" class="col-12 text-center q-pt-lg q-mt-lg text-bold">
                No hay tableros cargados.
            </div>

            <div
                v-for="t in tableros"
                :key="t.IdTipoMovimientoTablero"
                class="col-4 justify-center align-center q-pa-lg"
            >
                <table border="1">
                    <tr>
                        <th class="text-bold full-width">{{ t.TipoMovimiento }}</th>
                        <th class="text-bold">Hoy</th>
                        <th class="text-bold">Vencidos</th>
                        <th class="text-bold">Futuros</th>
                    </tr>

                    <tr>
                        <td class="bg-negative text-white text-bold">Perentorios</td>
                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantHoyPerentorios > 0 && abrirModal('negative', t.TipoMovimiento, 'hoy')"
                        >
                            {{ t.CantHoyPerentorios }}
                        </td>

                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantVencidosPerentorios > 0 && abrirModal('negative', t.TipoMovimiento, 'vencidos')"
                        >
                            {{ t.CantVencidosPerentorios }}
                        </td>

                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantFuturosPerentorios > 0 && abrirModal('negative', t.TipoMovimiento, 'futuros')"
                        >
                            {{ t.CantFuturosPerentorios }}
                        </td>
                    </tr>

                    <tr>
                        <td class="bg-primary text-white text-bold">Gestión Estudio</td>
                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantHoyGestionEstudio > 0 && abrirModal('primary', t.TipoMovimiento, 'hoy')"
                        >
                            {{ t.CantHoyGestionEstudio }}
                        </td>

                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantVencidosGestionEstudio > 0 && abrirModal('primary', t.TipoMovimiento, 'vencidos')"
                        >
                            {{ t.CantVencidosGestionEstudio }}
                        </td>

                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantFuturosGestionEstudio > 0 && abrirModal('primary', t.TipoMovimiento, 'futuros')"
                        >
                            {{ t.CantFuturosGestionEstudio }}
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="bg-warning text-bold">Gestión Externa</td>
                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantHoyGestionExterna > 0 && abrirModal('warning', t.TipoMovimiento, 'hoy')"
                        >
                            {{ t.CantHoyGestionExterna }}
                        </td>

                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantVencidosGestionExterna > 0 && abrirModal('warning', t.TipoMovimiento, 'vencidos')"
                        >
                            {{ t.CantVencidosGestionExterna }}
                        </td>

                        <td
                            align="center"
                            class="cursor-pointer"
                            @click="t.CantFuturosGestionExterna > 0 && abrirModal('warning', t.TipoMovimiento, 'futuros')"
                        >
                            {{ t.CantFuturosGestionExterna}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <q-dialog v-model="modalMovs">
            <q-card style="max-width: unset;">
                <q-card-section class="q-pt-none">
                    <div v-if="loadingMovs">
                        <Loading />
                    </div>

                    <q-infinite-scroll
                        :disable="noHayMasMovimientos || loadingMovs"
                        @load="onLoad"
                        style="width: 100%; padding: 0px; margin: 0px"
                        class="movimientos__container"
                    >
                        <TarjetaMovimiento
                            v-for="(m, i) in movimientos"
                            :key="m.IdMovimientoCaso"
                            :movimiento="m"
                            class="tarjeta-mov"
                        />

                        <template v-slot:loading>
                            <div class="row justify-center q-my-md">
                                <q-spinner-dots color="primary" size="100px" style="position: fixed; bottom: 10px; left: 50%"/>
                            </div>
                        </template>
                    </q-infinite-scroll>
                </q-card-section>
            </q-card>
        </q-dialog>
    </q-page>
</template>

<script>
import auth from '../auth'
import request from '../request'
import Loading from '../components/Loading.vue'
import TarjetaMovimiento from '../components/TarjetaMovimiento.vue'

export default {
    name: 'TablerosMovimientos',
    components: {
        Loading,
        TarjetaMovimiento
    },
    data () {
        return {
            loading: true,
            loadingMovs: false,
            tableros: [],
            movimientos: [],
            modalMovs: false,
            noHayMasMovimientos: false,
            objetivos: {},
            color: [],
            tipo: [],
            fecha: null,
        }
    },
    created () {
        request.Get('/estudios/tableros', {}, r => {
            if (r.Error) {
                this.$q.notify(r.Error)
            } else {
                this.loading = false
                this.tableros = r
            }
        })
    },
    methods: {
        abrirModal (color, tipo, fecha) {
            this.loadingMovs = true
            this.movimientos = []
            this.modalMovs = true
            this.noHayMasMovimientos = false
            this.color = [color]
            this.tipo = [tipo]
            this.fecha = fecha

            this.onLoad(0, () => {})
        },
        onLoad (index, done) {
            if (this.noHayMasMovimientos) return

            request.Get(`/casos/0/movimientos?Offset=${this.movimientos.length}&Color=${JSON.stringify(this.color)}&Tipos=${JSON.stringify(this.tipo)}&Fecha=${this.fecha}`, {}, t => {
                if (t.Error) {
                    this.$q.notify(t.Error)
                } else {
                    this.loadingMovs = false

                    if (t.length === 0) {
                        this.noHayMasMovimientos = true
                        return
                    }
                    let idcasos = []
                    t.forEach(m => {
                        m.Acciones = JSON.parse(m.Acciones).filter(a => a.IdMovimientoAccion)

                        //if (this.movimientos.find(mov => mov.IdMovimientoCaso === m.IdMovimientoCaso)) console.log(this.movimientos.find(mov => mov.IdMovimientoCaso === m.IdMovimientoCaso), m)

                        this.movimientos.push(m)
                        if (idcasos.indexOf(m.IdCaso) === -1 && !this.objetivos[m.IdCaso]) {
                            idcasos.push(m.IdCaso)
                        } else if (this.objetivos[m.IdCaso]) {
                            m.ObjetivosCaso = this.objetivos[m.IdCaso]
                        }
                    })

                    if (!idcasos.length) {
                        done()
                        return
                    }

                    request.Get(`/objetivos?IdsCaso=[${idcasos}]`, {}, r => {
                        if (!r.Error) {
                            this.movimientos.forEach(c => {
                                c.ObjetivosCaso = r[c.IdCaso]

                                if (!this.objetivos[c.IdCaso]) {
                                    this.objetivos[c.IdCaso] = []
                                }

                                this.objetivos[c.IdCaso] = this.objetivos[c.IdCaso].concat(r[c.IdCaso])
                            })
                            done()
                        }
                    })
                }
            })
        }
    }
}
</script>

<style scoped>
th, td {
    padding: 5px;
}
</style>
