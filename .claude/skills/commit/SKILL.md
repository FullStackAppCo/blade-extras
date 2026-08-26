---
name: commit
description: Commit staged or working-tree changes as atomic commits with terse single-line messages ending in an issue number when there is an associated issue. Use whenever the user asks to commit work.
---

# Commit

Create git commits following this project's conventions.

## Rules

1. **Atomic commits** — each commit contains one logical change only. If the working tree holds unrelated changes, split them into separate commits, staging each group of related files (or hunks via `git add -p`) individually.
2. **Terse messages** — one short sentence in sentence case, past tense (e.g. `Added qa checks`). No descriptive body or bullet points — an atomic commit is self-explanatory. The `Co-Authored-By: Claude` trailer is kept.
3. **Issue number at the end** — every message ends with the issue reference, e.g.:

   ```
   Added qa checks #8
   ```

   If the issue number isn't known, check recent `git log` for the issue currently being worked on, or ask the user which issue this relates to. It's OK to omit the issue number if there is no associated issue.

4. **Message shape** — subject line, blank line, then the co-author trailer only:

   ```
   Added qa checks #8

   Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>
   ```

## Workflow

1. Run `git status` and `git diff` to see what's changed.
2. Group the changes into logical units. One unit → one commit.
3. For each unit: stage only its files/hunks, then commit with a message following the shape above (terse subject + co-author trailer, nothing else).
4. Verify with `git log --oneline` that each subject is terse and ends with `#<issue>` when there is an associated issue.

## Don'ts

- No descriptive bodies or bullet points between the subject and the trailer.
- No lumping unrelated changes into one commit.
- Don't push unless asked.
