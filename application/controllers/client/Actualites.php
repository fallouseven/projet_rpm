<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Actualites
 *
 * @author Moussa
 */
class Actualites extends Client_Controller{
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->render('actualites');
    }
}
