# Project instructions

This WordPress project follows the user's developer-first architecture.

Before development, read `.agents/skills/wordpress-ai-development/SKILL.md` and the relevant sections of its `references/architecture-policy.md`. For setup or architecture changes, read the complete policy.

- Inspect the existing implementation before coding; preserve and reuse conventions.
- Never edit WordPress core, parent themes, or third-party plugins.
- Presentation belongs in the child theme; domain/business logic belongs in the project plugin; editable content belongs in Gutenberg/ACF.
- Keep dependencies upgradeable, credentials private, inputs validated, outputs escaped, and permissions/nonces enforced where required.
- Use the existing working XAMPP site at `C:\xampp\htdocs\fractionalcc-jp.vn`. Docker is temporarily not used; do not start it, require it for development/builds, or migrate environments without a user request.
- Build releases into `release/vX.Y.Z/` with exactly two installable ZIP packages: the project core plugin and the child theme to activate. Include approved default data with versioned, repeatable initialization that preserves existing edits; follow `release/README.md`.
- Follow WordPress Coding Standards, optimize on-page SEO, and reuse existing components. Avoid unused code, duplicated logic/markup/queries/assets, and unnecessary abstractions.
- Do not invent factual content or business claims. Use supplied/approved content and ask for missing facts; placeholders require authorization and must be clearly labeled.
- For Fractional CC content, read `docs/WEBSITE-BRIEF.md` and its sources under `template/fractional-cc/`. Preserve the recorded missing/conflicting facts; template ratings, promotions, policies and domain names are not automatically verified or approved.
- Preserve the approved UI; do not change design without user authorization. SEO, security, and accessibility work does not implicitly authorize a redesign.
- The repository root is currently the WordPress installation itself. Use `wp-content/...` paths, not an additional `wordpress/wp-content/...` prefix.
- This repository is a reusable starter snapshot: the user wants WordPress core, parent themes, third-party plugins and their bundled vendor files version controlled. Do not restore the old allowlist ignore rules. Tracking dependencies does not authorize editing their source; keep secrets, local configuration and runtime data ignored.
- Follow `docs/SETUP.md` for the current XAMPP workflow and `release/README.md` for release documentation. Historical Docker notes do not override this workflow. Inspect runtime state before claiming theme/plugin activation or completed checks.
