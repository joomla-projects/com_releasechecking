<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */



// register additional namespace
\spl_autoload_register(function ($class) {
	// project-specific base directories and namespace prefix
	$search = [
		'libraries/jcb_powers/VDM.Joomla' => 'VDM\\Joomla'
	];
	// Start the search and load if found
	$found = false;
	$found_base_dir = "";
	$found_len = 0;
	foreach ($search as $base_dir => $prefix)
	{
		// does the class use the namespace prefix?
		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) === 0)
		{
			// we have a match so load the values
			$found = true;
			$found_base_dir = $base_dir;
			$found_len = $len;
			// done here
			break;
		}
	}
	// check if we found a match
	if (!$found)
	{
		// not found so move to the next registered autoloader
		return;
	}
	// get the relative class name
	$relative_class = substr($class, $found_len);
	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = JPATH_ROOT . '/' . $found_base_dir . '/src' . str_replace('\\', '/', $relative_class) . '.php';
	// if the file exists, require it
	if (file_exists($file))
	{
		require $file;
	}
});

// (soon) use Joomla\CMS\Association\AssociationExtensionInterface;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use VDM\Component\Releasechecking\Administrator\Extension\ReleasecheckingComponent;
// (soon) use VDM\Component\Releasechecking\Administrator\Helper\AssociationsHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * The VDM Releasechecking service provider.
 *
 * @since  4.0.0
 */
return new class () implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function register(Container $container)
	{
		// (soon) $container->set(AssociationExtensionInterface::class, new AssociationsHelper());

		$container->registerServiceProvider(new CategoryFactory('\\VDM\\Component\\Releasechecking'));
		$container->registerServiceProvider(new MVCFactory('\\VDM\\Component\\Releasechecking'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\VDM\\Component\\Releasechecking'));
		$container->registerServiceProvider(new RouterFactory('\\VDM\\Component\\Releasechecking'));

		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new ReleasecheckingComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
				// (soon) $component->setAssociationExtension($container->get(AssociationExtensionInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};
