<?php

// $Id: form_execute.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $

if (preg_match('/form_execute.php/', $_SERVER['PHP_SELF'])) {
    die('Access denied');
}

$lawsuit_ele_mgr = xoops_getModuleHandler('elements');
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('form_id', $form->getVar('form_id')), 'AND');
$criteria->add(new Criteria('ele_display', 1), 'AND');
$criteria->setSort('ele_order');
$criteria->setOrder('ASC');
$elements = &$lawsuit_ele_mgr->getObjects($criteria, true);

$msg = $err = $attachments = [];
foreach ($_POST as $k => $v) {
    if (preg_match('/^ele_[0-9]+$/', $k)) {
        $n = explode('_', $k);

        $ele[$n[1]] = $v;
    }
}
if (isset($_POST['xoops_upload_file']) && is_array($_POST['xoops_upload_file'])) {
    foreach ($_POST['xoops_upload_file'] as $k => $v) {
        $n = explode('_', $v);

        $ele[$n[1]] = $v;
    }
}

foreach ($elements as $i) {
    $ele_id = $i->getVar('ele_id');

    $ele_type = $i->getVar('ele_type');

    $ele_value = $i->getVar('ele_value');

    $ele_req = $i->getVar('ele_req');

    $ele_caption = $i->getVar('ele_caption');

    if (isset($ele[$ele_id])) {
        if ('' != $i->getVar('ele_caption')) {
            $msg[$ele_id] = "\n" . $myts->stripSlashesGPC($i->getVar('ele_caption')) . "\n";
        }

        switch ($ele_type) {
            case 'upload':
            case 'uploadimg':
                if (isset($_FILES['ele_' . $ele_id])) {
                    require_once LAWSUIT_ROOT_PATH . 'class/uploader.php';

                    $ext = empty($ele_value[1]) ? 0 : explode('|', $ele_value[1]);

                    $mime = empty($ele_value[2]) ? 0 : explode('|', $ele_value[2]);

                    if ('uploadimg' == $ele_type) {
                        $uploader[$ele_id] = new LawsuitMediaUploader(LAWSUIT_UPLOAD_PATH, $ele_value[0], $ext, $mime, $ele_value[4], $ele_value[5]);
                    } else {
                        $uploader[$ele_id] = new LawsuitMediaUploader(LAWSUIT_UPLOAD_PATH, $ele_value[0], $ext, $mime);
                    }

                    if (0 == $ele_value[0]) {
                        $uploader[$ele_id]->noAdminSizeCheck(true);
                    }

                    if ($uploader[$ele_id]->fetchMedia('ele_' . $ele_id, null, $i)) {
                        $attachments[] = [
                            'id' => $ele_id,
'path' => $_FILES['ele_' . $ele_id]['tmp_name'],
'name' => $_FILES['ele_' . $ele_id]['name'],
'saveto' => $ele_value[3],
                        ];
                    } else {
                        if (count($uploader[$ele_id]->errors) > 0) {
                            $err[] = $uploader[$ele_id]->getErrors();
                        }
                    }
                }
                break;
            case 'text':
                $ele[$ele_id] = trim($ele[$ele_id]);
                if (preg_match('/\{EMAIL\}/', $ele_value[2])) {
                    if (!checkEmail($ele[$ele_id])) {
                        $err[] = _LAWSUIT_ERR_INVALIDMAIL;
                    } else {
                        $reply_mail = $ele[$ele_id];
                    }
                }
                if (preg_match('/\{UNAME\}/', $ele_value[2])) {
                    $reply_name = $ele[$ele_id];
                }
                $msg[$ele_id] .= $myts->stripSlashesGPC($ele[$ele_id]);
                $resp[$ele_caption][] = $myts->stripSlashesGPC($ele[$ele_id]);
                break;
            case 'textarea':
                $msg[$ele_id] .= $myts->stripSlashesGPC($ele[$ele_id]);
                $resp[$ele_caption][] = $myts->stripSlashesGPC($ele[$ele_id]);
                break;
            case 'radio':
                $opt_count = 1;
                while ($v = each($ele_value)) {
                    if ($opt_count == $ele[$ele_id]) {
                        $other = checkOther($v['key'], $ele_id, $ele_caption);

                        if (false !== $other) {
                            $msg[$ele_id] .= $other;

                            $resp[$ele_caption][] = $other;
                        } else {
                            $msg[$ele_id] .= $myts->stripSlashesGPC($v['key']);

                            $resp[$ele_caption][] = $myts->stripSlashesGPC($v['key']);
                        }
                    }

                    $opt_count++;
                }
                break;
            case 'yn':
                $v = (2 == $ele[$ele_id]) ? _NO : _YES;
                $msg[$ele_id] .= $myts->stripSlashesGPC($v);
                $resp[$ele_caption][] = $myts->stripSlashesGPC($v);
                break;
            case 'checkbox':
                $opt_count = 1;
                $ch = [];
                while ($v = each($ele_value)) {
                    if (is_array($ele[$ele_id])) {
                        if (in_array($opt_count, $ele[$ele_id], true)) {
                            $other = checkOther($v['key'], $ele_id, $ele_caption);

                            if (false !== $other) {
                                $ch[] = $other;
                            } else {
                                $ch[] = $myts->stripSlashesGPC($v['key']);
                            }
                        }

                        $opt_count++;
                    } else {
                        if (!empty($ele[$ele_id])) {
                            $ch[] = $myts->stripSlashesGPC($v['key']);
                        }
                    }
                }
                $msg[$ele_id] .= !empty($ch) ? implode("\n", $ch) : '';
                $resp[$ele_caption] = $ch;
                break;
            case 'select':
                $opt_count = 1;
                $ch = [];
                if (is_array($ele[$ele_id])) {
                    while ($v = each($ele_value[2])) {
                        if (in_array($opt_count, $ele[$ele_id], true)) {
                            $ch[] = $myts->stripSlashesGPC($v['key']);
                        }

                        $opt_count++;
                    }
                } else {
                    while ($j = each($ele_value[2])) {
                        if ($opt_count == $ele[$ele_id]) {
                            $ch[] = $myts->stripSlashesGPC($j['key']);
                        }

                        $opt_count++;
                    }
                }
                $msg[$ele_id] .= !empty($ch) ? implode("\n", $ch) : '';
                $resp[$ele_caption] = $ch;
                break;
            default:
                break;
        }
    } elseif (1 == $ele_req) {
        $err[] = sprintf(_LAWSUIT_ERR_REQ, $ele_caption);
    }
}

