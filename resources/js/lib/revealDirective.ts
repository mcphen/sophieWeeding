/**
 * Vue Directive for scroll-reveal animations.
 * Usage: <div v-reveal> ... </div>  or  <div v-reveal:up.200> ... </div> (200ms delay)
 */

import type { Directive, DirectiveBinding } from 'vue';

const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
                observer.unobserve(entry.target);
            }
        });
    },
    { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
);

export const vReveal: Directive = {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
        const direction = binding.arg || 'up';
        const delay = Object.keys(binding.modifiers)[0];

        el.classList.add('reveal', `reveal-${direction}`);
        if (delay) {
            el.style.transitionDelay = `${delay}ms`;
        }

        observer.observe(el);
    },
    unmounted(el: HTMLElement) {
        observer.unobserve(el);
    },
};

export default vReveal;
