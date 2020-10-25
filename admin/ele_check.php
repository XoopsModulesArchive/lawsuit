<?php

// $Id: ele_check.php,v 1.05 2009/06/24 23:45:00 wishcraft Exp $

if (!preg_match('/editelement.php/', $_SERVER['PHP_SELF'])) {
    exit('Access Denied');
}
$options = [];
$opt_count = 0;
if (empty($addopt) && !empty($ele_id)) {
    $keys = array_keys($value);

    for ($i = 0, $iMax = count($keys); $i < $iMax; $i++) {
        $v = htmlspecialchars($keys[$i], ENT_QUOTES | ENT_HTML5);

        $options[] = addOption('ele_value[' . $opt_count . ']', 'checked[' . $opt_count . ']', $v, 'check', $value[$keys[$i]]);

        $opt_count++;
    }
} else {
    if (isset($ele_value) && count($ele_value) > 0) {
        while ($v = each($ele_value)) {
            $v['value'] = htmlspecialchars($v['value'], ENT_QUOTES | ENT_HTML5);

            if (!empty($v['value'])) {
                $options[] = addOption('ele_value[' . $opt_count . ']', 'checked[' . $opt_count . ']', $v['value'], 'check', $checked[$v['key']]);

                $opt_count++;
            }
        }
    }

    $addopt = empty($addopt) ? 2 : $addopt;

    for ($i = 0; $i < $addopt; $i++) {
        $options[] = addOption('ele_value[' . $opt_count . ']', 'checked[' . $opt_count . ']');

        $opt_count++;
    }
}
$add_opt = addOptionsTray();
$options[] = $add_opt;
$opt_tray = new XoopsFormElementTray(_AM_ELE_OPT, '<br>');
$opt_tray->setDescription(_AM_ELE_OPT_DESC . '<br><br>' . _AM_ELE_OTHER);
for ($i = 0, $iMax = count($options); $i < $iMax; $i++) {
    $opt_tray->addElement($options[$i]);
}
$output->addElement($opt_tray);
