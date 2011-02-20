<?php

/* 
   This file is provided to you under the Apache License,
   Version 2.0 (the "License"); you may not use this file
   except in compliance with the License.  You may obtain
   a copy of the License at
   
   http://www.apache.org/licenses/LICENSE-2.0
   
   Unless required by applicable law or agreed to in writing,
   software distributed under the License is distributed on an
   "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
   KIND, either express or implied.  See the License for the
   specific language governing permissions and limitations
   under the License.    
*/

require_once('Ripple/Document.class.php');

/**
 * Ripple maps objects in PHP to objects in Riak.
 *
 * See /test/suites/ for example usage.
 * 
 * Inspired by Ripple (Sean Cribbs et al)
 * https://github.com/seancribbs/ripple
 *
 * @author Kevin Burns (@kevburnsjr) (kevburnsjr@gmail.com)
 * @package Ripple
 */

class Ripple {
	
	private $config;
	private $client;
	
	/**
	 * Construct a new Ripple object
	 * @param array $config - config for RiakClient
	 */
	public function __construct($config = null) {
		$this->config($config);
	}

	/**
	 * Retrieve/Instantiate client
	 * @param RiakClient $client
	 * @return RiakClient
	 */
	public static function client($client = null) {
		/* if($client instanceOf RiakClient) {
			$this->client = $client;
		} else if(!$this->client instanceOf RiakClient) {
			// TODO : Add config to client instantiation
			$this->client = new RiakClient();
		}
		return $this->client; */
		return new RiakClient();
	}

	/**
	 * Get and/or set config
	 * @param array $config - config for RiakClient
	 * @return array
	 */
	public function config($config = null) {
		if(is_array($config)) {
			$this->config = $config;
		}
		return $this->config;
	}
}