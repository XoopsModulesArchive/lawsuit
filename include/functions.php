<?php
// $Id: functions.php,v 1.3 2009/06/23 17:30:00 wishcraft Exp $

if (preg_match('/functions.php/', $_SERVER['PHP_SELF'])) {
    die('Access denied');
}

function xoops_module_install_lawsuit($module)
{
    $permHandler = xoops_getHandler('groupperm');
    for ($i = 1; $i < 4; $i++) {
        $perm = $permHandler->create();
        $perm->setVar('gperm_name', 'lawsuit_form_access');
        $perm->setVar('gperm_itemid', 1);
        $perm->setVar('gperm_groupid', $i);
        $perm->setVar('gperm_modid', $module->getVar('mid'));
        $permHandler->insert($perm);
    }

    $lawsuit_category_mgr = xoops_getModuleHandler('category', 'lawsuit');
    $category             = $lawsuit_category_mgr->create();
    $category->setVar('title', 'Default Category');
    $category->setVar('domain', urlencode(XOOPS_URL));
    $category->setVar('domains', [0 => urlencode(XOOPS_URL)]);
    @$lawsuit_category_mgr->insert($category)
	
	return true;
}

if (!function_exists('adminMenu')) {
    function adminMenu($currentoption = 0)
    {
        $moduleHandler = xoops_getHandler('module');
        $xoopsModule    = $moduleHandler->getByDirname('lawsuit');

        echo "<style type='text/css'>
	#form {float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . '/modules/' . 'lawsuit' . "/images/bg.gif') repeat-x left bottom; font-size:93%; line-height:normal; border-bottom: 1px solid black; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;}
			#buttontop { float:left; width:100%; background: #e7e7e7; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
	#buttonbar { float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . '/modules/' . 'lawsuit' . "/images/bg.gif') repeat-x left bottom; font-size:93%; line-height:normal; border-left: 1px solid black; border-right: 1px solid black; margin-bottom: 0px; border-bottom: 1px solid black; }
	#buttonbar ul { margin:0; margin-top: 15px; padding:10px 10px 0; list-style:none; }
	  #buttonbar li { display:inline; margin:0; padding:0; }
	  #buttonbar a { float:left; background:url('" . XOOPS_URL . '/modules/' . 'lawsuit' . "/images/left_both.gif') no-repeat left top; margin:0; padding:0 0 0 9px;  text-decoration:none; }
	  #buttonbar a span { float:left; display:block; background:url('" . XOOPS_URL . '/modules/' . 'lawsuit' . "/images/right_both.gif') no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
	  /* Commented Backslash Hack hides rule from IE5-Mac \*/
	  #buttonbar a span {float:none;}
	  /* End IE5-Mac hack */
	  #buttonbar a:hover span { color:#333; }
	  #buttonbar #current a { background-position:0 -150px; border-width:0; }
	  #buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
	  #buttonbar a:hover { background-position:0% -150px; }
	  #buttonbar a:hover span { background-position:100% -150px; }
	  </style>";

        // global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig;

        $myts = MyTextSanitizer::getInstance();

        $tblColors = [];
        // $adminmenu=array();
        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . 'lawsuit' . '/language/' . $xoopsConfig['language'] . '/modinfo.php')) {
            require_once XOOPS_ROOT_PATH . '/modules/' . 'lawsuit' . '/language/' . $xoopsConfig['language'] . '/modinfo.php';
        } else {
            require_once XOOPS_ROOT_PATH . '/modules/' . 'lawsuit' . '/language/' . 'english/modinfo.php';
        }

        require_once XOOPS_ROOT_PATH . '/modules/' . 'lawsuit' . '/admin/menu.php';

        echo "<table width=\"100%\" border='0'><tr><td>";
        echo "<div id='buttontop'>";
        echo '<table style="width: 100%; padding: 0; " cellspacing="0"><tr>';
        echo '<td style="width: 45%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;"><a class="nobutton" href="'
             . XOOPS_URL
             . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='
             . $xoopsModule->getVar('mid')
             . '">'
             . _PREFERENCES
             . '</a></td>';
        echo "<td style='font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;'><b>" . $myts->displayTarea($xoopsModule->name()) . '</td>';
        echo '</tr></table>';
        echo '</div>';
        echo "<div id='buttonbar'>";
        echo '<ul>';
        foreach ($GLOBALS['adminmenu'] as $key => $value) {
            $tblColors[$key]           = '';
            $tblColors[$currentoption] = 'current';
            echo "<li id='" . $tblColors[$key] . "'><a href=\"" . XOOPS_URL . '/modules/' . 'lawsuit' . '/' . $value['link'] . '"><span>' . $value['title'] . '</span></a></li>';
        }

        echo '</ul></div>';
        echo '</td></tr>';
        echo "<tr'><td><div id='form'>";
    }

    function footer_adminMenu()
    {
        echo '</div></td></tr>';
        echo '</table>';
    }
}


