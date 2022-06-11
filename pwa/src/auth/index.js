import request from '../request'
import { Notify } from 'quasar'

const DocdocToken = 'DOCDOCTOKENPWA'
const DebeCambiarPass = 'DOCDOCDEBECAMBIARPASS'
const UsuarioLogueado = 'DOCDOCUSUARIOLOGUEADO'

export default {
  callbacksLogin: [],
  get Token () {
    return sessionStorage.getItem(DocdocToken)
  },
  get DebeCambiarPass () {
    return sessionStorage.getItem(DebeCambiarPass)
  },
  get UsuarioLogueado () {
    try {
      return JSON.parse(sessionStorage.getItem(UsuarioLogueado))
    } catch (err) {
      return {}
    }
  },
  get isLoggedIn () {
    return !!(this.Token && this.DebeCambiarPass === 'N')
  },
  addCallbackLogin (callback) {
    this.callbacksLogin.push(callback)
  },
  login (user, callback) {
    request.Post('/login', {Usuario: user.Usuario, Password: user.Password}, r => {
      if (r.Error) {
        Notify.create(r.Error)
        callback()
      } else {
        let u = r.Mensaje.Usuario
        sessionStorage.setItem(DocdocToken, u.Token)
        if (u.DebeCambiarPass === 'S') {
          sessionStorage.setItem(DebeCambiarPass, u.DebeCambiarPass)
        } else {
          sessionStorage.setItem(DebeCambiarPass, 'N')
          sessionStorage.setItem(UsuarioLogueado, JSON.stringify(u))
        }
        callback(u.DebeCambiarPass)
      }
      this.callbacksLogin.forEach(c => c())
    })
  },
  cambiarPass (oldPass, newPass, callback) {
    request.Post('/usuarios/cambiar-password', {
      Token: sessionStorage.getItem(DocdocToken),
      OldPass: oldPass,
      NewPass: newPass
    }, r => {
      let redirect = false
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        redirect = true
      }
      callback(redirect)
    })
  },
  verificarUsuario (Usuario, callback) {
    request.Post('/usuarios/existe', {Usuario}, r => {
      if (r.Error) {
        Notify.create(r.Error)
      } else if (r.DebeCambiarPass === 'S') {
        sessionStorage.setItem(DebeCambiarPass, r.DebeCambiarPass)
      } else {
        sessionStorage.setItem(DebeCambiarPass, 'N')
      }
      callback(r.DebeCambiarPass)
    })
  },
  verificarCodigo (Usuario, Codigo, callback) {
    request.Post('/usuarios/validar-codigo', {Usuario, Codigo}, r => {
      let result = false
      if (r.Error) {
        Notify.create(r.Error)
      } else {
        let u = r.Mensaje.Usuario
        sessionStorage.setItem(DocdocToken, u.Token)
        result = true
      }
      callback(result)
    })
  },
  logout () {
    sessionStorage.removeItem(DocdocToken)
    sessionStorage.removeItem(DebeCambiarPass)
    sessionStorage.removeItem(UsuarioLogueado)
    this.callbacksLogin.forEach(c => c())
  }
}
