#!/usr/bin/env php
<?php
	/**
		Program: decryptdir.php
		Author: Brian Hewitt
		Date: December 28, 2014

		decryptdir.php is the complementary script to encryptdir.php
		it takes a .gpg file of a compressed folder, decrypts it into 
		a tarball, then untars that into a folder. It will then remove
		the tarball.
	*/

	// Make sure user gave us a folder
	if($argc < 2)
		die("invalid parameters, usage: php ".$argv[0]." [filename]\n");

	// Set Input and Output file names
	$file = $argv[1];
	$writeto = substr($file, 0, strlen($file)-4);

	// First we want to decrypt our input file
	$command = "gpg2 -o $writeto --decrypt $file";

	// Ensure the input file exists
	if(file_exists($file))
	{
		// Execute $command
		`$command`;

		// Next we want to untar our decrypted tarball
		$command = "tar -xzvf ".$writeto;

		// Check to make sure our tarball exists
		if(file_exists($writeto))
		{
			// Execute $command
			`$command`;

			// If we untarred successfully we're golden. Delete the tarball to cleanup.			
			if(file_exists(substr($writeto, 0, strlen($writeto)-7)))
			{
				print "Successfully decrypted directory!\n";
				print "Deleting $writeto\n";
				echo exec("rm ".$writeto);
			}
			else
				print "Failed to untar file!\n";
		}
		else
			print "Failed to decrypt file!\n";
	}
	else
		print "Failed to locate file to decrypt: ($file). Please verify it exists.\n";

?>