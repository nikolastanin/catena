/**
 * Public JavaScript for Slots Plugin
 *
 * @package Slots
 */

(function($) {
    'use strict';

    var Slots = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            $(document).on('change', '.slots-sort-select', this.handleSortChange);
            $(document).on('change', '.slots-limit-select', this.handleLimitChange);
            $(document).on('click', '.load-more-slots', this.handleLoadMore);
        },

        /**
         * Handle sort change
         */
        handleSortChange: function() {
            var select = $(this);
            var container = select.closest('.slots-container');
            var sort = select.val();
            var limit = container.find('.slots-limit-select').val();

            Slots.reloadSlots(container, { sort: sort, limit: limit });
        },

        /**
         * Handle limit change
         */
        handleLimitChange: function() {
            var select = $(this);
            var container = select.closest('.slots-container');
            var limit = select.val();
            var sort = container.find('.slots-sort-select').val();

            // Update container data
            container.data('limit', limit);

            // Update load more button data
            container.find('.load-more-slots').data('limit', limit).data('page', 1);

            Slots.reloadSlots(container, { sort: sort, limit: limit });
        },

        /**
         * Reload slots with new filters
         */
        reloadSlots: function(container, filters) {
            var grid = container.find('.slots-grid');

            // Show loading state
            grid.html('<div class="slots-loading">Loading...</div>');

            // Make AJAX request
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'load_more_slots',
                    nonce: container.find('.slots-grid').data('nonce'),
                    page: 1,
                    limit: parseInt(filters.limit),
                    sort: filters.sort
                },
                success: function(response) {
                    if (response.success) {
                        grid.html(response.data.html);

                        // Show/hide load more button
                        var loadMoreBtn = container.find('.load-more-slots');
                        if (response.data.has_more) {
                            loadMoreBtn.show().data('page', 1);
                        } else {
                            loadMoreBtn.hide();
                        }
                    } else {
                        grid.html('<div class="slots-error">Error loading slots. Please try again.</div>');
                    }
                },
                error: function() {
                    grid.html('<div class="slots-error">Error loading slots. Please try again.</div>');
                }
            });
        },

        /**
         * Load more slots
         */
        loadMoreSlots: function(container, page, callback) {
            var sort = container.find('.slots-sort-select').val();
            var limit = parseInt(container.find('.slots-limit-select').val());

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'load_more_slots',
                    nonce: container.find('.slots-grid').data('nonce'),
                    page: page,
                    limit: limit,
                    sort: sort
                },
                success: function(response) {
                    if (response.success) {
                        container.find('.slots-grid').append(response.data.html);
                        callback(response.data.has_more);
                    } else {
                        callback(false);
                    }
                },
                error: function() {
                    callback(false);
                }
            });
        },

        /**
         * Handle load more
         */
        handleLoadMore: function(e) {
            e.preventDefault();

            var button = $(this);
            var container = button.closest('.slots-container');
            var currentPage = parseInt(button.data('page'));
            var nextPage = currentPage + 1;

            // Disable button and show loading
            button.prop('disabled', true).text('Loading...');

            // Load more slots
            Slots.loadMoreSlots(container, nextPage, function(hasMore) {
                button.data('page', nextPage);
                button.prop('disabled', false);

                if (!hasMore) {
                    button.hide();
                } else {
                    button.text('Load More Slots');
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        Slots.init();
    });

    // Make Slots available globally
    window.Slots = Slots;

})(jQuery);