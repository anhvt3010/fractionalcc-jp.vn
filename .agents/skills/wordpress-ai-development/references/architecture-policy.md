# WordPress AI Development — Architecture Policy

This reference consolidates the user's 43-part policy. Preserve its requirements while adapting examples to the actual project.

Current project decisions (12/10/2026): the existing XAMPP site is working; Docker is temporarily not used. Releases contain two installable ZIPs under `release/vX.Y.Z/`, including approved default data. Preserve approved UI and never fabricate factual content. These decisions govern the sections below; historical Docker documents are not the current workflow.

## 1. Purpose

Use WordPress for CMS and administration; Kadence or Blocksy as the base theme; a child theme for project presentation; Gutenberg for editing; ACF for structured editable content; custom plugins for business logic; WooCommerce only when commerce is required; the existing XAMPP installation for local development; Git for source control; and AI for implementation and maintenance. Do not build primarily through drag-and-drop page builders. Docker may be reconsidered only when the user requests it.

Preferred workflow: stable WordPress ecosystem + custom code + structured CMS + AI-assisted development.

## 2. Core principles

Never edit WordPress core files, `wp-admin/`, or `wp-includes/`. Never edit parent theme files such as `kadence/` or `blocksy/`. Keep them upgradeable. Place presentation in the child theme and domain logic in a custom plugin. Do not put complex business logic in `functions.php` or presentation CSS/templates in the business plugin unless the UI is intrinsic to that plugin.

## 3. Recommended stack

WordPress, PHP, MariaDB or MySQL through XAMPP, Kadence OR Blocksy, child theme, Gutenberg, ACF, Git. PHP 8.2+ remains a target for future environment updates; inspect the actual runtime and dependency requirements before choosing syntax or versions. Do not upgrade or migrate the working environment as part of an unrelated task. Optional when justified: WooCommerce, Redis, SMTP, S3-compatible storage, WP-CLI, Composer, Node.js for asset building. Add plugins only for clear requirements. Verify compatibility and supported versions when choosing actual dependencies.

## 4. Theme selection

Prefer Kadence or Blocksy for general websites. Flatsome may be suitable for WooCommerce-heavy projects where customer visual editing is a significant requirement. Do not install multiple page builders. Avoid Elementor, WPBakery, Divi, and equivalents unless explicitly required. Prefer Gutenberg plus ACF and custom blocks/templates.

## 5. Project architecture

Example layout; simplify for small projects and preserve the actual repository root:

```text
project/
├── wp-config.php                # local only; never commit credentials
├── .gitignore
├── README.md
├── .agents/skills/wordpress-ai-development/SKILL.md
├── docs/
├── release/vX.Y.Z/               # plugin ZIP + child-theme ZIP + release notes
└── wp-content/
    ├── themes/
    │   ├── kadence/              # external dependency
    │   └── project-child/
    │       ├── style.css
    │       ├── functions.php
    │       ├── screenshot.png
    │       ├── assets/{css,js,images,fonts}/
    │       ├── template-parts/
    │       ├── templates/
    │       └── woocommerce/
    └── plugins/project-core/
        ├── project-core.php
        ├── src/{Admin,Api,Domain,PostType,Taxonomy,Service,Repository,Integration,Support}/
        ├── templates/
        ├── assets/
        └── languages/
```

Do not create unused layers or folders simply to imitate this tree.
The actual project root is `C:\xampp\htdocs\fractionalcc-jp.vn`, the WordPress installation itself. Existing Docker files may remain as historical infrastructure; do not require or run them for the current workflow.

## 6. Child theme responsibilities

Layout, typography, colors, header/footer, responsive behavior, templates, reusable template parts, CSS, presentation JavaScript, Gutenberg styles, WooCommerce overrides, product and blog presentation. Keep `functions.php` focused on enqueues, setup, theme support, simple hooks, and includes. Group assets by actual need, for example `assets/css/`, `assets/js/`, `assets/images/`, `template-parts/hero/`, `product/`, `sections/`, `common/`, and `templates/`.
Preserve the approved design. Do not change layout, colors, typography, spacing, imagery, or interactions without authorization for that change. When a required fix would visibly alter UI beyond the request, present the proposed change for approval before applying it.

## 7. Custom plugin responsibilities

Put domain-specific functionality in the project plugin, e.g. `project-core`: CPTs, taxonomies, REST/AJAX, business rules, database access, custom tables, cron, integrations, admin interfaces, import/export, notifications, validation, and WooCommerce business extensions. This plugin is the application core.

