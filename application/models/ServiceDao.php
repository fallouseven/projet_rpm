<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ServiceDao
 *
 * @author Moussa Thimbo
 */
require_once WEBROOT.'core/DAO.php';
require_once (WEBAPPROOT.'bean/Service.php');
class ServiceDao extends DAO {

    public function __construct(\Service $service = null) {
        parent::__construct($service?$service->getVars():array());
        $this->pk = 'id';
        $this->table = 'service';
    }

    public function test() {
        var_dump($this->variables);
    }

    public function getDataById() {
        return $this->read();
    }
    public function getAllData() {
        return $this->selectAll($this->table);
    }
}
