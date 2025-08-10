/**
 * Admin JavaScript for Slots Plugin
 *
 * @package Slots
 */

(function($) {
    'use strict';

    // Slots Admin Plugin Class
    var SlotsAdmin = {

        init: function() {
            this.bindEvents();
            this.initComponents();
        },

        bindEvents: function() {
            // Meta box field changes
            $(document).on('change', '.slots-meta-field input, .slots-meta-field select', function() {
                SlotsAdmin.handleMetaFieldChange($(this));
            });

            // Quick edit functionality
            $(document).on('click', '.quick-edit-slot', function(e) {
                e.preventDefault();
                var slotId = $(this).data('slot-id');
                SlotsAdmin.openQuickEdit(slotId);
            });

            // Bulk actions
            $(document).on('change', '#bulk-action-selector-top, #bulk-action-selector-bottom', function() {
                SlotsAdmin.handleBulkActionChange($(this));
            });

            // Settings page functionality
            $(document).on('change', '.slots-settings-field input[type="checkbox"]', function() {
                SlotsAdmin.handleSettingChange($(this));
            });

            // Dashboard widget refresh
            $(document).on('click', '.refresh-slots-stats', function(e) {
                e.preventDefault();
                SlotsAdmin.refreshStats();
            });

            // Post type filters
            $(document).on('change', '.slots-filter select', function() {
                SlotsAdmin.filterSlots($(this));
            });

            // Date picker initialization
            $(document).on('focus', '.slots-date-picker', function() {
                if (!$(this).hasClass('ui-datepicker')) {
                    SlotsAdmin.initDatePicker($(this));
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
            var fieldName = field.attr('name');
            var fieldValue = field.val();
            var postId = $('#post_ID').val();

            // Auto-save meta field
            this.autoSaveMetaField(postId, fieldName, fieldValue);

            // Update related fields if needed
            this.updateRelatedFields(fieldName, fieldValue);
        },

        autoSaveMetaField: function(postId, fieldName, fieldValue) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'slots_auto_save_meta',
                    post_id: postId,
                    field_name: fieldName,
                    field_value: fieldValue,
                    nonce: slots_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        SlotsAdmin.showNotice('Field updated successfully.', 'success');
                    }
                },
                error: function() {
                    SlotsAdmin.showNotice('Failed to update field.', 'error');
                }
            });
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
            var durationField = $('input[name="_slot_duration"]');
            if (durationField.length && timeValue) {
                // Default duration of 60 minutes
                durationField.val('60');
            }
        },

        updateStatusIndicator: function(available) {
            var statusField = $('.slots-status-indicator');
            if (statusField.length) {
                if (available === '1') {
                    statusField.removeClass('booked').addClass('available').text('Available');
                } else {
                    statusField.removeClass('available').addClass('booked').text('Booked');
                }
            }
        },

        openQuickEdit: function(slotId) {
            // Load slot data for quick edit
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'slots_get_slot_data',
                    slot_id: slotId,
                    nonce: slots_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        SlotsAdmin.populateQuickEditForm(response.data);
                    }
                }
            });
        },

        populateQuickEditForm: function(slotData) {
            // Populate quick edit form fields
            $('#quick-edit-slot-time').val(slotData.time);
            $('#quick-edit-slot-duration').val(slotData.duration);
            $('#quick-edit-slot-status').val(slotData.status);

            // Show quick edit form
            $('#quick-edit-slot-form').show();
        },

        handleBulkActionChange: function(selector) {
            var action = selector.val();
            var submitButton = selector.closest('.tablenav').find('.button');

            if (action && action !== '-1') {
                submitButton.prop('disabled', false);
            } else {
                submitButton.prop('disabled', true);
            }
        },

        handleSettingChange: function(checkbox) {
            var settingName = checkbox.attr('name');
            var settingValue = checkbox.is(':checked') ? '1' : '0';

            // Auto-save setting
            this.autoSaveSetting(settingName, settingValue);
        },

        autoSaveSetting: function(settingName, settingValue) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'slots_auto_save_setting',
                    setting_name: settingName,
                    setting_value: settingValue,
                    nonce: slots_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        SlotsAdmin.showNotice('Setting updated successfully.', 'success');
                    }
                }
            });
        },

        refreshStats: function() {
            var statsContainer = $('.slots-stats-grid');
            var refreshButton = $('.refresh-slots-stats');

            refreshButton.prop('disabled', true).text('Refreshing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'slots_refresh_stats',
                    nonce: slots_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        SlotsAdmin.updateStatsDisplay(response.data);
                        SlotsAdmin.showNotice('Statistics updated successfully.', 'success');
                    }
                },
                error: function() {
                    SlotsAdmin.showNotice('Failed to refresh statistics.', 'error');
                },
                complete: function() {
                    refreshButton.prop('disabled', false).text('Refresh Stats');
                }
            });
        },

        updateStatsDisplay: function(stats) {
            // Update each stat card
            $('.slots-stat-card').each(function() {
                var card = $(this);
                var statType = card.find('h3').text().toLowerCase().replace(/\s+/g, '_');

                if (stats[statType] !== undefined) {
                    card.find('.stat-number').text(stats[statType]);
                }
            });
        },

        filterSlots: function(filterSelect) {
            var filterValue = filterSelect.val();
            var filterType = filterSelect.data('filter-type');

            // Reload page with filter parameters
            var currentUrl = new URL(window.location);
            currentUrl.searchParams.set(filterType, filterValue);
            window.location.href = currentUrl.toString();
        },

        initDatePicker: function(field) {
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
        },

        initTooltips: function() {
            $('[data-tooltip]').tooltip({
                position: { my: 'left+5 center', at: 'right center' },
                show: { effect: 'fadeIn', duration: 200 },
                hide: { effect: 'fadeOut', duration: 200 }
            });
        },

        initSortableTables: function() {
            $('.slots-sortable-table').tablesorter({
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
        },

        initColorPickers: function() {
            $('.slots-color-picker').wpColorPicker();
        },

        showNotice: function(message, type) {
            var noticeClass = 'notice notice-' + type;
            var noticeHtml = '<div class="' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>';

            // Remove existing notices
            $('.slots-notice').remove();

            // Add new notice
            $('.wrap h1').after(noticeHtml);

            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $('.slots-notice').fadeOut();
            }, 5000);
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
    $(document).ready(function() {
        SlotsAdmin.init();
    });

    // Make plugin globally accessible
    window.SlotsAdmin = SlotsAdmin;

})(jQuery);