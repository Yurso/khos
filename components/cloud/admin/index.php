<?php 

Class CloudIndexController extends ControllerBase {

      private $token = '67d0d7b4073432566343194f1cfa92afe7ae0f9e74067e5f68ee9c7a598ad783';

      public function index() {   

            $tmpl = new template;
            
            //$tmpl->setVar('summary', $summary);
            
            $tmpl->setTitle('Cloud VPN server');

            $tmpl->addScript('/public/js/cloud/index.js');
            $tmpl->addStyle('/public/css/cloud/index.css');
            
            $tmpl->display('index');

      }

      public function get_scalets_list() {

            require_once 'classes/unirest-php-master/src/Unirest.php';            
            require_once 'classes/vscale.class.php';

            $vscale = new Vscale($this->token);

            $scalets = $vscale->getScalets();

            echo json_encode($scalets, JSON_UNESCAPED_UNICODE);

      }

      public function get_scalet_info() {

            $result = array();

            if (isset($_POST['ctid']) && !empty($_POST['ctid'])) {

                  $ctid = intval($_POST['ctid']);

                  require_once 'classes/unirest-php-master/src/Unirest.php';            
                  require_once 'classes/vscale.class.php';

                  $vscale = new Vscale($this->token);

                  $result = $vscale->getScaletInfo($ctid);

            }

            echo json_encode($result, JSON_UNESCAPED_UNICODE);

      }

      public function create_vpn_scalet() {

            require_once 'classes/unirest-php-master/src/Unirest.php';            
            require_once 'classes/vscale.class.php';

            $vscale = new Vscale($this->token);

            $result = $vscale->createScalet('ubuntu_16.04_64_001_docker', 'small', 'vpnsrv', 'zzOP7moBqT', 'msk0');

            echo json_encode($result, JSON_UNESCAPED_UNICODE);


      }

      public function delete_scalet() {

            require_once 'classes/unirest-php-master/src/Unirest.php';            
            require_once 'classes/vscale.class.php';

            $result = array();

            $vscale = new Vscale($this->token);

            if (isset($_POST['ctid']) && !empty($_POST['ctid'])) {

                  $result = $vscale->deleteScalet(intval($_POST['ctid']));

            }

            echo json_encode($result, JSON_UNESCAPED_UNICODE);

      }

      public function setup_vpn_on_scalet() {

            // $server_ip = '82.148.31.168';

            // $connection = ssh2_connect($server_ip, 22);
            // ssh2_auth_password($connection, 'root', 'zzOP7moBqT');

            // $stream = ssh2_exec($connection, 'docker run -d --name ikev2-vpn-server --privileged -p 500:500/udp -p 4500:4500/udp gaomd/ikev2-vpn-server:0.3.0');
            // //$stream = ssh2_exec($connection, 'docker run -i -t --rm --volumes-from ikev2-vpn-server -e "HOST='.$server_ip.'" gaomd/ikev2-vpn-server:0.3.0 generate-mobileconfig');

            // $stderr_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

            // print_r($stderr_stream);

      }

}