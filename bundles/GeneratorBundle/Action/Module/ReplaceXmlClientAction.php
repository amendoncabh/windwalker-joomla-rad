<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace GeneratorBundle\Action\Module;

use GeneratorBundle\Action\AbstractAction;
use Windwalker\Filesystem\File;
use Windwalker\String\SimpleTemplate;

/**
 * Class ReplaceXmlClientAction
 *
 * @since 1.0
 */
class ReplaceXmlClientAction extends AbstractAction
{
	/**
	 * doExecute
	 *
	 * @return  mixed
	 */
	protected function doExecute()
	{
		$xml = $this->config['dir.dest'] . '/{{extension.element.lower}}.xml';
		
		$content = file_get_contents($xml);

		$content = str_replace('client="site"', '{{module.client}}', $content);

		File::write($xml, $content);
	}
}
