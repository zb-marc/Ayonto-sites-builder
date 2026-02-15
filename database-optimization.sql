-- ============================================
-- Ayonto Sites Builder Database Optimization
-- Version: 0.2.0 Build 081
-- Date: November 2025
-- ============================================

-- IMPORTANT: Backup your database before running this script!
-- These indexes improve query performance for meta fields

-- Add indexes for frequently queried meta keys
-- These indexes significantly improve dashboard and filter performance

ALTER TABLE wp_postmeta 
ADD INDEX idx_vt_technology (meta_key(20), meta_value(50)) 
WHERE meta_key = 'technology';

ALTER TABLE wp_postmeta 
ADD INDEX idx_vt_brand (meta_key(20), meta_value(50)) 
WHERE meta_key = 'brand';

ALTER TABLE wp_postmeta 
ADD INDEX idx_vt_series (meta_key(20), meta_value(50)) 
WHERE meta_key = 'series';

ALTER TABLE wp_postmeta 
ADD INDEX idx_vt_voltage (meta_key(20), meta_value(20)) 
WHERE meta_key = 'voltage_v';

ALTER TABLE wp_postmeta 
ADD INDEX idx_vt_capacity (meta_key(20), meta_value(20)) 
WHERE meta_key = 'capacity_ah';

-- Composite index for complex queries
ALTER TABLE wp_postmeta 
ADD INDEX idx_vt_combined (post_id, meta_key(20), meta_value(50));

-- Clean up orphaned meta entries
DELETE pm FROM wp_postmeta pm
LEFT JOIN wp_posts p ON p.ID = pm.post_id
WHERE p.ID IS NULL;

-- Optimize tables for better performance
OPTIMIZE TABLE wp_posts;
OPTIMIZE TABLE wp_postmeta;
OPTIMIZE TABLE wp_terms;
OPTIMIZE TABLE wp_term_taxonomy;
OPTIMIZE TABLE wp_term_relationships;

-- Show index statistics
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    CARDINALITY
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('wp_postmeta', 'wp_posts')
AND INDEX_NAME LIKE 'idx_vt_%';

-- Performance check query
-- This should run much faster after indexes are added
SELECT 
    COUNT(*) as total_batteries,
    (SELECT COUNT(DISTINCT meta_value) FROM wp_postmeta WHERE meta_key = 'technology') as technologies,
    (SELECT COUNT(DISTINCT meta_value) FROM wp_postmeta WHERE meta_key = 'brand') as brands,
    (SELECT COUNT(DISTINCT meta_value) FROM wp_postmeta WHERE meta_key = 'voltage_v') as voltages
FROM wp_posts 
WHERE post_type = 'vt_battery' 
AND post_status = 'publish';

-- Success message
SELECT 'Database optimization completed successfully!' as Status;
