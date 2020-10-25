<?php

// $Id: forms.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $

if (!defined('LAWSUIT_ROOT_PATH')) {
    exit();
}

class LawsuitPages extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();

        $this->initVar('pid', XOBJ_DTYPE_INT);

        $this->initVar('cid', XOBJ_DTYPE_INT);

        $this->initVar('form_id', XOBJ_DTYPE_INT);

        $this->initVar('default', XOBJ_DTYPE_INT);

        $this->initVar('weight', XOBJ_DTYPE_INT);

        $this->initVar('html', XOBJ_DTYPE_OTHER);

        $this->initVar('title', XOBJ_DTYPE_TXTBOX, false, false, 128);

        $this->initVar('description', XOBJ_DTYPE_TXTBOX, false, false, 255);
    }
}

class LawsuitPagesHandler extends XoopsObjectHandler
{
    public $db;

    public $db_table;

    public $perm_name = 'lawsuit_pages_access';

    public $obj_class = 'LawsuitPages';

    public function __construct($db)
    {
        $this->db = $db;

        $this->db_table = $this->db->prefix('lawsuit_pages');

        $this->permHandler = xoops_getHandler('groupperm');
    }

    public function &getInstance($db)
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self($db);
        }

        return $instance;
    }

    public function &create()
    {
        return new $this->obj_class();
    }

    public function get($id, $fields = '*')
    {
        $id = (int)$id;

        if ($id > 0) {
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE pid=' . $id;

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $page = new $this->obj_class();

                $page->assignVars($this->db->fetchArray($result));

                return $page;
            }

            return false;
        }

        return false;
    }

    public function insert(XoopsObject $page, $force = false)
    {
        if (mb_strtolower(get_class($page)) != mb_strtolower($this->obj_class)) {
            return false;
        }

        if (!$page->isDirty()) {
            return true;
        }

        if (!$page->cleanVars()) {
            return false;
        }

        foreach ($page->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($page->isNew() || empty($pid)) {
            $pid = $this->db->genId($this->db_table . '_pid_seq');

            $sql = sprintf(
                'INSERT INTO %s (
				pid, cid, form_id, default, html, title, description, weight
				) VALUES (
				%u, %u, %u, %u, %s, %s, %s, %u
				)',
                $this->db_table,
                $pid,
                $cid,
                $form_id,
                $default,
                $this->db->quoteString($html),
                $this->db->quoteString($title),
                $this->db->quoteString($description),
                $weight
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
				html = %s,
				title = %s,
				description = %s,
				weight = %u,
				cid = %u,
				form_id = %u,
				default = %u
				WHERE pid = %u',
                $this->db_table,
                $this->db->quoteString($html),
                $this->db->quoteString($title),
                $this->db->quoteString($description),
                $weight,
                $cid,
                $form_id,
                $default,
                $pid
            );
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            $page->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);

            return false;
        }

        if (empty($pid)) {
            $pid = $this->db->getInsertId();
        }

        $sql = sprintf('UPDATE %s SET default = 0 WHERE cid = %u AND pid != %u', $this->db_table, $cid, $pid);

        if (false !== $force) {
            $resultb = $this->db->queryF($sql);
        } else {
            $resultb = $this->db->query($sql);
        }

        $page->assignVar('pid', $pid);

        return $pid;
    }

    public function delete(XoopsObject $page, $force = false)
    {
        if (mb_strtolower(get_class($page)) != mb_strtolower($this->obj_class)) {
            return false;
        }

        $sql = 'DELETE FROM ' . $this->db_table . ' WHERE pid=' . $page->getVar('pid') . '';

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        return true;
    }

    public function getObjects($criteria = null, $fields = '*', $id_as_key = false)
    {
        $ret = [];

        $limit = $start = 0;

        $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table;

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();

            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }

            $limit = $criteria->getLimit();

            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);

        if (!$result) {
            return false;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $pages = new $this->obj_class();

            $pages->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$pages;
            } else {
                $ret[$myrow['pid']] = &$pages;
            }

            unset($pages);
        }

        return count($ret) > 0 ? $ret : false;
    }

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db_table;

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db_table;

        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    public function deletePagesPermissions($pid)
    {
        global $xoopsModule;

        $criteria = new CriteriaCompo();

        $criteria->add(new Criteria('gperm_itemid', $pid));

        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));

        $criteria->add(new Criteria('gperm_name', $this->perm_name));

        if ($old_perms = &$this->permHandler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->permHandler->delete($p);
            }
        }

        return true;
    }

    public function insertPagesPermissions($pid, $group_ids)
    {
        global $xoopsModule;

        foreach ($group_ids as $id) {
            $perm = $this->permHandler->create();

            $perm->setVar('gperm_name', $this->perm_name);

            $perm->setVar('gperm_itemid', $pid);

            $perm->setVar('gperm_groupid', $id);

            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));

            $this->permHandler->insert($perm);
        }

        return true;
    }

    public function getPermittedPages()
    {
        global $xoopsUser, $xoopsModule;

        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;

        $criteria = new CriteriaCompo();

        $criteria->add(new Criteria('weight', 1, '>='), 'OR');

        $criteria->setSort('weight');

        $criteria->setOrder('ASC');

        if ($pages = $this->getObjects($criteria)) {
            $ret = [];

            foreach ($pages as $f) {
                if (false !== $this->permHandler->checkRight($this->perm_name, $f->getVar('pid'), $groups, $xoopsModule->getVar('mid'))) {
                    $ret[] = $f;

                    unset($f);
                }
            }

            return $ret;
        }

        return false;
    }

    public function getSinglePagePermission($pid)
    {
        global $xoopsUser, $xoopsModule;

        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;

        if (false !== $this->permHandler->checkRight($this->perm_name, $pid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }

        return false;
    }
}
