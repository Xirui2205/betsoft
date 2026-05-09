<?php
class Models_DbTable_Branch extends Zend_Db_Table_Abstract {

    protected $_name = 'branch';

    public function getBranch($id = NULL) {
        if (isset($id)) {
          $id = (int)$id;
          $res = $this->fetchRow('id = ' . $id);
        } else {
          $res = $this->fetchAll();
        }

        if (!$res) {
            throw new Exception("Count not find row $id");
        }
        return $row->toArray();
    }

    public function addBranch() {

    }

    public function updateBranch() {

    }

    public function deleteBranch($id) {

    }
}
