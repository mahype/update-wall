import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('notifications', (settings) => {
    if (!('Notification' in window)) {
        return {};
    }

    return {
        permission: Notification.permission,
        _interval: null,

        get canRequest() {
            return this.permission === 'default';
        },
        get isGranted() {
            return this.permission === 'granted';
        },
        get isDenied() {
            return this.permission === 'denied';
        },

        async requestPermission() {
            const result = await Notification.requestPermission();
            this.permission = result;
            if (result === 'granted') {
                this.initPolling();
            }
        },

        async initPolling() {
            if (!settings.enabled || this.permission !== 'granted') return;

            await this.seedIfEmpty();
            this.poll();
            this._interval = setInterval(() => this.poll(), 5 * 60 * 1000);
        },

        async seedIfEmpty() {
            if (localStorage.getItem('uw_machine_statuses') !== null) return;

            try {
                const response = await fetch('/dashboard/status', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await response.json();
                const snapshot = {};
                data.machines.forEach((m) => (snapshot[m.id] = m.status));
                localStorage.setItem('uw_machine_statuses', JSON.stringify(snapshot));
            } catch (_) {}
        },

        async poll() {
            if (!settings.enabled || this.permission !== 'granted') return;

            try {
                const response = await fetch('/dashboard/status', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await response.json();

                const previous = JSON.parse(localStorage.getItem('uw_machine_statuses') || '{}');
                const next = {};

                data.machines.forEach((machine) => {
                    next[machine.id] = machine.status;

                    const prevStatus = previous[machine.id];
                    const newStatus = machine.status;

                    if (prevStatus !== newStatus && settings.statuses.includes(newStatus)) {
                        this.notify(machine);
                    }
                });

                localStorage.setItem('uw_machine_statuses', JSON.stringify(next));
            } catch (_) {}
        },

        notify(machine) {
            new Notification('Update Wall', {
                body: `${machine.name}: ${machine.status_label}`,
                icon: '/favicon.ico',
                tag: `uw-machine-${machine.id}`,
            });
        },

        init() {
            if (this.isGranted) {
                this.initPolling();
            }
        },

        destroy() {
            if (this._interval) clearInterval(this._interval);
        },
    };
});

Alpine.start();
