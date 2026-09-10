output "public_ip" {
  description = "Public IP of the VM - use this in Ansible inventory"
  value       = oci_core_instance.main.public_ip
}

output "instance_id" {
  description = "OCID of the compute instance"
  value       = oci_core_instance.main.id
}

output "block_volume_id" {
  description = "OCID of the k3s data block volume - do not delete on terraform destroy!"
  value       = oci_core_volume.k3s_data.id
}

output "velero_bucket_name" {
  description = "OCI Object Storage bucket for Velero backups"
  value       = oci_objectstorage_bucket.velero.name
}

output "velero_s3_endpoint" {
  description = "S3-compatible endpoint for Velero - use in argocd app set"
  value       = "https://${data.oci_objectstorage_namespace.main.namespace}.compat.objectstorage.${var.region}.oraclecloud.com"
}
