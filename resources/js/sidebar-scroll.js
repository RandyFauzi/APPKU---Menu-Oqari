export function sidebarScroll(userId) {
    return {
        scrollKey: `cafe-sidebar-scroll:${userId}`,
        observer: null,
        init() {
            this.$nextTick(() => {
                try {
                    const saved = sessionStorage.getItem(this.scrollKey);
                    if (saved !== null && Number.isFinite(Number(saved))) {
                        this.$el.scrollTop = Math.max(0, Number(saved));
                    }
                } catch {}

                this.revealActive();
                this.observer = new MutationObserver(() => this.revealActive());
                this.observer.observe(this.$el, { subtree: true, attributes: true, attributeFilter: ['class'] });
            });
        },
        savePosition() {
            try {
                sessionStorage.setItem(this.scrollKey, String(this.$el.scrollTop));
            } catch {}
        },
        revealActive() {
            const active = this.$el.querySelector('.sidebar-link.is-active');
            if (!active) return;

            const viewport = this.$el.getBoundingClientRect();
            const item = active.getBoundingClientRect();
            if (item.top < viewport.top) {
                this.$el.scrollTop += item.top - viewport.top - 8;
            } else if (item.bottom > viewport.bottom) {
                this.$el.scrollTop += item.bottom - viewport.bottom + 8;
            }
            this.savePosition();
        },
        destroy() {
            this.observer?.disconnect();
        },
    };
}
