/**
 * Public JavaScript for Slots Plugin
 *
 * @package Slots
 */

(function() {
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
            document.addEventListener('change', this.handleSortChange.bind(this));
            document.addEventListener('change', this.handleLimitChange.bind(this));
            document.addEventListener('click', this.handleLoadMore.bind(this));
        },

        /**
         * Handle sort change
         */
        handleSortChange: function(event) {
            if (!event.target.classList.contains('slots-sort-select')) return;

            var select = event.target;
            var container = this.findClosest(select, '.slots-container');
            var sort = select.value;
            var limitSelect = container.querySelector('.slots-limit-select');
            var limit = limitSelect ? limitSelect.value : '10';

            this.reloadSlots(container, { sort: sort, limit: limit });
        },

        /**
         * Handle limit change
         */
        handleLimitChange: function(event) {
            if (!event.target.classList.contains('slots-limit-select')) return;

            var select = event.target;
            var container = this.findClosest(select, '.slots-container');
            var limit = select.value;
            var sortSelect = container.querySelector('.slots-sort-select');
            var sort = sortSelect ? sortSelect.value : 'date';

            // Update container data
            container.dataset.limit = limit;

            // Update load more button data
            var loadMoreBtn = container.querySelector('.load-more-slots');
            if (loadMoreBtn) {
                loadMoreBtn.dataset.limit = limit;
                loadMoreBtn.dataset.page = '1';
            }

            this.reloadSlots(container, { sort: sort, limit: limit });
        },

        /**
         * Reload slots with new filters
         */
        reloadSlots: function(container, filters) {
            var grid = container.querySelector('.slots-grid');

            // Show loading state
            grid.innerHTML = '<div class="slots-loading">Loading...</div>';

            // Make AJAX request
            this.makeAjaxRequest({
                url: window.slots_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_slots',
                    nonce: grid.dataset.nonce,
                    page: 1,
                    limit: parseInt(filters.limit),
                    sort: filters.sort
                },
                success: function(response) {
                    if (response.success) {
                        grid.innerHTML = response.data.html;

                        // Show/hide load more button
                        var loadMoreBtn = container.querySelector('.load-more-slots');
                        if (response.data.has_more) {
                            if (loadMoreBtn) {
                                loadMoreBtn.style.display = 'block';
                                loadMoreBtn.dataset.page = '1';
                            }
                        } else {
                            if (loadMoreBtn) {
                                loadMoreBtn.style.display = 'none';
                            }
                        }
                    } else {
                        console.error('AJAX Error:', response.data);
                        var errorMessage = response.data && response.data.message ? response.data.message : window.slots_ajax.strings.error;
                        grid.innerHTML = '<div class="slots-error">' + errorMessage + '</div>';
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Request Failed:', status, error);
                    var errorMessage = window.slots_ajax.strings.error;
                    if (xhr.status === 403) {
                        errorMessage = 'Access denied. Please refresh the page and try again.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Please try again later.';
                    }
                    grid.innerHTML = '<div class="slots-error">' + errorMessage + '</div>';
                }
            });
        },

        /**
         * Load more slots
         */
        loadMoreSlots: function(container, page, callback) {
            var sortSelect = container.querySelector('.slots-sort-select');
            var limitSelect = container.querySelector('.slots-limit-select');
            var sort = sortSelect ? sortSelect.value : 'date';
            var limit = limitSelect ? parseInt(limitSelect.value) : 10;

            this.makeAjaxRequest({
                url: window.slots_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_slots',
                    nonce: container.querySelector('.slots-grid').dataset.nonce,
                    page: page,
                    limit: limit,
                    sort: sort
                },
                success: function(response) {
                    if (response.success) {
                        container.querySelector('.slots-grid').insertAdjacentHTML('beforeend', response.data.html);
                        callback(response.data.has_more);
                    } else {
                        console.error('Load More Error:', response.data);
                        callback(false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Load More AJAX Failed:', status, error);
                    callback(false);
                }
            });
        },

        /**
         * Handle load more
         */
        handleLoadMore: function(event) {
            if (!event.target.classList.contains('load-more-slots')) return;

            event.preventDefault();

            var button = event.target;
            var container = this.findClosest(button, '.slots-container');
            var currentPage = parseInt(button.dataset.page) || 1;
            var nextPage = currentPage + 1;

            // Disable button and show loading
            button.disabled = true;
            button.textContent = 'Loading...';

            // Load more slots
            this.loadMoreSlots(container, nextPage, function(hasMore) {
                button.dataset.page = nextPage;
                button.disabled = false;

                if (!hasMore) {
                    button.style.display = 'none';
                } else {
                    button.textContent = 'Load More Slots';
                }
            });
        },

        /**
         * Find closest parent element with specified selector
         */
        findClosest: function(element, selector) {
            while (element && element !== document) {
                if (element.matches && element.matches(selector)) {
                    return element;
                }
                element = element.parentElement;
            }
            return null;
        },

        /**
         * Make AJAX request using fetch API
         */
        makeAjaxRequest: function(options) {
            var formData = new FormData();

            // Add data to FormData
            for (var key in options.data) {
                if (options.data.hasOwnProperty(key)) {
                    formData.append(key, options.data[key]);
                }
            }

            fetch(options.url, {
                    method: options.type || 'POST',
                    body: formData
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (options.success) {
                        options.success(data);
                    }
                })
                .catch(function(error) {
                    if (options.error) {
                        var xhr = { status: 500 };
                        if (error.message.includes('HTTP 403')) {
                            xhr.status = 403;
                        } else if (error.message.includes('HTTP 500')) {
                            xhr.status = 500;
                        }
                        options.error(xhr, 'error', error.message);
                    }
                });
        }
    };

    // Initialize when document is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            Slots.init();
        });
    } else {
        Slots.init();
    }

    // Make Slots available globally
    window.Slots = Slots;

})();