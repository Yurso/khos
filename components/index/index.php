<?php 

Class IndexIndexController extends ControllerBase {

    public function index() {  

        $conf = Registry::get('config');

       	Main::Redirect($conf->fpdirection);

       	// $tmpl = new Template;
       	// $tmpl->display('index');

    }

}