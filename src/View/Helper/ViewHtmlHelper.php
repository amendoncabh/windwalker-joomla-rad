<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\View\Helper;

use Joomla\CMS\HTML\HTMLHelper;
use Windwalker\Data\Data;

/**
 * The View Html Helper
 *
 * @since 2.0
 */
class ViewHtmlHelper
{
	/**
	 * A quick function to show item information.
	 *
	 * @param   Data    $item  Item object.
	 * @param   string  $key   Information key.
	 * @param   string  $label Information label. If is null, will use JText.
	 * @param   string  $icon  Icon name for bootstrap icon.
	 * @param   string  $link  Has link URL?
	 * @param   string  $class Set class to this wrap.
	 *
	 * @return  string  Information HTML.
	 *
	 * @deprecated  3.0  Use FrontViewHelper::showInfo() instead.
	 */
	public static function showInfo($item, $key = null, $label = null, $icon = '', $link = null, $class = null)
	{
		if (empty($item->$key))
		{
			return false;
		}

		$label = \Joomla\CMS\Language\Text::_($label);
		$value = $item->$key;

		if ($link)
		{
			$value = HTMLHelper::_('link', $link, $value);
		}

		$class = str_replace('_', '-', $key) . ' ' . $class;

		$icon = $icon ? 'icon-' . $icon : '';

		$info = <<<INFO
		<div class="{$class}">
            <span class="label">
            <i class="{$icon}"></i>
            {$label}
            </span>
            <span class="value">{$value}</span>
        </div>
INFO;

		return $info;
	}
}
