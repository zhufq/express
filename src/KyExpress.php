<?php


namespace Zfq\Express;


use GuzzleHttp\Client;

class KyExpress {

	public function commonRequest( $ky_config, $body, $method, $token ) {
		//获取token
//		$token  =$this->getToken($ky_config['app_key'], $ky_config['app_secret'], $ky_config['do_main_pro'] );
//		$time   = getUnixTimestamp();//获取时间戳
		$appkey = $ky_config['app_key'];//app_key

		list( $s1, $s2 ) = explode( ' ', microtime() );
		$time = sprintf( '%.0f', ( floatval( $s1 ) + floatval( $s2 ) ) * 1000 );

		$sign = getSign( $time, $ky_config['app_secret'], $body );

		$headers = [
			'Content-Type' => 'application/json',
			'token'        => $token,
			'sign'         => $sign,
			'appkey'       => $appkey,
			'method'       => $method,
			'timestamp'    => $time,
		];

		$client = new  Client(['timeout'=>5]);
		$url    = "https://open.ky-express.com/router/rest";


		try {
			$response = $client->post( $url, [
				'headers' => $headers,
				'json'    => $body,
			] );
			$res      = $response->getBody()->getContents();
			$response = json_decode( $res, true );
			if ( $response ) {
				return false;
			}

			return $response;
		} catch ( \Exception $e ) {
			return false;
		}
	}
}