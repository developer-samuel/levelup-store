import axios from 'axios'

/** Fetches raw HTML content for a product page via an XHR-flagged GET request */
export async function fetchProductHtml(url: string): Promise<string> {
  const response = await axios.get<string>(url, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
  })

  return response.data
}
