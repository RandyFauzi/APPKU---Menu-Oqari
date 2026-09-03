export function initOrderNotificationEngine(shopId) {
    if (typeof window.Echo === "undefined") {
        console.warn("Laravel Echo is not loaded.");
        return;
    }

    console.log("[Notification Engine] Initializing for shop: " + shopId);

    const channelName = "shop." + shopId + ".orders";

    window.Echo.private(channelName)
        .stopListening("OrderCreated")
        .listen("OrderCreated", (e) => {
            console.log("[Notification Engine] OrderCreated event received!", e);
            window.dispatchEvent(new CustomEvent("new-order-received", { 
                detail: e.order || e 
            }));
        });
}

window.initOrderNotificationEngine = initOrderNotificationEngine;

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        if (window.SHOP_ID) {
            initOrderNotificationEngine(window.SHOP_ID);
        }
    }, 500);
});
