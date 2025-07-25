(function($) {
    'use strict';
    
    // Wait for DOM to be ready
    $(document).ready(function() {
        initializeBonusList();
    });
    
    function initializeBonusList() {
        const searchInput = $('#bonus-search');
        const filterButtons = $('.filter-btn');
        const bonusContainer = $('#bonus-cards-container');
        
        let searchTimeout;
        let currentCategory = 'all';
        let currentSearch = '';
        
        // Initialize event listeners
        setupEventListeners();
        
        function setupEventListeners() {
            // Search input with debounce
            searchInput.on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val().trim();
                
                searchTimeout = setTimeout(function() {
                    currentSearch = searchTerm;
                    performFilter(currentSearch, currentCategory);
                }, 300); // 300ms debounce
            });
            
            // Category filter buttons
            filterButtons.on('click', function(e) {
                e.preventDefault();
                
                const category = $(this).data('category');
                
                // Update active state
                filterButtons.removeClass('active');
                $(this).addClass('active');
                
                // Add click animation
                $(this).addClass('clicked');
                setTimeout(() => {
                    $(this).removeClass('clicked');
                }, 150);
                
                currentCategory = category;
                performFilter(currentSearch, currentCategory);
            });
            
            // Handle bonus button clicks for analytics (optional)
            $(document).on('click', '.bonus-button', function() {
                const bonusTitle = $(this).closest('.bonus-card').find('.bonus-title').text();
                console.log('Bonus clicked:', bonusTitle);
                
                // Add click effect
                $(this).addClass('clicked');
                setTimeout(() => {
                    $(this).removeClass('clicked');
                }, 200);
            });
        }
        
        function performFilter(search, category) {
            // Show loading state
            showLoading();
            
            // Prepare AJAX data
            const ajaxData = {
                action: 'filter_bonuses',
                nonce: mbl_ajax.nonce,
                search: search,
                category: category
            };
            
            // Perform AJAX request
            $.ajax({
                url: mbl_ajax.ajax_url,
                type: 'POST',
                data: ajaxData,
                timeout: 10000, // 10 second timeout
                beforeSend: function() {
                    // Disable search and filter controls during request
                    searchInput.prop('disabled', true);
                    filterButtons.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        updateBonusContainer(response.data);
                        animateResults();
                    } else {
                        showError('Failed to load bonuses: ' + (response.data || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Failed to load bonuses.';
                    
                    if (status === 'timeout') {
                        errorMessage = 'Request timed out. Please try again.';
                    } else if (xhr.status === 0) {
                        errorMessage = 'Network error. Please check your connection.';
                    } else if (xhr.status >= 500) {
                        errorMessage = 'Server error. Please try again later.';
                    }
                    
                    showError(errorMessage);
                    console.error('AJAX Error:', status, error);
                },
                complete: function() {
                    // Re-enable controls
                    searchInput.prop('disabled', false);
                    filterButtons.prop('disabled', false);
                    hideLoading();
                }
            });
        }
        
        function showLoading() {
            bonusContainer.html('<div class="loading">Loading bonuses...</div>');
        }
        
        function hideLoading() {
            // Loading is hidden when content is updated
        }
        
        function updateBonusContainer(html) {
            bonusContainer.html(html);
        }
        
        function showError(message) {
            const errorHtml = `
                <div class="error-message" style="
                    text-align: center;
                    color: #ff6b6b;
                    background-color: rgba(255, 107, 107, 0.1);
                    border: 1px solid rgba(255, 107, 107, 0.3);
                    border-radius: 12px;
                    padding: 20px;
                    margin: 20px 0;
                ">
                    <strong>Error:</strong> ${message}
                    <br><br>
                    <button onclick="location.reload()" style="
                        background: #f7931e;
                        color: white;
                        border: none;
                        padding: 8px 16px;
                        border-radius: 6px;
                        cursor: pointer;
                        font-size: 14px;
                    ">Retry</button>
                </div>
            `;
            bonusContainer.html(errorHtml);
        }
        
        function animateResults() {
            // Animate bonus cards entrance
            const cards = $('.bonus-card');
            cards.each(function(index) {
                const card = $(this);
                card.css({
                    opacity: '0',
                    transform: 'translateY(20px)'
                });
                
                setTimeout(function() {
                    card.css({
                        opacity: '1',
                        transform: 'translateY(0)',
                        transition: 'all 0.3s ease'
                    });
                }, index * 100); // Stagger animation
            });
        }
        
        // Utility function to highlight search terms
        function highlightSearchTerm(text, searchTerm) {
            if (!searchTerm || searchTerm.length < 2) {
                return text;
            }
            
            const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
            return text.replace(regex, '<mark style="background-color: rgba(247, 147, 30, 0.3); color: inherit;">$1</mark>');
        }
        
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        // Handle window resize to ensure mobile-only display
        $(window).on('resize', function() {
            if ($(window).width() >= 768) {
                $('.mobile-bonus-list-container').hide();
            } else {
                $('.mobile-bonus-list-container').show();
            }
        });
        
        // Initialize smooth scrolling for internal links
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this.getAttribute('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 20
                }, 500);
            }
        });
        
        // Add touch feedback for mobile devices
        if ('ontouchstart' in window) {
            $(document).on('touchstart', '.bonus-button, .filter-btn', function() {
                $(this).addClass('touch-active');
            });
            
            $(document).on('touchend', '.bonus-button, .filter-btn', function() {
                const element = $(this);
                setTimeout(function() {
                    element.removeClass('touch-active');
                }, 150);
            });
        }
        
        // Performance optimization: Lazy load images if needed
        function lazyLoadImages() {
            const images = $('.bonus-logo img[data-src]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                images.each(function() {
                    imageObserver.observe(this);
                });
            } else {
                // Fallback for older browsers
                images.each(function() {
                    const img = $(this);
                    img.attr('src', img.data('src')).removeAttr('data-src');
                });
            }
        }
        
        // Initialize lazy loading
        lazyLoadImages();
        
        // Refresh lazy loading after AJAX updates
        $(document).on('ajaxComplete', function() {
            setTimeout(lazyLoadImages, 100);
        });
        
        // Add keyboard navigation support
        searchInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                $(this).val('').trigger('input');
            }
        });
        
        // Add focus management for accessibility
        filterButtons.on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).click();
            }
        });
        
        console.log('Mobile Bonus List initialized successfully');
    }
    
})(jQuery);

// Add CSS for touch feedback and additional states
const additionalCSS = `
    <style>
        .filter-btn.clicked,
        .bonus-button.clicked {
            transform: scale(0.95) !important;
        }
        
        .touch-active {
            opacity: 0.8 !important;
        }
        
        .filter-btn:disabled,
        #bonus-search:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        @media (max-width: 767px) {
            .mobile-bonus-list-container {
                display: block !important;
            }
        }
        
        /* Accessibility improvements */
        .filter-btn:focus,
        #bonus-search:focus,
        .bonus-button:focus {
            outline: 2px solid #f7931e;
            outline-offset: 2px;
        }
        
        /* Reduced motion for users who prefer it */
        @media (prefers-reduced-motion: reduce) {
            .bonus-card,
            .filter-btn,
            .bonus-button,
            .announcement-bar::before {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
`;

// Inject additional CSS
document.head.insertAdjacentHTML('beforeend', additionalCSS);
