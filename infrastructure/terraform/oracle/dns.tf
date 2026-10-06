# proxied = true → traffic goes through Cloudflare (WAF + DDoS + CDN)
# Real Oracle IP is hidden from the internet
# DNS records are only created when cloudflare_api_token is set

# Main app domain
resource "cloudflare_record" "app" {
  count   = var.cloudflare_api_token != "" ? 1 : 0
  zone_id = var.cloudflare_zone_id
  name    = var.app_domain
  content = oci_core_instance.main.public_ip
  type    = "A"
  proxied = true
}

# Wildcard for all services running on the same server
resource "cloudflare_record" "wildcard" {
  count   = var.cloudflare_api_token != "" ? 1 : 0
  zone_id = var.cloudflare_zone_id
  name    = "*.${var.app_domain}"
  content = oci_core_instance.main.public_ip
  type    = "A"
  proxied = true
}
