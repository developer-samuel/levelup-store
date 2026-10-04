Generate a CHANGELOG entry for the current branch or a given version.

## Steps

1. Get commits since last tag (or from the branch):
   ```bash
   git log $(git describe --tags --abbrev=0)..HEAD --oneline --no-merges
   ```
2. Group by Conventional Commits type:
   - `feat:` → 🚀 Features
   - `fix:` → 🐛 Fixes
   - `refactor:` → ♻️ Architecture
   - `ci:`, `build:` → 🏗️ Infrastructure
   - `test:` → 🧪 Tests
   - `docs:` → 📚 Docs
   - `chore:`, `style:`, `perf:` → omit unless significant
   - `BREAKING CHANGE` → **Breaking Changes** (top section)

3. Format:

```markdown
## [x.y.z] - YYYY-MM-DD

### Breaking Changes
- ...

### 🚀 Features
- ...

### 🐛 Fixes
- ...

### ♻️ Architecture
- ...

### 🏗️ Infrastructure
- ...

### 🧪 Tests
- ...

### 📚 Docs
- ...
```

## Rules
- One bullet per commit, rewritten as user-facing change
- Skip `chore:` - they are internal
- Breaking changes go first
- Ask the user for the version number if not provided
- Prepend new entry to `CHANGELOG.md` - do not overwrite existing entries
