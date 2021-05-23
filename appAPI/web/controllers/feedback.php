<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends MC_Controller {
       public $_hSocket=null;
       public $_nSocketSelectTimeout=1000000;
       const TIME_BINARY_SIZE =60;
        const TOKEN_LENGTH_BINARY_SIZE =64;
        
       const ENVIRONMENT_PRODUCTION = 0;  
        const ENVIRONMENT_SANDBOX = 1;  
        const DEVICE_BINARY_SIZE = 32;  
        const CONNECT_RETRY_INTERVAL = 1000000;  
        const SOCKET_SELECT_TIMEOUT = 1000000;  
        const COMMAND_PUSH = 1;  
        const STATUS_CODE_INTERNAL_ERROR = 999;  
        const ERROR_RESPONSE_SIZE = 6;  
        const ERROR_RESPONSE_COMMAND = 8;  
        const PAYLOAD_MAXIMUM_SIZE = 256;  
        const APPLE_RESERVED_NAMESPACE = 'aps';  
        
      public function check()
      {
	        $rootPath = $_SERVER['DOCUMENT_ROOT'];
			$pass = "blackberry";	
			$perPath = $rootPath ."/iPhoneAPI/pushCer/PushAshCloudApp.pem";
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', $perPath);
			// assume the private key passphase was removed.
			 stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
			stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
			
			$fp = stream_socket_client('ssl://feedback.push.apple.com:2196', $error, $errorString, 60, STREAM_CLIENT_CONNECT, $ctx);
			// Development server is ssl://feedback.sandbox.push.apple.com:2196
			
			
			if (!$fp) {
			print "Failed to connect feedback server: $err $errstr\n";
			return;
			}
			else {
			     print "Connection to feedback server OK\n";
			     $this->_hSocket=$fp;
			     $this->receive();
			}
			
			fclose($fp);
      }
      
      
	  public function receive()  
	  {  
		    $nFeedbackTupleLen = self::TIME_BINARY_SIZE + self::TOKEN_LENGTH_BINARY_SIZE + self::DEVICE_BINARY_SIZE;  
		  
		    $this->_aFeedback = array();  
		    $sBuffer = '';  
		    while (!feof($this->_hSocket)) {  
		        $this->_log('INFO: Reading...');  
		        $sBuffer .= $sCurrBuffer = fread($this->_hSocket, 8192);  
		        $nCurrBufferLen = strlen($sCurrBuffer);  
		        if ($nCurrBufferLen > 0) {  
		            $this->_log("INFO: {$nCurrBufferLen} bytes read.");  
		        }  
		        unset($sCurrBuffer, $nCurrBufferLen);  
		  
		        $nBufferLen = strlen($sBuffer);  
		        if ($nBufferLen >= $nFeedbackTupleLen) {  
		            $nFeedbackTuples = floor($nBufferLen / $nFeedbackTupleLen);  
		            for ($i = 0; $i < $nFeedbackTuples; $i++) {  
		                $sFeedbackTuple = substr($sBuffer, 0, $nFeedbackTupleLen);  
		                $sBuffer = substr($sBuffer, $nFeedbackTupleLen);  
		                $this->_aFeedback[] = $aFeedback = $this->_parseBinaryTuple($sFeedbackTuple);  
		                $this->_log(sprintf("INFO: New feedback tuple: timestamp=%d (%s), tokenLength=%d, deviceToken=%s.",  
		                    $aFeedback['timestamp'], date('Y-m-d H:i:s', $aFeedback['timestamp']),  
		                    $aFeedback['tokenLength'], $aFeedback['deviceToken']  
		                ));  
		                unset($aFeedback);  
		            }  
		        }  
		  
		        $read = array($this->_hSocket);  
		        $null = NULL;  
		        $nChangedStreams = stream_select($read, $null, $null, 0, $this->_nSocketSelectTimeout);  
		        if ($nChangedStreams === false) {  
		            $this->_log('WARNING: Unable to wait for a stream availability.');  
		            break;  
		        }  
		    }  
		    return $this->_aFeedback;  
		}  
		  
		/** 
		 * Parses binary tuples. 
		 * 
		 * @param  $sBinaryTuple @type string A binary tuple to parse. 
		 * @return @type array Array with timestamp, tokenLength and deviceToken keys. 
		 */  
		protected function _parseBinaryTuple($sBinaryTuple)  
		{  
		    return unpack('Ntimestamp/ntokenLength/H*deviceToken', $sBinaryTuple);  
		}  
		
		function _log($message){
			print($message);
		}

}
?>