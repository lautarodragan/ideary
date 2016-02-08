<?php

defined ('_JEXEC') or die ('Direct Access to this location is not allowed.');


/**
 * SocialLogin plugin helper class.
 */
class plgSystemSocialLoginTools {
	
/**
 * Get the databse settings.
 */
	public static function sociallogin_getsettings () {
      $lr_settings = array ();
      $db = JFactory::getDBO ();
	  $sql = "SELECT * FROM #__LoginRadius_settings";
      $db->
setQuery ($sql);
      $rows = $db->LoadAssocList ();
      if (is_array ($rows)) {
        foreach ($rows AS $key => $data) {
          $lr_settings [$data ['setting']] = $data ['value'];
        }
      }
      return $lr_settings;
    }
	
/*
 * Function that remove unescaped char from string.
 */
	public static function remove_unescapedChar($str) {
	   $in_str = str_replace(array('<', '>', '&', '{', '}', '*', '/', '(', '[', ']' , '@', '!', ')', '&', '*', '#', '$', '%', '^', '|','?', '+', '=','"',','), array(''), $str);
	   $cur_encoding = mb_detect_encoding($in_str) ;
       if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8"))
         return $in_str;
       else
         return utf8_encode($in_str);
    }
	
/*
 * Function that checking username exist then adding index to it.
 */
   public static function get_exist_username($username) {
     $nameexists = true;
        $index = 0;
        $userName = $username;
        while ($nameexists == true) {
          if (JUserHelper::getUserId($userName) != 0) {
            $index++;
            $userName = $username.$index;
          } 
		  else {
            $nameexists = false;
          }
        }
		return $userName;
   }
   
/*
 * Function that generate a random email.
 */
   public static function get_random_email($lrdata) {
     switch ($lrdata['Provider']){
		case 'twitter':
          $lrdata['email'] = $lrdata['id'].'@'.$lrdata['Provider'].'.com';
          break;
        case 'linkedin':
		  $lrdata['email'] = $lrdata['id'].'@'.$lrdata['Provider'].'.com';
		  break;
		default:
          $Email_id = substr($lrdata['id'],7);
          $Email_id2 = str_replace("/", "_", $Email_id);
	      $lrdata['email'] = str_replace(".", "_", $Email_id2).'@'.$lrdata['Provider'].'.com';
		  break;
        }
		return $lrdata['email'];
   }
 
/*
 * Function filter the username.
 */  
   public static function get_filter_username($lrdata) {
     if (!empty($lrdata['FullName'])) {
	    $username = $lrdata['FullName'];
	  }
	  elseif (!empty($lrdata['ProfileName'])) {
	    $username = $lrdata['ProfileName'];
	  }
	  elseif (!empty($lrdata['NickName'])) {
	    $username = $lrdata['NickName'];
	  }
	  elseif (!empty($lrdata['email'])) {
	    $user_name = explode('@',$lrdata['email']);
	    $username = $user_name[0];
	  }
	  else {
	    $username = $lrdata['id'];
	  }
	  return $username;
   }

/*
 * Function that saves users extra profile.
 */   
   /*public static function save_userprofile_data($user_id, $lrdata) {
      // Save the profile data of user
	   $db = JFactory::getDBO ();
	   $data = array();
	   $data['profile']['address1'] = $lrdata['address1'];
	   $data['profile']['address2'] = $lrdata['address2'];
	   $data['profile']['city'] = $lrdata['city'];
	   //$data['profile']['country'] = $lrdata['country'];
	   $data['profile']['dob'] = $lrdata['dob'];
	   $data['profile']['aboutme'] = $lrdata['aboutme'];
	   $data['profile']['website'] = $lrdata['website'];
	    //Sanitize the date
	   if (!empty($data['profile']['dob'])) {
		 //$date = new JDate($data['profile']['dob']);
		 $date = JFactory::getDate();
		 $data['profile']['dob'] = $date->toFormat('%Y-%m-%d');
	   }
	   else {
		 $data['profile']['dob'] = $data['profile']['dob'];
       }
	   $tuples = array();
       $order	= 1;
       foreach ($data['profile'] as $k => $v) {
		  $tuples[] = '('.$user_id.', '.$db->quote('profile.'.$k).', '.$db->quote($v).', '.$order++.')';
       }
       $db->setQuery('INSERT INTO #__user_profiles VALUES '.implode(', ', $tuples));
       $db->query();
   }*/
   
/*
 * Function that checks k2 component exists.
 */
   public static function check_exist_comk2($user_id, $username, $profile_Image, $userImage, $lrdata) {
	  $db = JFactory::getDBO();
	  $username = self::remove_unescapedChar($lrdata['FullName']);
	  $lr_settings = self::sociallogin_getsettings ();
	  /*$last_name = self::remove_unescapedChar($lrdata['lname']);*/
	  $savepath = JPATH_ROOT.DS.'media'.DS.'k2'.DS.'users'.DS;
	  self::insert_user_picture($savepath, $profile_Image, $userImage);
	  if($lrdata['gender']== 'M'){ $gender='m'; } else { $gender='f'; }
      $k2query = "INSERT IGNORE INTO #__k2_users(`id`,`userID`,`userName`,`gender`,`description`,`image`,`url`,`group`,`ip`,`hostname`,`notes`) VALUES ('".$user_id."','".$user_id."','".$username."','".$gender."','".$lrdata['aboutme']."','".$userImage."','".$lrdata['website']."','".trim($lr_settings['k2group'])."','".$_SERVER['REMOTE_ADDR']."','".gethostbyaddr($_SERVER['REMOTE_ADDR'])."','')";
      $db->setQuery($k2query);
      $db->query();
    }
	
/*
 * Function that inserting data in cb table.
 */
   public static function make_cb_user($user, $profile_Image, $userImage, $lrdata) {
	  $db = JFactory::getDBO();
	  $first_name = self::remove_unescapedChar($lrdata['fname']);
	  $last_name = self::remove_unescapedChar($lrdata['lname']);
	  $cbsavepath = JPATH_ROOT.DS.'images'.DS.'comprofiler'.DS;
      self::insert_user_picture($cbsavepath, $profile_Image, $userImage);
      $cbquery = "INSERT IGNORE INTO #__comprofiler(`id`,`user_id`,`firstname`,`lastname`,`avatar`) VALUES ('".$user->get('id')."','".$user->get('id')."','".$first_name."','".$last_name."','".$userImage."')";
      $db->setQuery($cbquery);
      $db->query();
	}
	
/*
 * Function that inserting data in jom social user table.
 */
   public static function make_jomsocial_user($user, $profile_Image, $userImage) {
	  $db = JFactory::getDBO();
	  // Check for jom social.
	  $joomsavepath = JPATH_ROOT.DS.'images'.DS.'avatar'.DS;
      $dumpuserImage = 'images/avatar/'.$userImage;
	  self::insert_user_picture($joomsavepath, $profile_Image, $userImage);
	  $joomquery = "INSERT IGNORE INTO #__community_users(`userid`,`avatar`,`thumb`) VALUES('".$user->get('id')."','".$dumpuserImage."','".$dumpuserImage."')";
      $db->setQuery($joomquery);
      $db->query();
	}
	
/*
 * Function getting k2 plugin userID.
 */
   public static function getK2UserID($id) {
        $db = JFactory::getDBO();
		$query = "SELECT id FROM #__k2_users WHERE userID={$id}";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
   }

/*
 * Function getting return url after login.
 */
   public static function getReturnURL() {
     $app = JFactory::getApplication();
     $router = $app->getRouter();
	 $lr_settings = self::sociallogin_getsettings ();
	 $check_rewrite = $app->getCfg('sef_rewrite');
     $url = '';
	 $db = JFactory::getDbo();
     if ($itemid = $lr_settings['setredirct']) {
       if ($router->getMode() == JROUTER_MODE_SEF) {
		   $query = "SELECT path FROM #__menu WHERE id = ".$itemid;
           $db->setQuery($query);
           $url = $db->loadResult();
		   if($check_rewrite == '0' AND !empty($url)) {
		     $url = 'index.php/'.$url;
		   }
         }
         else {
           $query = "SELECT link FROM #__menu WHERE id = ".$itemid;
           $db->setQuery($query);
           $url = $db->loadResult();
         }
     }
     if (!$url) {
       // stay on the same page
       $uri = clone JFactory::getURI();
       $vars = $router->parse($uri);
       unset($vars['lang']);
       if ($router->getMode() == JROUTER_MODE_SEF) {
         if (isset($vars['Itemid'])) {
           $itemid = $vars['Itemid'];
           $menu = $app->getMenu();
           $item = $menu->getItem($itemid);
           unset($vars['Itemid']);
           if (isset($item) && $vars == $item->query) {
		     $query = "SELECT path FROM #__menu WHERE id = '".$itemid."' AND home = 1";
             $db->setQuery($query);
             $home_url = $db->loadResult();
			 if ($home_url) {
			   $url = 'index.php'; 
			 }
		     else {
               $query = "SELECT path FROM #__menu WHERE id = ".$itemid;
               $db->setQuery($query);
               $url = $db->loadResult();
			   if($check_rewrite == '0' AND !empty($url)) {
		         $url = 'index.php/'.$url;
		       }
			 }
           }
           else {
             // get article url path
             $articlePath = JFactory::getURI()->getPath();
             $url = $articlePath;
           }
         }
         else {
          $articlePath = JFactory::getURI()->getPath();
          $url = $articlePath;
         }
       }
       else {
	   		$fullurl=$_SERVER['HTTP_REFERER'];
			if(strpos($fullurl,"callback=")>0){
			$urldata=explode("callback=",$fullurl);
			$amppos= strpos($urldata[1],"&");
			$endlimit=strlen($urldata[1]);
			if($amppos>0){
			$url = 'index.php?'.$_SERVER['QUERY_STRING'].substr($urldata[1],$amppos,$endlimit);
			}
			}
       }
     }
     return $url;
  }
  
/*
 * Function open a popup for enter email.
 */
   public static function enterEmailPopup($title, $msg, $msgtype) {
     $document = JFactory::getDocument();
	 $session = JFactory::getSession();
	 $lrdata = $session->get('tmpuser');
	 if ($msgtype == 'warning') { 
	   $style = 'background-color: #f6d9d0; border: 1px solid #990000;';
	 }
	 else {
	   $style = 'background-color: #e1eabc; border: 1px solid #90b203';
	 }
     $document->addStyleSheet(JURI::root().'modules/mod_socialloginandsocialshare/lrstyle.css');
	 $output = '
<div class="LoginRadius_overlay" class="LoginRadius_content_IE">
';
	 $output .='
<div id="popupouter">
  <p id ="outerp"> '.$title.'</p>
  <div id="popupinner">
    <div id="textmatter" style ="'.$style.'">';
      if ($msg) {
      $output .= '<strong>'.$msg.'</strong>';
      }
      $output .= '</div>
    <form method="post" action="">
      <div>';
        $output .= '
        <p id = "innerp">'.JText::_ ('COM_SOCIALLOGIN_POPUP_DESC').'</p>
        <input type="text" name="email" id="email" class="inputtxt"/>
      </div>
      <div>';
        $output .= '
        <input type="submit" name="sociallogin_emailclick" value="'.JText::_('JLOGIN').'" class="inputbutton"/>
        ';
        $output .= '
        <input type="submit" value="'.JText::_('JCANCEL').'" name = "cancel" class="inputbutton"/>
        ';
        $output .= '
        <input type="hidden" name ="session" value="'.$lrdata['session'].'"/>
        ';
        $output .= '</div>
    </form>
  </div>
</div>
</div>
';
	 $document->addCustomTag($output);
  }

/*
 * Function getting user data from loginradius.
 */
    public static function get_userprofile_data($userprofile) {
      $lrdata['session'] = uniqid('LoginRadius_', true);
      $lrdata['FullName'] = (!empty($userprofile->FullName) ? $userprofile->FullName : "");
      $lrdata['ProfileName'] = (!empty( $userprofile->ProfileName) ? $userprofile->ProfileName : "");
      $lrdata['fname'] = (!empty( $userprofile->FirstName) ? $userprofile->FirstName : "");
      $lrdata['lname'] = (!empty($userprofile->LastName) ? $userprofile->LastName : "");
      $lrdata['NickName'] = (!empty($userprofile->NickName) ? $userprofile->NickName : "");
      $lrdata['id'] = (!empty($userprofile->ID) ? $userprofile->ID : "");
      $lrdata['Provider'] = (!empty($userprofile->Provider) ? $userprofile->Provider : "");
      $lrdata['email'] = (sizeof($userprofile->Email) > 0 ? $userprofile->Email[0]->Value : "");
      $lrdata['thumbnail'] = (!empty ($userprofile->ThumbnailImageUrl) ? trim($userprofile->ThumbnailImageUrl) : "");
      $lrdata['address1'] = (!empty($userprofile->Addresses) ? $userprofile->Addresses :"");
      if (empty($lrdata['thumbnail']) && $lrdata['Provider'] == 'facebook') {
        $lrdata['thumbnail'] = "http://graph.facebook.com/".$lrdata['id']."/picture?type=square";
      }
      if (empty($lrdata['address1'])) {
        $lrdata['address1'] = (!empty($userprofile->MainAddress) ? $userprofile->MainAddress:"");
      }
      $lrdata['address2'] = $lrdata['address1'];
      $lrdata['city'] = (!empty($userprofile->City) ? $userprofile->City : "");
      if (empty($lrdata['city'])) {
	    $lrdata['city'] = (!empty($userprofile->HomeTown) ? $userprofile->HomeTown : $lrdata['address1']);
      }
      $lrdata['country'] = (!empty($userprofile->Country) ? $userprofile->Country: "");
      if (empty($lrdata['country'])) {
        $lrdata['country'] = (!empty($userprofile->Country->Name) ? $userprofile->Country->Name : "");
      }
      $lrdata['aboutme'] = (!empty($userprofile->About) ? $userprofile->About : "");
      $lrdata['website'] = (!empty( $userprofile->ProfileUrl) ? $userprofile->ProfileUrl : "");
      $lrdata['dob'] = (!empty($userprofile->BirthDate) ? $userprofile->BirthDate : "");
	  $dob = '';
	  if(!empty($lrdata['dob'])) {
        $userdob = str_replace("-","/",$lrdata['dob']);
        $userdob = str_replace(".","/",$lrdata['dob']);
        $dob = explode("/",$userdob);
        if ( $lrdata['Provider'] == "linkedin" ||  $lrdata['Provider'] == "vkontakte"){
          $dob = $dob[2]."-".$dob[1]."-".$dob[0];
        }
        else {
          $dob = $dob[2]."-".$dob[0]."-".$dob[1];
        }
	  }
      $lrdata['dob'] = date('Y-m-d', strtotime($dob));
      $lrdata['gender'] = (!empty($userprofile->Gender) ? $userprofile->Gender : '');
	  return $lrdata;
	}
   
/*
 * Function that remove unescaped char from string.
 */
   public static function insert_user_picture($path, $profile_Image, $userImage) {
      $lr_settings = self::sociallogin_getsettings ();
	  if ($lr_settings['useapi'] == 1) {
	    $ch = curl_init($profile_Image);
        $fp = fopen($path . $userImage, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
	  }
	  else {
	    $thumb_image = @file_get_contents($profile_Image);
		if (@$http_response_header == NULL) {
          $profile_Image = str_replace('https', 'http', $profile_Image);
          $thumb_image = @file_get_contents($profile_Image);
        }
		if (empty($thumb_image)) {
	      $thumb_image = @file_get_contents(JURI::root().'media' . DS . 'com_socialloginandsocialshare' . DS .'images' . DS . 'noimage.png');  
	    }
        $thumb_file = $path . $userImage;
        @file_put_contents($thumb_file, $thumb_image);
	 }
   }

/*
 * Function that remove unescaped char from string and add image.
 */
   public static function add_newid_image($lrdata) {
     $profile_Image = $lrdata['thumbnail'];
		if (empty($profile_Image)) {
          $profile_Image = JURI::root().'media' . DS . 'com_socialloginandsocialshare' . DS .'images' . DS . 'noimage.png';
        }
		$userImage = $lrdata['id'] . '.jpg';
		$find = strpos($userImage, 'http');
		
		if ($find !== false) {
          $userImage = substr($userImage, 8);
          $userImage = plgSystemSocialLoginTools::remove_unescapedChar($userImage);

		}
        $sociallogin_savepath = JPATH_ROOT.DS.'images'.DS.'sociallogin'.DS;
        plgSystemSocialLoginTools::insert_user_picture($sociallogin_savepath, $profile_Image, $userImage);
		return $userImage;
   }
   
/*
 * Function that make compitable with kunena.
 */
   public static function check_exist_comkunena($user_id, $username, $profile_Image, $userImage, $lrdata) {
      $db = JFactory::getDBO();
	  $userImage = 'avatar'.$userImage;
	  if ($lrdata['gender'] == 'M' OR $lrdata['gender'] == 'm' OR $lrdata['gender'] == 'Male' OR $lrdata['gender'] == 'male') {
	    $lrdata['gender'] = '1';
	  }
	  else if ($lrdata['gender'] == 'F' OR $lrdata['gender'] == 'f' OR $lrdata['gender'] == 'Female' OR $lrdata['gender'] == 'female') {
	    $lrdata['gender'] = '2';
	  }
	  $kunenasavepath = JPATH_ROOT.DS.'media'.DS.'kunena'.DS.'avatars'.DS.'users'.DS;
      $dumpuserImage = 'users/'.$userImage;
      self::insert_user_picture($kunenasavepath, $profile_Image, $userImage);
	  $kunenaquery = "UPDATE #__kunena_users SET `userid` = '".$user_id."',`avatar` = '".$dumpuserImage."',`gender` = '".$lrdata['gender']."',`birthdate` = '".$lrdata['dob']."',`location` = '".$lrdata['city']."',`personalText` = '".$lrdata['aboutme']."',`websiteurl` = '".$lrdata['website']."' WHERE `userid` = '".$user_id."'";
	
      $db->setQuery($kunenaquery);
      $db->query();
  }
   
/*
 * Function that make compitable with jfusion.

 */
   public static function create_jfusion_user(&$user, $newuser) {
     include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfusion' . DS . 'models' . DS . 'model.jfusion.php';
     include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfusion' . DS . 'models' . DS . 'model.factory.php';
     $options = array('entry_url' => JURI::root() . 'index.php?option=com_user&task=login', 'silent' => true);
     $slaves = JFusionFunction::getPlugins();
	 foreach ($slaves as $slave) {
       if ( $slave->dual_login == 1) {
	     $JFusionSlave =  JFusionFactory::getUser($slave->name);
         $status = array();
         $slaveUser = $JFusionSlave->getUser($user);
		 if (empty($slaveUser)) {
		   if ($slave->name == 'vbulletin') {
		     $slaveUser->password_clear = $user->password;
			  $slaveUser->password = $user->password;
			  $slaveUser->userid = $user->id;
			  $slaveUser->username = $user->username;
			  $slaveUser->email = $user->email;
			  $JFusionSlave->createUser($slaveUser, $status);
		   }
		   else {
		     $JFusionSlave->createUser($user, $status);
		   }
		   $slaveUser = $JFusionSlave->getUser($user);
		 }
		 if ($newuser == false) {
	       $user->password_clear = $user->password;
	       $slaveUser->password_clear = $user->password_clear;
		   $JFusionSlave->updatePassword($user,$slaveUser,$status);
	     }
	     else {
	       $user->password_clear = $user->password_clear;
	       $slaveUser->password_clear = $user->password_clear;
	     }
         $JFusionSlave->createSession($slaveUser, $options);
      }
    }  
  }
} 