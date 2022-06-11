<template>
  <div>
    <q-splitter
      v-model="splitterModel"
      style="height: 90vh"
    >
      <template v-slot:before>
        <div v-if="loadingChat" class="full-width row justify-center">
          <Loading />
        </div>

        <div v-else class="full-width">
          <q-item
            :class="'rounded-borders item-chat cursor-pointer q-my-xs q-mx-xs ' + (c.IdChatApi === chatAbierto && 'bg-grey')"
            style="border: solid 1px; height: 25px !important"
            sparse
            v-for="c in chats"
            :key="c.IdChatApi"
            clickable
            @click="abrirChat(c)"
          >
            <q-item-section class="flex-column relative-position align-center text-bold q-pt-lg">
              <q-badge class="absolute" v-if="c.Cant > 0" color="red" text-color="white">
                {{c.Cant}}
              </q-badge>

              <div class="q-ml-lg">{{ c.IdChatApi.split('@')[0] }}</div>
            </q-item-section>
          </q-item>
          <q-separator />
        </div>
      </template>

      <template v-slot:after>
        <div v-if="loadingMensajes" class="full-width row justify-center">
          <Loading />
        </div>

        <div
          class="chat_container q-px-lg q-pt-lg"
          style="width: 95% !important; background-color: inherit;"
        >
          <!-- Mensajes -->
          <div class="mensajes_container q-pr-lg" style="height: 70vh !important; overflow-y: scroll" id="scrollDiv">
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
                <img style="max-width: 350px; max-height: 400px" v-else-if="tipoArchivo(mensaje.Contenido) === 'I'" class="img--multimedia" :src="mensaje.Contenido">
                <span v-else>Formato Desconocido</span>
                <br>
                <a class="self-bottom" :href="mensaje.Contenido" :download="`Archivo`" target="_blank">
                  Descargar
                </a>
              </template>
            </q-chat-message>
          </div>

          <!-- Input -->
          <div
            class="input_container"
            style="background-color: inherit"
          >
            <div class="row">
              <q-checkbox
                v-model="enviarUsuario"
                label="Mostrar nombre en el mensaje"
                class="q-pr-md q-pl-lg"
              />
              <div class="col-12">
                <q-input v-model="inputMessage" class="q-pr-md q-pl-lg" filled type="textarea" rows="1" placeholder="Escriba su mensaje aqui...">
                  <template v-slot:after>
                    <q-btn v-if="checkMensajesDefault" round flat icon="message" class="send_btn">
                      <q-tooltip>Mensajes predeterminados</q-tooltip>
                      <q-popup-proxy>
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
                    <q-btn round flat icon="send" class="send_btn" @click="send">
                      <q-tooltip>Enviar</q-tooltip>
                    </q-btn>
                  </template>
                </q-input>
              </div>
            </div>
          </div>
        </div>
      </template>
    </q-splitter>
  </div>
</template>

<script>
import Loading from '../components/Loading'
import { Notify, QSplitter, QSeparator } from 'quasar'
import request from '../request'
import Select from '../components/Compartidos/Select'
import auth from '../auth'

