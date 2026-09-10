# ── OCI Authentication ────────────────────────────────────────────────────────

variable "tenancy_ocid" {
  description = "OCID of the tenancy"
  type        = string
}

variable "user_ocid" {
  description = "OCID of the user"
  type        = string
}

variable "fingerprint" {
  description = "Fingerprint of the API key"
  type        = string
}

variable "private_key_path" {
  description = "Path to the OCI API private key"
  type        = string
}

variable "region" {
  description = "OCI region"
  type        = string
}

# ── OCI Resources ─────────────────────────────────────────────────────────────

variable "compartment_ocid" {
  description = "OCID of the compartment"
  type        = string
}

variable "vm_shape" {
  description = "Shape of the compute instance"
  type        = string
}

variable "vm_ocpus" {
  description = "Number of OCPUs - verify current free tier limit at cloud.oracle.com/free"
  type        = number
  default     = 2
}

variable "vm_memory_gb" {
  description = "Memory in GB - verify current free tier limit at cloud.oracle.com/free"
  type        = number
  default     = 12
}

# ── Network ───────────────────────────────────────────────────────────────────

variable "vcn_cidr" {
  description = "CIDR block for VCN"
  type        = string
}

variable "subnet_cidr" {
  description = "CIDR block for subnet"
  type        = string
}

variable "allowed_cidr" {
  description = "List of CIDRs allowed to access SSH (22) and Kubernetes API (6443) - restrict to your IP(s)"
  type        = list(string)
}

# ── SSH ───────────────────────────────────────────────────────────────────────

variable "ssh_public_key" {
  description = "SSH public key for VM access"
  type        = string
}

# ── Storage ───────────────────────────────────────────────────────────────────

variable "velero_bucket" {
  description = "OCI Object Storage bucket name for Velero backups"
  type        = string
}

# ── Cloudflare & DNS ─────────────────────────────────────────────────────────

variable "cloudflare_api_token" {
  description = "Cloudflare API token - create at dash.cloudflare.com → My Profile → API Tokens"
  type        = string
  sensitive   = true
}

variable "cloudflare_zone_id" {
  description = "Cloudflare Zone ID - find at dash.cloudflare.com → your domain → Overview → Zone ID"
  type        = string
}

variable "app_domain" {
  description = "Full app domain or subdomain (e.g. example.com or store.example.com)"
  type        = string
}
