<?php
/*
Plugin Name: Enhanced Text Widget
Plugin URI: http://pomelodesign.com/enhanced-text-widget
Description: An enhanced version of the default text widget where you may have Text, HTML, CSS, JavaScript, Flash, and/or PHP as content with linkable widget title.
Version: 1.3.4
Author: Pomelo Design
Author URI: http://pomelodesign.com/
License: GPL2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class EnhancedTextWidget extends WP_Widget {

    function EnhancedTextWidget() {
        $widget_ops = array('classname' => 'widget_text', 'description' => __('Widget supporting Text, HTML, CSS, PHP, Flash, JavaScript, Shortcodes'));
        $control_ops = array('width' => 400, 'height' => 350);
        $this->WP_Widget('EnhancedTextWidget', __('Enhanced Text'), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
        $hideTitle = $instance['hideTitle'] ? true : false;
        $titleUrl = apply_filters('widget_title', empty($instance['titleUrl']) ? '' : $instance['titleUrl'], $instance);
        $newWindow = $instance['newWindow'] ? true : false;
        $cssClass = apply_filters('widget_title', empty($instance['cssClass']) ? '' : $instance['cssClass'], $instance);
        $bare = $instance['bare'] ? true : false;
        $text = apply_filters( 'widget_text', $instance['text'], $instance );
        if ( $cssClass ) {
            if( strpos($before_widget, 'class') === false ) {
                $before_widget = str_replace('>', 'class="'. $cssClass . '"', $before_widget);
            } else {
                $before_widget = str_replace('class="', 'class="'. $cssClass . ' ', $before_widget);
            }
        }
        echo $bare ? '' : $before_widget;
        if($hideTitle == false) {
            if( $titleUrl && $title ) {
                $title = '<a href="'.$titleUrl.'"'.($newWindow == true?' target="_blank"':'').' title="'.$title.'">'.$title.'</a>';
            }
            if ( !empty( $title ) ) {
                echo $bare ? $title : $before_title . $title . $after_title;
            }
        }
        ?>
        <div class="textwidget">
            <?php if($instance['filter']) {
                ob_start();
                eval("?>$text<?php ");
                $output = ob_get_contents();
                ob_end_clean();
                echo wpautop($output);
            } else {
                eval("?>".$text."<?php ");
            } ?>
        </div>
        <?php
        echo $bare ? '' : $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['hideTitle'] = isset($new_instance['hideTitle']);
        $instance['titleUrl'] = strip_tags($new_instance['titleUrl']);
        $instance['cssClass'] = strip_tags($new_instance['cssClass']);
        $instance['newWindow'] = isset($new_instance['newWindow']);
        if ( current_user_can('unfiltered_html') )
            $instance['text'] =  $new_instance['text'];
        else
            $instance['text'] = wp_filter_post_kses( $new_instance['text'] );
        $instance['filter'] = isset($new_instance['filter']);
        $instance['bare'] = isset($new_instance['bare']);
        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'titleUrl' => '', 'text' => '' ) );
        $title = "";
        if(isset($instance['title'])){
            $title = strip_tags($instance['title']);
        }
        $titleUrl = "";
        if(isset($instance['titleUrl'])){
            $titleUrl = strip_tags($instance['titleUrl']);
        }
        $cssClass = "";
        if(isset($instance['cssClass'])){
            $cssClass = strip_tags($instance['cssClass']);
        }
        $text = "";
        if(isset($instance['text'])){
            $text = format_to_edit($instance['text']);
        }
?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('titleUrl'); ?>"><?php _e('URL:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('titleUrl'); ?>" name="<?php echo $this->get_field_name('titleUrl'); ?>" type="text" value="<?php echo esc_attr($titleUrl); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('cssClass'); ?>"><?php _e('CSS Classes:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('cssClass'); ?>" name="<?php echo $this->get_field_name('cssClass'); ?>" type="text" value="<?php echo esc_attr($cssClass); ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Content:'); ?></label>
        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        <p><input id="<?php echo $this->get_field_id('hideTitle'); ?>" name="<?php echo $this->get_field_name('hideTitle'); ?>" type="checkbox" <?php checked(isset($instance['hideTitle']) ? $instance['hideTitle'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('hideTitle'); ?>"><?php _e('Do not display the title'); ?></label></p>
        <p><input type="checkbox" id="<?php echo $this->get_field_id('newWindow'); ?>" name="<?php echo $this->get_field_name('newWindow'); ?>" <?php checked(isset($instance['newWindow']) ? $instance['newWindow'] : 0); ?> />
        <label for="<?php echo $this->get_field_id('newWindow'); ?>"><?php _e('Open the URL in a new window'); ?></label></p>
        <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs to the content'); ?></label></p>
        <p><input id="<?php echo $this->get_field_id('bare'); ?>" name="<?php echo $this->get_field_name('bare'); ?>" type="checkbox" <?php checked(isset($instance['bare']) ? $instance['bare'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('bare'); ?>"><?php _e('Do not output before/after_widget/title'); ?></label></p>
        <p class="credits"><small>Developed by <a href="http://pomelodesign.com">Pomelo Design</a></small></p>
<?php
    }
}
function EnhancedTextWidgetInit() {
    register_widget('EnhancedTextWidget');
}

add_action('widgets_init', 'EnhancedTextWidgetInit');
?>