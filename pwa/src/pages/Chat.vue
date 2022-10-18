<template>
  <div
    class="full_container"
    :style="id  ? 'width: auto !important; margin-top: 52px' : ''"
  >
    <div
      class="chat_container"
      :style="id ? 'width: 100% !important; background-color: inherit' : ''"
    >
      <!-- Header -->
      <q-item v-if="!id" class="header_container" :style=" idMediacion || idContacto ? '' : 'cursor: pointer'" @click.native="abrirCaso()">
        <span class="text-black" style="text-align:center">
          {{ idMediacion ? 'Mediador: ' : (idContacto ? 'Contacto: ' : 'Caso: ')}}
          <span class="text-weight-bold text-black">
            {{ caratula ? caratula : 'Sin carátula' }}
          </span>
          <br>
          Telefono:
          <span class="text-weight-bold text-black">
            {{ telefono }}
          </span>
        </span>
        <q-tooltip v-if="IdCaso">Ir al caso</q-tooltip>
      </q-item>

      <!-- Mensajes -->
      <div class="mensajes_container" id="scrollDiv">
        <q-chat-message
          v-for="(mensaje, index) in mensajes"
          :key="index"
          :text="esArchivo(mensaje.Contenido) ? [] : [mensaje.Contenido]"
          :stamp="stamp(mensaje)"
          bg-color="grey-5"
          :sent="mensaje.IdUsuario ? true : false"
          class="caja-chat"
          size="5"
          text-color="white"
        >
          <template v-if="esArchivo(mensaje.Contenido)" v-slot:default>
            <audio v-if="tipoArchivo(mensaje.Contenido) === 'A'" controls>
              <source :src="mensaje.Contenido">
            </audio>
            <video v-else-if="tipoArchivo(mensaje.Contenido) === 'V'" class="img--multimedia" :src="mensaje.Contenido" controls></video>
            <img  v-else-if="tipoArchivo(mensaje.Contenido) === 'I'" class="img--multimedia" :src="mensaje.Contenido">
            <span v-else>Formato Desconocido</span>
            <br>
            <a class="self-bottom" :href="mensaje.Contenido" :download="`ArchivoCaso-${caratula}`" target="_blank">
              Descargar
            </a>
          </template>
        </q-chat-message>
        <span id="scrollSpan" style="height: 0"></span>
      </div>

      <!-- Input -->
      <div
        class="input_container"
        :style="id ? 'background-color: inherit' : ''"
      >
        <div class="row">
          <q-checkbox
            v-model="enviarUsuario"
            label="Mostrar nombre en el mensaje"
            class="q-pr-md q-pl-lg"
          />
          <div class="col-12">
            <q-input v-model="inputMessage" @keyup.enter="send" class="q-pr-md q-pl-lg" filled type="textarea" rows="1" placeholder="Escriba su mensaje aqui...">
              <template v-slot:after>
                <q-btn round flat icon="attach_file" style="transform: rotate(-45deg)" class="send_btn">
                  <q-tooltip>Enviar Archivo</q-tooltip>
                  <q-popup-proxy @show="upload()" style="width: 300px; height: 300px">
                    <q-item class="items-center" style="width: 300px; height: 100%">
                      <q-uploader
                        v-if="Multimedia.length === 0"
                        ref="uploader"
                        auto-upload
                        max-files="1"
                        :factory="factoryFn"
                        @uploaded="uploadedFile"
                        @removed="removedFile"
                        style="width: 100%; margin-top: 10px"
                      />
                      <div v-else class="items-center" style="position: relative; width: 300px">
                        <span>{{ archivo.nombre }}</span>
                        <q-btn style="position: absolute; top: 0px; right: 0px" color="negative" round size="xs" @click="Multimedia.splice(0)">
                          X
                        </q-btn>
                        <br>
                        <img v-if="archivo.srcImg" :src="archivo.srcImg" class="img--multimedia--enviado" />
                      </div>
                    </q-item>
                  </q-popup-proxy>
                </q-btn>
                <q-btn v-if="checkMensajesDefault" round flat icon="message" class="send_btn">
                  <q-tooltip>Mensajes predeterminados</q-tooltip>
                  <q-popup-proxy>
                    <q-item
                      v-if="idMediacion && fechasMediacionMensaje"
                      clickable
                      style="align-items: center; justify-content: center"
                      @click="inputMessage = fechasMediacionMensaje"
                    >
                      Listar Fechas de Mediacion
                    </q-item>
                    <q-separator/>
                    <q-item>
                      <Select
                        :multiple="false"
                        :label="'Buscar Mensaje'"
                        :hint="'Seleccione un Mensaje'"
                        :tooltip="true"
                        :opciones="opcionesMensajes"
                        @seleccion="mensajeSeleccionado"
                      />
                    </q-item>
                  </q-popup-proxy>
                </q-btn>
                <q-btn round flat icon="send" class="send_btn" @click="send()">
                  <q-tooltip>Enviar</q-tooltip>
                </q-btn>
              </template>
            </q-input>

            <q-dialog v-model="template">
              <q-card class="q-pa-sm text-center">
                <span clasS="text-h5 text-weight-bold">
                  Vista Previa
                </span>

                <div class="full-width text-center q-px-sm q-py-lg" id="caja-template">
                </div>

                <div class="q-py-sm" style="width: 50%">
                  <q-input dense class="q-my-sm" v-for="(p, i) in paramsTemplate" :key="p.key"  v-model="p.param" @input="reemplazarParam(p.param, p.key, i)">
                    <template v-slot:prepend>
                      {{p.key}}
                    </template>
                  </q-input>
                </div>

                <div class="full-width row justify-center">
                  <q-btn
                    color="primary"
                    class="q-subheading q-mr-xs"
                    size="sm"
                    style="color:black;"
                    @click="enviarTemplate()"
                  >
                    Enviar Mensaje
                  </q-btn>

                  <q-btn
                    color="negative"
                    class="q-subheading q-ml-xs"
                    size="sm"
                    @click="template = false"
                  >
                    Cancelar
                  </q-btn>
                </div>
              </q-card>
            </q-dialog>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import request from '../request'
