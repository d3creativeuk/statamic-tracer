# UTM Builder for Statamic

A Statamic fieldtype that **generates** UTM-tagged URLs for entries, ready to copy and paste into a tweet, a newsletter, or a Google Ads campaign. Think of it as [Google's Campaign URL Builder](https://ga-dev-tools.google/campaign-url-builder/) baked into your entry edit screen.

Three sections in one field:

- **Social** — one Copy button per platform (X, LinkedIn, BlueSky, Threads by default; configurable).
- **Newsletter** — `utm_source=newsletter&utm_medium=email` with editable `utm_campaign` + optional `utm_id`.
- **Paid Ads** — free-form `utm_source` / `utm_medium` plus `utm_campaign` / `utm_term` / `utm_id`.

All three share a single `utm_content` (defaults to the entry slug; can be overridden). Inputs auto-normalize to lowercase with underscores.

> **Looking to *read* UTM params from incoming visitor traffic** (e.g. to personalize content based on the campaign that brought someone in)? That's a different problem; see [toni-suarez/utm-parameter](https://statamic.com/addons/toni-suarez/utm-parameter). The two addons are complementary: this one builds the URLs you put out; that one reads them when those clicks come back in.

## Installation

```bash
composer require d3creative/statamic-utm-builder
php artisan vendor:publish --tag=statamic-utm-builder --force
```

Then add a UTM Builder field to any blueprint via the Statamic CP.

## Configuration

Per-field config in the blueprint editor:

- Toggle each section on or off independently.
- Edit the Social platforms list (label + utm_source per platform).
- Override the default `utm_medium` values for Social and Newsletter.

## How values persist

The field stores a nested object:

```json
{
    "content": "european_accessibility_act_compliance_websites",
    "newsletter": { "campaign": "may_offer", "id": null },
    "paid": { "source": "google", "medium": "cpc", "campaign": null, "term": null, "id": null }
}
```

When all fields are empty, the field stores `null`.

## Local development

```bash
npm install
npm run build      # produces public/build/
```

The package depends on `@statamic/cms` from a sibling Statamic site at `../d3creative` for build-time stubs that resolve `window.__STATAMIC__` at runtime. Adjust the file path in `package.json` if your sibling Statamic project is elsewhere.

## License

MIT
