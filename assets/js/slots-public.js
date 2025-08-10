/**
 * Public JavaScript for Slots Plugin
 *
 * @package Slots
 */

(function($) {
    'use strict';

    // Slots Plugin Class
    var SlotsPlugin = {

        init: function() {
            this.bindEvents();
            this.initModals();
        },

        bindEvents: function() {
            // Load more slots
            $(document).on('click', '.load-more-slots', function(e) {
                e.preventDefault();
                var page = $(this).data('page');
                SlotsPlugin.loadMoreSlots(page);
            });
        },

        initModals: function() {
            // Initialize notification
            this.notification = $('#slots-notification');
        },



        loadMoreSlots: function(page) {
            var loadMoreBtn = $('.load-more-slots');

            loadMoreBtn.prop('disabled', true).text('Loading...');

            $.ajax({
                url: slots_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'slots_action',
                    action_type: 'get_slots',
                    page: page + 1,
                    nonce: slots_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        SlotsPlugin.appendSlots(response.data);
                        loadMoreBtn.data('page', page + 1);
                    } else {
                        loadMoreBtn.hide();
                    }
                },
                error: function() {
                    SlotsPlugin.showNotification('Failed to load more slots.', 'error');
                },
                complete: function() {
                    loadMoreBtn.prop('disabled', false).text('Load More Slots');
                }
            });
        },

        appendSlots: function(slots) {
            var slotsList = $('.slots-list');

            slots.forEach(function(slot) {
                var slotHtml = SlotsPlugin.createSlotHtml(slot);
                slotsList.append(slotHtml);
            });
        },

        createSlotHtml: function(slot) {
            return '<div class="slot-item" data-slot-id="' + slot.id + '">' +
                '<div class="slot-time">' +
                '<span class="time-label">Time:</span>' +
                '<span class="time-value">' + slot.time + '</span>' +
                '</div>' +
                '</div>';
        },

        showNotification: function(message, type) {
            this.notification
                .removeClass()
                .addClass('slots-notification ' + type)
                .text(message)
                .show();

            // Auto-hide after 5 seconds
            setTimeout(function() {
                SlotsPlugin.notification.fadeOut();
            }, 5000);
        }
    };

    // Initialize plugin when DOM is ready
    $(document).ready(function() {
        SlotsPlugin.init();
    });

    // Make plugin globally accessible
    window.SlotsPlugin = SlotsPlugin;

})(jQuery);