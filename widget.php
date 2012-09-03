<?php
/**
 * Plugin Name: My Groups Widget
 * Description: Thw widget lists the groups of which the logged in used is member.
 * Author: Ulrich Sossou
 * Author URI: http://ulrichsossou.com/
 * Version: 0.1
 * Network: true
 * License: GPL2
 */
/*  Copyright 2012  Ulrich Sossou  (http://ulrichsossou.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
// Fork of the plugin of the same name by Peter Anselmo, Studio66 (http://www.studio66design.com).

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Run widget registration in corresponding BuddyPress hook
 *
 * @since 0.1
 */
function bp_my_groups_register_widget() {
	add_action( 'widgets_init', 'bp_my_groups_widget_init' );
}
add_action( 'bp_register_widgets', 'bp_my_groups_register_widget' );

/**
 * Register the widget
 *
 * @since 0.1
 */
function bp_my_groups_widget_init() {
	register_widget( 'BP_My_Groups_Widget' );
}

/**
 * My Groups widget class
 *
 * @since 0.1
 */
class BP_My_Groups_Widget extends WP_Widget {
	function __construct() {
		parent::__construct( false, $name = __( 'My Groups', 'bp-my-groups' ) );
	}

	function widget( $args, $instance ) {
		if ( ! is_user_logged_in() )
			return;

		extract( $args );

		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );


		echo $before_widget;

		if ( $instance['title'] )
			echo $before_title . $instance['title'] . $after_title;
		?>

		<?php if ( bp_has_groups( 'type=alphabetical&user_id=' . bp_loggedin_user_id() ) ) : ?>

			<ul class="my-groups-list item-list">
				<?php while ( bp_groups() ) : bp_the_group(); ?>
					<li>
						<div class="item-avatar">
							<a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar_thumb() ?></a>
						</div>

						<div class="item">
							<div class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></div>
							<?php if ( $instance['member_count'] ) : ?>
								<div class="item-meta"><span class="activity"><?php bp_group_member_count() ?></span></div>
							<?php endif; ?>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>

		<?php else: ?>

			<div class="widget-error"><?php _e( 'You have not joined any groups.', 'bp-my-groups-widget' ); ?></div>

		<?php endif; ?>

		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['member_count'] = (bool) $new_instance['member_count'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __( 'My Groups', 'bp-my-groups-widget' ),
			'member_count' => true
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = esc_attr( $instance['title'] );
		$member_count = $instance['member_count'];

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'bp-my-groups' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('member_count'); ?>">
				<input id="<?php echo $this->get_field_id('member_count'); ?>" name="<?php echo $this->get_field_name('member_count'); ?>" type="checkbox"<?php checked( true, $member_count ); ?> />
				<?php _e( 'Show member count per group', 'bp-my-groups' ); ?>
			</label>
		</p>
		<?php

	}
}
