<?php

// $Id: ele_yn.php,v 1.05 2009/06/24 23:45:00 wishcraft Exp $

if (!preg_match('/editelement.php/', $_SERVER['PHP_SELF'])) {
    exit('Access Denied');
}

if (!empty($ele_id)) {
    if (1 == $value['_YES']) {
        $selected = '_YES';
    } else {
        $selected = '_NO';
    }
} else {
    $selected = '_YES';
}
$options = new XoopsFormRadio(_AM_ELE_DEFAULT, 'ele_value', $selected);
$options->addOption('_YES', _YES);
$options->addOption('_NO', _NO);
$output->addElement($options);
