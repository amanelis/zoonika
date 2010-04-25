<?PHP
/****************************** START SCRAPING FUNCTIONS *******************************************/
/*
This function will return a random quote, feel free to add yours.
Don't copy mine, be creative, I like tea, choose something you like.
*/
function getQuote(){
	//username/password, db credentials
	include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');
	
	// Connect to server and select databse.
	$conn = mysql_connect("$db_host", "$db_user", "$db_pass")or die("cannot connect"); 
	$dbselct = mysql_select_db("$database")or die("cannot select DB");
	
	$quotes_query = ("SELECT * FROM tbs_tweets");
	$quotes_result = mysql_query($quotes_query) or die (mysql_error());
	
	$index = 0;
	$q_array = array();
	while($row = mysql_fetch_array($quotes_result, MYSQL_ASSOC)){
		$r_msg = $row['msg'];
		$q_array[$index] = $r_msg;
		$index++;
	}
	
	$q_size = (count($q_array) - 1);
	$rand_range = rand(0, $q_size);

	mysql_free_result($quotes_result);
	$close = mysql_close($conn);
	return $q_array[$rand_range];
}

/*
Standard function that returns links or
more so their 'href' source not that actual
text itself in the link but the href
@return: returns an array of links
@params: $url = 'SHOULD BE A PARSABLE HTML PAGE
WITH LINKS'
*/
function getLinksByURLByDOM($url){
    $array_links_count = 0;
    $array_links = array();
    $html = file_get_html($url);

    foreach($html->find('a') as $element){
        $array_links[$array_links_count] = $element;
        $array_links_count++;
    }
    return $array_links;
}

/*
This function will scrape a particular users
twitter feed, along with time stamp
@return: echos content out.
@params: must pass in twitter.com/user as url
*/
function getTwitterUserFeedByDOM($url){
	$html = file_get_html($url);
	
	$posts = $html->find('span.entry-content');
	$tstmp = $html->find('a[class]');
	
	//twitter user feeds only contain 20 posts per page
	for($i=0; $i<19; $i++){
		echo $posts[$i]." ".$tstmp[$i]."<br>";
	}
}

/*
This function searches twitter's real time
twitter feed from all users on a search request 
of your choice
@return: prints array of real time queries
@params: give a search criteria, ex: tea
*/
function getTwitterSearchFeedByDOM($search){
	$full_url = 'http://search.twitter.com/search?q='.$search;

	$html = file_get_html($full_url);
	$posts = $html->find('span[class=msgtxt en]');
	
	//live twitter feed output
	for($i=0; $i<25; $i++){
		echo $posts[$i]."<br>";
	}
}

/*
This function enables you to query twitter search
and pull data using json and then allow for array parse
this one is easy
@return: two dimensional array of users and twitter posts
@params: pass in search criteria, ex: tea
*/
function getTwitterSearchFeedByJSON($search){
	$full_url = 'http://search.twitter.com/search.json?q='.$search;
	
	$json = file_get_contents($full_url, true);
	$decode = json_decode($json, true);
	
	$posts = array();
	$user_count = 0;

	//apparently can only return 15 results in json array
	for($i=0; $i<14; $i++){
		//echo "<img src=\"$decode[results][$i][profile_image_url]\"/>";
		$r_user = $decode[results][$i][from_user];
		$r_text = $decode[results][$i][text];
	
		$posts[$user_count][0] = $r_user;
		$posts[$user_count][1] = $r_text;
		$user_count++;	
	}
	return $posts;
}
/****************************** END SCRAPING FUNCTIONS *********************************************/






/****************************** START TWITTER FUNCTIONS ********************************************/
/*
Return API calls remianing in current hour/period for given
twitter bot.
*/
function hourly_hits_left() {
		//username/pass db credentials
		include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');
    	
    	$ch = curl_init();            
    	$target = 'http://twitter.com/account/rate_limit_status.xml';  
    	curl_setopt($ch, CURLOPT_URL, $target); 
    	curl_setopt($ch, CURLOPT_USERPWD, "$bot_username:$bot_password");
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    	$str = curl_exec($ch);
    	preg_match_all('#<remaining-hits type="integer">([^<]+)</remaining-hits>#', $str, $matches); 
		return "API calls left this period: ".$matches[0][0]."<br />";
}

