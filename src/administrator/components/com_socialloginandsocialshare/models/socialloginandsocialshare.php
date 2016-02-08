<?php
defined ('_JEXEC') or die ('Direct Access to this location is not allowed.');
jimport ('joomla.application.component.modellist');

/**
 * SocialLoginAndSocialShare Model.
 */
 
class SocialLoginAndSocialShareModelSocialLoginAndSocialShare extends JModelList
{
	/**
	 * Save Settings.
	 */
	public function saveSettings ()
	{
		//Get database handle
		$db = $this->getDbo ();
		$api_settings = array();
        $mainframe = JFactory::getApplication();
		//Read Settings
		$rearrange_settings = JRequest::getVar ('rearrange_settings');
		$settings = JRequest::getVar ('settings');
		$s_articles = JRequest::getVar ('s_articles');
		$c_articles = JRequest::getVar ('c_articles');
		$settings['apikey'] = trim($settings['apikey']);
		$settings['apisecret'] = trim($settings['apisecret']);
        //print_r($settings);
		$apikey = trim($settings['apikey']);
		$apisecret = trim($settings['apisecret']);
		$apicred = $settings['useapi'];
		$settings['s_articles'] = (sizeof($s_articles) > 0 ? serialize($s_articles) : "");
		$settings['c_articles'] = (sizeof($c_articles) > 0 ? serialize($c_articles) : "");
		$settings['rearrange_settings'] = (sizeof($rearrange_settings) > 0 ? serialize($rearrange_settings) : "");
		$api_settings = $this->check_api_settings($apikey, $apisecret, $apicred);
		if (! $this->isValidApiSettings($apikey)) {
		  JError::raiseWarning ('', JText::_ ('COM_SOCIALLOGIN_APIKEY_ERROR'));
		  $mainframe->redirect (JRoute::_ ('index.php?option=com_socialloginandsocialshare&view=socialloginandsocialshare&layout=default', false));
		}
		else if (! $this->isValidApiSettings($apisecret)) {
		  JError::raiseWarning ('', JText::_ ('COM_SOCIALLOGIN_APISECRET_ERROR'));
		  $mainframe->redirect (JRoute::_ ('index.php?option=com_socialloginandsocialshare&view=socialloginandsocialshare&layout=default', false));
		}
		else if($api_settings==JTEXT::_('COM_LOGINRADIUS_SERVICE_AND_TIMEOUT_ERROR'))
		{
		  JError::raiseWarning ('', JText::_ ('COM_LOGINRADIUS_SERVICE_AND_TIMEOUT_ERROR'));
		  $mainframe->redirect (JRoute::_ ('index.php?option=com_socialloginandsocialshare&view=socialloginandsocialshare&layout=default', false));
		}
		else if($api_settings==JTEXT::_('COM_LOGINRADIUS_CURL_ERROR'))
		{
			JError::raiseWarning ('', JText::_ ('COM_LOGINRADIUS_CURL_ERROR'));
		  	$mainframe->redirect (JRoute::_ ('index.php?option=com_socialloginandsocialshare&view=socialloginandsocialshare&layout=default', false));
		}
		else if($api_settings==JTEXT::_('COM_LOGINRADIUS_FSOCKOPEN_ERROR'))
		{
		 	JError::raiseWarning ('', JText::_ ('COM_LOGINRADIUS_FSOCKOPEN_ERROR'));
		  	$mainframe->redirect (JRoute::_ ('index.php?option=com_socialloginandsocialshare&view=socialloginandsocialshare&layout=default', false));
		}
		else {
		  $sql = "DELETE FROM #__LoginRadius_settings";
		  $db->setQuery ($sql);
		  $db->query ();
		  
          //Insert new settings
		  foreach ($settings as $k => $v){
			 $sql = "INSERT INTO #__LoginRadius_settings ( setting, value )" . " VALUES ( " . $db->Quote ($k) . ", " . $db->Quote ($v) . " )";
			$db->setQuery ($sql);
			$db->query ();
		  }
		}
	 }
	/**
	 * Read Settings
	 */
	public function getSettings ()
	{
		$settings = array ();
        $db = $this->getDbo ();
        $sql = "SELECT * FROM #__LoginRadius_settings";
		$db->setQuery ($sql);
		$rows = $db->LoadAssocList ();

		if (is_array ($rows))
		{
			foreach ($rows AS $key => $data)
			{
				$settings [$data['setting']] = $data ['value'];
				
			}
		}

		return $settings;
	}
	/**
	 * Check apikey and secret is valid.
	 */
	public function isValidApiSettings($apikey) {
      return !empty($apikey) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $apikey);
    }
	/**
	 * Check api credential settings.
	 */
	public function check_api_settings($apikey, $apisecret, $apicred){
		$JsonResponse='';
      if (isset($apikey)){
        $ValidateUrl = "https://hub.loginradius.com/ping/$apikey/$apisecret";
        if ($apicred == 1) {
		  if (in_array ('curl', get_loaded_extensions ()) AND function_exists('curl_exec')) {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $ValidateUrl);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5);
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
            if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' or !ini_get('safe_mode'))) 
			{
              curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
              curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            }
            else 
			{
              curl_setopt($curl_handle,CURLOPT_HEADER, 1);
              $url = curl_getinfo($curl_handle,CURLINFO_EFFECTIVE_URL);
              curl_close($curl_handle);
              $curl_handle = curl_init();
              $url = str_replace('?','/?',$url);
              curl_setopt($curl_handle, CURLOPT_URL, $url);
              curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
           }
		   $JsonResponse = curl_exec($curl_handle);
	   	   $httpCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
           if(in_array($httpCode, array(400, 401, 403, 404, 500, 503, 0)) && $httpCode != 200)
		   {
				return JTEXT::_('COM_LOGINRADIUS_SERVICE_AND_TIMEOUT_ERROR');
			}
			else
			{
				if(curl_errno($curl_handle) == 28)
				{
					return JTEXT::_('COM_LOGINRADIUS_SERVICE_AND_TIMEOUT_ERROR');
				}
			}
       		$UserProfile = json_decode($JsonResponse);
	   		curl_close($curl_handle);
			if (isset($UserProfile->ok)) 
			{ 
			  $this->IsAuthenticated = true;
			  return $UserProfile;
			}
		 }
		 else
		 {
		 	return JTEXT::_('COM_LOGINRADIUS_CURL_ERROR');
		 }
       }
       else 
	   {
        $JsonResponse = @file_get_contents($ValidateUrl);
		 if(strpos(@$http_response_header[0], "400") !== false || strpos(@$http_response_header[0], "401") !== false || strpos(@$http_response_header[0], "403") !== false || strpos(@$http_response_header[0], "404") !== false || strpos(@$http_response_header[0], "500") !== false || strpos(@$http_response_header[0], "503") !== false)
		 {
				return JTEXT::_('COM_LOGINRADIUS_SERVICE_AND_TIMEOUT_ERROR');
		 }
		 if(empty($JsonResponse))
		 {
		 	return JTEXT::_('COM_LOGINRADIUS_FSOCKOPEN_ERROR');
		 }
		 else 
		 {
		 	$UserProfile = json_decode($JsonResponse);
           if (isset($UserProfile->ok)) 
			{ 
			  $this->IsAuthenticated = true;
			  return $UserProfile;
			}
		 }
       }
     }
   }
 }