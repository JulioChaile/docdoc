<template>
  <div class="tarjeta__caso" :class="clases">
    <q-card style="background-color: white;">
      <div class="ver_caso_button_container">
        <q-btn class="ver_caso_button" color="accent" :to="enlaceVerCaso">
          <span class="text-capitalize text-caption">Ver caso completo</span>
        </q-btn>
      </div>
      <!-- Asignado -->
      <q-item
        style="color:white;display:flex; align-items:center; justify-content: space-between;"
        class="bg-primary"
      >
        <q-icon
          color="white"
          :name="ParamsTipoCaso[caso.TipoCaso] ? ParamsTipoCaso[caso.TipoCaso].icon : 'description'"
          size="sm"
        >
          <q-tooltip>{{ caso.TipoCaso || 'Tipo de Caso' }}</q-tooltip>
        </q-icon>
        <q-item-label>
          <span
            class="q-subheading"
            @click="mostrarPersonas()"
            style="cursor:pointer;"
          >{{ caso.Caratula ? caso.Caratula : '(Sin Carátula)' }}</span>
        </q-item-label>
        <q-btn icon="person" color="white" flat round @click="mostrandoModalPermisos = true" />
      </q-item>
      <q-separator />
      <q-item
        :style=" clases === 'casos__grilla' ? 'padding: 15px;display:flex; align-items:center;justify-content:space-between' : 'padding: 15px;display:flex; align-items:center;justify-content:flex-start'"
      >
        <q-item-label
          header
          :style="clases === 'casos__grilla' ? 'text-align:center;display:flex; flex-flow:column; align-items:center;' : ''"
        >
          <span>{{ `${caso.Juzgado}`}}</span>
          <span v-if="clases === 'casos__listado'">-</span>
          <span>{{ `${caso.Nominacion || '(Sin nominación)'}`}}</span>
        </q-item-label>
        <q-item-label v-if="clases === 'casos__listado'">|</q-item-label>
        <q-item-label
          header
          :style="clases === 'casos__grilla' ? 'text-align:center;display:flex; flex-flow:column; align-items:center;' : ''"
        >
          <span>{{ `${caso.TipoCaso}`}}</span>
          <span>{{ `${caso.EstadoCaso || 'Estado del Caso'}`}}</span>
          <span>{{`${caso.NroExpediente || '(Sin expediente)'}`}}</span>
        </q-item-label>
      </q-item>
      <q-separator />
      <div v-if="comparticionesEnviadas.length > 0">
        <q-list highlight>
          <q-item>
            <strong>Compartidas por mí estudio</strong>
          </q-item>
          <q-item
            v-for="comparticion in comparticionesEnviadas"
            :key="comparticion.IdComparticion"
            style="display:flex; justify-content: space-evenly"
          >
            <span
              class="tablaComparticion"
              v-for="(v, k) in datosComparticion(comparticion)"
              :key="v + k"
            >
              <strong>{{k}}</strong>
              <br />
              {{v}}
            </span>
          </q-item>
        </q-list>
      </div>
      <div v-if="comparticionesRecibidas.length > 0">
        <q-list highlight>
          <q-item>
            <strong>Compartidas de otros estudios</strong>
          </q-item>
          <q-item
            v-for="comparticion in comparticionesRecibidas"
            :key="comparticion.IdComparticion"
            style="display:flex; justify-content: space-evenly"
          >
            <span
              class="tablaComparticion"
              v-for="(v, k) in datosComparticion(comparticion)"
              :key="v + k"
            >
              <strong>{{k}}</strong>
              <br />
              {{v}}
            </span>
          </q-item>
        </q-list>
      </div>
      <!-- Ultimos 3 movimientos -->
      <div v-if="movimientosCaso.length > 0">
        <a
          v-for="movimiento in movimientosCaso.slice(0, 3)"
          :key="movimiento.IdMovimientoCaso"
          @click="mostrarMovimiento(movimiento)"
          style="cursor:pointer;"
        >
          <q-list highlight inset-separator>
            <q-item v-if="movimiento.FechaRealizado" style="display:flex; align-items:center;">
              <q-icon name="check_box" size="sm" color="green" style="margin-right:1rem;">
                <q-tooltip
                  anchor="top left"
                  self="bottom left"
                >Realizado el {{ parseDate(movimiento.FechaRealizado) }}</q-tooltip>
              </q-icon>
              <span>{{ movimiento.Detalle }} - {{diasRestantes(movimiento)}}</span>
            </q-item>
            <q-item
              v-if="!movimiento.FechaRealizado"
            >{{ movimiento.Detalle }} - {{diasRestantes(movimiento)}}</q-item>
          </q-list>
        </a>
      </div>
      <div v-else>
        <div v-if="cargandoMovimientos">
          <Loading />
        </div>
        <div v-else>
          <div style="margin-top:15px;margin-bottom:15px;margin-left: 15px">No hay movimientos</div>
          <q-separator />
        </div>
      </div>
      <q-card-actions
        :align="'right'"
        class="text-primary"
        :style="isMobile() ? 'display:flex; flex-direction:column; font-weight:500; font-size:14; background-color: #E8EAF6;' : 'font-weight:500; font-size:14; background-color: #E8EAF6;'"
      >
        <div :style="isMobile() ? 'margin-left: auto; margin-right: auto;' : 'margin-right: auto;'">
          <q-dialog v-model="mostrarUnMovimiento" style="background-color: white">
            <q-card style="min-width:95vw;">
              <TarjetaMovimiento
                v-if="mostrarUnMovimiento"
                :movimiento="MovimientoAMostrar"
                style="margin-left: auto; margin-right:auto;"
              />
              <q-btn
                flat
                @click="mostrarUnMovimiento = false"
                style="padding-top:30px; float: right; margin-bottom:20px; margin-right: 20px;"
              >Cancelar</q-btn>
            </q-card>
          </q-dialog>
          <!-- Movimientos -->
          <div style="margin-left:auto; margin-right:auto; text-align:center;">
            <q-btn
              round
              color="accent"
              icon="add"
              class="q-subheading"
              size="sm"
              @click="altaMovimientosModal = true"
            />
            <span style="margin-left: 5px">Nuevo Movimiento</span>
          </div>
        </div>
        <div :style="isMobile() ? 'margin-left: auto; margin-right: auto; margin-top:0.5rem;' : ''">
          <q-btn
            icon="assignment"
            color="primary"
            flat
            rounded
            outline
            @click="abrirMovimientosModal"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver Movimientos</q-tooltip>
          </q-btn>
          <q-dialog v-model="altaMovimientosModal">
            <q-card>
              <q-item class="relative-position" style="background-color: black; color:white;display:flex;align-items:center;">
                <q-icon color="white" name="assignment" size="sm" style="margin-right: 1rem;" />
                <span class="q-subheading">Nuevo Movimiento</span>
                <q-icon
                  name="close"
                  class="absolute-right cursor-pointer"
                  color="negative"
                  size="lg"
                  @click="altaMovimientosModal = false"
                />
              </q-item>
              <AltaMovimientos
                v-if="altaMovimientosModal"
                :altaDesdeTarjetaCaso="true"
                :casoRecibido="caso.IdCaso"
                @cancelar="altaMovimientosModal = false"
                @exito="exitoAltaMovimiento"
              />
            </q-card>
          </q-dialog>
          <q-dialog v-model="movimientosModal">
            <q-card style="min-width:95vw;">
              <q-item
                style="background-color: black; color: white;display:flex; justify-content:space-between; align-items:center;"
              >
                <q-icon color="white" name="assignment" size="sm" />
                <q-item-label>
                  <span class="q-subheading">Movimientos del Caso</span>
                </q-item-label>
                <q-btn
                  round
                  color="primary"
                  class="q-subheading"
                  size="sm"
                  style="color:black;"
                  @click="altaMovimientosModal = true"
                >
                  <q-icon name="add" color="black;"></q-icon>
                  <q-tooltip>Nuevo Movimiento</q-tooltip>
                </q-btn>
              </q-item>
              <div v-if="movimientosCaso.length > 0">
                <TarjetaMovimiento
                  v-for="(m, i) in movimientosCaso"
                  :key="i"
                  :movimiento="m"
                  :tarjetaCaso="true"
                  @borrar="borrarMovimiento(m)"
                  style="margin-right:auto; margin-left:auto;"
                />
              </div>
              <q-item v-else>
                <q-item-label>No se asignaron movimientos al caso</q-item-label>
              </q-item>
              <q-btn
                flat
                @click="movimientosModal = false"
                style="float:right; margin:10px 10px 20px"
              >Cancelar</q-btn>
            </q-card>
          </q-dialog>
          <!-- Objetivos -->
          <q-btn icon="timeline" color="primary" flat rounded outline @click="mostrarObjetivos">
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver Objetivos</q-tooltip>
          </q-btn>
          <q-dialog v-model="objetivosModal">
            <q-card>
              <q-item
                style="background-color: black; color:white; display:flex;align-items:center;"
              >
                <q-icon color="white" name="timeline" size="sm" />
                <span class="q-subheading" style="margin-left:1rem;">Objetivos del Caso</span>
              </q-item>
              <q-separator />
              <q-dialog v-model="altaObjetivosModal">
                <q-card>
                  <q-item
                    style="background-color:black; display:flex; align-items:center; color:white;"
                  >
                    <q-icon color="white" name="timeline" size="sm" style="margin-right: 1rem" />
                    <span class="q-subheading">Nuevo Objetivo</span>
                  </q-item>
                  <q-separator />
                  <q-item>
                    <q-input v-model="nuevoObjetivo.Objetivo" label="Nombre del objetivo" />
                  </q-item>
                  <q-card-actions :align="'right'">
                    <q-btn flat color="primary" label="Aceptar" @click="crearNuevoObjetivo" />
                    <q-btn
                      flat
                      color="primary"
                      label="Cancelar"
                      @click="altaObjetivosModal = false"
                    />
                  </q-card-actions>
                </q-card>
              </q-dialog>
              <div v-if="!perentorioHome">
                <div v-if="caso.Objetivos && caso.Objetivos.length > 0">
                  <q-item
                    v-for="objetivo in caso.Objetivos"
                    :key="objetivo.IdObjetivo"
                    style="padding: 15px;"
                  >
                    <q-item-section color="primary" style="margin-right: 5px;" icon="label" />
                    <q-item-label header>{{ objetivo.Objetivo }}</q-item-label>
                    <q-btn
                      icon="edit"
                      color="primary"
                      flat
                      rounded
                      outline
                      @click="edicionObjetivo(objetivo)"
                    >
                      <q-tooltip
                        anchor="bottom middle"
                        self="top middle"
                        :offset="[10, 0]"
                      >Editar Objetivo</q-tooltip>
                    </q-btn>
                    <q-dialog v-model="editarObjetivo">
                      <q-card>
                        <q-item
                          style="background-color:black; display:flex; align-items:center; color:white;"
                        >
                          <q-icon
                            color="white"
                            name="timeline"
                            size="sm"
                            style="margin-right: 1rem"
                          />
                          <span class="q-subheading">Editar Objetivo</span>
                        </q-item>
                        <q-separator />
                        <q-item>
                          <q-input
                            v-model="objetivoEditar.Objetivo"
                            type="textarea"
                            rows="1"
                            :max-height="50"
                            label="Objetivo"
                            style="margin: 2rem"
                          />
                        </q-item>
                        <q-card-actions :align="'right'">
                          <q-btn flat color="primary" @click="finalizarEdicionObjetivo()">Guardar</q-btn>
                          <q-btn flat @click="editarObjetivo = false">Cancelar</q-btn>
                        </q-card-actions>
                      </q-card>
                    </q-dialog>
                    <q-dialog v-model="mostrandoModalEditarObjetivo" prevent-close>
                      <q-card style="padding:1rem;">
                        <span class="text-h6 white">Editar Objetivo</span>
                        <span>
                          <p>Al salir se descartarán los cambios. ¿Está seguro?</p>
                        </span>
                        <div style="float:right;">
                          <q-btn
                            color="primary"
                            label="Aceptar"
                            @click="editarObjetivo=false, mostrandoModalEditarObjetivo=false"
                          />
                          <q-btn
                            flat
                            label="Cancelar"
                            @click="mostrandoModalEditarObjetivo = false"
                          />
                        </div>
                      </q-card>
                    </q-dialog>
                    <q-btn
                      icon="delete"
                      color="primary"
                      flat
                      rounded
                      outline
                      @click="borrarObjetivo(objetivo)"
                    >
                      <q-tooltip
                        anchor="bottom middle"
                        self="top middle"
                        :offset="[10, 0]"
                      >Eliminar Objetivo</q-tooltip>
                    </q-btn>
                    <q-dialog v-model="mostrandoModalBorrarObjetivo" prevent-close>
                      <q-card style="padding:1rem;">
                        <span class="text-h6">Eliminar Objetivo</span>
                        <span>
                          <p>
                            Al eliminar un objetivo, este no podrá ser recuperado en el futuro.
                            ¿Está seguro que desea eliminar este objetivo?
                          </p>
                        </span>
                        <div style="float:right;">
                          <q-btn
                            color="primary"
                            label="Eliminar"
                            @click="finalizarBorradoObjetivo()"
                          />
                          <q-btn
                            flat
                            label="Cancelar"
                            @click="mostrandoModalBorrarObjetivo = false"
                          />
                        </div>
                      </q-card>
                    </q-dialog>
                  </q-item>
                </div>
                <q-item v-else>
                  <q-item-label>No se asignaron objetivos al caso</q-item-label>
                </q-item>
              </div>
              <div v-else>
                <div v-if="objetivosCaso.length">
                  <q-item
                    v-for="objetivo in objetivosCaso"
                    :key="objetivo.IdObjetivo"
                    style="padding: 15px;"
                  >
                    <q-item-section color="primary" style="margin-right: 5px;" icon="label" />
                    <q-item-label header>{{ objetivo.Objetivo }}</q-item-label>
                    <q-btn
                      icon="edit"
                      color="primary"
                      flat
                      rounded
                      outline
                      @click="edicionObjetivo(objetivo)"
                    >
                      <q-tooltip
                        anchor="bottom middle"
                        self="top middle"
                        :offset="[10, 0]"
                      >Editar Objetivo</q-tooltip>
                    </q-btn>
                    <q-dialog v-model="editarObjetivo">
                      <q-card>
                        <q-item
                          style="background-color:black; display:flex; align-items:center; color:white;"
                        >
                          <q-icon
                            color="white"
                            name="timeline"
                            size="sm"
                            style="margin-right: 1rem"
                          />
                          <span class="q-subheading">Editar Objetivo</span>
                        </q-item>
                        <q-separator />
                        <q-item>
                          <q-input
                            v-model="objetivoEditar.Objetivo"
                            type="textarea"
                            rows="1"
                            :max-height="50"
                            label="Objetivo"
                            style="margin: 2rem"
                          />
                        </q-item>
                        <q-card-actions :align="'right'">
                          <q-btn flat color="primary" @click="finalizarEdicionObjetivo()">Guardar</q-btn>
                          <q-btn flat @click="editarObjetivo = false">Cancelar</q-btn>
                        </q-card-actions>
                      </q-card>
                    </q-dialog>
                    <q-dialog v-model="mostrandoModalEditarObjetivo" prevent-close>
                      <q-card style="padding:1rem;">
                        <span class="text-h6 white">Editar Objetivo</span>
                        <span>
                          <p>Al salir se descartarán los cambios. ¿Está seguro?</p>
                        </span>
                        <div style="float:right;">
                          <q-btn
                            color="primary"
                            label="Aceptar"
                            @click="editarObjetivo=false, mostrandoModalEditarObjetivo=false"
                          />
                          <q-btn
                            flat
                            label="Cancelar"
                            @click="mostrandoModalEditarObjetivo = false"
                          />
                        </div>
                      </q-card>
                    </q-dialog>
                    <q-btn
                      icon="delete"
                      color="primary"
                      flat
                      rounded
                      outline
                      @click="borrarObjetivo(objetivo)"
                    >
                      <q-tooltip
                        anchor="bottom middle"
                        self="top middle"
                        :offset="[10, 0]"
                      >Eliminar Objetivo</q-tooltip>
                    </q-btn>
                    <q-dialog v-model="mostrandoModalBorrarObjetivo" prevent-close>
                      <q-card style="padding:1rem;">
                        <span class="text-h6">Eliminar Objetivo</span>
                        <span>
                          <p>
                            Al eliminar un objetivo, este no podrá ser recuperado en el futuro.
                            ¿Está seguro que desea eliminar este objetivo?
                          </p>
                        </span>
                        <div style="float:right;">
                          <q-btn
                            color="primary"
                            label="Eliminar"
                            @click="finalizarBorradoObjetivo()"
                          />
                          <q-btn
                            flat
                            label="Cancelar"
                            @click="mostrandoModalBorrarObjetivo = false"
                          />
                        </div>
                      </q-card>
                    </q-dialog>
                  </q-item>
                </div>
                <q-item v-else>
                  <q-item-label>No se asignaron objetivos al caso</q-item-label>
                </q-item>
              </div>
              <div style="padding-top:20px; padding-left:20px; float: right">
                <q-btn
                  rounded
                  color="accent"
                  icon="add"
                  style="margin-top:10px; margin-bottom:20px; margin-right: 10px;"
                  label="Nuevo Objetivo"
                  @click="altaObjetivosModal = true"
                />
                <q-btn
                  flat
                  @click="objetivosModal = false"
                  style="margin-top:10px; margin-bottom:20px; margin-right: 10px;"
                >Cancelar</q-btn>
              </div>
            </q-card>
          </q-dialog>
          <!-- Personas -->
          <q-btn icon="people" color="primary" flat rounded outline @click="mostrarPersonas()">
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Ver Personas</q-tooltip>
          </q-btn>
          <q-dialog v-model="personas">
            <q-card style="width: 100%;">
              <q-item style="background-color: black; color:white;display:flex;align-items:center;">
                <q-icon color="white" name="people" size="sm" />
                <span class="q-subheading" style="margin-left:1rem;">Personas del Caso</span>
              </q-item>
              <q-separator />
              <div v-if="cargandoPersonas">
                <Loading />
              </div>
              <div v-else-if="personas">
                <q-item
                  style="padding: 15px; display:flex; align-items:center; justify-content: space-between;"
                  v-for="persona in personasCaso"
                  :key="persona.Documento"
                >
                  <q-icon
                    v-bind:color="persona.EsPrincipal ? 'yellow' : 'white'"
                    name="star"
                    style="margin-right: 5px;"
                  />
                  <q-item-label
                    header
                  >{{`${persona.Apellidos ? persona.Apellidos + ', ' : ''}`}} {{persona.Nombres}}</q-item-label>
                  <q-item-label
                    subheader
                    style="margin-top: 0;"
                  >DNI: {{ `${persona.Documento || '-'}` }}</q-item-label>
                  <q-item-label
                    v-if="persona.Telefono != null"
                    subheader
                    style="margin-left:1rem; margin-top: 0;"
                  >Tel.: {{persona.Telefono}}</q-item-label>
                  <q-btn
                    color="primary"
                    icon="edit"
                    style="margin-left: 5px;"
                    flat
                    round
                    outline
                    @click="editarPersona(persona)"
                  />
                  <q-dialog v-model="editarPers" style="padding:2rem;">
                    <q-card>
                      <div class="form__container" style="padding:1rem 2rem 0 2rem;">
                        <q-input
                          v-if="personaEditar.Tipo === 'F'"
                          class="input"
                          type="number"
                          v-model="documento"
                          label="Documento"
                        />
                        <q-input
                          class="input"
                          type="text"
                          v-model="personaEditar.Nombres"
                          v-bind:label="personaEditar.Tipo === 'F' ? 'Nombres/s' : 'Razón Social'"
                        />
                        <q-input
                          class="input"
                          type="text"
                          v-model="personaEditar.Apellidos"
                          label="Apellidos/s"
                          v-if="personaEditar.Tipo === 'F'"
                        />
                        <q-input
                          class="input"
                          type="email"
                          v-model="personaEditar.Email"
                          label="Email"
                        />
                        <q-input
                          class="input"
                          type="tel"
                          v-model="personaEditar.Cuit"
                          label="CUIT"
                        />
                        <q-input
                          class="input"
                          type="tel"
                          v-model="personaEditar.Telefono"
                          label="Teléfono"
                        />
                        <q-input
                          v-if="false"
                          class="input"
                          type="tel"
                          v-model="newTel"
                          label="Nuevo Teléfono"
                        />
                        <q-input class="input" v-model="personaEditar.Domicilio" label="Domicilio" />
                      </div>
                      <div style="display: flex;justify-content: flex-end; margin-botton:2rem;">
                        <q-btn
                          flat
                          style="margin-right: 15px; margin-top: 30px; margin-bottom:2rem;"
                          color="primary"
                          @click="editarPers = false"
                        >Cancelar</q-btn>
                        <q-btn
                          style="margin-top: 30px; margin-right:2rem; margin-bottom:2rem;"
                          color="accent"
                          @click="guardarPersona(personaEditar)"
                        >Listo</q-btn>
                      </div>
                    </q-card>
                  </q-dialog>
                </q-item>
              </div>
              <q-btn
                flat
                @click="personas = false"
                style="float: right; margin-top:10px; margin-bottom:20px; margin-right: 10px;"
              >Cancelar</q-btn>
            </q-card>
          </q-dialog>
          <!-- Editar -->
          <q-btn icon="edit" color="primary" flat rounded outline @click="editar=true">
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Editar</q-tooltip>
          </q-btn>
          <q-dialog v-model="editar" no-backdrop-dismiss no-esc-dismiss>
            <q-card>
              <EditarCaso
                v-if="this.editar"
                :casoRecibido="this.caso"
                :EstadosCaso="EstadosCaso"
                @edicionTerminada="edicionTerminada()"
                @cancelar-edicion="editar = false"
              />
            </q-card>
          </q-dialog>
          <!-- Compartir -->
          <q-btn icon="share" color="primary" flat rounded outline @click="compartir=true">
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Compartir</q-tooltip>
          </q-btn>
          <q-dialog v-model="compartir" style="width: auto; height:auto;">
            <CompartirCaso
              :caso="caso"
              @compartir="compartir = false"
            />
          </q-dialog>
          <!-- Archivar -->
          <q-btn
            icon="archive"
            color="primary"
            flat
            rounded
            outline
            @click="mostrandoModalArchivar = true"
          >
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Archivar</q-tooltip>
          </q-btn>
          <!-- Eliminar -->
          <q-btn icon="delete" color="primary" flat rounded outline @click="mostrarBorrarCaso">
            <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 0]">Eliminar</q-tooltip>
          </q-btn>
        </div>
      </q-card-actions>
    </q-card>
    <!-- Modales -->
    <q-dialog v-model="mostrandoModalPermisos">
      <q-card>
        <TarjetaPermisos
          v-if="mostrandoModalPermisos"
          v-model="caso.UsuariosCaso"
          :idcaso="caso.IdCaso"
        />
      </q-card>
    </q-dialog>
    <q-dialog v-model="mostrandoModalArchivar" prevent-close>
      <q-card style="padding:1rem;">
        <span class="text-h6">Archivar Caso</span>
        <span>
          <p>
            Al archivar un caso, este se ocultará de la lista.
            Puede volver a ver el caso filtrando la búsqueda en la Página Casos
          </p>
        </span>
        <div style="float:right;">
          <q-btn color="primary" label="Archivar" @click="archivarCaso()" />
          <q-btn flat label="Cancelar" @click="mostrandoModalArchivar = false" />
        </div>
      </q-card>
    </q-dialog>
    <q-dialog v-model="mostrandoModalBorrar" prevent-close>
      <q-card style="padding:1rem;">
        <span class="text-h6">Eliminar Caso</span>
        <span>
          <p>
            Al eliminar un caso, este no podrá ser recuperado en el futuro.
            ¿Está seguro que desea eliminar este caso?
          </p>
          <div>
            <strong>Al eliminarse el caso se eliminarán también los siguientes elementos:</strong>
          </div>
          <div v-for="(val, info) in infoBorrar" :key="info">{{ mapBorrado[info] }} : {{ val }}</div>
        </span>

        <div style="float:right;">
          <q-btn color="primary" label="Eliminar" @click="bajaCaso()" />
          <q-btn flat label="Cancelar" @click="mostrandoModalBorrar = false" />
        </div>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { sortBy, reverse } from 'lodash'
