<?php
/**
 * @version    1.0.0
 * @package    Com_Lpdfparser
 * @author     syed <shabbir4it@gmail.com>
 * @copyright  2017 syed
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 $path = getcwd();

echo $path; 
echo 'This is pathcsdfs ';
exit; 

defined('_JEXEC') or die; 

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Lpdfparser', JPATH_COMPONENT);
JLoader::register('LpdfparserController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Lpdfparser');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
