<template>
<div style="width: 90%; max-width: 90vw;">
  <div>
      <!-- Actores -->
      <!--
      <div
        v-if="actores.length !== 0"
        style="padding: 15px; margin-bottom: 15px; min-width: 15vw; max-width: 90vw;"
      >
        <div class="docdoc-title">Actores</div>
        <q-list separator>
          <div v-for="actor in actores" :key="actor.Documento">
            <q-expansion-item
              icon="perm_identity"
              :label="actor.EsJuridica === 'N' ? actor.Nombres + ' ' + (actor.Apellidos || '') : actor.RazonSocial"
            >
            <div v-if="actor.EsJuridica === 'N'">
              <div>
                <span class="propiedad_nombre">Apellido:</span>
                <span class="propiedad_valor">{{ actor.Apellidos || '-' }}</span>
              </div>
              <div>
                <span class="propiedad_nombre">Nombre:</span>
                <span class="propiedad_valor">{{ actor.Nombres }}</span>
              </div>
              <div>
                <span class="propiedad_nombre">DNI:</span>
                <span class="propiedad_valor">{{ actor.Documento || '-' }}</span>
              </div>
            </div>
            <div v-if="actor.EsJuridica === 'S'">
              <div>
                <span class="propiedad_nombre">Razon Social:</span>
                <span class="propiedad_valor">{{ actor.RazonSocial }}</span>
              </div>
              <div>
                <span class="propiedad_nombre">CUIT:</span>
                <span class="propiedad_valor">{{ actor.CUIT || '-' }}</span>
              </div>
            </div>
              <div>
                <span class="propiedad_nombre">Teléfono:</span>
                <span class="propiedad_valor">{{ actor.Telefono || '-' }}</span>
              </div>
              <div>
                <span class="propiedad_nombre">Domicilio:</span>
                <span class="propiedad_valor">{{ actor.Domicilio || '-' }}</span>
              </div>
            </q-expansion-item>
          </div>
        </q-list>
      </div>
      -->

      <!-- Demandados -->
      <!--
      <div
        v-if="demandados.length !== 0"
        style="padding: 15px;"
      >
        <div class="docdoc-title">Demandados</div>
        <q-list separator>
          <div v-for="demandado in demandados" :key="demandado.Documento">
            <q-expansion-item
              icon="perm_identity"
              :label="demandado.EsJuridica === 'N' ? demandado.Nombres + ' ' + demandado.Apellidos : demandado.RazonSocial"
            >
            <div v-if="demandado.EsJuridica === 'N'">
              <div>
                <span class="propiedad_nombre">Apellido:</span>
                <span class="propiedad_valor">{{ demandado.Apellidos }}</span>
              </div>
              <div>
                <span class="propiedad_nombre">Nombre:</span>
                <span class="propiedad_valor">{{ demandado.Nombres }}</span>
              </div>
              <div>
                <span class="propiedad_nombre">DNI:</span>
                <span class="propiedad_valor">{{ demandado.Documento || '-' }}</span>
              </div>
            </div>
            <div v-if="demandado.EsJuridica === 'S'">
              <div>
                <span class="propiedad_nombre">Razon Social:</span>
                <span class="propiedad_valor">{{ demandado.RazonSocial }}</span>
              </div>
              <div>
                <span class="propiedad_nombre">CUIT:</span>
                <span class="propiedad_valor">{{ demandado.CUIT || '-' }}</span>
              </div>
            </div>
              <div>
                <span class="propiedad_nombre">Teléfono:</span>
                <span class="propiedad_valor">{{ demandado.Telefono || '-' }}</span>
              </div>
              <div>
                <span class="propiedad_nombre">Domicilio:</span>
                <span class="propiedad_valor">{{ demandado.Domicilio || '-' }}</span>
              </div>
              <q-btn
                style="margin-top: 2vw; margin-bottom:2vw"
                color="primary"
                rounded
                size="sm"
                @click="editarPersona(demandado)"
                icon="edit"
                label="Editar"
              />
            </q-expansion-item>
          </div>
        </q-list>
      </div>
    -->
    </div>
    <div style="padding: 15px;">
      <div class="docdoc-title">
        Relato de lo ocurrido
      </div>
      <q-editor
        @input="onChange"
        v-model="relato"
        toolbar-text-color="white"
        toolbar-toggle-color="accent"
        toolbar-flat
        toolbar-bg="primary"
        :toolbar="[
          ['bold', 'italic', 'strike', 'underline'],
          ['token', 'hr', 'link', 'custom_btn'],
          ['print', 'fullscreen'],
          [
            {
              label: 'Formato',
              icon: $q.icon.editor.formatting,
              list: 'no-icons',
              options: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']
            },
            {
              label: 'Tamaño',
              icon: $q.icon.editor.fontSize,
              fixedLabel: true,
              fixedIcon: true,
              list: 'no-icons',
              options: ['size-1', 'size-2', 'size-3', 'size-4', 'size-5', 'size-6', 'size-7']
            },
            'removeFormat'
          ],
          ['quote', 'unordered', 'ordered'],
          [
            {
              label: 'Alinear texto',
              icon: $q.icon.editor.align,
              fixedLabel: true,
              list: 'only-icons',
              options: ['left', 'center', 'right', 'justify']
            }
          ],
          ['undo', 'redo']
        ]"
        :definitions="{
          bold: {cmd: 'bold', tip: 'Negrita'},
          italic: {cmd: 'italic', tip: 'Cursiva'},
          strike: {cmd: 'strike', tip: 'Tachado'},
          underline: {cmd: 'underline', tip: 'Subrayado'},
          hr: {cmd: 'hr', tip: 'Insertar Línea Horizontal'},
          quote: {tip: 'Cita'},
          unordered: {tip: 'Lista sin orden'},
          ordered: {tip: 'Lista ordenada'},
          undo: {tip: 'Deshacer'},
          redo: {tip: 'Rehacer'},
          fullscreen: {tip: 'Pantalla completa'},
          left: {tip: 'Izquierda'},
          center: {tip: 'Centro'},
          right: {tip: 'Derecha'},
          justify: {tip: 'Justificar'},
          removeFormat: {tip: 'Remover formato'}
        }"
        :fonts="{
          arial: 'Arial',
          arial_black: 'Arial Black',
          comic_sans: 'Comic Sans MS',
          courier_new: 'Courier New',
          impact: 'Impact',
          lucida_grande: 'Lucida Grande',
          times_new_roman: 'Times New Roman',
          verdana: 'Verdana'
        }"
      />
    </div>
</div>
</template>

<script>

import sanitizeHtml from 'sanitize-html'

export default {
  props: ['actores', 'demandados'],
  name: 'ResumenCaso',
  data () {
    return {
      // Lo que se guarda es HTML
      // Sanitizar el HTML del relato
      relato: null
    }
  },
  methods: {
    onChange () {
      this.relato = sanitizeHtml(this.relato)
    }
  }
}
</script>

<style>
.propiedad_nombre {
  margin-right: 15px;
  font-weight: bold;
  color: #303F9F;
}

.propiedad_valor {
  font-style: italic;
}

.docdoc-title {
  color: #303F9F;
  font-size: 20px;
  margin-bottom: 15px;
}
</style>
