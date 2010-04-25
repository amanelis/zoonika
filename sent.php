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
$search = 'sex';
$result = 10;
$search_output = twitter_search($search, $result);

foreach ($search_output as $term){ 
	sentimentAnalysis_1($term[tweet], $search);
}


?>
