#!/bin/bash

# shellcheck disable=SC1091

set -o errexit
set -o nounset
set -o pipefail
# set -o xtrace # Uncomment this line for debugging purposes

# Load libraries
. /opt/bitnami/scripts/liblaravel.sh

# Load Laravel environment
. /opt/bitnami/scripts/laravel-env.sh

# Ensure Laravel environment variables are valid
laravel_validate

# Ensure Laravel app is initialized
laravel_initialize

info "update composer"
debug_execute composer update

info "dump auto load composer"
debug_execute composer dumpautoload

info "optimize code"
debug_execute php artisan optimize

info "start custom commands"
# Load custom commands
. /opt/bitnami/scripts/laravel/startup_commands.sh
info "end custom commands"


# Ensure all folders in /app are writable by the non-root "bitnami" user
chown -R bitnami:bitnami /app

