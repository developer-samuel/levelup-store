# proxied = true → traffic goes through Cloudflare (WAF + DDoS + CDN)
# Real Oracle IP is hidden from the internet

# Main app domain
resource "cloudflare_record" "app" {
  zone_id = var.cloudflare_zone_id
  name    = var.app_domain
  content = oci_core_instance.main.public_ip
  type    = "A"
  proxied = true
}

# Wildcard for all services running on the same server
resource "cloudflare_record" "wildcard" {
  zone_id = var.cloudflare_zone_id
  name    = "*.${var.app_domain}"
  content = oci_core_instance.main.public_ip
  type    = "A"
  proxied = true
}
