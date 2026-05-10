CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(120) NOT NULL PRIMARY KEY,
    setting_value_json LONGTEXT NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_users (
    admin_user_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(190) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'owner',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS languages (
    language_code VARCHAR(10) NOT NULL PRIMARY KEY,
    language_name VARCHAR(120) NOT NULL,
    locale VARCHAR(35) NOT NULL,
    url_prefix VARCHAR(32) NOT NULL DEFAULT '',
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS content_items (
    content_item_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    source_wp_post_id BIGINT UNSIGNED NULL,
    translation_group_id BIGINT UNSIGNED NULL,
    content_type VARCHAR(50) NOT NULL,
    lifecycle_status VARCHAR(50) NOT NULL DEFAULT 'draft',
    editor_template VARCHAR(80) NOT NULL DEFAULT 'article',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_content_items_type_status (content_type, lifecycle_status),
    INDEX idx_content_items_wp_post (source_wp_post_id),
    INDEX idx_content_items_translation_group (translation_group_id)
);

CREATE TABLE IF NOT EXISTS content_translations (
    content_translation_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    content_item_id BIGINT UNSIGNED NOT NULL,
    source_wp_post_id BIGINT UNSIGNED NULL,
    language_code VARCHAR(10) NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    excerpt TEXT NULL,
    body_html LONGTEXT NULL,
    body_json LONGTEXT NULL,
    summary_html MEDIUMTEXT NULL,
    faq_json LONGTEXT NULL,
    featured_image_path VARCHAR(255) NULL,
    published_at DATETIME NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_content_translations_source_wp_post (source_wp_post_id),
    INDEX idx_content_translations_language_code (language_code),
    UNIQUE KEY uq_content_translation_item_language (content_item_id, language_code),
    CONSTRAINT fk_content_translations_item
        FOREIGN KEY (content_item_id) REFERENCES content_items(content_item_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_content_translations_language
        FOREIGN KEY (language_code) REFERENCES languages(language_code)
        ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS product_recommendations (
    product_recommendation_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    content_translation_id BIGINT UNSIGNED NOT NULL,
    source_wp_post_id BIGINT UNSIGNED NULL,
    translation_group_id BIGINT UNSIGNED NULL,
    external_url VARCHAR(1000) NOT NULL,
    source_host VARCHAR(255) NULL,
    button_label VARCHAR(255) NULL,
    sku VARCHAR(120) NULL,
    stock_status VARCHAR(50) NULL,
    price DECIMAL(10,2) NULL,
    regular_price DECIMAL(10,2) NULL,
    sale_price DECIMAL(10,2) NULL,
    currency_code VARCHAR(10) NOT NULL DEFAULT 'EUR',
    destination_strategy VARCHAR(50) NOT NULL DEFAULT 'passthrough',
    market_urls_json LONGTEXT NULL,
    source_system VARCHAR(50) NOT NULL DEFAULT 'wordpress',
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_product_recommendations_translation (content_translation_id),
    INDEX idx_product_recommendations_wp_post (source_wp_post_id),
    INDEX idx_product_recommendations_translation_group (translation_group_id),
    CONSTRAINT fk_product_recommendations_translation
        FOREIGN KEY (content_translation_id) REFERENCES content_translations(content_translation_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS product_market_overrides (
    product_market_override_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    translation_group_id BIGINT UNSIGNED NOT NULL,
    market_code VARCHAR(10) NOT NULL,
    destination_url VARCHAR(1000) NOT NULL,
    updated_by_admin_user_id BIGINT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_product_market_overrides_group_market (translation_group_id, market_code),
    INDEX idx_product_market_overrides_group (translation_group_id),
    INDEX idx_product_market_overrides_admin (updated_by_admin_user_id),
    CONSTRAINT fk_product_market_overrides_admin
        FOREIGN KEY (updated_by_admin_user_id) REFERENCES admin_users(admin_user_id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS content_routes (
    content_route_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    language_code VARCHAR(10) NOT NULL,
    route_path VARCHAR(500) NOT NULL,
    content_translation_id BIGINT UNSIGNED NULL,
    route_type VARCHAR(50) NOT NULL DEFAULT 'content',
    http_status_code SMALLINT UNSIGNED NOT NULL DEFAULT 200,
    redirect_target_path VARCHAR(500) NULL,
    source_system VARCHAR(50) NOT NULL DEFAULT 'wordpress',
    is_primary TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_content_routes_path (route_path),
    INDEX idx_content_routes_translation (content_translation_id),
    CONSTRAINT fk_content_routes_language
        FOREIGN KEY (language_code) REFERENCES languages(language_code)
        ON DELETE RESTRICT,
    CONSTRAINT fk_content_routes_translation
        FOREIGN KEY (content_translation_id) REFERENCES content_translations(content_translation_id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS seo_metadata (
    seo_metadata_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    content_translation_id BIGINT UNSIGNED NOT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    canonical_url VARCHAR(500) NULL,
    robots_index TINYINT(1) NOT NULL DEFAULT 1,
    robots_follow TINYINT(1) NOT NULL DEFAULT 1,
    breadcrumb_title VARCHAR(255) NULL,
    focus_keyword VARCHAR(255) NULL,
    open_graph_title VARCHAR(255) NULL,
    open_graph_description TEXT NULL,
    open_graph_image_path VARCHAR(255) NULL,
    schema_json LONGTEXT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_seo_metadata_translation (content_translation_id),
    CONSTRAINT fk_seo_metadata_translation
        FOREIGN KEY (content_translation_id) REFERENCES content_translations(content_translation_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS referral_settings_history (
    referral_settings_history_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    active_forever_id VARCHAR(120) NOT NULL,
    changed_by_admin_user_id BIGINT UNSIGNED NULL,
    change_note VARCHAR(255) NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_referral_history_changed_at (changed_at),
    CONSTRAINT fk_referral_history_admin
        FOREIGN KEY (changed_by_admin_user_id) REFERENCES admin_users(admin_user_id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS outbound_clicks (
    outbound_click_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    content_translation_id BIGINT UNSIGNED NULL,
    source_path VARCHAR(500) NOT NULL,
    destination_url VARCHAR(1000) NOT NULL,
    destination_market_code VARCHAR(10) NULL,
    forever_id_used VARCHAR(120) NULL,
    language_code VARCHAR(10) NULL,
    country_code VARCHAR(10) NULL,
    city_name VARCHAR(120) NULL,
    browser_language VARCHAR(50) NULL,
    click_source VARCHAR(50) NOT NULL DEFAULT 'content_cta',
    cta_position VARCHAR(80) NULL,
    cta_variant VARCHAR(80) NULL,
    cta_label VARCHAR(180) NULL,
    utm_source VARCHAR(120) NULL,
    utm_medium VARCHAR(120) NULL,
    utm_campaign VARCHAR(120) NULL,
    visitor_hash VARCHAR(128) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_outbound_clicks_created_at (created_at),
    INDEX idx_outbound_clicks_market (destination_market_code),
    INDEX idx_outbound_clicks_country (country_code),
    INDEX idx_outbound_clicks_translation (content_translation_id),
    INDEX idx_outbound_clicks_cta_position (cta_position),
    INDEX idx_outbound_clicks_cta_variant (cta_variant),
    CONSTRAINT fk_outbound_clicks_translation
        FOREIGN KEY (content_translation_id) REFERENCES content_translations(content_translation_id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS advisor_conversations (
    advisor_conversation_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    conversation_public_id VARCHAR(80) NOT NULL,
    visitor_key VARCHAR(120) NOT NULL,
    content_translation_id BIGINT UNSIGNED NULL,
    language_code VARCHAR(10) NOT NULL,
    country_code VARCHAR(10) NULL,
    source_path VARCHAR(500) NOT NULL,
    source_type VARCHAR(50) NOT NULL DEFAULT 'page',
    source_title VARCHAR(255) NULL,
    preferred_market_code VARCHAR(10) NULL,
    lead_status VARCHAR(50) NOT NULL DEFAULT 'open',
    last_user_message_at DATETIME NULL,
    last_assistant_message_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_advisor_conversations_public (conversation_public_id),
    INDEX idx_advisor_conversations_visitor_source (visitor_key, source_path),
    INDEX idx_advisor_conversations_created (created_at),
    CONSTRAINT fk_advisor_conversations_translation
        FOREIGN KEY (content_translation_id) REFERENCES content_translations(content_translation_id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS advisor_messages (
    advisor_message_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    advisor_conversation_id BIGINT UNSIGNED NOT NULL,
    message_public_id VARCHAR(80) NOT NULL,
    role VARCHAR(20) NOT NULL,
    body_text LONGTEXT NOT NULL,
    payload_json LONGTEXT NULL,
    feedback_value VARCHAR(20) NULL,
    feedback_note TEXT NULL,
    feedback_created_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_advisor_messages_public (message_public_id),
    INDEX idx_advisor_messages_conversation_created (advisor_conversation_id, created_at),
    CONSTRAINT fk_advisor_messages_conversation
        FOREIGN KEY (advisor_conversation_id) REFERENCES advisor_conversations(advisor_conversation_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ai_leads (
    ai_lead_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    content_translation_id BIGINT UNSIGNED NULL,
    advisor_session_key VARCHAR(120) NOT NULL,
    conversation_public_id VARCHAR(80) NULL,
    language_code VARCHAR(10) NOT NULL,
    country_code VARCHAR(10) NULL,
    name VARCHAR(190) NULL,
    email VARCHAR(190) NULL,
    phone VARCHAR(80) NULL,
    lead_question TEXT NOT NULL,
    source_path VARCHAR(500) NOT NULL,
    recommended_content_path VARCHAR(500) NULL,
    recommended_market_code VARCHAR(10) NULL,
    lead_status VARCHAR(50) NOT NULL DEFAULT 'new',
    admin_notified_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ai_leads_status_created (lead_status, created_at),
    INDEX idx_ai_leads_language_country (language_code, country_code),
    INDEX idx_ai_leads_conversation_public (conversation_public_id),
    CONSTRAINT fk_ai_leads_translation
        FOREIGN KEY (content_translation_id) REFERENCES content_translations(content_translation_id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS discount_leads (
    discount_lead_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    content_translation_id BIGINT UNSIGNED NULL,
    discount_token VARCHAR(80) NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    country_code VARCHAR(10) NULL,
    market_code VARCHAR(10) NULL,
    name VARCHAR(190) NULL,
    email VARCHAR(190) NULL,
    phone VARCHAR(80) NULL,
    consent_contact TINYINT(1) NOT NULL DEFAULT 0,
    product_title VARCHAR(255) NULL,
    source_path VARCHAR(500) NOT NULL,
    destination_url VARCHAR(1200) NOT NULL,
    fallback_url VARCHAR(1200) NULL,
    lead_status VARCHAR(50) NOT NULL DEFAULT 'new',
    admin_notified_at DATETIME NULL,
    customer_notified_at DATETIME NULL,
    visitor_hash VARCHAR(128) NULL,
    browser_language VARCHAR(120) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_discount_leads_token (discount_token),
    INDEX idx_discount_leads_status_created (lead_status, created_at),
    INDEX idx_discount_leads_language_country (language_code, country_code),
    INDEX idx_discount_leads_contact (email, phone),
    INDEX idx_discount_leads_translation (content_translation_id),
    CONSTRAINT fk_discount_leads_translation
        FOREIGN KEY (content_translation_id) REFERENCES content_translations(content_translation_id)
        ON DELETE SET NULL
);

INSERT INTO languages (language_code, language_name, locale, url_prefix, is_default, is_active)
VALUES
    ('hr', 'Croatian', 'hr', '', 1, 1),
    ('en', 'English', 'en_US', 'en', 0, 1),
    ('sl', 'Slovenian', 'sl_SI', 'sl', 0, 1)
ON DUPLICATE KEY UPDATE
    language_name = VALUES(language_name),
    locale = VALUES(locale),
    url_prefix = VALUES(url_prefix),
    is_default = VALUES(is_default),
    is_active = VALUES(is_active);

INSERT INTO settings (setting_key, setting_value_json)
VALUES
    ('platform', JSON_OBJECT('app_name', 'AVC Platform', 'base_url', 'https://aloevera-centar.com', 'default_language', 'hr')),
    ('referral', JSON_OBJECT(
        'active_forever_id', '',
        'admin_notification_email', 'admin@example.com',
        'fcc_discount_enabled', true,
        'fcc_discount_percent', 15,
        'fcc_short_url', 'https://thealoeveraco.shop/wf8afIMZ',
        'fcc_shorten_url', 'thealoeveraco.shop/wf8afIMZ',
        'fcc_referral_uuid', '7073bc6f-4b23-4219-99cd-a7a109e23835',
        'fcc_unique_ext_ref_id', '6e568a04-f257-4f77-97bf-a2f4c20c3566',
        'fcc_discount_config_type', '11',
        'fcc_title', 'FCC'
    ))
ON DUPLICATE KEY UPDATE
    setting_value_json = setting_value_json;
