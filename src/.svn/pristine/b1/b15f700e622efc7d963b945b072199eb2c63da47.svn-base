<?php
/**
 * @version		$Id: searches.php 22338 2011-11-04 17:24:53Z github_bot $
 * @package		Joomla.Administrator
 * @subpackage	com_search
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class TextsModelDefault extends JModelList
{
	public function __construct($config = array()){
		parent::__construct($config);
	}
	
	public function submitPrizeDraw($post){
		$lang = wh::lang();
		$db	 =& JFactory::getDBO();	
		$answer1 = $post["q1_opt"];
		$answer2 = $post["q2_opt"];
		$answer3 = $post["q3_opt"];
		$answer4 = $post["q4"];
		$answer5 = $post["q5"];
		$firstname = $post["firstname"];
		$lastname = $post["lastname"];
		$country = $post["country"];
		
		$hasMail=false;
		if (isset($post["email"])){
			$email = $post["email"];
			$hasMail=true;
		}else{
			$email = "rodrigo.aronas@sourcingup.com";
		}
		if (isset($post["nl"])){
			$nl = $post["nl"];
			if ($hasMail){
				$res = $this->RegisterEmarsys($post["email"],$lang);
			}
			$nl=1;
		}else{
			$nl=0;
		}
		
		$sqlPD = "INSERT INTO whs_prizedraw_results (answer1, answer2, answer3, answer4, answer5, nl,email,firstname,lastname,country) VALUES ";		
		$sqlPD .= "(".$answer1.", '".$answer2."', '".$answer3."', '".$answer4."', '".$answer5."', ".$nl.", '".$email . "','" . $firstname . "','" . $lastname . "','" . $country . "')";
		$db->setQuery($sqlPD);
		$res = $db->Query();
		//die(var_dump($sqlPD));
		if ($res){ //send mail to customer
		
					$headers=Array();
					$headers[0] = wh::label('PRIZE_DRAW_Q1');
					$headers[1] = wh::label('PRIZE_DRAW_Q2');
					$headers[2] = wh::label('PRIZE_DRAW_Q3');
					$headers[3] = wh::label('PRIZE_DRAW_Q4');
					$headers[4] = wh::label('PRIZE_DRAW_Q5');
					
					$values = Array();
					$values[0]= $answer1;
					$values[1]= $answer2;
					$values[2]= $answer3;
					$values[3]= $answer4;
					$values[4]= $answer5;
					
				$mensaje = wh::label('PRIZE_DRAW_MAIL_BODY');
				$mensaje = str_replace("{username}",ucfirst($firstname)." " . ucfirst($lastname),$mensaje);
				$mailThis2 =& JFactory::getMailer();
				$mailThis2->SetFrom('info@worldhotels.com', 'Worldhotels');
				$mailThis2->AddReplyTo('info@worldhotels.com', 'Worldhotels');
				$mailThis2->setSubject(wh::label('PRIZE_DRAW_MAIL_SUBJECT'));
				$mailThis2->isHTML(true);
				$mailThis2->Encoding = 'base64';
				$mailThis2->setBody(wh::apply_email_template($headers,$values,$mensaje,"","",true));
				$mailThis2->addRecipient($email);
				$mailThis2->addBCC("kjungjohann@worldhotels.com");
				$mailThis2->addBCC("rodrigo.aronas@sourcingup.com");
				$mailThis2->addBCC("cthomas@worldhotels.com ");
				$var = $mailThis2->Send();
				//die(var_dump($var));
		}
		
		return $res;
	
	}
	public function RegisterEmarsys($email,$lang){
	
		switch($lang){
			case "ES":
				$lang=4;
			break;
			case "EN":
				$lang=3;
			break;
			case "IT":
				$lang=6;
			break;
			case "RU":
				$lang=8;
			break;
			case "CN":
				$lang=1;
			break;
			case "DE":
				$lang=2;
			break;
			case "JP":
				$lang=7;
			break;
			case "FR":
				$lang=5;
			break;
		}
	
		$ch = curl_init();
		$url="http://www1.emarsys.net/u/register_bg.php?owner_id=128804548&key_id=3&f=7078&p=2&a=r&SID=&el=&llid=&counted=&c=";

		$url.="&inp_3=".$email;
		$url.="&inp_35=".$lang;

		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);          //execute de Emarsys register      
		curl_close($ch); //close connection
		return $result;
	}
}
