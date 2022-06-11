import request from '../request'

const Usuarios = {
  Props: {
    IdUsuario: ['int'],
    IdRol: ['int'],
    Nombres: ['max 30'],
    Apellidos: ['max 30'],
    Usuario: ['max 120'],
    Password: ['max 255'],
    Token: ['max 500'],
    Email: ['email', 'max 120'],
    IntentosPass: ['int'],
    FechaUltIntento: ['datetime'],
    FechaAlta: ['datetime'],
    DebeCambiarPass: ['max 1', 'min 1'],
    Estado: ['max 1', 'min 1'],
    Observaciones: ['max 255']
  },
  Scenarios: {
    Login: ['Usuario', 'Password']
  },
  Dame (callback) {
    request.Get('/usuarios', {id: this.IdUsuario}, r => {
      let result = { Error: null }
      if (r.Error) {
        result.Error = r.Error
      } else {
        this.copyProps(r)
      }
      callback(result)
    })
  }
}

export default Usuarios
