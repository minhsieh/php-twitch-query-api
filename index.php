<?php
// ini_set('display_errors','1');
// error_reporting(E_ALL);
require_once('vendor/onephp/onephp.php');
include_once __DIR__ . "/vendor/autoload.php";

use Api\Twitch;

$app = new \OnePHP\App();

$app->get('/twitch/channel/:name/exist',function( $name ) use ( $app ){//Action
	$tw = new Twitch();
	$channel = $tw->getChannel($name);
	
	$err_msg = ( empty($_REQUEST['err']) )?'找不到這個Twitch使用者':$_REQUEST['err'];
	if(empty($channel['created_at'])) return $app->ResponseHTML('找不到這個Twitch使用者', 200);
	
	if(!empty($_REQUEST['format'])){
		$msg = $_REQUEST['format'];
		$msg = str_replace("[0]", $channel['display_name'], $msg);
		$msg = str_replace("[1]", $tw->timeago($channel['created_at']), $msg);
	}else{
		$msg = $tw->timeago($channel['created_at']);
	}
	
	return $app->ResponseHTML($msg, 200);
});

$app->get('/twitch/channel/:name/status',function( $name ) use ( $app ){//Action
	$tw = new Twitch();
	$data = $tw->getStream($name);
	$err_msg = ( empty($_REQUEST['err']) )?'目前沒在開台':$_REQUEST['err'];
	
	if(empty($data['stream'])) return $app->ResponseHTML($err_msg, 200);
	$data = $data['stream'];
	
	if(!empty($_REQUEST['format'])){
		$msg = $_REQUEST['format'];
		$msg = str_replace("[0]", $data['channel']['display_name'], $msg);
		$msg = str_replace("[1]", $data['game'], $msg);
		$msg = str_replace("[2]", $data['viewers'], $msg);
	}else{
		$msg = $data['channel']['display_name'].' 目前正在玩 '.$data['game'].'，有 '.$data['viewers'].' 個人正在觀看喔';
	}
	return $app->ResponseHTML($msg, 200);
});

$app->get('/twitch/channel/:name/uptime',function( $name ) use ( $app ){//Action
	$tw = new Twitch();
	$data = $tw->getStream($name);
	$err_msg = ( empty($_REQUEST['err']) )?'目前沒在開台':$_REQUEST['err'];
	
	if(empty($data['stream'])) return $app->ResponseHTML($err_msg, 200);
	$data = $data['stream'];
	
	if(!empty($_REQUEST['format'])){
		$msg = $_REQUEST['format'];
		$msg = str_replace("[0]", $data['channel']['display_name'], $msg);
		$msg = str_replace("[1]", $tw->timeago($data['created_at']), $msg);
	}else{
		$msg = $tw->timeago($data['created_at']);
	}
	return $app->ResponseHTML($msg, 200);
});

$app->get('/twitch/channel/:name/random',function( $name ) use ( $app ){//Action
	$tw = new Twitch();
	$data = $tw->getChatters($name);
	$err_msg = ( empty($_REQUEST['err']) )?'目前沒在開台':$_REQUEST['err'];
	
	if(empty($data['chatters']['moderators']) && empty($data['chatters']['viewers']) ) return $app->ResponseHTML($err_msg, 200);
	
	$viewer = array_merge($data['chatters']['moderators'],$data['chatters']['viewers']);
	$name = $viewer[array_rand($viewer)];
	$user = $tw->getUser($name);
	
	return $app->ResponseHTML($user['display_name'], 200);
});

$app->get('/twitch/channel/:name/follows',function( $name ) use ( $app ){//Action
	$tw = new Twitch();
	$channel = $tw->getChannel($name);
	$follows = $tw->getChannelFollows($name);
	$err_msg = ( empty($_REQUEST['err']) )?'找不到這個Twitch使用者':$_REQUEST['err'];
	
	if(!empty($channel['error'])) return $app->ResponseHTML($err_msg, 200);
	
	if(!empty($_REQUEST['format'])){
		$msg = $_REQUEST['format'];
		$msg = str_replace("[0]", $channel['display_name'], $msg);
		$msg = str_replace("[1]", $channel['followers'], $msg);
		$msg = str_replace("[2]", $follows['follows'][0]['user']['display_name'] , $msg);
	}else{
		$msg = $channel['display_name'].' 目前有 '.$channel['followers'].' 個人追隨，最新的追隨者是 '.$follows['follows'][0]['user']['display_name'];
	}
	
	return $app->ResponseHTML($msg, 200);
});

$app->get('/twitch/channel/:name/followage/:follow',function( $name , $follow ) use ( $app ){//Action
	$tw = new Twitch();
	$followage = $tw->getUserFollowage($follow, $name);
	$err_msg = ( empty($_REQUEST['err']) )?'還沒開始追隨':$_REQUEST['err'];
	if(empty($followage['created_at'])){
		return $app->ResponseHTML($err_msg, 200);
	}else{
		return $app->ResponseHTML($tw->timeago($followage['created_at']) , 200);
	}
});

$app->get('/minecraft/:ip/:port',function( $ip , $port ) use ($app){
	$mc_status = file_get_contents('https://mcapi.us/server/status?ip='.$ip.'&port='.$port);
	$result = json_decode($mc_status,true);
	
	$err_msg = ( empty($_REQUEST['err']) )?'找不到伺服器':$_REQUEST['err'];
	if($result['status'] == 'error' || empty($result['last_online'])) return $app->ResponseHTML($err_msg, 200);
	
	$online = ($result['online']==1)?"Online":"Offline";
	$players = $result['players']['now'];
	$players_max = $result['players']['max'];
	$version = $result['server']['name'];
	
	if(!empty($_REQUEST['format'])){
		$msg = $_REQUEST['format'];
		$msg = str_replace("[0]", $online, $msg);
		$msg = str_replace("[1]", $players, $msg);
		$msg = str_replace("[2]", $players_max , $msg);
		$msg = str_replace("[3]", $version , $msg);
	}else{
		$msg = '伺服器狀態:'.$online.'，線上人數:'.$players.'，最多人數:'.$players_max.'，伺服器版本:'.$version;
	}
	return $app->ResponseHTML($msg, 200);
});

$app->respond( function() use ( $app ){
  return $app->ResponseHTML('Kappa, 這是一個錯誤的請求網址 This is a bad request url path.', 404);
});

$app->listen();