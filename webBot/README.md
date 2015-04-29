======
webBot
======

## A web scraper written in PHP
webBot.php aims to simplify the use of cURL with php. At the moment it only handles GET and POST HTTP requests but I may add more to it as time and interest permits. It should work with HTTP and SOCKS proxies, the default behaviour is to use HTTP, to enable SOCKS proxies you must either declare it as the second parameter when instantiating the bot as in the first example below, or you must set it as the third parameter using the setProxy() method.

An example of using it with tor:

	$bot = new webBot("127.0.0.1:9050", "SOCKS");
	$page = $bot->requestGET("http://zqktlwi4fecvo6ri.onion/wiki/index.php/Main_Page");
	file_put_contents("index.html", $page);
	// index.html contains the html of the page
		
if you then ran setProxy() with no parameters it would clear the proxy settings and the same request would fail:

	$bot.setProxy();
	$page = $bot->requestGET("http://zqktlwi4fecvo6ri.onion/wiki/index.php/Main_Page");
	file_put_contents("index.html", $page);
	// index.html is an empty file

by default a random User-Agent is selected, this behaviour can be overridden by explicitly calling the setAgent() function and sending it the value you want:

	$bot.setAgent("myBot user-agent");

POST parameters should be sent as an array through generatePOSTData() which will ensure they are urlencoded and properly formatted:

	$pdata = array("username" => "Durendal", "password" => "abc123", "submit" => "true");
	$result = $bot.requestPOST("http://www.example.com/login.php", $bot.generatePOSTData($pdata));
	if(stristr($result, "Login Successful"))
		print "Successfully logged in\n";
	else
		print "Failed to log in\n";

This class also comes packaged with a number of parsing routines written by Mike Schrenk for his book Webbots, Spiders and Screenscrapers that I have found extremely useful in the past. 

Example:

	require_once 'webBot.php';
	$bot = new webBot();
	$subreddit = ($argc > 1) ? $argv[1] : 'talesfromtechsupport';
	$page = $bot->requestGET("http://www.reddit.com/r/$subreddit/.rss");
	$posts = $bot->parse_array($page, "<item>", "</item>");
	$titles = array();
	$links = array();
	for($i = 0; $i < count($posts); $i++)
	{
		$titles[$i] = $bot->return_between($posts[$i], "<title>", "</title>", 1);
		$links[$i] = $bot->return_between($posts[$i], "<link>", "</link>", 1);
		print "Title #$i: ".$titles[$i]."\n";
		print "Link #$i: ".$links[$i]."\n";
	}
	

This script takes an optional parameter of a subreddit name the default is 'talesfromtechsupport' 
It will scrape the RSS feed and post the front page of posts. This should illustrate
the basic principles of using the bot. All parsing methods were adapted from original
code written by Mike Schrenk in his book 'Webbots spiders and Screenscrapers' 
