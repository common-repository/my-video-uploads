<?php

class YoutubeAPI
{
	private $ytusername;
	private $ytlimit;
	private $width;
	private $height;
	public  $cache = "youtubeCache";
	private $cacheTime; //Expiration time of cache
	public  $uploaded; //Upload date
	
	
	/**
	 *  Constructor that sets Class variables
	 *  @param $width, $height sets  
	 */
	public function __construct($ytusername,$ytlimit,$width,$height,$cacheTime)
	{
		$this->ytusername = $ytusername;
		$this->ytlimit    = $ytlimit;
		$this->width      = $width;
		$this->height     = $height;
		$this->cacheTime  = $cacheTime;
	}
	
	/**
	 * Function that connects to API if cache has expired and retrieves JSON
	 */
	public function getFeed()
	{
		// set feed URL
		
		if(!$this->getCachedYoutubeData($this->ytusername))
		{
		$jsoncURL = 'http://gdata.youtube.com/feeds/api/users/'.$this->ytusername.'/uploads?v=2&alt=jsonc&max-results='.$this->ytlimit;
   		
			try{//cURL
				$ytcurl = curl_init();
						curl_setopt($ytcurl, CURLOPT_URL, $jsoncURL);
						curl_setopt($ytcurl, CURLOPT_HEADER, 0);
						curl_setopt($ytcurl, CURLOPT_RETURNTRANSFER, 1); 
			   $jsonc = curl_exec($ytcurl);
						curl_close($ytcurl);
						
						$ytvideo = json_decode($jsonc);
						$this->cacheYoutubeData($this->ytusername, $jsonc);
				} 
				catch (Exception $e)
				{
					try{//file_get_contents()
						$jsonc = file_get_contents($jsoncURL);
						$ytvideo = json_decode($jsonc);
						$this->cacheYoutubeData($this->ytusername,$jsonc);	
					   } 
					   catch(Exception $e)
					   {
						echo 'Could not connect to YouTube API ',  $e->getMessage(), "\n";
					   }
				}
			
		}
		else
		{
			$ytvideo = $this->getCachedYoutubeData($this->ytusername);
		}
			
			//Youtube video
			
			foreach($ytvideo->data->items as $row)
			{
				$this->uploaded = strtotime($row->uploaded);
			
				$content = 5;//Youtube stream
				$feed['bar'] .= "<li class='trigger'><a data='".$row->content->$content."' class='videoClick' href='".$_SERVER['PHP_SELF']."/?video_id=".$row->id."'><img src='".$row->thumbnail->sqDefault."' width='150' /></a><div class='video_desc_box'><div class='video_title' id='title_".$row->id."'>".$row->title."</div><div class='video_desc'>".$row->description."</div><div class='video_date'>".date('Y-m-d H:i:s',$this->uploaded)."</div></div></li>";
				
			$feed['playerUrl'][] = $row->content->$content;		
			$feed['date'][] = date('Y-m-d H:i:s',$this->uploaded);
			}	
			
			
			return $feed;
		
	}
	/**
	 *  Caching JSON result in database
	 */
	public function cacheYoutubeData($user, $data)
		{
			// Load the cache stored in the db
			$youtubeCache = get_option($this->cache);
			if(empty($youtubeCache))
			{
				$youtubeCache = array();
			}
			$youtubeCache[$user] = $data;
			$youtubeCache[$user.'-date'] = date('Y-m-d H:i:s',mktime(date('H')+$this->cacheTime)); //Sets date for stored cache
			// Store updated options back in the database.
			update_option($this->cache, $youtubeCache);
		}
		
	/**
	 *  Retrieves cached JSON data
	 */
	public function getCachedYoutubeData($user)
	{
		$youtubeCache = get_option($this->cache);
		if(empty($youtubeCache) || empty($youtubeCache[$user]) || empty($youtubeCache[$user.'-date'])
			|| strtotime($youtubeCache[$user.'-date']) < strtotime(date('Y-m-d H:i:s')))
		{
			return false;
		}
		else
		{   
			 $jsonc = $youtubeCache[$user];
			 $ytvideo = json_decode($jsonc); 
			 return $ytvideo;
			 
		}
	}
	
}
?>