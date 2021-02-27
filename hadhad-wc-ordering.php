<?php

/**
 * WooCommerce Ordering Widget
 *
 * @author            Hadi Alizadeh Jalali
 * @license           GPL-2.0-or-later
 * 
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Ordering Widget
 * Plugin URI:        https://github.com/hadi-aj/wp-wc-ordering-widget
 * Description:       This plugint add a WooCommerce sorting dropdown widget
 * Version:           1.0.0
 * Author:            Hadi Alizadeh Jalali
 * Author URI:        https://github.com/hadi-aj/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class Hadhad_WC_Ordering_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
                'hadhad_wc_ordering_widget',
                __('WooCommerce Ordering Widget', 'text_domain'),
                array(
                    'description' => esc_html__('Display a product sorting dropdown', 'text_domain'),
                    'customize_selective_refresh' => true,
                )
        );
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {

        // Set widget defaults
        $defaults = array(
            'title' => '',
        );

        // Parse current settings with defaults
        extract(wp_parse_args((array) $instance, $defaults));
        ?>

        <?php // Widget Title   ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? sanitize_text_field($new_instance['title']) : '';

        return $instance;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {

        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $catalog_orderby_options = apply_filters(
                'WooCommerce_catalog_orderby',
                array(
                    'menu_order' => __('Default sorting', 'WooCommerce'),
                    'popularity' => __('Sort by popularity', 'WooCommerce'),
                    'rating' => __('Sort by average rating', 'WooCommerce'),
                    'date' => __('Sort by latest', 'WooCommerce'),
                    'price' => __('Sort by price: low to high', 'WooCommerce'),
                    'price-desc' => __('Sort by price: high to low', 'WooCommerce'),
                )
        );

        $default_orderby = wc_get_loop_prop('is_search') ? 'relevance' : apply_filters('WooCommerce_default_catalog_orderby', get_option('WooCommerce_default_catalog_orderby', ''));
        $orderby = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : $default_orderby;

        $html = '<form class="WooCommerce-ordering" method="get">
                            <select name="orderby" class="orderby" aria-label="' . esc_attr_e('Shop order', 'WooCommerce') . '">';
        foreach ($catalog_orderby_options as $id => $name) :
            $html .= '<option value="' . esc_attr($id) . '"' . selected($orderby, $id) . '>' . esc_html($name) . '</option>';
        endforeach;
        $html .= '</select>
	<input type="hidden" name="paged" value="1" />
	' . wc_query_string_form_fields(null, array('orderby', 'submit', 'paged', 'product-page')) . '</form>';

        echo $html;

        // WordPress core after_widget hook (always include )
        echo $args['after_widget'];
    }

}

// Register the widget
function register_WooCommerce_ordering_widget()
{
    register_widget('hadhad_wc_ordering_widget');
}

add_action('widgets_init', 'register_WooCommerce_ordering_widget');

