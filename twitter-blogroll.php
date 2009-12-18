<?php  
/* 
Plugin Name: Twitter Blogroll 
Plugin URI: http://blog.nnatali.com/tag/twitter-blogroll/ 
Description: Create a blogroll on our blog based on a list of twitter.  
Version: 1.0 
Author: nnatali 
Author URI: http://nnatali.com 
*/  

//constante que define el widget
define(TWITTER_BLOGROLL_WIDGET_ID, "widget_twitter_blogroll");  
  
//funcion especifica   
function twitter_blogroll($twitter_user, $twitter_pass, $twitter_list, $list_avatar, $size_avatar){

	if($twitter_list!='')
		$twitter_url="http://api.twitter.com/1/".$twitter_user."/".$twitter_list."/members.xml";
    else
		$twitter_url="http://twitter.com/statuses/friends/".$twitter_user.".xml";
		 
	
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $twitter_url);
    curl_setopt($ch, CURLOPT_VERBOSE, 0); // no imprimir nada
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, "$twitter_user:$twitter_pass"); // autenticación
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // para no esperar indefinidamente
    curl_setopt($ch, CURLOPT_GET, 1);
    $result = curl_exec($ch);
	curl_close($ch);

	$replies=@simplexml_load_string($result);
	
	//recogemos en un array los valores a utilizar
	$items = $replies->xpath('//screen_name'); //nombres de usuarios
	foreach ( $items as $item ) {
		$hasta=count($items)-1;
		$usuarios[]=$item;
	} 
	$items2 = $replies->xpath('//url'); //urls
	foreach ( $items2 as $item ) {
		$webs[]=$item;
	} 
	$items3 = $replies->xpath('//profile_image_url'); //avatars
	foreach ( $items3 as $item ) {
		$avatars[]=$item;
	}
	
	//mostramos por pantalla
	if($list_avatar!=1)
		echo '<ul>';
	if($size_avatar=='')
		$size_avatar='25';
		
	for($i=0;$i<=$hasta;$i++)
	{
		if($webs[$i]=='')
			$webs[$i]='http://twitter.com/'.$usuarios[$i];
	
		if($list_avatar==1)
			echo '<a href="'.$webs[$i].'" title="'.$usuarios[$i].'"><img src="'.$avatars[$i].'" alt="'.$usuarios[$i].'" width="'.$size_avatar.'" height="'.$size_avatar.'" /></a>';
		else
			echo '<li><a href="'.$webs[$i].'" title="'.$usuarios[$i].'">'.$usuarios[$i].'</a></li>';
	}
	
	if($list_avatar!=1)
		echo '</ul>';
	
}

//crea la estructura del widget
function widget_twitter_blogroll($args) {
  extract($args, EXTR_SKIP);
  $options = get_option(TWITTER_BLOGROLL_WIDGET_ID);

  $widget_title = $options["widget_title"];
  $twitter_user = $options["twitter_user"];
  $twitter_pass = $options["twitter_pass"];
  $twitter_list = $options["twitter_list"];
  $list_avatar = $options["list_avatar"];
  $size_avatar = $options["size_avatar"];

  echo $before_widget;
  echo $before_widget;
  echo $before_title . $widget_title . $after_title;
  twitter_blogroll($twitter_user, $twitter_pass, $twitter_list, $list_avatar, $size_avatar);
  echo $after_widget;
}


//añade el widget a la lista
function widget_twitter_blogroll_init(){
  wp_register_sidebar_widget(TWITTER_BLOGROLL_WIDGET_ID, 
  	__('Twitter Blogroll'), 'widget_twitter_blogroll');
}
add_action("plugins_loaded", "widget_twitter_blogroll_init");


// Opciones para la administracion del widget
function widget_twitter_blogroll_control() {
  $options = get_option(TWITTER_BLOGROLL_WIDGET_ID);
  if (!is_array($options)) {
    $options = array();
  }

  $widget_data = $_POST[TWITTER_BLOGROLL_WIDGET_ID];
  if ($widget_data['submit']) {
    $options['widget_title'] = $widget_data['widget_title'];
	$options['twitter_user'] = $widget_data['twitter_user'];
	$options['twitter_pass'] = $widget_data['twitter_pass'];
    $options['twitter_list'] = $widget_data['twitter_list'];
	$options['list_avatar'] = $widget_data['list_avatar'];
	$options['size_avatar'] = $widget_data['size_avatar'];

    update_option(TWITTER_BLOGROLL_WIDGET_ID, $options);
  }

  // Parametros de la funcion
  $widget_title = $options['widget_title'];
  $twitter_user = $options['twitter_user'];
  $twitter_pass = $options['twitter_pass'];
  $twitter_list = $options['twitter_list'];
  $list_avatar = $options['list_avatar'];
  $size_avatar = $options['size_avatar'];
  
  // El html del formulario de opciones
  ?>
	<p>
	  <label for="<?php echo TWITTER_BLOGROLL_WIDGET_ID;?>-widget-title">
		Widget title:
	  </label>
	  <input class="widefat" 
		type="text"
		name="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>[widget_title]" 
		id="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>-widget-title" 
		value="<?php echo $widget_title; ?>"/>
	</p>
	<p>
	  <label for="<?php echo TWITTER_BLOGROLL_WIDGET_ID;?>-twitter-user">
		Twitter user:
	  </label>
	  <input class="widefat" 
		type="text"
		name="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>[twitter_user]" 
		id="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>-twitter-user" 
		value="<?php echo $twitter_user; ?>"/>
	</p>
	<p>
	  <label for="<?php echo TWITTER_BLOGROLL_WIDGET_ID;?>-twitter-pass">
		Twitter pass:
	  </label>
	  <input class="widefat" type="password" 
		name="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>[twitter_pass]" 
		id="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>-twitter-pass" 
		value="<?php echo $twitter_pass; ?>"/>
	</p>
	<p>
	  <label for="<?php echo TWITTER_BLOGROLL_WIDGET_ID;?>-twitter-list">
		Twitter list:
	  </label>
	  <input class="widefat" type="text" 
		name="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>[twitter_list]" 
		id="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>-twitter-list" 
		value="<?php echo $twitter_list; ?>"/>
	</p>
	<p>
	  <label for="<?php echo TWITTER_BLOGROLL_WIDGET_ID;?>-list-avatar">
		Show avatars:
	  </label>
	  <select class="widefat"
		name="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>[list_avatar]"
		id="<?php echo TWITTER_BLOGROLL_WIDGET_ID;?>-list-avatar">
		<option value="1" <?php echo ($list_avatar == "1") ? "selected" : ""; ?>>
		  Yes
		</option>
		<option value="0" <?php echo ($list_avatar == "1") ? "" : "selected"; ?>>
		  No
		</option>
	  </select>
	</p>
	<p>
	  <label for="<?php echo TWITTER_BLOGROLL_WIDGET_ID;?>-size-avatar">
		Avatars size:
	  </label>
	  <input class="widefat" type="text" 
		name="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>[size_avatar]" 
		id="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>-size-avatar" 
		value="<?php echo $size_avatar; ?>"/>
	</p>
	<input type="hidden" name="<?php echo TWITTER_BLOGROLL_WIDGET_ID; ?>[submit]" value="1"/>
<?php
}

wp_register_widget_control(TWITTER_BLOGROLL_WIDGET_ID, __('Twitter Blogroll'), 'widget_twitter_blogroll_control');


?> 