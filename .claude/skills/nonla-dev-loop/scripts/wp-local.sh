#!/usr/bin/env bash
# Run WP-CLI against the LOCAL site from Git Bash, without Local's "Open site shell".
#
#   bash .claude/skills/nonla-dev-loop/scripts/wp-local.sh option get home
#   bash .claude/skills/nonla-dev-loop/scripts/wp-local.sh --php -l app/public/sections/hero.php
#
# The first form runs WP-CLI in app/public; the second runs plain PHP (syntax checks).
# It reads app/.envrc, which Local regenerates every time the site starts, so the
# PHP / MySQL / WP-CLI paths always match the running site. Local only: there is
# no way to point this at production, and nothing here should ever try.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." && pwd)"
ENVRC="$ROOT/app/.envrc"
WP_ROOT="$ROOT/app/public"

if [ ! -f "$ENVRC" ]; then
  echo "wp-local: $ENVRC not found. Start the site in Local first (it writes that file)." >&2
  exit 1
fi

# export NAME="value"  ->  value
val() { sed -nE "s/^export $1=\"(.*)\"\$/\1/p" "$ENVRC" | head -n 1; }

PHPRC="$(val PHPRC)"
MYSQL_HOME="$(val MYSQL_HOME)"
WP_CLI_CONFIG_PATH="$(val WP_CLI_CONFIG_PATH)"
MAGICK_CODER_MODULE_PATH="$(val MAGICK_CODER_MODULE_PATH)"
export PHPRC MYSQL_HOME WP_CLI_CONFIG_PATH MAGICK_CODER_MODULE_PATH
export WP_CLI_DISABLE_AUTO_CHECK_UPDATE=1

# export PATH="C:/some/dir:$PATH"  ->  prepend /c/some/dir (Git Bash cannot use C:/ in PATH)
while IFS= read -r dir; do
  [ -n "$dir" ] && PATH="$(cygpath -u "$dir"):$PATH"
done < <(sed -nE 's/^export PATH="(.*):\$PATH"$/\1/p' "$ENVRC")
export PATH

PHAR="$(dirname "$WP_CLI_CONFIG_PATH")/wp-cli.phar"
if ! command -v php >/dev/null 2>&1 || [ ! -f "$PHAR" ]; then
  echo "wp-local: could not find Local's php or wp-cli.phar from $ENVRC." >&2
  echo "          Fall back to Local -> right-click the site -> Open site shell." >&2
  exit 1
fi

# --php ...  runs Local's PHP directly (for example a syntax check with -l), from the current directory.
if [ "${1:-}" = "--php" ]; then
  shift
  exec php "$@"
fi

cd "$WP_ROOT"
exec php "$PHAR" "$@"
