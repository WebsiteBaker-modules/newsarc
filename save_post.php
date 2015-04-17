<?php

// $Id: save_post.php 399 2006-12-24 07:50:44Z Ruebenwurzel $

/*

 Website Baker Project <http://www.websitebaker.org/>
 Copyright (C) 2004-2007, Ryan Djurovich

 Website Baker is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Website Baker is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Website Baker; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

require('../../config.php');

// Get id
if(!isset($_POST['post_id']) OR !is_numeric($_POST['post_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
} else {
	$id = $_POST['post_id'];
	$post_id = $id;
}

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

// Validate all fields
if($admin->get_post('title') == '' AND $admin->get_post('url') == '') {
	$admin->print_error($MESSAGE['GENERIC']['FILL_IN_ALL'], WB_URL.'/modules/newsarc/modify_post.php?page_id='.$page_id.'&section_id='.$section_id.'&post_id='.$id);
} else {
	$title = $admin->add_slashes($admin->get_post('title'));
	$daterange = $admin->add_slashes($admin->get_post('daterange'));
	$short = $admin->add_slashes($admin->get_post('short'));
	$long = $admin->add_slashes($admin->get_post('long'));
	$commenting = $admin->get_post('commenting');
	$active = $admin->get_post('active');
	$old_link = $admin->get_post('link');
	$group_id = $admin->get_post('group');
}

// Get page link URL
$query_page = $database->query("SELECT level,link FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
$page = $query_page->fetchRow();
$page_level = $page['level'];
$page_link = $page['link'];

// Include WB functions file
require(WB_PATH.'/framework/functions.php');

// Work-out what the link should be
$post_link = '/posts/'.page_filename($title).$post_id;

// Make sure the post link is set and exists
// Make newsarc post access files dir
make_dir(WB_PATH.PAGES_DIRECTORY.'/posts/');
if(!is_writable(WB_PATH.PAGES_DIRECTORY.'/posts/')) {
	$admin->print_error($MESSAGE['PAGES']['CANNOT_CREATE_ACCESS_FILE']);
} elseif($old_link != $post_link OR !file_exists(WB_PATH.PAGES_DIRECTORY.$post_link.'.php')) {
	// We need to create a new file
	// First, delete old file if it exists
	if(file_exists(WB_PATH.$old_link.'.php')) {
		unlink(WB_PATH.$old_link.'.php');
	}
	// Specify the filename
	$filename = WB_PATH.PAGES_DIRECTORY.'/'.$post_link.'.php';
	// The depth of the page directory in the directory hierarchy
	// '/pages' is at depth 1
	$pages_dir_depth=count(explode('/',PAGES_DIRECTORY))-1;
	// Work-out how many ../'s we need to get to the index page
	$index_location = '../';
	for($i = 0; $i < $pages_dir_depth; $i++) {
		$index_location .= '../';
	}
	// Write to the filename
	$content = ''.
'<?php
$page_id = '.$page_id.';
$section_id = '.$section_id.';
$post_id = '.$post_id.';
define("POST_ID", $post_id);
require("'.$index_location.'config.php");
require(WB_PATH."/index.php");
?>';
	$handle = fopen($filename, 'w');
	fwrite($handle, $content);
	fclose($handle);
	change_mode($filename);
}

//$post_date = strtotime(substr($_POST['post_date'],6,4)."-".substr($_POST['post_date'],3,2)."-".substr($_POST['post_date'],0,2)." ".$_POST['hour'].":".$_POST['minute']." ".$_POST['day']);
$post_date = mktime($_POST['hour'], $_POST['minute'], 0, substr($_POST['post_date'],3,2), substr($_POST['post_date'],0,2), substr($_POST['post_date'],6,4));

// Update row
//$database->query("UPDATE ".TABLE_PREFIX."mod_news_posts SET group_id = '$group_id', title = '$title', link = '$post_link', content_short = '$short', content_long = '$long', commenting = '$commenting', active = '$active', posted_when = '".mktime()."', posted_by = '".$admin->get_user_id()."' WHERE post_id = '$post_id'");
$database->query("UPDATE ".TABLE_PREFIX."mod_newsarc_posts SET group_id = '$group_id', title = '$title', daterange = '$daterange', link = '$post_link', content_short = '$short', content_long = '$long', commenting = '$commenting', active = '$active', posted_when = '".$post_date."', posted_by = '".$admin->get_user_id()."' WHERE post_id = '$post_id'");

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
	$admin->print_error($database->get_error(), WB_URL.'/modules/newsarc/modify_post.php?page_id='.$page_id.'&section_id='.$section_id.'&post_id='.$id);
} else {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();

?>