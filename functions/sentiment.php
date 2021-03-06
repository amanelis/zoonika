<?PHP
/***********microtime_float()***********
purpose: starts a time counter to calculate
time spent processing one sentiment analysis
***************************************/
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/*******printArray($input_array)***********
purpose: takes a single [] array as input 
and simply prints it formatted nicely
***************************************/
function printArray($input_array) {
	if(empty($input_array)){
		echo 'FUNCTION ERROR: printArray($input_array), param array looks to be empty or null.<br />';
		exit(-1);
	}
	
	echo "<pre>";
	print_r($input_array);
	echo "</pre>";
}

/*******fileToArray($file)***********
purpose: takes in a file path param,
parses the file and puts each line in
array as one entry, i.e., [0] = 'word'..
***************************************/
function fileToArray($file) {
	if(empty($file)){
		echo 'FUNCTION ERROR: fileToArray($file), param file looks to be empty or null.<br />';
		exit(-1);
	}
	$index = 0;
	$lines = file($file);
	$file_array = array();

	foreach ($lines as $line_num => $line) {
    		$clean = trim($line);
    		$file_array[$index] = $clean;
    		$index++;
	}
	return $file_array;
}

/*******fileToTwoDArray($file)***********
purpose: takes in a file path param,
parses the file and puts each line in
array as one entry, and stores a word count
i.e., [0] => Array
        (
            [word] => abrasive
            [count] => 0
        )
***************************************/
function fileToTwoDArray($file) {
	if(empty($file)){
		echo 'FUNCTION ERROR: fileToTwoDArray($file), param file looks to be empty or null.<br />';
		exit(-1);
	}
	$index = 0;
	$lines = file($file);
	$file_array = array(array());
	
	foreach ($lines as $line_num => $line) {
		$clean = trim($line);
    		$file_array[$index]['word']  = $clean;
    		$file_array[$index]['count'] = 0;
    		$index++;
	}
	return $file_array;
}

function positiveAdj() {
	$positive_adjective_array = fileToTwoDArray('diction/adj_pos.txt');
	sort($positive_adjective_array);
	return $positive_adjective_array;
}

function negativeAdj() {
	$negative_adjective_array = fileToTwoDArray('diction/adj_neg.txt');
	sort($negative_adjective_array);
	return $negative_adjective_array;
}

/*
Function takes a sentence, or large body
of text and tokenizes the input.
@return: returns a single dimension array of tokens
@params: string, sentence, char, paragraph
@comments: maybe use explode() to parse out data
*/
function simpleTok($sentence){
	if(empty($sentence)){
		echo 'FUNCTION ERROR: parseSentence($sentence), param sentence looks to be empty.<br />';
		exit(-1);
	}
	
	//use a copy just in case need to reference original sentence
	$use_sentence = $sentence;
	trim($use_sentence);
									        
	//move to lower case
	$use_sentence = strtolower($use_sentence);

	//for now we want to delimit all other tokens that are not included in alphabet
	$delimiters = " ";
	$tokenize_chars = strtok($use_sentence, $delimiters);
	
	//setup array and a ndx
	$tokenized_sentence_array = array();
	$index = 0;

	//take out any non alphabetical characters such as :!#~`...
	while($tokenize_chars !== false) {
		$tokenized_sentence_array[$index] = $tokenize_chars;
		$tokenize_chars = strtok($delimiters);
		$index++;
	}

	return $tokenized_sentence_array;
}

/*
Function takes a sentence, or large body
of text and tokenizes the input.
@return: returns a single dimension array of tokens
@params: string, sentence, char, paragraph
@comments: maybe use explode() to parse out data
*/
function tokenizeSentence($sentence){
	if(empty($sentence)){
		echo 'FUNCTION ERROR: parseSentence($sentence), param sentence looks to be empty.<br />';
		exit(-1);
	}
	
	//use a copy just in case need to reference original sentence
	$use_sentence = $sentence;
	trim($use_sentence);
	
	//move to lower case
	$use_sentence = strtolower($use_sentence);
	
	//for now we want to delimit all other tokens that are not included in alphabet
	$delimiters = "`~!@#$%^&*()_+=-{}|[]\\';:\"/.,<>?";
	$tokenize_chars = strtok($use_sentence, $delimiters);
	
	//setup array and a ndx
	$tokenized_sentence_array = array();
	$index = 0;

	//take out any non alphabetical characters such as :!#~`...
	while($tokenize_chars !== false) {
		$tokenized_sentence_array[$index] = $tokenize_chars;
		$tokenize_chars = strtok($delimiters);
		$index++;
	}

	//setup a string variable to hold new tokens
	$string = "";
	foreach($tokenized_sentence_array as $token) {
		$string .= $token;
	}
	
	trim($string);
	$space_delimiter = " ";
	$tokenize_space = strtok($string, $space_delimiter);
	
	$tokenize_space_array = array();
	$ndx = 0;
	
	while($tokenize_space !== false) {
		$tokenize_space_array[$ndx] = $tokenize_space;
		$tokenize_space = strtok($space_delimiter);
		$ndx++;
	}
	return $tokenize_space_array;
}

