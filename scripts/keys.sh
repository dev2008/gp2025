#!/usr/bin/env bash
# append_key.sh — append a local public key to remote authorized_keys (no overwrite)
# Usage: ./append_key.sh user@host [path/to/key.pub]
# Example: ./append_key.sh zh2342@zh2342.rsync.net ~/.ssh/id_ecdsa.pub

set -euo pipefail

REMOTE="${1:-}"
PUB="${2:-$HOME/.ssh/id_ecdsa.pub}"

if [[ -z "$REMOTE" ]]; then
  echo "Usage: $0 user@host [path/to/key.pub]" >&2
  exit 1
fi
if [[ ! -f "$PUB" ]]; then
  echo "Public key not found: $PUB" >&2
  exit 2
fi

# 0) Prep remote .ssh with correct perms (allowed cmds only)
#ssh "$REMOTE" "mkdir -p .ssh; chmod 700 .ssh; touch .ssh/authorized_keys; chmod 600 .ssh/authorized_keys"

# 1) Upload key and a newline chunk
tmpnl="$(mktemp)"; printf '\n' > "$tmpnl"
scp "$PUB"  "$REMOTE:.ssh/key.tmp"
scp "$tmpnl" "$REMOTE:.ssh/nl.tmp"
rm -f "$tmpnl"

# 2) Append (no remote redirection or pipes)
ssh "$REMOTE" "dd if=.ssh/nl.tmp  of=.ssh/authorized_keys oflag=append conv=notrunc"
ssh "$REMOTE" "dd if=.ssh/key.tmp of=.ssh/authorized_keys oflag=append conv=notrunc"
ssh "$REMOTE" "dd if=.ssh/nl.tmp  of=.ssh/authorized_keys oflag=append conv=notrunc"

# 3) Cleanup & lock perms
ssh "$REMOTE" "rm -f .ssh/key.tmp .ssh/nl.tmp; chmod 600 .ssh/authorized_keys; ls -l .ssh/authorized_keys"

echo "Appended $(basename "$PUB") to $REMOTE:.ssh/authorized_keys"
