-- Add new fields for Saturday half-day settings to company_rules table
ALTER TABLE `company_rules`
ADD COLUMN `saturday_half_day_enabled` TINYINT(1) DEFAULT 0 AFTER `saturday_pay_type`,
ADD COLUMN `saturday_half_day_hours` FLOAT DEFAULT 4.0 AFTER `saturday_half_day_enabled`;

-- Update any existing records to set default values
UPDATE `company_rules` SET `saturday_half_day_enabled` = 0, `saturday_half_day_hours` = 4.0;
