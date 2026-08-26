---
name: release
description: Cut a release by bumping the Semver version in composer.json and the README install example, committing, and creating a release tag. Use whenever the user asks to release, tag, or bump the version.
---

# Release

Cut a new release of this package.

## Semver policy

Versions follow [Semver](https://semver.org) (`MAJOR.MINOR.PATCH`).

While the package is **pre-1.0** (`0.y.z`):

- **Breaking change** → bump **MINOR** (`0.1.0` → `0.2.0`)
- **New feature or fix** → bump **PATCH** (`0.1.0` → `0.1.1`)

From **1.0.0** onwards:

- **Breaking change** → bump **MAJOR**
- **Backwards-compatible feature** → bump **MINOR**
- **Backwards-compatible fix** → bump **PATCH**

To choose the bump, review every commit since the last release tag (`git log <last-tag>..HEAD --oneline`, or all commits if no tag exists yet) and classify the most significant change. A single breaking change makes the whole release breaking. If it's unclear whether a change is breaking, ask the user.

## Tag format

Tags are the version prefixed with `v`, e.g. `v0.2.0`. Annotated tags only.

## Workflow

1. **Preflight** — must be on `main`, working tree clean, and up to date with `origin/main` (`git fetch origin` then compare). Abort and tell the user if not.
2. **CI green** — the QA workflow run for the current `HEAD` must have passed: `gh run list --workflow QA --branch main --commit $(git rev-parse HEAD)`. If it failed, hasn't run, or is still in progress, abort and tell the user — never release on red or unverified CI.
3. **Determine the new version** — find the current version in `composer.json`, review commits since the last tag, and apply the Semver policy above. Confirm the chosen version with the user before proceeding.
4. **Bump versions** — update the `version` field in `composer.json` and the pinned version in the README install example so all three (composer.json, README, tag) agree.
5. **Commit** — follow the commit skill; message `Released <version>`, e.g. `Released 0.2.0`.
6. **Tag** — `git tag -a v<version> -m "v<version>"` on that commit.
7. **Push** — `git push origin main v<version>`. The package is installed from GitHub as a `vcs` repository, so the release isn't usable until the tag is pushed.

## Don'ts

- No release from a dirty tree or a non-`main` branch.
- No release when CI is red, pending, or missing for the release commit.
- No lightweight tags.
- Don't skip the README version pin — consumers copy the install example verbatim.
- Don't retag or move an existing tag; if a release is broken, cut a new patch release.
