<?php

// $Id: common.php,v 1.05 2009/06/24 23:45:00 wishcraft Exp $

require __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

xoops_cp_header();
adminMenu(6);

$opform = new XoopsThemeForm(_AM_PERM_ACTION, 'actionform', 'permissions.php', 'get');
$op_select = new XoopsFormSelect('', 'action');
$op_select->setExtra('onchange="document.forms.actionform.submit()"');
$op_select->addOptionArray(
    [
        'no' => _SELECT,
'pages' => _AM_LAWSUIT_PAGES,
'category' => _AM_LAWSUIT_CATEGORY,
'forms' => _AM_LAWSUIT_FORMS,
    ]
);
$op_select->setValue($_REQUEST['action']);
$opform->addElement($op_select);
$opform->display();

switch ($_REQUEST['action']) {
    default:
    case 'pages':

        echo "
		<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_LAWSUIT_PAGES_PERMHEADER . "</legend>\n
		<div style='padding: 2px;'>\n";

        $cat_form = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'lawsuit_pages_access', _AM_LAWSUIT_PAGES_PERMDESC, '/admin/permissions.php?action=' . $_REQUEST['action']);

        $result = $xoopsDB->query('SELECT pid, title FROM ' . $xoopsDB->prefix('lawsuit_pages'));
        if ($xoopsDB->getRowsNum($result)) {
            while (false !== ($cat_row = $xoopsDB->fetchArray($result))) {
                $cat_form->addItem($cat_row['pid'], ucfirst($cat_row['title']));
            }
        }
        echo $cat_form->render();
        echo '</div></fieldset><br>';
        unset($cat_form);
        break;
    case 'category':
        echo "
		<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_LAWSUIT_CATEGORY_PERMHEADER . "</legend>\n
		<div style='padding: 2px;'>\n";

        $cat_form = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'lawsuit_category_access', _AM_LAWSUIT_CATEGORY_PERMDESC, '/admin/permissions.php?action=' . $_REQUEST['action']);

        $result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('lawsuit_category'));
        if ($xoopsDB->getRowsNum($result)) {
            while (false !== ($cat_row = $xoopsDB->fetchArray($result))) {
                $cat_form->addItem($cat_row['cid'], ucfirst($cat_row['title']));
            }
        }
        echo $cat_form->render();
        echo '</div></fieldset><br>';
        unset($cat_form);
        break;
    case 'forms':
        echo "
		<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_LAWSUIT_FORMS_PERMHEADER . "</legend>\n
		<div style='padding: 2px;'>\n";

        $cat_form = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'lawsuit_form_access', _AM_LAWSUIT_FORMS_PERMDESC, '/admin/permissions.php?action=' . $_REQUEST['action']);

        $result = $xoopsDB->query('SELECT form_id, form_title FROM ' . $xoopsDB->prefix('lawsuit_forms'));
        if ($xoopsDB->getRowsNum($result)) {
            while (false !== ($cat_row = $xoopsDB->fetchArray($result))) {
                $cat_form->addItem($cat_row['form_id'], ucfirst($cat_row['form_title']));
            }
        }
        echo $cat_form->render();
        echo '</div></fieldset><br>';
        unset($cat_form);
}
footer_adminMenu(7);
xoops_cp_footer();
