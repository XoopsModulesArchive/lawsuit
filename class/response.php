<?php

// $Id: forms.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $

if (!defined('LAWSUIT_ROOT_PATH')) {
    exit();
}

class LawsuitResponse extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();

        $this->initVar('rid', XOBJ_DTYPE_INT);

        $this->initVar('cid', XOBJ_DTYPE_INT);

        $this->initVar('pid', XOBJ_DTYPE_INT);

        $this->initVar('form_id', XOBJ_DTYPE_INT);

        $this->initVar('fingerprint', XOBJ_DTYPE_TXTBOX, false, false, 32);

        $this->initVar('response', XOBJ_DTYPE_ARRAY);

        $this->initVar('time_response', XOBJ_DTYPE_INT, false, false);
    }
}

class LawsuitResponseHandler extends XoopsObjectHandler
{
    public $db;

    public $db_table;

    public $perm_name = 'lawsuit_response_access';

    public $obj_class = 'LawsuitResponse';

    public function __construct($db)
    {
        $this->db = $db;

        $this->db_table = $this->db->prefix('lawsuit_response');

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
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE rid=' . $id;

            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);

            if (1 == $numrows) {
                $response = new $this->obj_class();

                $response->assignVars($this->db->fetchArray($result));

                return $response;
            }

            return false;
        }

        return false;
    }

    public function insert(XoopsObject $respons, $force = false)
    {
        if (mb_strtolower(get_class($respons)) != mb_strtolower($this->obj_class)) {
            return false;
        }

        if (!$respons->isDirty()) {
            return true;
        }

        if (!$respons->cleanVars()) {
            return false;
        }

        foreach ($respons->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if (empty($rid)) {
            $rid = $this->db->genId($this->db_table . '_rid_seq');

            $sql = sprintf(
                'INSERT INTO %s (
				rid, cid, pid, form_id, fingerprint, response, time_response
				) VALUES (
				%u, %u, %u, %u, %s, %s, %u
				)',
                $this->db_table,
                $rid,
                $cid,
                $pid,
                $form_id,
                $this->db->quoteString(md5(md5($response) . md5(time()))),
                $this->db->quoteString($response),
                time()
            );
        } else {
            $respons->setErrors('Could not edit store data in the database.<br> this is a locked record');

            return false;
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            $response->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);

            return false;
        }

        if (empty($rid)) {
            $rid = $this->db->getInsertId();
        }

        $respons->setVar('rid', $rid);

        return $rid;
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
            $responses = new $this->obj_class();

            $responses->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = &$responses;
            } else {
                $ret[$myrow['rid']] = &$responses;
            }

            unset($responses);
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

    public function deleteResponsePermissions($rid)
    {
        global $xoopsModule;

        $criteria = new CriteriaCompo();

        $criteria->add(new Criteria('gperm_itemid', $rid));

        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));

        $criteria->add(new Criteria('gperm_name', $this->perm_name));

        if ($old_perms = &$this->permHandler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->permHandler->delete($p);
            }
        }

        return true;
    }

    public function insertResponsePermissions($rid, $group_ids)
    {
        global $xoopsModule;

        foreach ($group_ids as $id) {
            $perm = $this->permHandler->create();

            $perm->setVar('gperm_name', $this->perm_name);

            $perm->setVar('gperm_itemid', $rid);

            $perm->setVar('gperm_groupid', $id);

            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));

            $this->permHandler->insert($perm);
        }

        return true;
    }

    public function getPermittedResponse()
    {
        global $xoopsUser, $xoopsModule;

        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;

        if ($responses = $this->getObjects($criteria)) {
            $ret = [];

            foreach ($responses as $f) {
                if (false !== $this->permHandler->checkRight($this->perm_name, $f->getVar('rid'), $groups, $xoopsModule->getVar('mid'))) {
                    $ret[] = $f;

                    unset($f);
                }
            }

            return $ret;
        }

        return false;
    }

    public function getSingleResponsePermission($rid)
    {
        global $xoopsUser, $xoopsModule;

        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;

        if (false !== $this->permHandler->checkRight($this->perm_name, $rid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }

        return false;
    }
}
