<template>
    <div class="utm-builder">
        <template v-if="baseUrl && defaultContent">
            <div v-if="sections.social" class="border border-gray-200 rounded p-4 mb-4">
                <h4 class="text-sm font-semibold mb-3">Social</h4>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">UTM Content</label>
                    <Input
                        type="text"
                        :model-value="contentValue"
                        :placeholder="normalizedSlug"
                        @update:model-value="updateContent($event)"
                    />
                    <p class="text-xs text-gray-500 mt-1">Leave blank to use the slug ({{ normalizedSlug }}).</p>
                </div>
                <div class="p-2 bg-gray-100 border border-gray-200 rounded mb-3 min-w-0">
                    <code class="block text-xs text-gray-700 whitespace-normal" style="word-break: break-all; overflow-wrap: anywhere;">{{ socialExampleParams }}</code>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Button
                        v-for="item in socialUrls"
                        :key="item.source"
                        size="sm"
                        icon="duplicate"
                        @click="copy(item)"
                    >
                        {{ copied === item.source ? 'Copied!' : item.label }}
                    </Button>
                </div>
            </div>

            <div v-if="sections.newsletter" class="border border-gray-200 rounded p-4 mb-4">
                <h4 class="text-sm font-semibold mb-3">Newsletter</h4>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">UTM Campaign</label>
                        <Input
                            type="text"
                            :model-value="value?.newsletter?.campaign ?? ''"
                            placeholder="e.g. may_offer"
                            @update:model-value="updateNewsletterField('campaign', $event)"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">UTM ID <span class="text-gray-400 font-normal">(optional)</span></label>
                        <Input
                            type="text"
                            :model-value="value?.newsletter?.id ?? ''"
                            placeholder="e.g. nl_042"
                            @update:model-value="updateNewsletterField('id', $event)"
                        />
                    </div>
                </div>
                <div class="p-2 bg-gray-100 border border-gray-200 rounded mb-3 min-w-0">
                    <code class="block text-xs text-gray-700 whitespace-normal" style="word-break: break-all; overflow-wrap: anywhere;">{{ newsletterParams }}</code>
                </div>
                <Button size="sm" icon="duplicate" @click="copy(newsletterItem)">
                    {{ copied === 'newsletter' ? 'Copied!' : 'Copy' }}
                </Button>
            </div>

            <div v-if="sections.paid" class="border border-gray-200 rounded p-4">
                <h4 class="text-sm font-semibold mb-3">Paid Ads</h4>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">UTM Source</label>
                        <Input
                            type="text"
                            :model-value="value?.paid?.source ?? ''"
                            placeholder="e.g. google"
                            @update:model-value="updatePaidField('source', $event)"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">UTM Medium</label>
                        <Input
                            type="text"
                            :model-value="value?.paid?.medium ?? ''"
                            placeholder="e.g. cpc"
                            @update:model-value="updatePaidField('medium', $event)"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">UTM Campaign</label>
                        <Input
                            type="text"
                            :model-value="value?.paid?.campaign ?? ''"
                            placeholder="e.g. eea_launch"
                            @update:model-value="updatePaidField('campaign', $event)"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">UTM Term <span class="text-gray-400 font-normal">(optional)</span></label>
                        <Input
                            type="text"
                            :model-value="value?.paid?.term ?? ''"
                            placeholder="e.g. eea_compliance"
                            @update:model-value="updatePaidField('term', $event)"
                        />
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">UTM ID <span class="text-gray-400 font-normal">(optional)</span></label>
                        <Input
                            type="text"
                            :model-value="value?.paid?.id ?? ''"
                            placeholder="e.g. 17234580921"
                            @update:model-value="updatePaidField('id', $event)"
                        />
                    </div>
                </div>
                <div class="p-2 bg-gray-100 border border-gray-200 rounded mb-3 min-w-0">
                    <code class="block text-xs text-gray-700 whitespace-normal" style="word-break: break-all; overflow-wrap: anywhere;">{{ paidParams }}</code>
                </div>
                <Button size="sm" icon="duplicate" :disabled="! paidItem" @click="copy(paidItem)">
                    {{ copied === 'paid' ? 'Copied!' : 'Copy' }}
                </Button>
                <p v-if="! paidItem" class="text-xs text-gray-500 mt-2">Fill in at least Source and Medium to generate the URL.</p>
            </div>
        </template>
        <div
            v-else
            class="p-3 bg-gray-100 border border-gray-200 rounded text-sm text-gray-600"
        >
            Publish the entry to see share URLs.
        </div>
    </div>
</template>

<script>
import { FieldtypeMixin as Fieldtype } from '@statamic/cms';
import { Button, Input } from '@statamic/cms/ui';