export default {
  components: { QSplitter, Loading, QSeparator, Select },
  data () {
    return {
      splitterModel: 15,
      chats: [],
      opcionesMensajes: [],
      chatAbierto: null,
      mensajes: [],
      loadingChat: true,
      idInterval: null,
      loadingMensajes: true,
      enviarUsuario: true,
      inputMessage: '',
      checkMensajesDefault: false
    }
  },
  created () {
    const usuario = auth.UsuarioLogueado

    this.NombreUsuario = usuario.Apellidos + ', ' + usuario.Nombres

    request.Get('/mensajes/listar-chats-externo', {}, r => {
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        request.Get('/mensajes/nuevos-mensajes-externo', {}, t => {
          if (t.Error) {
            Notify.create(t.Error)
          } else {
            r.forEach(m => {
              const i = t.findIndex(n => n.IdChatApi === m.IdChatApi)
              const msj = i < 0 ? 0 : t[i].MensajesSinLeer

              this.chats.push({
                IdChatApi: m.IdChatApi,
                Cant: msj
              })
            })

            this.chatAbierto = this.chats[0].IdChatApi
            this.chats[0].Cant = 0
            this.loadingChat = false

            this.buscarMensajes()
          }
        })
      }
    })

    request.Get(`/estudios/5/mensajes-estudio`, {}, r => {
      if (r.length > 0) {
        this.checkMensajesDefault = true

        this.opcionesMensajes = r.map(m => {
          return {
            label: m.Titulo,
            value: m.MensajeEstudio,
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
  },
  mounted () {
    this.goToBottom()

    const target = document.querySelector('div.mensajes_container')

    const config = {
      attributes: true,
      childList: true,
      characterData: true
    }

    const observer = new MutationObserver(mutationList => {
      mutationList.forEach(mutation => {
        if (mutation.addedNodes.length) {
          this.goToBottom()
        }
      })
    })

    observer.observe(target, config)
  },
  methods: {
    buscarMensajes () {
      clearInterval(this.idInterval)
      this.idInterval = setInterval(() => {
        if (!'/Whatsapp'.includes(this.$route.path.slice(0, 5))) {
          clearInterval(this.idInterval)
          return
        }
        request.Get(`/mensajes/listar-mensajes-externo`, { IdChatApi: this.chatAbierto }, m => {
          this.loadingMensajes = false
          this.mensajes = m.reverse()
        })
        request.Get('/mensajes/nuevos-mensajes-externo', {}, r => {
          if (r.Error) {
            Notify.create(r.Error)
          } else {
            r.forEach(m => {
              const i = this.chats.findIndex(c => c.IdChatApi === m.IdChatApi)

              if (i < 0) {
                this.chats.push({
                  IdChatApi: m.IdChatApi,
                  Cant: m.Cant
                })
              } else {
                this.chats[i].Cant = m.MensajesSinLeer
              }
            })
          }
        })
      }, 5000)
    },
    abrirChat (c) {
      this.chatAbierto = c.IdChatApi
      c.Cant = 0
      this.loadingMensajes = true
      this.mensajes = []

      this.buscarMensajes()
    },
    stamp (mensaje) {
      return mensaje.FechaEnviado + (mensaje.IdUsuario ? ` <i aria-hidden='true' role='presentation' class='${mensaje.FechaVisto && 'text-primary'} q-icon q-mr-sm absolute notranslate material-icons' style='font-size: 18px; right: 0;'>${mensaje.FechaRecibido ? 'done_all' : 'done'}</i>` : '')
    },
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
    esArchivo (mensaje) {
      const a = mensaje.includes('wasabisys') || mensaje.includes('https://io.docdoc.com.ar/api/multimedia?file=')

      if (a) {
        return true
      } else {
        return false
      }
    },
    mensajeSeleccionado (mensaje) {
      this.inputMessage = mensaje.value
    },
    goToBottom () {
      var element = document.getElementById('scrollDiv')
      element.scrollTop = element.scrollHeight - element.clientHeight
    },
    currentDateTime () {
      var now = new Date()
      const date = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`
      const hour = `${now.getHours()}:${now.getMinutes()}`
      return `${date} - ${hour}`
    },
    send () {
      if (this.inputMessage !== '') {
        this.inputMessage = this.inputMessage.replace(/\r?\n/g, '<br>')

        if (this.enviarUsuario) {
          this.inputMessage = `${this.inputMessage}<br>- Enviado por ${this.NombreUsuario}`
        }

        const mensajeTemporal = {
          IdUsuario: true,
          Contenido: this.inputMessage ? this.inputMessage : 'Archivo enviado',
          FechaEnviado: this.currentDateTime()
        }

        this.mensajes.push(mensajeTemporal)

        const mensajePost = {
          IdChatApi: this.chatAbierto,
          Contenido: this.inputMessage
        }
        this.inputMessage = ''
        request.Post(`/mensajes/enviar-externo`, mensajePost, r => {
          if (!r.Error) {
            console.log('Mensaje enviado correctamente!')
          } else {
            Notify.create(r.Error)
          }
        })
      }
    }
  }
}
</script>

<style>
  .item-chat:hover {
    background-color: grey;
  }
</style>
