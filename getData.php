<?php
	//sscheck.php
	//主に「ユーザごとのサブスクライバを表示する」「睡眠などのデータを見る」
	//などのデータの更新を伴わない命令に使える
	//引数に見たいユーザのencodedIDを「id=○○」の形で渡す
	//60行目のURLを変更することで取得できるデータが変わる
	ini_set('display_errors',1);
	ini_set('default_charset', 'UTF-8');

	define('CLIENT_ID', '229RM7');
	define('CLIENT_KEY', '59b894f8dffb808e08364ba88c6a85aa');
	define('CLIENT_SECRET', '4faaf1b8a2042a03427c0f21bf8254ea');
	define('TOKEN_URL','https://api.fitbit.com/oauth2/token');

	$clikey = CLIENT_ID;
	$clisec = CLIENT_SECRET;

	$link = mysql_connect('localhost', 'zukky', '0913kazu');
	$result = mysql_select_db('graduate', $link);

	$AuthStr = "Authorization: Basic " . base64_encode("$clikey:$clisec");

	$headers = [
        $AuthStr,
        'Content-Type: application/x-www-form-urlencoded'
    ];

	//DBからリフレッシュトークンを取得
	$getid = $_GET['id'];
	$result = mysql_query("select refresh_token from user2 where id = '$getid'");
	$row = mysql_fetch_assoc($result);

	$params = [
			'grant_type' => 'refresh_token',
			'refresh_token' => $row['refresh_token']
	];
	


	//アクセストークンの再発行
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_URL, TOKEN_URL);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
	$response = curl_exec($curl);
	$json = json_decode($response, true);
	curl_close($curl);

	$headers2 = [
		'Authorization: Bearer ' . $json['access_token']
	];
	$refresh_token = $json['refresh_token'];

	//DBのリフレッシュトークンの更新
	$result = mysql_query("update graduate.user2 set refresh_token='$refresh_token' where id='$getid'");
	
	//$json['refresh_token'];
	$url = 
	//"https://api.fitbit.com/1/user/". $getid ."/sleep/apiSubscriptions.json";//サブスクライバの確認
	//"https://api.fitbit.com/1/user/". $getid ."/sleep/apiSubscriptions/4.json";//ID4番のサブスクライバを追加
	"https://api.fitbit.com/1/user/". $getid ."/sleep/date/2015-11-25.json";//2015-11-11の睡眠データを取得
	$curl2 = curl_init();
	curl_setopt($curl2, CURLOPT_HTTPHEADER, $headers2);
	curl_setopt($curl2, CURLOPT_URL, $url);
	curl_setopt($curl2, CURLOPT_POST, true);
	curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
	$response2 = curl_exec($curl2);
	$json = json_decode($response2, true);
	var_dump($json);
