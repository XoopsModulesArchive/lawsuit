<?php

// $Id: admin_header.php,v 1.05 2009/06/24 23:45:00 wishcraft Exp $

require dirname(__DIR__, 3) . '/include/cp_header.php';
require dirname(__DIR__) . '/include/common.php';
require dirname(__DIR__) . '/include/forms.php';
define('LAWSUIT_ADMIN_URL', LAWSUIT_URL . 'admin/index.php');

function adminHtmlHeader()
{
    xoops_cp_header();

    //	//$xTheme->loadModuleAdminMenu(0);
}
