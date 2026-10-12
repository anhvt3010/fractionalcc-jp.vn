---
name: wordpress-ai-development
description: Develop and maintain this WordPress project using child themes, Gutenberg, ACF, custom plugins, XAMPP, and versioned plugin/theme ZIP releases. Apply when implementing, reviewing, setting up, or packaging this project; preserve approved content, UI, and existing conventions.
---

# WordPress AI Development

Build the smallest maintainable solution using a stable WordPress ecosystem, custom code, structured CMS content, and AI-assisted development.

## Current project workflow

- Use the existing working XAMPP installation at `C:\xampp\htdocs\fractionalcc-jp.vn`. The project root is WordPress itself; use `wp-content/...` directly. Docker is temporarily out of scope: do not start containers, require Docker checks, or migrate the environment unless the user requests it.
- For a release, create `release/vX.Y.Z/` containing exactly two installable ZIP packages: the project core plugin and the child theme to activate. Include approved default data and a versioned, repeatable initializer in the plugin package; package presentation field definitions and assets with their owner. Follow [release/README.md](../../../release/README.md) for package layout, installation, initialization, and verification. This is the required build workflow, not evidence that a build script or release ZIPs already exist.
- Never invent business facts, claims, prices, statistics, testimonials, clients, contact details, or SEO claims. Use supplied or approved content; leave missing content empty or ask for it. Use clearly labeled placeholders only when the user authorizes them, and do not publish them as facts.
- Preserve the approved UI. Do not independently change layout, colors, typography, spacing, imagery, or interactions. If a security, SEO, or accessibility fix needs a visible design change outside the request, explain the proposed change and obtain authorization first.

## Architecture and non-negotiable rules

- Content belongs in WordPress, Gutenberg, or ACF. Presentation belongs in the child theme. Domain and business logic belong in the project custom plugin. WooCommerce supplies the e-commerce engine when required.
- Never edit WordPress core, `wp-admin/`, `wp-includes/`, parent themes, WooCommerce source, or other third-party plugins. Keep dependencies upgradeable.
- Prefer Kadence or Blocksy as the parent. Consider Flatsome only for an explicitly suitable WooCommerce and visual-editing requirement. Do not introduce page builders unless explicitly required; never stack multiple builders.
- Keep `functions.php` small: setup, enqueues, simple hooks, and includes. Do not place pricing, repositories, integrations, or complex business logic there.
- Inspect and reuse existing equivalents before adding helpers, services, shortcodes, endpoints, tables, components, or dependencies.
- Follow WordPress Coding Standards and existing project style. Keep code focused and reusable; avoid dead code, speculative layers, copied components, duplicate queries, and redundant asset loading.
- Customers edit content and selected settings; developers control structure and application behavior.
- Never hardcode secrets, weaken security to fix bugs, or expose private data. Validate and sanitize inputs, escape outputs by context, enforce authorization, verify nonces where required, and prepare dynamic SQL.
- Optimize on-page SEO within the approved UI and truthful content: semantic HTML, heading hierarchy, editable titles/descriptions, valid internal links, image alt text, and efficient assets. Reuse existing SEO ownership; avoid duplicate metadata, canonical tags, sitemaps, or structured data. Read policy section 29 when relevant.

## Workflow

1. Understand the requirement and inspect applicable project instructions and existing code before generating code.
2. Identify the active parent and child theme, project plugins, hooks, templates, prefixes, helpers, ACF definitions, and WooCommerce usage. Installed files do not prove activation; query runtime state when relevant. Do not print secrets while inspecting configuration.
3. Classify the work as presentation, structured content, free content, domain behavior, e-commerce behavior, or infrastructure. Identify affected files.
4. Prefer WordPress native features, existing theme/plugin features, and hooks or filters before adding custom implementations or dependencies. Preserve the presentation/business separation even when another placement would use fewer lines.
5. Implement the smallest maintainable change using WordPress conventions and existing project style.
6. Verify security and affected workflows on XAMPP. For authorized UI changes check desktop, mobile, accessibility, intended customer editing, PHP warnings, and browser console errors. Check relevant on-page SEO, regressions, and performance. For releases verify both ZIPs, required dependencies, default initialization on a clean installation, and preservation of edited content on rerun/update. Report checks you could not run.
7. For affected WooCommerce workflows verify cart, checkout, pricing, order creation, relevant emails, and customer/admin behavior.
8. Report briefly using **Analysis**, **Files Changed**, **Implementation**, **Verification**, and **Notes** where applicable. Mention migrations, ACF synchronization, admin actions, deployment, and compatibility limitations.

## Reference routing

Read [architecture-policy.md](references/architecture-policy.md) for the project's 43-part policy, implementation decisions, and examples. For a new setup or architecture change, read the whole reference. For a focused task, read the relevant numbered sections:

- Theme, components, CSS, JavaScript, content, ACF, Gutenberg: 4–14, 29–30.
- Custom plugins, data, security, WooCommerce: 7–8, 15–18, 31–34.
- Git, XAMPP environment, releases, deployment, backups, updates: 19–21, 35–37; also read [release/README.md](../../../release/README.md) when packaging.
- Reuse, implementation choices, dependencies, performance, SEO, coding style: 22–29, 32.
- Workflow, completion, response format, decision guide: 38–43.

These paths in the reference are examples. Adapt to the inspected repository layout; do not relocate an existing site just to match the example. Create only directories and abstractions justified by the work.