import AltaCasos from '../pages/AltaCasos'
import AltaMovimientos from '../pages/AltaMovimientos'
import EditarCaso from '../components/EditarCaso'
import TarjetaMovimiento from '../components/TarjetaMovimiento'
import TarjetaPermisos from '../components/TarjetaPermisos'
import Loading from '../components/Loading'
import StepPersona from '../components/StepPersona'
import CompartirCaso from '../components/Caso/CompartirCaso'
import request from '../request'
import ParamsTipoCaso from '../parameters/TiposCaso'
import { Notify, QDialog } from 'quasar'
import auth from '../auth'
import Vue from 'vue'

export default {
  components: {
    AltaCasos,
    AltaMovimientos,
    TarjetaMovimiento,
    TarjetaPermisos,
    StepPersona,
    EditarCaso,
    Loading,
    QDialog,
    CompartirCaso
  },
  data () {
    return {
      enlaceVerCaso: `/Caso?id=${this.caso.IdCaso}`,
      objetivosCaso: [],
      documento: '',
      newTel: '',
      ParamsTipoCaso,
      personas: false,
      objetivosModal: false,
      altaObjetivosModal: false,
      movimientosModal: false,
      altaMovimientosModal: false,
      verMovimientoModal: false,
      editar: false,
      editarObjetivo: false,
      compartir: false,
      movimientosCaso: [],
      MovimientoAMostrar: {},
      Juzgado: '',
      TipoCaso: '',
      editarPers: false,
      personaEditar: {},
      personasCaso: [],
      cargandoMovimientos: true,
      cargandoPersonas: true,
      mostrandoModalArchivar: false,
      mostrandoModalBorrar: false,
      mostrandoModalEditar: false,
      mostrandoModalEditarObjetivo: false,
      mostrarUnMovimiento: false,
      mostrandoModalBorrarObjetivo: false,
      mostrandoModalPermisos: false,
      infoBorrar: null,
      index: 0,
      EstadosCaso: [],
      mapBorrado: {
        CantPersonas: 'Cantidad de personas',
        CantMovimientos: 'Cantidad de movimientos',
        CantUsuariosCaso: 'Cantidad de usuarios del caso',
        CantMultimedia: 'Cantidad de archivos multimedia'
      },
      nuevoObjetivo: {
        Objetivo: '',
        IdCaso: this.caso.IdCaso
      },
      objetivoEditar: {},
      objetivoBorrar: {},
      emailCompartir: ''
    }
  },
  props: ['caso', 'clases', 'perentorioHome'],
  created () {
    if (this.perentorioHome) {
      this.movimientosCaso = this.caso.UltimosMovimientos
        ? reverse(sortBy(JSON.parse(this.caso.UltimosMovimientos), 'FechaAlta'))
        : reverse(sortBy(this.caso.Movimientos, 'FechaAlta'))
      this.cargandoMovimientos = false
    } else {
      request.Get(`/casos/${this.caso.IdCaso}/movimientos`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.movimientosCaso = r
          this.cargandoMovimientos = false
        }
      })
      request.Get(`/casos/${this.caso.IdCaso}`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.caso.PersonasCaso = r.PersonasCaso
        }
      })
      request.Get(
        `/objetivos?IdsCaso=${JSON.stringify([this.caso.IdCaso])}`,
        {},
        (res) => {
          if (!res.Error) {
            // Obtuve un array de todos los objetivos del movimiento
            Vue.set(this, 'objetivosCaso', res[this.caso.IdCaso])
          }
        }
      )
    }
  },
  computed: {
    comparticionesEnviadas () {
      return this.caso.ComparticionesEnviadas
        ? JSON.parse(this.caso.ComparticionesEnviadas)
        : []
    },
    comparticionesRecibidas () {
      return this.caso.ComparticionesRecibidas
        ? JSON.parse(this.caso.ComparticionesRecibidas)
        : []
    }
  },
  methods: {
    guardarPersona (persona) {
      if (this.newTel) {
        persona.Telefono.push(this.newTel)
      }
      persona.Documento = this.documento
      persona.Telefonos = persona.Telefono
      const idEstudio = auth.UsuarioLogueado.IdEstudio
      const idPersona = persona.IdPersona
      request.Put(
        `/estudios/${idEstudio}/modificar-persona/${idPersona}`,
        persona,
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.$emit('edicionTerminada')
          }
        }
      )
      this.editarPers = false
    },
    datosComparticion (comparticion) {
      let usuarioOrigen = ''
      let usuarioDestino = comparticion.Email
      let usuarios = this.caso.UsuariosCaso
      if (typeof usuarios === 'string') {
        usuarios = JSON.parse(usuarios)
      }
      usuarios.forEach((c) => {
        if (String(c.IdUsuario) === String(comparticion.IdUsuarioOrigen)) {
          usuarioOrigen = c.Apellidos + ', ' + c.Nombres
        }
        if (String(c.IdUsuario) === String(comparticion.IdUsuarioDestino)) {
          usuarioDestino = c.Apellidos + ', ' + c.Nombres
        }
      })
      let fechaRecibido = comparticion.FechaRecibido
        ? new Date(comparticion.FechaRecibido).toLocaleString()
        : 'Todavía no se aceptó'
      return {
        'Usuario Orígen': usuarioOrigen,
        'Usuario Destino': usuarioDestino,
        'Fecha Enviado': new Date(comparticion.FechaEnviado).toLocaleString(),
        'Fecha Aceptado': fechaRecibido
      }
    },
    isMobile () {
      if (
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
          navigator.userAgent
        )
      ) {
        return true
      } else {
        return false
      }
    },
    abrirMovimientosModal () {
      this.movimientosModal = true
      /*
      request.Get(`/casos/${this.caso.IdCaso}/movimientos`, {}, r => {
        if (!r.Error) {
          this.movimientosCaso = reverse(sortBy(r, 'FechaAlta'))
        }
      })
      */
    },
    exitoAltaMovimiento (movimiento) {
      this.altaMovimientosModal = false
      this.$q.notify({
        color: 'green',
        message: `El movimiento "${movimiento.Detalle}" se dio de alta correctamente`
      })
      this.movimientosCaso.unshift(movimiento)
    },
    parseDateTime (fecha) {
      if (fecha !== null) {
        const fh = fecha.split(' ')
        const amd = fh[0].split('-')
        const hms = fh[1].split(':')
        return `${amd[2]}/${amd[1]}/${amd[0]} ${hms[0]}:${hms[1]}`
      } else return null
    },
    parseDate (fecha) {
      if (fecha !== null) {
        return this.parseDateTime(fecha).split(' ')[0]
      } else return null
    },
    mostrarMovimiento (movimiento) {
      this.mostrarUnMovimiento = true
      this.MovimientoAMostrar = movimiento
    },
    mostrarPersonas () {
      this.personas = true
      this.personasCaso = []
      request.Get(`/casos/${this.caso.IdCaso}/personas`, {}, (r) => {
        if (!r.Error) {
          this.cargandoPersonas = false
          this.personasCaso = r.slice()
          this.personasCaso.forEach((c) => {
            if (!c.Telefono || c.Telefono === 'null') {
              c.Telefono = []
            }
          })
        }
      })
    },
    editarPersona (persona) {
      /*
      JSON.parse(persona.Telefonos).forEach(t => {
        console.log(t);
      });
      */
      this.personaEditar = persona
      this.documento = persona.Documento
      this.editarPers = true
    },
    diasRestantes (movimiento) {
      if (movimiento.FechaRealizado) {
        return 'Realizado'
      }
      if (!movimiento.FechaEsperada) {
        return 'Sin fecha esperada'
      }
      this.fecha2 = new Date()
      var year = movimiento.FechaEsperada.split('-')[0]
      var month = movimiento.FechaEsperada.split('-')[1] - 1
      var day = movimiento.FechaEsperada.split('-')[2].split(' ')[0] - 1
      this.fecha2.setMonth(month)
      this.fecha2.setFullYear(year)
      this.fecha2.setDate(day)
      this.hoy = new Date()
      var resultado =
        Math.ceil(
          (this.fecha2.getTime() - this.hoy.getTime()) / (1000 * 60 * 60 * 24)
        ) + 1
      if (resultado > 0) {
        return 'Vence en ' + resultado + (resultado === 1 ? ' día' : ' días')
      } else if (resultado < 0) {
        return (
          'Vencido hace ' +
          resultado * -1 +
          (resultado === 1 ? ' día' : ' días')
        )
      } else {
        return 'Vence hoy'
      }
    },
    archivarCaso: function () {
      request.Put(`/casos/${this.caso.IdCaso}/archivar`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
          this.mostrandoModalArchivar = false
        } else {
          this.$emit('quitar', this.caso)
          this.mostrandoModalArchivar = false
        }
      })
    },
    bajaCaso: function () {
      request.Put(`/casos/${this.caso.IdCaso}/baja`, {}, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.$emit('quitar', this.caso)
        }
      })
    },
    mostrarBorrarCaso () {
      request.Get(
        `/casos/${this.caso.IdCaso}/previsualizar-borrado`,
        {},
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.mostrandoModalBorrar = true
            this.infoBorrar = r
          }
        }
      )
    },
    mostrarObjetivos () {
      this.objetivosModal = true
    },
    crearNuevoObjetivo () {
      request.Post('/objetivos', this.nuevoObjetivo, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else if (!this.perentorioHome) {
          this.caso.Objetivos.push(Object.assign({}, this.nuevoObjetivo))
          this.nuevoObjetivo.Objetivo = ''
          this.altaObjetivosModal = false
        } else {
          this.objetivosCaso.push(Object.assign({}, this.nuevoObjetivo))
          this.nuevoObjetivo.Objetivo = ''
          this.altaObjetivosModal = false
        }
      })
    },
    edicionObjetivo (objetivo) {
      this.objetivoEditar = objetivo
      this.editarObjetivo = true
    },
    finalizarEdicionObjetivo () {
      let objetivo = {
        Objetivo: this.objetivoEditar.Objetivo
      }
      request.Put(
        `/objetivos/${this.objetivoEditar.IdObjetivo}`,
        objetivo,
        (r) => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            this.editarObjetivo = false
            this.objetivoEditar = {}
          }
        }
      )
    },
    borrarObjetivo (objetivo) {
      this.mostrandoModalBorrarObjetivo = true
      this.objetivoBorrar = objetivo
    },
    finalizarBorradoObjetivo () {
      request.Delete('/objetivos/', this.objetivoBorrar.IdObjetivo, (r) => {
        if (r.Error) {
          Notify.create(r.Error)
        } else {
          this.mostrandoModalBorrarObjetivo = false
          this.caso.Objetivos.splice(
            this.caso.Objetivos.indexOf(this.objetivoBorrar),
            1
          )
        }
      })
    },
    edicionTerminada () {
      this.editar = false
    },
    borrarMovimiento (movimiento) {
      this.movimientosModal = false
      this.movimientosCaso.splice(this.movimientosCaso.indexOf(movimiento), 1)
    },
    validarEmail () {
      let re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      if (!re.test(String(this.emailCompartir).toLowerCase())) {
        return false
      }
      return true
    },
    verificarMail () {
      if (this.validarEmail()) {
        this.compartirCaso()
      } else {
        this.$q.notify('Debe ingresar un email válido')
      }
    },
    compartirCaso () {
      let datosCompartir = {
        Email: this.emailCompartir,
        IdCaso: this.caso.IdCaso
      }
      request.Post('/casos/compartir', datosCompartir, (r) => {
        if (r.Error) {
          this.$q.notify(r.Error)
        } else {
          this.compartir = false
          this.$q.notify({
            color: 'green',
            message: `Se compartió el caso '${this.caso.Caratula}' correctamente`
          })
        }
      })
    }
  }
}
</script>

<style>
.tarjeta_caso {
  transition: all 0.6s linear !important;
}
.tarjeta__caso:hover {
  filter: brightness(0.94);
}
.ver_caso_button_container {
  position: absolute;
  z-index: 0;
  bottom: -36px;
  right: 0;
}
.ver_caso_button {
  border-radius: 0 0 15px 15px;
  cursor: pointer !important;
}

.q-subheading {
  font-size: 18px;
}
@media (max-width: 900px) {
  .casos__grilla {
    width: 100%;
  }
}
@media (min-width: 900px) and (max-width: 1200px) {
  .casos__grilla {
    width: 45%;
  }
}
@media (min-width: 1200px) {
  .casos__grilla {
    width: 30%;
  }
}
.casos__listado {
  width: 100%;
}
.tarjeta__caso {
  margin: 1rem;
}
.docdoc-title {
  font-size: 15px;
  text-align: left;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: flex-start;
}
.tablaComparticion {
  width: 25%;
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
  padding: 3px;
}
</style>
