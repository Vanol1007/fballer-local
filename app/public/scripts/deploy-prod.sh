#!/bin/zsh

set -euo pipefail

REMOTE_HOST="sic4bu_fballer@sic4bu.beget.tech"
REMOTE_REPO_DIR="/home/s/sic4bu/fballer.ru/public_html/repos/fballer"
REMOTE_LIVE_DIR="/home/s/sic4bu/fballer.ru/public_html"
THEME_RELATIVE_PATH="wp-content/themes/fballer"
PLUGIN_RELATIVE_PATH="wp-content/plugins/fballer-rest-bridge"

ssh "$REMOTE_HOST" "
set -euo pipefail

cd '$REMOTE_REPO_DIR'
git pull --ff-only origin main

mkdir -p '$REMOTE_LIVE_DIR/$THEME_RELATIVE_PATH'
rsync -av --delete '$REMOTE_REPO_DIR/$THEME_RELATIVE_PATH/' '$REMOTE_LIVE_DIR/$THEME_RELATIVE_PATH/'

mkdir -p '$REMOTE_LIVE_DIR/$PLUGIN_RELATIVE_PATH'
rsync -av --delete '$REMOTE_REPO_DIR/$PLUGIN_RELATIVE_PATH/' '$REMOTE_LIVE_DIR/$PLUGIN_RELATIVE_PATH/'
"
