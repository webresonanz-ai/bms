/**
 * Client-side image compression (canvas-based).
 * Compresses BEFORE upload to save bandwidth; the backend compresses
 * again (GD → max 1600px WebP) to save storage.
 *
 * Usage:
 *   import { compressImage, formatBytes } from '@/utils/imageCompress'
 *   const { blob, width, height } = await compressImage(file)
 */

const API_BASE = import.meta.env.VITE_API_BASE_URL || ''

export const IMAGE_MAX_DIM = 1600
export const IMAGE_QUALITY = 0.82
export const IMAGE_MAX_SIZE = 15 * 1024 * 1024 // must stay <= backend limit

/**
 * Resolve a stored image_url to a displayable src.
 * Relative paths (uploads/…) are served by the backend host;
 * absolute http(s) URLs are returned as-is.
 */
export function resolveUploadSrc(imageUrl) {
  if (!imageUrl) return ''
  if (/^https?:\/\//i.test(imageUrl)) return imageUrl
  return `${String(API_BASE).replace(/\/+$/, '')}/${String(imageUrl).replace(/^\/+/, '')}`
}

export function formatBytes(bytes) {
  const n = Number(bytes) || 0
  if (n < 1024) return `${n} B`
  if (n < 1024 * 1024) return `${(n / 1024).toFixed(0)} KB`
  return `${(n / 1024 / 1024).toFixed(1)} MB`
}

function loadBitmap(file) {
  // Prefer createImageBitmap with EXIF-aware orientation…
  if (typeof createImageBitmap === 'function') {
    return createImageBitmap(file, { imageOrientation: 'fromImage' }).catch(() =>
      createImageBitmap(file),
    )
  }
  // …fallback to <img> for very old browsers
  return new Promise((resolve, reject) => {
    const url = URL.createObjectURL(file)
    const img = new Image()
    img.onload = () => {
      URL.revokeObjectURL(url)
      resolve(img)
    }
    img.onerror = () => {
      URL.revokeObjectURL(url)
      reject(new Error('Could not read image file.'))
    }
    img.src = url
  })
}

function canvasToBlob(canvas, mime, quality) {
  return new Promise((resolve, reject) => {
    canvas.toBlob(
      (blob) => (blob ? resolve(blob) : reject(new Error('Image compression failed.'))),
      mime,
      quality,
    )
  })
}

/**
 * Compress an image file: downscale to max 1600px + re-encode.
 * Returns the smaller of {compressed, original} so storage never grows.
 *
 * @returns {{ blob: Blob, width: number, height: number,
 *            originalSize: number, size: number, mime: string }}
 */
export async function compressImage(
  file,
  { maxDim = IMAGE_MAX_DIM, quality = IMAGE_QUALITY } = {},
) {
  if (!(file instanceof Blob)) throw new Error('No image selected.')
  if (file.size > IMAGE_MAX_SIZE) {
    throw new Error(`Image is too large (max ${formatBytes(IMAGE_MAX_SIZE)}).`)
  }

  const bitmap = await loadBitmap(file)
  const srcW = bitmap.width
  const srcH = bitmap.height
  if (!srcW || !srcH) throw new Error('Could not read image dimensions.')

  const scale = Math.min(1, maxDim / Math.max(srcW, srcH))
  const width = Math.round(srcW * scale)
  const height = Math.round(srcH * scale)

  const canvas = document.createElement('canvas')
  canvas.width = width
  canvas.height = height
  const ctx = canvas.getContext('2d')
  ctx.drawImage(bitmap, 0, 0, width, height)
  if (typeof bitmap.close === 'function') bitmap.close()

  // Try WebP first (smallest), fall back to JPEG
  let blob
  let mime = 'image/webp'
  try {
    blob = await canvasToBlob(canvas, 'image/webp', quality)
    if (!blob || blob.size === 0) throw new Error('webp unsupported')
  } catch {
    mime = 'image/jpeg'
    blob = await canvasToBlob(canvas, 'image/jpeg', quality)
  }

  // Never return something bigger than the original
  if (blob.size >= file.size) {
    return {
      blob: file,
      width: srcW,
      height: srcH,
      originalSize: file.size,
      size: file.size,
      mime: file.type || mime,
      skipped: true,
    }
  }

  return { blob, width, height, originalSize: file.size, size: blob.size, mime, skipped: false }
}
