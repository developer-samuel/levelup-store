# Storage

> See also: [Makefile Command Reference](../../COMMANDS.md)


**Namespace:** `levelup-store`

---

## minio

S3-compatible object storage for files and images.

| Command                        | Description |
|--------------------------------|-------------|
| `minio-start/down/stop/unsync` | Lifecycle   |
| `minio-status/sync/pods/logs`  | Status      |

> MinIO Console: `make pf-minio` → http://localhost:9091

### Initial setup

Run once after MinIO is deployed to populate the bucket from the [levelup-store-uploads](https://github.com/Developer-Samuel/levelup-store-uploads) repository:

| Command              | Description       |
|----------------------|-------------------|
| `minio-seed`         | Production bucket |
| `minio-seed-staging` | Staging bucket    |
