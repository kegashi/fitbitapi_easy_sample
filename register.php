<?php

	ini_set('display_errors',1); //デバッグ用
	ini_set('default_charset', 'UTF-8');

	define('CLIENT_ID', '229RM7');
	define('CLIENT_KEY', '59b894f8dffb808e08364ba88c6a85aa');
	define('CLIENT_SECRET', '4faaf1b8a2042a03427c0f21bf8254ea');
	define('CALLBACK_URL', 'http://133.27.171.211/SleepGame/api/easy_sample/register.php');
	define('TOKEN_URL','https://api.fitbit.com/oauth2/token');
	define('USER_URL', 'https://api.fitbit.com/1/user/-/profile.json');
	define('SUBSCRIBER', 'sleep');//睡眠データを自動取得したい場合
	define('SUBSCRIBER_ID', '1')

	$cliid = CLIENT_ID;
	$clisec = CLIENT_SECRET;

	$AuthStr = "Authorization: Basic " . base64_encode("$cliid:$clisec");
	
	$headers = [
        $AuthStr,
        'Content-Type: application/x-www-form-urlencoded'
    ];

	$params = [
			'code' => $_GET['code'],
			'grant_type' => 'authorization_code',
			'redirect_uri' => CALLBACK_URL,
			'client_id' => CLIENT_ID,
	];

	//ユーザ情報をjson形式で取得
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_URL, TOKEN_URL);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
	$response = curl_exec($curl);
	$json = json_decode($response, true);
	curl_close($curl);
	

	//ここから取得したaccess_tokenを使用してuser情報を取得する
	
	$headers2 = [
		'Authorization: Bearer ' . $json['access_token']
	];

	$curl2 = curl_init();
	curl_setopt($curl2, CURLOPT_HTTPHEADER, $headers2);
	curl_setopt($curl2, CURLOPT_URL, USER_URL);
	curl_setopt($curl2, CURLOPT_POST, true);
	curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
	$res2 = curl_exec($curl2);
	$json2 = json_decode($res2, true);
	curl_close($curl2);

	//subscriberの登録
	//ユーザが睡眠情報をfitbitで同期した時クライアントサーバに通知してくれる機能
	$subscriberurl = "https://api.fitbit.com/1/user/-/". SUBSCRIBER ."/apiSubscriptions/". SUBSCRIBER_ID .".json";
	$curl3 = curl_init();
	curl_setopt($curl3, CURLOPT_HTTPHEADER, $headers2);
	curl_setopt($curl3, CURLOPT_URL, $subscriberurl);
	curl_setopt($curl3, CURLOPT_POST, true);
	curl_setopt($curl3, CURLOPT_RETURNTRANSFER, true);
	$res3 = curl_exec($curl3);
	$json3 = json_decode($res3, true);
	var_dump($json3);
	curl_close($curl3);

	echo "<strong>名前:</strong> ";
	echo $json2['user']['displayName'];
	echo "<br /><strong>Id:</strong> ";
	echo $json2['user']['encodedId'];
	echo "<br /><strong>始めた日:</strong> ";
	echo $json2['user']['memberSince'];
	echo "<br /><strong>アクセストークン:</strong> ";
	echo $json['access_token'];
	echo "<br /><strong>リフレッシュトークン:</strong> ";
	echo $json['refresh_token'];
