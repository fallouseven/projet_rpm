<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Moussa
 */
interface CrudInterface {

    public function create();

    public function read();
    
    public function update();

    public function delete();
}
