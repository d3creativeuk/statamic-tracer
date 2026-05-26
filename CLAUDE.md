# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Statamic 6 addon (Tracer) that ships a single fieldtype (`tracer`). The fieldtype renders on an entry edit screen and lets editors copy UTM-tagged share URLs for that entry's public URL — it's a UTM Builder, just branded Tracer. Three sections — Social, Newsletter, Paid Ads — all share one `utm_content` value (defaults to the entry slug, normalized to lowercase with underscores).

This repo is the standalone addon (PHP + compiled JS). The host Statamic site used for development is the sibling project at `../d3creative` — composer requires the addon by path, and the addon's `package.json` resolves `@statamic/cms` via `file:../d3creative/vendor/statamic/cms/packages/cms` for build-time types/stubs.

## Build & publish workflow

```bash
npm install            # one-time; resolves @statamic/cms from sibling Statamic install
npm run build          # vite production build → public/build/
npm run dev            # vite build --watch (no HMR; rebuilds on file change)
```

**Critical:** `npm run build` writes only to *this addon's* `public/build/`. The host Statamic site loads its JS from `d3creative/public/vendor/statamic-tracer/build/`, which is a one-time copy of the published asset — **not a symlink**. After every build you must republish from the host:

```bash
cd ../d3creative && php artisan vendor:publish --provider="D3Creative\Tracer\ServiceProvider" --force
```

Skipping this step is the most common dev pitfall — your Vue changes silently won't appear in the CP because the host serves a stale bundle. The published manifest at `d3creative/public/vendor/statamic-tracer/build/manifest.json` should reference the latest hashed `addon-*.js` from `public/build/`.

There's no test suite; the addon is verified by reloading the CP and exercising the fieldtype on an entry.

## Architecture

Three layers wired by `src/ServiceProvider.php`:

- **PHP fieldtype** (`src/Fieldtypes/Tracer.php`) — declares blueprint config (`configFieldItems`), supplies runtime data via `preload()` (the entry's `absoluteUrl()` and `slug()`, plus configured platforms/sections), and round-trips storage via `preProcess`/`process`. `preProcess` also handles two **legacy data shapes** (bare string = old `utm_content` only; flat `{ content, campaign }` = old newsletter-only schema) so older entries still load. `process` returns `null` when every input is empty, so blueprints stay clean for entries without active campaigns.

- **Vue component** (`resources/js/components/TracerFieldtype.vue`) — Options API component mixed with Statamic's `FieldtypeMixin` from `@statamic/cms`. Reads `meta` (from `preload()`) and `value` (the persisted object), generates URLs via `URLSearchParams`, and uses `Input` / `Button` from `@statamic/cms/ui` for native CP styling. Slug-style normalization (`lowercase + spaces/dashes → underscores`) is applied on every write so analytics stays consistent.

- **Registration** (`resources/js/addon.js`) — registers the Vue component as `tracer-fieldtype` under Statamic's `$components` registry on boot.

### Persisted shape

```json
{
  "content": "european_accessibility_act",
  "newsletter": { "campaign": "may_offer", "id": null },
  "paid": { "source": "google", "medium": "cpc", "campaign": null, "term": null, "id": null }
}
```

`content` is shared by all three sections (it's the `utm_content` everyone uses). Editing it inside the Social card propagates to Newsletter and Paid Ads URLs.

## Statamic 6 gotchas

- **`vite.config.js` must use `@statamic/cms/vite-plugin`**, not bare `@vitejs/plugin-vue`. Statamic's plugin externalizes `vue` to `window.Vue` so the addon shares Statamic's Vue runtime. Without it, `Button`/`Input` from `@statamic/cms/ui` render but their cva-generated Tailwind classes effectively don't style anything — buttons look like plain text, inputs lose their borders. Bundle size is a quick sanity check: ~14 kB if Vue is externalized, ~28 kB if it's being bundled.

- **Icons** are referenced by name from Statamic's built-in icon set (`resources/svg/icons/` in the host). `globe` doesn't exist in Statamic 6 — currently using `link`. Verify any new icon name exists in that directory before referencing it.

- **`Button` accepts both `:text` prop and a default slot**. Use the default slot — it's been more reliable across builds in this addon (`<Button>Label</Button>` vs `<Button :text="'Label'">`).

- **Grid fieldtype `mode: 'table'` vs `'stacked'`** — for narrow 2-3 column configs like the platforms list, `'table'` is dramatically more compact. `'stacked'` adds large vertical gaps per row in Statamic 6.

## Marketplace metadata

The `composer.json` `extra.statamic` block (`name`, `description`, `developer`, `developer_url`, `tags`) is what Statamic's marketplace pulls when the package is listed via Packagist. Keep it in sync with the README's pitch.
