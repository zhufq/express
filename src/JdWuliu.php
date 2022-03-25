<?php


namespace Zfq\Express;


use GuzzleHttp\Client;

class JdWuliu {

	//京东物流通用接口
	public function commonMethods( $config, $methods, $business ) {
		//时间
		$date = date( "Y-m-d H:i:s", time() );
		//需要签名的参数
		$need_sign = [
			'access_token' => $config['access_token'],
			'app_key'      => $config['app_key'],
			"method"       => $methods,
			'timestamp'    => $date,
			'sign_method'  => "md5",
			'v'            => "2.0",
		];

		//业务参数
		$need_sign['360buy_param_json'] = json_encode( $business );

		//秘钥
		$app_secret = $config['app_secret'];

		//得出签名
		$need_sign['sign'] = $this->sign( $need_sign, $app_secret );

		$url    = "https://api.jd.com/routerjson";
		$client = new Client(['timeout'=>5]);

		//url参数
		$url_params = [
			"method"            => $methods,
			'access_token'      => $config['access_token'],
			'app_key'           => $config['app_key'],
			'timestamp'         => $date,
			'v'                 => "2.0",
			"sign"              => $need_sign['sign'],
			"sign_method"       => 'md5',
			'360buy_param_json' => json_encode( $business ),
		];

		try {
			//请求
			$res = $client->post( $url, [
				'headers' => [
					"Content-Type" => "application/json;charset=UTF-8",
				],
				'query'   => $url_params,
			] );

			$result = $res->getBody()->getContents();
			$content   = json_decode( $result, true );
			if(!$content){
				return false;
			}

			return $content;
		}catch (\Exception $e){
			return false;
		}

	}

	//物流签名
	public function sign( $params, $app_secret ) {
		$result = '';
		if ( count( $params ) > 0 && strlen( $app_secret ) > 0 ) {
			ksort( $params );                                  // 1
			$string = '';
			foreach ( $params as $key => $val ) {
				$string .= $key . $val;                       // 2
			}
			$string = $app_secret . $string . $app_secret;          // 3
			$md5    = md5( $string );                            // 4
			$result = strtoupper( $md5 );                     // 4
		}

		return $result;
	}
}