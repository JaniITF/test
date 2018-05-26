<?php
/**
 * @version    1.0.0
 * @package    Com_Lpdfparser
 * @author     syed <shabbir4it@gmail.com>
 * @copyright  2017 syed
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_lpdfparser'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}
// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Lpdfparser', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Lpdfparser');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();  




 
