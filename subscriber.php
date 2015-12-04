<?php
	
	//ユーザがfitbitデータを同期した時に呼ばれる

	$inputStr = file_get_contents("php://input");

	$json = json_decode($inputStr);
	echo $json[0]->ownerId;
	echo count($json);

	$count = count($json);
	for ($num = 0; $num < $count; $num++){
		if('sleep' == $json[$num]->collectionType){
			$fp5 = fopen('log/test5.txt', 'a');
			fwrite($fp5, "true");
			fclose($fp5);
			$id = $json[$num]->ownerId;
			$date = $json[$num]->date;
			
			//$idと$dateを使ってデータを取得しよう

		}		
	}

?>
