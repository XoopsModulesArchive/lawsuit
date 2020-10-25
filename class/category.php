<?php

// $Id: forms.php,v 1.05 2009/06/24 23:45:00 wishcraft Exp $

if (!defined('LAWSUIT_ROOT_PATH')) {
    exit();
}

class LawsuitCategory extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();

        $this->initVar('cid', XOBJ_DTYPE_INT);

        $this->initVar('title', XOBJ_DTYPE_TXTBOX, false, true, 255);

        $this->initVar('domain', XOBJ_DTYPE_TXTBOX, false, true, 255);

        $this->initVar('domains', XOBJ_DTYPE_ARRAY, false, true);
    }
}

class LawsuitCategoryHandler extends XoopsObjectHandler
{
    public $db;

    public $db_table;

    public $perm_name = 'lawsuit_category_access';

    public $obj_class = 'LawsuitCategory';

    public function __construct($db)
    {
        $this->db = $db;

        $this->db_table = $this->db->prefix('lawsuit_category');

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
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE cid=' . $id;

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $category = new $this->obj_class();

                $category->assignVars($this->db->fetchArray($result));

                return $category;
            }

            return false;
        }

        return false;
    }

    public function insert(XoopsObject $category, $force = false)
    {
        if (mb_strtolower(get_class($category)) != mb_strtolower($this->obj_class)) {
            return false;
        }

        if (!$category->isDirty()) {
            return true;
        }

        if (!$category->cleanVars()) {
            return false;
        }

        foreach ($category->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($category->isNew() || empty($cid)) {
            $cid = $this->db->genId($this->db_table . '_cid_seq');

            $sql = sprintf(
                'INSERT INTO %s (
				cid, title, domain, domains
				) VALUES (
				%u, %s, %s, %s
				)',
                $this->db_table,
                $cid,
                $this->db->quoteString($title),
                $this->db->quoteString($domain),
                $this->db->quoteString($domains)
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
				title = %s,
				domain = %s,
				domains = %s
				WHERE cid = %u',
                $this->db_table,
                $this->db->quoteString($title),
                $this->db->quoteString($domain),
                $this->db->quoteString($domains),
                $cid
            );
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            $category->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);

            return false;
        }

        if (empty($cid)) {
            $cid = $this->db->getInsertId();
        }

        $category->assignVar('cid', $cid);

        return $cid;
    }

    public function delete(XoopsObject $category, $force = false)
    {
        if (mb_strtolower(get_class($category)) != mb_strtolower($this->obj_class)) {
            return false;
        }

        $sql = 'DELETE FROM ' . $this->db_table . ' WHERE cid=' . $category->getVar('cid') . '';

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
            $categorys = new $this->obj_class();

            $categorys->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$categorys;
            } else {
                $ret[$myrow['cid']] = &$categorys;
            }

            unset($categorys);
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

    public function deleteCategoryPermissions($cid)
    {
        global $xoopsModule;

        $criteria = new CriteriaCompo();

        $criteria->add(new Criteria('gperm_itemid', $cid));

        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));

        $criteria->add(new Criteria('gperm_name', $this->perm_name));

        if ($old_perms = &$this->permHandler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->permHandler->delete($p);
            }
        }

        return true;
    }

    public function insertCategoryPermissions($cid, $group_ids)
    {
        global $xoopsModule;

        foreach ($group_ids as $id) {
            $perm = $this->permHandler->create();

            $perm->setVar('gperm_name', $this->perm_name);

            $perm->setVar('gperm_itemid', $cid);

            $perm->setVar('gperm_groupid', $id);

            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));

            $this->permHandler->insert($perm);
        }

        return true;
    }

    public function getPermittedCategory()
    {
        global $xoopsUser, $xoopsModule;

        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;

        if ($categorys = $this->getObjects(null)) {
            $ret = [];

            foreach ($categorys as $f) {
                if (false !== $this->permHandler->checkRight($this->perm_name, $f->getVar('cid'), $groups, $xoopsModule->getVar('mid'))) {
                    $ret[] = $f;

                    unset($f);
                }
            }

            return $ret;
        }

        return false;
    }

    public function getSingleCategoryPermission($cid)
    {
        global $xoopsUser, $xoopsModule;

        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;

        if (false !== $this->permHandler->checkRight($this->perm_name, $cid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }

        return false;
    }
}
