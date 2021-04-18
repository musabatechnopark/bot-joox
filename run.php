<?php 
$token = "ISI_TOKEN_BOT"; //token bot kalian
function scrap($url){
	$data = file_get_contents($url);
	$data = json_decode($data,1);
	return $data;
}
function updates($update_id){
	global $token;
	$updates = scrap("https://api.telegram.org/bot$token/getUpdates?offset=".$update_id);
	foreach ($updates['result'] as $satuan) {
		$update_id = $satuan['update_id']+1;
		$text = $satuan['message']['text'];
		$chat_id = $satuan['message']['chat']['id'];
		$message_id = $satuan['message']['message_id'];
		if (strstr($text, "/start")) {
			$jawaban = "Untuk menggunakan bot ini kamu tinggal mengirimkan kata kunci / judul lagu yang kamu cari.";
			scrap("https://api.telegram.org/bot$token/sendMessage?chat_id=".$chat_id."&text=".$jawaban."&reply_to_message_id=$message_id");
		}else{
			$data_search = scrap("http://public-restapi.herokuapp.com/api/joox/search?q=".urlencode($text));
			if (isset($data_search['songs'][0]['id'])) {
				$id = $data_search['songs'][0]['id'];
				$api_show = scrap("http://public-restapi.herokuapp.com/api/joox/show?id=$id");
				$mp3 = $api_show[0]['downloadLinks']['mp3'];
				scrap("https://api.telegram.org/bot$token/sendAudio?chat_id=$chat_id&audio=".urlencode($mp3)."&reply_to_message_id=$message_id");
			}else{
				scrap("https://api.telegram.org/bot$token/sendMessage?chat_id=".$chat_id."&text=Lagu dengan kata kunci tersebut tidak tersedia.&reply_to_message_id=$message_id");
			}
		}
		
	}
	updates($update_id);
}

updates(1);
