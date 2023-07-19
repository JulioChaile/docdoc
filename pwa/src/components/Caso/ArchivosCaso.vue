<template>
  <q-page style="position:relative">

    <!-- Modal: Eliminar archivos -->
    <q-dialog
      v-model="modalEliminar"
      prevent-close
    >
      <q-card style="padding:1rem;">
        <div v-if="!eliminando">
          <span class="text-h6">Eliminar Archivos</span>
          <span>
            <p>
              Al eliminar archivos, estos no podrán ser recuperados en el futuro.
              <br>
              ¿Está seguro que desea eliminar estos archivos?
            </p>
          </span>
          <div style="float:right;">
            <q-btn color="negative" label="Eliminar" @click="eliminarArchivos()" />
            <q-btn flat label="Cancelar" @click="modalEliminar = false" />
          </div>
        </div>
        <div v-else style="display: flex; justify-content: center; text-align: center; margin-left: 15px; margin-right: 15px; padding: 35px;">
          Espere unos instantes, se estan eliminando los archivos.
          <br>
          Esto puede tardar algunos segundos.
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal: Nueva / Editar Carpeta -->
    <q-dialog
      v-model="modalCarpeta"
    >
      <q-card style="padding:1rem;">
        <div>
          <span class="text-h6">{{ editandoCarpeta ? 'Editar' : 'Nueva' }} Carpeta</span>
          <span>
            <p>
              Escriba que nombre tendra su carpeta.
            </p>
          </span>
          <q-input
            class="q-my-sm"
            type="text"
            label="Nombre"
            dense
            v-model="NuevaCarpeta"
          />
          <div class="flex justify-center">
            <q-btn
              color="teal"
              :label="editandoCarpeta ? 'Editar' : 'Crear'"
              @click="editandoCarpeta ? editarCarpeta() : crearCarpeta()"
            />
          </div>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal: Borrar Carpeta -->
    <q-dialog
      v-model="modalBorrarCarpeta"
    >
      <q-card style="padding:1rem;">
        <div>
          <span class="text-h6">Borrar Carpeta</span>
          <span class="text-center">
            <p>
              ¿Esta seguro que desea eliminar esta carpeta?
            </p>
            <p>
              Esta accion es permanente.
            </p>
            <p>
              Los archivos no seran eliminados, en su lugar volveran a su carpeta de origen (CASO, MOVIMIENTOS, CHAT / CLIENTE).
            </p>
          </span>
          <div class="flex justify-center">
            <q-btn
              class="q-mr-sm"
              color="teal"
              label="Confirmar"
              @click="eliminarCarpeta"
            />
            <q-btn
              color="negative"
              label="Cancelar"
              @click="modalBorrarCarpeta = false"
            />
          </div>
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal: Enviar Mail -->
    <q-dialog v-model="modalMail">
      <EnviarMail
        :Multimedia="MultimediaSeleccionado"
        :ContenidoPDF="ContenidoPDF"
        :asunto="datosCaso.Caratula"
        @mailEnviado="mailEnviado"
        @cerrar="modalMail = false"
      />
    </q-dialog>

    <!-- Modal: Generar PDF -->
    <q-dialog v-model="modalPDF">
      <GenerarPDF
        :Multimedia="MultimediaSeleccionado"
        @enviarMail="enviarPDF"
        @cerrar="modalPDF = false"
      />
    </q-dialog>

    <q-splitter
      v-model="splitterModel"
      style="height: 100%"
      :limits="[0, 100]"
      before-class="width-documentacion relative-position"
      after-class="full-width"
    >
      <template v-slot:before>
        <div class="botones_container_ column" v-if="MultimediaSeleccionado.length > 0">
          <q-btn
            label="Eliminar"
            color="negative"
            @click="modalEliminar = true"
            push
          />
          <q-btn
            label="Enviar por Mail"
            color="teal"
            style="margin-top: 10px"
            @click="modalMail = true"
            push
          />
          <q-btn
            v-if="MultimediaSeleccionado.filter(m => m.Tipo !== 'I').length === 0"
            label="Generar PDF"
            color="teal"
            style="margin-top: 10px"
            @click="modalPDF = true"
            push
          />
          <q-btn
            label="Mover a..."
            color="teal"
            style="margin-top: 10px"
            push
          >
            <q-menu style="min-width: 100px" anchor="top right" self="top left">
              <q-list style="min-width: 100px">
                <q-item
                  v-for="c in CarpetasCaso.filter(c => c.Nombre !== tab)"
                  :key="c.IdCarpetaCaso"
                  clickable
                  v-close-popup
                  @click="moverArchivo(MultimediaSeleccionado.map(m => m.IdMultimedia), c.IdCarpetaCaso)"
                >
                  <q-item-section>{{ c.Nombre }}</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>

        <q-splitter
          v-model="splitterArchivos"
          style="height: 100%"
        >
          <template v-slot:before>
            <q-item
              class="text-teal justify-star items-center q-px-sm"
              clickable
              @click="habilitarNuevaCarpeta"
            >
              <div class="q-pr-sm">
                <q-icon
                  name="create_new_folder"
                  color="teal"
                  size="sm"
                />
              </div>
              <span class="q-tab__label">
                Nueva Carpeta
              </span>
            </q-item>

            <q-separator/>

            <q-tabs
              v-model="tab"
              vertical
              inline-label
              align="left"
              class="text-teal"
              @input="changeTab"
            >
              <q-tab name="caso" :icon="folderIcon('caso')" label="Caso" style="justify-content:initial" />
              <q-tab name="documentos" :icon="folderIcon('documentos')" label="Documentos" style="justify-content:initial" />
              <q-tab name="cliente" :icon="folderIcon('cliente')" label="Chat / Cliente" style="justify-content:initial" />

              <q-separator/>

              <q-tab
                v-for="c in CarpetasCaso"
                :key="c.IdCarpetaCaso"
                :name="c.Nombre"
                :icon="folderIcon(c.Nombre)"
                :label="c.Nombre"
                content-class="carpeta"
              >
                <div class="acciones_carpeta">
                  <q-icon
                    class="q-px-xs"
                    name="more_vert"
                    color="grey"
                    size="sm"
                    @click="event => event.stopPropagation()"
                  >
                    <q-menu style="min-width: 100px" anchor="top right" self="top left">
                      <q-list style="min-width: 100px">
                        <q-item
                          clickable
                          v-close-popup
                          @click="habilitarEditarCarpeta(c)"
                        >
                          <q-item-section>Editar nombre</q-item-section>
                        </q-item>
                        <q-item
                          clickable
                          v-close-popup
                          @click="habilitarEliminarCarpeta(c.IdCarpetaCaso)"
                        >
                          <q-item-section>Eliminar carpeta</q-item-section>
                        </q-item>
                      </q-list>
                    </q-menu>
                  </q-icon>
                </div>
              </q-tab>

              <q-separator v-if="CarpetaCaso.length" />
            </q-tabs>

            <q-uploader
              ref="uploader"
              label="Multimedia"
              :factory="factoryFn"
              @added="addedFile"
              @uploaded="uploadedFile"
              @finish="finishUpload"
              @failed="verErrorUpload"
              @factory-failed="verErrorUpload"
              style="width: 97%; margin-top: 10px"
            />
            * Los archivos que se intenten subir en la carpeta "Movimientos" se subiran en la carpeta "Caso".

            <q-separator
              v-if="$route.path !== '/ArchivosCaso'"
            />

            <q-btn
              v-if="$route.path !== '/ArchivosCaso'"
              label="Ver en otra pestaña"
              color="teal"
              style="margin-top: 10px; margin-bottom: 10px"
              @click="abrirNuevaPestaña()"
              push
            />

            <q-btn
              v-if="actores.length"
              label="Documentación Solicitada"
              color="teal"
              style="margin-top: 10px; margin-bottom: 10px"
              @click="splitterModel === 100 ? splitterModel = 80 : splitterModel = 100"
              push
            />
          </template>

          <template v-slot:separator>
            <q-avatar color="teal" text-color="white" size="35px" icon="drag_indicator" style="top: 400px" />
          </template>

          <template v-slot:after>
            <q-tabs
              v-model="tabCarpeta"
              v-if="tab !== 'documentos'"
              indicator-color="transparent"
              active-color="white"
              align="justify"
              class="text-grey-5 bg-teal"
              @input="changeTabCarpeta"
            >
              <q-tab name="todo" label="Todo" />
              <q-tab name="imagenes" label="Imagenes" />
              <q-tab name="videos" label="Videos" />
              <q-tab name="otros" label="Otros Archivos" />
            </q-tabs>

            <q-tab-panels
              v-model="tab"
              class="bg-transparent"
              animated
              vertical
              transition-prev="jump-up"
              transition-next="jump-up"
            >
              <q-tab-panel
                v-for="t in tabs"
                :key="t"
                :name="t"
                class="row"
              >
                <div v-if="loading" class="col-12">
                  <Loading />
                </div>
                <div v-else-if="filtroCarpeta.length === 0">
                  No hay archivos añadidos en esta carpeta.
                </div>
                <div
                  v-else
                  v-for="m in filtroCarpeta"
                  :key="m.URL"
                  class="col-grow-3 container_multimedia items-end"
                  :style="m.check ? 'border-color: red' : ''"
                >
                  <q-checkbox
                    v-model="m.check"
                    class="check_multimedia"
                    @input="selectArchive(m)"
                    color="red"
                  />
                  <q-item class="column">
                    <div v-if="m.Tipo === 'A'">
                      <q-avatar
                        size="200px"
                        color="black"
                        text-color="yellow"
                        icon="graphic_eq"
                      />
                      <audio controls>
                        <source :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`">
                      </audio>
                    </div>
                    <video v-else-if="m.Tipo === 'V'" class="img--multimedia" :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`" controls></video>
                    <img  v-else-if="m.Tipo === 'I'" class="img--multimedia" :src="`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`">
                    <q-avatar
                      v-else
                      square
                      rounded
                      size="200px"
                      color="white"
                      text-color="black"
                    >
                      {{ format(m.URL) }}
                    </q-avatar>
                    <q-item
                      class="nombre_multimedia"
                      clickable
                    >
                      {{ m.Nombre }}
                      <q-menu v-model="m.showing">
                        <q-list style="min-width: 100px">
                          <q-item clickable v-close-popup @click="descargarArchivo(m.URL, m.Nombre)">
                            <q-item-section>Descargar</q-item-section>
                          </q-item>
                          <q-item clickable v-close-popup @click="abrirVisor(m)">
                            <q-item-section>Ver Archivo</q-item-section>
                          </q-item>
                          <q-item clickable v-if="m.Tipo === 'I'" v-close-popup @click="habilitarModalPerfil(m.URL)">
                            <q-item-section>Usar para Perfil</q-item-section>
                          </q-item>
                          <q-item clickable v-if="m.Tipo === 'I'" v-close-popup @click="habilitarModalEdicion(m.URL, m.IdMultimedia)">
                            <q-item-section>Editar Imagen</q-item-section>
                          </q-item>
                          <q-separator />
                          <q-item clickable v-close-popup @click="editarNombre(false, m.Nombre, m.IdMultimedia)">
                            <q-item-section>Editar Nombre</q-item-section>
                          </q-item>
                          <q-item  v-if="CarpetasCaso.length" clickable>
                            <q-item-section>Mover a la carpeta...</q-item-section>
                            <q-item-section side>
                              <q-icon name="keyboard_arrow_right" />
                            </q-item-section>
                            <q-menu style="min-width: 100px" anchor="top right" self="top left">
                              <q-list style="min-width: 100px">
                                <!--q-item v-if="tab !== 'caso'" clickable v-close-popup @click="descargarArchivo(m.URL, m.Nombre)">
                                  <q-item-section>CASO</q-item-section>
                                </q-item>
                                <q-separator v-if="tab !== 'caso'" /-->
                                <q-item
                                  v-for="c in CarpetasCaso.filter(c => c.Nombre !== tab)"
                                  :key="c.IdCarpetaCaso"
                                  clickable
                                  v-close-popup
                                  @click="moverArchivo([m.IdMultimedia], c.IdCarpetaCaso)"
                                >
                                  <q-item-section>{{ c.Nombre }}</q-item-section>
                                </q-item>
                              </q-list>
                            </q-menu>
                          </q-item>
                        </q-list>
                      </q-menu>
                    </q-item>
                  </q-item>
                </div>
              </q-tab-panel>
            </q-tab-panels>
          </template>
        </q-splitter>
      </template>

      <template v-slot:after>
        <div class="full-width bg-teal flex items-center text-center text-white q-tab__label justify-center" style="height: 48px">
          DOCUMENTACIÓN
        </div>

        <div
          v-if="primeraVez"
          class="column items-start q-pa-md full-width"
        >
          <div
            v-for="(p, i) in actores"
            :key="p.IdPersona"
            class="column q-mb-lg full-width"
          >
            <div class="text-weight-bold">
              {{ p.Persona }}
            </div>

            <div class="row items-center full-width q-mb-sm">
              <q-select
                dense
                v-model="p.modelDoc"
                use-input
                fill-input
                hide-selected
                emit-value
                input-debounce="0"
                :options="p.opciones"
                @filter="(v, u, a) => filterFn(v, u, a, i)"
                hint="Agregar documentación requerida"
                @input-value="(val) => {p.modelDoc = val}"
              />
              <q-icon
                name="add_circle"
                color="teal"
                size="md"
                class="q-ml-sm cursor-pointer"
                @click="agregarDoc(p.modelDoc, i)"
              />
            </div>

            <div
              class="row items-center full-width q-mb-sm q-mt-sm"
            >
              <q-select
                label="Combos"
                v-model="ComboSeleccionado"
                :options="Combos.map(c => c.Combo)"
                style="width:47%;"
              />
              <q-icon
                name="add_circle"
                color="teal"
                size="md"
                class="q-ml-sm cursor-pointer"
                @click="agregarCombo(i)"
              />
            </div>

            <div
              v-for="(d, j) in p.DocumentacionSolicitada"
              :key="d.Doc"
              class="row"
            >
              <div class="text-weight-medium col-11">
                - {{ d.Doc }}
              </div>

              <q-icon
                name="delete_outline"
                color="negative"
                size="xs"
                class="cursor-pointer"
                @click="quitarDoc(i, j)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Quitar</q-tooltip>
              </q-icon>
            </div>

            <div
              v-if="!p.DocumentacionSolicitada.length"
              class="text-caption text-left text-grey full-width text-center q-mt-md"
            >
              No se agregó documentacion que se requiera de esta persona.
            </div>

            <q-separator style="width: 50%; margin-left:auto; margin-right:auto" />
          </div>

          <q-btn
            v-if="!subiendo"
            label="solicitar"
            color="teal"
            class="self-center"
            @click="solicitarDoc(actores)"
            push
          />
          <q-spinner
            v-else
            color="teal"
            class="self-center"
            size="2em"
          />
        </div>

        <div
          v-else
          class="column items-start q-pa-md full-width"
        >
          <div
            v-for="(p, i) in actores"
            :key="p.IdPersona"
            class="column q-mb-lg full-width relative-position"
          >
            <q-icon
              v-if="!p.editar"
              name="edit"
              color="primary"
              size="sm"
              class="cursor-pointer absolute-top-right"
              @click="p.editar = true"
            />
            <q-icon
              v-else
              name="done"
              color="positive"
              size="sm"
              class="cursor-pointer absolute-top-right"
              @click="p.editar = false; solicitarDoc([p])"
            />

            <div class="text-weight-bold q-mb-sm column">
              {{ p.Persona }}
              <div
                v-if="p.DocumentacionSolicitada.length"
                class="text-caption"
              >
                {{ progresoDoc(p.DocumentacionSolicitada) }}
              </div>
            </div>

            <div
              v-if="p.editar"
              class="row items-center full-width q-mb-sm"
            >
              <q-select
                dense
                v-model="p.modelDoc"
                use-input
                fill-input
                hide-selected
                emit-value
                input-debounce="0"
                :options="p.opciones"
                @filter="(v, u, a) => filterFn(v, u, a, i)"
                hint="Agregar documentación requerida"
                @input-value="(val) => {p.modelDoc = val}"
              />
              <q-icon
                name="add_circle"
                color="teal"
                size="md"
                class="q-ml-sm cursor-pointer"
                @click="agregarDoc(p.modelDoc, i)"
              />
            </div>

            <div
              v-if="p.editar"
              class="row items-center full-width q-mb-sm q-mt-sm"
            >
              <q-select
                label="Combos"
                v-model="ComboSeleccionado"
                :options="Combos.map(c => c.Combo)"
                style="width:47%;"
              />
              <q-icon
                name="add_circle"
                color="teal"
                size="md"
                class="q-ml-sm cursor-pointer"
                @click="agregarCombo(i)"
              />
            </div>

            <div
              v-for="(d, j) in p.DocumentacionSolicitada"
              :key="d.Doc"
              class="row items-center"
            >
              <q-icon
                v-if="!p.editar"
                :name="d.Estado ? 'done' : 'close'"
                :color="d.Estado ? 'positive' : 'negative'"
                size="xs"
                class="col-1"
              />
              <q-checkbox
                v-else
                v-model="d.Estado"
                size="xs"
                color="teal"
                class="q-pa-none q-ma-none col-1"
              />

              <div class="text-weight-medium col-10">
                {{ d.Doc }}
              </div>

              <q-icon
                v-if="p.editar"
                name="delete_outline"
                color="negative"
                size="xs"
                class="cursor-pointer"
                @click="quitarDoc(i, j)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Quitar</q-tooltip>
              </q-icon>
            </div>

            <div
              v-if="!p.DocumentacionSolicitada.length"
              class="text-caption text-left text-grey full-width text-center q-mt-md"
            >
              No se agregó documentacion que se requiera de esta persona.
            </div>

            <q-separator class="q-mt-sm" style="width: 50%; margin-left:auto; margin-right:auto" />
          </div>

          <span class="text-weight-bold text-h5">
            Total
          </span>
          {{ progresoDocTotal() }}
        </div>

        <q-separator class="q-my-lg" style="width: 50%; margin-left:auto; margin-right:auto" />

        <div v-if="!altaRec" class="q-px-lg">
          <div class="text-bold">
            Recordatorio
          </div>

          <div class="text-caption text-italic">
            Fecha Limite: {{ Recordatorio.FechaLimite ? formatFechaRec() : '---' }} <br>
            Frecuencia: {{ Recordatorio.Frecuencia ? 'cada ' + Recordatorio.Frecuencia + ' días' : '---' }} <br>
            Activo: {{ Recordatorio.Activa === 'S' ? 'Si' : 'No' }}
          </div>

          <q-btn
            style="justify-self: center; margin-top: 10px"
            color="teal"
            dense
            label="Modificar Recordatorio"
            @click="habilitarModificarRec"
          />

          <q-btn
            style="justify-self: center; margin-top: 10px"
            color="negative"
            dense
            label="Eliminar Recordatorio"
            @click="eliminarRec"
          />
        </div>

        <div v-else class="q-px-sm">
          <q-input ref="inputFechaEsperada" label="Fecha Limite" v-model="FechaRec" mask="##-##-####" :rules="[v => /^-?[0-3]\d-[0-1]\d-[\d]+$/.test(v) || 'Fecha invalida']" style="width:90%;">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                  <q-date mask="DD-MM-YYYY" v-model="FechaRec" @input="() => $refs.qDateProxy1.hide()" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>

          <q-input
            v-model="FrecuenciaRec"
            label="Frecuencia de días"
            type="number"
            filled
          />

          <div class="row justify-center">
            <q-btn
              style="justify-self: center; margin-top: 10px"
              class="q-mx-sm"
              color="teal"
              dense
              label="Guardar"
              @click="guardarRecordatorio"
            />

            <q-btn
              style="justify-self: center; margin-top: 10px"
              color="negative"
              class="q-mx-sm"
              dense
              label="Cancelar"
              @click="altaRec = false"
            />
          </div>
        </div>

        <q-separator class="q-my-lg" style="width: 50%; margin-left:auto; margin-right:auto" />

        <div class="full-width row justify-center">
          <q-btn
            style="justify-self: center; margin-top: 10px; margin-left: auto; margin-right: auto"
            color="warning"
            dense
            label="Combos"
            @click="modalCombos = true"
          />
        </div>
      </template>
    </q-splitter>

    <!-- Modal Visor -->
    <q-dialog v-model="modalVisor">
      <VisorArchivo
        :archivo="archivoVer"
        @cerrar="modalVisor = false"
      />
    </q-dialog>

    <!-- Modal Editar Nombre -->
    <q-dialog v-model="modalNombre">
      <q-card style="padding: 1em; width: 70%; display: grid">
        <q-input
          class="input"
          type="text"
          v-model="nombreEditar"
        />
        <q-btn
          style="justify-self: center; margin-top: 10px"
          color="teal"
          label="Aceptar"
          @click="editarNombre(true)"
        />
      </q-card>
    </q-dialog>

    <!-- Modal Combos -->
    <q-dialog v-model="modalCombos">
      <q-card style="padding: 1em; width: 70%; display: grid">
        <div class="full-width row justify-start relative-position">
          <q-icon
            v-if="!editarCombo"
            name="edit"
            color="primary"
            size="sm"
            class="cursor-pointer absolute-top-right"
            @click="editarCombo = true"
          />
          <q-icon
            v-else
            name="done"
            color="positive"
            size="sm"
            class="cursor-pointer absolute-top-right"
            @click="editarCombo = false; guardarCombo()"
          />

          <div class="col-3">
            <div
              :class="'text-bold q-pa-sm q-my-xs cursor-pointer ' + (IdCombo === c.IdCombo && 'bg-grey')"
              v-for="c in Combos"
              :key="c.IdCombo"
              @click="IdCombo = c.IdCombo"
            >
              {{ c.Combo }}
            </div>
          </div>

          <q-separator vertical />

          <div class="col-8">
            <div
              v-if="editarCombo"
              class="row items-center full-width q-mb-sm q-pa-sm"
            >
              <q-select
                dense
                v-model="newItem"
                use-input
                fill-input
                hide-selected
                emit-value
                input-debounce="0"
                :options="opcionesCombo"
                @filter="(v, u, a) => filterFnCombo(v, u, a, i)"
                hint="Agregar documentación requerida"
                @input-value="(val) => {newItem = val}"
              />
              <q-icon
                name="add_circle"
                color="teal"
                size="md"
                class="q-ml-sm cursor-pointer"
                @click="agregarDocCombo(newItem)"
              />
            </div>
            <div
              class="q-pa-sm q-my-xs row"
              v-for="(item, i) in Combos.filter(c => c.IdCombo === IdCombo)[0].Items"
              :key="item"
            >
              <div class="text-weight-medium col-11">
                - {{ item }}
              </div>

              <q-icon
                v-if="editarCombo"
                name="delete_outline"
                color="negative"
                size="xs"
                class="cursor-pointer"
                @click="quitarDocCombo(i)"
              >
                <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Quitar</q-tooltip>
              </q-icon>
            </div>
          </div>
        </div>

        <q-separator />

        <div class="full-width row justify-center q-px-lg q-my-lg">
          <q-input
            dense
            type="text"
            v-model="newCombo"
            class="q-mr-lg"
          />
          <q-btn
            color="warning"
            dense
            label="Nuevo Combo"
            @click="altaCombo"
          />
        </div>
      </q-card>
    </q-dialog>

    <!-- Modal Foto Perfil -->
    <q-dialog v-model="modalPerfil">
      <q-card class="row flex-column" style="padding: 1em; width: 70%; display: grid">
        <vue-cropper
          ref="cropper"
          :aspect-ratio="1"
          :src="'https://io.docdoc.com.ar/api/multimedia?file=' + URLPerfil"
          preview=".preview"
        />
        <q-btn
          style="justify-self: center; margin-top: 20px"
          color="teal"
          label="Guardar"
          @click="cropImage"
        />
      </q-card>
    </q-dialog>

    <!-- Modal Edicion Foto -->
    <q-dialog v-model="modalEditarFoto">
      <q-card class="row flex-column" style="padding: 1em; width: 70%; display: grid">
        <vue-cropper
          ref="cropper"
          :aspect-ratio="1"
          :src="'https://io.docdoc.com.ar/api/multimedia?file=' + URLPerfil"
          preview=".preview"
        />
        <q-btn
          style="justify-self: center; margin-top: 20px"
          color="negative"
          label="Rotar"
          @click="rotate(90)"
        />
        <q-btn
          style="justify-self: center; margin-top: 20px"
          color="teal"
          label="Guardar"
          @click="cropImageEditar"
        />
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import moment from 'moment'
import VueCropper from 'vue-cropperjs'
import 'cropperjs/dist/cropper.css'
import auth from '../../auth'
import request from '../../request'
import Loading from '../../components/Loading'
import VisorArchivo from '../../components/Caso/VisorArchivo'
import EnviarMail from '../../components/Compartidos/EnviarMail'
import GenerarPDF from '../../components/Archivos/GenerarPDF'
import { QTabPanels, QTabPanel, QTab, QTabs, QSplitter, QSpinner, Notify } from 'quasar'
// import JSZip from 'jszip'
export default {
  name: 'ArchivosCaso',
  components: {
    QTabPanels,
    QTabPanel,
    QTab,
    QTabs,
    QSplitter,
    QSpinner,
    Loading,
    VisorArchivo,
    EnviarMail,
    GenerarPDF,
    VueCropper
  },
  data () {
    return {
      url: '',
      file: '',
      IdMultimediaEdicion: 0,
      modalEditarFoto: false,
      id: 0,
      check: false,
      enviando: false,
      loading: true,
      altaRec: false,
      tab: 'caso',
      tabCarpeta: 'todo',
      tabs: ['caso', 'documentos', 'cliente'],
      tabsPorDefecto: ['caso', 'documentos', 'cliente'],
      splitterModel: 100,
      splitterArchivos: 15,
      CarpetasCaso: [],
      CarpetaCaso: [],
      CarpetaDocumentos: [],
      CarpetaCliente: [],
      Multimedia: [],
      MultimediaSeleccionado: [],
      MultimediaCarpetas: [],
      datosCaso: {},
      modalEliminar: false,
      eliminando: false,
      modalVisor: false,
      archivoVer: {},
      modalNombre: false,
      nombreEditar: '',
      IdMultimedia: 0,
      modalMail: false,
      modalPDF: false,
      ContenidoPDF: '',
      NuevaCarpeta: '',
      modalCarpeta: false,
      modalBorrarCarpeta: false,
      editandoCarpeta: false,
      IdCarpetaCaso: null,
      docMenu: false,
      actores: [],
      documentacionPrevia: [],
      primeraVez: true,
      subiendo: false,
      modalPerfil: false,
      URLPerfil: '',
      FechaRec: '',
      FrecuenciaRec: 2,
      Recordatorio: {
        FechaLimite: '',
        Frecuencia: '',
        Activa: 'N'
      },
      modalCombos: false,
      Combos: [],
      IdCombo: 0,
      editarCombo: false,
      newItem: '',
      opcionesCombo: [],
      newCombo: '',
      ComboSeleccionado: ''
    }
  },
  created () {
    if (!this.$route.query.id) {
      this.$router.push('GrillaCasos')
      return
    }
    this.id = this.$route.query.id

    request.Get(`/casos`, { id: this.id }, (r) => {
      if (!r.Error) {
        this.caso = r
        console.log(r)

        this.Recordatorio = {
          FechaLimite: r.RecDocFecha,
          Frecuencia: r.RecDocFrec,
          Activa: r.RecDocActiva
        }

        // Datos del caso:
        this.datosCaso = {
          Caratula: r.Caratula,
          Carpeta: r.Carpeta,
          IdCaso: r.IdCaso,
          Estado: r.Estado,
          FechaAlta: r.FechaAlta,
          FechaEstado: r.FechaEstado,
          FechaUltVisita: r.FechaUltVisita,
          NroExpediente: r.NroExpediente,
          Competencia: r.Competencia,
          IdCompetencia: r.IdCompetencia,
          TipoCaso: r.TipoCaso,
          IdTipoCaso: r.IdTipoCaso,
          Juzgado: r.Juzgado,
          IdJuzgado: r.IdJuzgado,
          Nominacion: r.Nominacion,
          IdNominacion: r.IdNominacion,
          EstadoAmbitoGestion: r.EstadoAmbitoGestion,
          IdEstadoAmbitoGestion: r.IdEstadoAmbitoGestion,
          Origen: r.Origen,
          IdOrigen: r.IdOrigen,
          IdMediacion: r.IdMediacion
        }
        /*
        // Movimientos del caso:
        this.movimientos = r.MovimientosCaso
        console.log(this.movimientos)
        if (this.Objetivos.length > 0) {
          console.log(this.Objetivos)
          this.Objetivos.forEach(e => {
            const i = this.movimientos.findIndex(f => f.IdMovimientoCaso === parseInt(e.IdMovimientoCaso))
            if (i !== -1) {
              this.movimientos[i].IdObjetivo = e.IdObjetivo
              this.movimientos[i].Objetivo = e.Objetivo
            }
          })
        }
        */

        // Personas del caso:
        this.personas = r.PersonasCaso ? r.PersonasCaso : []

        this.personas.forEach(p => {
          if (p.Observaciones === 'Actor') {
            this.actores.push({
              IdPersona: p.IdPersona,
              Persona: p.Apellidos
                ? p.Apellidos + ', ' + p.Nombres
                : p.Nombres,
              DocumentacionSolicitada: p.DocumentacionSolicitada || [],
              modelDoc: '',
              opciones: [],
              editar: false,
              TokenApp: p.TokenApp
            })

            if (p.DocumentacionSolicitada) {
              this.primeraVez = false
            }
          }
        })
      } else {
        console.log('Hubo un error al traer el caso.')
      }
    })

    /* Prueba con JSZip
    request.Get('/multimedia?file=m3BbFI5xgZSWvJ3VdvhyLVWQWH0QiHMD.png', {}, r => {
      console.log(r)
      let zip = new JSZip()
      zip.file('awer.png', r, {base64: true})
      zip.generateAsync({type: 'string'})
        .then(r => {
          location.href = 'data:application/zip;base64,' + r
        })
    })
    */

    request.Get('/estudios/listar-documentacion-solicitada', {}, r => {
      this.documentacionPrevia = r.Doc.map(d => d.Documentacion)
      this.Combos = r.Combos.map(c => {
        c.Items = JSON.parse(c.Items)
        return c
      })
      this.IdCombo = this.Combos[0].IdCombo
    })

    request.Get('/casos/listar-carpetas', {IdCaso: this.id}, r => {
      if (r.Error) {
        this.$q.notify('Ha ocurrido un error al traer las carpetas del caso')
      } else {
        r.forEach(c => {
          this.tabs.push(c.Nombre)
        })

        this.CarpetasCaso = r

        this.ordenarCarpetas()
      }
    })

    request.Get('/multimedia-caso', {IdCaso: this.id}, r => {
      if (r.Error) {
        this.$q.notify(r.Error)
      } else {
        const formatosDoc = ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'odt', 'pdf']
        r.forEach(m => {
          m.check = false
          m.showing = false

          const formato = m.URL.split('.').reverse()[0].toLowerCase()

          m.OrigenMultimedia = formatosDoc.includes(formato) ? 'D' : m.OrigenMultimedia

          if (!m.IdCarpetaCaso) {
            switch (m.OrigenMultimedia) {
              case 'D':
                this.CarpetaDocumentos.push(m)
                break

              case 'R':
                this.CarpetaCliente.push(m)
                break

              case 'C':
                this.CarpetaCaso.push(m)
                break

              default:
                break
            }
          } else {
            this.MultimediaCarpetas.push(m)
          }
        })

        this.loading = false
      }
    })
  },
  computed: {
    filtroCarpeta () {
      const tab = this.tab
      let multimedia

      if (!this.tabsPorDefecto.includes(tab)) {
        const i = this.CarpetasCaso.findIndex(c => c.Nombre === tab)
        const IdCarpeta = parseInt(this.CarpetasCaso[i].IdCarpetaCaso)
        multimedia = this.MultimediaCarpetas.filter(m => parseInt(m.IdCarpetaCaso) === IdCarpeta)
      } else {
        const Carpeta = 'Carpeta' + tab[0].toUpperCase() + tab.slice(1)
        multimedia = this[Carpeta].filter(m => !m.IdCarpetaCaso)
      }

      multimedia.reverse()

      switch (this.tabCarpeta) {
        case 'todo':
          return multimedia

        case 'imagenes':
          return multimedia.filter(m => m.Tipo === 'I')

        case 'videos':
          return multimedia.filter(m => m.Tipo === 'V')

        case 'otros':
          return multimedia.filter(m => m.Tipo === 'A' || m.Tipo === 'O' || m.Tipo === 'D')

        default:
          break
      }
    }
  },
  methods: {
    cropImage () {
      const img = this.$refs.cropper.getCroppedCanvas().toDataURL()

      request.Post('/multimedia/subir-img', { img, IdCaso: this.id }, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
          return
        }

        this.modalPerfil = false

        this.$q.notify('El recorte fue reemplazado en el caso correctamente')
      })
    },
    cropImageEditar () {
      const img = this.$refs.cropper.getCroppedCanvas().toDataURL()

      request.Post('/multimedia/reemplazar-img', { img, IdMultimedia: this.IdMultimediaEdicion }, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
          return
        }

        const URL = r.name

        this.CarpetaCaso.forEach(m => {
          if (m.IdMultimedia === this.IdMultimediaEdicion) m.URL = URL
        })
        this.CarpetaDocumentos.forEach(m => {
          if (m.IdMultimedia === this.IdMultimediaEdicion) m.URL = URL
        })
        this.CarpetaCliente.forEach(m => {
          if (m.IdMultimedia === this.IdMultimediaEdicion) m.URL = URL
        })
        this.MultimediaCarpetas.forEach(m => {
          if (m.IdMultimedia === this.IdMultimediaEdicion) m.URL = URL
        })

        this.modalEditarFoto = false

        this.$q.notify('La edicion fue realizada correctamente')
      })
    },
    rotate (deg) {
      this.$refs.cropper.rotate(deg)
    },
    filterFn (val, update, abort, i) {
      update(() => {
        const needle = val.toLowerCase()
        this.actores[i].opciones = this.documentacionPrevia.filter(v => v.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterFnCombo (val, update, abort, i) {
      update(() => {
        const needle = val.toLowerCase()
        this.opcionesCombo = this.documentacionPrevia.filter(v => v.toLowerCase().indexOf(needle) > -1)
      })
    },
    factoryFn () {
      const method = this.url ? 'PUT' : 'POST'

      const headers = this.url
        ? [{ name: 'Content-Type', value: 'application/octet-stream' }]
        : [{ name: 'Authorization', value: `Bearer ${auth.Token}` }]
      
      return {
        url: this.url || 'https://io.docdoc.com.ar/api/multimedia',
        method,
        headers
      }
    },
    uploadFile(file) {
      // Aquí puedes realizar acciones adicionales antes de subir el archivo,
      // como mostrar una barra de progreso o validar el tamaño del archivo, etc.
      
      // Ejemplo de acción adicional: Imprimir el nombre del archivo
      console.log('Archivo agregado:', file);
      
      // Ejemplo de acción adicional: Realizar la subida del archivo utilizando la URL firmada
      // Puedes utilizar aquí otra biblioteca o método específico para subir el archivo
      // al Cloud Storage utilizando la URL firmada.
    },
    uploadedFile ({ files, xhr }) {
      const data = this.url ? null : JSON.parse(xhr.response)
      for (let i = 0; i < files.length; i++) {
        const Tipo = files[i].type

        const formatosDoc = ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'odt', 'pdf']
        const formato =  data ? data.Urls[0].split('.').reverse()[0].toLowerCase() : this.file.split('.').reverse()[0].toLowerCase()

        let origen = this.tab === 'cliente' ? 'R' : 'C'

        origen = formatosDoc.includes(formato) ? 'D' : origen

        const j = this.CarpetasCaso.findIndex(c => c.Nombre === this.tab)
        const IdCarpetaCaso = j >= 0
          ? this.CarpetasCaso[j].IdCarpetaCaso
          : 0

        this.Multimedia.push({
          URL: data ? data.Urls[0] : this.file,
          Nombre: data ? data.Names[0] : files[i].name.split('.')[0],
          Tipo: Tipo.includes('application') ? 'O' : Tipo.substring(0, 1).toUpperCase(),
          OrigenMultimedia: origen,
          IdCarpetaCaso: IdCarpetaCaso
        })
      }
    },
    finishUpload () {
      request.Post('/multimedia-caso/alta', {IdCaso: this.id, Multimedia: this.Multimedia}, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          if (!this.tabsPorDefecto.includes(this.tab)) {
            this.Multimedia.forEach(m => {
              m.check = false
              this.MultimediaCarpetas.push(m)
            })
          } else if (this.tab === 'cliente') {
            this.Multimedia.forEach(m => {
              m.check = false
              m.OrigenMultimedia === 'D'
                ? this.CarpetaDocumentos.push(m)
                : this.CarpetaCliente.push(m)
            })
          } else {
            this.Multimedia.forEach(m => {
              m.check = false
              m.OrigenMultimedia === 'D'
                ? this.CarpetaDocumentos.push(m)
                : this.CarpetaCaso.push(m)
            })
          }

          this.$q.notify('Los archivos se han terminado de subir con exito.')
        }

        this.Multimedia = []
      })
    },
    formatFechaRec () {
      return moment(this.Recordatorio.FechaLimite).format('DD/MM/YYYY')
    },
    habilitarModificarRec () {
      this.altaRec = true
      this.FechaRec = moment().add(10, 'days').format('DD-MM-YYYY')
      this.FrecuenciaRec = 2
    },
    eliminarRec () {
      this.Recordatorio = {
        FechaLimite: '',
        Frecuencia: '',
        Activa: 'N'
      }

      request.Post('/casos/eliminar-recordatorio-doc', { IdCaso: this.id }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          Notify.create('Recordatorio Eliminado')
        }
      })
    },
    guardarRecordatorio () {
      this.FechaRec = this.FechaRec.split('-').reverse().join('-')
      this.Recordatorio = {
        FechaLimite: this.FechaRec,
        Frecuencia: this.FrecuenciaRec,
        Activa: 'S'
      }
      this.altaRec = false

      const Dias = -moment(moment().format('YYYY-MM-DD')).diff(moment(moment(this.FechaRec).format('YYYY-MM-DD')), 'days')

      request.Post('/casos/alta-recordatorio-doc', { IdCaso: this.id, FechaLimite: this.FechaRec, Frecuencia: this.FrecuenciaRec, Dias }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          Notify.create('Recordatorio de caso añadido')
        }
      })
    },
    agregarDoc (value, i) {
      if (!value) {
        return
      }

      if (this.actores[i].DocumentacionSolicitada.findIndex(d => d.Doc.toLowerCase() === value.toLowerCase()) > -1) {
        this.$q.notify('Este elemento ya se encuentra en la lista')
        return
      }

      this.actores[i].DocumentacionSolicitada.push({
        Doc: value,
        Estado: false
      })

      if (this.documentacionPrevia.findIndex(d => d.toLowerCase() === value.toLowerCase()) === -1) {
        this.documentacionPrevia.push(value)

        request.Post('/estudios/alta-documentacion-solicitada', { doc: value }, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          }
        })
      }

      this.actores[i].modelDoc = ''
    },
    agregarCombo (i) {
      const combo = this.Combos.filter(c => c.Combo === this.ComboSeleccionado)[0]

      combo.Items.forEach(doc => {
        if (this.actores[i].DocumentacionSolicitada.findIndex(d => d.Doc.toLowerCase() === doc.toLowerCase()) === -1) {
          this.actores[i].DocumentacionSolicitada.push({
            Doc: doc,
            Estado: false
          })
        }
      })

      this.ComboSeleccionado = ''
    },
    agregarDocCombo (value) {
      if (!value) {
        return
      }

      if (this.Combos.filter(c => c.IdCombo === this.IdCombo)[0].Items.findIndex(d => d.toLowerCase() === value.toLowerCase()) > -1) {
        this.$q.notify('Este elemento ya se encuentra en la lista')
        return
      }

      this.Combos.filter(c => c.IdCombo === this.IdCombo)[0].Items.push(value)

      if (this.documentacionPrevia.findIndex(d => d.toLowerCase() === value.toLowerCase()) === -1) {
        this.documentacionPrevia.push(value)

        request.Post('/estudios/alta-documentacion-solicitada', { doc: value }, r => {
          if (r.Error) {
            this.$q.notify(r.Error)
          }
        })
      }

      this.newItem = ''
    },
    quitarDoc (i, j) {
      this.actores[i].DocumentacionSolicitada.splice(j, 1)
    },
    quitarDocCombo (i) {
      const items = this.Combos.filter(c => c.IdCombo === this.IdCombo)[0].Items
      items.splice(i, 1)
    },
    guardarCombo () {
      const items = this.Combos.filter(c => c.IdCombo === this.IdCombo)[0].Items

      request.Post('/casos/guardar-combo', { IdCombo: this.IdCombo, items: JSON.stringify(items) }, r => { if (r.Error) Notify.create(r.Error) })
    },
    altaCombo () {
      if (this.Combos.filter(c => c.Combo.toLowerCase() === this.newCombo).length > 0) {
        Notify.create('El combo ya existe')
        return
      }

      if (!this.newCombo) {
        Notify.create('Debe escribir un nombre para el combo')
        return
      }

      request.Post('/casos/alta-combo', { combo: this.newCombo }, r => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          Notify.create('Combo añadido con exito')

          this.Combos.push({
            IdCombo: r.IdCombo,
            Combo: this.newCombo,
            Items: []
          })

          this.newCombo = ''
        }
      })
    },
    solicitarDoc (actores) {
      const personas = actores.map(a => {
        return {
          IdPersona: a.IdPersona,
          DocumentacionSolicitada: JSON.stringify(a.DocumentacionSolicitada),
          TokenApp: a.TokenApp
        }
      })

      this.subiendo = true

      request.Post(`/personas/${this.id}/editar-documentacion`, {personas: personas, primeraVez: this.primeraVez}, r => {
        console.log(r)
        this.subiendo = false

        if (r.length) {
          this.$q.notify('Hubo un error inesperado, contacte con el administrador.')
        } else {
          this.primeraVez = false
          this.$q.notify('Se guardo la documentacion requerida con exito.')
        }
      })
    },
    habilitarModalPerfil (url) {
      this.URLPerfil = url

      this.modalPerfil = true
    },
    habilitarModalEdicion (url, id) {
      this.URLPerfil = url
      this.IdMultimediaEdicion = id

      this.modalEditarFoto = true
    },
    progresoDoc (doc) {
      const total = parseInt(doc.length)
      const progreso = parseInt(doc.filter(d => d.Estado).length)
      const porcentaje = (progreso / total) * 100

      return `${progreso}/${total} - ${parseInt(porcentaje)}%`
    },
    progresoDocTotal () {
      let total = 0
      let progreso = 0

      this.actores.forEach(a => {
        total += parseInt(a.DocumentacionSolicitada.length)
        progreso += parseInt(a.DocumentacionSolicitada.filter(d => d.Estado).length)
      })

      const porcentaje = (progreso / total) * 100

      return total !== 0
        ? `${progreso}/${total} - ${parseInt(porcentaje)}%`
        : 'Sin documentación solicitada'
    },
    ordenarCarpetas () {
      this.CarpetasCaso.sort((a, b) => {
        if (a.Nombre > b.Nombre) {
          return 1
        } else if (a.Nombre < b.Nombre) {
          return -1
        } else {
          return 0
        }
      })
    },
    abrirVisor (m) {
      this.archivoVer = {
        URL: m.URL,
        Tipo: m.Tipo,
        Formato: this.format(m.URL)
      }

      this.modalVisor = true
    },
    folderIcon (nameTab) {
      return nameTab === this.tab ? 'folder_open' : 'folder'
    },
    format (name) {
      return name.split('.').reverse()[0]
    },
    addedFile (files) {
      const extension = files[0].name.split('.').reverse()[0].toLowerCase()
      
      if (['jpeg', 'jpg', 'png'].includes(extension)) {
        this.url = null
        this.file = null
        this.$refs.uploader.upload()

        return
      }

      request.Post('/multimedia/link', { extension }, r => {
        this.url = r.url
        this.file = r.file

        console.log(r)

        this.$refs.uploader.upload()
      })
    },
    selectArchive (m) {
      if (m.check) {
        this.MultimediaSeleccionado.push({
          IdMultimedia: m.IdMultimedia,
          URL: m.URL,
          OrigenMultimedia: m.OrigenMultimedia,
          Tipo: m.Tipo
        })
      } else {
        const i = this.MultimediaSeleccionado.findIndex(f => f.IdMultimedia === m.IdMultimedia)
        this.MultimediaSeleccionado.splice(i, 1)
      }
    },
    changeTab (tab) {
      const Carpeta = 'Carpeta' + tab[0].toUpperCase() + tab.slice(1)

      this.tabCarpeta = 'todo'

      this.MultimediaSeleccionado = []

      if (this[Carpeta] !== undefined) {
        this[Carpeta].forEach(m => { m.check = false })
      } else {
        this.MultimediaCarpetas.forEach(m => { m.check = false })
      }
    },
    changeTabCarpeta (tabCarpeta) {
      const Carpeta = 'Carpeta' + this.tab[0].toUpperCase() + this.tab.slice(1)

      this.MultimediaSeleccionado = []

      if (this[Carpeta] !== undefined) {
        this[Carpeta].forEach(m => { m.check = false })
      } else {
        this.MultimediaCarpetas.forEach(m => { m.check = false })
      }
    },
    eliminarArchivos () {
      /* Codigo para crear archivos zip que hay que ver en algun momento
      let zip = new JSZip()
      let c = 0

      this.MultimediaSeleccionado.forEach(m => {
        fetch(`https://io.docdoc.com.ar/api/multimedia?file=${m.URL}`)
          .then(r => {
            c++
            r.text()
              .then(r => {
                console.log(r)
                zip.file(m.URL, r, {base64: true})
                if (c === this.MultimediaSeleccionado.length) {
                  zip.generateAsync({type: 'base64'})
                    .then(r => {
                      location.href = 'data:application/zip;base64,' + r
                    })
                }
              })
          })
      })
      */
      const tab = this.tab
      const Carpeta = 'Carpeta' + tab[0].toUpperCase() + tab.slice(1)
      const Multimedia = this.MultimediaSeleccionado
      const IdCaso = this.id

      this.eliminando = true

      request.Post('/multimedia-caso/eliminar', {IdCaso: IdCaso, Multimedia: JSON.stringify(Multimedia)}, r => {
        if (!r.Error) {
          if (this.tabsPorDefecto.includes(tab)) {
            for (let i = this[Carpeta].length - 1; i >= 0; i--) {
              const IdMultimedia = this[Carpeta][i].IdMultimedia

              const index = Multimedia.findIndex(m => m.IdMultimedia === IdMultimedia)

              if (index >= 0) { this[Carpeta].splice(i, 1) }
            }
          } else {
            for (let i = this.MultimediaCarpetas.length - 1; i >= 0; i--) {
              const IdMultimedia = this.MultimediaCarpetas[i].IdMultimedia

              const index = Multimedia.findIndex(m => m.IdMultimedia === IdMultimedia)

              if (index >= 0) { this.MultimediaCarpetas.splice(i, 1) }
            }
          }

          this.$q.notify('Los archivos se han eliminado con exito.')
        } else {
          this.tabsPorDefecto.includes(tab)
            ? this[Carpeta].forEach(m => { m.check = false })
            : this.MultimediaCarpetas.forEach(m => { m.check = false })

          this.$q.notify(r.Error)
        }
        this.eliminando = false
        this.modalEliminar = false

        this.MultimediaSeleccionado = []
      })
    },
    mailEnviado () {
      const tab = this.tab
      const Carpeta = 'Carpeta' + tab[0].toUpperCase() + tab.slice(1)
      this[Carpeta].forEach(m => { m.check = false })
      this.modalMail = false
      this.MultimediaSeleccionado = []
      this.ContenidoPDF = ''
    },
    enviarPDF (multimedia, contenido) {
      this.MultimediaSeleccionado = multimedia
      this.ContenidoPDF = contenido
      this.modalMail = true
      this.modalPDF = false
    },
    abrirNuevaPestaña () {
      let routeData = this.$router.resolve({ path: `/ArchivosCaso?id=${this.id}` })
      window.open(routeData.href, '_blank')
    },
    descargarArchivo (url, name) {
      location.href = `https://io.docdoc.com.ar/api/multimedia?file=${url}&download=true&name=${name}`
    },
    verErrorUpload (info) {
      console.log(info)
    },
    editarNombre (editar, nombre, id) {
      if (editar) {
        const tab = this.tab
        const Carpeta = 'Carpeta' + tab[0].toUpperCase() + tab.slice(1)
        request.Post('/multimedia-caso/editar-nombre', {IdCaso: this.id, IdMultimedia: this.IdMultimedia, Nombre: this.nombreEditar}, r => {
          if (!r.Error) {
            const i = this[Carpeta].findIndex(m => parseInt(m.IdMultimedia) === parseInt(this.IdMultimedia))
            this[Carpeta][i].Nombre = this.nombreEditar

            this.$q.notify('Se ha editado el archivo con exito.')
          } else {
            this.$q.notify(r.Error)
          }
          this.modalNombre = false
          this.nombreEditar = ''
          this.IdMultimedia = 0
        })
      } else {
        this.modalNombre = true
        this.nombreEditar = nombre
        this.IdMultimedia = id
      }
    },
    habilitarNuevaCarpeta () {
      this.NuevaCarpeta = ''
      this.editandoCarpeta = false
      this.modalCarpeta = true
    },
    crearCarpeta () {
      if (!this.NuevaCarpeta) {
        this.$q.notify('Debe escribir un nombre para la carpeta')
        return
      }

      request.Post('/casos/alta-carpeta', {Nombre: this.NuevaCarpeta.toUpperCase(), IdCaso: this.id}, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.CarpetasCaso.push({
            IdCarpetaCaso: r.IdCarpetaCaso,
            IdCaso: this.id,
            Nombre: this.NuevaCarpeta.toUpperCase()
          })
          this.tabs.push(this.NuevaCarpeta.toUpperCase())
          this.NuevaCarpeta = ''
          this.modalCarpeta = false
          this.ordenarCarpetas()
          this.$q.notify('Se creo una nueva carpeta con exito.')
        }
      })
    },
    moverArchivo (ids, IdCarpetaCaso) {
      request.Post('/multimedia-caso/mover', {ids: JSON.stringify(ids), IdCarpetaCaso: IdCarpetaCaso}, r => {
        if (r.length) {
          this.$q.notify('Ha ocurrido un error al intentar mover algunos archivos.')

          console.log(r)

          r.forEach(id => {
            const i = ids.findIndex(c => parseInt(c) === parseInt(id))
            ids.splice(i, 1)
          })
        } else {
          this.$q.notify('Los archivos han sido movidos con exito.')
        }

        const tab = this.tab

        if (this.tabsPorDefecto.includes(tab)) {
          const Carpeta = 'Carpeta' + tab[0].toUpperCase() + tab.slice(1)

          ids.forEach(id => {
            const i = this[Carpeta].findIndex(m => parseInt(m.IdMultimedia) === parseInt(id))
            this[Carpeta][i].IdCarpetaCaso = IdCarpetaCaso

            this.MultimediaCarpetas.push(this[Carpeta][i])
            this[Carpeta].splice(i, 1)
          })
        } else {
          ids.forEach(id => {
            const i = this.MultimediaCarpetas.findIndex(m => parseInt(m.IdMultimedia) === parseInt(id))
            this.MultimediaCarpetas[i].IdCarpetaCaso = IdCarpetaCaso
          })
        }
      })
    },
    habilitarEditarCarpeta (carpeta) {
      this.NuevaCarpeta = carpeta.Nombre
      this.IdCarpetaCaso = carpeta.IdCarpetaCaso
      this.editandoCarpeta = true
      this.modalCarpeta = true
    },
    editarCarpeta () {
      if (!this.NuevaCarpeta) {
        this.$q.notify('Debe escribir un nombre para la carpeta')
        return
      }

      request.Post('/casos/editar-carpeta', {Nombre: this.NuevaCarpeta.toUpperCase(), IdCaso: this.id, IdCarpetaCaso: this.IdCarpetaCaso}, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          const i = this.CarpetasCaso.findIndex(c => c.IdCarpetaCaso === this.IdCarpetaCaso)
          this.CarpetasCaso[i].Nombre = this.NuevaCarpeta

          this.tabs.push(this.NuevaCarpeta)
          this.NuevaCarpeta = ''
          this.editandoCarpeta = false
          this.modalCarpeta = false
          this.ordenarCarpetas()
          this.$q.notify('Se editó la carpeta con exito.')
        }
      })
    },
    habilitarEliminarCarpeta (id) {
      this.IdCarpetaCaso = id
      this.modalBorrarCarpeta = true
    },
    eliminarCarpeta () {
      request.Post('/casos/borrar-carpeta', { IdCarpetaCaso: this.IdCarpetaCaso }, r => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          const i = this.CarpetasCaso.findIndex(c => parseInt(c.IdCarpetaCaso) === parseInt(this.IdCarpetaCaso))
          const n = this.CarpetasCaso[i].Nombre

          const j = this.tabs.findIndex(t => t === n)

          this.CarpetasCaso.splice(i, 1)
          this.tabs.splice(j, 1)

          if (this.tab === n) { this.tab = 'caso' }

          this.MultimediaCarpetas.forEach(m => {
            if (parseInt(m.IdCarpetaCaso) === parseInt(this.IdCarpetaCaso)) {
              m.IdCarpetaCaso = ''

              switch (m.OrigenMultimedia) {
                case 'D':
                  this.CarpetaMovimientos.push(m)
                  break

                case 'R':
                  this.CarpetaCliente.push(m)
                  break

                case 'C':
                  this.CarpetaCaso.push(m)
                  break

                default:
                  break
              }
            }
          })

          this.modalBorrarCarpeta = false
          this.$q.notify('Se eliminó la carpeta con exito.')
        }
      })
    }
  }
}
</script>

<style>
  .q-tab__content.carpeta {
    width: 100% !important;
    justify-content: flex-start;
  }

  .acciones_carpeta {
    display: flex;
    width: 100%;
    justify-content: flex-end;
  }

  .img--multimedia {
    height: auto;
    width: auto;
    max-width: 320px;
    max-height: 240px;
  }

  .container_multimedia {
    height: 300px;
    width: 390px;
    display: flex;
    position: relative;
    margin: 2px auto;
    justify-content: center;
    text-align: center;
    border: 10px solid;
    border-color: transparent;
    border-radius: 25px;
  }

  .check_multimedia {
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: 100;
  }

  .botones_container_ {
    position: fixed;
    top: 180px;
    right: 30px;
    z-index: 100;
  }

  .nombre_multimedia {
    padding: 0px;
    min-height: 40px;
    align-items: end;
    justify-content: center;
    font-weight: bold;
    color: teal;
    font-size: 16px;
    margin-bottom: -10px;
  }

  .width-documentacion {
    transition: width 0.5s;
  }
</style>
