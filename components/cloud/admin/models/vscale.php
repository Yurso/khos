<?php
Class CloudVscaleModel Extends ModelBase {

	public $url = 'https://api.vscale.io/v1/';
	public $token = '';

	public function sendRequest($method, $type = 'GET', $body = '') {

		//print_r($body);
		print_r(json_encode($body));

		$ch = curl_init();

        $headers = array();
        $headers[] = "X-Token: " . $this->token;
        $headers[] = "Content-Type: application/json;charset=UTF-8";
        
        curl_setopt($ch, CURLOPT_URL, $this->url . $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($type == 'POST') {
        	curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($body));
        }

        $res = curl_exec($ch);
        $rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        
        curl_close($ch);

        if ($rescode != 200) {            
              return false;
        }

        if (empty($res)) {
              return false;
        }

        return json_decode($res, 1);   

	}

	public function getAccountDetails() {
		return $this->sendRequest('account');
	}

	public function getScaletsList() {
		return $this->sendRequest('scalets');
	}

	public function createServer($make_from = 'ubuntu_18.04_64_001_master', $rplan = 'small', $do_start = true, $name = 'newsrv', $keys = array(), $password = '', $location = 'msk0') {

		return $this->sendRequest('scalets', 'POST', array(
			'make_from' => $make_from, 
			'rplan' => $rplan, 
			'do_start' => $do_start, 
			'name' => $name, 
			'keys' => $keys, 
			'password' => $password,
			'location' => $location	
		));

	}
	
}