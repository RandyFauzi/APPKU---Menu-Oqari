<?php
$content = file_get_contents("C:/Users/LENOVO/.gemini/antigravity/brain/db049d6d-dba7-4007-977a-f142349ca36d/db_roadmap.md");
$content = str_replace(
    "- `order_status_history`, `refunds`",
    "- `order_status_history`, `refunds` (Terpisah dari tabel Payments)\n- `qris_settlements` (Financial Ledger untuk integrasi bank/gateway)",
    $content
);
file_put_contents("C:/Users/LENOVO/.gemini/antigravity/brain/db049d6d-dba7-4007-977a-f142349ca36d/db_roadmap.md", $content);
