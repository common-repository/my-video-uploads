jQuery(document).ready(function() {
	 
	 var iframesrc = jQuery("#player iframe").attr("src");
	 jQuery(".trigger").each(function(index, element) {
        var data = jQuery(this).children("a").attr("data");
		if(data == iframesrc)
		{
			var desc = jQuery(this).children(".video_desc_box").children(".video_desc").html();
			jQuery("#player_desc").html(desc)
		}
    });
	 
   jQuery(".video_title").hide();
   jQuery("#player_desc").show();
   
	var config = {
		over: makeTall, // function = onMouseOver callback (REQUIRED)    
     		timeout: 500, // number = milliseconds delay before onMouseOut    
    		out: makeShort // function = onMouseOut callback (REQUIRED)    
		};
	function makeTall()
	{
		jQuery(this).children(".video_desc_box").children(".video_title").fadeIn(1000);
	}
	function makeShort()
	{
		jQuery(this).children(".video_desc_box").children(".video_title").fadeOut(1000);
	}
   jQuery(".trigger").hoverIntent(config);
   
   //Changes the content of the player
   jQuery(".videoClick").click(function(){
	   var data = jQuery(this).attr("data");
	   jQuery("#player").children("iframe").attr("src",data);
	   
	   var desc = jQuery(this).parent().children(".video_desc_box").children(".video_desc").html();
	   jQuery("#player_desc").hide();
	   jQuery("#player_desc").html(desc);
	   jQuery("#player_desc").fadeIn("slow");
	   return false;
   });
  
});

