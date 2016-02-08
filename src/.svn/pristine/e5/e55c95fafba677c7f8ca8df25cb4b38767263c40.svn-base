<?php 
jimport ('joomla.plugin.helper');
require_once(dirname (__FILE__) . DS . 'socialloginandsocialshare_helper.php');
class LoginRadius {
  public $IsAuthenticated, $JsonResponse, $UserProfile; 
  public function sociallogin_getapi($ApiSecrete) {
    $IsAuthenticated = false;
	$lr_settings = plgSystemSocialLoginTools::sociallogin_getsettings ();
    if (isset($_REQUEST['token'])) {
      $ValidateUrl = "https://hub.loginradius.com/userprofile.ashx?token=".$_REQUEST['token']."&apisecrete=".$ApiSecrete."";
      if ($lr_settings['useapi'] == 1) {
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
	   if (isset($UserProfile->ID) && $UserProfile->ID != '') 
		{ 
		  $this->IsAuthenticated = true;
		  return $UserProfile;
		}
    }
    else 
	{
         $JsonResponse = @file_get_contents($ValidateUrl);
		 if(strpos(@$http_response_header[0], "400") !== false || strpos(@$http_response_header[0], "401") !== false || strpos(@$http_response_header[0], "403") !== false || strpos(@$http_response_header[0], "404") !== false || strpos(@$http_response_header[0], "500") !== false || strpos(@$http_response_header[0], "503") !== false)
		 {
				return JTEXT::_('COM_LOGINRADIUS_SERVICE_AND_TIMEOUT_ERROR');
		 }
		 else 
		 {
		 	$UserProfile = json_decode($JsonResponse);
		 	if (isset($UserProfile->ID) && $UserProfile->ID != '') 
			{ 
			  $this->IsAuthenticated = true;
			  return $UserProfile;
			}
		 }
    }
   }
  }
}
?>
