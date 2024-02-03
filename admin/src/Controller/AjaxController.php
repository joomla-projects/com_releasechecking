<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VDM\Component\Releasechecking\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Releasechecking Ajax Base Controller
 *
 * @since  1.6
 */
class AjaxController extends BaseController
{
    /**
     * Constructor.
     *
     * @param   array                 $config   An optional associative array of configuration settings.
     *                                          Recognized key values include 'name', 'default_task', 'model_path', and
     *                                          'view_path' (this list is not meant to be comprehensive).
     * @param   ?MVCFactoryInterface  $factory  The factory.
     * @param   ?CMSApplication       $app      The Application for the dispatcher
     * @param   ?Input                $input    Input
     *
     * @since   3.0
     */
    public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		// make sure all json stuff are set
		$this->app->getDocument()->setMimeEncoding( 'application/json' );
		$this->app->setHeader('Content-Disposition','attachment;filename="getajax.json"');
		$this->app->setHeader('Access-Control-Allow-Origin', '*');
		// load the tasks 
		$this->registerTask('getAction', 'ajax');
		$this->registerTask('getActionDescription', 'ajax');
	}

	public function ajax()
	{
		// get the user for later use
		$user         = $this->app->getIdentity();
		// get the input values
		$jinput       = $this->input ?? $this->app->input;
		// check if we should return raw (DEFAULT TRUE SINCE J4)
		$returnRaw    = $jinput->get('raw', true, 'BOOLEAN');
		// return to a callback function
		$callback     = $jinput->get('callback', null, 'CMD');
		// Check Token!
		$token        = Session::getFormToken();
		$call_token   = $jinput->get('token', 0, 'ALNUM');
		if($jinput->get($token, 0, 'ALNUM') || $token === $call_token)
		{
			// get the task
			$task = $this->getTask();
			switch($task)
			{
				case 'getAction':
					try
					{
						$contextValue = $jinput->get('context', NULL, 'INT');
						$joomla_versionValue = $jinput->get('joomla_version', NULL, 'INT');
						$current_idValue = $jinput->get('current_id', NULL, 'INT');
						if($contextValue && $user->id != 0 && $joomla_versionValue)
						{
							$ajaxModule = $this->getModel('ajax', 'Administrator');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getAction($contextValue, $joomla_versionValue, $current_idValue);
							}
							else
							{
								$result = ['error' => 'There was an error! [149]'];
							}
						}
						else
						{
							$result = ['error' => 'There was an error! [149]'];
						}
						if($callback)
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(\Exception $e)
					{
						if($callback)
						{
							echo $callback."(".json_encode($e).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($e);
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
				case 'getActionDescription':
					try
					{
						$actionValue = $jinput->get('action', NULL, 'INT');
						if($actionValue && $user->id != 0)
						{
							$ajaxModule = $this->getModel('ajax', 'Administrator');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getActionDescription($actionValue);
							}
							else
							{
								$result = ['error' => 'There was an error! [149]'];
							}
						}
						else
						{
							$result = ['error' => 'There was an error! [149]'];
						}
						if($callback)
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(\Exception $e)
					{
						if($callback)
						{
							echo $callback."(".json_encode($e).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($e);
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
			}
		}
		else
		{
			// return to a callback function
			if($callback)
			{
				echo $callback."(".json_encode(['error' => 'There was an error! [129]']).");";
			}
			elseif($returnRaw)
			{
				echo json_encode(['error' => 'There was an error! [129]']);
			}
			else
			{
				echo "(".json_encode(['error' => 'There was an error! [129]']).");";
			}
		}
	}
}
