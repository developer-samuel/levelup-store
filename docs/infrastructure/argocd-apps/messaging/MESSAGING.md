# Messaging

> See also: [Makefile Command Reference](../../COMMANDS.md)


**Namespace:** `levelup-store`

---

## rabbitmq

Message broker used by Symfony Messenger for async jobs.

| Command | Description |
|---|---|
| `rabbitmq-start/down/stop/unsync` | Lifecycle |
| `rabbitmq-status/sync/pods/logs` | Status |
| `rabbitmq-queues` | List queues with message counts and consumers |

> RabbitMQ Management UI: `make pf-rabbitmq` → http://localhost:15672
