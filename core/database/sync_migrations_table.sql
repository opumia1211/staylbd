-- Sync migrations table for existing database (XAMPP)
-- Run this ONCE so Laravel thinks all old migrations have already run.
-- Then run only the new category migration (see MIGRATE_INSTRUCTIONS.txt).

-- Use your actual database name if different (e.g. staylbd, wintersm_tt, etc.)
-- INSERT INTO migrations (migration, batch) VALUES
-- ('2014_10_12_000000_create_users_table', 1),
-- ...

-- Below: all migrations EXCEPT 2026_02_12_100000_add_publish_status_and_scheduled_at_to_categories_table
-- Run this ONCE. Then run: php artisan migrate --force

INSERT INTO migrations (migration, batch) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2019_08_19_000000_create_failed_jobs_table', 1),
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2022_02_26_061836_create_forms_table', 1),
('2023_02_22_095338_create_user_tokens_table', 1),
('2023_02_22_101032_create_tokens_table', 1),
('2023_02_23_144521_create_brands_table', 1),
('2023_02_23_162048_create_categories_table', 1),
('2023_02_25_092916_create_subcategories_table', 1),
('2023_02_25_104148_create_coupons_table', 1),
('2023_02_25_134428_create_products_table', 1),
('2023_02_25_140858_create_product_galleries_table', 1),
('2023_02_26_140953_create_reviews_table', 1),
('2023_02_26_160717_create_orders_table', 1),
('2023_02_27_094248_create_wishlists_table', 1),
('2023_02_27_121428_create_carts_table', 1),
('2023_02_27_135749_create_shipping_methods_table', 1),
('2023_02_28_132511_create_order_details_table', 1),
('2024_01_01_000000_create_courierapis_table', 1),
('2025_01_31_000000_add_logo_effect_columns_to_general_settings', 1),
('2025_02_04_000000_create_admin_activity_logs_table', 1),
('2025_02_04_000001_add_channel_to_support_tickets_table', 1),
('2025_02_04_100000_create_banner_analytics_table', 1),
('2025_02_04_100001_create_conversations_table', 1),
('2025_02_04_100002_create_omnichannel_messages_table', 1),
('2025_02_04_100003_create_message_channels_table', 1),
('2025_02_04_100004_create_message_templates_table', 1),
('2025_02_04_100005_create_auto_responses_table', 1),
('2025_02_04_100006_create_chat_assignments_table', 1),
('2025_02_04_100007_create_internal_notes_table', 1),
('2025_02_04_100008_create_message_status_logs_table', 1),
('2025_02_04_100009_create_omnichannel_message_attachments_table', 1),
('2025_02_05_000000_add_age_to_users_table', 1),
('2025_02_05_100000_add_floating_auth_to_general_settings', 1),
('2025_10_16_000000_add_social_provider_columns_to_users_table', 1),
('2026_02_05_191214_add_loyalty_features_to_tables', 1),
('2026_02_05_200022_create_loyalty_transactions_table', 1),
('2026_02_05_201755_create_product_comparisons_table', 1),
('2026_02_08_193500_create_product_variants_system', 1),
('2026_02_09_000001_add_admin_online_to_general_settings', 1),
('2026_02_09_100000_add_product_video_and_user_gender', 1),
('2026_02_11_000000_add_click_url_to_notification_logs_table', 1),
('2026_02_11_000001_create_contact_channel_integrations_table', 1),
('2026_02_11_000002_create_contact_channel_messages_table', 1),
('2026_02_11_000003_add_contact_handles_to_users_table', 1),
('2026_02_11_000004_add_channel_reference_to_support_tickets_table', 1),
('2026_02_11_100000_add_keywords_and_name_to_auto_responses', 1),
('2026_02_11_110000_add_is_public_to_auto_responses', 1),
('2026_02_11_120000_create_admin_reports_table', 1);
