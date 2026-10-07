# Port Forwarding - Local Access to Cluster Services

Services run inside the k3s cluster and are not exposed locally by default.
Use `make pf-<service>` from the `infrastructure/` directory to forward a port to your machine.

```bash
cd infrastructure
make pf-grafana
```

---

## Available Services

| Command                | URL / Address          | Credentials                                                |
| ---------------------- | ---------------------- | ---------------------------------------------------------- |
| `make pf-grafana`      | http://localhost:3000  | `GRAFANA_USER` / `GRAFANA_PASSWORD` from `.env.production` |
| `make pf-prometheus`   | http://localhost:9090  |                                                            |
| `make pf-alertmanager` | http://localhost:9093  |                                                            |
| `make pf-minio`        | http://localhost:9091  | `MINIO_ROOT_USER` / `MINIO_ROOT_PASSWORD`                  |
| `make pf-rabbitmq`     | http://localhost:15672 | `RABBITMQ_USER` / `RABBITMQ_PASS`                          |
| `make pf-postgresql`   | `localhost:5432`       | `DB_USERNAME` / `DB_PASSWORD`                              |
| `make pf-jenkins`      | http://localhost:8080  | `admin` / see `.env.production.example` Jenkins section    |
| `make pf-atlantis`     | http://localhost:4141  |                                                            |
| `make pf-argocd`       | http://localhost:8888  | `admin` / `ARGOCD_PASSWORD`                                |
| `make pf-ollama`       | http://localhost:11434 | API only                                                   |
| `make pf-chromadb`     | http://localhost:8000  | API only                                                   |

Most credentials are in `.env.production`. Jenkins password is in a K8s secret - see `.env.production.example` Jenkins section for the command.

---

## Notes

- Port forwarding stays active until you press `Ctrl+C`.
- Only one forward per local port at a time - if a port is already in use, change the local port: `kubectl port-forward svc/... 3001:80`.
- KEDA, Kyverno, cert-manager and similar controllers have no web UI and are managed through ArgoCD or `kubectl`.

---

## Related docs

- [Infrastructure Overview](OVERVIEW.md)
- [Setup & Prerequisites](SETUP.md)
- [Environment Variables & Secrets](SECRETS.md)
- [Deployment Guide](DEPLOYMENT.md)
- [Port Forwarding](PORT_FORWARDING.md)
- [Makefile Command Reference](COMMANDS.md)