if (is_dir(LAWSUIT_ROOT_PATH . 'language/' . $xoopsConfig['language'] . '/mail_template')) {
    $template_dir = LAWSUIT_ROOT_PATH . 'language/' . $xoopsConfig['language'] . '/mail_template';
} else {
    $template_dir = LAWSUIT_ROOT_PATH . 'language/english/mail_template';
}
$xoopsMailer = getMailer();
$xoopsMailer->setTemplateDir($template_dir);
$xoopsMailer->setTemplate('liaise.tpl');
$xoopsMailer->setSubject(sprintf(_LAWSUIT_MSG_SUBJECT, $myts->stripSlashesGPC($form->getVar('form_title'))));
if (in_array('user', $xoopsModuleConfig['moreinfo'], true)) {
    if (is_object($xoopsUser)) {
        $xoopsMailer->assign('UNAME', sprintf(_LAWSUIT_MSG_UNAME, $xoopsUser->getVar('uname')));

        $xoopsMailer->assign('ULINK', sprintf(_LAWSUIT_MSG_UINFO, XOOPS_URL . '/userinfo.php?uid=' . $xoopsUser->getVar('uid')));

        $resp['UNAME'] = sprintf(_LAWSUIT_MSG_UNAME, $xoopsUser->getVar('uname'));

        $resp['ULINK'] = sprintf(_LAWSUIT_MSG_UINFO, XOOPS_URL . '/userinfo.php?uid=' . $xoopsUser->getVar('uid'));
    } else {
        $xoopsMailer->assign('UNAME', sprintf(_LAWSUIT_MSG_UNAME, $xoopsConfig['anonymous']));

        $xoopsMailer->assign('ULINK', '');

        $resp['UNAME'] = sprintf(_LAWSUIT_MSG_UNAME, $xoopsConfig['anonymous']);

        $resp['ULINK'] = sprintf(_LAWSUIT_MSG_UINFO, XOOPS_URL . '/userinfo.php?uid=' . $xoopsUser->getVar('uid'));
    }
} else {
    $xoopsMailer->assign('UNAME', '');

    $xoopsMailer->assign('ULINK', '');

    $resp['UNAME'] = '';

    $resp['ULINK'] = '';
}
if (in_array('ip', $xoopsModuleConfig['moreinfo'], true)) {
    $proxy = $_SERVER['REMOTE_ADDR'];

    $ip = '';

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_PROXY_CONNECTION'])) {
        $ip = $_SERVER['HTTP_PROXY_CONNECTION'];
    } elseif (isset($_SERVER['HTTP_VIA'])) {
        $ip = $_SERVER['HTTP_VIA'];
    }

    $ip = empty($ip) ? $_SERVER['REMOTE_ADDR'] : $ip;

    if ($proxy != $ip) {
        $ip .= sprintf(_LAWSUIT_PROXY, $proxy);
    }

    $resp['IP'] = $ip;

    $xoopsMailer->assign('IP', sprintf(_LAWSUIT_MSG_IP, $ip));
} else {
    $xoopsMailer->assign('IP', '');

    $resp['IP'] = '';
}
if (in_array('agent', $xoopsModuleConfig['moreinfo'], true)) {
    $xoopsMailer->assign('AGENT', sprintf(_LAWSUIT_MSG_AGENT, $_SERVER['HTTP_USER_AGENT']));

    $resp['AGENT'] = sprintf(_LAWSUIT_MSG_AGENT, $_SERVER['HTTP_USER_AGENT']);
} else {
    $xoopsMailer->assign('AGENT', '');

    $resp['AGENT'] = '';
}
if (in_array('form', $xoopsModuleConfig['moreinfo'], true)) {
    $xoopsMailer->assign('FORMURL', sprintf(_LAWSUIT_MSG_FORMURL, LAWSUIT_URL . 'index.php?id=' . $page_id));

    $resp['FORMURL'] = sprintf(_LAWSUIT_MSG_FORMURL, LAWSUIT_URL . 'index.php?id=' . $page_id);
} else {
    $xoopsMailer->assign('FORMURL', '');

    $resp['FORMURL'] = '';
}