export default {
    mixins: [Fieldtype],
    components: { Button, Input },
    data() {
        return {
            copied: null,
            resetHandle: null,
        };
    },
    computed: {
        baseUrl() {
            return this.meta?.baseUrl ?? null;
        },
        defaultContent() {
            return this.meta?.defaultContent ?? null;
        },
        platforms() {
            return this.meta?.platforms ?? [];
        },
        socialMedium() {
            return this.meta?.socialMedium ?? 'social';
        },
        newsletter() {
            return this.meta?.newsletter ?? { source: 'newsletter', medium: 'email' };
        },
        sections() {
            return this.meta?.sections ?? { social: true, newsletter: true, paid: true };
        },
        contentValue() {
            return this.value?.content ?? '';
        },
        normalizedSlug() {
            return this.normalize(this.defaultContent || '');
        },
        effectiveContent() {
            const override = (this.contentValue || '').trim();
            return this.normalize(override || this.defaultContent || '');
        },
        socialExampleParams() {
            if (! this.effectiveContent) return '';
            const params = new URLSearchParams({
                utm_source: '{platform}',
                utm_medium: this.socialMedium,
                utm_content: this.effectiveContent,
            }).toString();
            return `?${decodeURIComponent(params)}`;
        },
        socialUrls() {
            if (! this.baseUrl || ! this.effectiveContent) return [];

            return this.platforms.map((platform) => {
                const params = new URLSearchParams({
                    utm_source: platform.source,
                    utm_medium: this.socialMedium,
                    utm_content: this.effectiveContent,
                }).toString();

                return {
                    label: platform.label,
                    source: platform.source,
                    url: `${this.baseUrl}?${params}`,
                };
            });
        },
        newsletterItem() {
            if (! this.baseUrl || ! this.effectiveContent) return null;

            const payload = {
                utm_source: this.newsletter.source,
                utm_medium: this.newsletter.medium,
                utm_content: this.effectiveContent,
            };

            const campaign = (this.value?.newsletter?.campaign || '').trim();
            if (campaign) payload.utm_campaign = campaign;

            const id = (this.value?.newsletter?.id || '').trim();
            if (id) payload.utm_id = id;

            const params = new URLSearchParams(payload).toString();

            return {
                source: 'newsletter',
                url: `${this.baseUrl}?${params}`,
                params: `?${params}`,
            };
        },
        newsletterParams() {
            return this.newsletterItem?.params ?? '';
        },
        paidItem() {
            if (! this.baseUrl || ! this.effectiveContent) return null;

            const source = (this.value?.paid?.source || '').trim();
            const medium = (this.value?.paid?.medium || '').trim();
            if (! source || ! medium) return null;

            const payload = {
                utm_source: source,
                utm_medium: medium,
                utm_content: this.effectiveContent,
            };

            const campaign = (this.value?.paid?.campaign || '').trim();
            if (campaign) payload.utm_campaign = campaign;

            const term = (this.value?.paid?.term || '').trim();
            if (term) payload.utm_term = term;

            const id = (this.value?.paid?.id || '').trim();
            if (id) payload.utm_id = id;

            const params = new URLSearchParams(payload).toString();

            return {
                source: 'paid',
                url: `${this.baseUrl}?${params}`,
                params: `?${params}`,
            };
        },
        paidParams() {
            if (this.paidItem) return this.paidItem.params;
            const payload = {};
            const source = (this.value?.paid?.source || '').trim();
            const medium = (this.value?.paid?.medium || '').trim();
            payload.utm_source = source || '{source}';
            payload.utm_medium = medium || '{medium}';
            if (this.effectiveContent) payload.utm_content = this.effectiveContent;
            const campaign = (this.value?.paid?.campaign || '').trim();
            if (campaign) payload.utm_campaign = campaign;
            const term = (this.value?.paid?.term || '').trim();
            if (term) payload.utm_term = term;
            const id = (this.value?.paid?.id || '').trim();
            if (id) payload.utm_id = id;
            const params = new URLSearchParams(payload).toString();
            return `?${decodeURIComponent(params)}`;
        },
    },
    beforeDestroy() {
        if (this.resetHandle) clearTimeout(this.resetHandle);
    },
    methods: {
        normalize(val) {
            return (val || '').toLowerCase().replace(/[\s-]+/g, '_');
        },
        baseValue() {
            return {
                content: this.value?.content ?? null,
                newsletter: {
                    campaign: this.value?.newsletter?.campaign ?? null,
                    id: this.value?.newsletter?.id ?? null,
                },
                paid: {
                    source: this.value?.paid?.source ?? null,
                    medium: this.value?.paid?.medium ?? null,
                    campaign: this.value?.paid?.campaign ?? null,
                    term: this.value?.paid?.term ?? null,
                    id: this.value?.paid?.id ?? null,
                },
            };
        },
        updateContent(val) {
            const next = this.baseValue();
            next.content = this.normalize(val) || null;
            this.update(next);
        },
        updateNewsletterField(field, val) {
            const next = this.baseValue();
            next.newsletter[field] = this.normalize(val) || null;
            this.update(next);
        },
        updatePaidField(field, val) {
            const next = this.baseValue();
            next.paid[field] = this.normalize(val) || null;
            this.update(next);
        },
        async copy(item) {
            if (! item) return;
            const ok = await this.writeToClipboard(item.url);
            if (! ok) {
                console.error('Failed to copy share URL');
                return;
            }
            this.copied = item.source;
            if (this.resetHandle) clearTimeout(this.resetHandle);
            this.resetHandle = setTimeout(() => {
                if (this.copied === item.source) this.copied = null;
            }, 1500);
        },
        async writeToClipboard(text) {
            if (navigator.clipboard?.writeText) {
                try {
                    await navigator.clipboard.writeText(text);
                    return true;
                } catch (e) {
                    // fall through to legacy path
                }
            }
            return this.legacyCopy(text);
        },
        legacyCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.top = '0';
            textarea.style.left = '0';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            let success = false;
            try {
                success = document.execCommand('copy');
            } catch (e) {
                success = false;
            }
            document.body.removeChild(textarea);
            return success;
        },
    },
};
</script>
