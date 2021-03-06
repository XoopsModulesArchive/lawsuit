<?php

// $Id: ele_upload.php,v 1.05 2009/06/24 23:45:00 wishcraft Exp $

if (!preg_match('/editelement.php/', $_SERVER['PHP_SELF'])) {
    exit('Access Denied');
}

$size = !empty($value[0]) ? (int)$value[0] : 0;
$saveas = 1 != $value[3] ? 0 : 1;

$size = new XoopsFormText(_AM_ELE_UPLOAD_MAXSIZE, 'ele_value[0]', 10, 20, $size);
$size->setDescription(_AM_ELE_UPLOAD_MAXSIZE_DESC . '<br>' . _AM_ELE_UPLOAD_DESC_SIZE_NOLIMIT);

$ext = new XoopsFormText(_AM_ELE_UPLOAD_ALLOWED_EXT, 'ele_value[1]', 50, 255, $myts->stripSlashesGPC($value[1]));
$ext->setDescription(_AM_ELE_UPLOAD_ALLOWED_EXT_DESC . '<br><br>' . _AM_ELE_UPLOAD_DESC_NOLIMIT);

$mime = new XoopsFormTextArea(_AM_ELE_UPLOAD_ALLOWED_MIME, 'ele_value[2]', $myts->stripSlashesGPC($value[2]), 5, 50);
$mime->setDescription(_AM_ELE_UPLOAD_ALLOWED_MIME_DESC . '<br><br>' . _AM_ELE_UPLOAD_DESC_NOLIMIT);

$saveas = new XoopsFormSelect(_AM_ELE_UPLOAD_SAVEAS, 'ele_value[3]', $saveas);
$saveas->addOptionArray([0 => _AM_ELE_UPLOAD_SAVEAS_MAIL, 1 => _AM_ELE_UPLOAD_SAVEAS_FILE]);

$output->addElement($size, 1);
$output->addElement($ext);
$output->addElement($mime);
$output->addElement($saveas, 1);
