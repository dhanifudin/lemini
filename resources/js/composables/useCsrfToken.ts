export function useCsrfToken() {
  const meta = document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null
  const token = meta?.content ?? ''
  return { csrfToken: token }
}
