<?php
namespace GDO\Facebook;

use GDO\Core\GDO;
use GDO\Core\GDT_Char;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Text;
use GDO\Net\GDT_IP;
use GDO\User\GDO_User;
use GDO\User\GDT_User;
use GDO\User\GDT_UserType;

/**
 * Mapping of ProviderID to userid.
 * Mapping is only possible via username field.
 * Realname is used for users realname.
 *
 * @version 5.0
 *
 * @since 4.0
 * @author gizmore
 */
final class GDO_OAuthToken extends GDO
{

	/**
	 * Refresh login tokens and user association.
	 *
	 * @param string $token
	 * @param array $fbVars
	 *
	 * @return GDO_User
	 */
	public static function refresh($token, array $fbVars, $provider = 'FB')
	{
		# Provider data
		$id = $fbVars['id'];
		$email = @$fbVars['email'];
		$displayName = $fbVars['name'];

		$name = "-$provider-$id"; # Build ProviderUsername
		if (!($user = GDO_User::getByName($name))) # And get by name
		{
			# Not found => Create with fb data
			$user = GDO_User::blank([
				'user_type' => GDT_UserType::MEMBER,
				'user_email' => $email,
				'user_name' => $name,
// 				'user_real_name' => $displayName,
// 				'user_password' => $provider,
				'user_register_ip' => GDT_IP::current(),
			])->insert();
			$user->tempSet('justActivated', true);
		}

		# Update mapping
		self::blank([
			'oauth_id' => $id,
			'oauth_provider' => $provider,
			'oauth_user' => $user->getID(),
			'oauth_token' => $token,
		])->replace();

		return $user;
	}

	public function gdoColumns(): array
	{
		return [
			GDT_Char::make('oauth_provider')->ascii()->caseS()->length(2)->primary(),
			GDT_String::make('oauth_id')->ascii()->caseS()->max(32)->primary(),
			GDT_User::make('oauth_user')->notNull(),
			GDT_Text::make('oauth_token')->utf8()->caseS()->max(4096),
		];
	}

	/**
	 * @return GDO_User
	 */
	public function getUser() { return $this->gdoValue('oauth_user'); }

	public function getUserID() { return $this->gdoVar('oauth_user'); }

	public function getToken() { return $this->gdoVar('oauth_token'); }

}
