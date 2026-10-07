# Scaling

> See also: [Makefile Command Reference](../../COMMANDS.md)


---

## keda

Event-driven autoscaling - scales workers and schedulers based on queue depth.

**Namespace:** `keda`

| Command                       | Description                                          |
|-------------------------------|------------------------------------------------------|
| `keda-start/down/stop/unsync` | Lifecycle                                            |
| `keda-status/sync/pods/logs`  | Status                                               |
| `keda-scaledobjects`          | List all ScaledObjects with current/desired replicas |
| `keda-triggers`               | List all TriggerAuthentications                      |

---

## vpa

Vertical Pod Autoscaler - recommends CPU/RAM requests and limits.

**Namespace:** `vpa`

| Command                      | Description |
|------------------------------|-------------|
| `vpa-start/down/stop/unsync` | Lifecycle   |
| `vpa-status/sync/pods/logs`  | Status      |

App-specific VPA recommendations:

> - `make ecommerce-vpa`
> - `make assistant-vpa`
