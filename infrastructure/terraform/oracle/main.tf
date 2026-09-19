terraform {
  required_providers {
    oci = {
      source  = "oracle/oci"
      version = "~> 6.0"
    }
    cloudflare = {
      source  = "cloudflare/cloudflare"
      version = "~> 4.0"
    }
  }

  required_version = ">= 1.5.0"

  # Backend config is never hardcoded here - it would end up in git.
  # All backend config (bucket, endpoint, keys) goes into secrets/ files (gitignored).
  #
  # Local state  (first-time / no secrets): comment out backend "s3" {} → make tf-init
  # Remote state (OCI Object Storage):      keep backend "s3" {}        → make tf-init-remote
  backend "s3" {}
}

provider "oci" {
  tenancy_ocid     = var.tenancy_ocid
  user_ocid        = var.user_ocid
  fingerprint      = var.fingerprint
  private_key_path = var.private_key_path
  region           = var.region
}

provider "cloudflare" {
  api_token = var.cloudflare_api_token
}
