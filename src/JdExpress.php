<?php

namespace Zfq\Express;

use GuzzleHttp\Client;

class JdExpress {


	/**
	 * 通用请求
	 *
	 * @param $config   配置信息
	 * @param $methods  调用方法
	 * @param $business 业务参数
	 *
	 * @return false|mixed
	 */
	public function commonMethods( $config, $methods, $business ) {

		$date = date( "Y-m-d H:i:s", time() );

		//需要签名的参数
		$need_sign = [
			'access_token' => $config['access_token'],
			'app_key'      => $config['app_key'],
			'timestamp'    => $date,
			'v'            => "2.0",
			"method"       => $methods,
		];

		//业务参数
		$need_sign['param_json'] = json_encode( $business );

		//秘钥
		$app_secret = $config['app_secret'];

		//得出签名
		$need_sign['sign'] = $this->sign( $need_sign, $app_secret );

		$url    = "https://api.jdl.cn/" . $methods;
		$client = new Client(['timeout'=>5.0]);

		//url参数
		$url_params = [
			"LOP-DN"       => "express",
			'access_token' => $config['access_token'],
			'app_key'      => $config['app_key'],
			'timestamp'    => $date,
			'v'            => "2.0",
			"sign"         => $need_sign['sign'],
		];

		//请求
		$res = $client->post( $url, [
			'headers' => [
				"X-UseJosAuth" => "true",
				"Content-Type" => "application/json;charset=UTF-8",
			],
			"json"    => $business,
			'query'   => $url_params
		] );

		//获取返回值
		$result = $res->getBody()->getContents();
		$content   = json_decode( $result, true );


		if(empty($over)){
			return  false;
		}

		return $content;
	}


	/**
	 * 签名算法
	 * @param $params
	 * @param $app_secret
	 *
	 * @return string
	 */
	public function sign( $params, $app_secret ) {
		ksort( $params );
		$stringToBeSigned = $app_secret;
		foreach ( $params as $k => $v ) {
			if ( "@" != substr( $v, 0, 1 ) ) {
				$stringToBeSigned .= "$k$v";
			}
		}

		unset( $k, $v );
		$stringToBeSigned .= $app_secret;

		return strtoupper( md5( $stringToBeSigned ) );
	}
}