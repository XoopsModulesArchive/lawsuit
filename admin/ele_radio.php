<?php

// $Id: ele_radio.php,v 1.05 2009/06/24 23:45:00 wishcraft Exp $

if (!preg_match('/editelement.php/', $_SERVER['PHP_SELF'])) {
    exit('Access Denied');
}
$options = [];
$opt_count = 0;
if (empty($addopt) && !empty($ele_id)) {
    $keys = array_keys($value);

    for ($i = 0, $iMax = count($keys); $i < $iMax; $i++) {
        $r = $value[$keys[$i]] ? $opt_count : null;

        $v = htmlspecialchars($keys[$i], ENT_QUOTES | ENT_HTML5);

        $options[] = addOption('ele_value[' . $opt_count . ']', $opt_count, $v, 'radio', $r);

        $opt_count++;
    }
} else {
    if (isset($ele_value) && count($ele_value) > 0) {
        while ($v = each($ele_value)) {
            $t['value'] = htmlspecialchars($v['value'], ENT_QUOTES | ENT_HTML5);

            if (!empty($v['value'])) {
                $r = ($checked == $opt_count) ? $opt_count : null;

                $options[] = addOption('ele_value[' . $opt_count . ']', $opt_count, $t['value'], 'radio', $r);

                $opt_count++;
            }
        }
    }

    $addopt = empty($addopt) ? 2 : $addopt;

    for ($i = 0; $i < $addopt; $i++) {
        $options[] = addOption('ele_value[' . $opt_count . ']', $opt_count, '', 'radio');

        $opt_count++;
    }
}
$options[] = addOptionsTray();
$opt_tray = new XoopsFormElementTray(_AM_ELE_OPT, '<br>');
$opt_tray->setDescription(_AM_ELE_OPT_DESC2 . '<br><br>' . _AM_ELE_OTHER);
for ($i = 0, $iMax = count($options); $i < $iMax; $i++) {
    $opt_tray->addElement($options[$i]);
}
$output->addElement($opt_tray);
