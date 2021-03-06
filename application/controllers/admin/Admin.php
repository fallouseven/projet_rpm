<?php
require_once (WEBAPPROOT.'models/ProjetDao.php');
require_once (WEBAPPROOT.'models/UserDao.php');
require_once (WEBAPPROOT.'models/PourquoiDao.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Admin extends Admin_Controller{
    
    function __construct() {
          parent::__construct();
          //$this->section = "";
    }
    
    function index() {
        $this->render('index');
    }
    
    function projets($request = null,$params=null) {
        if($request === null){
            if(isset($_POST['check'])){
                $deleteProjects = $_POST['check'];
                
                foreach($deleteProjects as $value){
                    $projetDao = new ProjetDao(new Projet());
                    $list = $projetDao->read($value);
                    $list->setStatut(-1);
                    $projetDao = new ProjetDao($list);
                    $projetDao->update($value);
                }
            }
            $this->set($this->getListProjets());
            $this->render('projets/projets');
        }else if($request === 'creer_projet'){
            $erreur_array = array('name'=>-1,'description'=>-1,'contenu'=>-1,'from'=>-1,'to'=>-1);
            $erreur = false;
            //POST
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $array_projet  = $this->postProject($erreur_array,$erreur);
                if(!$erreur ){
                    $projetDao = new ProjetDao(new Projet($array_projet));
                    $projetDao->create();
                    
                    $this->redirect('admin/projets');
                }else{
                    $this->set(array('erreur_array'=>$erreur_array,'erreur'=>$erreur));
                    $this->render('projets/creer_projet');
                }
            }else{
           
                $this->set(array('erreur_array'=>$erreur_array,'erreur'=>$erreur));
                $this->render('projets/creer_projet');
            }
        }else if($request  === 'editer_projet'){
             $erreur_array = array('name'=>-1,'description'=>-1,'contenu'=>-1,'from'=>-1,'to'=>-1);
            $erreur = false;
            //POST
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $array_projet  = $this->postProject($erreur_array,$erreur);
                if(!$erreur ){
                    $projetDao = new ProjetDao(new Projet($array_projet));
                    $projetDao->update($params);
                    $this->redirect('admin/projets');
                }else{
                    $this->set(array('erreur_array'=>$erreur_array,'erreur'=>$erreur));
                    $this->render('projets/editer_projet');
                }
            }else{
                 $projetDao = new ProjetDao(new Projet());
                 $list = $projetDao->read($params);
                 $this->set(array("projet" => $list));
                 $this->render('projets/editer_projet');
            }
        }else if($request  === 'supprimer_projet'){
             //POST
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $deleteProjects = $_POST['check'];
                $projetDao = new ProjetDao(new Projet());
                $list = $projetDao->read($deleteProjects );
                $list->setStatut(-1);
                $projetDao = new ProjetDao($list);
                $projetDao->update($deleteProjects);
                $this->redirect('admin/projets');
            }
             $projetDao = new ProjetDao(new Projet());
             $list = $projetDao->read($params);
             $this->set(array("projet" => $list));
             $this->render('projets/supprimer_projet');
            
        }else if($request  === 'afficher_projet'){
            
             $projetDao = new ProjetDao(new Projet());
             $list = $projetDao->read($params);
             $this->set(array("projet" => $list));
             $this->render('projets/afficher_projet');
            
        }else{
            echo 'erreur 404 doit etre lancé';
        }
    }
    
    function membres($request = null){
        if($request== null){
            $this->set($this->getListMembres());
            $this->render('membres/liste_membres');
        }else{
            if($request == 'add_membre'){
                $this->render('membres/add_membre');
            }
            else{
                echo 'erreur 404 doit etre lancé';
            }
        }
    }
    function pourquoi($request = null){
        if($request== null){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                 $erreur = false;
                if($_POST['contenu'] == ''){
                    $erreur_array['contenu'] = 1;
                    $erreur = true;
                }else{
                    $contenu = $_POST['contenu'];
                     $erreur_array['contenu'] = 0;
                }
                if(!$erreur ){
                    $array_projet = array('titre' => "Pourquoi devenir membre",
                                        'description' => "Devenir membre est devenir mouride",
                                        'contenu' =>"$contenu");
                    $pourquoiDao = new PourquoiDao(new Pourquoi($array_projet));
                    $pourquoiDao->create();
                    $file = WEBROOT.'application/views/pourquoi/contenu_pourquoi.tpl';
                    // Open the file to get existing content
                    //$current = file_get_contents($file);
                    // Append a new person to the file
                    $current = $contenu;
                    // Write the contents back to the file
                    file_put_contents($file, $current);
                    //$this->redirect('admin');
                }
            }
            //$this->set($this->getListMembres());
            $pourquoiDao = new PourquoiDao(new Pourquoi());
            $list = $pourquoiDao->getAllData();
            $this->set(array("pourquoi" => $list));
            $this->render('pourquoi/pourquoi');
        }
    }
    function getListProjets(){
       $projetDao = new ProjetDao(new Projet());
       $list = $projetDao->getAllData();
       return array("projets" => $list);
    }
    function getListMembres(){
        $userDao = new UserDao(new User());
        $list = $userDao->getAllData();
       return array("membres" => $list);
    }
    function uploadImage($name,$dir){
      //header('Content-Type: text/plain; charset=utf-8');
      $errors = array();
      $err = false;
      $file_name = $name['name'];
      $file_size = $name['size'];
      $file_tmp = $name['tmp_name'];
      $file_type = $name['type'];
      $file_ext=strtolower(end(explode('.',$name['name'])));
      
      $expensions= array("jpeg","jpg","png");
      
      if(in_array($file_ext,$expensions)=== false){
         $errors[]="extension not allowed, please choose a JPEG or PNG file.";
      }
      
      if($file_size > 2097152){
         $errors[]='File size must be excately 2 MB';
      }
      
      if(empty($errors)==true){
         move_uploaded_file($file_tmp,$dir.$file_name);
         echo "Success";
      }else{
          $err = true;
      }
      return $err;
    }
    function postProject(&$erreur_array,&$erreur){
        //POST
        if($_POST['name'] == ''){
            $erreur_array['name'] = 1;
            $erreur = true;
        }else{
            $name = trim($_POST['name']);
            $name = $this->resizeString($name,100);
            $erreur_array['name'] = 0;
        }
        if($_POST['description'] == ''){
            $erreur_array['description'] = 1;
            $erreur = true;
        }else{
            $description = $_POST['description'];
            $description = $this->resizeString($description,100);
             $erreur_array['description'] = 0;
        }
        if($_POST['contenu'] == ''){
            $erreur_array['contenu'] = 1;
            $erreur = true;
        }else{
            $contenu = $_POST['contenu'];
             $erreur_array['contenu'] = 0;
        }
        if($_POST['from'] == ''){
            $erreur_array['from'] = 1;
            $erreur = true;
        }else{
            $from = trim($_POST['from']);
            $erreur_array['from'] = 0;
            $time = strtotime($from);
            $dtFrom = date('Y-m-d',$time);
        }
        if($_POST['to'] == ''){
            $erreur_array['to'] = 1;
            $erreur = true;
        }else{
            $to = trim($_POST['to']);
            $erreur_array['to'] = 0;
            $time = strtotime($to);
            $dtTo = date('Y-m-d',$time);
        }
        if(isset($_FILES['imgp'])){
            $dir = WEBROOT."ressources/images/projets/";
            $url =$_FILES['imgp']['name'];
            $name_image = $_FILES['imgp']['name'];
            if(isset($_POST['nameimg'])){
                $name_image = trim($_POST['nameimg']);
                $name_image = $this->resizeString($name_image,100);
            }
            $erreur = $this->uploadImage($_FILES['imgp'],$dir);
          
        }else{
            $url = isset($_POST['url'])?$_POST['url']:"";
            $name_image = trim($_POST['nameimg']);
             $name_image = $this->resizeString($name_image,100);
        }
        
        $statut = $_POST['state'];
        return $array_projet = array(
                                        'titre' => "$name",
                                        'description' => "$description",
                                        'contenu' =>"$contenu",
                                        'url' => "$url",
                                        'date_creation' => $dtFrom,
                                        'date_fin' => $dtTo,
                                        'image' => "$name_image",
                                        'statut' => "$statut");
    }
    function resizeString($chaine,$size){
        if(count_chars($chaine) > $size){
            return substr($chaine,0,$size-1);
        }else{
            return $chaine;
        }
    }
}