## 8. Suggested plugin architecture

For medium-sized projects use only useful parts of `src/Admin`, `Api`, `Domain`, `PostType`, `Taxonomy`, `Service`, `Repository`, `Integration`, `Support`, plus plugin-owned templates and assets. Controller/API → service → repository → database is a useful concept, not a requirement to reproduce Spring Boot. Follow WordPress conventions and add abstraction only when it improves maintainability.

## 9. ACF strategy

Use ACF for structured editable customer content. Example hero fields: title, description, image, button text, button URL. Render in the theme and escape appropriately:

```php
<section class="home-hero">
    <div class="container">
        <h1><?php echo esc_html( get_field( 'hero_title' ) ); ?></h1>
        <p><?php echo esc_html( get_field( 'hero_description' ) ); ?></p>
    </div>
</section>
```

This example assumes ACF is available and text fields return strings. Handle missing dependencies, empty content, and field return types as needed. Version field definitions as JSON or PHP; decide ownership according to presentation versus domain concerns.

## 10. Customer editing philosophy

Customers control text, images, banners, products, articles, menus, contacts, links, selected colors/section visibility, and SEO metadata. Customers should not have to edit PHP, CSS, JavaScript, HTML structure, responsive layout, queries, APIs, or business rules. Developer controls structure; customer controls content.
Use only supplied or approved factual content. Do not fabricate services, pricing, statistics, testimonials, customer identities, contact information, credentials, or achievements. If facts are missing, leave fields empty or request the information. Clearly labeled placeholders require user authorization and must not be presented as real business facts, including in seed data or SEO metadata.

## 11. Gutenberg strategy

Level 1, free content: normal Gutenberg for articles, news, simple pages. Level 2, structured sections: custom blocks, patterns, or ACF components for heroes, features, services, testimonials, FAQ, CTA, product showcases. Level 3, applications: custom plugin for booking, pricing engines, custom checkout, dashboards, advanced filters, and order workflows. Do not automatically make every feature a free-form block.

## 12. Reusable components

Compose pages from reusable template parts such as `hero.php`, `section-title.php`, `product-card.php`, `testimonial-card.php`, and `call-to-action.php`. Avoid repeated HTML across templates; search for existing components first.

## 13. CSS strategy

Prefer assets in `assets/css/`; add `main.css`, `components/`, `pages/`, or `woocommerce/` only as needed. Avoid extensive inline styles and unnecessary `!important`. Use mobile-first responsive design. If hundreds of parent overrides are required, reassess parent configuration.

## 14. JavaScript strategy

Prefer vanilla JavaScript for simple interactions, AJAX, filters, modals, sliders, and small components. Avoid large frameworks without a clear requirement. Critical rules and validation must also run server-side.

## 15. Security

Consider capability checks, nonces, validation, sanitization, escaping, prepared SQL, authentication, and authorization. Never trust request parameters. Examples: `sanitize_text_field()`, `sanitize_email()`, `esc_html()`, `esc_attr()`, `esc_url()`, `wp_nonce_field()`, `wp_verify_nonce()`, `current_user_can()`. Nonces do not replace authorization. REST endpoints need permission decisions appropriate to their data and operations.
For each affected operation: identify the input and who may perform it; validate allowed values and sanitize at the boundary; enforce capabilities/ownership and nonces where required; use WordPress APIs or prepared SQL; escape at output. For file uploads, imports, and external requests, validate file types, size, paths, and destinations as applicable. Verify permitted and rejected requests and failure handling. Default-data setup must follow the same rules and must not ship credentials or bypass permissions. Keep sensitive errors and request data out of public output and logs.

## 16. Database rules

Prefer posts/postmeta, users/usermeta, and taxonomies for CMS data. Consider custom tables for application data such as transaction logs, bookings, complex orders, inventory movements, event logs, or large relational/high-volume records. Evaluate query patterns, indexing, lifecycle, and scale; resemblance to an entity alone is not a reason for a table.

## 17. Custom SQL

Use `$wpdb->prepare()` for dynamic values. Never concatenate untrusted request data:

```php
global $wpdb;
$sql = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}example WHERE status = %s",
    $status
);
$rows = $wpdb->get_results( $sql );
```

Only trusted identifiers may form table/column names. Scope selected columns and pagination to actual requirements.

## 18. WooCommerce

Never edit WooCommerce source. Use hooks, filters, child-theme overrides, and custom plugin extensions. Business rules belong in `project-core`; presentation overrides in `project-child/woocommerce`. Check hooks/filters before overriding templates, which increase upgrade maintenance.

