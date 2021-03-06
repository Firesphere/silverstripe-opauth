<?php

/**
 * Base authenticator for SilverStripe Opauth module.
 *
 * @author Will Morgan <@willmorgan>
 * @author Dan Hensby <@dhensby>
 */
class OpauthAuthenticator extends MemberAuthenticator {

	private static
		/**
		 * @config array The enabled strategy classes for Opauth
		 */
		$enabled_strategies = array(),
		/**
		 * @config string
		 */
		$opauth_security_salt,
		/**
		 * @var Opauth Persistent Opauth instance.
		 */
		$opauth;

	/**
	 * get_enabled_strategies
	 * @return array Enabled strategies set in _config
	 */
	public static function get_enabled_strategies() {
		return self::config()->enabled_strategies;
	}

	/**
	 * get_opauth_config
	 * @param array Any extra overrides
	 * @return array Config for use with Opauth
	 */
	public static function get_opauth_config($mergeConfig = array()) {
		$config = self::config();
		return array_merge(
			array(
				'path' => OpauthController::get_path(),
				'callback_url' => OpauthController::get_callback_path(),
				'security_salt' => $config->opauth_security_salt,
				'security_iteration' => $config->opauth_security_iteration,
				'security_timeout' => $config->opauth_security_timeout,
				'callback_transport' => $config->opauth_callback_transport,
				'Strategy' => $config->opauth_strategy_config,
			),
			$mergeConfig
		);
	}

	/**
	 * opauth
	 * @param boolean $autoRun Should Opauth auto run? Default: false
	 * @return Opauth The Opauth instance. Isn't it easy to typo this as Opeth?
	 */
	public static function opauth($autoRun = false, $config = array()) {
		if(!isset(self::$opauth)) {
			self::$opauth = new Opauth(self::get_opauth_config($config), $autoRun);
		}
		return self::$opauth;
	}

	/**
	 * get_strategy_segment
	 * Works around Opauth's weird URL scheme - GoogleStrategy => /google/
	 * @return string
	 */
	public static function get_strategy_segment($strategy) {
		return preg_replace('/(strategy)$/', '', strtolower($strategy));
	}

	/**
	 * @return OpauthLoginForm
	 */
	public static function get_login_form(Controller $controller) {
		return new OpauthLoginForm($controller, 'LoginForm');
	}

	/**
	 * Get the name of the authentication method
	 *
	 * @return string Returns the name of the authentication method.
	 */
	public static function get_name() {
		return _t('OpauthAuthenticator.TITLE', 'Social Login');
	}

}
