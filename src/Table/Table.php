<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Table;

use JTable;
use Windwalker\DI\Container;
use Windwalker\Relation\Relation;

/**
 * Windwalker active record Table.
 *
 * @since 2.0
 */
class Table extends \JTable
{
	/**
	 * Property _relation.
	 *
	 * @var  Relation
	 */
	public $_relation;

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   string           $table  Name of the table to model.
	 * @param   mixed            $key    Name of the primary key field in the table or array of field names that compose the primary key.
	 * @param   \JDatabaseDriver $db     JDatabaseDriver object.
	 */
	public function __construct($table, $key = 'id', $db = null)
	{
		$db = $db ?: Container::getInstance()->get('db');

		parent::__construct($table, $key, $db);

		// Prepare Relation handler
		$this->_relation = new Relation($this);

		$this->configure();
	}

	/**
	 * Configure this table.
	 *
	 * This method will run after \Windwalker\Table\Table::__construct().
	 *
	 * @return  void
	 *
	 * @since   2.1
	 */
	protected function configure()
	{
		// Do some stuff
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @throws  \InvalidArgumentException
	 * @throws  \RuntimeException
	 * @throws  \UnexpectedValueException
	 */
	public function load($keys = null, $reset = true)
	{
		if (!($result = parent::load($keys, $reset)))
		{
			return $result;
		}

		$this->_relation->load();

		return $result;
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		if (property_exists($this, 'params') && !empty($this->params))
		{
			if (is_array($this->params) || is_object($this->params))
			{
				$this->params = json_encode($this->params);
			}
		}

		if (!$result = parent::store($updateNulls))
		{
			return $result;
		}

		$this->_relation->store();

		return $result;
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  \UnexpectedValueException
	 */
	public function delete($pk = null)
	{
		if (get_called_class() == 'Windwalker\Table\Table')
		{
			return parent::delete($pk);
		}

		$table = clone $this;
		$table->load($pk);

		if (!$result = parent::delete($pk))
		{
			return $result;
		}

		$table->_relation->delete();

		return $result;
	}

	/**
	 * Magic method to get property and avoid errors.
	 *
	 * @param   string  $name  The property name to get.
	 *
	 * @return  mixed  Value of this property.
	 */
	public function __get($name)
	{
		if (property_exists($this, $name))
		{
			return $this->$name;
		}

		return null;
	}

	/**
	 * Get table.
	 *
	 * @param string $name
	 * @param string $prefix
	 *
	 * @return  Table
	 */
	public function getTable($name = null, $prefix = null)
	{
		if (!$name && !$prefix)
		{
			$table = clone $this;
			$table->reset();

			foreach ($table->_tbl_keys as $key)
			{
				$table->$key = null;
			}

			return $table;
		}

		$ref = new \ReflectionClass($this);

		$className = explode('Table', $ref->getShortName());

		if (count($className) >= 2)
		{
			$tablePrefix = $className[0] . 'Table';
			$tableName = $className[1];
		}
		else
		{
			$tablePrefix = 'JTable';
			$tableName = $className[0];
		}

		$name = $name ? : $tableName;
		$prefix = $prefix ? : $tablePrefix;

		return JTable::getInstance($name, $prefix);
	}
}