## 19. Git

This repository is a reusable starter snapshot: track WordPress core, parent themes, third-party plugins, their bundled vendor files, child theme, project plugin, documentation, scripts, and field definitions. Do not reinstate a root allowlist or global vendor exclusion. Tracking dependencies does not authorize editing them. Keep secrets, local configuration (`wp-config.php`, `.env`), private database dumps, uploads/runtime data, caches, and temporary files excluded. Keep release documentation versioned and explicitly decide ZIP tracking when setting up Git; their build destination remains `release/vX.Y.Z/`. Record dependency versions and restoration sources.

## 20. Environment variables

Use environment variables for database, API, SMTP, payment, and third-party secrets. Example variable names: `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `WORDPRESS_DEBUG`. Never commit real credentials. Providing `.env` alone does not make ordinary XAMPP WordPress read it; configure loading explicitly or use a runtime that injects variables.

## 21. Local environment: XAMPP; Docker deferred

Use the existing XAMPP Apache/PHP and MySQL/MariaDB with the project under `htdocs`. The user confirms this site already runs; inspect only runtime details needed for the task. Use the actual configured site URL, and do not assume it matches the folder name when a virtual host is configured. Do not start containers, require Docker lint/build checks, or migrate config/database unless the user requests a return to Docker. Keep local credentials private. Run PHP lint and relevant checks with XAMPP PHP. README must explain this workflow and distinguish historical Docker instructions.

## 22. Development workflow

Classify each feature first. Featured products: ACF configuration → child-theme rendering → WooCommerce product data. Membership pricing: project plugin → service if useful → WooCommerce hooks. Do not put price calculation in a page template.

## 23. AI inspection

Before coding identify active parent/child theme, custom plugins, relevant hooks/templates, naming conventions/helpers, ACF fields, and WooCommerce usage. Inspect existing code and runtime where needed; do not duplicate functionality.

## 24. Preserve architecture

Prefer extending existing components over parallel systems. Search before creating services, helpers, shortcodes, REST endpoints, tables, or CSS components. Reuse where appropriate.

## 25. No dangerous shortcuts

Never modify core, parent themes, third-party plugins, hardcode credentials, disable security checks to fix bugs, remove nonces without justification, concatenate request input into SQL, expose sensitive REST data, create unnecessary plugins, or install arbitrary dependencies without explaining their requirement.

## 26. Implementation preference

WordPress native → existing theme feature → existing plugin feature → hook/filter → child theme → custom plugin → new dependency. This is a preference, not permission to put business logic in a theme. Architecture takes priority over short-term convenience.

## 27. Plugin policy

Before adding a plugin determine whether core, the existing theme/plugin, or a trivial custom implementation already solves it. Evaluate maintenance, security, frontend assets, license cost, customer dependency, and removal difficulty. Avoid stacking plugins for tiny functions belonging in the project plugin.

## 28. Performance

Avoid N+1 queries, unnecessary `WP_Query`, global loading of page-only assets, huge images, excessive third-party scripts/plugins, and AJAX polling. Prefer suitable image sizes, lazy loading, selective assets, optimized queries, pagination, appropriate indexes, and caching where justified. Optimize from evidence; do not introduce complex caching prematurely.

## 29. SEO

Optimize on-page SEO within approved content and UI. Preserve semantic `header`, `nav`, `main`, `article`, `section`, `footer`, a clear main page heading, and meaningful heading hierarchy. Use editable, truthful page titles and descriptions, descriptive internal links, stable URLs, appropriate image alt text (empty for decorative images), responsive image sizes, and efficient assets. Do not stuff keywords or invent claims.

Inspect existing WordPress/SEO plugin ownership before changing metadata, canonical URLs, robots directives, XML sitemaps, social metadata, or structured data; avoid duplicate output and unnecessary plugins. Structured data must match verified visible content. Check relevant rendered HTML and URLs, including accidental indexing blocks when preparing a public release; do not remove intentional local/staging indexing restrictions. SEO work does not authorize visible design changes.

## 30. Accessibility

Consider semantics, keyboard navigation, labels, alt text, button/link semantics, focus states, contrast, and ARIA only when needed. Visual convenience must not undermine accessibility.

## 31. Naming

Use a project-specific prefix for global WordPress symbols and meaningful names. Example for TANH Store: `tanh_register_product_type()`, `tanh_get_featured_items()`, namespace `Tanh\Store\Service`. Choose the real project's identity before adopting sample names.

## 32. Coding style

Follow WordPress Coding Standards unless the project has another consistent standard. Keep functions focused. Avoid 500-line functions, massive `functions.php`, mutable global state, copied templates, business logic mixed into HTML, and SQL in arbitrary templates. Readability over cleverness.
Do not add unused code, speculative abstractions, or redundant dependencies. Reuse existing functions, template parts, field definitions, queries, and assets when behavior is shared; keep real differences explicit. Remove obsolete code caused by the change without refactoring unrelated features.

## 33. Error handling

Do not silently ignore errors. Use appropriate `WP_Error` and controlled logging. Do not expose stack traces, SQL errors, API secrets, or filesystem paths to production users.

## 34. Customer administration

Expose relevant customer tools: Dashboard, Pages, Products, Orders, Posts, Website Content (Homepage, Contact, Footer, Social Links). Hide irrelevant menus for customer roles when appropriate, while retaining administrator recovery. Menu hiding alone is not authorization.

## 35. Deployment

Reproducible flow: developer → Git → server/deploy → database migration/update → cache clear. Avoid editing production PHP in the WordPress editor. Prefer `define( 'DISALLOW_FILE_EDIT', true );` in production configuration. Production changes require the authorization appropriate to the actual task.

Build into `release/vX.Y.Z/` with exactly two installable ZIP packages: the project core plugin and the child theme. Each archive has a single top-level folder matching its source slug. The plugin archive contains approved default data and its versioned initializer; theme-owned field definitions/assets remain in the theme archive. ACF definitions alone do not include saved content values. Seed only missing records/settings, record initialization versions, and preserve edited content on rerun/update. Do not automatically replace homepage, menus, theme selection, or existing content through activation. Exclude WordPress core, parent themes, third-party plugins, secrets, database dumps, logs, and private uploads from these packages; document required dependencies separately. Use [release/README.md](../../../../release/README.md) for layout and release checks.

## 36. Backups

Back up database, uploads, and recovery configuration through an appropriate private mechanism. Source lives in Git. A backup is valid only if restoration works; verify recovery without committing production data or secrets.

## 37. Updates

Keep core, parent theme, WooCommerce, ACF, and plugins upgradeable. For major updates: backup → local/staging test → compatibility review → update → critical workflow tests → deploy. Record versions and licensed dependency restoration.

## 38. Definition of done

Verify requirement, desktop/mobile where affected, intended customer editing, protected structure, validation, authorization, required nonces, escaping, PHP warnings, browser errors, obvious duplicate queries, and regressions. For affected commerce verify cart, checkout, pricing, order creation, relevant emails, and customer/admin workflows. Mark unavailable checks explicitly rather than claiming success.
Also verify relevant on-page SEO and preservation of approved content/design. For a release, check both archive layouts, dependency requirements, clean-install default initialization, and rerun/update preservation of customer edits. Do not claim ZIPs or initialization support exist until implemented and tested.

## 39. AI execution sequence

Understand → inspect → classify Theme/ACF/Gutenberg/Plugin/WooCommerce → identify files → smallest maintainable implementation → security → responsive checks if UI → regressions → summary. Do not generate large implementations before inspection.

## 40. Coding response format

Use concise sections when reporting implementation: Analysis (placement and reason), Files Changed, Implementation, Verification, Notes (migrations, ACF configuration/sync, admin actions, deployment, compatibility). Straightforward changes need short explanations.

## 41. Decision guide

| Requirement | Owner |
| --- | --- |
| Appearance | Child theme |
| Editable structured content | ACF / Gutenberg |
| Domain/business logic | Custom plugin |
| E-commerce behavior | WooCommerce hooks + custom plugin |
| E-commerce presentation | Child theme + WooCommerce hooks/templates |
| Generic article/page | Gutenberg |

## 42. Final architecture

Customer → WordPress Admin → Gutenberg/ACF → structured data → child-theme presentation and custom-plugin behavior → WordPress/WooCommerce → website. Infrastructure: developer + AI → XAMPP local → versioned plugin/theme ZIP release → staging → production; Git tracks the starter snapshot.

## 43. Primary rule

Content → WordPress/Gutenberg/ACF. Presentation → child theme. Business logic → custom plugin. E-commerce engine → WooCommerce. Local infrastructure → XAMPP (Docker deferred). Release → two installable ZIPs under a version folder with approved default data. Source control → Git. Implementation assistance → AI. Build the smallest maintainable solution, reuse WordPress capabilities, preserve approved UI, and never fabricate facts.
