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

ALTER TABLE ai_leads
    ADD COLUMN IF NOT EXISTS conversation_public_id VARCHAR(80) NULL AFTER advisor_session_key,
    ADD INDEX IF NOT EXISTS idx_ai_leads_conversation_public (conversation_public_id);
