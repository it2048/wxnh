<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of indexController
 *
 * @author xfl
 */
class indexController extends Controller {
    public function __construct() {
        parent::__construct();
    }
    public function indexAction(){
        $this->showTemplate('index/index'); 
    }
    public function recommendAction(){
        $this->showTemplate('index/recommend'); 
    }
    public function sendAction(){
        $this->showTemplate('index/send'); 
    }
    
}
