<?php

// $Id: index.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $

require __DIR__ . '/header.php';

$myts = MyTextSanitizer::getInstance();
if (empty($_POST['submit'])) {
    $page_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $category_id = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;

    if (!empty($category_id)) {
        $criteria = new Criteria('cid', $category_id);

        $pages = &$lawsuit_pages_mgr->getObjects($criteria);

        $GLOBALS['xoopsOption']['template_main'] = 'lawsuit_index.html';

        require_once XOOPS_ROOT_PATH . '/header.php';

        if (count($pages) > 0) {
            foreach ($pages as $page) {
                $xoopsTpl->append(
                    'lawsuits',
                    [
                        'title' => $page->getVar('title'),
'desc' => $page->getVar('description'),
'id' => $page->getVar('pid'),
                    ]
                );
            }

            $xoopsTpl->assign('lawsuit_intro', $myts->displayTarea($xoopsModuleConfig['intro']));
        }
    } elseif (empty($page_id)) {
        $pages = $lawsuit_pages_mgr->getPermittedPages();

        if (false !== $pages && 1 === count($pages)) {
            $page = $pages[0];

            require __DIR__ . '/include/form_render.php';
        } else {
            $GLOBALS['xoopsOption']['template_main'] = 'lawsuit_index.html';

            require_once XOOPS_ROOT_PATH . '/header.php';

            if (count($pages) > 0) {
                foreach ($pages as $page) {
                    if ($page->getVar('default')) {
                        $xoopsTpl->append(
                            'lawsuits',
                            [
                                'title' => $page->getVar('title'),
'desc' => $page->getVar('description'),
'id' => $page->getVar('pid'),
                            ]
                        );
                    }
                }

                $xoopsTpl->assign('lawsuit_intro', $myts->displayTarea($xoopsModuleConfig['intro']));
            }
        }
    } else {
        if (!$page = $lawsuit_pages_mgr->get($page_id)) {
            header('Location: ' . LAWSUIT_URL);

            exit();
        }

        if (false !== $lawsuit_pages_mgr->getSinglePagePermission($page_id)) {
            require __DIR__ . '/include/form_render.php';
        } else {
            header('Location: ' . LAWSUIT_URL);

            exit();
        }
    }

    require XOOPS_ROOT_PATH . '/footer.php';
} else {
    $form_id = isset($_POST['form_id']) ? (int)$_POST['form_id'] : 0;

    $page_id = isset($_POST['page_id']) ? (int)$_POST['page_id'] : 0;

    if (empty($form_id) || !$form = $lawsuit_form_mgr->get($form_id) || false === $lawsuit_form_mgr->getSingleFormPermission($form_id)) {
        header('Location: ' . LAWSUIT_URL);

        exit();
    }

    if (empty($page_id) || !$page = $lawsuit_pages_mgr->get($page_id) || false === $lawsuit_pages_mgr->getSinglePagePermission($page_id)) {
        header('Location: ' . LAWSUIT_URL);

        exit();
    }

    require __DIR__ . '/include/form_execute.php';
}
