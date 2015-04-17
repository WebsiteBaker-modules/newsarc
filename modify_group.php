<?php

// $Id: modify_group.php 399 2006-12-24 07:50:44Z Ruebenwurzel $

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
if(!isset($_GET['group_id']) OR !is_numeric($_GET['group_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
	exit(0);
} else {
	$group_id = $_GET['group_id'];
}

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Get header and footer
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_newsarc_groups WHERE group_id = '$group_id'");
$fetch_content = $query_content->fetchRow();

?>

<form name="modify" action="<?php echo WB_URL; ?>/modules/newsarc/save_group.php" method="post" enctype="multipart/form-data" style="margin: 0;">

<input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
<input type="hidden" name="group_id" value="<?php echo $group_id; ?>">

<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr>
	<td width="80"><?php echo $TEXT['TITLE']; ?>:</td>
	<td>
		<input type="text" name="title" value="<?php echo (htmlspecialchars($fetch_content['title'])); ?>" style="width: 100%;" maxlength="255" />
	</td>
</tr>
<tr>
	<td><?php echo $TEXT['IMAGE']; ?>:</td>
	<?php if(file_exists(WB_PATH.MEDIA_DIRECTORY.'/.news/image'.$group_id.'.jpg')) { ?>
	<td>
		<a href="<?php echo WB_URL.MEDIA_DIRECTORY; ?>/.news/image<?php echo $group_id; ?>.jpg" target="_blank">View</a>
		&nbsp;
		<input type="checkbox" name="delete_image" id="delete_image" value="true" />
		<label for="delete_image">Delete</label>
	</td>
	<?php } else { ?>
	<td>
		<input type="file" name="image" />
	</td>
	<?php } ?>
</tr>
<tr>
	<td><?php echo $TEXT['ACTIVE']; ?>:</td>
	<td>
		<input type="radio" name="active" id="active_true" value="1" <?php if($fetch_content['active'] == 1) { echo ' checked'; } ?> />
		<a href="#" onclick="javascript: document.getElementById('active_true').checked = true;">
		<?php echo $TEXT['YES']; ?>
		</a>
		-
		<input type="radio" name="active" id="active_false" value="0" <?php if($fetch_content['active'] == 0) { echo ' checked'; } ?> />
		<a href="#" onclick="javascript: document.getElementById('active_false').checked = true;">
		<?php echo $TEXT['NO']; ?>
		</a>
	</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td align="left">
		<input name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 200px; margin-top: 5px;"></form>
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