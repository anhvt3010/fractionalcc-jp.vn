#!/bin/sh
set -eu

case "$PROJECT_SITE_URL" in
  http://localhost:*) ;;
  *) echo "Refusing setup: this script is intended for localhost Docker only." >&2; exit 1 ;;
esac

if ! wp core is-installed; then
  wp core install \
    --url="$PROJECT_SITE_URL" \
    --title="$WP_SITE_TITLE" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="$WP_ADMIN_EMAIL" \
    --skip-email
else
  echo "WordPress already installed; existing users and content preserved."
fi

for plugin in advanced-custom-fields project-core; do
  if ! wp plugin is-active "$plugin"; then
    wp plugin activate "$plugin"
  fi
done
if [ "$(wp option get stylesheet)" != "project-child" ]; then
  wp theme activate project-child
fi
if [ "$(wp option get permalink_structure)" = "" ]; then
  wp rewrite structure '/%postname%/'
fi
php /opt/project/create-sample-page.php
echo "Local setup completed. Visit $PROJECT_SITE_URL"
