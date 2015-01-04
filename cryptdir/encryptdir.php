#!/usr/bin/env php
<?php
	/**
		Program: encryptdir.php
		Author: Brian Hewitt
		Date: December 28, 2014

		encryptdir.php takes as input the name of a directory. It will 
		check if the folder exists, and if so, will create a tarball of it.
		Once this is done it will use gpg2 to encrypt the resulting tarball.
		change the -r value on line 31 to your own GPG ID to encrypt files for yourself.
	*/
	$armor = 0;
	foreach($argv as $arg)
	{
		if(strtolower($arg) == "--armor")
			$armor = 1;
		else if(file_exists($arg))
			$folder = $arg;
		else if(strtolower($arg) == "--help")
			help();

	}
	// Make sure user gave us a folder
	if($argc < 2)
		help();

	$writeto = $folder.".tar.gz";

	// Since we cant encrypt a directory directly, we tar it first, and encrypt the resulting tarball
	$command = "tar -cvzf ".$writeto." ".$folder;
	
	// Ensure the input folder exists
	if(file_exists($folder))
	{
		// Execute $command
		`$command`;

		// Next we want to encrypt our file, change the name of the recipient to suit your purposes
		$command = ($armor) ? "gpg2 --armor --encrypt -r \"Brian Hewitt\" ".$writeto : "gpg2 --encrypt -r \"Brian Hewitt\" ".$writeto;

		// Check to make sure our tarball exists
		if(file_exists($writeto))
		{
			// Execute $command
			`$command`;

			// If our encrypted file exists we want to delete the tarball, leaving us with just the original Input directory, and the resulting encrypted file.
			if(file_exists($writeto.".gpg") || file_exists($writeto.".asc"))
			{
				print "Successfully encrypted directory!\n";
				print "Deleting $writeto\n";
				echo exec("rm ".$writeto);
			}
			else
				print "Failed to encrypt directory!\n";
		}
		else
			print "Failed to tar directory!\n";
	}
	else
		print "Failed to locate folder to encrypt: ($folder). Please verify it exists.\n";

function help()
{
	die("invalid parameters, usage: php ".$argv[0]." [foldername]\n");

	echo "options:";
	echo "\t--armor - Writes .gpg file in ascii text instead of binary\n";
}
?>