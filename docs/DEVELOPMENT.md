# ⚒️ Development

Platform-level development guide covering the shared Docker stack used by all apps.

---

```bash
# Show all available make commands
make help
# or
awk '/^## /{desc=substr($0,4)} /^[a-zA-Z_-]+:/{if(desc!="") printf "  %-45s %s\n", $1, desc; desc=""}' Makefile make/*.mk | sed 's/://' | sort

# Fix file permissions - fixes root-owned files (WSL2)
make fix-permissions
# or
bash scripts/set-permissions/entrypoints/run.sh
```

---

### Project structure

> Requires `tree` - install with `sudo apt install tree` (Debian/Ubuntu) or `brew install tree` (macOS).

```bash
# Generate to file (.structure/tree.txt)
make generate-structure
# or
mkdir -p .structure && git ls-files | tree --fromfile > .structure/tree.txt && echo "✅ Project structure saved to .structure/tree.txt"

# Generate directory structure to .structure/dirs.txt (directories only)
make generate-structure-dirs
# or
mkdir -p .structure && git ls-files | tree --fromfile -d > .structure/dirs.txt && echo "✅ Project directory structure saved to .structure/dirs.txt"

# Generate UML diagrams from source code
make generate-uml
# or
bash scripts/generate-uml/entrypoints/run.sh
```

---

## References

- [Install & Git hooks](development/INSTALL.md)
- [Docker commands](development/DOCKER.md)
- [Ecommerce commands](development/ECOMMERCE.md)
- [Assistant commands](development/ASSISTANT.md)

---

See also: [Ecommerce Development](../apps/ecommerce/docs/DEVELOPMENT.md) · [Assistant Development](../apps/assistant/docs/DEVELOPMENT.md)
