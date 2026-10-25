import './bootstrap';
import { createIcons, icons } from 'lucide';

createIcons({ icons });

const root = document.documentElement;
const themeToggle = document.querySelector('[data-theme-toggle]');
const menuToggle = document.querySelector('[data-menu-toggle]');
const mobileMenu = document.querySelector('[data-mobile-menu]');

themeToggle?.addEventListener('click', () => {
    const dark = !root.classList.contains('dark');
    root.classList.toggle('dark', dark);
    root.dataset.theme = dark ? 'dark' : 'light';
    localStorage.setItem('theme', dark ? 'dark' : 'light');
});

document.querySelectorAll('[data-workspace-theme]').forEach((button) => {
    button.addEventListener('click', () => {
        const dark = root.dataset.theme !== 'dark';
        root.dataset.theme = dark ? 'dark' : 'light';
        root.classList.toggle('dark', dark);
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    });
});

menuToggle?.addEventListener('click', () => {
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    mobileMenu?.classList.toggle('hidden', isOpen);
});

mobileMenu?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
        menuToggle?.setAttribute('aria-expanded', 'false');
    });
});
