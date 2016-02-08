<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/models/category.php';
 
/**
 * HTML Contact View class for the Contact component
 *
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @since 		1.5
 */
class ContactViewPublic extends JViewLegacy
{
	protected $state;
	protected $form;
	protected $item;
	protected $return_page;

	function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$dispatcher = JDispatcher::getInstance();
		$user_id	= $this->get('id');
		$loggedUser		= JFactory::getUser();
        $loggedUserId = $loggedUser->get('id');
		
		
		if (isset($_GET["id"])){
            $user = ideary::getUserById($_GET["id"], $loggedUserId);
			$userextra = Ideary::getExtraUserData($_GET["id"]);
			$this->assignRef('userextra', $userextra);
		}
		
        $texts = ideary::getTextsByUserId($_GET["id"], $loggedUserId);
		
		$yes_clapped_texts = ideary::getUserPermission('clap_texts_visib', $_GET["id"], $loggedUserId);
        $this->assignRef('texts', $texts);
        $this->assignRef('yes_clapped_texts', $yes_clapped_texts);

		
		$this->assignRef('return', $return);
		$this->assignRef('user', $user);
        $this->assignRef('loggedUser', $loggedUser);
        $this->assignRef('lgid', $loggedUserId);
		
		$this->document->setTitle($user->name);
		
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else {
			$this->params->def('page_heading', JText::_('COM_CONTACT_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this contact
		if ($menu && ($menu->query['option'] != 'com_contact' || $menu->query['view'] != 'contact' || $id != $this->item->id))
		{

			// If this is not a single contact menu item, set the page title to the contact title
			if ($this->item->name) {
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->contact->name, 'link' => ''));
			$category = JCategories::getInstance('Contact')->get($this->contact->catid);

			while ($category && ($menu->query['option'] != 'com_contact' || $menu->query['view'] == 'contact' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => ContactHelperRoute::getCategoryRoute($this->contact->catid));
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		if (empty($title)) {
			$title = $this->item->name;
		}
		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		$mdata = $this->item->metadata->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}
	}
}
