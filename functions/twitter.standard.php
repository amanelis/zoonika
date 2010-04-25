<?PHP


/*
 * FUNCTION: twitter_search
 * PURPOSE: to grab search results on a keywor from search.twitter.com
 *			these results are tweets with certain keywords
 * @PARAMS: 
 *		$search = "your search criteria you want to find on twitter"
 *		$results = "max results are 150, you can have any interger between that
 * @INCLUDES: n/a
 * @RETURN: returns a two dimensional array of [tweet result #] -> [user][tweet]
 */
function twitter_search($search, $results) {
	$base_url = "http://search.twitter.com/search.json?lang=en&q=".$search."&rpp=".$results."";
	
	$json = file_get_contents($base_url, true);
	$decode = json_decode($json, true);
	
	$posts = array();
	$user_count = 0;

	for($i = 0; $i < $results; $i++){
		$r_user = $decode[results][$i][from_user];
		$r_text = $decode[results][$i][text];
	
		$posts[$user_count][user] = $r_user;
		$posts[$user_count][tweet] = $r_text;
		$user_count++;	
	}
	return $posts;
}

/*
 * FUNCTION: twitter_trends
 * @PARAMS: n/a
 * @INCLUDES: n/a
 * @RETURN: returns a two dimensional array of [tweet result #] -> [user][tweet]
 */
function twitter_trends() {
	$jsonurl = "http://search.twitter.com/trends.json";
	
	$json = file_get_contents($jsonurl,0,null,null);
	$json_output = json_decode($json);
	
	$trends_array = array(array());
	$trends_count = 0;

	foreach($json_output->trends as $trend) {
    	$name = $trend->name;
    	$url = $trend->url;
    	
    	$trends_array[$trends_count][name] = $name;
    	$trends_array[$trends_count][url] = $url;
    	
    	$trends_count++;
	}
	
	return $trends_array;	
}

function twitter_trends_current() {
	$jsonurl = "http://search.twitter.com/trends/current.json";
	
	$json = file_get_contents($jsonurl,0,null,null);
	$json_output = json_decode($json);
	
	$trends_array = array();
	$trends_count = 0;

	foreach ($json_output->trends as $date => $val) {
		foreach ($val as $item) {
			$name = $item->name;
			$query = $item->query;

			$trends_array[$date][$trends_count][name]  = $name; 
			$trends_array[$date][$trends_count][query] = $query;
			$trends_count++;
		}
		$trends_count = 0;
	}
	return $trends_array;
}

function twitter_trends_daily($date = false) {
	if($date != false) {
		$date = date('Y-m-d');
	}

	$jsonurl = "http://search.twitter.com/trends/daily.json?date=$date";
	
	$json = file_get_contents($jsonurl,0,null,null);
	$json_output = json_decode($json);
	
	$trends_array = array();
	$trends_count = 0;

	foreach ($json_output->trends as $date => $val) {
		foreach ($val as $item) {
			$name = $item->name;
			$query = $item->query;

			$trends_array[$date][$trends_count][name]  = $name; 
			$trends_array[$date][$trends_count][query] = $query;
			$trends_count++;
		}
		$trends_count = 0;
	}
	return $trends_array;
}

function twitter_trends_weekly($date = false) {
	if($date != false) {
		$date = date('Y-m-d');
	}

	$jsonurl = "http://search.twitter.com/trends/weekly.json?date=$date";
	
	$json = file_get_contents($jsonurl,0,null,null);
	$json_output = json_decode($json);
	
	$trends_array = array();
	$trends_count = 0;

	foreach ($json_output->trends as $date => $val) {
		foreach ($val as $item) {
			$name = $item->name;
			$query = $item->query;

			$trends_array[$date][$trends_count][name]  = $name; 
			$trends_array[$date][$trends_count][query] = $query;
			$trends_count++;
		}
		$trends_count = 0;
	}
	return $trends_array;
}

function twitter_public_timeline() {
	$jsonurl = "http://api.twitter.com/1/statuses/public_timeline.json";
	
	$json = file_get_contents($jsonurl,0,null,null);
	$json_output = json_decode($json);
	
	printArray($json_output);
}
