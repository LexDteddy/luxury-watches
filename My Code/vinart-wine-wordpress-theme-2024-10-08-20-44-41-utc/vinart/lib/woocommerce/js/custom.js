jQuery(document).ready(function($) {
    var currentCategory = ajax_filter_params.current_category;  // Access the current category passed from PHP
    var currentTag = ajax_filter_params.current_tag;  // Access the current tag passed from PHP

    // Toggle filters visibility and scroll the page only when opening
    $('#toggle-filters').on('click', function() {
        if (!$('.filters-container').hasClass('open')) {
            $('html, body').animate({
                scrollTop: $('#toggle-filters').offset().top
            }, 600); // Adjust the duration as needed
        }
        $('.filters-container').toggleClass('open');
    });

    // Handle the reset button visibility and active filters display
    function updateActiveFilters() {
        var activeFilters = [];
        var hasFilters = false;

        // Collect active categories (only on the shop page)
        $('input[name="categories[]"]:checked').each(function() {
            activeFilters.push('<span class="filter-pill">' + $(this).parent().text().trim() + '</span>');
            hasFilters = true;
        });

        // Collect active tags (only if not on a tag page)
        if (!currentTag) {
            $('input[name="tags[]"]:checked').each(function() {
                activeFilters.push('<span class="filter-pill">' + $(this).parent().text().trim() + '</span>');
                hasFilters = true;
            });
        }

        // Collect active attributes
        $('.attribute-filter input[type="checkbox"]:checked').each(function() {
            activeFilters.push('<span class="filter-pill">' + $(this).parent().text().trim() + '</span>');
            hasFilters = true;
        });

        // Display active filters or clear them if none are active
        if (hasFilters) {
            $('#reset-filters').show();
            $('.active-filters').html(activeFilters.join(' ')).show();  // Join spans with space
        } else {
            $('#reset-filters').hide();
            $('.active-filters').html('').hide();  // Clear the active filters display
        }
    }


    // Attach the reset button event handler
    $('#reset-filters').on('click', function(e) {
        e.preventDefault();

        // Uncheck all checkboxes except disabled ones
        $('.filter:not(:disabled)').prop('checked', false);

        // Clear all filters and reload the original product list
        showFilterSpinners();
        applyFilters();

        // Clear active filters
        $('.active-filters').html('').hide();  // Clear the active filters display

        // Update the filters display
        updateActiveFilters();
    });


    // Show small spinners in each filter section
    function showFilterSpinners() {
        $('.filter-section .small-spinner').show();
    }

    // Hide small spinners in each filter section
    function hideFilterSpinners() {
        $('.filter-section .small-spinner').hide();
    }

    // Attach the updateActiveFilters function to filter change and reset button click
    $(document).on('change', '.filter', function() {
        showFilterSpinners();
        applyFilters();
        updateActiveFilters();
    });

    // Apply filters
    function applyFilters() {
        var categories = [];
        var tags = [];
        var attributes = {};

        // Collect selected categories
        $('input[name="categories[]"]:checked').each(function() {
            categories.push($(this).val());
        });

        // Ensure the current category is always applied on category pages
        if (currentCategory && !categories.length) {
            categories.push(currentCategory);
        }

        // Collect selected tags
        if (!currentTag) {
            $('input[name="tags[]"]:checked').each(function() {
                tags.push($(this).val());
            });
        } else {
            tags.push(currentTag); // Ensure the current tag is always applied on tag pages
        }

        // Collect selected attributes
        $('.attribute-filter').each(function() {
            var attribute = $(this).data('attribute');
            attributes[attribute] = [];
            $(this).find('input[type="checkbox"]:checked').each(function() {
                attributes[attribute].push($(this).val());
            });
        });

        console.log('Collected categories:', categories);
        console.log('Collected tags:', tags);
        console.log('Collected attributes:', attributes);

        // Make AJAX request
        $.ajax({
            url: ajax_filter_params.ajaxurl,
            type: 'POST',
            data: {
                action: 'ajax_filter_products',
                categories: categories,  // Handle category filtering correctly
                tags: tags,  // Handle tag filtering correctly
                attributes: attributes,
                current_category: currentCategory,  // Send the current category for category pages
                current_tag: currentTag,  // Send the current tag for tag pages
                ajax_nonce: ajax_filter_params.ajax_nonce
            },
            beforeSend: function(xhr) {
                // Add 'processing' class and animate out products
                $('.filter-actions button').addClass('processing');
                gsap.to('.products', {
                    duration: 0.1,
                    opacity: 0,
                    onComplete: function() {
                        gsap.to('.spinner', { duration: 0.1, opacity: 1, display: 'block' });
                    }
                });
            },
            success: function(response) {
                $('.filter-actions button').removeClass('processing');
                if (response.success) {
                    gsap.to('.spinner', {
                        duration: 0.1,
                        opacity: 0,
                        onComplete: function() {
                            // Update the product list
                            $('.products').html(response.data.products);

                            // Always update the filters to ensure correct filtering
                            $('.filters-container').html(response.data.filters);

                            // Hide the small spinners
                            hideFilterSpinners();

                            gsap.set('.products li', { opacity: 0, y: 20 });
                            gsap.to('.products', {
                                duration: 0.1,
                                opacity: 1,
                                onComplete: function() {
                                    gsap.to('.products li', 
                                        { opacity: 1, y: 0, duration: 0.6, stagger: 0.1 }
                                    );
                                }
                            });
                        }
                    });
                } else {
                    gsap.to('.spinner', { duration: 0.1, opacity: 0 });
                    console.log('AJAX error:', response.data);
                    hideFilterSpinners();
                }
            },
            error: function(xhr, status, error) {
                gsap.to('.spinner', { duration: 0.1, opacity: 0 });
                console.log('AJAX Error: ', xhr.responseText);
                hideFilterSpinners();
            }
        });
    }

    // Initial state for the reset button and active filters
    updateActiveFilters();

});