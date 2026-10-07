# ──────────────────────────────────────────────────────────────────────────────
# 🔌 Port Forwarding - local access to cluster services
#
# Usage: make pf-<service>
# Docs:  docs/infrastructure/PORT_FORWARDING.md
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: pf-grafana pf-prometheus pf-alertmanager pf-minio pf-rabbitmq \
        pf-postgresql pf-jenkins pf-atlantis pf-argocd pf-ollama pf-chromadb

## Grafana UI → http://localhost:3000  (GRAFANA_USER / GRAFANA_PASSWORD)
pf-grafana:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/monitoring-grafana -n monitoring 3000:80

## Prometheus UI → http://localhost:9090
pf-prometheus:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/monitoring-kube-prometheus-prometheus -n monitoring 9090:9090

## Alertmanager UI → http://localhost:9093
pf-alertmanager:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/monitoring-kube-prometheus-alertmanager -n monitoring 9093:9093

## MinIO Console → http://localhost:9091  (MINIO_ROOT_USER / MINIO_ROOT_PASSWORD)
pf-minio:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/levelup-store-minio-console -n levelup-store 9091:9090

## RabbitMQ Management UI → http://localhost:15672  (RABBITMQ_USER / RABBITMQ_PASS)
pf-rabbitmq:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/levelup-store-rabbitmq -n levelup-store 15672:15672

## PostgreSQL → localhost:5432  (DB_USERNAME / DB_PASSWORD)
pf-postgresql:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/levelup-store-postgresql -n levelup-store 5432:5432

## Jenkins UI → http://localhost:8080  (admin / K8s secret)
pf-jenkins:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward pod/jenkins-0 -n jenkins 8080:8080

## Atlantis UI → http://localhost:4141
pf-atlantis:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/atlantis -n atlantis 4141:80

## ArgoCD UI → http://localhost:8888  (admin / ARGOCD_PASSWORD)
pf-argocd:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/argocd-server -n argocd 8888:80

## Ollama API → http://localhost:11434
pf-ollama:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/ollama -n levelup-store 11434:11434

## ChromaDB API → http://localhost:8000
pf-chromadb:
	KUBECONFIG=$(KUBECONFIG) kubectl port-forward svc/chromadb -n levelup-store-assistant 8000:8000
