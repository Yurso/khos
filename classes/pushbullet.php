<?php
Class Pushbullet {    

    static public function sendPush($title, $body) {

		$link='https://api.pushbullet.com/v2/pushes';

		$request = array(
			'body' => $body,
			'title' => $title,
			'type' => 'note'
		);

		return self::sendRequest($link, $request);

    }

    public static function CheckCurlResponse($code) {

		$code=(int)$code;
		$errors=array(
			301=>'Moved permanently',
			400=>'Bad request',
			401=>'Unauthorized',
			403=>'Forbidden',
			404=>'Not found',
			500=>'Internal server error',
			502=>'Bad gateway',
			503=>'Service unavailable'
			);
		#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		if($code!=200 && $code!=204) {
			return false;
		}

		return true;

	}

	public static function sendRequest($link, $request) {

		$token = Params::getParamValue('pushbullet_token');		

		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'CRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($request));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json', 'Access-Token: '.$token));
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		 
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

		curl_close($curl);
		
		if (!self::CheckCurlResponse($code)) {
			return array();
		}

		$response = json_decode($out, true);
		
		return $response;

	}

}