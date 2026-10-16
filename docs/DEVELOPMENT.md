# ⚒️ Development

Platform-level development guide covering the shared Docker stack used by all apps.

---

```bash
# Show all available make commands
make help

# Fix file permissions - fixes root-owned files (WSL2)
make fix-permissions
```

---

### Project structure

> Requires `tree` - install with `sudo apt install tree` (Debian/Ubuntu) or `brew install tree` (macOS).

```bash
# Generate both tree.txt and dirs.txt in one command
make generate-structure-all

# Generate tree only (.structure/tree.txt)
make generate-structure

# Generate dirs only (.structure/dirs.txt)
make generate-structure-dirs

# Generate UML diagrams from source code
make generate-uml
```

---

### Linting

```bash
# Validate GitHub Actions workflow syntax
yamllint .github/workflows/
```

---

## References

- [Install & Git hooks](development/INSTALL.md)
- [Docker commands](development/DOCKER.md)
- [Ecommerce commands](development/ECOMMERCE.md)
- [Assistant commands](development/ASSISTANT.md)

---

See also: [Ecommerce Development](../apps/ecommerce/docs/DEVELOPMENT.md) · [Assistant Development](../apps/assistant/docs/DEVELOPMENT.md)
