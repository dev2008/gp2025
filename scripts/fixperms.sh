#!/usr/bin/env bash
#
# fix-webroot-perms.sh
#
# Reset permissions and group ownership under /opt/lampp/htdocs/webroot
# so members of "webdev" can read/write, and new files inherit the group.
#

set -euo pipefail

ROOT="/opt/lampp/htdocs/webroot"
GROUP="webdev"

echo "🔧 Fixing ownership and permissions under: $ROOT"
echo "   Target group: $GROUP"
echo

# 1) Ensure everything is owned by group webdev
echo "👉 Setting group ownership..."
sudo chgrp -R "$GROUP" "$ROOT"

# 2) Directories: rwx for owner/group, r-x for others, setgid (2775)
echo "👉 Fixing directory permissions..."
sudo find "$ROOT" -type d -exec chmod 2775 {} \;

# 3) Files: rw for owner/group, r for others (664)
echo "👉 Fixing file permissions..."
sudo find "$ROOT" -type f -exec chmod 664 {} \;

# 4) Restore group rwX everywhere (capital X only applies +x on dirs/executables)
echo "👉 Restoring group rwX..."
sudo chmod -R g+rwX "$ROOT"

# 5) Apply ACLs if available (so new files are always group-writable)
if command -v setfacl >/dev/null 2>&1; then
  echo "👉 Setting default ACLs (if supported)..."
  sudo setfacl -R -m g:"$GROUP":rwX "$ROOT" || true
  sudo setfacl -R -d -m g:"$GROUP":rwX "$ROOT" || true
fi

echo
echo "✅ Done. Everything under $ROOT is now group-owned by $GROUP and writable."
