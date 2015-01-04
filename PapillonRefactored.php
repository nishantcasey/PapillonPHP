<?php   

	/**
	 * This class file is a PHP based implementation of the RESTful Papillon API
	 * by Stratergia. 
	 *
	 * PapillonAPI.php authors: 
	 * 
	 * Nishant Casey
	 * Garrett Coleman
	 * Mike Finn
	 * Thomas McCallister 
	 * David O'Connor
	 * 
	 */
	class PapillonAPI{
    	/**
    	 * The $_BASEURL points at the master server on which the Papillon API is installed.
    	 * @var string
    	 */
    	
    	private $_BASEURL = '/papillonserver/rest/';

    	/**
    	 * The endpoints property refers to the specific API endpoints
    	 * specified in the Papillon Documentation
    	 * @var Global String associative array of Endpoints
    	 */
    	private $_ENDPOINTS;


		/**
		 * The Papillon Constructor instantiates the relevant endpoints
		 * and sets the timezone to default - Europe/Dublin
		 */
		public function PapillonAPI(){

			$ip = file_get_contents('ip.txt');
			$base = $this->_BASEURL;
			$this->_BASEURL = $ip.$base;


			$this->_ENDPOINTS['datacenters'] = 'datacenters/';
			$this->_ENDPOINTS['floors'] = 'datacenters/{datacenterId}/allfloors';
			$this->_ENDPOINTS['racks'] = 'datacenters/{datacenterId}/floors/{floorId}/racks/';
			$this->_ENDPOINTS['hosts'] = 'datacenters/{datacenterId}/floors/{floorId}/racks/{rackId}/hosts';
			$this->_ENDPOINTS['datacenterspower'] = 'datacenters/{datacenterId}/power?';
			$this->_ENDPOINTS['floorpower'] = 'datacenters/{datacenterId}/floors/{floorId}/power?';
			$this->_ENDPOINTS['rack'] = 'datacenters/{datacenterId}/floors/{floorId}/racks/{rackId}/power?';
			$this->_ENDPOINTS['host'] = 'datacenters/{datacenterId}/floors/{floorId}/racks/{rackId}/hosts/{hostId}/power?';
			
			date_default_timezone_set("Europe/Dublin");

		}


		/**
		 * Lists all datacenters reporting back to the Papillon Master as specified above
		 * 
		 * @return PHP Object with root node of <datcenters>
		 */
		public function listDatacenters(){
			$url = $this->_BASEURL.$this->_ENDPOINTS['datacenters'];
			
			return $this->curl_get_json($url);
		}
		/**
		 * Lists all floors relating to a speicfic datacenter.
		 * @param  int $dc
		 * @return PHP Object with root node of <floor> or <floors>
		 */
		public function listFloors($dc){
			$url = str_replace('{datacenterId}', $dc, $this->_ENDPOINTS['floors']);
			
			return $this->curl_get_json($this->_BASEURL.$url);
		}

		/**
		 * Lists all the racks relating to a specific floor in a specific datacenter.
		 * @param  int $dc - Datacenter in question
		 * @param  int $floor - Floor in question
		 * @return PHP Object with root node of <rack> or <racks>
		 */
		public function listRacks($dc, $floor){
			$url = str_replace('{datacenterId}', $dc, $this->_ENDPOINTS['racks']);
			$url = str_replace('{floorId}', $floor, $url);
			
			return $this->curl_get_json($this->_BASEURL.$url);
		}

		/**
		 * Lists all hosts relating to a specific rack on a specific floor in a datacenter.
		 * 
		 * @param  int $dc - Datacenter in question
		 * @param  int $floor - Floor in question
		 * @param  int $rack - Rack in question
		 * @return PHP Object with root node of <host> or <hosts>
		 */
		public function listHosts($dc, $floor, $rack){
			$url = str_replace('{datacenterId}', $dc, $this->_ENDPOINTS['hosts']);
			$url = str_replace('{floorId}', $floor, $url);
			$url = str_replace('{rackId}', $rack, $url);
			
			return $this->curl_get_json($this->_BASEURL.$url);
		}

		/**
		 * Gets the power usage information for all hosts relating to a specified datacenter.
		 * 
		 * @param  integer  $dcID
		 * @param  integer $interval - 0 for 1hr, 1 for 12hrs, 2 for 24hrs.
		 * @return PHP Object with root node of power
		 */
		public function getDCPower($dcID, $interval=0){
			$start = $this->getStart($interval);
			$end = time();
			$url = str_replace('{datacenterId}', $dcID, $this->_ENDPOINTS['datacenterspower']);
			$times = array('starttime' => $start, 'endtime' => $end);
			
			return $this->curl_get_json($this->_BASEURL.$url, $times);
		}
		
		/**
		 * Gets the power usage information for all hosts relating to a specified floor in a 
		 * specific datacenter.
		 * 
		 * @param  integer $dcID
		 * @param  integer $floorID
		 * @param  integer $interval - 0 for 1hr, 1 for 12hrs, 2 for 24hrs.
		 * @return PHP Object with <power> as root node. 
		 */
		public function getFloorPower($dcID, $floorID, $interval=0){
			$start = $this->getStart($interval);
			$end = time();
			$url = str_replace('{datacenterId}', $dcID, $this->_ENDPOINTS['floorpower']);
			$url = str_replace('{floorId}', $floorID, $url);
			$times = array('starttime' => $start, 'endtime' => $end);
			
			return $this->curl_get_json($this->_BASEURL.$url, $times);	
		}
		
		/**
		 * Gets the power usage information for all hosts relating to aspecified rack
		 * ona  specific floor in a specific datacenter
		 * 
		 * @param  integer  $dcID
		 * @param  integer  $floorID
		 * @param  integer  $rackID
		 * @param  integer $interval - 0 for 1hr, 1 for 12hrs, 2 for 24hrs.
		 * @return PHP Object with root node of <power>
		 */
		public function getRackPower($dcID,$floorID,$rackID,$interval=0){
			$start = $this->getStart($interval);
			$end = time();
			$url = str_replace('{datacenterId}', $dcID, $this->_ENDPOINTS['rack']);
			$url = str_replace('{floorId}', $floorID, $url);
			$url = str_replace('{rackId}', $rackID, $url);
			$times = array('starttime' => $start, 'endtime' => $end);
			
			return $this->curl_get_json($this->_BASEURL.$url, $times);	
		}
		/**
		 * Gets the power usage information for all hosts relating to a specified host on a specific
		 * rack on a specific floor in a specific datacenter.
		 * 
		 * @param  [type]  $dcID
		 * @param  [type]  $floorID
		 * @param  [type]  $rackID
		 * @param  [type]  $hostID
		 * @param  integer $interval
		 * @return PHP Object with root node of <power>
		 */
		public function getHostPower($dcID,$floorID, $rackID, $hostID, $interval=0){
			$start = $this->getStart($interval);
			$end = time();
			$ch = curl_init();
			$url = str_replace('{datacenterId}', $dcID, $this->_ENDPOINTS['host']);
			$url = str_replace('{floorId}', $floorID, $url);
			$url = str_replace('{rackId}', $rackID, $url);
			$url = str_replace('{hostId}', $hostID, $url);
			$times = array('starttime' => $start, 'endtime' => $end);

			return $this->curl_get_json($this->_BASEURL.$url, $times);
		}

		/**
		 * Finds unix timestamp for specified intervals in getPower functions. 
		 * @param  long integer $interval -- 0 for 1hr, 2 for 12hrs, 3 for 24hrs
		 * @return long integer $start
		 */
		public function getStart($interval){
			$end = time();
			$twentyFour = $end - 86400;
			$start;
			switch ($interval) {
				case 0:
    				//milliseconds in an hour
				$start = $end-3600;
				break;
				case 1:
    				//milliseconds in 12 hours
				$start = $end-43200;
				break;
				case 2:
				$start = $end-86400;
				break;		
			}
			return $start;
		}
		/**
		 * Wrapper function for basic curl functionality implemented here to ensure the return
		 * of JSON objects from the Papillon API to remove a layer of conversion from PHP Interpreter.
		 * @param  String $url -- full restful url without GET params with '?' terminator if GET to be passed
		 * @param  array $get -- get parameters
		 * @param  array $options -- additional curl options (must be array with valid curl_setopt() options)
		 * @return PHP Object
		 */
		public function curl_get_json($url, array $get = NULL, array $options = array()){

			if( isset($get)){
				$defaults = array( 
					CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get), 
					CURLOPT_HEADER => 0, 
					CURLOPT_RETURNTRANSFER => TRUE, 
					CURLOPT_TIMEOUT => 4,
					CURLOPT_HTTPHEADER => array('Content-Type: application/json','Accept: application/json')
					); 
			}else{
				$defaults = array( 
					CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''), 
					CURLOPT_HEADER => 0, 
					CURLOPT_RETURNTRANSFER => TRUE, 
					CURLOPT_TIMEOUT => 4,
					CURLOPT_HTTPHEADER => array('Content-Type: application/json','Accept: application/json')
					); 
			}
			
			$ch = curl_init(); 
			curl_setopt_array($ch, ($options + $defaults)); 
			if( ! $result = curl_exec($ch)) 
			{ 
				trigger_error(curl_error($ch)); 
			} 
			curl_close($ch);

			return json_decode($result); 
		}
		
		

	}

	if (php_sapi_name() == 'cli' && isset($argv) && count($argv) == 2) {
	    file_put_contents('ip.txt',$argv[1]);
	    echo 'IP: ' . $argv[1] . ' successfully written to ip.txt', PHP_EOL;
	    echo 'You can now include this refactored Papillon API into your code', PHP_EOL;
	} else if (php_sapi_name() == 'cli' && isset($argv) && count($argv) != 2) {
		echo 'Incorrect CLI use of Papillon PHP Refactored File. Please use as follows: ', PHP_EOL;
		echo '"php PapillonRefactored.php <Master IP Address:8080>"', PHP_EOL;
	} 



	?>
