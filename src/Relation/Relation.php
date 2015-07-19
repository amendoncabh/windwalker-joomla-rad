<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation;

use Windwalker\Relation\Handler\OneToManyRelation;
use Windwalker\Relation\Handler\OneToOneRelation;
use Windwalker\Relation\Handler\RelationHandlerInterface;
use Windwalker\Table\Table;

/**
 * The Relation handler object. This is a composite object to combine multiple relation handlers.
 * 
 * @since  {DEPLOY_VERSION}
 */
class Relation implements RelationHandlerInterface
{
	/**
	 * Property parent.
	 *
	 * @var  Table
	 */
	protected $parent;

	/**
	 * Property prefix.
	 *
	 * @var  string
	 */
	protected $prefix;

	/**
	 * Property relations.
	 *
	 * @var  RelationHandlerInterface[]
	 */
	protected $relations = array();

	/**
	 * Class init.
	 *
	 * @param Table $parent
	 */
	public function __construct(Table $parent, $prefix = 'JTable')
	{
		$this->parent = $parent;
	}

	/**
	 * Add one to many relation configurations.
	 *
	 * @param string  $field     Field of parent table to store children.
	 * @param \JTable $table     The Table object of this relation child.
	 * @param array   $fks       Foreign key mapping.
	 * @param string  $onUpdate  The action of ON UPDATE operation.
	 * @param string  $onDelete  The action of ON DELETE operation.
	 * @param array   $options   Some options to configure this relation.
	 *
	 * @return  static
	 */
	public function addOneToMany($field, $table, $fks = array(), $onUpdate = Action::CASCADE, $onDelete = Action::CASCADE,
		$options = array())
	{
		if (!($table instanceof \JTable))
		{
			$table = $this->getTable($table, $this->prefix);
		}

		$relation = new OneToManyRelation($this->parent, $field, $table, $fks, $onUpdate, $onDelete, $options);

		$relation->setParent($this->parent);

		$this->relations[$field] = $relation;

		return $this;
	}

	/**
	 * Add one to many relation configurations.
	 *
	 * @param string  $field     Field of parent table to store children.
	 * @param \JTable $table     The Table object of this relation child.
	 * @param array   $fks       Foreign key mapping.
	 * @param string  $onUpdate  The action of ON UPDATE operation.
	 * @param string  $onDelete  The action of ON DELETE operation.
	 * @param array   $options   Some options to configure this relation.
	 *
	 * @return  static
	 */
	public function addOneToOne($field, $table, $fks = array(), $onUpdate = Action::CASCADE, $onDelete = Action::CASCADE,
		$options = array())
	{
		if (!($table instanceof \JTable))
		{
			$table = $this->getTable($table, $this->prefix);
		}

		$relation = new OneToOneRelation($this->parent, $field, $table, $fks, $onUpdate, $onDelete, $options);

		$relation->setParent($this->parent);

		$this->relations[$field] = $relation;

		return $this;
	}

	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load()
	{
		foreach ($this->relations as $relation)
		{
			$relation->load();
		}
	}

	/**
	 * Store all relative children data.
	 *
	 * The onUpdate option will work in this method.
	 *
	 * @return  void
	 */
	public function store()
	{
		foreach ($this->relations as $relation)
		{
			$relation->store();
		}
	}

	/**
	 * Delete all relative children data.
	 *
	 * The onDelete option will work in this method.
	 *
	 * @return  void
	 */
	public function delete()
	{
		foreach ($this->relations as $relation)
		{
			$relation->delete();
		}
	}

	/**
	 * Get Table object.
	 *
	 * @param   string  $table   The table name.
	 * @param   string  $prefix  The table class prefix.
	 *
	 * @return  \JTable
	 */
	protected function getTable($table, $prefix = null)
	{
		if (!is_string($table))
		{
			throw new \InvalidArgumentException('Table name should be string.');
		}

		if ($table = \JTable::getInstance($table, $prefix ? : $this->prefix))
		{
			return $table;
		}

		return new Table($table);
	}
}
