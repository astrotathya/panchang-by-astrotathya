#!/bin/sh
# Builds dist/panchang-by-astrotathya.zip with a top-level panchang-by-astrotathya/ folder.
set -eu
cd "$(dirname "$0")/.."
SLUG=panchang-by-astrotathya
STAGE=$(mktemp -d)
mkdir "$STAGE/$SLUG"
# .wordpress-org/ holds the directory listing's icon, banners and screenshots (uploaded to the
# SVN assets folder, never shipped in the plugin).
git ls-files | grep -v -e '^\.gitignore$' -e '^bin/' -e '^\.wordpress-org/' -e '^README\.md$' -e '^SECURITY\.md$' | while read -r f; do
	mkdir -p "$STAGE/$SLUG/$(dirname "$f")"
	cp "$f" "$STAGE/$SLUG/$f"
done
mkdir -p dist
rm -f "dist/$SLUG.zip"
(cd "$STAGE" && zip -qr -X "$OLDPWD/dist/$SLUG.zip" "$SLUG")
rm -rf "$STAGE"
echo "dist/$SLUG.zip"
