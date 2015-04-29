<?php

	require_once 'webBot.php';

	$bot = new webBot();

	$subreddit = ($argc > 1) ? $argv[1] : 'talesfromtechsupport';

	$page = $bot->requestGET("http://www.reddit.com/r/$subreddit/.rss");

	$posts = $bot->parse_array($page, "<item>", "</item>");
	$titles = array();
	$links = array();

	for($i = 0; $i < count($posts); $i++)
	{
		$ii = $i+1;
		$titles[$i] = $bot->return_between($posts[$i], "<title>", "</title>", 1);
		$links[$i] = $bot->return_between($posts[$i], "<link>", "</link>", 1);
		print "Title #$ii: ".$titles[$i]."\n";
		print "Link #$ii: ".$links[$i]."\n";
	}
	
	$bot->setProxy("127.0.0.1:9050", null, "SOCKS");
	print "User-Agent: " . $bot->getAgent() . "\n";
	print "Scraping hidden index...\n";
	$page = $bot->requestGET("http://zqktlwi4fecvo6ri.onion/wiki/index.php/Main_Page");
	print "Done!\n";
	file_put_contents("index.html", $page);
?>
