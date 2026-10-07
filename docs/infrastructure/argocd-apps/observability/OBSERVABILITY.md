# Monitoring

> See also: [Makefile Command Reference](../../COMMANDS.md)


Observability stack - metrics, logs, traces.

---

## monitoring (Prometheus + Grafana)

| Command                             | Description |
| ----------------------------------- | ----------- |
| `monitoring-start/down/stop/unsync` | Lifecycle   |
| `monitoring-status/sync/pods/logs`  | Status      |

> Grafana: `make pf-grafana` → http://localhost:3000
> Prometheus: `make pf-prometheus` → http://localhost:9090
> Alertmanager: `make pf-alertmanager` → http://localhost:9093

---

## loki

Log aggregation backend.

| Command                       | Description |
| ----------------------------- | ----------- |
| `loki-start/down/stop/unsync` | Lifecycle   |
| `loki-status/sync/pods/logs`  | Status      |

---

## tempo

Distributed tracing backend.

| Command                        | Description |
| ------------------------------ | ----------- |
| `tempo-start/down/stop/unsync` | Lifecycle   |
| `tempo-status/sync/pods/logs`  | Status      |

> Traces visible in Grafana → Explore → datasource: **Tempo**

---

## otel-collector

OpenTelemetry collector - receives traces and metrics from apps.

| Command                                 | Description |
| --------------------------------------- | ----------- |
| `otel-collector-start/down/stop/unsync` | Lifecycle   |
| `otel-collector-status/sync/pods/logs`  | Status      |

---

## blackbox-exporter

HTTP endpoint uptime monitoring.

| Command                                    | Description |
| ------------------------------------------ | ----------- |
| `blackbox-exporter-start/down/stop/unsync` | Lifecycle   |
| `blackbox-exporter-status/sync/pods/logs`  | Status      |
