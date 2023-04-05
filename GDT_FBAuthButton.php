<?php
declare(strict_types=1);
namespace GDO\Facebook;

use GDO\Core\Application;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Button;

/**
 * Login with Facebook button.
 *
 * @author gizmore
 */
final class GDT_FBAuthButton extends GDT_Button
{

	public function isTestable(): bool
	{
		return false;
	}

	protected function __construct()
	{
		parent::__construct();
		$this->name('btn_facebook');
		if (!Application::$INSTANCE->isCLIOrUnitTest())
		{
			$this->href($this->facebookURL());
		}
	}

	public function facebookURL()
	{
		return Module_Facebook::withDeprecation(function ()
		{
			$module = Module_Facebook::instance();
			$fb = $module->getFacebook();
			$helper = $fb->getRedirectLoginHelper();
			$permissions = ['email']; // Optional permissions
			$redirectURL = url('Facebook', 'Auth', '&connectFB=1');
			return $helper->getLoginUrl($redirectURL, $permissions);
		});
	}

	public function renderHTML(): string { return GDT_Template::php('Facebook', 'cell/fbauthbutton.php', ['field' => $this]); }

}
