<?php

class VimeoAPI
{
	private $vmusername;
	private $limit;
	private $width;
	private $height;
	private $cacheTime;
	public  $uploaded;
	public  $cache = "vimeoCache";
	
	function __construct($username,$limit,$width,$height,$cacheTime)
	{
		$this->vmusername = $username;
		$this->limit      = $limit;
		$this->width      = $width;
		$this->height     = $height;
		$this->cacheTime  = $cacheTime;
	}
	
	function getFeed()
	{
		if(!$this->getCachedVimeoData($this->vmusername))
		{
			$jsonURL = "http://vimeo.com/api/v2/".$this->vmusername."/videos.json";
			try{//cURL
						$vimcurl = curl_init();
						curl_setopt($vimcurl, CURLOPT_URL, $jsonURL);
						curl_setopt($vimcurl, CURLOPT_HEADER, 0);
						curl_setopt($vimcurl, CURLOPT_RETURNTRANSFER, 1); 
			    $json = curl_exec($vimcurl);
						curl_close($vimcurl);
						
						$video = json_decode($json);
						$this->cacheVimeoData($this->ytusername, $jsonc);				
				} 
				catch (Exception $e)
				{
					try{//file_get_contents()
						$json = file_get_contents($jsonURL);
						$video = json_decode($json);
						$this->cacheVimeoData($this->ytusername,$json);	
					   } 
					   catch(Exception $e)
					   {
						echo 'Could not connect to Vimeo API ',  $e->getMessage(), "\n";
					   }
				}
		}
		else
		{
			$video = $this->getCachedVimeoData($this->vmusername);
		}
		
		//Vimeo video
		
		foreach($video as $row)
		{
			$this->uploaded = strtotime($row->upload_date);
			
			$feed['bar'] .=  "<li class='trigger'><a data='http://player.vimeo.com/video/".$row->id."' class='videoClick' href='".$_SERVER['PHP_SELF']."/?video_vm=".$row->id."'><img src='".$row->thumbnail_medium."' width='150' /></a><div class='video_desc_box'><div class='video_title'>".$row->title."</div><div class='video_desc'>".$row->description."</div><div class='video_date'>".date('Y-m-d H:i:s',$this->uploaded)."</div></div></li>";
			
			$feed['playerUrl'][] = 'http://player.vimeo.com/video/'.$row->id;
			$feed['date'][] = date('Y-m-d H:i:s',$this->uploaded);
		}
		return $feed;
	}
	
	public function cacheVimeoData($user, $data)
		{
			// Load the cache stored in the db
			$youtubeCache = get_option($this->cache);
			if(empty($youtubeCache))
			{
				$youtubeCache = array();
			}
			$youtubeCache[$user] = $data;
			$youtubeCache[$user.'-date'] = date('Y-m-d H:i:s',mktime(date('H')+$this->cacheTime)); //Setter dato for lagret cache
			// Store updated options back in the database.
			update_option($this->cache, $youtubeCache);
		}
		
	public function getCachedVimeoData($user)
	{
		$vimeoCache = get_option($this->cache);
		if(empty($vimeoCache) || empty($vimeoCache[$user]) || empty($vimeoCache[$user.'-date'])
			|| strtotime($vimeoCache[$user.'-date']) < strtotime(date('Y-m-d H:i:s')))
		{
			return false;
		}
		else
		{   
			 $jsonURL = $vimeoCache[$user];
			 $vmvideo = json_decode($jsonURL); 
			 return $vmvideo;
			 
		}
	}
	
	
}

?>