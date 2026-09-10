data "oci_objectstorage_namespace" "main" {
  compartment_id = var.compartment_ocid
}

resource "oci_objectstorage_bucket" "velero" {
  compartment_id = var.compartment_ocid
  namespace      = data.oci_objectstorage_namespace.main.namespace
  name           = var.velero_bucket
  access_type    = "NoPublicAccess"
  storage_tier   = "Standard"
}
