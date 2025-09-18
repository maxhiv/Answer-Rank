=== AnswerRank Connector ===
Contributors: 251SEO, maxhansen
Tags: integration, seo, semrush, answerrank, connector, api
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight WordPress connector plugin that handles UI & authentication to your AnswerRank backend (e.g., Replit).

== Description ==

This plugin adds an admin settings page (Settings → AnswerRank) to connect your site to the AnswerRank backend (hosted wherever you like—Replit, VPS, etc.). It stores a Connector API Key, exposes a small admin-only REST proxy with nonce/capability checks and basic rate limiting, and includes a "Test Connection" button that calls your backend health endpoint. All heavy logic (SEMrush token exchange, installs, schema pushes) lives in your backend—this plugin only handles UI & auth.

**Key Features**
* Admin settings page: Backend Base URL, Connector API Key, optional Webhook Secret.
* Admin-only REST proxy: `/wp-json/answerrank/v1/proxy` (nonce + `manage_options` required).
* Basic rate limiting using transients.
* Uninstall cleanup for stored options.

== Installation ==

1. Upload the ZIP via **Plugins → Add New → Upload Plugin** and activate.
2. Go to **Settings → AnswerRank**.
3. Set **Backend Base URL** (e.g., `https://your-app.onreplit.app/api`).
4. Click **Open Connect** to complete your backend flow and obtain the **Connector API Key**.
5. Paste the **Connector API Key** and **Save**.
6. Use the **Test Connection** button to verify connectivity.

== Frequently Asked Questions ==

= Does this plugin include the AnswerRank backend? =
No. This is a connector (UI & auth) only. Your backend performs all heavy operations.

= Is this plugin safe to use on production sites? =
Yes—inputs are sanitized, capability checks + REST nonces are required, and basic rate limiting is provided. Always keep WordPress, themes, and plugins updated.

= Can I use this without SEMrush? =
Yes. SEMrush-related workflows live in your backend. The plugin simply forwards allowed actions to whatever backend you configure.

= Can I extend the allowlisted proxy paths? =
Yes. Edit `includes/class-arc-rest.php` and update the `$allow` array to add/remove paths.

== Screenshots ==

1. Settings page — Backend URL, Connector API Key, and rate limiter.
2. Test Connection — verifies your backend `/health` endpoint.

== Changelog ==

= 1.0.0 - 2025-09-16 =
* Initial release to WordPress.org. UI/auth connector, REST proxy with nonce/capability checks, transient-based rate limiting, and uninstall cleanup.
