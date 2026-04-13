(function ($) {
    'use strict';

    $(document).ready(function () {
        const $searchForm = $('.op-glossary-search-form');
        const $searchInput = $searchForm.find('input[name="op_glossary_search"]');
        const $searchButton = $searchForm.find('.op-glossary-search-form__button');
        const $resultsContainer = $('.op-glossary-results');
        const $paginationLinks = $('.op-glossary-pagination-list a');
        let searchTimeout;

        /**
         * Perform AJAX search
         * @param {string} searchTerm 
         * @param {string} letter 
         */
        function performSearch(searchTerm = '', letter = '') {
            $resultsContainer.addClass('loading').css('opacity', '0.5');

            $.ajax({
                url: op_glossary_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'op_glossary_search_ajax',
                    nonce: op_glossary_vars.nonce,
                    search: searchTerm,
                    letter: letter
                },
                success: function (response) {
                    if (response.success) {
                        $resultsContainer.html(response.data.html);
                        // Update active state in pagination if letter was clicked
                        if (letter) {
                            $paginationLinks.parent().removeClass('active');
                            $paginationLinks.filter(function () {
                                return $(this).text().trim() === letter;
                            }).parent().addClass('active');
                        }
                    } else {
                        $resultsContainer.html('<p class="op-glossary-no-results">' + response.data.message + '</p>');
                    }
                },
                error: function () {
                    $resultsContainer.html('<p class="op-glossary-no-results">An error occurred. Please try again.</p>');
                },
                complete: function () {
                    $resultsContainer.removeClass('loading').css('opacity', '1');
                }
            });
        }

        // Live search with debounce
        $searchInput.on('input', function () {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val();

            searchTimeout = setTimeout(function () {
                performSearch(searchTerm);
            }, 500); // 500ms debounce
        });

        // AJAX pagination
        $(document).on('click', '.op-glossary-pagination-list a', function (e) {
            e.preventDefault();
            const label = $(this).text().trim();
            const letter = label === 'All' ? '' : label;
            const searchTerm = $searchInput.val();

            performSearch(searchTerm, letter);

            // Update URL without reloading (optional, but good for UX)
            const url = new URL(window.location);
            if (letter) {
                url.searchParams.set('op-glossary-pagination', letter);
            } else {
                url.searchParams.delete('op-glossary-pagination');
            }
            if (searchTerm) {
                url.searchParams.set('op_glossary_search', searchTerm);
            } else {
                url.searchParams.delete('op_glossary_search');
            }
            window.history.pushState({}, '', url);
        });

        $searchInput.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch($searchInput.val());
            }
        });

        $searchButton.on('click', function (e) {
            e.preventDefault();
            performSearch($searchInput.val());
        });
    });

})(jQuery);
document.addEventListener("DOMContentLoaded", function () {

    const items = document.querySelectorAll(".glossary-nav");

    let maxWidth = 0;

    items.forEach(el => {
        el.style.width = "fit-content";
        const w = el.offsetWidth;
        if (w > maxWidth) maxWidth = w;
    });

    items.forEach(el => {
        el.style.width = maxWidth + "px";
    });

});
