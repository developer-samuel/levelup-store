# ⚡ Composer Scripts Overview

This file serves as the central index for all Composer script documentation in this project.  
Each link below points to a dedicated file with detailed descriptions, commands, and purpose.

---

### ⚙️ Setup Scripts
- Environment generation, secrets, JWT keys, directory preparation, and UML diagram generation.
- See: [SETUP.md](scripts/SETUP.md)

### 📦 Cache Scripts
- Clears and warms up Symfony cache.
- See: [CACHE.md](scripts/CACHE.md)

### 🗃️ Database Scripts
- Scripts for database setup, migrations, and seeding.
- See: [DATABASE.md](scripts/DATABASE.md)

### 🧠 Tools Scripts
- Static analysis, metrics, refactoring, code formatting, linting.
- See: [TOOLS.md](scripts/TOOLS.md)

### 🧪 Testing Scripts
- Backend unit, integration and feature tests (PHPUnit).
- See: [TESTS.md](scripts/TESTS.md)

### 📊 CodeStats Scripts
- Custom scripts for counting files, rows, and characters.
- See: [CODESTATS.md](scripts/CODESTATS.md)

### 🕒 Misc / Utilities
- Scheduler, local server, other helpers.
- See: [MISC.md](scripts/MISC.md)

> 💡 All scripts that could take longer than 300 seconds have `Composer\\Config::disableProcessTimeout` applied to avoid timeout errors in Docker.
