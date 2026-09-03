export function initOrderNotificationEngine(shopId) {
    if (typeof window.Echo === "undefined") {
        console.warn("Laravel Echo is not loaded.");
        return;
    }

    console.log("[Notification Engine] Initializing for shop: " + shopId);

    const channelName = "shop." + shopId + ".orders";

    window.Echo.private(channelName)
        .stopListening(".order.created")
        .listen(".order.created", (e) => {
            console.log("[Notification Engine] OrderCreated event received!", e);
            // Payload is already lean (e.g. e.id, e.order_number, e.total)
            window.dispatchEvent(new CustomEvent("new-order-received", { 
                detail: e 
            }));
        });
}

window.initOrderNotificationEngine = initOrderNotificationEngine;

document.addEventListener("DOMContentLoaded", () => {
    const shopId = window.OQARI_REALTIME?.shopId;
    if (shopId) {
        initOrderNotificationEngine(shopId);
    }
});