/*
This function should return the users lists of tweets,
basically custom lists you/or user define #doggies
@return: returns an xml list of data
*/
function getTwitterUserList(){
	//username/pass db credentials
	include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');
	
	$e_result = exec('curl -u '.$bot_username.':'.$bot_password.' http://api.twitter.com/1/'.$bot_username.'/lists.xml');

	$url = 'http://api.twitter.com/1/'.$bot_username.'/lists.xml';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_USERPWD, "$bot_username:$bot_password"); 
	$result = curl_exec($ch); 
	curl_close($ch); 
	echo $result;
}

/*
This function given a users screen_name, can force the bot to follow that user. 
*/
function Follow_User($screen_name){
		//username/pass db credentials
		include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');
        
        $ch = curl_init();
        $target="http://twitter.com/friendships/create/".$screen_name.".xml?follow=true";
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_USERPWD, "$bot_username:$bot_password");
        $str = curl_exec($ch);
        curl_close ($ch);
		echo"$target <br><br> $str <br><br>";
}

/*
Gets recent @yourbot tweets
*/
function recent_mentions_reply($login){
	//username/pass db credentials
	include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');

	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://twitter.com/statuses/mentions.xml"); 
    curl_setopt($ch, CURLOPT_USERPWD, "$bot_username:$bot_password");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch); 
	$responseInfo = curl_getinfo($ch); 
	curl_close($ch);
	if(intval($responseInfo['http_code'])==200){
		preg_match_all('/<text>(.*)<\/text>/', $response, $text); 
		preg_match_all('/<screen_name>(.*)<\/screen_name>/', $response, $sn); 
		preg_match_all('/<profile_image_url>(.*)<\/profile_image_url>/', $response, $icon); 
		preg_match_all('/<source>(.*)<\/source>/', $response, $source); 
		preg_match_all('/<created_at>(.*)<\/created_at>/', $response, $tweeted); 
		preg_match_all('/<id>(.*)<\/id>/', $response, $id); 
			$i=0; $d=0; $color1 = "#F9F9F9"; $color2 = "#FFF"; 
		if(sizeof($text) == "0"){
			echo"No @mentions to reply to at this time.";
		} else {
			foreach($text[0] as $k => $txt) {
				$row_color = ($i % 2) ? $color1 : $color2; 
				$msg = strip_tags($text[0][$i]);
					$msg = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\">\\0</a>", $msg);
				$user = strip_tags($sn[0][$i]);
				$img = strip_tags($icon[0][$i]);
				$from = strip_tags($source[0][$i]);
				$time = strip_tags($tweeted[0][$d]);
				$tweet_id = strip_tags($id[0][$d]);
				//$tcheck = mysql_query ("SELECT * FROM `tbs_reply_log` WHERE status_id = '$tweet_id'"); 
				//$tcn = mysql_num_rows($tcheck);
				if($tcn == 0){				
					echo "<img style=\"padding:10px;\" src=\"$img\" border=\"0\" height=\"48\" width=\"48\" />";
					echo "<a href=\"http://twitter.com/$user\" target=\"_blank\">$user</a></strong> - $msg <br /> ";
			
				}//END IF
				
				$i++;
				$d = $d+2;
			}
		}
	} else { 
			// Something went wrong
			//echo "Twitter Error: " . $responseInfo['http_code'] . $response; 
	}
}

/*
Global process POST
*/
function processPost($url,$postargs=false,$returncode=false){ 
	//username/pass db credentials
	include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');
		 
	$ch = curl_init($url);
	curl_setopt ($ch, CURLOPT_POST, true);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
	curl_setopt($ch, CURLOPT_USERPWD, "$bot_username:$bot_password"); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$response = curl_exec($ch); 
	$responseInfo = curl_getinfo($ch); 
	curl_close($ch);
	if(intval($responseInfo['http_code'])==200){
		if($returncode = true){
			return $responseInfo['http_code'];
		} else {
			return $response;
		}
	} else { 
			// Something went wrong
			return "Twitter Error: " . $responseInfo['http_code'] . $response; 
	}
	
}

