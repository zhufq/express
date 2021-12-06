<?php

namespace Zfq\Express;

use GuzzleHttp\Client;

class JdExpress {

	/**
	 * 京东快递签名算法
	 *
	 * @param $params
	 * @param $app_secret
	 *
	 * @return string
	 */
	public function sign( $params, $app_secret ) {
		$result = '';
		if ( count( $params ) > 0 && strlen( $app_secret ) > 0 ) {
			ksort( $params );
			$string = '';
			foreach ( $params as $key => $val ) {
				$string .= $key . $val;
			}
			$string = $app_secret . $string . $app_secret;
			$md5    = md5( $string );
			$result = strtoupper( $md5 );
		}

		return $result;
	}

	/**
	 * 京东接口  通用方法
	 *
	 * @param $config
	 * @param $methods
	 * @param $business
	 *
	 * @return false|mixed
	 */
	public function  jd_common_request($config, $methods, $business){
		//时间
		$date = date( "Y-m-d H:i:s", time() );
		//需要签名的参数
		$needQianMing = [
			'access_token' => $config['access_token'],
			'app_key'      => $config['app_key'],
			"method"       => $methods,
			'timestamp'    => $date,
			'sign_method'  => "md5",
			'v'            => "2.0",
		];

		//业务参数
		$needQianMing['360buy_param_json'] = json_encode( $business );

		//秘钥
		$app_secret = $config['app_secret'];

		//得出签名
		$needQianMing['sign'] = $this->sign( $needQianMing, $app_secret );

		$url    = "https://api.jd.com/routerjson";
		$client = new Client();

		//url参数
		$url_params = [
			"method"            => $methods,
			'access_token'      => $config['access_token'],
			'app_key'           => $config['app_key'],
			'timestamp'         => $date,
			'v'                 => "2.0",
			"sign"              => $needQianMing['sign'],
			"sign_method"       => 'md5',
			'360buy_param_json' => json_encode( $business ),
		];

		//请求
		$res = $client->post( $url, [
			'headers' => [
				"Content-Type" => "application/json;charset=UTF-8",
			],
			'query'   => $url_params,
		] );

		$result = $res->getBody()->getContents();
		$info   = json_decode( $result, true );

		if ( empty( $info ) ) {
			return false;
		}

		return $info;
	}


	public function test(){
		return 333;
	}

}