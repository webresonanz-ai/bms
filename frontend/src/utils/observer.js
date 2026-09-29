/**
 * Returns a native IntersectionObserver when the browser supports it,
 * or a harmless no-op stub otherwise (very old browsers, some webviews).
 */
export const getIntersectionObserver = (callback, options) => {
  if (typeof window !== 'undefined' && 'IntersectionObserver' in window) {
    return new window.IntersectionObserver(callback, options)
  }
  return {
    observe() {},
    unobserve() {},
    disconnect() {}
  }
}
