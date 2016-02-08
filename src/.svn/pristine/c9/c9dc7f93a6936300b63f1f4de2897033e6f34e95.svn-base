<?php
/**
 *
 * @version   $Id: pageinfo.php 2354 2012-06-12 18:16:34Z silianacom-svn $
 * @copyright Copyright (C) 2010 Yannick Gaultier. All rights reserved.
 * @license   GNU/GPL, see LICENSE.php
 * Sh404sefClassShdb is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 * Class adding a few method to Joomla! default database class
 *
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die;

/**
 * Maintain data and handle requests about current
 * page. Accessed through factory:
 *
 * $liveSite = Sh404sefFactory::getPageInfo()->getDefaultLiveSite();
 *
 *
 * @author yannick
 *
 */
class Sh404sefClassPageinfo  {

  const LIVE_SITE_SECURE_IGNORE = 0;
  const LIVE_SITE_SECURE_YES = 1;
  const LIVE_SITE_SECURE_NO = -1;

  const LIVE_SITE_NOT_MOBILE = 0;
  const LIVE_SITE_MOBILE = 1;

  public $shURL = '';
  public $shCurrentPageNonSef = '';
  public $shCurrentPageURL = '';
  public $baseUrl = '';
  public $shMosConfig_locale = '';
  public $shMosConfig_shortcode = '';
  public $allLangHomeLink = '';
  public $homeLink = '';
  public $homeLinks = array();
  public $homeItemid = 0;
  public $isMobileRequest = self::LIVE_SITE_NOT_MOBILE;
  public $httpStatus = null;
  public $isMultilingual = null;
  public $pageCanonicalUrl = '';
  
  // pagination management
  public $paginationPrevLink = '';
  public $paginationNextLink = '';

  // store our router instance
  public $router = null;

  // this will be filled up upon startup by system plugin
  // code with the current detected base site url for the request
  // ie: it can be secure, unsecure, for one language or another
  protected $_defaultLiveSite = '';

  public function setDefaultLiveSite( $url) {
    $this->_defaultLiveSite = $url;
  }

  public function getDefaultLiveSite() {
    return $this->_defaultLiveSite;
  }

  public function getDefaultFrontLiveSite() {
    return str_replace( '/administrator', '', $this->_defaultLiveSite);
  }

  public function loadHomepages() {

    $app = JFactory::getApplication();
    if($app->isAdmin()) {
      return;
    }

    // store default links in each language
    jimport( 'joomla.language.helper');
    $languages	= JLanguageHelper::getLanguages();
    $this->isMultilingual = shIsMultilingual();
    $defaultLanguage = shGetDefaultLang();
    if($this->isMultilingual === false || $this->isMultilingual == 'joomla') {
      $menu = JFactory::getApplication()->getMenu();
      foreach( $languages as $language) {
        $menuItem = $menu->getDefault($language->lang_code);
        if(!empty($menuItem)) {
          $this->homeLinks[$language->lang_code] = $this->_prepareLink($menuItem);
          if($language->lang_code == $defaultLanguage) {
            $this->homeLink = $this->homeLinks[$language->lang_code];
            $this->homeItemid = $menuItem->id;
          }
        }
      }

      // find about the "All" languages home link
      $menuItem = $menu->getDefault( '*');
      if(!empty( $menuItem)) {
        $this->allLangHomeLink = $this->_prepareLink($menuItem, $languages);
      }
    } else {
      // trouble starts
      $db = JFactory::getDbo();
      $query = $db->getQuery( true);
      $query->select( 'id,language,link');
      $query->from( '#__menu');
      $query->where( 'home <> 0');
      $db->setQuery( $query);
      $items = $db->loadObjectList( 'language');
      if(!empty( $items)) {
        if( count( $items) == 1) {
          $tmp = array_values( $items);
          $defaultItem = $tmp[0];
        }
        if(empty( $defaultItem)) {
          $defaultItem = empty( $items[$defaultLanguage]) ? null : $items[$defaultLanguage];
        }
        if(empty( $defaultItem)) {
          $defaultItem = empty( $items['*']) ? null : $items['*'];
        }
        foreach( $languages as $language) {
          if(!empty($items[$language->lang_code])) {
            $this->homeLinks[$language->lang_code] = $this->_prepareLink( $items[$language->lang_code]);
          } else {
            // no menu item for home link
            // let's try to  build one
            $this->homeLinks[$language->lang_code] = $this->_prepareLink( $defaultItem, shGetIsoCodeFromName( $language->lang_code));
          }

          if($language->lang_code == $defaultLanguage) {
            $this->homeLink = $this->homeLinks[$language->lang_code];
            $this->homeItemid = $defaultItem->id;
            $this->allLangHomeLink = shCleanUpLang( $this->homeLinks[$language->lang_code]);
          }

        }
      }
    }
  }

  protected function _prepareLink( $menuItem, $forceLanguage = null) {

    $link = shSetURLVar( $menuItem->link, 'Itemid', $menuItem->id);
    $linkLang = shGetURLLang( $link);
    if(empty( $linkLang)) {
      // if no language in link, use current, except if 'All', in which case use actual default
      if(empty( $forceLanguage)) {
        $itemLanguage = $menuItem->language == '*' ? shGetDefaultLanguageSef() : shGetIsoCodeFromName($menuItem->language);
      } else {
        $itemLanguage = $forceLanguage;
      }
      $link = shSetUrlVar( $link, 'lang', $itemLanguage);
    }

    return $link;
  }

}