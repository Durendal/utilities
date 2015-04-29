<?php
	/*
			File: webBot.php
			Author: Durendal

			webBot.php aims to simplify the use of cURL with php. At the moment it only
			handles GET and POST HTTP requests but I may add more to it as time and
			interest permits. 

	*/

	class webBot
	{
		private $agent;			// User-Agent
		private $keepalive;		// KeepAlive value
		private $cookies;		// Cookie File
		private $ch;			// cURL Handler
		private $proxy;			// Proxy Address
		private $proxtype;		// Proxy Type
		private $credentials;	// Proxy Credentials
			

		public function __construct($proxy = null, $type = 'HTTP', $credentials = null, $cookies = 'cookies.txt')
		{
			
			$this->setCookie($cookies);
			$this->setupCURL();
			$this->setProxy($proxy, $credentials, $type);
			$this->keepalive = 300;
			$this->setRandomAgent();
			
		}

		/*
			setKeepAlive($keepalive)
				
				sets the Keep-Alive value to $keepalive
		*/
		public function setKeepAlive($keepalive)
		{
			if($keepalive > 0)
				$this->keepalive = $keepalive;
		}
		/*
			getKeepAlive()
		
				returns the current Keep-Alive value
		*/
		public function getKeepAlive()
		{
			return $this->keepalive;
		}

		/*
			setProxy($py, $creds, $type)

				will set the proxy using the specified credentials and type,
				by default it assumes an HTTP proxy with no credentials. To 
				use a SOCKS proxy simply pass the string 'SOCKS' as the third 
				parameter. If no parameters are sent, it will remove any proxy
				settings and begin routing in the clear.
		*/
		public function setProxy($py = null, $creds = null, $type = 'HTTP')
		{
			$this->proxy = $py;
			$this->credentials = $creds;
			$this->proxtype = $type;

			if($py)
			{
				// Check for SOCKS or HTTP Proxy
				if(strtoupper($this->proxtype) == 'SOCKS')
					curl_setopt($this->ch, CURLOPT_PROXYTYPE, 7);
				else
					curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

				curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, 1);
				curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
				print "Using {$this->proxtype} Proxy: {$this->proxy} ";
				if($this->credentials)
				{
					print "Credentials: {$this->credentials}";
					curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $this->credentials);
				}
				print "\n";
			}
			// Disable Proxy Support if called with no parameters
			else
			{
				print "Disabling Proxy.\n";
				curl_setopt($this->ch, CURLOPT_PROXYTYPE, null);
				curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, 0);
				curl_setopt($this->ch, CURLOPT_PROXY, null);
				curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, null);
			}

		}

		/*
			getProxy()
			
				returns an array with the currently set proxy, credentials, and its type.
		*/
		public function getProxy()
		{
			return array('proxy' => $this->proxy, 'credentials' => $this->credentials, 'type' => $this->proxtype);
		}

		/*
			setCookie($cookie)
			
				sets the cookie file to $cookie and rebuilds the curl handler.
				note that if you already have an instance of the curlHandler 
				instantiated, you will need to rebuild it via rebuildHandler()
				for this to take effect
		*/
		public function setCookie($cookie)
		{
			$this->cookies = $cookie;
		}

		/*
			getCookie()
			
				returns the current file where cookies are stored
		*/
		public function getCookie()
		{
			return $this->cookies;
		}

		/*
			setAgent($agent)
			
				sets the User-Agent to $agent
		*/
		public function setAgent($agent='curlBot')
		{
			$this->agent = $agent;
		}

		/*
			getAgent()
			
				returns the currently set User-Agent
		*/

		public function getAgent()
		{
			return $this->agent;
		}

		/*
			setRandomAgent()
			
				sets the useragent at random to one from the list below
					
			List of user-agents from: https://techblog.willshouse.com/2012/01/03/most-common-user-agents/
		*/
		public function setRandomAgent()
		{
			$agents = array("Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/600.5.17 (KHTML, like Gecko) Version/8.0.5 Safari/600.5.17",
					"Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/600.4.10 (KHTML, like Gecko) Version/8.0.4 Safari/600.4.10",
					"Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0",
					"Mozilla/5.0 (Windows NT 6.3; WOW64; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.104 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko",
					"Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4",
					"Mozilla/5.0 (iPhone; CPU iPhone OS 8_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12D508 Safari/600.1.4",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/600.3.18 (KHTML, like Gecko) Version/8.0.3 Safari/600.3.18",
					"Mozilla/5.0 (Windows NT 6.3; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0",
					"Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko",
					"Mozilla/5.0 (Windows NT 6.1; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:36.0) Gecko/20100101 Firefox/36.0",
					"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36",
					"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36",
					"Mozilla/5.0 (iPad; CPU OS 8_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12D508 Safari/600.1.4",
					"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:36.0) Gecko/20100101 Firefox/36.0",
					"Mozilla/5.0 (iPad; CPU OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F69 Safari/600.1.4",
					"Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.104 Safari/537.36",
					"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/41.0.2272.76 Chrome/41.0.2272.76 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/600.4.10 (KHTML, like Gecko) Version/7.1.4 Safari/537.85.13",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/600.5.17 (KHTML, like Gecko) Version/7.1.5 Safari/537.85.14",
					"Mozilla/5.0 (iPhone; CPU iPhone OS 7_1_2 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Version/7.0 Mobile/11D257 Safari/9537.53",
					"Mozilla/5.0 (Windows NT 6.1; rv:36.0) Gecko/20100101 Firefox/36.0",
					"Mozilla/5.0 (Windows NT 5.1; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:36.0) Gecko/20100101 Firefox/36.0",
					"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)",
					"Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B466 Safari/600.1.4",
					"Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko",
					"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)",
					"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36",
					"Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20100101 Firefox/31.0",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.78.2 (KHTML, like Gecko) Version/6.1.6 Safari/537.78.2",
					"Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0",
					"Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B440 Safari/600.1.4",
					"Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0",
					"Mozilla/5.0 (X11; Linux x86_64; rv:37.0) Gecko/20100101 Firefox/37.0",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/600.3.18 (KHTML, like Gecko) Version/8.0.4 Safari/600.4.10",
					"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (iPhone; CPU iPhone OS 8_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B411 Safari/600.1.4",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10) AppleWebKit/600.1.25 (KHTML, like Gecko) Version/8.0 Safari/600.1.25",
					"Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.59.10 (KHTML, like Gecko) Version/5.1.9 Safari/534.59.10",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/600.3.18 (KHTML, like Gecko) Version/7.1.3 Safari/537.85.12",
					"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.78.2 (KHTML, like Gecko) Version/7.0.6 Safari/537.78.2",
					"Mozilla/5.0 (Windows NT 5.1; rv:36.0) Gecko/20100101 Firefox/36.0",
					"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36 OPR/28.0.1750.51",
					"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36");
			
			$this->setAgent($agents[rand(0,count($agents)-1)]);

		}

		/*
			setupCURL()
			
				Creates and returns a new generic cURL handler
		*/
		private function setupCURL()
		{
			
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($this->ch, CURLOPT_HEADER, 1);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookies);
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookies);
		}

		/*
			requestGET($url, $ref)
			
				makes a GET based HTTP Request to the url specified in $url using the referer specified in $ref
				if no $ref is specified it will use the $url
		*/

		public function requestGET($url, $ref='')
		{
			if($ref == '')
				$ref = $url;
			$hd = array("Connection: Keep-alive",
						"Keep-alive: {$this->keepalive}",
						"Expect:",
						"Referer: $ref",
						"User-Agent: {$this->agent}"
						);
			
			curl_setopt($this->ch, CURLOPT_URL, $url);
			curl_setopt($this->ch, CURLOPT_POST, 0);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $hd);
			$x = curl_exec($this->ch);

			return $x;
		}


		/*
			requestGET($url, $pdata, $ref)
			
				makes a POST based HTTP Request to the url specified in $url using the referer specified in $ref
				and the parameters specified in $pdata. If no $ref is specified it will use the $url
		*/

		public function requestPOST($purl, $pdata, $ref='')
		{
			if($ref == '')
				$ref = $purl;
			$hd = array("Connection: Keep-alive",
						"Keep-alive: {$this->keepalive}",
						"Expect:",
						"Referer: $ref",
						"User-Agent: {$this->agent}"
						);
			
			curl_setopt($this->ch, CURLOPT_URL, $purl);
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $pdata);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $hd);
			
			$x = curl_exec($this->ch);

			curl_setopt($this->ch, CURLOPT_POST, 0);

			return $x;
		}

		/*
			generatePOSTData($data)
			
				generates a urlencoded string from an associative array of POST parameters
		*/
		public function generatePOSTData($data)
		{
			$params = '';

			foreach($data as $key => $val)
				$params .= urlencode($key) . '=' . urlencode($val) . '&';
			
			// trim trailing &
			return substr($params, 0, -1);
		}

		/*
			rebuildHandler()
			
				rebuilds the cURL Handler for the next request
		*/
		public function rebuildHandler()
		{
			$this->setupCURL();
			$this->setProxy($this->proxy, $this->credentials, $this->proxtype);
			$this->setRandomAgent();
		}

		/*
			Parsing subroutines adapted from Mike Schrenks LIB_PARSE.php in Webbots spiders and screenscrapers http://webbotsspidersscreenscrapers.com/
		*/

		public function split_string($string, $delineator, $desired, $type)
		{
			// Case insensitive parse, convert string and delineator to lower case
			$lc_str = strtolower($string);
			$marker = strtolower($delineator);
			// Return text true the delineator
			if($desired == true)
			{
				if($type == true) // Return text ESCL of the delineator
					$split_here = strpos($lc_str, $marker);
				else // Return text false of the delineator
					$split_here = strpos($lc_str, $marker)+strlen($marker);

				$parsed_string = substr($string, 0, $split_here);
			}
			// Return text false the delineator
			else
			{
				if($type==true) // Return text ESCL of the delineator
					$split_here = strpos($lc_str, $marker) + strlen($marker);
				else // Return text false of the delineator
					$split_here = strpos($lc_str, $marker) ;

				$parsed_string = substr($string, $split_here, strlen($string));
			}
			return $parsed_string;
		}

		public function return_between($string, $start, $stop, $type)
		{
			$temp = $this->split_string($string, $start, false, $type);
			return $this->split_string($temp, $stop, true, $type);
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
			$cleaned_html = str_replace(array("\r\n", "\n", "\r"), "", $cleaned_html);
			
			// Use return_between() to find the properly quoted value for the attribute
			return return_between($cleaned_html, strtoupper($attribute)."=\"", "\"", true);
		}

		public function remove($string, $open_tag, $close_tag)
		{
			# Get array of things that should be removed from the input string
			$remove_array = parse_array($string, $open_tag, $close_tag);
			
			# Remove each occurrence of each array element from string;
			for($xx=0; $xx<count($remove_array); $xx++)
				$string = str_replace($remove_array, "", $string);
			
			return $string;
		}
		public function tidy_html($input_string)
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
								   'wrap'				 => 800);
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