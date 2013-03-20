<?php
/**
 * Module description
 * 
 * @package General
 * @version $Rev$
 * @copyright $Date$
 * @author $Author$
 * @license BSD License. Feel free to use and modify
 */

class deviceDetection {
	/**
	 * The HTTP Headers that will be examined to find the best User Agent, if one is not specified, copied from WURFL
	 * 
	 * This code is copied from BWF/thirdparty/WurflCloudClient/Client/Client.php
	 * 
	 * @var array
	 */
	private $user_agent_headers = array(
		'HTTP_X_DEVICE_USER_AGENT',
		'HTTP_X_ORIGINAL_USER_AGENT',
		'HTTP_X_OPERAMINI_PHONE_UA',
		'HTTP_X_SKYFIRE_PHONE',
		'HTTP_X_BOLT_PHONE_UA',
		'HTTP_USER_AGENT'
	);
	
	/**
	 * Whether the UA is a mobile device or not
	 * 
	 * @var boolean
	 */
	public $isMobile;
	
	/**
	 * Whether the UA is Internet Explorer or not
	 * 
	 * @var boolean
	 */
	public $isIE;
	
	/**
	 * Whether the UA is an old version (9<) of IE
	 * 
	 * @var boolean
	 */
	public $isOldIE;
	
	/**
	 * Sets the internal user-agent string representation to the provided UA or, if empty, to whatever value the server has
	 * 
	 * @param string $userAgent The (custom) UA
	 * @return string Returns a UA
	 */
	public function setUserAgent($userAgent='') {
		$this->ua = $userAgent;
		if (!$this->ua) {
			$this->ua = $this->getUserAgent();
		}
		
		return $this->ua;
	}
	
	/**
	 * Sets the user agent, copied from WURFL
	 * 
	 * This piece of code is copied from BWF/thirdparty/WurflCloudClient/Client/Client.php
	 * 
	 * @param mixed $source
	 */
	protected function getUserAgent($source=null) {
		if (is_null($source) || !is_array($source)) {
			$source = $_SERVER;
		}
		$user_agent = '';
		foreach ($this->user_agent_headers as $header) {
			if (array_key_exists($header, $source) && $source[$header]) {
				$user_agent = $source[$header];
				break;
			}
		}
		if (strlen($user_agent) > 255) {
			return substr($user_agent, 0, 255);
		}
		return $user_agent;
	}
	
	/**
	 * Detects IE and its version (Used by NOI)
	 * 
	 * Original from stackoverflow, modified a bit to set some flags
	 */
	public function detectIE() {
		preg_match('/MSIE (.*?);(.(?!Opera))*$/', $this->ua, $matches);

		if (count($matches)>1){
			$this->isIE = true;
			//Then we're using IE
			$version = $matches[1];

			switch(true){
				//IE 8 or under!
				case ($version<=8):
					$this->isOldIE = true;
					break;
			}
		}
	}
	
	/**
	 * Does all the hazzle in one step and fills in the object with data
	 * 
	 * @TODO CSP@2012-10-05 Change function name before committing?
	 */
	public function setObjectData() {
		$this->setUserAgent();
		if ($this->isMobileUA()) {
			$this->doWurflCloudRequest();
		} else {
			$this->detectIE();
		}
		
		return true;
	}
	
	/**
	 * (Very!) quick function to check whether we are using a mobile device or not
	 * 
	 * This script comes from http://detectmobilebrowsers.com/
	 * 
	 * Last update: 02-10-2012
	 * 
	 * Do NOT change the $useragent variable name because it comes so from the original website and changing this could
	 * break things on an update
	 * 
	 * If you update the script, please copy/paste the second line! The first line was added by CSP to increase the
	 * accuracy from 97,6% (364 bad detections) to 98,8% (182 bad detections). For comparisons, ask CSP to execute
	 * conversieScripts/tests/wurfl-vs-phpMobile/comparison.php
	 * 
	 * @param string $useragent
	 * @return string Returns true if device is suspected to be a mobile device or false otherwise
	 */
	public function isMobileUA($useragent='') {
		$return = false;
		if (!$useragent) {
			$useragent = $this->ua;
		}
		
		// First line: add all android devices, "andro id" which is a typo in some sony mobile devices, i-mobile UA, nintendo, ericsson devices, iPad, iTouch, Opera mini (old versions) and "tablet browser" as mobile devices
		if (preg_match('/android|andro id|i\-mobile|nintendo|ericsson|ipad|itouch|MSIE (5|6)\.0; (KDDI|nitro|epoc).+Opera (6|7|8)\.|tablet browser/i', $useragent) || 
			preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))
		) {
			$return = true;
		}
		
		// Set our internal flag and return value
		$this->isMobile = $return;
		return $return;
	}

	/**
	 * Makes the actual WURFL Cloud request and saves the data
	 */
	protected function doWurflCloudRequest() {
		$capabilities = array();
		
		try {
			$wurflConfig = new WurflCloud_Client_Config();
			$wurflConfig->api_key = WURFL_APIKEY;
			
			$wurflClient = new WurflCloud_Client_Client($wurflConfig);
			$wurflClient->detectDevice();
			
			$capabilities = $wurflClient->capabilities;
		} catch (Exception $e) {
			// Log the problem should one occur.
			$sistProblemIdentifier = new sistProblemIdentifier();
			$sistProblemIdentifier->addProblem('wurflCloudRequestProblem: '.get_class($e), $e->getMessage());
		}
		
		if (!empty($capabilities)) {
			foreach($capabilities AS $key => $capability) {
				$this->{'wurfl_'.$key} = $capability;
			}
			
			/*
			 * The information of WURFL is mandatory: if fast detection says that the current device is a mobile device
			 * but WURFL says it is not, believe WURFL (or default case which is always desktop)
			 */
			if (isset($this->wurfl_is_wireless_device)) {
				$this->isMobile = $this->wurfl_is_wireless_device;
			}
		}
	}
}