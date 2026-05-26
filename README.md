# Tracer by D3 Creative



**A UTM Builder fieldtype that generates UTM-tagged share URLs for Statamic entries, directly from the Control Panel.**

Tracer adds a UTM Builder fieldtype to any blueprint that turns entry edit screens into a campaign URL builder. Think of it as [Google's Campaign URL Builder](https://ga-dev-tools.google/campaign-url-builder/) baked into Statamic, so editors can grab a ready-tagged link without leaving the entry they're publishing.

![Tracer by D3 Creative](art/tracer-art.jpg)

Three sections in one field:

- **Social** — one Copy button per platform (X, LinkedIn, BlueSky, Threads by default; configurable).
- **Newsletter** — `utm_source=newsletter&utm_medium=email` with editable `utm_campaign` and optional `utm_id`.
- **Paid Ads** — free-form `utm_source` / `utm_medium` plus `utm_campaign` / `utm_term` / `utm_id`.

All three share a single `utm_content` value (defaults to the entry slug; can be overridden per-entry). Inputs auto-normalize to lowercase with underscores so your analytics stays consistent.



## Installation

```bash
composer require d3creative/statamic-tracer
```

Then add a **Tracer (UTM Builder)** field to any blueprint via the Statamic CP. The field renders on the entry edit screen for any entry whose collection produces a public URL.

## Configuration

Per-field config in the blueprint editor:

- Toggle each section (Social / Newsletter / Paid Ads) on or off independently.
- Edit the Social platforms list (label + `utm_source` per platform).
- Override the default `utm_medium` values for Social and Newsletter.

## How values persist

The field stores a nested object on the entry:

```json
{
    "content": "european_accessibility_act_compliance_websites",
    "newsletter": { "campaign": "may_offer", "id": null },
    "paid": { "source": "google", "medium": "cpc", "campaign": null, "term": null, "id": null }
}
```

When every input is empty the field stores `null`, so blueprints without active campaign data stay clean.

## Requirements

- PHP 8.2+
- Statamic 6.x

## Support

This addon is maintained by [D3 Creative](https://d3creative.uk). For enquiries about managed Statamic maintenance, visit [d3creative.uk/services/statamic-maintenance](https://d3creative.uk/services/statamic-maintenance).

## License

Released under the [MIT License](LICENSE).
