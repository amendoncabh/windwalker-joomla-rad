<?php
/**
 * @package        {ORGANIZATION}.Module
 * @subpackage     {{extension.element.lower}}
 * @copyright      Copyright (C) 2016 {ORGANIZATION}, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later.
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * Module helper to provides some useful methods.
 *
 * @since 1.0s
 */
abstract class Mod{{extension.name.cap}}Helper
{
	/**
	 * Columns cache.
	 *
	 * @var array.
	 */
	protected static $columns;

	/**
	 * Get select query from tables array.
	 *
	 * @param   array   $tables Tables name to get columns.
	 * @param   boolean $all    Contain a.*, b.* etc.
	 *
	 * @return  array  Select column list.
	 */
	public static function getSelectList($tables = array(), $all = true)
	{
		$db = Factory::getDbo();

		$select = array();
		$fields = array();
		$i      = 'a';

		foreach ($tables as $k => $table)
		{
			if (empty(self::$columns[$table]))
			{
				self::$columns[$table] = $db->getTableColumns($table);
			}

			$columns = self::$columns[$table];

			if ($all)
			{
				$select[] = "`{$k}`.*";
			}

			foreach ($columns as $key => $var)
			{
				$fields[] = $db->qn("{$k}.{$key}", "{$k}_{$key}");
			}

			$i = ord($i);
			$i++;
			$i = chr($i);
		}

		return $final = implode(",", $select) . ",\n" . implode(",\n", $fields);
	}

	/**
	 * Escape text for safe.
	 *
	 * @param string $text Text to escape.
	 *
	 * @return  string  Escaped text.
	 */
	public static function escape($text)
	{
		return htmlspecialchars($text);
	}
}
