<?php
/**
 * @version		$Id: controller.php 22338 2011-11-04 17:24:53Z github_bot $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

jimport( 'joomla.application.component.controller' );

class TextsController extends JController
{
	public function display($cachable = false, $urlparams = false)
	{
		if (!isset($_REQUEST["view"])){
			JRequest::setVar('view', 'default');  
		}
		parent::display();
	}
	
	/*public function submitDraw(){

		$model = $this->getModel('default'); 
		$result = $model->submitPrizeDraw($_POST);	
		$this->setRedirect("index.php?lang=".$lang."&option=com_thankyoupage&text_id=6");
	}
	public function submithrgdraw(){

		$model = $this->getModel('hrg'); 
		$answer1 = JRequest::getString('q1_opt');
		$answer2 = JRequest::getString('q2_opt');
		$answer3 = JRequest::getString('q3_opt');
		$answer4 = JRequest::getString('q4_opt');
		$answer5 = JRequest::getString('q5_opt');
		$firstname = JRequest::getString("firstname");
		$team = JRequest::getString("team");
		$hrg_office = JRequest::getString("hrg_office");
		$iata = JRequest::getString("iata");
		$hrg_office_addr = JRequest::getString("hrg_office_add");
		$country = JRequest::getString("country");
		$email = JRequest::getString("email");
		$nl = JRequest::getString("nl");
		$lang = wh::lang();
		$result = $model->submitPrizeDraw($answer1,$answer2,$answer3,$answer4,$answer5,$firstname,$team,$hrg_office, $iata, $hrg_office_addr,$country ,$email,$nl);	
		$this->setRedirect("index.php?lang=".$lang."&option=com_thankyoupage&text_id=10"); // prizedraw hrg chequear el numero
	}
	public function submitcityguidedraw(){

		$model = $this->getModel('cityguide'); 
		$answer1 = JRequest::getString('q1_opt');
		$answer2 = JRequest::getString('q2_opt');
		$firstname = JRequest::getString("firstname");
		$lastname = JRequest::getString("lastname");
		$country = JRequest::getString("country");
		$email = JRequest::getString("email");
		$nl = JRequest::getString("nl");
		$lang = wh::lang();
		$result = $model->submitPrizeDraw($answer1,$answer2,$firstname,$lastname,$country ,$email,$nl);	
		$this->setRedirect("index.php?lang=".$lang."&option=com_thankyoupage&text_id=30"); // prizedraw hrg chequear el numero
	}
	
	public function submit_BT_draw(){
		$model = $this->getModel('bt'); 
		$answer1 = JRequest::getString('q1');
		$answer2 = JRequest::getString('q2');
		$answer3 = JRequest::getString('q3');
		$answer4 = JRequest::getString('q4_opt');
		$answer5 = JRequest::getString('q5');
		$answer6 = JRequest::getString('q6');
		$firstname = JRequest::getString("firstname");
		$lastname = JRequest::getString("lastname");
		$hotel = JRequest::getString("hotel");
		$position = JRequest::getString("position");
		$email = JRequest::getString("email");
		$nl = JRequest::getString("nl");
		$lang = wh::lang();
		$result = $model->submitPrizeDraw($answer1,$answer2,$answer3,$answer4,$answer5,$answer6,$firstname,$lastname,$hotel,$position,$email,$nl);	
		$this->setRedirect('index.php?lang='.$lang.'&option=com_thankyoupage&text_id=23'); // prizedraw bt chequear el numero
	}
	public function submit_ta_draw(){

		$model = $this->getModel('ta'); 
		$answer1 = JRequest::getString('q1_opt');
		$answer2 = JRequest::getString('q2_opt');
		$answer3 = JRequest::getString('q3_opt');
		$answer4 = JRequest::getString('q4_opt');
		$answer5 = JRequest::getString('q5');
		$answer6 = JRequest::getString('q6');
		$firstname = JRequest::getString("firstname");
		$lastname = JRequest::getString("lastname");
		$country = JRequest::getString("country");
		$email = JRequest::getString("email");
		$nl = JRequest::getString("nl");
		$lang = wh::lang();
		$result = $model->submitPrizeDraw($answer1,$answer2,$answer3,$answer4,$answer5,$answer6,$firstname,$lastname,$country ,$email,$nl);	
		$this->setRedirect('index.php?lang='.$lang.'&option=com_thankyoupage&text_id=14'); // prizedraw hrg chequear el numero
	}
*/
}
