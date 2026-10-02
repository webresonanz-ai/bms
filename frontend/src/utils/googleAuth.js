/**
 * Google Identity Services (GIS) loader + One-Tap/Button helper.
 * Docs: https://developers.google.com/identity/gsi/web
 *
 * Usage:
 *   import { renderGoogleButton } from '@/utils/googleAuth'
 *   renderGoogleButton(el, (idToken) => auth.loginWithGoogle(idToken))
 */

const GIS_SRC = 'https://accounts.google.com/gsi/client'

let gisPromise = null

export function getGoogleClientId() {
  return import.meta.env.VITE_GOOGLE_CLIENT_ID || ''
}

export function isGoogleConfigured() {
  return !!getGoogleClientId()
}

function loadGis() {
  if (window.google?.accounts?.id) return Promise.resolve()
  if (gisPromise) return gisPromise

  gisPromise = new Promise((resolve, reject) => {
    const existing = document.querySelector(`script[src="${GIS_SRC}"]`)
    if (existing) {
      existing.addEventListener('load', () => resolve(), { once: true })
      existing.addEventListener('error', () => reject(new Error('Failed to load Google Identity Services.')), { once: true })
      return
    }
    const s = document.createElement('script')
    s.src = GIS_SRC
    s.async = true
    s.defer = true
    s.onload = () => resolve()
    s.onerror = () => reject(new Error('Failed to load Google Identity Services. Check your connection / ad-blocker.'))
    document.head.appendChild(s)
  })

  return gisPromise
}

/**
 * Render the official Google Sign-In button into `el`.
 *
 * @param {HTMLElement} el
 * @param {(idToken: string) => Promise<void>} onCredential
 * @param {{ text?: string }} opts  text: 'signin_with' | 'signup_with' | 'continue_with'
 */
export async function renderGoogleButton(el, onCredential, opts = {}) {
  const clientId = getGoogleClientId()
  if (!clientId) throw new Error('Missing VITE_GOOGLE_CLIENT_ID. Add your Google OAuth Client ID to frontend/.env.')
  if (!el) return

  await loadGis()

  window.google.accounts.id.initialize({
    client_id: clientId,
    callback: (response) => {
      if (response?.credential) onCredential(response.credential)
    },
    auto_select: false,
    cancel_on_tap_outside: true,
  })

  window.google.accounts.id.renderButton(el, {
    type: 'standard',
    theme: 'outline',
    size: 'large',
    width: '100%',
    text: opts.text || 'continue_with',
    shape: 'rectangular',
    logo_alignment: 'left',
  })
}
