# DevOps

> See also: [Makefile Command Reference](../../COMMANDS.md)


CI/CD, backups, and security tooling.

---

## jenkins

CI/CD server.

| Command                          | Description |
| -------------------------------- | ----------- |
| `jenkins-start/down/stop/unsync` | Lifecycle   |
| `jenkins-status/sync/pods/logs`  | Status      |

> Jenkins UI: `make pf-jenkins` → http://localhost:8080

---

## atlantis

Terraform PR automation - runs `terraform plan/apply` on PR comments.

| Command                           | Description |
| --------------------------------- | ----------- |
| `atlantis-start/down/stop/unsync` | Lifecycle   |
| `atlantis-status/sync/pods/logs`  | Status      |

> Atlantis UI: `make pf-atlantis` → http://localhost:4141

---

## velero

Kubernetes backup to OCI Object Storage.

| Command                         | Description                  |
| ------------------------------- | ---------------------------- |
| `velero-start/down/stop/unsync` | Lifecycle                    |
| `velero-status/sync/pods/logs`  | Status                       |
| `velero-backups`                | List all backups with status |
| `velero-schedules`              | List backup schedules        |

---

## falco

Runtime security monitoring - detects suspicious container behavior.

| Command                        | Description                        |
| ------------------------------ | ---------------------------------- |
| `falco-start/down/stop/unsync` | Lifecycle                          |
| `falco-status/sync/pods/logs`  | Status                             |
| `falco-rules`                  | List loaded Falco rules (first 40) |
