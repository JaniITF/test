<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Model for component configuration
 *
 * @since  3.2
 */
class ConfigModelComponent extends ConfigModelForm
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 *
	 * @since	3.2
	 */
	protected function populateState()
	{
		$input = JFactory::getApplication()->input;

		// Set the component (option) we are dealing with.
		$component = $input->get('component');
		$state = $this->loadState();
		$state->set('component.option', $component);

		// Set an alternative path for the configuration file.
		if ($path = $input->getString('path'))
		{
			$path = JPath::clean(JPATH_SITE . '/' . $path);
			JPath::check($path);
			$state->set('component.path', $path);
		}

		$this->setState($state);
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since	3.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$state = $this->getState();
		$option = $state->get('component.option');

		if ($path = $state->get('component.path'))
		{
			// Add the search path for the admin component config.xml file.
			JForm::addFormPath($path);
		}
		else
		{
			// Add the search path for the admin component config.xml file.
			JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/' . $option);
		}

		// Get the form.
		$form = $this->loadForm(
			'com_config.component',
			'config',
			array('control' => 'jform', 'load_data' => $loadData),
			false,
			'/config'
		);

		if (empty($form))
		{
			return false;
		}

		$lang = JFactory::getLanguage();
		$lang->load($option, JPATH_BASE, null, false, true)
		|| $lang->load($option, JPATH_BASE . "/components/$option", null, false, true);

		return $form;
	}

	/**
	 * Get the component information.
	 *
	 * @return	object
	 *
	 * @since	3.2
	 */
	public function getComponent()
	{
		$state = $this->getState();
		$option = $state->get('component.option');

		// Load common and local language files.
		$lang = JFactory::getLanguage();
		$lang->load($option, JPATH_BASE, null, false, true)
		|| $lang->load($option, JPATH_BASE . "/components/$option", null, false, true);

		$result = JComponentHelper::getComponent($option);

		return $result;
	}

	/**
	 * Method to save the configuration data.
	 *
	 * @param   array  $data  An array containing all global config data.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since	3.2
	 * @throws  RuntimeException
	 */
	public function save($data)
	{
		// echo "<pre>";
		// print_r($_POST);
		// exit;
		
		$table      = JTable::getInstance('extension');
		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;
		JPluginHelper::importPlugin('extension');

		// Check super user group.
		if (isset($data['params']) && !JFactory::getUser()->authorise('core.admin'))
		{
			$form = $this->getForm(array(), false);

			foreach ($form->getFieldsets() as $fieldset)
			{
				foreach ($form->getFieldset($fieldset->name) as $field)
				{
					if ($field->type === 'UserGroupList' && isset($data['params'][$field->fieldname])
						&& (int) $field->getAttribute('checksuperusergroup', 0) === 1
						&& JAccess::checkGroup($data['params'][$field->fieldname], 'core.admin'))
					{
						throw new RuntimeException(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
					}
				}
			}
		}

		// Save the rules.
		if (isset($data['params']) && isset($data['params']['rules']))
		{
			$rules = new JAccessRules($data['params']['rules']);
			$asset = JTable::getInstance('asset');

			if (!$asset->loadByName($data['option']))
			{
				$root = JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = $data['option'];
				$asset->title = $data['option'];
				$asset->setLocation($root->id, 'last-child');
			}

			$asset->rules = (string) $rules;

			if (!$asset->check() || !$asset->store())
			{
				throw new RuntimeException($asset->getError());
			}

			// We don't need this anymore
			unset($data['option']);
			unset($data['params']['rules']);
		}

		// Load the previous Data
		if (!$table->load($data['id']))
		{
			throw new RuntimeException($table->getError());
		}

		unset($data['id']);

		// Bind the data.
		if (!$table->bind($data))
		{
			throw new RuntimeException($table->getError());
		}

		// Check the data.
		if (!$table->check())
		{
			throw new RuntimeException($table->getError());
		}

		$result = $dispatcher->trigger('onExtensionBeforeSave', array($context, $table, false));

			// Store the data.
		if (in_array(false, $result, true) || !$table->store())
		{
			throw new RuntimeException($table->getError());
		}

		// Trigger the after save event.
		$dispatcher->trigger('onExtensionAfterSave', array($context, $table, false));
		
		if(isset($_POST['jform'])!=''){	
		
	      
			if($_POST['component']=='com_lpdfparser'){

					$params = json_encode($_POST['jform']);
					
					$id = $_POST['id'];
					
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					// Fields to update.
					$fields = array(
						$db->quoteName('params') . ' = ' . $db->quote($params)
					);
					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('extension_id') . ' = '.$id , 
					);
					$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
					$db->setQuery($query);
					//echo($query->__toString());
					$result = $db->execute();
			}
			
			if(isset($_POST['jtemp'])){
				
				$hikashop_product = $_POST['jtemp']['hikashop_product'];
				foreach($hikashop_product as $key => $value){
					
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					// Fields to update.
					$fields = array(
						$db->quoteName('product_template_id') . ' = ' . $db->quote($_POST['jtemp']['template_name'][$key])
					);
					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('product_id') . ' = '.$value , 
					);
					$query->update($db->quoteName('#__hikashop_product'))->set($fields)->where($conditions);
					$db->setQuery($query);
					//echo($query->__toString());
					$result = $db->execute();
				}
				
				   $product_id = implode(',',$hikashop_product);
				   $query = $db->getQuery(true);
					// Fields to update.
					$fields = array(
						$db->quoteName('product_template_id') . ' = ' . $db->quote(0)
					);
					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('product_id') . ' NOT IN ('.$product_id.')' , 
					);
					$query->update($db->quoteName('#__hikashop_product'))->set($fields)->where($conditions);
					$db->setQuery($query);
					//echo($query->__toString());
					$result = $db->execute();
		   }
		}	
		
		// Clean the component cache.
		$this->cleanCache('_system', 0);
		$this->cleanCache('_system', 1);

		return true;
	}
}


