/**
 * Public JavaScript for Slots Plugin
 *
 * @package Slots
 */

// Import only frontend CSS
import '../css/slots-public.css';

class Slots {
    /**
     * Initialize
     */
    init() {
        this.bindEvents();
    }

    /**
     * Bind events
     */
    bindEvents() {
        // Use event delegation to handle dynamically added elements
        document.addEventListener('change', this.handleSortChange.bind(this));
        document.addEventListener('change', this.handleLimitChange.bind(this));
        document.addEventListener('click', this.handleLoadMore.bind(this));

        // Prevent any form submissions that might cause page refresh
        document.addEventListener('submit', (event) => {
            // Only prevent if it's related to slots
            if (event.target.closest('.slots-container')) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        });

        // Add specific event prevention for the limit select
        document.addEventListener('DOMContentLoaded', () => {
            const limitSelect = document.getElementById('slots-limit');
            if (limitSelect) {

                // Prevent any default behavior
                limitSelect.addEventListener('change', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                }, true);

                // Also prevent any other events that might cause issues
                limitSelect.addEventListener('submit', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                });

                // Prevent any click events that might cause form submission
                limitSelect.addEventListener('click', (event) => {
                    event.stopPropagation();
                });

                // Check if the select is inside a form
                if (limitSelect.form) {
                    // Prevent form submission
                    limitSelect.form.addEventListener('submit', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    });
                }
            }
        });
    }

    /**
     * Handle sort change
     */
    handleSortChange(event) {
        if (!event.target.classList.contains('slots-sort-select')) {
            return;
        }

        // Prevent any default form submission
        event.preventDefault();
        event.stopPropagation();

        const select = event.target;
        const container = this.findClosest(select, '.slots-container');

        if (!container) {
            return;
        }

        const sort = select.value;
        const limitSelect = container.querySelector('.slots-limit-select');
        const limit = limitSelect ? limitSelect.value : '10';

        this.reloadSlots(container, { sort, limit });
    }

    /**
     * Handle limit change
     */
    handleLimitChange(event) {
        if (!event.target.classList.contains('slots-limit-select')) {
            return;
        }

        // Prevent any default form submission
        event.preventDefault();
        event.stopPropagation();

        const select = event.target;
        const container = this.findClosest(select, '.slots-container');

        if (!container) {
            return;
        }

        const limit = select.value;
        const sortSelect = container.querySelector('.slots-sort-select');
        const sort = sortSelect ? sortSelect.value : 'date';

        // Update container data
        container.dataset.limit = limit;

        // Update load more button data
        const loadMoreBtn = container.querySelector('.load-more-slots');
        if (loadMoreBtn) {
            loadMoreBtn.dataset.limit = limit;
            loadMoreBtn.dataset.page = '1';
        }

        this.reloadSlots(container, { sort, limit });
    }

    /**
     * Reload slots with new filters
     */
    reloadSlots(container, filters) {
        const grid = container.querySelector('.slots-grid');
        if (!grid) {
            return;
        }

        // Show loading state
        grid.innerHTML = '<div class="slots-loading">Loading...</div>';

        // Check if AJAX object exists
        if (!window.slots_ajax || !window.slots_ajax.ajax_url) {
            grid.innerHTML = '<div class="slots-error">AJAX configuration error. Please refresh the page.</div>';
            return;
        }

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
            success: (response) => {
                if (response.success) {
                    // Store the current scroll position
                    const scrollPos = window.scrollY;

                    // Update the grid content
                    grid.innerHTML = response.data.html;

                    // Restore scroll position
                    window.scrollTo(0, scrollPos);

                    // Show/hide load more button
                    const loadMoreBtn = container.querySelector('.load-more-slots');
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
                    const errorMessage = response.data && response.data.message ? response.data.message : window.slots_ajax.strings.error;
                    grid.innerHTML = '<div class="slots-error">' + errorMessage + '</div>';
                }
            },
            error: (xhr, status, error) => {
                let errorMessage = window.slots_ajax.strings.error;
                if (xhr.status === 403) {
                    errorMessage = 'Access denied. Please refresh the page and try again.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }
                grid.innerHTML = '<div class="slots-error">' + errorMessage + '</div>';
            }
        });
    }

    /**
     * Load more slots
     */
    loadMoreSlots(container, page, callback) {
        const sortSelect = container.querySelector('.slots-sort-select');
        const limitSelect = container.querySelector('.slots-limit-select');
        const sort = sortSelect ? sortSelect.value : 'date';
        const limit = limitSelect ? parseInt(limitSelect.value) : 10;

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
            success: (response) => {
                if (response.success) {
                    container.querySelector('.slots-grid').insertAdjacentHTML('beforeend', response.data.html);
                    callback(response.data.has_more);
                } else {
                    callback(false);
                }
            },
            error: (xhr, status, error) => {
                callback(false);
            }
        });
    }

    /**
     * Handle load more
     */
    handleLoadMore(event) {
        if (!event.target.classList.contains('load-more-slots')) return;

        event.preventDefault();

        const button = event.target;
        const container = this.findClosest(button, '.slots-container');
        const currentPage = parseInt(button.dataset.page) || 1;
        const nextPage = currentPage + 1;

        // Disable button and show loading
        button.disabled = true;
        button.textContent = 'Loading...';

        // Load more slots
        this.loadMoreSlots(container, nextPage, (hasMore) => {
            button.dataset.page = nextPage;
            button.disabled = false;

            if (!hasMore) {
                button.style.display = 'none';
            } else {
                button.textContent = 'Load More Slots';
            }
        });
    }

    /**
     * Find closest parent element with specified selector
     */
    findClosest(element, selector) {
        while (element && element !== document) {
            if (element.matches && element.matches(selector)) {
                return element;
            }
            element = element.parentElement;
        }
        return null;
    }

    /**
     * Make AJAX request using fetch API
     */
    makeAjaxRequest(options) {

        const formData = new FormData();

        // Add data to FormData
        for (const key in options.data) {
            if (options.data.hasOwnProperty(key)) {
                formData.append(key, options.data[key]);
            }
        }

        // Add timeout to prevent hanging requests
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

        fetch(options.url, {
                method: options.type || 'POST',
                body: formData,
                signal: controller.signal
            })
            .then((response) => {
                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                return response.json();
            })
            .then((data) => {
                if (options.success) {
                    options.success(data);
                }
            })
            .catch((error) => {
                clearTimeout(timeoutId);

                // Handle different types of errors
                let errorMessage = 'Network error occurred';
                let xhrStatus = 500;

                if (error.name === 'AbortError') {
                    errorMessage = 'Request timed out';
                    xhrStatus = 408;
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Network connection failed. Please check your internet connection.';
                    xhrStatus = 0;
                } else if (error.message.includes('HTTP 403')) {
                    errorMessage = 'Access denied. Please refresh the page and try again.';
                    xhrStatus = 403;
                } else if (error.message.includes('HTTP 500')) {
                    errorMessage = 'Server error. Please try again later.';
                    xhrStatus = 500;
                }

                if (options.error && typeof options.error === 'function') {
                    const xhr = { status: xhrStatus };
                    options.error(xhr, 'error', errorMessage);
                }
            });
    }
}

// Initialize when document is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.Slots = new Slots();
        window.Slots.init();
    });
} else {
    window.Slots = new Slots();
    window.Slots.init();
}

export default Slots;