ALTER TABLE outbound_clicks
    ADD COLUMN IF NOT EXISTS cta_position VARCHAR(80) NULL AFTER click_source,
    ADD COLUMN IF NOT EXISTS cta_variant VARCHAR(80) NULL AFTER cta_position,
    ADD COLUMN IF NOT EXISTS cta_label VARCHAR(180) NULL AFTER cta_variant,
    ADD INDEX IF NOT EXISTS idx_outbound_clicks_cta_position (cta_position),
    ADD INDEX IF NOT EXISTS idx_outbound_clicks_cta_variant (cta_variant);
