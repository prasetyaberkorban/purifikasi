import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;

Alpine.plugin(collapse);

document.addEventListener('alpine:init', () => {
    //
});

Alpine.start();