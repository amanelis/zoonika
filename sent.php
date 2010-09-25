<?PHP
//////////MUST INCLUDE TO WORK/////////
/*************************************/
//include config file
require_once("config/config.inc.php");

//include the dom class
require_once("classes/dom.class.php");

//include twitter api class
require_once("classes/twit.class.php");

//include the twitter API
require_once("functions/twitter.standard.php");

//include the scrapper class
require_once('functions/scrape.php');

//include the sentiment analysis class
require_once('functions/sentiment.php');
/*************************************/

/* MAIN PROGRAM */
$result = 100;
$search = htmlentities($_GET['q']);

if($search != NULL) {


	$search_output = twitter_search($search, $result);
	foreach ($search_output as $term){ 
		sentimentAnalysis_1($term[tweet], $term[user], $search);
	}


} else {
	echo "ERROR: you have entered a NULL search<br />";
}


?>
