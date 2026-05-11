CREATE TABLE IF NOT EXISTS ab_tests (
    test_key VARCHAR(80) NOT NULL PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    description TEXT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ab_tests_status (status)
);

CREATE TABLE IF NOT EXISTS ab_test_variants (
    ab_test_variant_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    test_key VARCHAR(80) NOT NULL,
    variant_key VARCHAR(80) NOT NULL,
    label VARCHAR(160) NOT NULL,
    description TEXT NULL,
    weight INT UNSIGNED NOT NULL DEFAULT 50,
    is_control TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ab_test_variants_key (test_key, variant_key),
    INDEX idx_ab_test_variants_test (test_key)
);

CREATE TABLE IF NOT EXISTS ab_test_events (
    ab_test_event_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    test_key VARCHAR(80) NOT NULL,
    variant_key VARCHAR(80) NOT NULL,
    event_type VARCHAR(40) NOT NULL,
    visitor_hash VARCHAR(128) NULL,
    source_path VARCHAR(500) NULL,
    content_translation_id BIGINT UNSIGNED NULL,
    language_code VARCHAR(10) NULL,
    metadata_json LONGTEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ab_test_events_test_variant (test_key, variant_key, event_type),
    INDEX idx_ab_test_events_created (created_at),
    INDEX idx_ab_test_events_visitor (visitor_hash)
);

ALTER TABLE discount_leads
    ADD COLUMN IF NOT EXISTS ab_test_key VARCHAR(80) NULL AFTER browser_language,
    ADD COLUMN IF NOT EXISTS ab_variant_key VARCHAR(80) NULL AFTER ab_test_key,
    ADD INDEX IF NOT EXISTS idx_discount_leads_ab_test (ab_test_key, ab_variant_key);

INSERT INTO ab_tests (test_key, title, description, status)
VALUES (
    'discount_modal_contact',
    'Popup za 15% popust: email ili mobitel',
    'Testira daje li bolji rezultat popup koji traži samo email ili popup koji traži samo broj mobitela.',
    'active'
)
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    description = VALUES(description),
    status = VALUES(status);

INSERT INTO ab_test_variants (test_key, variant_key, label, description, weight, is_control)
VALUES
    (
        'discount_modal_contact',
        'email_only',
        'Samo email',
        'Posjetitelj vidi samo email polje i nakon prijave dobiva link s popustom emailom.',
        50,
        1
    ),
    (
        'discount_modal_contact',
        'phone_only',
        'Samo mobitel',
        'Posjetitelj vidi samo polje za mobitel, a AVC sprema kontakt i odmah otvara shop.',
        50,
        0
    )
ON DUPLICATE KEY UPDATE
    label = VALUES(label),
    description = VALUES(description),
    weight = VALUES(weight),
    is_control = VALUES(is_control);
