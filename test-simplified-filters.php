<?php
/**
 * Test file for Simplified Slot Grid Filters
 * 
 * This file demonstrates the simplified slot grid shortcode with only two filters:
 * 1. Limit: Number of slots to display
 * 2. Sort: Random or Recent (by last updated)
 * 
 * Usage: Include this file in a WordPress page or post to test the functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="slots-test-container">
    <h2>🎰 Simplified Slot Grid Filters Test</h2>
    
    <p>This demonstrates the simplified slot grid with only two essential filters:</p>
    <ul>
        <li><strong>Limit:</strong> Choose how many slots to display (6, 12, 18, or 24)</li>
        <li><strong>Sort:</strong> Choose between Recent (by last updated) or Random</li>
    </ul>
    
    <h3>Basic Grid (Default: 12 slots, Recent)</h3>
    <?php echo do_shortcode('[slots_grid]'); ?>
    
    <h3>Limited Grid (6 slots, Random)</h3>
    <?php echo do_shortcode('[slots_grid limit="6" sort="random"]'); ?>
    
    <h3>Large Grid (24 slots, Recent)</h3>
    <?php echo do_shortcode('[slots_grid limit="24" sort="recent"]'); ?>
    
    <h3>Custom Grid (18 slots, Random, No pagination)</h3>
    <?php echo do_shortcode('[slots_grid limit="18" sort="random" show_pagination="false"]'); ?>
</div>

<style>
.slots-test-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.slots-test-container h2 {
    color: #3b82f6;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.slots-test-container h3 {
    color: #64748b;
    margin-top: 30px;
    margin-bottom: 15px;
}

.slots-test-container ul {
    background: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
}

.slots-test-container li {
    margin-bottom: 8px;
}
</style>
