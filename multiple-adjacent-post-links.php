<?php
/*
Plugin Name: Multiple Adjacent Post Links
Description: What the plugin name says.
Version: 0.1
Author: Jeremy Boggs
Author URI: http://clioweb.org
*/

/*
Copyright (C) 2011 Jeremy Boggs

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Retrieve any number of adjacent posts with the same post type.
 *
 * Can either be next or previous posts.
 *
 * @see get_adjacent_posts()
 * @param int $num The number of adjacent posts to return.
 * @param bool $previous Optional. Whether to retrieve previous posts.
 * @return mixed Post array if successful. Null if global $post is not set.
 * Empty string if no corresponding post exists.
 */
function multiple_get_adjacent_posts($num = 1, $previous = true) {
	global $post, $wpdb;

	if ( empty( $post ) )
		return null;

	$current_post_date = $post->post_date;

	$adjacent = $previous ? 'previous' : 'next';
	$op = $previous ? '<' : '>';
	$order = $previous ? 'DESC' : 'ASC';

	$where = $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish'", $current_post_date, $post->post_type);
	$sort  = "ORDER BY p.post_date $order LIMIT $num";

	$results = $wpdb->get_results("SELECT p.* FROM $wpdb->posts AS p $where $sort");
	
	if ( null === $results )
		$results = '';
		
	return $results;
}

/**
 * Returns links for custom adjacent posts.
 *
 * @param int The number of posts to return.
 * @param bool Whether to return previous posts.
 * @return string HTML hyperlinks if posts are available, empty string if not.
 */
function multiple_adjacent_post_links($num = 2, $previous = true) {    
    $html = '';
    
    if ( $adjacentPosts = multiple_get_adjacent_posts($num, $previous) ) {    
        foreach ($adjacentPosts as $adjacentPost) {
            $html .= '<a href="'.get_permalink($adjacentPost) .'">'.$adjacentPost->post_title .'</a>';
        }
    }
    
    return $html;
}

/**
 * Returns links for next posts.
 *
 * @param int The number of posts to return.
 * @return string HTML hyperlinks or empty string.
 */
function multiple_next_post_links($num = 2) {
    return multiple_adjacent_post_links($num, false);
}

/**
 * Returns links for previous posts.
 *
 * @param int The number of posts to return.
 * @return string HTML hyperlinks or empty string.
 */
function multiple_previous_post_links($num = 2) {
    return multiple_adjacent_post_links($num);
}