import axios from 'axios'

// Read CSRF token from meta tag injected by Blade layout
const token = (document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content

if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token
}

// Laravel default XSRF cookie name is XSRF-TOKEN when using Axios auto config
axios.defaults.xsrfCookieName = 'XSRF-TOKEN'
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
axios.defaults.withCredentials = true

// Optional: request interceptor for debugging CSRF in production
axios.interceptors.request.use((config) => {
  if (!config.headers['X-CSRF-TOKEN']) {
    // fallback in case header stripped
    if (token) {
      config.headers['X-CSRF-TOKEN'] = token
    }
  }
  return config
})

// Optional: response interceptor to log 419 errors
axios.interceptors.response.use(
  r => r,
  err => {
    if (err.response && err.response.status === 419) {
      console.warn('Detected 419 (Page Expired). CSRF token mismatch or session missing.', {
        requestUrl: err.config?.url,
        sentToken: err.config?.headers?.['X-CSRF-TOKEN'],
        xsrfHeader: err.config?.headers?.['X-XSRF-TOKEN'],
      })
    }
    return Promise.reject(err)
  }
)
