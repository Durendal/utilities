<?php

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
	print $argc."\n";
	var_dump($argv);
	//print $page;

?>