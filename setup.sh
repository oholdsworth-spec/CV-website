#!/bin/sh
# One-off WP-CLI setup for the local test stack. Safe to re-run.
set -e
cd /var/www/html

echo "Waiting for WordPress files and database..."
until [ -f wp-config.php ] && wp db check >/dev/null 2>&1; do sleep 3; done

if ! wp core is-installed 2>/dev/null; then
  wp core install --url="$WP_URL" --title="$WP_TITLE" \
    --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="$WP_ADMIN_EMAIL" --skip-email
fi

wp theme activate cv-blog
wp option update blogdescription "Making Workday work for people."

# Remove the stock sample content
wp post delete "$(wp post list --post_type=post --name=hello-world --field=ID)" --force 2>/dev/null || true
wp post delete "$(wp post list --post_type=page --name=sample-page --field=ID)" --force 2>/dev/null || true

# Home (the CV, rendered by the theme's front-page template) and Blog pages
page_id() { wp post list --post_type=page --name="$1" --field=ID | head -n1; }
[ -n "$(page_id home)" ] || wp post create --post_type=page --post_status=publish --post_title="Home" --post_name=home >/dev/null
[ -n "$(page_id blog)" ] || wp post create --post_type=page --post_status=publish --post_title="Blog" --post_name=blog >/dev/null
wp option update show_on_front page
wp option update page_on_front "$(page_id home)"
wp option update page_for_posts "$(page_id blog)"

# Pretty permalinks
wp rewrite structure '/%postname%/' --hard

# Header menu: Home, Career (anchor on the CV), Blog
if [ "$(wp post list --post_type=wp_navigation --format=count)" = "0" ]; then
  wp post create --post_type=wp_navigation --post_status=publish --post_title="Main menu" \
    --post_content='<!-- wp:navigation-link {"label":"Home","type":"custom","url":"/","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Career","type":"custom","url":"/#career","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Blog","type":"custom","url":"/blog/","kind":"custom","isTopLevelLink":true} /-->' >/dev/null
fi

echo "Done. Open $WP_URL"
