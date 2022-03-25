<?php

namespace Zfq\Express;

use GuzzleHttp\Client;


class DbExpress {

	/**
	 * @param $url
	 * @param $config
	 * @param $params
	 *
	 * @return false|mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function commonMethods($url,$config,$params){

		list( $t1, $t2 ) = explode( ' ', microtime() );
		$timestamp = (float) sprintf( '%.0f', ( floatval( $t1 ) + floatval( $t2 ) ) * 1000 );

		$appkey = $config['appkey'];
		$custom_code = $config['company_code'];
		$params_info = json_encode( $params );
		$digest = base64_encode( md5( $params_info . $appkey . $timestamp ) );

		$data = [
			'companyCode' => $custom_code,
			'params'      => $params_info,
			'digest'      => $digest,
			'timestamp'   => $timestamp,
		];

		try {
			//发送请求
			$client   = new Client( [ 'time_out' => 5 ] );
			$response = $client->request( 'POST', $url, [
				'form_params' => $data,
			] );

			$body = $response->getBody()->getContents();
			$res  = json_decode( $body, true );

			if(!$res){
				return false;
			}

			return $res;

		}catch (\Exception $e){
			return false;
		}
	}
}