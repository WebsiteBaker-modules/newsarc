<?php

// $Id: view.php 399 2006-12-24 07:50:44Z Ruebenwurzel $

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

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { exit("Cannot access this file directly"); }

// Check if there is a start point defined
if(isset($_GET['p']) AND is_numeric($_GET['p']) AND $_GET['p'] >= 0) {
	$position = $_GET['p'];
} else {
	$position = 0;
}

// Get user's username, display name, email, and id - needed for insertion into post info
$users = array();
$query_users = $database->query("SELECT user_id,username,display_name,email FROM ".TABLE_PREFIX."users");
if($query_users->numRows() > 0) {
	while($user = $query_users->fetchRow()) {
		// Insert user info into users array
		$user_id = $user['user_id'];
		$users[$user_id]['username'] = $user['username'];
		$users[$user_id]['display_name'] = $user['display_name'];
		$users[$user_id]['email'] = $user['email'];
	}
}

// Get groups (title, if they are active, and their image [if one has been uploaded])
$groups[0]['title'] = '';
$groups[0]['active'] = true;
$groups[0]['image'] = '';
$query_users = $database->query("SELECT group_id,title,active FROM ".TABLE_PREFIX."mod_newsarc_groups WHERE section_id = '$section_id' ORDER BY position ASC");
if($query_users->numRows() > 0) {
	while($group = $query_users->fetchRow()) {
		// Insert user info into users array
		$group_id = $group['group_id'];
		$groups[$group_id]['title'] = ($group['title']);
		$groups[$group_id]['active'] = $group['active'];
		if(file_exists(WB_PATH.MEDIA_DIRECTORY.'/.news/image'.$group_id.'.jpg')) {
			$groups[$group_id]['image'] = WB_URL.MEDIA_DIRECTORY.'/.news/image'.$group_id.'.jpg';
		} else {
			$groups[$group_id]['image'] = '';
		}
	}
}

