import './bootstrap';

import Alpine from 'alpinejs';
import { initEcho } from './echo';

window.Alpine = Alpine;
window.initEcho = initEcho;

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function timeAgo(isoDate) {
    if (!isoDate) return '';
    const diffSec = Math.max(0, Math.floor((Date.now() - new Date(isoDate).getTime()) / 1000));
    if (diffSec < 60) return 'À l’instant';
    const diffMin = Math.floor(diffSec / 60);
    if (diffMin < 60) return `Il y a ${diffMin} min`;
    const diffH = Math.floor(diffMin / 60);
    if (diffH < 24) return `Il y a ${diffH} h`;
    const diffJ = Math.floor(diffH / 24);
    return `Il y a ${diffJ} j`;
}

Alpine.data('notificationsMenu', () => ({
    open: false,
    items: [],
    unreadCount: 0,
    pollHandle: null,

    init() {
        this.fetchNotifications();
        this.pollHandle = setInterval(() => this.fetchNotifications(), 20000);
    },

    async fetchNotifications() {
        try {
            const res = await fetch('/notifications/unread', {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const json = await res.json();
            const notifications = json?.data?.notifications ?? [];
            this.unreadCount = json?.data?.unread_count ?? notifications.length;
            this.items = notifications.slice(0, 8).map((n) => ({
                ...n,
                timeAgo: timeAgo(n.created_at),
            }));
        } catch (e) {
            // Silencieux : la navbar ne doit jamais casser la page si l'API est indisponible.
        }
    },

    async markAsRead(item) {
        if (item.read_at) return;
        item.read_at = new Date().toISOString();
        this.unreadCount = Math.max(0, this.unreadCount - 1);
        try {
            await fetch(`/notifications/${item.id}/read`, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                credentials: 'same-origin',
            });
        } catch (e) {
            // best effort
        }
    },

    async markAllAsRead() {
        this.items.forEach((i) => { i.read_at = i.read_at ?? new Date().toISOString(); });
        this.unreadCount = 0;
        try {
            await fetch('/notifications/read-all', {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                credentials: 'same-origin',
            });
        } catch (e) {
            // best effort
        }
    },
}));

Alpine.start();
