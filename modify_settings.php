<?php

// $Id: modify_settings.php 399 2006-12-24 07:50:44Z Ruebenwurzel $

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

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Get header and footer
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_newsarc_settings WHERE section_id = '$section_id'");
$fetch_content = $query_content->fetchRow();

// Set raw html <'s and >'s to be replace by friendly html code
$raw = array('<', '>');
$friendly = array('&lt;', '&gt;');

?>

<style type="text/css">
.setting_name {
	vertical-align: top;
}
</style>

<form name="modify" action="<?php echo WB_URL; ?>/modules/newsarc/save_settings.php" method="post" style="margin: 0;">

<input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>">

<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
	<td class="setting_name" width="100"><?php echo $TEXT['HEADER']; ?>:</td>
	<td class="setting_name">
		<textarea name="header" style="width: 100%; height: 80px;"><?php echo ($fetch_content['header']); ?></textarea>
	</td>
</tr>
<tr>
	<td class="setting_name"><?php echo $TEXT['POST'].' '.$TEXT['LOOP']; ?>:</td>
	<td class="setting_name">
		<textarea name="post_loop" style="width: 100%; height: 60px;"><?php echo ($fetch_content['post_loop']); ?></textarea>
	</td>
</tr>
<tr>
	<td class="setting_name"><?php echo $TEXT['FOOTER']; ?>:</td>
	<td class="setting_name">
		<textarea name="footer" style="width: 100%; height: 80px;"><?php echo str_replace($raw, $friendly, ($fetch_content['footer'])); ?></textarea>
	</td>
</tr>
<tr>
	<td class="setting_name"><?php echo $TEXT['POST_HEADER']; ?>:</td>
	<td class="setting_name">
		<textarea name="post_header" style="width: 100%; height: 60px;"><?php echo str_replace($raw, $friendly, ($fetch_content['post_header'])); ?></textarea>
	</td>
</tr>
<tr>
	<td class="setting_name"><?php echo $TEXT['POST_FOOTER']; ?>:</td>
	<td class="setting_name">
		<textarea name="post_footer" style="width: 100%; height: 60px;"><?php echo str_replace($raw, $friendly, ($fetch_content['post_footer'])); ?></textarea>
	</td>
</tr>
<tr>
	<td class="setting_name"><?php echo $TEXT['POSTS_PER_PAGE']; ?>:</td>
	<td class="setting_name">
		<select name="posts_per_page" style="width: 100%;">
			<option value=""><?php echo $TEXT['UNLIMITED']; ?></option>
			<?php
			for($i = 1; $i <= 20; $i++) {
				if($fetch_content['posts_per_page'] == ($i*5)) { $selected = ' selected'; } else { $selected = ''; }
				echo '<option value="'.($i*5).'"'.$selected.'>'.($i*5).'</option>';
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td><?php echo $TEXT['COMMENTING']; ?>:</td>
	<td>
		<select name="commenting" style="width: 100%;">
			<option value="none"><?php echo $TEXT['DISABLED']; ?></option>
			<option value="public" <?php if($fetch_content['commenting'] == 'public') { echo 'selected'; } ?>><?php echo $TEXT['PUBLIC']; ?></option>
			<option value="private" <?php if($fetch_content['commenting'] == 'private') { echo 'selected'; } ?>><?php echo $TEXT['PRIVATE']; ?></option>
		</select>
	</td>
</tr>
<?php if(extension_loaded('gd') AND function_exists('imageCreateFromJpeg')) { /* Make's sure GD library is installed */ ?>
<tr>
	<td class="setting_name"><?php echo $TEXT['CAPTCHA_VERIFICATION']; ?>:</td>
	<td>
		<input type="radio" name="use_captcha" id="use_captcha_true" value="1"<?php if($fetch_content['use_captcha'] == true) { echo ' checked'; } ?> />
		<label for="use_captcha_true"><?php echo $TEXT['ENABLED']; ?></label>
		<input type="radio" name="use_captcha" id="use_captcha_false" value="0"<?php if($fetch_content['use_captcha'] == false) { echo ' checked'; } ?> />
		<label for="use_captcha_false"><?php echo $TEXT['DISABLED']; ?></label>
	</td>
</tr>
<tr>
	<td>
		<?php echo $TEXT['RESIZE_IMAGE_TO']; ?>:
	</td>
	<td>
		<select name="resize" style="width: 100%;">
			<option value=""><?php echo $TEXT['NONE']; ?></option>
			<?php
			$SIZES['50'] = '50x50px';
			$SIZES['75'] = '75x75px';
			$SIZES['100'] = '100x100px';
			$SIZES['125'] = '125x125px';
			$SIZES['150'] = '150x150px';
			foreach($SIZES AS $size => $size_name) {
				if($fetch_content['resize'] == $size) { $selected = ' selected'; } else { $selected = ''; }
				echo '<option value="'.$size.'"'.$selected.'>'.$size_name.'</option>';
			}
			?>
		</select>
	</td>
</tr>
<?php } ?>
<tr>
	<td class="setting_name"><?php echo $TEXT['COMMENTS'].' '.$TEXT['HEADER']; ?>:</td>
	<td class="setting_name">
		<textarea name="comments_header" style="width: 100%; height: 60px;"><?php echo str_replace($raw, $friendly, ($fetch_content['comments_header'])); ?></textarea>
	</td>
</tr>
<tr>
	<td class="setting_name"><?php echo $TEXT['COMMENTS'].' '.$TEXT['LOOP']; ?>:</td>
	<td class="setting_name">
		<textarea name="comments_loop" style="width: 100%; height: 60px;"><?php echo str_replace($raw, $friendly, ($fetch_content['comments_loop'])); ?></textarea>
	</td>
</tr>
<tr>
	<td class="setting_name"><?php echo $TEXT['COMMENTS'].' '.$TEXT['FOOTER']; ?>:</td>
	<td class="setting_name">
		<textarea name="comments_footer" style="width: 100%; height: 60px;"><?php echo str_replace($raw, $friendly, ($fetch_content['comments_footer'])); ?></textarea>
	</td>
</tr>
<tr>
	<td class="setting_name"><?php echo $TEXT['COMMENTS'].' '.$TEXT['PAGE']; ?>:</td>
	<td class="setting_name">
		<textarea name="comments_page" style="width: 100%; height: 80px;"><?php echo str_replace($raw, $friendly, ($fetch_content['comments_page'])); ?></textarea>
	</td>
</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="105">&nbsp;</td>
	<td align="left">
		<input name="save" type="submit" value="<?php echo $TEXT['SAVE'].' '.$TEXT['SETTINGS']; ?>" style="width: 200px; margin-top: 5px;"></form>
	</td>
	<td align="right">
		<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
	</td>
</tr>
</table>


<?php

// Print admin footer
$admin->print_footer();

?>