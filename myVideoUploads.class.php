<?php
/*
Plugin Name: My Video Uploads
Plugin URI: http://www.glennwedin.net/article/a-work-in-progress-wordpress-theme/16
Description: Video plugin that lists your YouTube and Vimeo uploads in a playlist with an embedded player. Use this shortcode in the editor: [videoUploads width="" height=""] Please define the width and height og the player. You can also use this php code in your template: &lt;?php do_action('myvideouploads'); ?&gt;
Version:1.2.2
Author: Glenn Wedin
Author URI: http://www.glennwedin.net
License: GPLv3
*/
require_once("youtubeAPI.class.php"); require_once("vimeoAPI.class.php");

class MyVideoUploads
{
	private $ytuser;  //Youtube username
	private $vmuser;  //Vimeo username
	private $ytlimit; //Limit from each of youtube and vimeo
	private $width;   //Player width
	private $height;  //Player height
	private $api;     //The youtube api object
	private $vmapi;   //The vimeop api object
	private $cacheTime; //The amount of time data should be stored in cache
	private $cssEnabled = true; //Set to true by default. Enables the plugin css.
	private $javascript = true; //Set to true by default. Enables the plugin Javascripts.
	
	/*
	 * __construct
	 * Initializing options, wordpress actions and creates an instance of the YouTube and Vimeo API if the user has provided
	 * his Usernames
	 */
	public function __construct()
	{
		/* Stores settings */
		if(isset($_POST['username']))
		{
			$this->postSettings();
		}
		
		$this->getOptions();
		if(!is_object($this->api))
		{
			if(!empty($this->ytuser) || $this->ytuser !="")
			{
			$this->api = new YoutubeAPI($this->ytuser,$this->ytlimit,$this->width,$this->height,$this->cacheTime);
			}
		}
		if(!is_object($this->vmapi))
		{
			if(!empty($this->vmuser) || $this->vmuser !="")
			{
				$this->vmapi = new VimeoAPI($this->vmuser,$this->ytlimit,$this->width,$this->height,$this->cacheTime);
			}
		}
		
		//Wordpress actions
		add_action('admin_menu',  array(&$this, 'videoUploads')); //Adding menuoption
		if($this->cssEnabled){add_action('wp_head', array(&$this, 'addHeaderStuff')); }     //adding css to head
		add_action('admin_head', array(&$this, 'addAdminHeader'));  //Adding css to head
		add_action('myvideouploads', array(&$this, 'runPlugin'));   //Action that runs the plugin from static page
		add_shortcode('videoUploads', array(&$this, 'videoUploadsShortcode')); //Creates a wordpress shortcode

		if(function_exists('wp_enqueue_script'))
		{
			wp_enqueue_script('jquery');
			if($this->javascript)
			{
			wp_enqueue_script('myvideouploads',WP_PLUGIN_URL.'/my-video-uploads/js/myvideouploads.js',array('jquery'),'1.0',true);
			wp_enqueue_script('hoverIntent',WP_PLUGIN_URL.'/my-video-uploads/js/jquery.hoverIntent.minified.js',array('jquery'),'6.0',true);
			}
		}
		
	}
	
	public function getFeed()
	{
		echo '<div id="videopresenter"><div id="video_bar"><noscript class="notice">Vimeo requires javascript activated</noscript><div class="video_bar left"><ul>';
		if(!empty($this->ytuser) || $this->ytuser != "")
	   {
	   		$yt = $this->api->getFeed();
	   echo $yt['bar'];//Contains the playlist from the YouTube API
			$video_url[$yt['date'][0]] = $yt['playerUrl'][0]; //The url for the player	
			
			$feedIsset = true;	
	   }
	   echo '</ul></div>';	
	   echo '<div class="video_bar right"><ul>';
	   if(!empty($this->vmuser) || $this->vmuser != "")
	   {
	  		$vm = $this->vmapi->getFeed();
	   echo $vm['bar'];//Contains the playlist from the Vimeo API
			$video_url[$vm['date'][0]] = $vm['playerUrl'][0];//The url for the player //FIKS
			
			$feedIsset = true; //Shows the videoplayer only if username is set
	   }
	   	echo '</ul></div></div>';	
			
			if($feedIsset)
			{
			krsort($video_url);
			//var_dump($video_url);
			}
			echo '<div id="player">';
			if(isset($_GET['video_id']) && $_GET['video_id']!="")
			{
				echo '<iframe src="http://www.youtube.com/v/'.$_GET['video_id'].'?f=user_uploads&app=youtube_gdata" frameborder="0" width="'.$this->width.'" height="'.$this->height.'"></iframe>';
			}
			elseif(isset($_GET['video_vm']) && $_GET['video_vm']!="")
			{
				echo '<iframe src="http://player.vimeo.com/video/'.$_GET['video_vm'].'" frameborder=0" width="'.$this->width.'" height="'.$this->height.'"></iframe>';	
			}
			else
			{		
				if($feedIsset)
				{								//gets first array value					
					echo '<iframe src="'.reset($video_url).'" frameborder="0" width="'.$this->width.'" height="'.$this->height.'"></iframe><div id="player_desc"><p></p></div>';		}
				else
				{
					echo '<span class="notice">No videos exists or no usernames has been defined in the option page.</span> ';
				}
			}	
			echo '</div></div>'; 
	}
	
