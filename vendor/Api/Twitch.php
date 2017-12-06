<?php
namespace Api;

class Twitch
{
	private $client_id = 'gvrfkc0vh0y1rmtcg5txlqx2ago25y' ;
	
	private $base_url = 'https://api.twitch.tv/kraken/';
	
	public function __construct(){}
	
	public function setClientId($client_id)
	{
		return $this->client_id = $client_id;
	}
	
	public function setBaseUrl($base_url)
	{
		return $this->base_url = $base_url;
	}
	
	public function getChannel($channel)
	{
		$url = $this->base_url.'channels/'.$channel;
		return $this->get($url);
	}
	
	public function getChannelFollows($channel)
	{
		$url = $this->base_url.'channels/'.$channel.'/follows';
		return $this->get($url);
	}
	
	public function getStream($channel)
	{
		$url = $this->base_url.'streams/'.$channel;
		return $this->get($url);
	}
	
	public function getUser($user)
	{
		$url = $this->base_url.'users/'.$user;
		return $this->get($url);
	}
	
	public function getUserFollowage($user , $channel)
	{
		$url = $this->base_url.'users/'.$user.'/follows/channels/'.$channel;
		return $this->get($url);
	}
	
	public function getUserBlocks($user)
	{
		$url = $this->base_url.'users/'.$user.'/blocks';
		return $this->get($url);
	}
	
	public function getSearchChannel($query)
	{
		$url = $this->base_url.'search/channels?query='.urlencode($query);
		return $this->get($url);
	}
	
	private function get($url , $query = [])
	{
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_HTTPHEADER => [
				'Client-ID: ' . $this->client_id
			],
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_URL => $url
		]);
		$response = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($response,true);
		return $res;
	}
	
	public function getChatters($channel)
	{
		$ch = curl_init();
		curl_setopt_array($ch, [
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_URL => 'https://tmi.twitch.tv/group/user/'.$channel.'/chatters'
		]);
		$response = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($response,true);
		return $res;
	}
	
	public function timeago($time)
	{
		$output = '';
		$second1=floor((time()-strtotime($time)));//兩個日期時間 相差 幾秒
		if($second1 > 2592000){
			$output .= floor($second1/2592000).'個月';
		}if($second1 > 604800){
			$output .= floor( ($second1%2592000)/604800 ).'星期';
		}if($second1 > 86400){
			$output .= floor( (($second1%2592000)%604800)/86400 ).'天';
		}if($second1 > 3600){
			$output .= floor( ((($second1%2592000)%604800)%86400)/3600 ).'小時';
		}if($second1 > 60){
			$output .= floor( (((($second1%2592000)%604800)%86400)%3600)/60 ).'分鐘';
		}if($second1 > 1){
			$output .= floor( (((($second1%2592000)%604800)%86400)%3600)%60 ).'秒';
		}
		return $output;
	}
	
}