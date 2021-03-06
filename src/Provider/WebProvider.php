<?php

namespace Windwalker\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The web application provider.
 *
 * @since 2.0
 */
class WebProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 */
	public function register(Container $container)
	{
		// Application
		$container->alias('app', 'JApplicationCms')
			->share('JApplicationCms', array('JFactory', 'getApplication'));

		// Document
		$container->alias('document', 'JDocumentHtml')
			->share('JDocumentHtml', array('JFactory', 'getDocument'));

		// User
		$container->alias('user', 'JUser')
			->share('JUser', \Joomla\CMS\Factory::getUser());

		// Input
		$container->alias('input', 'JInput')
			->share('JInput', \Joomla\CMS\Factory::getApplication()->input);
	}
}
