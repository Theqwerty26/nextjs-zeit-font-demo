=== Mobile Bonus List ===
Contributors: yourname
Tags: bonus, casino, betting, mobile, shortcode, ajax
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin to manage and display bonus listings for betting/casino sites with mobile-first design.

== Description ==

Mobile Bonus List is a comprehensive WordPress plugin designed specifically for betting and casino websites. It allows you to easily manage and display bonus offers with a beautiful, mobile-first interface that only appears on mobile devices.

= Key Features =

* **Custom Post Type**: Dedicated "Bonus" post type for easy management
* **Mobile-Only Display**: Responsive design that only shows on devices under 768px width
* **AJAX Filtering**: Real-time search and category filtering without page reloads
* **Modern UI**: Dark theme with orange accents (#f7931e) for a modern app-like experience
* **SEO Friendly**: All bonus links include rel="nofollow" attributes
* **Elementor Compatible**: Works seamlessly with Elementor page builder
* **Performance Optimized**: CSS and JS only load when shortcode is present

= Bonus Management =

Each bonus entry includes:
* Site name (title)
* Logo upload
* Bonus description text
* Bonus link (opens in new tab with nofollow)
* Category assignment (Trend, Recommended, or All)

= Frontend Display =

The plugin provides a `[bonus_list]` shortcode that displays:
* Top and bottom announcement bars
* Search functionality
* Category filter buttons (All, Trend, Recommended)
* Responsive bonus cards with logos and call-to-action buttons

= Styling =

* Background color: #0b1224 (dark blue)
* Card background: #1d2236 (lighter blue)
* Primary accent: #f7931e (orange)
* Fully responsive mobile-app-style interface
* Smooth animations and hover effects

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/mobile-bonus-list` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to 'Bonuses' in your WordPress admin to start adding bonus entries.
4. Use the `[bonus_list]` shortcode on any page or post to display the bonus list.

== Usage ==

= Adding Bonuses =

1. Go to **Bonuses > Add New** in your WordPress admin
2. Enter the site name as the title
3. Upload a logo using the logo upload field
4. Enter the bonus description in the bonus text field
5. Add the bonus URL in the bonus link field
6. Select appropriate categories (Trend, Recommended, or both)
7. Publish the bonus

= Displaying Bonuses =

Simply add the `[bonus_list]` shortcode to any page, post, or Elementor widget where you want the bonus list to appear. The list will only be visible on mobile devices (screen width < 768px).

= Elementor Integration =

1. Add a Text Editor widget to your Elementor page
2. Insert the `[bonus_list]` shortcode
3. The bonus list will render with full functionality

== Frequently Asked Questions ==

= Why doesn't the bonus list show on desktop? =

This plugin is specifically designed for mobile users only. The bonus list is hidden on desktop devices (screen width ≥ 768px) by design to provide a focused mobile experience.

= Can I customize the colors? =

The plugin uses a predefined color scheme optimized for mobile casino/betting sites. For customization, you can override the CSS styles in your theme's stylesheet.

= How do I add more categories? =

The plugin comes with "Trend" and "Recommended" categories by default. You can add more categories by going to **Bonuses > Categories** in your WordPress admin.

= Are the bonus links SEO-friendly? =

Yes, all bonus links automatically include `rel="nofollow"` and `target="_blank"` attributes for proper SEO handling of affiliate/promotional links.

= Does this work with caching plugins? =

Yes, the plugin is compatible with most caching plugins. The AJAX functionality ensures dynamic content updates without affecting cached pages.

== Screenshots ==

1. Mobile bonus list display with search and filter functionality
2. Admin interface for adding new bonuses
3. Bonus categories management
4. Elementor integration example

== Changelog ==

= 1.0.0 =
* Initial release
* Custom post type for bonuses
* Mobile-first responsive design
* AJAX search and filtering
* Elementor compatibility
* SEO-friendly bonus links
* Performance optimizations

== Upgrade Notice ==

= 1.0.0 =
Initial release of Mobile Bonus List plugin.

== Developer Notes ==

= Hooks and Filters =

The plugin provides several hooks for developers:

* `mbl_bonus_card_html` - Filter bonus card HTML output
* `mbl_search_query_args` - Filter WP_Query arguments for search
* `mbl_ajax_response` - Filter AJAX response data

= Custom Styling =

To customize the appearance, override these CSS classes in your theme:

* `.mobile-bonus-list-container` - Main container
* `.bonus-card` - Individual bonus cards
* `.filter-btn` - Category filter buttons
* `.bonus-button` - Bonus action buttons

= Performance =

The plugin follows WordPress best practices:
* Conditional asset loading
* Proper nonce verification
* Sanitized inputs and escaped outputs
* Optimized database queries
* Mobile-first responsive design

== Support ==

For support and feature requests, please contact the plugin author or visit the plugin's support forum.
