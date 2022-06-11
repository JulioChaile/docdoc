import lang from '../lang'
import auth from '../auth'
import axios from 'axios'
import { Notify } from 'quasar'

const API_URL = 'https://io.docdoc.com.ar/api'

const getHeaders = () => {
  const headers = {}
  if (auth.Token) {
    headers['Authorization'] = 'Bearer ' + auth.Token
  }
  return headers
}

const handleError = (callback) => {
  return (err) => {
    let error
    if (err.response) {
      const Error = err.response.data.Error || err.response.data.message || lang.error.request
      error = { Error }

      // Venció la sesión -> Deslogueo
      if (err.response.status === 401) {
        auth.logout()
        if (!window.location.href.includes('Login')) {
          window.location = '/#/Login'
          Notify.create(error.Error)
        }
        return
      }
    } else {
      console.log(err)
      error = { Error: lang.error.request }
    }
    callback(error)
  }
}

const generateQuery = (params) => {
  let query = ''
  let keyParams = Object.keys(params)
  let idIndex = keyParams.indexOf('id')
  if (idIndex !== -1) {
    query = `${params['id']}`
    keyParams.splice(idIndex, 1)
  }
  for (let i = 0; i < keyParams.length; i++) {
    if (i === 0) {
      query += `?${keyParams[i]}=${params[keyParams[i]]}`
    } else {
      query += `&${keyParams[i]}=${params[keyParams[i]]}`
    }
  }
  return query
}

export const getAlike = (method, url, params, callback, cachear) => {
  if (params.id) {
    url = url.endsWith('/') ? url : (url + '/')
  }
  const query = API_URL + url + generateQuery(params)
  method(query, {headers: getHeaders()})
    .then(({ data }) => {
      callback(data)
    })
    .catch(handleError(callback))
}

let cancelGet = null
export const uniqGet = (url, params, callback) => {
  if (cancelGet) {
    cancelGet()
    cancelGet = null
  }
  if (params.id) {
    url = url.endsWith('/') ? url : (url + '/')
  }
  axios.get(API_URL + url + generateQuery(params), {
    headers: getHeaders(),
    cancelToken: new axios.CancelToken(function executor (c) {
      cancelGet = c
    })
  })
    .then(({ data }) => {
      callback(data)
    })
    .catch(handleError(callback))
}

export const postAlike = (method, url, params, callback) => {
  method(API_URL + url, params, {headers: getHeaders()})
    .then(({ data }) => {
      callback(data)
    })
    .catch(handleError(callback))
}
