======
webBot
======

## A web scraper written in PHP

Example:

	require_once 'webBot.php';
	$bot = new webBot();
	$subreddit = ($argc > 1) ? $argv[1] : 'talesfromtechsupport';
	$page = $bot->get_contents("http://www.reddit.com/r/$subreddit/.rss");
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
