export function Validate (obj, prop, type) {
  let value = obj[prop]
  if (!value) {
    return ''
  }
  let error = ''
  switch (type.trim()) {
    case 'int':
      if (typeof value !== 'number') {
        error = `El campo ${prop} debe ser un número.`
      }
      break
    case 'email':
      let re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
      if (!re.test(String(value).toLowerCase())) {
        error = `El email no es válido.`
      }
      break
    case 'trim':
      value = String(value).trim()
      break
    default:
      if (type.includes('max')) {
        let n = parseInt(type.split(' ')[1])
        if (value.length > n) {
          error = `El campo ${prop} no puede tener más de ${n} caracteres.`
        }
      } else if (type.includes('min')) {
        let n = parseInt(type.split(' ')[1])
        if (value.length < n) {
          error = `El campo ${prop} no puede tener menos de ${n} caracteres.`
        }
      }
  }
  obj[prop] = value
  return error
}
