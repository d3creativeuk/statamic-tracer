<?php

namespace D3Creative\Tracer\Fieldtypes;

use Statamic\Fields\Fieldtype;

class Tracer extends Fieldtype
{
    protected static $handle = 'tracer';

    protected static $title = 'Tracer (UTM Builder)';

    protected $categories = ['special'];

    public function icon()
    {
        return 'link';
    }

    public function configFieldItems(): array
    {
        return [
            'sections' => [
                'display' => 'Sections',
                'type' => 'section',
            ],
            'social_enabled' => [
                'display' => 'Show Social section',
                'type' => 'toggle',
                'default' => true,
            ],
            'newsletter_enabled' => [
                'display' => 'Show Newsletter section',
                'type' => 'toggle',
                'default' => true,
            ],
            'paid_enabled' => [
                'display' => 'Show Paid Ads section',
                'type' => 'toggle',
                'default' => true,
            ],
            'social_section' => [
                'display' => 'Social',
                'type' => 'section',
            ],
            'platforms' => [
                'display' => 'Social platforms',
                'instructions' => 'The buttons rendered in the Social section. utm_source is used verbatim.',
                'type' => 'grid',
                'mode' => 'table',
                'add_row' => 'Add platform',
                'if' => ['social_enabled' => 'equals true'],
                'fields' => [
                    ['handle' => 'label', 'field' => ['type' => 'text', 'display' => 'Label']],
                    ['handle' => 'source', 'field' => ['type' => 'text', 'display' => 'utm_source']],
                ],
                'default' => [
                    ['label' => 'X', 'source' => 'x'],
                    ['label' => 'LinkedIn', 'source' => 'linkedin'],
                    ['label' => 'BlueSky', 'source' => 'bluesky'],
                    ['label' => 'Threads', 'source' => 'threads'],
                ],
            ],
            'social_medium' => [
                'display' => 'Social utm_medium',
                'type' => 'text',
                'default' => 'social',
                'if' => ['social_enabled' => 'equals true'],
            ],
            'newsletter_section' => [
                'display' => 'Newsletter',
                'type' => 'section',
            ],
            'newsletter_source' => [
                'display' => 'Newsletter utm_source',
                'type' => 'text',
                'default' => 'newsletter',
                'if' => ['newsletter_enabled' => 'equals true'],
            ],
            'newsletter_medium' => [
                'display' => 'Newsletter utm_medium',
                'type' => 'text',
                'default' => 'email',
                'if' => ['newsletter_enabled' => 'equals true'],
            ],
        ];
    }

    public function preload()
    {
        $parent = $this->field?->parent();

        $baseUrl = null;
        $defaultContent = null;

        if ($parent && method_exists($parent, 'absoluteUrl') && method_exists($parent, 'slug')) {
            $baseUrl = $parent->absoluteUrl();
            $defaultContent = $parent->slug();
        }

        return [
            'baseUrl' => $baseUrl,
            'defaultContent' => $defaultContent,
            'platforms' => $this->config('platforms') ?: [
                ['label' => 'X', 'source' => 'x'],
                ['label' => 'LinkedIn', 'source' => 'linkedin'],
                ['label' => 'BlueSky', 'source' => 'bluesky'],
                ['label' => 'Threads', 'source' => 'threads'],
            ],
            'socialMedium' => $this->config('social_medium', 'social'),
            'newsletter' => [
                'source' => $this->config('newsletter_source', 'newsletter'),
                'medium' => $this->config('newsletter_medium', 'email'),
            ],
            'sections' => [
                'social' => (bool) $this->config('social_enabled', true),
                'newsletter' => (bool) $this->config('newsletter_enabled', true),
                'paid' => (bool) $this->config('paid_enabled', true),
            ],
        ];
    }

    public function preProcess($data)
    {
        // Legacy: bare string was utm_content only.
        if (is_string($data)) {
            return $this->emptyValue(['content' => $data]);
        }

        if (! is_array($data)) {
            return $this->emptyValue();
        }

        // Legacy: { content, campaign } where campaign was newsletter-specific.
        if (array_key_exists('campaign', $data) && ! isset($data['newsletter'])) {
            return $this->emptyValue([
                'content' => $data['content'] ?? null,
                'newsletter' => ['campaign' => $data['campaign'] ?? null],
            ]);
        }

        return $this->emptyValue([
            'content' => $data['content'] ?? null,
            'newsletter' => [
                'campaign' => $data['newsletter']['campaign'] ?? null,
                'id' => $data['newsletter']['id'] ?? null,
            ],
            'paid' => [
                'source' => $data['paid']['source'] ?? null,
                'medium' => $data['paid']['medium'] ?? null,
                'campaign' => $data['paid']['campaign'] ?? null,
                'term' => $data['paid']['term'] ?? null,
                'id' => $data['paid']['id'] ?? null,
            ],
        ]);
    }

    public function process($data)
    {
        if (! is_array($data)) {
            return null;
        }

        $cleaned = [
            'content' => $this->cleanString($data['content'] ?? null),
            'newsletter' => [
                'campaign' => $this->cleanString($data['newsletter']['campaign'] ?? null),
                'id' => $this->cleanString($data['newsletter']['id'] ?? null),
            ],
            'paid' => [
                'source' => $this->cleanString($data['paid']['source'] ?? null),
                'medium' => $this->cleanString($data['paid']['medium'] ?? null),
                'campaign' => $this->cleanString($data['paid']['campaign'] ?? null),
                'term' => $this->cleanString($data['paid']['term'] ?? null),
                'id' => $this->cleanString($data['paid']['id'] ?? null),
            ],
        ];

        return $this->isEmpty($cleaned) ? null : $cleaned;
    }

    public function defaultValue()
    {
        return $this->emptyValue();
    }

    private function emptyValue(array $overrides = []): array
    {
        $base = [
            'content' => null,
            'newsletter' => ['campaign' => null, 'id' => null],
            'paid' => [
                'source' => null,
                'medium' => null,
                'campaign' => null,
                'term' => null,
                'id' => null,
            ],
        ];

        return array_replace_recursive($base, $overrides);
    }

    private function cleanString($value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function isEmpty(array $cleaned): bool
    {
        foreach ($cleaned as $value) {
            if (is_array($value)) {
                if (! $this->isEmpty($value)) return false;
            } elseif ($value !== null) {
                return false;
            }
        }

        return true;
    }
}
