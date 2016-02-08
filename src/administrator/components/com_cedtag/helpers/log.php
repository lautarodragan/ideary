<?php
/**
 * @package Component cedTag for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.log.log');

class CedTagLog
{

    public static function debug($message)
    {
        JLog::addLogger(array(
            'text_file' => 'cedtag.php'
        ), JLog::ALL, 'com_cedtag');

        if (CedTagLog::param('debug', 0)) {
            JLog::add($message, JLog::DEBUG, 'com_cedtag');
        }

    }

    public static function error($message)
    {
        JLog::addLogger(array(
            'text_file' => 'cedtag.php'
        ), JLog::ALL, 'com_cedtag');

        JLog::add($message, JLog::ERROR, 'com_cedtag');
    }

    private static function  param($name, $default = '')
    {
        static $params;
        if (!isset($params)) {
            $params = JComponentHelper::getParams('com_cedtag');
        }

        return $params->get($name, $default);
    }

}
