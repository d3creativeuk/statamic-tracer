import TracerFieldtype from './components/TracerFieldtype.vue';

Statamic.booting(() => {
    Statamic.$components.register('tracer-fieldtype', TracerFieldtype);
});
