<?php

return [
    'groups' => [
        'summary' => [
            'categories' => ['id', 'shop_id', 'name', 'sort_order'],
            'tables' => ['id', 'shop_id', 'name'],
            'users' => ['id', 'shop_id', 'name', 'role'],
        ],
        'catalog' => [
            'shop_slug_histories' => ['id', 'shop_id', 'old_slug'],
            'categories' => ['id', 'shop_id', 'name', 'slug', 'sort_order', 'is_active', 'created_at', 'updated_at'],
            'products' => ['id', 'shop_id', 'category_id', 'category_name', 'name', 'description', 'price', 'cogs', 'image_path', 'is_sold_out', 'deleted_at', 'created_at', 'updated_at'],
            'product_variants' => ['id', 'product_id', 'name', 'price_adjustment', 'is_active'],
            'modifier_groups' => ['id', 'product_id', 'name', 'is_required', 'min_selections', 'max_selections', 'is_active'],
            'modifiers' => ['id', 'modifier_group_id', 'name', 'price_adjustment', 'is_active'],
        ],
        'orders' => [
            'orders' => ['id', 'shop_id', 'table_id', 'customer_name', 'customer_phone', 'customer_email', 'subtotal', 'discount_amount', 'tax_amount', 'service_charge_amount', 'grand_total', 'total_cogs', 'order_status', 'payment_status', 'payment_method', 'fulfillment_type', 'created_at', 'updated_at'],
            'order_items' => ['id', 'order_id', 'product_id', 'product_name', 'variant_name', 'quantity', 'unit_price', 'subtotal', 'discount_amount', 'tax_amount', 'cogs_total', 'modifiers', 'notes', 'created_at', 'updated_at'],
            'tables' => ['id', 'shop_id', 'name', 'public_token'],
        ],
        'variant_costs' => [
            'product_variants' => ['cogs_adjustment'],
            'modifiers' => ['cogs_adjustment'],
            'cash_drawer_transactions' => ['payment_id'],
        ],
        'checkout' => [
            'orders' => ['checkout_request_id', 'amount_paid', 'change_amount', 'payment_reference', 'paid_at'],
            'order_items' => ['variant_id'],
            'payments' => ['id', 'shop_id', 'order_id', 'payment_method', 'amount', 'reference_number', 'cancelled_at', 'created_at', 'updated_at'],
        ],
        'register' => [
            'cash_registers' => ['id', 'shop_id', 'name', 'is_active'],
            'cash_register_sessions' => ['id', 'cash_register_id', 'user_id', 'status', 'opened_at', 'closed_at', 'opening_cash', 'expected_cash', 'actual_cash', 'difference', 'total_cash_sales', 'total_qris_sales', 'total_other_sales', 'notes', 'created_at', 'updated_at'],
            'cash_drawer_transactions' => ['id', 'cash_register_session_id', 'type', 'amount', 'description', 'cancelled_at'],
        ],
        'inventory' => [
            'raw_materials' => ['id', 'shop_id', 'name', 'sku', 'unit', 'current_stock', 'minimum_stock', 'cost_per_unit', 'deleted_at', 'created_at', 'updated_at'],
            'product_recipes' => ['id', 'product_id', 'raw_material_id', 'amount', 'created_at', 'updated_at'],
            'stock_mutations' => ['id', 'shop_id', 'raw_material_id', 'type', 'amount', 'balance_after', 'unit', 'reference_type', 'reference_id', 'notes', 'user_id', 'created_at', 'updated_at'],
        ],
        'hpp' => [
            'products' => ['hpp_overhead_cost', 'hpp_target_margin', 'hpp_settings'],
            'product_recipes' => ['batch_amount', 'notes'],
        ],
        'users' => [
            'users' => ['id', 'shop_id', 'name', 'email', 'password', 'role', 'is_active', 'permissions', 'account_version', 'remember_token', 'email_verified_at', 'created_at', 'updated_at'],
        ],
        'shifts' => [
            'crew_shifts' => ['id', 'shop_id', 'user_id', 'shift_template_id', 'position', 'date', 'start_time', 'end_time', 'notes', 'created_at', 'updated_at'],
        ],
        'attendance' => [
            'attendances' => ['id', 'shop_id', 'user_id', 'employee_name', 'crew_shift_id', 'work_date', 'timezone', 'scheduled_start_at', 'scheduled_end_at', 'checked_in_at', 'checked_out_at', 'late_minutes', 'early_leave_minutes', 'worked_minutes', 'check_in_note', 'check_out_note', 'created_at', 'updated_at'],
        ],
        'audit' => [
            'activity_logs' => ['id', 'shop_id', 'user_id', 'action', 'description', 'entity_type', 'entity_id', 'old_values', 'new_values', 'ip_address', 'user_agent', 'created_at', 'updated_at'],
        ],
        'settings' => [
            'shop_slug_histories' => ['id', 'shop_id', 'old_slug'],
            'shops' => ['id', 'name', 'slug', 'logo_path', 'banner_paths', 'primary_color', 'theme_style', 'business_type', 'sales_modes', 'onboarding_status', 'onboarding_step', 'is_open', 'slogan', 'font_family', 'title_font_family', 'receipt_header', 'receipt_footer', 'instagram_link', 'whatsapp_number', 'maps_link', 'is_banner_active', 'operating_hours', 'address', 'email'],
            'payment_methods' => ['id', 'shop_id', 'name', 'code', 'type', 'is_active'],
        ],
        'reports' => [
            'payments' => ['id', 'shop_id', 'order_id', 'payment_method', 'amount', 'cancelled_at', 'created_at'],
        ],
        'platform' => [
            'shops' => ['status', 'trial_ends_at', 'last_active_at', 'mrr', 'suspended_at', 'suspended_reason', 'suspended_by'],
            'superadmin_audit_logs' => ['id', 'actor_id', 'action', 'target_type', 'target_id', 'target_label', 'meta', 'ip_address', 'created_at', 'updated_at'],
        ],
        'receipts' => [
            'receipt_deliveries' => ['id', 'order_id', 'shop_id', 'channel', 'destination', 'status', 'attempts', 'provider_message_id', 'failure_reason', 'sent_at', 'failed_at', 'created_at', 'updated_at'],
        ],
    ],
    'features' => [
        'summary' => ['summary'],
        'catalog' => ['catalog'], 'orders' => ['orders', 'catalog'],
        'pos' => ['catalog', 'orders', 'register'],
        'checkout' => ['catalog', 'orders', 'checkout', 'register', 'inventory', 'receipts'],
        'inventory' => ['inventory', 'audit'], 'hpp' => ['catalog', 'inventory', 'hpp', 'audit'],
        'users' => ['users', 'audit'], 'shifts' => ['shifts'],
        'attendance' => ['attendance', 'shifts', 'audit'], 'register' => ['register', 'audit'],
        'settings' => ['settings', 'orders'], 'reports' => ['reports', 'orders', 'catalog'],
        'analytics' => ['orders', 'catalog'], 'audit' => ['audit'], 'platform' => ['platform'],
    ],
];
