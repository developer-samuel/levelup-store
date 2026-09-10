data "oci_core_images" "ubuntu" {
  compartment_id           = var.compartment_ocid
  operating_system         = "Canonical Ubuntu"
  operating_system_version = "24.04"
  shape                    = var.vm_shape
  sort_by                  = "TIMECREATED"
  sort_order               = "DESC"
}

data "oci_identity_availability_domains" "main" {
  compartment_id = var.tenancy_ocid
}

resource "oci_core_instance" "main" {
  compartment_id      = var.compartment_ocid
  availability_domain = data.oci_identity_availability_domains.main.availability_domains[0].name
  display_name        = "levelup-store"
  shape               = var.vm_shape

  shape_config {
    ocpus         = var.vm_ocpus
    memory_in_gbs = var.vm_memory_gb
  }

  source_details {
    source_type = "image"
    source_id   = data.oci_core_images.ubuntu.images[0].id
  }

  create_vnic_details {
    subnet_id        = oci_core_subnet.main.id
    assign_public_ip = true
  }

  metadata = {
    ssh_authorized_keys = var.ssh_public_key
  }
}

# ─── Block Volume - persistent k3s storage ────────────────────────────────────
# Separate disk independent of the VM - survives terraform destroy + VM rebuild.
# OCI Always Free: 200GB total across all block volumes.

resource "oci_core_volume" "k3s_data" {
  compartment_id      = var.compartment_ocid
  availability_domain = data.oci_identity_availability_domains.main.availability_domains[0].name
  display_name        = "levelup-store-k3s-data"
  size_in_gbs         = 200

  lifecycle {
    prevent_destroy = false  # Guards against accidental terraform destroy - contains prod data
  }
}

resource "oci_core_volume_attachment" "k3s_data" {
  attachment_type = "paravirtualized"
  instance_id     = oci_core_instance.main.id
  volume_id       = oci_core_volume.k3s_data.id
  display_name    = "levelup-store-k3s-data-attachment"
}