$group = $memberHandler->getGroup($form->getVar('form_send_to_group'));
if ('p' == $form->getVar('form_send_method') && is_object($xoopsUser) && false !== $group && '' != $group->getVar('groupid')) {
    $xoopsMailer->usePM();

    $xoopsMailer->setToGroups($group);
} else {
    $xoopsMailer->useMail();

    $xoopsMailer->setFromName($xoopsConfig['sitename']);

    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);

    $xoopsMailer->multimailer->addReplyTo($reply_mail, '"' . $reply_name . '"');

    $charset = !empty($xoopsModuleConfig['mail_charset']) ? $xoopsModuleConfig['mail_charset'] : _CHARSET;

    $xoopsMailer->charSet = $charset;

    if (false !== $group && '' != $group->getVar('groupid')) {
        $xoopsMailer->setToGroups($group);
    } else {
        $xoopsMailer->setToEmails($xoopsConfig['adminmail']);
    }
}

$uploaded = [];
if (count($attachments) > 0) {
    foreach ($attachments as $a) {
        if (false === $xoopsMailer->isMail || $a['saveto']) {
            $uploader[$a['id']]->prefix = $form->getVar('form_id') . '_';

            if (false === $uploader[$a['id']]->upload()) {
                $err[] = $uploader[$a['id']]->getErrors();
            } else {
                $saved = $uploader[$a['id']]->savedFileName;

                $uploaded[] = LAWSUIT_UPLOAD_PATH . $saved;

                $msg[$a['id']] .= sprintf(_LAWSUIT_UPLOADED_FILE, mb_strtolower(mb_substr($_SERVER['SERVER_PROTOCOL'], 0, mb_strpos($_SERVER['SERVER_PROTOCOL'], '/', 0))) . '://' . $_SERVER['HTTP_HOST'] . LAWSUIT_URL . 'admin/file.php?f=' . $saved);
            }
        } else {
            if (false === $xoopsMailer->multimailer->addAttachment($a['path'], $a['name'])) {
                $err[] = $xoopsMailer->multimailer->ErrorInfo;
            } else {
                $msg[$a['id']] .= sprintf(_LAWSUIT_ATTACHED_FILE, $_FILES['ele_' . $a['id']]['name']);
            }
        }
    }
}

$xoopsMailer->assign('MSG', implode("\n", $msg));

$response = $lawsuit_response_mgr->create();
$msg[] = "Submitters IP: $ip";
$response->setVar('cid', $page->getVar('cid'));
$response->setVar('pid', $page->getVar('pid'));
$response->setVar('form_id', $page->getVar('form_id'));
$response->setVar('response', $resp);
$lawsuit_response_mgr->insert($response);

if (count($err) < 1) {
    if (!$xoopsMailer->send(true)) {
        $err[] = $xoopsMailer->getErrors();
    }
}

if (count($err) > 0) {
    if (count($uploaded) > 0) {
        foreach ($uploaded as $u) {
            @unlink($u);
        }
    }

    $GLOBALS['xoopsOption']['template_main'] = 'lawsuit_error.html';

    require_once XOOPS_ROOT_PATH . '/header.php';

    $xoopsTpl->assign('error_heading', _LAWSUIT_ERR_HEADING);

    $xoopsTpl->assign('errors', $err);

    $xoopsTpl->assign('go_back', _BACK);

    $xoopsTpl->assign('lawsuit_url', LAWSUIT_URL . '/index.php?form_id=' . $form_id);

    $xoopsTpl->assign('xoops_pagetitle', _LAWSUIT_ERR_HEADING);

    require XOOPS_ROOT_PATH . '/footer.php';

    exit();
}

$whereto = $form->getVar('form_whereto');
$whereto = !empty($whereto) ? str_replace('{SITE_URL}', XOOPS_URL, $whereto) : XOOPS_URL . '/index.php';
redirect_header($whereto, 0, _LAWSUIT_MSG_SENT);

function checkOther($key, $id, $caption)
{
    global $err, $myts;

    if (!preg_match('/\{OTHER\|+[0-9]+\}/', $key)) {
        return false;
    }

    if (!empty($_POST['other']['ele_' . $id])) {
        return _LAWSUIT_OPT_OTHER . $myts->stripSlashesGPC($_POST['other']['ele_' . $id]);
    }

    $err[] = sprintf(_LAWSUIT_ERR_REQ, $caption);

    return false;
}
