<?php

// $Id: add.php 399 2006-12-24 07:50:44Z Ruebenwurzel $

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

$header = '<style type=\"text/css\">
.post_title, .post_date { border-bottom: 1px solid #DDDDDD; }
.post_title { font-weight: bold; font-size: 12px; color: #000000; }
.post_date { text-align: right; font-weight: bold; }
.post_short { text-align: justify; padding-bottom: 5px; }
</style>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"98%\">';
$post_loop = '<tr class=\"post_top\">
<td class=\"post_title\"><a href=\"[LINK]\">[TITLE]</a></td>
<td class=\"post_date\">[TIME], [DATE]</td>
</tr>
<tr>
<td class=\"post_short\" colspan=\"2\">
[SHORT] 
<a href=\"[LINK]\">[TEXT_READ_MORE]</a>
</td>
</tr>';
$footer = '</table>
<table cellpadding="0" cellspacing="0" border="0" width="98%" style="display: [DISPLAY_PREVIOUS_NEXT_LINKS]">
<tr>
<td width="35%" align="left">[PREVIOUS_PAGE_LINK]</td>
<td width="30%" align="center">[OF]</td>
<td width="35%" align="right">[NEXT_PAGE_LINK]</td>
</tr>
</table>';
$post_header = addslashes('<table cellpadding="0" cellspacing="0" border="0" width="98%">
<tr>
<td height="30"><h1>[TITLE]</h1></td>
<td rowspan="3" style="display: [DISPLAY_IMAGE]"><img src="[GROUP_IMAGE]" alt="[GROUP_TITLE]" /></td>
</tr>
<tr>
<td valign="top"><b>Posted by [DISPLAY_NAME] ([USERNAME]) on [DATE] at [TIME]</b></td>
</tr>
<tr style="display: [DISPLAY_GROUP]">
<td valign="top"><a href="[BACK]">[PAGE_TITLE]</a> >> <a href="[BACK]?g=[GROUP_ID]">[GROUP_TITLE]</a></td>
</tr>
</table>
<p style="text-align: justify;">');
$post_footer = '</p>
<a href=\"[BACK]\">Back</a>';
$comments_header = addslashes('<br /><br />
<style type="text/css">
.comment_title { font-weight: bold; }
.comment_text { font-weight: bold; background-color: #FDFDFD; border-bottom: 1px solid #DDDDDD; padding-bottom: 15px; }
.comment_title, .comment_text { border-left: 1px solid #DDDDDD; }
.comment_info { text-align: right; border-right: 1px solid #DDDDDD; }
.comment_title, .comment_info { border-top: 1px solid #DDDDDD; background-color: #EEEEEE; }
</style>
<h2>Comments</h2>
<table cellpadding="2" cellspacing="0" border="0" width="98%">');
$comments_loop = addslashes('<tr>
<td class="comment_title">[TITLE]</td>
<td class="comment_info">By [DISPLAY_NAME] on [DATE] at [TIME]</td>
</tr>
<tr>
<td colspan="2" class="comment_text">[COMMENT]</td>
</tr>');
$comments_footer = '</table>
<br /><a href=\"[ADD_COMMENT_URL]\">Add Comment</a>';
$comments_page = '<h1>Comment</h1>
<h2>[POST_TITLE]</h2>
<br />';
$commenting = 'none';
$use_captcha = true;
$database->query("INSERT INTO ".TABLE_PREFIX."mod_newsarc_settings (section_id,page_id,header,post_loop,footer,post_header,post_footer,comments_header,comments_loop,comments_footer,comments_page,commenting,use_captcha) VALUES ('$section_id','$page_id','$header','$post_loop','$footer','$post_header','$post_footer','$comments_header','$comments_loop','$comments_footer','$comments_page','$commenting','$use_captcha')");

?>