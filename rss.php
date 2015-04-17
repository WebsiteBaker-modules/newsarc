<?php

// $Id: rss.php 496 2007-06-28 22:51:10Z Ruebenwurzel $

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

// Check that GET values have been supplied
if(isset($_GET['page_id']) AND is_numeric($_GET['page_id'])) {
	$page_id = $_GET['page_id'];
	define('PAGE_ID', $page_id);
} else {
	header('Location: '.WB_URL);
	exit(0);
}
if(isset($_GET['group_id']) AND is_numeric($_GET['group_id'])) {
	$group_id = $_GET['group_id'];
	define('GROUP_ID', $group_id);
}

// Include WB files
require_once('../../config.php');
require_once(WB_PATH.'/framework/class.frontend.php');
$database = new database();
$wb = new frontend();
$wb->page_id = $page_id;
$wb->get_page_details();
$wb->get_website_settings();

//checkout if a charset is defined otherwise use UTF-8
if(defined('DEFAULT_CHARSET')) {
	$charset=DEFAULT_CHARSET;
} else {
	$charset='utf-8';
}

// Sending XML header
header("Content-type: text/xml; charset=$charset" );

// Header info
// Required by CSS 2.0
echo '<?xml version="1.0" encoding="'.$charset.'"?>';
?> 
<rss version="2.0">
<channel>
<title><?php echo PAGE_TITLE; ?></title>
<link>http://<?php echo $_SERVER['SERVER_NAME']; ?></link>
<description> <?php echo PAGE_DESCRIPTION; ?></description>
<?php
// Optional header info 
?>
<language><?php echo DEFAULT_LANGUAGE; ?></language>
<copyright><?php echo WB_URL.$_SERVER['REQUEST_URI']; ?></copyright>
<managingEditor><?php echo SERVER_EMAIL; ?></managingEditor>
<webMaster><?php echo SERVER_EMAIL; ?></webMaster>
<category><?php echo WEBSITE_TITLE; ?></category>
<generator>Website Baker Content Management System</generator>

<?php
// Get news items from database

//Query
if(isset($group_id)) {
	$query = "SELECT * FROM ".TABLE_PREFIX."mod_newsarc_posts WHERE group_id=".$group_id." AND page_id = ".$page_id." AND active=1 ORDER BY posted_when DESC";
} else {
	$query = "SELECT * FROM ".TABLE_PREFIX."mod_newsarc_posts WHERE page_id=".$page_id." AND active=1 ORDER BY posted_when DESC";	
}
$result = $database->query($query);

//Generating the news items
while($item = $result->fetchRow($result)){ ?>

<item>
<title><![CDATA[<?php echo stripslashes($item["title"]); ?>]]></title>
<description><![CDATA[<?php echo stripslashes($item["content_short"]); ?>]]></description>
<guid><?php echo WB_URL."/pages".$item["link"].PAGE_EXTENSION; ?></guid>
<link><?php echo WB_URL."/pages".$item["link"].PAGE_EXTENSION; ?></link>
</item>

<?php } ?>

</channel>
</rss>