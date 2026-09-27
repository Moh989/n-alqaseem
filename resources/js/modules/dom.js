export const isRtl = () => document.documentElement.dir === 'rtl';

export const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export const FOCUSABLE =
    'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

export function focusableWithin(root) {
    return [...root.querySelectorAll(FOCUSABLE)].filter((el) => !el.closest('[hidden], [inert]') && el.offsetParent !== null);
}
