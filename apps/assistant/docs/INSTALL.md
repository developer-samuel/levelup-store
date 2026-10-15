# 📦 Assistant Install

This file describes the **installation steps** for the assistant app on a fresh checkout.

---

## 1. Install Dependencies

```bash
# Install Python + frontend dependencies
make assistant-install
```

---

## 2. Run with Docker

First time setup (stops any running stack first, then runs initialization):

```bash
make setup-build
```

With all dev tools:

```bash
make dev-setup-build
```

Subsequent starts:

```bash
make dev
```

---

✅ For environment variables and configuration setup see [SETUP.md](SETUP.md).

---

See also: [Platform Install](../../../docs/INSTALL.md)