	/*
	 * videoUploads() 
	 * Adds the option page to the settings menu
	 */
	public function videoUploads()
	{
		add_options_page('My videos', 'My Video Uploads', 'manage_options', 'myvideouploads', array(&$this, 'adminPage'));	
	}

	/*
	 * runPlugin() 
	 * Is used by a wordpress action. Lets users place a do_action() in a template
 	 */
	public function runPlugin()
	{
		$this->getFeed();
	}
	
	/*
	 * adminPage() 
	 * This is just HTML for the controlpanel
	 */
	public function adminPage()
	{
		if($this->cssEnabled){ $checkcss = 'checked="checked"';}
		if($this->javascript){ $checkjs  = 'checked="checked"';}
		echo '<div class="wrap">
			<h2>My Video Uploads settings</h2>
			<p><strong>Here is where you enter your username and other options</strong></p>
			
			<form method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'">
			<label class="labels" for="Username">YouTube username:</label><div><input type="text" name="username" value="'.$this->ytuser.'" /></div>
			<label class="labels" for="Username">Vimeo username:</label><div><input type="text" name="vmusername" value="'.$this->vmuser.'" /></div>
			<label class="labels" for="Limit">Video limit(max 50):</label><div><input style="width:40px;" type="text" min="0" max="50" name="limit" value="'.$this->ytlimit.'" /><span class="notice">*</span></div>
			<label class="labels" for="width">Player width:</label><div><input style="width:40px;" type="text" name="width" value="'.$this->width.'" /></div>
			<label class="labels" for="height">Player height:</label><div><input style="width:40px;" type="text" name="height" value="'.$this->height.'" /></div>
			<label class="labels" for="cache">API Cache:</label><div><input style="width:40px;" type="text" name="cache" value="'.$this->cacheTime.'" /><span class="notice">*</span> Hours</div>
			<label class="labels" for="enable css">Enable css:</label><div><input type="checkbox" name="cssenabled" value="true" '.$checkcss.' /></div><label class="labels" for="enable javascript">Enable Javascript:</label><div><input style="margin-top:25px;" type="checkbox" name="jsenabled" value="true" '.$checkjs.' /></div>
			<p><input type="submit" value="Save" /></p>
			</form>
			<hr>
			<h3>Shortcode:</h3>
			<p>For use in editor for pages and posts. This shortcode overrides width and height settings set in the options panel</p>
			<p><strong>[videoUploads width="x" height="x"]</strong></p>
			<p>Or just:</p>
			<p><strong>[videoUploads]</strong></p>
			</br>
			<h3>PHP-code:</h3>
			<p>For use in template</p>
			<p><strong>&lt;?php do_action("myvideouploads"); ?&gt;</strong></p><hr>
			<h4>Notice</h4>
			<p><span class="notice"> *Number of videos needs to be set as it does not default to any value. <br />*Please specify how many hours to store the cache. The cache reduces api calls to youtube. Updates will clear the cache. Width and height will be overridden by defining it in the shortcode.</span></p>
		</div>';
		}
	
	private function postSettings()
	{
		$ytusername = $_POST['username'];
		update_option('ytusername',$ytusername);
		$this->ytuser = $ytusername;
		
		$vmusername = $_POST['vmusername'];
		update_option('vmusername',$vmusername);
		$this->vmuser = $vmusername;
		
		$limit = $_POST['limit'];
		update_option('ytlimit',$limit);
		$this->ytlimit = $limit;
		
		$width = $_POST['width'];
		update_option('width', $width);
		$this->width = $width;
		
		$height = $_POST['height'];
		update_option('height',$height);
		$this->height = $height;
		echo '<div class="updated"><p><strong>Options saved</strong></p></div>'; 

		$css = (bool) $_POST['cssenabled'];
		update_option('cssenabled',$css);
		$this->cssEnabled = $css;
		
		$js = (bool) $_POST['jsenabled'];
		update_option('jsenabled',$js);
		$this->javascript = $js;
		
		
		//Expiration time for the cache
		$cacheExpiration  = $_POST['cache'];
		//pr hour
		update_option('cacheTime',$cacheExpiration);
		$this->cacheTime = $cacheExpiration;
		update_option('youtubeCache',"");//Clears the cache
	}
	
	private function getOptions()
	{
		$this->ytuser	 = get_option('ytusername');//Youtube
		$this->vmuser    = get_option('vmusername');//Vimeo
		$this->ytlimit	 = get_option('ytlimit');   //Video limit
		$this->width   	 = get_option('width');
		$this->height 	 = get_option('height');
		$this->cacheTime = get_option('cacheTime');
		$this->cssEnabled= get_option('cssenabled');
		$this->javascript= get_option('jsenabled');
	}	
	
	public function addHeaderStuff()
	{
		echo '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/my-video-uploads/css/myvideouploads.css"/>';
	}
	
	public function addAdminHeader()
	{
		echo '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/my-video-uploads/css/admin.css"/>';
	}
	
	/**  
	 *  Function that lets users add the widget with a shortcode. 
	 *  Lets them set size in the shortcode itself or defaults to the settings in the settings panel
	 */
	public function videoUploadsShortcode($atts)
	{
		extract(shortcode_atts(array(
		'width' => $this->width,
		'height' => $this->height,
		), $atts ) );
		$this->width = esc_attr($width);
		$this->height= esc_attr($height);
		$this->getFeed();
	}
	
}//End of class


if(class_exists(MyVideoUploads))
{
	$api = new MyVideoUploads();
}
?>
