/**
 * Admin JavaScript for Slots Plugin
 *
 * @package Slots
 */

// Import only admin CSS
import '../css/slots-admin.css';

var SlotsAdmin = {

    init: function() {
        this.bindEvents();
        this.initComponents();
    },

    bindEvents: function() {
        // Meta box field changes
        document.addEventListener('change', function(e) {
            if (e.target.matches('.slots-meta-field input, .slots-meta-field select')) {
                SlotsAdmin.handleMetaFieldChange(e.target);
            }
        });

        // Quick edit functionality
        document.addEventListener('click', function(e) {
            if (e.target.matches('.quick-edit-slot')) {
                e.preventDefault();
                var slotId = e.target.dataset.slotId;
                SlotsAdmin.openQuickEdit(slotId);
            }
        });

        // Bulk actions
        document.addEventListener('change', function(e) {
            if (e.target.matches('#bulk-action-selector-top, #bulk-action-selector-bottom')) {
                SlotsAdmin.handleBulkActionChange(e.target);
            }
        });

        // Settings page functionality
        document.addEventListener('change', function(e) {
            if (e.target.matches('.slots-settings-field input[type="checkbox"]')) {
                SlotsAdmin.handleSettingChange(e.target);
            }
        });

        // Dashboard widget refresh
        document.addEventListener('click', function(e) {
            if (e.target.matches('.refresh-slots-stats')) {
                e.preventDefault();
                SlotsAdmin.refreshStats();
            }
        });

        // Post type filters
        document.addEventListener('change', function(e) {
            if (e.target.matches('.slots-filter select')) {
                SlotsAdmin.filterSlots(e.target);
            }
        });

        // Date picker initialization
        document.addEventListener('focus', function(e) {
            if (e.target.matches('.slots-date-picker') && !e.target.classList.contains('ui-datepicker')) {
                SlotsAdmin.initDatePicker(e.target);
            }
        });
    },

    initComponents: function() {
        // Initialize tooltips
        this.initTooltips();

        // Initialize sortable tables
        this.initSortableTables();

        // Initialize color pickers
        this.initColorPickers();
    },

    handleMetaFieldChange: function(field) {
        var fieldName = field.name;
        var fieldValue = field.value;
        var postId = document.getElementById('post_ID') ? document.getElementById('post_ID').value : '';

        // Auto-save meta field
        this.autoSaveMetaField(postId, fieldName, fieldValue);

        // Update related fields if needed
        this.updateRelatedFields(fieldName, fieldValue);
    },

    autoSaveMetaField: function(postId, fieldName, fieldValue) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        SlotsAdmin.showNotice('Field updated successfully.', 'success');
                    }
                } catch (e) {
                    SlotsAdmin.showNotice('Failed to update field.', 'error');
                }
            } else {
                SlotsAdmin.showNotice('Failed to update field.', 'error');
            }
        };

        xhr.onerror = function() {
            SlotsAdmin.showNotice('Failed to update field.', 'error');
        };

        var data = 'action=slots_auto_save_meta&post_id=' + encodeURIComponent(postId) +
            '&field_name=' + encodeURIComponent(fieldName) +
            '&field_value=' + encodeURIComponent(fieldValue) +
            '&nonce=' + encodeURIComponent(slots_admin.nonce);
        xhr.send(data);
    },

    updateRelatedFields: function(fieldName, fieldValue) {
        // Example: Update duration field when time changes
        if (fieldName === '_slot_time') {
            this.calculateDuration(fieldValue);
        }

        // Example: Update status when availability changes
        if (fieldName === '_slot_available') {
            this.updateStatusIndicator(fieldValue);
        }
    },

    calculateDuration: function(timeValue) {
        // Simple duration calculation logic
        var durationField = document.querySelector('input[name="_slot_duration"]');
        if (durationField && timeValue) {
            // Default duration of 60 minutes
            durationField.value = '60';
        }
    },

    updateStatusIndicator: function(available) {
        var statusField = document.querySelector('.slots-status-indicator');
        if (statusField) {
            if (available === '1') {
                statusField.classList.remove('booked');
                statusField.classList.add('available');
                statusField.textContent = 'Available';
            } else {
                statusField.classList.remove('available');
                statusField.classList.add('booked');
                statusField.textContent = 'Booked';
            }
        }
    },

    openQuickEdit: function(slotId) {
        // Load slot data for quick edit
        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        SlotsAdmin.populateQuickEditForm(response.data);
                    }
                } catch (e) {
                    // Failed to parse response
                }
            }
        };

        var data = 'action=slots_get_slot_data&slot_id=' + encodeURIComponent(slotId) +
            '&nonce=' + encodeURIComponent(slots_admin.nonce);
        xhr.send(data);
    },

    populateQuickEditForm: function(slotData) {
        // Populate quick edit form fields
        var timeField = document.getElementById('quick-edit-slot-time');
        var durationField = document.getElementById('quick-edit-slot-duration');
        var statusField = document.getElementById('quick-edit-slot-status');
        var form = document.getElementById('quick-edit-slot-form');

        if (timeField) timeField.value = slotData.time;
        if (durationField) durationField.value = slotData.duration;
        if (statusField) statusField.value = slotData.status;

        // Show quick edit form
        if (form) form.style.display = 'block';
    },

    handleBulkActionChange: function(selector) {
        var action = selector.value;
        var submitButton = selector.closest('.tablenav').querySelector('.button');

        if (action && action !== '-1') {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    },

    handleSettingChange: function(checkbox) {
        var settingName = checkbox.name;
        var settingValue = checkbox.checked ? '1' : '0';

        // Auto-save setting
        this.autoSaveSetting(settingName, settingValue);
    },

    autoSaveSetting: function(settingName, settingValue) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        SlotsAdmin.showNotice('Setting updated successfully.', 'success');
                    }
                } catch (e) {
                    // Failed to parse response
                }
            }
        };

        var data = 'action=slots_auto_save_setting&setting_name=' + encodeURIComponent(settingName) +
            '&setting_value=' + encodeURIComponent(settingValue) +
            '&nonce=' + encodeURIComponent(slots_admin.nonce);
        xhr.send(data);
    },

    refreshStats: function() {
        var statsContainer = document.querySelector('.slots-stats-grid');
        var refreshButton = document.querySelector('.refresh-slots-stats');

        if (refreshButton) {
            refreshButton.disabled = true;
            refreshButton.textContent = 'Refreshing...';
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        SlotsAdmin.updateStatsDisplay(response.data);
                        SlotsAdmin.showNotice('Statistics updated successfully.', 'success');
                    }
                } catch (e) {
                    SlotsAdmin.showNotice('Failed to refresh statistics.', 'error');
                }
            } else {
                SlotsAdmin.showNotice('Failed to refresh statistics.', 'error');
            }

            if (refreshButton) {
                refreshButton.disabled = false;
                refreshButton.textContent = 'Refresh Stats';
            }
        };

        xhr.onerror = function() {
            SlotsAdmin.showNotice('Failed to refresh statistics.', 'error');
            if (refreshButton) {
                refreshButton.disabled = false;
                refreshButton.textContent = 'Refresh Stats';
            }
        };

        var data = 'action=slots_refresh_stats&nonce=' + encodeURIComponent(slots_admin.nonce);
        xhr.send(data);
    },

    updateStatsDisplay: function(stats) {
        // Update each stat card
        var statCards = document.querySelectorAll('.slots-stat-card');
        statCards.forEach(function(card) {
            var titleElement = card.querySelector('h3');
            var statNumberElement = card.querySelector('.stat-number');

            if (titleElement && statNumberElement) {
                var statType = titleElement.textContent.toLowerCase().replace(/\s+/g, '_');

                if (stats[statType] !== undefined) {
                    statNumberElement.textContent = stats[statType];
                }
            }
        });
    },

    filterSlots: function(filterSelect) {
        var filterValue = filterSelect.value;
        var filterType = filterSelect.dataset.filterType;

        // Reload page with filter parameters
        var currentUrl = new URL(window.location);
        currentUrl.searchParams.set(filterType, filterValue);
        window.location.href = currentUrl.toString();
    },

    initDatePicker: function(field) {
        // Check if jQuery UI datepicker is available
        if (typeof field.datepicker === 'function') {
            field.datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '-10:+10',
                showButtonPanel: true,
                closeText: 'Close',
                currentText: 'Today',
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ],
                monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ],
                dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']
            });
        } else {
            // Fallback to native date input if jQuery UI is not available
            field.type = 'date';
        }
    },

    initTooltips: function() {
        // Check if jQuery UI tooltip is available
        var tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(function(element) {
            if (typeof element.tooltip === 'function') {
                element.tooltip({
                    position: { my: 'left+5 center', at: 'right center' },
                    show: { effect: 'fadeIn', duration: 200 },
                    hide: { effect: 'fadeOut', duration: 200 }
                });
            } else {
                // Fallback to native title attribute
                element.title = element.dataset.tooltip;
            }
        });
    },

    initSortableTables: function() {
        // Check if tablesorter plugin is available
        var sortableTables = document.querySelectorAll('.slots-sortable-table');
        sortableTables.forEach(function(table) {
            if (typeof table.tablesorter === 'function') {
                table.tablesorter({
                    sortList: [
                        [0, 0]
                    ],
                    headers: {
                        0: { sorter: 'text' },
                        1: { sorter: 'text' },
                        2: { sorter: 'text' },
                        3: { sorter: 'text' }
                    }
                });
            }
        });
    },

    initColorPickers: function() {
        // Check if WordPress color picker is available
        var colorPickers = document.querySelectorAll('.slots-color-picker');
        colorPickers.forEach(function(picker) {
            if (typeof picker.wpColorPicker === 'function') {
                picker.wpColorPicker();
            }
        });
    },

    showNotice: function(message, type) {
        var noticeClass = 'notice notice-' + type;
        var noticeHtml = '<div class="' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>';

        // Remove existing notices
        var existingNotices = document.querySelectorAll('.slots-notice');
        existingNotices.forEach(function(notice) {
            notice.remove();
        });

        // Add new notice
        var wrapHeader = document.querySelector('.wrap h1');
        if (wrapHeader) {
            var noticeElement = document.createElement('div');
            noticeElement.innerHTML = noticeHtml;
            var notice = noticeElement.firstElementChild;
            notice.classList.add('slots-notice');
            wrapHeader.parentNode.insertBefore(notice, wrapHeader.nextSibling);

            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                if (notice && notice.parentNode) {
                    notice.style.opacity = '0';
                    notice.style.transition = 'opacity 0.5s';
                    setTimeout(function() {
                        if (notice && notice.parentNode) {
                            notice.remove();
                        }
                    }, 500);
                }
            }, 5000);
        }
    },

    // Utility functions
    formatDate: function(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString();
    },

    formatTime: function(timeString) {
        return timeString;
    },

    validateTimeFormat: function(timeString) {
        var timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
        return timeRegex.test(timeString);
    },

    validateDuration: function(duration) {
        return !isNaN(duration) && duration > 0 && duration <= 1440; // Max 24 hours
    }
};

// Initialize plugin when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        SlotsAdmin.init();
    });
} else {
    SlotsAdmin.init();
}

// Make plugin globally accessible
window.SlotsAdmin = SlotsAdmin;