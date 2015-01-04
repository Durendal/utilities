==============
Cryptdir
==============

## Setup:

	Run these commands:
		chmod +x encryptdir.php
		chmod +x decryptdir.php
		sudo ln -s /path/to/encryptdir.php /usr/bin/encryptdir
		sudo ln -s /path/to/decryptdir.php /usr/bin/decryptdir

	Adjust these values as you see fit, the preceding commands will create links 
	to the scripts available through the $PATH variable, making the use of the 
	scripts as simple as the following


## Usage:
	
	encryptdir [Folder name]
	decryptdir [filename]

## Options:
	--armor - writes the encrypted data as an ASCII text file instead of binary

I got sick of having to tar folders, then run through gpg2, then delete the tarball
so I just wrote this script to handle most of the dirty work for me. Included is a 
complementary script for decrypting. This program assumes you have already gone through
the process of setting up your own PGP key. It's little more than a macro to save you a
few keystrokes but hopefully someone else might find it useful. I'll likely write a similar
script in python at some point down the line.

Just change the recipient address on line 31 to your own and you'll be able to start
encrypting/decrypting.

Enjoy!


