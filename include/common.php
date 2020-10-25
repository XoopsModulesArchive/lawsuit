<?php

// $Id: common.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $
require __DIR__ . '/functions.php';

$moduleHandler = xoops_getHandler('module');
$xoopsModule = $moduleHandler->getByDirname('lawsuit');

if (!defined('LAWSUIT_CONSTANTS_DEFINED')) {
    define('LAWSUIT_URL', XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/');

    define('LAWSUIT_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/');

    define('LAWSUIT_UPLOAD_PATH', $xoopsModuleConfig['uploaddir'] . '/');

    define('LAWSUIT_CONSTANTS_DEFINED', true);
}

$lawsuit_form_mgr = xoops_getModuleHandler('forms', 'lawsuit');
$lawsuit_category_mgr = xoops_getModuleHandler('category', 'lawsuit');
$lawsuit_response_mgr = xoops_getModuleHandler('response', 'lawsuit');
$lawsuit_pages_mgr = xoops_getModuleHandler('pages', 'lawsuit');

if (false !== LAWSUIT_UPLOAD_PATH) {
    if (!is_dir(LAWSUIT_UPLOAD_PATH)) {
        $oldumask = umask(0);

        mkdir(LAWSUIT_UPLOAD_PATH, 0777);

        umask($oldumask);
    }

    if (is_dir(LAWSUIT_UPLOAD_PATH) && !is_writable(LAWSUIT_UPLOAD_PATH)) {
        chmod(LAWSUIT_UPLOAD_PATH, 0777);
    }
}