/*
Function takes in a sentence and does 
sentiment analysis on it.
@return: sentiment analysis report
@params: pass in a sentence, it will tokenize, pass in a search term
*/
function sentimentAnalysis_1($sentence, $user, $search) {
	if(empty($sentence)){
		echo 'FUNCTION ERROR: sentimentAnalysis_1($sentence, $user, $search), param array looks to be empty -> $sentence<br />';
		exit(-1);
	}
	if(empty($user)) {
		echo 'FUNCTION ERROR: sentimentAnalysis_1($sentence, $user, $search), param string user looks to be empty -> $user<br />';
		exit(-1);
	}
	if(empty($search)){
		echo 'FUNCTION ERROR: sentimentAnalysis_1($sentence, $search), param string looks to be empty -> $search<br />';
		exit(-1);
	}
	
	//Start execution timer
	$time_start = microtime_float();
	
	//Get pos/neg adj arrays from dictionary files
	$pos_arr_adj = positiveAdj();
	$neg_arr_adj = negativeAdj();
	
	//Compute array size
	$pos_sz = (count($pos_arr_adj) - 1);
	$neg_sz = (count($neg_arr_adj) - 1);
	
	//make a copy of tokenized array passed
	$ta_copy_1 = tokenizeSentence($sentence);
	$ta_sz = (count($ta_copy_1) - 1);
	
	//Set indexing variables
	$index = 0;
	$pos_cnt = 0;
	$neg_cnt = 0;
	$neu_cnt = 0;
	$string_count = 0;
	
	$complex_pos = 0;
	$complex_neg = 0;
	
	//Counter arrays for words
	$pos_found = array();
	$neg_found = array();
	
	foreach($ta_copy_1 as $ta_word) {
		foreach($pos_arr_adj as $pos_w => $pos_word) {
			if($ta_word == $pos_word['word']){
				$pos_found[$pos_cnt] = $pos_word['word'];
				$complex_pos = 1;
				$pos_cnt++;
			} 
			$string_count++;
		}
		
		foreach($neg_arr_adj as $neg_w => $neg_word) {
			if($ta_word == $neg_word['word']){
				$neg_found[$neg_cnt] = $neg_word['word'];
				$complex_neg = 1;
				$neg_cnt++;
			}
			$string_count++;
		}
		
		if(($complex_pos == 0) && ($complex_neg == 0)){
			$neu_cnt = 1;
		} else {
			$neu_cnt = 0;
		}
		
		$index++;
	}
	
	/*
	function posWordsFound() {
		if(!empty($pos_found)){
			echo "Word: <strong>".$pos_found[0]."</strong>";
		}
	}
	
	function negWordsFound() {
		if(!empty($neg_found)){
			echo "Word: <strong>".$neg_found[0]."</strong>";
		}
	}
	*/
	
	if($pos_cnt != 0) {
		//$sentiment_positive = round((($string_count / $ta_sz) / ($pos_cnt)), 2);
		$sentiment_positive = round(($ta_sz / $pos_cnt), 2);
	} else {
		$sentiment_positive = 0;
	}

	if($neg_cnt !=0) {
		$sentiment_negative = round(($ta_sz / $neg_cnt), 2);
	} else {
		$sentiment_negative = 0;
	}
	
	//Return the twitter API calls left
	$limit = hourly_hits_left();
	
	$time_end = microtime_float();
	$time = $time_end - $time_start;
	$total_time = round($time, 5);
	
	echo "|******************START SENTIMENT ANALYSIS REPORT******************|<br />";
	echo "Scrapping twitter for: <strong><font color=\"blue\">$search</font></strong><br />";
	echo "Original sentence: <strong>$sentence</strong><br />";
	echo "Tokenized sentence: ";
	for($r = 0; $r < ($ta_sz + 1); $r++)
		echo "<strong>".$ta_copy_1[$r]."</strong> ";
	echo "<br />";
	echo "Tweet user: <a href=\"http://twitter.com/$user\" target=\"_blank\">$user</a><br />";
	echo "Pos: <strong><font color='green'>$pos_cnt</font></strong><br />"; 
	echo "Neg: <strong><font color='red'>$neg_cnt</font></strong><br />";
	echo "Neu: <strong>$neu_cnt</strong><br />";
	if($pos_cnt > $neg_cnt) {
		echo "Sentiment: <strong><font color='green'>Positive</font></strong><br />";
		echo "Sentiment: <strong>$sentiment_positive</strong><br />";
	} else if ($neg_cnt > $pos_cnt) {
        echo "Sentiment: <strong><font color='red'>Negative</font></strong><br />";
		echo "Sentiment: <strong>$sentiment_negative</strong><br />";
	} else {
		echo "Sentiment: <strong>0.0</strong><br />";
	}
	echo "Total Strings Analyzed: <strong>$string_count</strong><br />";
	echo "Total execution time <strong>$total_time</strong> seconds<br />";
	echo $limit;
	echo "|******************END SENTIMENT ANALYSIS REPORT********************|<br /><br /><br />";
}
