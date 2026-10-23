# CV & Blog: WordPress block theme

A one-page digital CV on the front page, plus a blog. Styled in your personal brand: cobalt and hot pink on a soft lavender ground, Space Grotesk / Inter / JetBrains Mono, Dusk hero with Glow and rings, Day-gradient career timeline, logo lockup in the header. Needs WordPress 6.6+ and PHP 7.4+.

## Install on your real site

1. Zip the `cv-blog` folder (or use `cv-blog-theme.zip`).
2. Appearance → Themes → Add New → Upload Theme → activate.
3. Settings → Reading: set **Your homepage displays** to a static page. Pick an empty page called Home as the homepage and an empty page called Blog as the posts page. (The theme's front-page template renders the CV whatever the Home page contains.)
4. Settings → Permalinks: choose **Post name**.
5. Appearance → Editor → Navigation: add Home, Career (`/#career`) and Blog links.

## Edit the CV

Appearance → Editor → Templates → **Front Page**. Click any text and type. Each section is a block pattern (cv-hero, cv-capabilities, cv-ai, cv-delivered, cv-career, cv-certifications, cv-contact), so you can reorder, delete or re-insert sections from the pattern inserter under the **CV** category.

- Add a role: duplicate a step group in the career section.
- Reuse the same CV layout on another page: Page → Template → **CV page**.
- Brand tokens live in `theme.json` (palette, gradients, fonts). The dark-mode values are in `styles/dark.json` and `style.css` and the print values in `assets/print.css`. The logo is `assets/logo.svg`.

## What's built in

- **Dark mode** follows the visitor's system setting automatically (palette remap in `style.css`). There is also a bundled **Dark** style variation (`styles/dark.json`): Editor → Styles → Browse styles → Dark, to force dark for everyone. The hero stays Dusk with light text in both.
- **Print / save as PDF**: the "Print or save as PDF" button opens the print dialog. `assets/print.css` forces the light palette, hides the header, footer and buttons, and avoids splitting cards across pages.
- **No email address** appears on the site; the closing section links to LinkedIn (`patterns/cv-contact.php`).
- **Fonts** (Space Grotesk, Inter, JetBrains Mono) are bundled and self-hosted, so nothing is fetched from Google.
- **Blog**: index, single post (with comments), archive, search, 404.

## Local test with Docker

```
cp .env.example .env            # set passwords
docker compose up -d
docker compose --profile setup run --rm wpcli
```

Open http://localhost:8080 (admin at /wp-admin). The `cv-blog` folder is mounted live, so edits show on refresh. The setup step installs WordPress, activates the theme, creates Home and Blog pages, sets permalinks and creates the header menu. To reset: `docker compose down -v`.

## Before it goes public

- Footer carries an "independent material, not endorsed by Workday" line to match your current page; edit it in Editor → Patterns → Template Parts → Footer.
- Put the site behind HTTPS (reverse proxy or Cloudflare tunnel) and add a caching or security plugin of your choice. Keep core, PHP and the database image updated.

## Contact form (Fluent Forms + FluentSMTP)

"Contact me" (top and bottom of the page) opens a pop-up containing a Fluent Forms form. Until the plugin and a form exist, those buttons just go to LinkedIn.

1. Plugins → Add New: install and activate **Fluent Forms** and **FluentSMTP**.
2. **FluentSMTP** → Settings → Add Connection: enter your SMTP provider's host, port, username and password (or pick the provider's API option). Send the test email. Use a sending address your provider allows as "From".
3. **Fluent Forms** → Forms → New Form → Contact Form. Title it "Contact me" (the theme uses the first form whose title starts with "Contact", otherwise the oldest form). Keep name, email and message fields.
4. Settings & Integrations → Email Notifications: set the recipient to your real address, keep Reply-To as the sender's email field.
5. Bot protection. Honeypot: Fluent Forms → Global Settings → General → Miscellaneous → turn on **Enable Honeypot Security** → Save Settings. Turnstile: create a free widget in Cloudflare (Turnstile → Add widget, add your domain), then Global Settings → Security → Turnstile, paste Site Key and Secret Key, Verify, Save Settings; then edit the form and add **Turnstile** from Advanced Fields, and Save Form.
6. Reload the home page and click the button.

To force a specific form, add `add_filter( 'cv_blog_contact_form_id', fn() => 3 );` to `functions.php`.
