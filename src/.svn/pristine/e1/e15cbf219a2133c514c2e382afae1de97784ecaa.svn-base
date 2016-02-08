<?php
/**
 * @version		$Id: view.html.php 22265 2011-10-20 05:22:13Z github_bot $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class TextsViewDefault extends JView
{
public function display($tpl = null)
	{				
		$model = $this->getModel();
		$document = & JFactory::getDocument();	
		//$jsHelper = wh::staticUrl(false).'templates/atomic/js/helper.js';
		//$document->addScript($jsHelper);		
		
		$app = JFactory::getApplication();

			/*$content = wh::getPageContent('press_releases'); //logued in
			$img = wh::getPressReleaseImageHP();
			$this->assignRef('img', $img);			
			$this->assignRef('content', $content);			
			
			//breadcrumb
			global $bread;		
			$bread[0]['label'] = wh::label('PRESS_RELEASES_TITLE'); //guardar en labels
			//breadcrumb	
			
			*/
			parent::display($tpl);
	}
}
?>