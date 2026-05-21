import UtmBuilderFieldtype from './components/UtmBuilderFieldtype.vue';

Statamic.booting(() => {
    Statamic.$components.register('utm_builder-fieldtype', UtmBuilderFieldtype);
});