// Check if we should show the main page or a post itself
if(!defined('POST_ID') OR !is_numeric(POST_ID)) {

	if(isset($_GET['archive'])){
		// Check if we should only list posts from a certain group
		if(isset($_GET['g']) AND is_numeric($_GET['g'])) {
			$query_extra = " AND group_id = '".$_GET['g']."'";
			?>
			<style type="text/css">.selected_group_title { font-size: 14px; text-align: center; }</style>
			<?php
		} else {
			$query_extra = '';
		}
		
		// Get total number of posts
		$query_dates = $database->query("SELECT posted_when FROM ".TABLE_PREFIX."mod_newsarc_posts WHERE section_id = '$section_id' AND active = '1' AND title != ''$query_extra");
		$dates_num = $query_dates->numRows();
	
		// set the month and year if selected
		if(isset($_GET['l']) && isset($_GET['h'])){
			$query_date = "AND posted_when >= '".$_GET['l']."' AND posted_when <= '".$_GET['h']."'";
		}
		else { $query_date = ''; }
		
		// Query posts (for this page)
		$query_posts = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_newsarc_posts WHERE section_id = '$section_id' AND active = '1' AND title != '' $query_date $query_extra ORDER BY posted_when DESC");
		$num_posts = $query_posts->numRows();
		
		if($num_posts > 0) {
			if($query_extra != '') {
				?>
				<div class="selected_group_title">
					<?php echo '<a href="'.$_SERVER['PHP_SELF'].'">'.PAGE_TITLE.'</a> >> '.$groups[$_GET['g']]['title']; ?>
				</div>
				<?php
			}
			$month_current = '';
			$month_prev = 'Jan 1900';
			while($post = $query_posts->fetchRow()) {
				if(isset($groups[$post['group_id']]['active']) AND $groups[$post['group_id']]['active'] != false) { // Make sure parent group is active
					$uid = $post['posted_by']; // User who last modified the post
					// Workout date and time of last modified post
					$post_date = gmdate(DATE_FORMAT, $post['posted_when']+TIMEZONE);
					$post_time = gmdate(TIME_FORMAT, $post['posted_when']+TIMEZONE);
					$month_current = gmdate('F Y', $post['posted_when']+TIMEZONE);
					$days_inmonth = gmdate('t', $post['posted_when']+TIMEZONE);
					$curr_month = gmdate('n', $post['posted_when']+TIMEZONE);
					$curr_year = gmdate('Y', $post['posted_when']+TIMEZONE);
					$date_low = mktime(0,0,0,$curr_month,1,$curr_year);
					$date_high = mktime(23,59,59,$curr_month,$days_inmonth,$curr_year);
					//echo $date_low.'-'.$date_high;
					// Work-out the post link
					$post_link = page_link($post['link']);
					if(isset($_GET['p']) AND $position > 0) {
						$post_link .= '?p='.$position;
					}
					if(isset($_GET['g']) AND is_numeric($_GET['g'])) {
						if(isset($_GET['p']) AND $position > 0) { $post_link .= '&'; } else { $post_link .= '?'; }
						$post_link .= 'g='.$_GET['g'];
					}
					if($month_current != $month_prev){
						echo '<h2 class="orange" style="text-transform: none;"><a href="?archive=all&l='.$date_low.'&h='.$date_high.'">'.$month_current.'</a></h3>';
						$month_prev = $month_current;
					}
					echo '<div class="archivelink"><a href="'.$post_link.'">'.$post['title']. ' - ' .$post_date.'</a></div>';
				}
			}
		}
	} //end if get archive
	
	else{
	
		echo '<div><a href="?archive=all">View Archive</a></div>';
		// Check if we should only list posts from a certain group
		if(isset($_GET['g']) AND is_numeric($_GET['g'])) {
			$query_extra = " AND group_id = '".$_GET['g']."'";
			?>
			<style type="text/css">.selected_group_title { font-size: 14px; text-align: center; }</style>
			<?php
		} else {
			$query_extra = '';
		}
		
		// Get settings
		$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_newsarc_settings WHERE section_id = '$section_id'");
		if($query_settings->numRows() > 0) {
			$fetch_settings = $query_settings->fetchRow();
			$setting_header = ($fetch_settings['header']);
			$setting_post_loop = ($fetch_settings['post_loop']);
			$setting_footer = ($fetch_settings['footer']);
			$setting_posts_per_page = $fetch_settings['posts_per_page'];
		} else {
			$setting_header = '';
			$setting_post_loop = '';
			$setting_footer = '';
			$setting_posts_per_page = '';
		}
		
		// Get total number of posts
		$query_total_num = $database->query("SELECT post_id FROM ".TABLE_PREFIX."mod_newsarc_posts WHERE section_id = '$section_id' AND active = '1' AND title != ''$query_extra");
		$total_num = $query_total_num->numRows();
	
		// Work-out if we need to add limit code to sql
		if($setting_posts_per_page != 0) {
			$limit_sql = " LIMIT $position,$setting_posts_per_page";
		} else {
			$limit_sql = "";
		}
		$limit_sql = " LIMIT 0,10";
		// Query posts (for this page)
		$query_posts = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_newsarc_posts WHERE section_id = '$section_id' AND active = '1' AND title != ''$query_extra ORDER BY posted_when DESC".$limit_sql);
		$num_posts = $query_posts->numRows();
		
		// Create previous and next links
		if($setting_posts_per_page != 0) {
			if($position > 0) {
				if(isset($_GET['g']) AND is_numeric($_GET['g'])) {
					$pl_prepend = '<a href="?p='.($position-$setting_posts_per_page).'&g='.$_GET['g'].'"><< ';
				} else {
					$pl_prepend = '<a href="?p='.($position-$setting_posts_per_page).'"><< ';
				}
				$pl_append = '</a>';
				$previous_link = $pl_prepend.$TEXT['PREVIOUS'].$pl_append;
				$previous_page_link = $pl_prepend.$TEXT['PREVIOUS_PAGE'].$pl_append;
			} else {
				$previous_link = '';
				$previous_page_link = '';
			}
			if($position+$setting_posts_per_page >= $total_num) {
				$next_link = '';
				$next_page_link = '';
			} else {
				if(isset($_GET['g']) AND is_numeric($_GET['g'])) {
					$nl_prepend = '<a href="?p='.($position+$setting_posts_per_page).'&g='.$_GET['g'].'"> ';
				} else {
					$nl_prepend = '<a href="?p='.($position+$setting_posts_per_page).'"> ';
				}
				$nl_append = ' >></a>';
				$next_link = $nl_prepend.$TEXT['NEXT'].$nl_append;
				$next_page_link = $nl_prepend.$TEXT['NEXT_PAGE'].$nl_append;
			}
			if($position+$setting_posts_per_page > $total_num) {
				$num_of = $position+$num_posts;
			} else {
				$num_of = $position+$setting_posts_per_page;
			}
			$out_of = ($position+1).'-'.$num_of.' '.strtolower($TEXT['OUT_OF']).' '.$total_num;
			$of = ($position+1).'-'.$num_of.' '.strtolower($TEXT['OF']).' '.$total_num;
			$display_previous_next_links = '';
		} else {
			$display_previous_next_links = 'none';
		}
			
		// Print header
		if($display_previous_next_links == 'none') {
			//echo  str_replace(array('[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]'), array('','','','','','', $display_previous_next_links), $setting_header);
		} else {
			//echo str_replace(array('[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]'), array($next_page_link, $next_link, $previous_page_link, $previous_link, $out_of, $of, $display_previous_next_links), $setting_header);
		}
		
		echo  str_replace(array('[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]'), array('','','','','','', $display_previous_next_links), $setting_header);
		
		if($num_posts > 0) {
			if($query_extra != '') {
				?>
				<div class="selected_group_title">
					<?php echo '<a href="'.$_SERVER['PHP_SELF'].'">'.PAGE_TITLE.'</a> >> '.$groups[$_GET['g']]['title']; ?>
				</div>
				<?php
			}
			while($post = $query_posts->fetchRow()) {
				if(isset($groups[$post['group_id']]['active']) AND $groups[$post['group_id']]['active'] != false) { // Make sure parent group is active
					$uid = $post['posted_by']; // User who last modified the post
					// Workout date and time of last modified post
					$post_date = gmdate(DATE_FORMAT, $post['posted_when']+TIMEZONE);
					$post_time = gmdate(TIME_FORMAT, $post['posted_when']+TIMEZONE);
					$post_daterange = $post['daterange'];
					// Work-out the post link
					$post_link = page_link($post['link']);
					if(isset($_GET['p']) AND $position > 0) {
						$post_link .= '?p='.$position;
					}
					if(isset($_GET['g']) AND is_numeric($_GET['g'])) {
						if(isset($_GET['p']) AND $position > 0) { $post_link .= '&'; } else { $post_link .= '?'; }
						$post_link .= 'g='.$_GET['g'];
					}
					// Get group id, title, and image
					$group_id = $post['group_id'];
					$group_title = $groups[$group_id]['title'];
					$group_image = $groups[$group_id]['image'];
					if($group_image == '') { $display_image = 'none'; } else { $display_image = ''; }
					if($group_id == 0) { $display_group = 'none'; } else { $display_group = ''; }
					// Replace [wblink--PAGE_ID--] with real link
					$short = ($post['content_short']);
					$wb->preprocess($short);
					// Replace vars with values
					$post_long_len = strlen($post['content_long']);
					$vars = array('[PAGE_TITLE]', '[GROUP_ID]', '[GROUP_TITLE]', '[GROUP_IMAGE]', '[DISPLAY_GROUP]', '[DISPLAY_IMAGE]', '[TITLE]', '[DATERANGE]', '[SHORT]', '[LINK]', '[DATE]', '[TIME]', '[USER_ID]', '[USERNAME]', '[DISPLAY_NAME]', '[EMAIL]', '[TEXT_READ_MORE]');
					if(isset($users[$uid]['username']) AND $users[$uid]['username'] != '') {
						if($post_long_len < 9) {
							$values = array(PAGE_TITLE, $group_id, $group_title, $group_image, $display_group, $display_image, $post['title'], $post_daterange, $short, $post_link, $post_date, $post_time, $uid, $users[$uid]['username'], $users[$uid]['display_name'], $users[$uid]['email'], '');
						} else {
							$values = array(PAGE_TITLE, $group_id, $group_title, $group_image, $display_group, $display_image, $post['title'], $post_daterange, $short, $post_link, $post_date, $post_time, $uid, $users[$uid]['username'], $users[$uid]['display_name'], $users[$uid]['email'], $TEXT['READ_MORE']);
						}
					} else {
						if($post_long_len < 9) {
							$values = array(PAGE_TITLE, $group_id, $group_title, $group_image, $display_group, $display_image, $post['title'], $post_daterange, $short, $post_link, $post_date, $post_time, '', '', '', '', '');
						} else {
							$values = array(PAGE_TITLE, $group_id, $group_title, $group_image, $display_group, $display_image, $post['title'], $post_daterange, $short, $post_link, $post_date, $post_time, '', '', '', '', $TEXT['READ_MORE']);
						}
					}
					echo str_replace($vars, $values, $setting_post_loop);
				}
			}
		}
		
		// Print footer
		if($display_previous_next_links == 'none') {
			//echo  str_replace(array('[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]'), array('','','','','','', $display_previous_next_links), $setting_footer);
		} else {
			//echo str_replace(array('[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]'), array($next_page_link, $next_link, $previous_page_link, $previous_link, $out_of, $of, $display_previous_next_links), $setting_footer);
		}
		echo  str_replace(array('[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]'), array('','','','','','', $display_previous_next_links), $setting_footer);
	} // end else not archive
} elseif(defined('POST_ID') AND is_numeric(POST_ID)) {
	
	// Get settings
	$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_newsarc_settings WHERE section_id = '$section_id'");
	if($query_settings->numRows() > 0) {
		$fetch_settings = $query_settings->fetchRow();
		$setting_post_header = ($fetch_settings['post_header']);
		$setting_post_footer = ($fetch_settings['post_footer']);
		$setting_comments_header = ($fetch_settings['comments_header']);
		$setting_comments_loop = ($fetch_settings['comments_loop']);
		$setting_comments_footer = ($fetch_settings['comments_footer']);
	} else {
		$setting_post_header = '';
		$setting_post_footer = '';
		$setting_comments_header = '';
		$setting_comments_loop = '';
		$setting_comments_footer = '';
	}
	
	// Get page info
	$query_page = $database->query("SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '".PAGE_ID."'");
	if($query_page->numRows() > 0) {
		$page = $query_page->fetchRow();
		$page_link = page_link($page['link']);
		if(isset($_GET['p']) AND $position > 0) {
			$page_link .= '?p='.$_GET['p'];
		}
		if(isset($_GET['g']) AND is_numeric($_GET['g'])) {
			if(isset($_GET['p']) AND $position > 0) { $page_link .= '&'; } else { $page_link .= '?'; }
			$page_link .= 'g='.$_GET['g'];
		}
	} else {
		exit('Page not found');
	}
	
	// Get post info
	$query_post = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_newsarc_posts WHERE post_id = '".POST_ID."' AND active = '1'");
	if($query_post->numRows() > 0) {
		$post = $query_post->fetchRow();
		if(isset($groups[$post['group_id']]['active']) AND $groups[$post['group_id']]['active'] != false) { // Make sure parent group is active
			$uid = $post['posted_by']; // User who last modified the post
			// Workout date and time of last modified post
			$post_date = gmdate(DATE_FORMAT, $post['posted_when']+TIMEZONE);
			$post_time = gmdate(TIME_FORMAT, $post['posted_when']+TIMEZONE);
			// Get group id, title, and image
			$group_id = $post['group_id'];
			$group_title = $groups[$group_id]['title'];
			$group_image = $groups[$group_id]['image'];
			if($group_image == '') { $display_image = 'none'; } else { $display_image = ''; }
			if($group_id == 0) { $display_group = 'none'; } else { $display_group = ''; }
			$vars = array('[PAGE_TITLE]', '[GROUP_ID]', '[GROUP_TITLE]', '[GROUP_IMAGE]', '[DISPLAY_GROUP]', '[DISPLAY_IMAGE]', '[TITLE]', '[SHORT]', '[BACK]', '[DATE]', '[TIME]', '[USER_ID]', '[USERNAME]', '[DISPLAY_NAME]', '[EMAIL]');
			$post_short=$post['content_short'];
			$wb->preprocess($post_short);
			if(isset($users[$uid]['username']) AND $users[$uid]['username'] != '') {
				$values = array(PAGE_TITLE, $group_id, $group_title, $group_image, $display_group, $display_image, $post['title'], $post_short, $page_link, $post_date, $post_time, $uid, $users[$uid]['username'], $users[$uid]['display_name'], $users[$uid]['email']);
			} else {
				$values = array(PAGE_TITLE, $group_id, $group_title, $group_image, $display_group, $display_image, $post['title'], $post_short, $page_link, $post_date, $post_time, '', '', '', '');
			}
			$post_long = ($post['content_long']);
		}
	} else {
		header('Location: '.WB_URL.'/pages/');
		exit(0);
	}
	
	// Print post header
	echo str_replace($vars, $values, $setting_post_header);
	
	// Replace [wblink--PAGE_ID--] with real link
  	$wb->preprocess($post_long);
	// Print long
	echo $post_long;
	
	// Print post footer
	echo str_replace($vars, $values, $setting_post_footer);
	
	// Show comments section if we have to
	if($post['commenting'] == 'private' AND isset($admin) AND $admin->is_authenticated() == true OR $post['commenting'] == 'public') {
		
		// Print comments header
		echo str_replace('[ADD_COMMENT_URL]', WB_URL.'/modules/newsarc/comment.php?id='.POST_ID, $setting_comments_header);
		
		// Query for comments
		$query_comments = $database->query("SELECT title,comment,commented_when,commented_by FROM ".TABLE_PREFIX."mod_newsarc_comments WHERE post_id = '".POST_ID."' ORDER BY commented_when ASC");
		if($query_comments->numRows() > 0) {
			while($comment = $query_comments->fetchRow()) {
				// Display Comments without slashes, but with new-line characters
				$comment['comment'] = nl2br(($comment['comment']));
				$comment['title'] = ($comment['title']);
				// Print comments loop
				$commented_date = gmdate(DATE_FORMAT, $comment['commented_when']+TIMEZONE);
				$commented_time = gmdate(TIME_FORMAT, $comment['commented_when']+TIMEZONE);
				$uid = $comment['commented_by'];
				$vars = array('[TITLE]','[COMMENT]','[DATE]','[TIME]','[USER_ID]','[USERNAME]','[DISPLAY_NAME]', '[EMAIL]');
				if(isset($users[$uid]['username']) AND $users[$uid]['username'] != '') {
					$values = array(($comment['title']), ($comment['comment']), $commented_date, $commented_time, $uid, ($users[$uid]['username']), ($users[$uid]['display_name']), ($users[$uid]['email']));
				} else {
					$values = array(($comment['title']), ($comment['comment']), $commented_date, $commented_time, '0', strtolower($TEXT['UNKNOWN']), $TEXT['UNKNOWN'], '');
				}
				echo str_replace($vars, $values, $setting_comments_loop);
			}
		} else {
			// Say no comments found
			if(isset($TEXT['NONE_FOUND'])) {
				echo $TEXT['NONE_FOUND'].'<br />';
			} else {
				echo 'None Found<br />';
			}
		}
		
		// Print comments footer
		echo str_replace('[ADD_COMMENT_URL]', WB_URL.'/modules/newsarc/comment.php?id='.POST_ID, $setting_comments_footer);
		
	}
		
}

?>