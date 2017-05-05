<?php 
session_save_path("sessions");
session_start();
//ini_set('display_errors', 1);
require_once('twitter_lib/TwitterAPIExchange.php');



//Code to connect to twitter from the example in the API
$settings = array(
    'oauth_access_token' => "700798112294522880-W47RiQOdT4kajm6dFmu4zZp5l3Hc5K4",
    'oauth_access_token_secret' => "3szkF9tKeA2PFdxlRx7bBX5CFXPQYqocT5RSwAuB2r6Cs",
    'consumer_key' => "KQaxFaJMRq4y0IfeNDdk61SrK",
    'consumer_secret' => "PMBAqAksqIRNfpEek1IA9JrxiPn7wuxUbyfrQJ1gKqkfwRqeoA"
);
 
 
$twitter = new TwitterAPIExchange($settings);



//Declare an Array for the words in the string
//$_SESSION['wordCountArray'];
//The search ID that gets assigned if a cloud is posted
$searchID;
	
$dbconn = pg_connect("host=cs.utm.utoronto.ca dbname=khanwal1 user=khanwal1 password=1901501")
   or die('Could not connect: ' . pg_last_error());


	

function computeWordCloud($array){

$wordcloud = '';

foreach($array as $word=>$count){
	$color = '';
	//compute font_size using $count
	$fontsize = 20*$count;
	$color = "rgb(".(($count*254)%255).",".(($count*50)%255).",".(($count*75)%255).")";
	/*
	if($count%3 == 0){
	$color = "RED";}
	else if($count%3 ==1){
	$color = "BLUE";}
	else{
	$color = "GREEN";}
	*/
$wordcloud = $wordcloud.'<span style="display: inline-block; margin: 2px; font-size:'.
					$fontsize.'; color:'.$color.'">'.$word.'</span>';

}

return $wordcloud;
}

//Function which generates and shows the word Cloud
function showKeyWordCloud(){
		
global $twitter;

/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/
$url = 'https://api.twitter.com/1.1/search/tweets.json';

$getfield = '?q='.$_SESSION['searchWord'].'&result_type=recent';
$requestMethod = 'GET';
$tweets = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();

//echo $tweets;
$obj = json_decode($tweets);
$textAll = '';
$textUnfiltered = '';
//print_r ($obj->{'statuses'}); 
foreach($obj->{'statuses'} as $item){
	$tweetText = $item->text;
	//Remove Hyperlinks
	$tweetText = preg_replace("#http(s*)://t(.*)[\s]*#", " ", $tweetText);
	//Remove all speical symbols, only keep text and numbers
	$tweetText = preg_replace("#[^a-zA-Z0-9\s{1}]#", "", $tweetText);
	//Remove elipses, and RT
	$tweetText = str_replace("[&hellip]", "", $tweetText);
	//Remove all hashtags
	$tweetText = str_replace("#", "", $tweetText);
	//Remove all "RT"
	$tweetText = str_replace("RT", "", $tweetText);
	
	$textUnfiltered = $textUnfiltered.$item->text;
	$textAll = $textAll.$tweetText; 
	
	//echo $item->user->screen_name.":::".$item->text."<br>";
}
	$_SESSION['wordCountArray'] = (array_count_values(str_word_count($textAll,1)));

	return computeWordCloud($_SESSION['wordCountArray']);
		
}

//Function which generates and shows the word Cloud
function showUserTweetsCloud(){

global  $twitter;

/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';


$getfield = '?screen_name='.$_SESSION['searchWord'];
$requestMethod = 'GET';
$tweets = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();

//Get Associative Array from json returned result
$tweets = json_decode($tweets, true);	
$textAll = '';


foreach($tweets as $item){
	
	
	$tweetText = $item["text"];
	//Remove Hyperlinks
	$tweetText = preg_replace("#http(s*)://t(.*)[\s]*#", " ", $tweetText);
	//Remove all speical symbols, only keep text and numbers
	$tweetText = preg_replace("#[^a-zA-Z0-9\s{1}]#", "", $tweetText);
	//Remove elipses, and RT
	$tweetText = str_replace("[&hellip]", "", $tweetText);
	//Remove all hashtags
	$tweetText = str_replace("#", "", $tweetText);
	//Remove all "RT"
	$tweetText = str_replace("RT", "", $tweetText);
	
	$textAll = $textAll.$tweetText; 
		
}
	$_SESSION['wordCountArray'] = (array_count_values(str_word_count($textAll,1)));

	return computeWordCloud($_SESSION['wordCountArray']);
		
}



//Function which is used to save a cloud into the database should a 
//User wish to share its contents
function postCloud(){	

global $dbconn;
$userName = $_SESSION['user'];	
$searchWord = $_SESSION['searchWord'];


//Save the search into the database storing user searches
$query = "INSERT INTO wordSearch (searchedWord, username) values ($1, $2)";
$result = pg_prepare($dbconn, "insertWordSearchQuery", $query);
$result = pg_execute($dbconn, "insertWordSearchQuery", array($searchWord, $userName));
//storeCloud();


//Get the newly created search ID
$query = "SELECT id FROM wordSearch WHERE searchedWord = $1 AND username = $2";
$result = pg_prepare($dbconn, "getSearchID", $query);
$result = pg_execute($dbconn, "getSearchID", array($searchWord, $userName));

$row = pg_fetch_row($result);
$searchID = $row[0];

$wordCloud = $_SESSION['wordCloud'];	

$query = "INSERT INTO searchResults (searchID, word_cloud) values ($1, $2)";
$result = pg_prepare($dbconn, "saveSearchQuery", $query);
$result = pg_execute($dbconn, "saveSearchQuery", array(intval($searchID), $wordCloud));

}

function likeCloud(){
	
global $dbconn;

//retreive latest 10 wordclouds and their likes and authers
$query = "UPDATE searchResults set likes = likes + 1 WHERE searchID = $1";
$result = pg_prepare($dbconn, "updateLikes", $query);
$result = pg_execute($dbconn, "updateLikes", array(intval($_SESSION['searchID'])));

return true;
 
}
		


	
?>
