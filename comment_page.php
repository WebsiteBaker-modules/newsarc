<?php

// $Id: comment_page.php 399 2006-12-24 07:50:44Z Ruebenwurzel $

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

// Make sure page cannot be accessed directly
if(!defined('WB_URL')) { 
	header('Location: ../index.php');
	exit(0);
}
	
// Get comments page template details from db
$query_settings = $database->query("SELECT comments_page,use_captcha FROM ".TABLE_PREFIX."mod_newsarc_settings WHERE section_id = '".SECTION_ID."'");
if($query_settings->numRows() == 0) {
	header('Location: '.WB_URL.'/pages/');
	exit(0);
} else {
	$settings = $query_settings->fetchRow();
	// Print comments page
	echo str_replace('[POST_TITLE]', POST_TITLE, ($settings['comments_page']));
	?>
	<form name="comment" action="<?php echo WB_URL.'/modules/newsarc/submit_comment.php?page_id='.PAGE_ID.'&section_id='.SECTION_ID.'&post_id='.POST_ID; ?>" method="post">
	<?php echo $TEXT['TITLE']; ?>:
	<br />
	<input type="text" name="title" maxlength="255" style="width: 90%;"<?php if(isset($_SESSION['comment_title'])) { echo ' value="'.$_SESSION['comment_title'].'"'; unset($_SESSION['comment_title']); } ?> />
	<br /><br />
	<?php echo $TEXT['COMMENT']; ?>:
	<br />
	<textarea name="comment" style="width: 90%; height: 150px;"><?php if(isset($_SESSION['comment_body'])) { echo $_SESSION['comment_body']; unset($_SESSION['comment_body']); } ?></textarea>
	<br /><br />
	<?php
	if(isset($_SESSION['captcha_error'])) {
		echo '<font color="#FF0000">'.$_SESSION['captcha_error'].'</font><br />';
		unset($_SESSION['captcha_error']);
	}
	// Captcha
	if($settings['use_captcha']) {
	$_SESSION['captcha'] = '';
	for($i = 0; $i < 5; $i++) {
		$_SESSION['captcha'] .= rand(0,9);
	}
	?>
	<table cellpadding="2" cellspacing="0" border="0">
	<tr>
	<td><?php echo $TEXT['VERIFICATION']; ?>:</td>
	<td><img src="<?php echo WB_URL; ?>/include/captcha.php?t=<?php echo time(); ?>" alt="Captcha" /></td>
	<td><input type="text" name="captcha" maxlength="5" /></td>
	</tr></table>
	<br />
	<?php
	}
	?>
	<input type="submit" name="submit" value="<?php echo $TEXT['ADD']; ?> <?php echo $TEXT['COMMENT']; ?>" />
	</form>	
	<?php
}

?>