/*
Global GET Process function cURL
*/
function processGet($url,$returncode=false){ 
	//username/pass db credentials
	include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');
		 
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_USERPWD, "$bot_username:$bot_password");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch); 
	$responseInfo = curl_getinfo($ch); 
	curl_close($ch);
	if(intval($responseInfo['http_code']) == 200){
		if($returncode = true){
			return $responseInfo['http_code'];
		} else {
			return $response;
		}
	} else { 
			// Something went wrong
			return "Twitter Error: " . $responseInfo['http_code'] . $response; 
	}
}

/*
Funtion returns all data about twitter user
*/
function twitterUserInformation($user) {
	//username/pass db credentials
	include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');
 
	//url to pull data from
	$url_xml = 'http://api.twitter.com/1/users/show/'.$bot_username.'.xml';

	//execute grab of data
    $curlhandle = curl_init();
	curl_setopt($curlhandle, CURLOPT_URL, $url_xml);
	curl_setopt($curlhandle, CURLOPT_USERPWD, $bot_username.':'.$bot_password);
    curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curlhandle);
    curl_close($curlhandle);
     
	//create new xmlobj from curl output
    $xmlobj = new SimpleXMLElement($response); 

	//array to store info
	$info = array();
	
	//straight ghetto old fashioned way of storing data...
	$info['profile_background_image_url'] 	= $xmlobj->profile_background_image_url;
	$info['created_at'] 					= $xmlobj->created_at;
	$info['followers_count'] 				= $xmlobj->followers_count;
	$info['description'] 					= $xmlobj->description;
	$info['statuses_count'] 				= $xmlobj->statuses_count;
	$info['friends_count'] 					= $xmlobj->friends_count;
	$info['url'] 							= $xmlobj->url;
	$info['profile_image_url'] 				= $xmlobj->profile_image_url;
	$info['favourites_count'] 				= $xmlobj->favourites_count;
	$info['location'] 						= $xmlobj->location;
	$info['screen_name'] 					= $xmlobj->screen_name;
	$info['geo_enabled'] 					= $xmlobj->geo_enabled;
	$info['time_zone']						= $xmlobj->time_zone;
	$info['verified']						= $xmlobj->verified;
	$info['name']							= $xmlobj->name;
	$info['id']								= $xmlobj->id;
	$info['lang']							= $xmlobj->lang;
	
	return $info;
}

/*
This function will tweet a given message to all users returned in search
query for search term and tweet to them.
*/
function sendTweets($search_term){
	//username/pass db credentials
	include('/home/51949/users/.home/domains/z2.zootzy.com/html/ztbot/config/config.inc.php');

	//Date/Time of execution
	$today = date("F j, Y, g:i a");

	//base url for posting with curl
	$url = 'http://twitter.com/statuses/update.xml';
	
	//Call this function in scrape.php, will return array
	$posts = getTwitterSearchFeedByJSON($search_term);
	$user_count = 0;

	echo "<h1>Twitter bot: <strong>$bot_username</strong></h1>";
	echo "<h2>Tweet date/time: <strong>$today</strong></h2>";

	for($i = 0; $i < 10; $i++) {	
		//grab user, post from array, then increment user counter
		$r_user = $posts[$user_count][0];
		$r_text = $posts[$user_count][1];
		$user_count++;

		//concatenate message to send
		$quo = getQuote();
		$msg = "@$r_user $quo";

		if($bot_username != $r_user){
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $url);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, 1);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$msg");
			curl_setopt($curl_handle, CURLOPT_USERPWD, "$bot_username:$bot_password");
			$buffer = curl_exec($curl_handle);
			curl_close($curl_handle);
	
			if(empty($buffer)){
				echo "Failure: tweet unsuccessfully posted<br />";
			} else {
				echo "Your tweet has been submitted: <strong>$msg</strong> in response to: <strong>$r_text</strong><br />";
			}
		} else {
			echo "Oooops, almost just tweeted myself, nope I caught it!<br />";
		}
	}
	echo "<br />Searching and tweeting for: <strong>$search_term</strong><br />";
	echo hourly_hits_left();
}
