<?php

// $Id: modinfo.php,v 1.05 2009/06/24 23:45:00 wishcraft Exp $

// The name of this module
define('_MI_LAWSUIT_NAME', 'Lawsuit');

// A brief description of this module
define('_MI_LAWSUIT_DESC', 'Lawsuit forms generator');

// admin/menu.php
define('_MI_LAWSUIT_ADMENU1', 'Pages');
define('_MI_LAWSUIT_ADMENU2', 'Categories');
define('_MI_LAWSUIT_ADMENU3', 'Form listing');
define('_MI_LAWSUIT_ADMENU4', 'Create a new form');
define('_MI_LAWSUIT_ADMENU5', 'Edit form elements');
define('_MI_LAWSUIT_ADMENU6', 'Form Reports');
define('_MI_LAWSUIT_ADMENU7', 'Permissions');

//	preferences
define('_MI_LAWSUIT_TEXT_WIDTH', 'Default width of text boxes');
define('_MI_LAWSUIT_TEXT_MAX', 'Default maximum length of text boxes');
define('_MI_LAWSUIT_TAREA_ROWS', 'Default rows of text areas');
define('_MI_LAWSUIT_TAREA_COLS', 'Default columns of text areas');

//	preferences
define('_MI_LAWSUIT_MAIL_CHARSET', 'Text encoding for sending emails');

//	template descriptions
define('_MI_LAWSUIT_TMPL_MAIN_DESC', 'Main page of Lawsuit');
define('_MI_LAWSUIT_TMPL_ERROR_DESC', 'Page to show when error occurs');
define('_MI_LAWSUIT_TMPL_PAGE_DESC', 'Page to show paginated content');
define('_MI_LAWSUIT_TMPL_FORM_DESC', 'Template for forms');

//	preferences
define('_MI_LAWSUIT_MOREINFO', 'Send additional information along with the submitted data');
define('_MI_LAWSUIT_MOREINFO_USER', 'User name and url to user info page');
define('_MI_LAWSUIT_MOREINFO_IP', "Submitter's IP address");
define('_MI_LAWSUIT_MOREINFO_AGENT', "Submitter's user agent (browser info)");
define('_MI_LAWSUIT_MOREINFO_FORM', 'URL of the submitted form');
define('_MI_LAWSUIT_MAIL_CHARSET_DESC', 'Leave blank for ' . _CHARSET);
define('_MI_LAWSUIT_PREFIX', 'Text prefix for required fields');
define('_MI_LAWSUIT_SUFFIX', 'Text suffix for required fields');
define('_MI_LAWSUIT_INTRO', 'Introduction text in main page');
define('_MI_LAWSUIT_GLOBAL', 'Text to be displayed in every form page');

// admin/menu.php
define('_MI_LAWSUIT_ADMENU3', 'Create form elements');

// preferences default values
define('_MI_LAWSUIT_INTRO_DEFAULT', 'Feel free to contact us via the following means:');
define('_MI_LAWSUIT_GLOBAL_DEFAULT', '[b]* Required[/b]');

######### version 1.23 additions #########
define('_MI_LAWSUIT_UPLOADDIR', 'Physical path for storing uploaded files WITHOUT trailing slash');
define('_MI_LAWSUIT_UPLOADDIR_DESC', 'All upload files will be stored here when a form is sent via private message');
