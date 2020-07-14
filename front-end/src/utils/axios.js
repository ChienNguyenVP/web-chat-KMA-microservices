import axios from 'axios'
import qs from 'querystring'
import Vue from 'vue'
import VueCookie from 'vue-cookie'
import pickBy from 'lodash/pickBy'

Vue.use(VueCookie)

const axiosInstance = axios.create({
  baseURL: 'http://api.webchat.com:8000',
  paramsSerializer: params => qs.stringify(params, { arrayFormat: 'repeat' }),
  timeout: 5000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json'
  }
})

export function setHeaders (params) {
  const newHeaders = {
    ...axiosInstance.defaults.headers.common,
    ...params
  }
  axiosInstance.defaults.headers.common = pickBy(newHeaders, val => !!val)
}

export default axiosInstance
