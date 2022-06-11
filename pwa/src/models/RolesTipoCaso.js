import request from '../request'

const RolesTipoCaso = {
  Props: {
    IdRTC: ['int'],
    IdTipoCaso: ['int'],
    Rol: ['trim', 'max 100'],
    Parametros: ['object']
  },
  Scenarios: {
    Dame: ['IdRTC']
  },
  Dame (callback) {
    request.Get('/roles-tipo-caso/', {id: this.IdRTC}, r => {
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

export default RolesTipoCaso
