<?php
	
	require_once 'webBot.php';

	$bot = new webBot();
	$baseurl = "http://www.cl.cam.ac.uk/projects/raspberrypi/tutorials/os/";
	$homepage = $bot->get_contents($baseurl."/index.html");

	$linkdata = $bot->return_between($homepage, "<div id=\"container\">", "<div id=\"pageBody\">", 1);
	$links = $bot->parse_array($linkdata, "<div><a href=\"", "\">");
	for($i = 0; $i < count($links); $i++)
	{
		$links[$i] = $bot->return_between($links[$i], "<div><a href=\"", "\">", 1);
		print "Downloading and Writing: ".$links[$i]."\n";
		$page = $bot->get_contents($baseurl.$links[$i]);
		file_put_contents("arm/".$links[$i], $page);
		sleep(1);
	}
	print "Done!\n";
?>