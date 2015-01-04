<?php

	class webBot
	{
		private $agent;
		private $timeout;
		private $cook;
		private $curl;
		private $proxy;
		private $credentials;
		private $EXCL;
		private $INCL;
		private $BEFORE;
		private $AFTER;
	

		public function __construct($proxy=null, $credentials=null)
		{
			$this->proxy = $proxy;
			$this->timeout = 30;
			$this->agent = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us'
			.' AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16';
			$this->cook = 'cookies.txt';
			$this->curl = $this->setupCURL($this->proxy);
			$this->EXCL = true;
			$this->INCL = false;
			$this->BEFORE = true;
			$this->AFTER = false;
			$this->credentials = $credentials;
		}

		private function setupCURL($py)
		{
			$ck = $this->cook;
			$creds = $this->credentials;
			$ch = curl_init();
			if($py)
			{
				curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
				curl_setopt($ch, CURLOPT_PROXY, $py);
				if($creds)
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $creds);
				print("Using Proxy: $py\n");
			}
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $ck);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $ck);

			return $ch;
		}

		public function get_contents($url, $ref='')
		{
			if($ref == '')
				$ref = $url;
			$ch = $this->curl;
			$hd = array("Connection: Keep-alive",
						"Keep-alive: 300",
						"Expect:",
						"Referer: $ref",
						"User-Agent: {$this->agent}"
						);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $hd);
			$x = curl_exec($ch);

			return $x;
		}

		public function post_contents($purl, $pdata, $ref='')
		{
			if($ref == '')
				$ref = $purl;
			$ch = $this->curl;
			$hd = array("Connection: Keep-alive",
						"Keep-alive: 300",
						"Expect:",
						"Referer: $ref",
						"User-Agent: {$this->agent}"
						);
			curl_setopt($ch, CURLOPT_URL, $purl);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $pdata);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $hd);
			$x = curl_exec($ch);
			curl_setopt($ch, CURLOPT_POST, 0);

			return $x;
		}

		public function split_string($string, $delineator, $desired, $type)
	    {
	        // Case insensitive parse, convert string and delineator to lower case
	        $lc_str = strtolower($string);
	        $marker = strtolower($delineator);
	        // Return text $this->BEFORE the delineator
	        if($desired == $this->BEFORE)
	        {
				if($type == $this->EXCL) // Return text ESCL of the delineator
	    			$split_here = strpos($lc_str, $marker);
				else // Return text $this->INCL of the delineator
	    			$split_here = strpos($lc_str, $marker)+strlen($marker);

				$parsed_string = substr($string, 0, $split_here);
	        }
	        // Return text $this->AFTER the delineator
	        else
	        {
				if($type==$this->EXCL) // Return text ESCL of the delineator
	    			$split_here = strpos($lc_str, $marker) + strlen($marker);
				else // Return text $this->INCL of the delineator
	    			$split_here = strpos($lc_str, $marker) ;

				$parsed_string = substr($string, $split_here, strlen($string));
	        }
	        return $parsed_string;
	    }

	    public function return_between($string, $start, $stop, $type)
	    {
	        $temp = $this->split_string($string, $start, $this->AFTER, $type);
	        return $this->split_string($temp, $stop, $this->BEFORE, $type);
	    }

	    public function parse_array($string, $beg_tag, $close_tag)
	    {
	        preg_match_all("($beg_tag(.*)$close_tag)siU", $string, $matching_data);
	        return $matching_data[0];
	    }

	    public function get_attribute($tag, $attribute)
	    {
	        // Use Tidy library to 'clean' input
	        $cleaned_html = tidy_html($tag);
	        // Remove all line feeds from the string
	        $cleaned_html = str_replace("\r", "", $cleaned_html);
	        $cleaned_html = str_replace("\n", "", $cleaned_html);
	        // Use return_between() to find the properly quoted value for the attribute
	        return return_between($cleaned_html, strtoupper($attribute)."=\"", "\"", $this->EXCL);
	    }

		function remove($string, $open_tag, $close_tag)
	    {
		    # Get array of things that should be removed from the input string
		    $remove_array = parse_array($string, $open_tag, $close_tag);
		    
		    # Remove each occurrence of each array element from string;
		    for($xx=0; $xx<count($remove_array); $xx++)
		        $string = str_replace($remove_array, "", $string);
		    
		    return $string;
	    }
	    function tidy_html($input_string)
	    {
		    // Detect if Tidy is in configured
		    if( function_exists('tidy_get_release') )
		        {
		        # Tidy for PHP version 4
		        if(substr(phpversion(), 0, 1) == 4)
		            {
		            tidy_setopt('uppercase-attributes', TRUE);
		            tidy_setopt('wrap', 800);
		            tidy_parse_string($input_string);            
		            $cleaned_html = tidy_get_output();  
		            }
		        # Tidy for PHP version 5
		        if(substr(phpversion(), 0, 1) == 5)
		            {
		            $config = array(
		                           'uppercase-attributes' => true,
		                           'wrap'                 => 800);
		            $tidy = new tidy;
		            $tidy->parseString($input_string, $config, 'utf8');
		            $tidy->cleanRepair();
		            $cleaned_html  = tidy_get_output($tidy);  
		            }
		        }
		    else
		        {
		        # Tidy not configured for this computer
		        $cleaned_html = $input_string;
		        }
		    return $cleaned_html;
	    }

	    public function validateURL($url)
	    {    
	        $pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w]'
	        .'[-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]'
	        .'|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
	        return preg_match($pattern, $url);
	    }

	}