import auth from '../auth'
import Select from '../components/Compartidos/Select'
import { Notify, QDialog, QCard } from 'quasar'
export default {
  name: 'Chat',
  props: ['id'],
  components: {
    Select,
    QCard,
    QDialog
  },
  data () {
    return {
      templateSeleccionado: null,
      force: 1,
      template: false,
      IdCaso: 0,
      inputMessage: '',
      loading: false,
      caratula: '',
      telefono: '',
      idChat: 0,
      idMediacion: 0,
      idContacto: 0,
      idExternoChat: 0,
      mensajes: [],
      idUltimoMensaje: 0,
      timerMensajes: '',
      notificaciones: [],
      checkMensajesDefault: false,
      opcionesMensajes: [],
      Multimedia: [],
      fechasMediacionMensaje: '',
      archivo: {
        nombre: '',
        srcImg: ''
      },
      enviarUsuario: true,
      NombreUsuario: '',
      Templates: [],
      paramsTemplate: []
    }
  },
  created () {
    if (!this.$route.query.id) {
      this.$router.push('/GrillaCasos')
      return
    }
    /* Por ahora no hace falta porque se actualizan las notificaciones desde NotificacionesMensajes
    if (this.$route.query.notificaciones) {
      this.notificaciones = this.$route.query.notificaciones
      console.log(this.notificaciones, '---------------------------------------')
      this.actualizarLeidos()
    }
    */

    const usuario = auth.UsuarioLogueado

    this.NombreUsuario = usuario.Apellidos + ', ' + usuario.Nombres

    this.caratula = this.$route.query.caratula
    this.telefono = this.$route.query.telefono
    this.idChat = this.id || this.$route.query.id
    this.idMediacion = this.$route.query.idMediacion ? this.$route.query.idMediacion : null
    this.idContacto = this.$route.query.idContacto ? this.$route.query.idContacto : null

    request.Get(`/estudios/${auth.UsuarioLogueado.IdEstudio}/mensajes-estudio`, {}, r => {
      if (r.length > 0) {
        this.checkMensajesDefault = true

        this.Templates = r

        this.opcionesMensajes = r.map(m => {
          return {
            label: m.Titulo,
            value: m.IdMensajeEstudio,
            tooltip: m.MensajeEstudio
          }
        })

        this.opcionesMensajes.sort((a, b) => {
          const A = a.label.toLowerCase()
          const B = b.label.toLowerCase()

          if (A < B) return -1
          if (A > B) return 1
          return 0
        })
      }
    })
    request.Get(`/mediaciones/fechas`, {}, r => {
      if (r.length > 0) {
        this.fechasMediacionMensaje = 'El estudio tiene las siguientes fechas ocupadas por audiencias de mediación:\n'
        r.forEach((f, i) => {
          this.fechasMediacionMensaje += f.FechaProximaAudiencia.slice(0, -3)
          if (i !== r.length) { this.fechasMediacionMensaje += '\n' }
        })
      }
    })
    if (!this.idMediacion && !this.idContacto) {
      request.Get(`/chats/${this.idChat}`, {}, r => {
        if (r.Error) {
          Notify.create('No se pudieron obtener los datos del chat.')
        } else {
          this.IdCaso = r.IdCaso
        }
      })
    }

    // this.setEnterEvent()
    this.verificarChat()
  },
  mounted () {
    this.$nextTick().then(() => {
      this.goToBottom()
    })

    const target = document.querySelector('div.mensajes_container')

    const config = {
      attributes: true,
      childList: true,
      characterData: true
    }

    const observer = new MutationObserver(mutationList => {
      mutationList.forEach(mutation => {
        if (mutation.addedNodes.length) {
          this.$nextTick().then(() => {
            this.goToBottom()
          })
        }
      })
    })

    observer.observe(target, config)
  },
  methods: {
    tipoArchivo (link) {
      const formato = link.split('.').reverse()[0]
      const mimes = JSON.parse('{"png":["image/png","image/x-png"],"bmp":["image/bmp","image/x-bmp","image/x-bitmap","image/x-xbitmap","image/x-win-bitmap","image/x-windows-bmp","image/ms-bmp","image/x-ms-bmp","application/bmp","application/x-bmp","application/x-win-bitmap"],"gif":["image/gif"],"jpeg":["image/jpeg","image/pjpeg"],"xspf":["application/xspf+xml"],"vlc":["application/videolan"],"wmv":["video/x-ms-wmv","video/x-ms-asf"],"au":["audio/x-au"],"ac3":["audio/ac3"],"flac":["audio/x-flac"],"ogg":["audio/ogg","video/ogg","application/ogg"],"oga":["audio/ogg","video/ogg","application/ogg"],"kmz":["application/vnd.google-earth.kmz"],"kml":["application/vnd.google-earth.kml+xml"],"rtx":["text/richtext"],"rtf":["text/rtf"],"jar":["application/java-archive","application/x-java-application","application/x-jar"],"zip":["application/x-zip","application/zip","application/x-zip-compressed","application/s-compressed","multipart/x-zip"],"7zip":["application/x-compressed"],"xml":["application/xml","text/xml"],"svg":["image/svg+xml"],"3g2":["video/3gpp2"],"3gp":["video/3gp","video/3gpp"],"mp4":["video/mp4"],"m4a":["audio/x-m4a"],"f4v":["video/x-f4v"],"flv":["video/x-flv"],"webm":["video/webm"],"aac":["audio/x-acc"],"m4u":["application/vnd.mpegurl"],"pdf":["application/pdf","application/octet-stream"],"pptx":["application/vnd.openxmlformats-officedocument.presentationml.presentation"],"ppt":["application/powerpoint","application/vnd.ms-powerpoint","application/vnd.ms-office","application/msword"],"docx":["application/vnd.openxmlformats-officedocument.wordprocessingml.document"],"xlsx":["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.ms-excel"],"xl":["application/excel"],"xls":["application/msexcel","application/x-msexcel","application/x-ms-excel","application/x-excel","application/x-dos_ms_excel","application/xls","application/x-xls"],"xsl":["text/xsl"],"mpeg":["video/mpeg"],"mov":["video/quicktime"],"avi":["video/x-msvideo","video/msvideo","video/avi","application/x-troff-msvideo"],"movie":["video/x-sgi-movie"],"log":["text/x-log"],"txt":["text/plain"],"css":["text/css"],"html":["text/html"],"wav":["audio/x-wav","audio/wave","audio/wav"],"xhtml":["application/xhtml+xml"],"tar":["application/x-tar"],"tgz":["application/x-gzip-compressed"],"psd":["application/x-photoshop","image/vnd.adobe.photoshop"],"exe":["application/x-msdownload"],"js":["application/x-javascript"],"mp3":["audio/mpeg","audio/mpg","audio/mpeg3","audio/mp3"],"rar":["application/x-rar","application/rar","application/x-rar-compressed"],"gzip":["application/x-gzip"],"hqx":["application/mac-binhex40","application/mac-binhex","application/x-binhex40","application/x-mac-binhex40"],"cpt":["application/mac-compactpro"],"bin":["application/macbinary","application/mac-binary","application/x-binary","application/x-macbinary"],"oda":["application/oda"],"ai":["application/postscript"],"smil":["application/smil"],"mif":["application/vnd.mif"],"wbxml":["application/wbxml"],"wmlc":["application/wmlc"],"dcr":["application/x-director"],"dvi":["application/x-dvi"],"gtar":["application/x-gtar"],"php":["application/x-httpd-php","application/php","application/x-php","text/php","text/x-php","application/x-httpd-php-source"],"swf":["application/x-shockwave-flash"],"sit":["application/x-stuffit"],"z":["application/x-compress"],"mid":["audio/midi"],"aif":["audio/x-aiff","audio/aiff"],"ram":["audio/x-pn-realaudio"],"rpm":["audio/x-pn-realaudio-plugin"],"ra":["audio/x-realaudio"],"rv":["video/vnd.rn-realvideo"],"jp2":["image/jp2","video/mj2","image/jpx","image/jpm"],"tiff":["image/tiff"],"eml":["message/rfc822"],"pem":["application/x-x509-user-cert","application/x-pem-file"],"p10":["application/x-pkcs10","application/pkcs10"],"p12":["application/x-pkcs12"],"p7a":["application/x-pkcs7-signature"],"p7c":["application/pkcs7-mime","application/x-pkcs7-mime"],"p7r":["application/x-pkcs7-certreqresp"],"p7s":["application/pkcs7-signature"],"crt":["application/x-x509-ca-cert","application/pkix-cert"],"crl":["application/pkix-crl","application/pkcs-crl"],"pgp":["application/pgp"],"gpg":["application/gpg-keys"],"rsa":["application/x-pkcs7"],"ics":["text/calendar"],"zsh":["text/x-scriptzsh"],"cdr":["application/cdr","application/coreldraw","application/x-cdr","application/x-coreldraw","image/cdr","image/x-cdr","zz-application/zz-winassoc-cdr"],"wma":["audio/x-ms-wma"],"vcf":["text/x-vcard"],"srt":["text/srt"],"vtt":["text/vtt"],"ico":["image/x-icon","image/x-ico","image/vnd.microsoft.icon"],"csv":["text/x-comma-separated-values","text/comma-separated-values","application/vnd.msexcel"],"json":["application/json","text/json"]}')

      const Tipo = mimes[formato] ? mimes[formato][0] : false

      if (Tipo) {
        return Tipo.includes('application') ? 'O' : Tipo.substring(0, 1).toUpperCase()
      } else {
        return 'O'
      }
    },
    mensajeSeleccionado (mensaje) {
      this.template = true
      this.paramsTemplate = []

      const m = this.Templates.filter(t => t.IdMensajeEstudio === mensaje.value)[0]
      let crudo = m.MensajeEstudio.replace(/{{/g, `<span class="param-template text-negative text-weight-bold">{{`).replace(/}}/g, '}}</span>')

      let i = 1

      while (m.MensajeEstudio.includes(`{{${i}}}`)) {
        this.paramsTemplate.push({
          key: `{{${i}}}`,
          param: ''
        })

        i++
      }

      this.templateSeleccionado = m

      this.$nextTick().then(() => {
        document.getElementById('caja-template').innerHTML = crudo
      })
    },
    reemplazarParam (p, key, i) {
      const params = document.querySelectorAll('.param-template')

      for (let index = 0; index < params.length; index++) {
        const element = params[index]

        if (element.innerHTML === key) {
          element.classList.add('param-' + i)

          this.$nextTick().then(() => {
            element.innerHTML = p || key
          })
        } else if (element.classList.contains('param-' + i)) {
          this.$nextTick().then(() => {
            element.innerHTML = p || key
          })
        }
      }
    },
    enviarTemplate () {
      let vacio = false

      this.paramsTemplate.forEach(p => {
        if (!p.param) vacio = true
      })

      if (vacio) {
        Notify.create('Debe completar todos los parametros de la plantilla')
        return
      }

      const Contenido = document.getElementById('caja-template').textContent

      const Objeto = {
        template: this.templateSeleccionado.NombreTemplate,
        language: {
          policy: 'deterministic',
          code: 'es'
        },
        namespace: this.templateSeleccionado.NameSpace
      }

      if (this.paramsTemplate.length !== 0) {
        const body = {}
        body.type = 'body'
        body.parameters = this.paramsTemplate.map(p => {
          return {
            type: 'text',
            text: p.param
          }
        })

        Objeto.params = [body]
      }

      const mensajeTemporal = {
        IdUsuario: true,
        Contenido,
        FechaEnviado: this.currentDateTime()
      }

      this.mensajes.push(mensajeTemporal)

      const mensajePost = {
        IdChat: this.idChat,
        Contenido,
        Objeto,
        mediador: this.idMediacion,
        contacto: this.idContacto
      }
      this.template = false

      request.Post(`/mensajes/enviar-template`, mensajePost, r => {
        if (!r.Error) {
          console.log('Mensaje enviado correctamente!')
          this.idUltimoMensaje = r.IdMensaje
          request.Post(`/chats/${this.idChat}/actualizar`, { IdUltimoLeido: this.idUltimoMensaje, mediador: this.idMediacion, contacto: this.idContacto }, p => {
            if (!p.Error) {
              console.log('UltimoMensajeLeido actualizado correctamente.')
            } else {
              Notify.create(p.Error)
            }
          })
        } else {
          Notify.create(r.Error)
        }
      })
    },
    verificarChat () {
      this.$nextTick().then(() => {
        this.goToBottom()
      })
      if (sessionStorage.getItem(this.idChat)) {
        // El chat esta guardado en sessionStorage
        let mensajesSesion = JSON.parse(sessionStorage.getItem(this.idChat))
        this.mensajes = mensajesSesion
        this.idUltimoMensaje = mensajesSesion[mensajesSesion.length - 1].IdMensaje
        this.$nextTick().then(() => {
          this.goToBottom()
        })
      } else {
        // Buscamos los mensajes desde backend
        request.Get(`/mensajes/${this.idChat}`, { IdUltimoMensaje: null, mediador: this.idMediacion, contacto: this.idContacto }, r => {
          if (!r.Error) {
            if (r.length) {
              r.reverse()
              this.mensajes = r
              if (this.IdCaso) { sessionStorage.setItem(this.idChat, JSON.stringify(r)) }
              this.idUltimoMensaje = r[r.length - 1].IdMensaje
              this.$nextTick().then(() => {
                this.goToBottom()
              })
            } else {
              console.log('Primera respuesta vacía')
              this.goToBottom()
            }
          } else {
            Notify.create(r.Error)
          }
        })
      }

      if (localStorage.intervalChat) {
        clearInterval(localStorage.intervalChat)
      }
      localStorage.intervalChat = setInterval(this.buscarMensajes, 2000)
    },
    buscarMensajes () {
      if (!'/Chat/Caso'.includes(this.$route.path.slice(0, 5))) {
        clearInterval(localStorage.intervalChat)
        return
      }
      request.Get(`/mensajes/${this.idChat}`, { IdUltimoMensaje: null, Limit: 100, mediador: this.idMediacion, contacto: this.idContacto }, r => {
        if (!r.Error) {
          console.log('Request de mensajes exitoso.', this.idUltimoMensaje)
          if (r.length) {
            r.reverse()
            console.log('Respuesta contiene mensajes...')
            let mensajesSesion = sessionStorage.getItem(this.idChat) ? JSON.parse(sessionStorage.getItem(this.idChat)) : []
            r.forEach(mensaje => {
              mensajesSesion.push(mensaje)
            })
            this.mensajes = r
            try {
              sessionStorage.setItem(this.idChat, JSON.stringify(mensajesSesion))
            } catch {}
            this.idUltimoMensaje = r[r.length - 1].IdMensaje
            request.Post(`/chats/${this.idChat}/actualizar`, { IdUltimoLeido: this.idUltimoMensaje, mediador: this.idMediacion, contacto: this.idContacto }, p => {
              if (!p.Error) {
                console.log('UltimoMensajeLeido actualizado correctamente.')
              } else {
                Notify.create(p.Error)
              }
            })
          } else {
            console.log('Respuesta vacía')
          }
        } else {
          console.log(r.Error)
          console.log('Error en el loop.')
        }
      })
    },
    stamp (mensaje) {
      return mensaje.FechaEnviado + (mensaje.IdUsuario ? ` <i aria-hidden='true' role='presentation' class='${mensaje.FechaVisto && 'text-primary'} q-icon q-mr-sm absolute notranslate material-icons' style='font-size: 18px; right: 0;'>${mensaje.FechaRecibido ? 'done_all' : 'done'}</i>` : '')
    },
    send () {
      if (this.inputMessage !== '' || this.Multimedia.length > 0) {
        this.inputMessage = this.inputMessage.replace(/\r?\n/g, '<br>')

        if (this.enviarUsuario) {
          this.inputMessage = `${this.inputMessage}<br>- Enviado por ${this.NombreUsuario}`
        }

        const mensajeTemporal = {
          IdUsuario: true,
          Contenido: this.inputMessage ? this.inputMessage : 'Archivo enviado',
          FechaEnviado: this.currentDateTime()
        }

        if (this.Multimedia.length > 0) {
          this.mensajes.push({
            IdUsuario: true,
            Contenido: `https://io.docdoc.com.ar/api/multimedia?file=${this.Multimedia[0].URL}`,
            FechaEnviado: this.currentDateTime()
          })
        }

        this.mensajes.push(mensajeTemporal)

        /* let mensajesSesion = sessionStorage.getItem(this.idChat) ? JSON.parse(sessionStorage.getItem(this.idChat)) : []
        mensajesSesion.push(mensajeTemporal)
        sessionStorage.setItem(this.idChat, JSON.stringify(mensajesSesion)) */

        const mensajePost = {
          IdChat: this.idChat,
          Contenido: this.inputMessage,
          Multimedia: JSON.stringify(this.Multimedia),
          IdCaso: this.IdCaso,
          mediador: this.idMediacion,
          contacto: this.idContacto
        }
        this.inputMessage = ''
        request.Post(`/mensajes/enviar`, mensajePost, r => {
          if (!r.Error) {
            console.log('Mensaje enviado correctamente!')
            this.idUltimoMensaje = r.IdMensaje
            this.Multimedia.splice(0)
            request.Post(`/chats/${this.idChat}/actualizar`, { IdUltimoLeido: this.idUltimoMensaje, mediador: this.idMediacion, contacto: this.idContacto }, p => {
              if (!p.Error) {
                console.log('UltimoMensajeLeido actualizado correctamente.')
              } else {
                Notify.create(p.Error)
              }
            })
          } else {
            Notify.create(r.Error)
          }
        })
      }
    },
    /* Por ahora no hace falta porque se actualizan las notificaciones desde NotificacionesMensajes
    actualizarLeidos () {
      this.notificaciones.forEach(item => {
        request.Post(`/chats/${item.IdChat}/actualizar`, { IdUltimoLeido: item.IdMensaje }, p => {
          console.log('RESPUESTA:', p)
        })
      })
    },
    */
    currentDateTime () {
      var now = new Date()
      const date = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`
      const hour = `${now.getHours()}:${now.getMinutes()}`
      return `${date} - ${hour}`
    },
    goToBottom () {
      var element = document.getElementById('scrollDiv')
      element.scrollTop = element.scrollHeight - element.clientHeight
    },
    setEnterEvent () {
      document.body.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault()
          this.send()
        }
      })
    },
    esArchivo (mensaje) {
      const a = mensaje.includes('wasabisys') || mensaje.includes('https://io.docdoc.com.ar/api/multimedia?file=')

      if (a) {
        return true
      } else {
        return false
      }
    },
    upload () {
      this.$refs.uploader.pickFiles()
    },
    factoryFn () {
      return {
        url: 'https://io.docdoc.com.ar/api/multimedia',
        method: 'POST',
        headers: [
          { name: 'Authorization', value: `Bearer ${auth.Token}` }
        ]
      }
    },
    uploadedFile ({ files, xhr }) {
      this.archivo.nombre = files[0].name
      this.archivo.srcImg = files[0].__img ? files[0].__img.currentSrc : ''
      const data = JSON.parse(xhr.response)
      for (let i = 0; i < files.length; i++) {
        const Tipo = files[i].type

        const formatosDoc = ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'odt', 'pdf']
        const formato = data.Urls[0].split('.').reverse()[0].toLowerCase()

        this.Multimedia.push({
          URL: data.Urls[0],
          Nombre: data.Names[0],
          Tipo: Tipo.includes('application') ? 'O' : Tipo.substring(0, 1).toUpperCase(),
          OrigenMultimedia: formatosDoc.includes(formato) ? 'D' : 'R'
        })
      }
    },
    removedFile (files) {
      for (let i = 0; i < files.length; i++) {
        const file = files[i]
        const r = JSON.parse(file.xhr.response)
        for (let j = 0; j < this.Multimedia.length; j++) {
          if (this.Multimedia[j].Url === r.Urls[0]) {
            this.Multimedia.splice(j, 1)
          }
        }
      }
    },
    abrirCaso () {
      if (this.IdCaso) {
        this.$router.push({
          name: 'Caso',
          query: {
            id: this.IdCaso
          }
        })
      }
    }
  }
}
</script>

<style scoped>
.img--multimedia {
  height: auto;
  width: auto;
  max-width: 320px;
  max-height: 240px;
}

.img--multimedia--enviado {
  height: auto;
  width: auto;
  max-width: 280px;
  max-height: 299px;
}

.full_container {
  height: 90vh;
  padding-top: 10px;
  width: 100vw;
}
.chat_container {
  background-color: #f1f2f6;
  width: 60%;
  height: 100%;
  max-height: 100%;
  margin: 0 auto;
  border-radius: 10px;
}

/** Secciones */
/** header */
.header_container {
  background-color:#ced6e0;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px 10px 0 0;
  font-size: 1.3em;
}

/** mensajes */
.mensajes_container {
  height: calc(100% - 145px);
  overflow-x: hidden;
  overflow-y: auto;
  padding: 10px 100px;
}

/** input */
.input_container {
  height: auto;
  border-radius: 0 0 10px 10px;
  background-color: #f1f2f6;
}

.send_btn {
  transition: 0.2s all ease-in-out;
}
.send_btn:hover {
  color:dodgerblue;
}

@media screen and (max-width: 600px) {
  .full_container {
    padding: 5px;
  }
  .mensajes_container {
    padding: 5px 10px;
  }
  .chat_container {
    width: 100%;
  }
}

/** Scroll bar */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
  background: #888;
}
::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
