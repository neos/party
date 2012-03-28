<?php
namespace TYPO3\Setup\Core;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Setup".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Special setup bootstrap class
 *
 * @api
 * @FLOW3\Proxy(false)
 * @FLOW3\Scope("singleton")
 */
class Bootstrap {

	/**
	 * Required PHP version
	 */
	const MINIMUM_PHP_VERSION = '5.3.2';
	const MAXIMUM_PHP_VERSION = '5.99.9';

	/**
	 * @var array
	 */
	protected $requiredExtensions = array(
		'Reflection' => 1329403179,
		'tokenizer' => 1329403180,
		'json' => 1329403181,
		'session' => 1329403182,
		'ctype' => 1329403183,
		'dom' => 1329403184,
		'date' => 1329403185,
		'libxml' => 1329403186,
		'xmlreader' => 1329403187,
		'xmlwriter' => 1329403188,
		'SimpleXML' => 1329403189,
		'openssl' => 1329403190,
		'pcre' => 1329403191,
		'zlib' => 1329403192,
		'filter' => 1329403193,
		'SPL' => 1329403194,
		'iconv' => 1329403195,
		'PDO' => 1329403196,
		'hash' => 1329403198
	);

	/**
	 * @var array
	 */
	protected $requiredFunctions = array(
		'system' => 1330707108,
		'shell_exec' => 1330707133,
		'escapeshellcmd' => 1330707156,
		'escapeshellarg' => 1330707177
	);

	/**
	 * @var array
	 */
	protected $requiredWritableFolders = array('Configuration', 'Data', 'Packages', 'Web');

	/**
	 * @var string
	 */
	protected $rootPath;

	/**
	 * Constructor
	 *
	 * @param string $rootPath
	 */
	public function __construct($rootPath) {
		$this->rootPath = pathinfo(pathinfo($rootPath, PATHINFO_DIRNAME), PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
	}

	/**
	 * @return void
	 */
	public function run() {
			// TODO: Check package state instead
		if (!file_exists($this->rootPath . 'enable_setup')) {
			$this->error('Setup not enabled', sprintf('Create a file called "enable_setup" in your rootdir (%s)', $this->rootPath));
		}
		if ($requiredEnvironmentError = $this->ensureRequiredEnvironment()) {
			$this->error('Environment did not meet requirements', $requiredEnvironmentError);
		}
		if ($filePermissionsError = $this->checkFilePermissions()) {
			$this->error('Problem with file permissions', $filePermissionsError);
		}

		header('Location: setup');
	}

	/**
	 * @param string $title
	 * @param string $error
	 * @return void
	 */
	protected function error($title, $error) {
		echo '<link rel="stylesheet" type="text/css" href="/_Resources/Static/Packages/TYPO3.Setup/StyleSheet/Setup.css" />';
		echo '<link rel="stylesheet" type="text/css" href="/_Resources/Static/Packages/Twitter.Bootstrap/css/bootstrap.min.css" />';
		echo sprintf('
			<h1>%s</h1>
			<br />
			<div class="alert alert-error">
				<h3>%s</h3>
			</div>
			', $title, $error);
		exit(0);
	}

	/**
	 * Checks PHP version and other parameters of the environment
	 *
	 * @return mixed
	 */
	protected function ensureRequiredEnvironment() {
		if (version_compare(phpversion(), self::MINIMUM_PHP_VERSION, '<')) {
			return 'FLOW3 requires PHP version ' . self::MINIMUM_PHP_VERSION . ' or higher but your installed version is currently ' . phpversion() . '. (Error #1172215790)';
		}
		if (version_compare(PHP_VERSION, self::MAXIMUM_PHP_VERSION, '>')) {
			return 'FLOW3 requires PHP version ' . self::MAXIMUM_PHP_VERSION . ' or lower but your installed version is currently ' . PHP_VERSION . '. (Error #1172215790)';
		}
		if (version_compare(PHP_VERSION, '6.0.0', '<') && !extension_loaded('mbstring')) {
			return 'FLOW3 requires the PHP extension "mbstring" for PHP versions below 6.0.0 (Error #1207148809)' . PHP_EOL;
		}
		if (DIRECTORY_SEPARATOR !== '/' && PHP_WINDOWS_VERSION_MAJOR < 6) {
			return 'FLOW3 does not support Windows versions older than Windows Vista or Windows Server 2008 (Error #1312463704)';
		}
		foreach ($this->requiredExtensions as $extension => $errorKey) {
			if (!extension_loaded($extension)) {
				return sprintf('FLOW3 requires the PHP extension "%s" (Error #%u)', $extension, $errorKey);
			}
		}
		foreach ($this->requiredFunctions as $function => $errorKey) {
			if (!function_exists($function)) {
				return sprintf('FLOW3 requires the PHP function "%s" (Error #%u)', $function, $errorKey);
			}
		}

		// Check for database drivers? PDO::getAvailableDrivers()

		$method = new \ReflectionMethod(__CLASS__, __FUNCTION__);
		$docComment = $method->getDocComment();
		if ($docComment === FALSE || $docComment === '') {
			return 'Reflection of doc comments is not supported by your PHP setup. Please check if you have installed an accelerator which removes doc comments. (Error #1329405326)';
		}

		set_time_limit(0);

		if (version_compare(PHP_VERSION, '5.4', '<')) {
			if (get_magic_quotes_gpc() === 1) {
				return 'FLOW3 requires the PHP setting "magic_quotes_gpc" set to off (Error #1224003190)';
			}
			if (ini_get('register_globals')) {
				return 'FLOW3 requires the PHP setting "register_globals" set to off (Error #1224003190)';
			}
		}

		if (ini_get('session.auto_start')) {
			return 'FLOW3 requires the PHP setting "session.auto_start" set to off (Error #1224003190)';
		}

		return NULL;
	}

	/**
	 * Check write permissions for folders used for writing files
	 *
	 * @return mixed
	 */
	protected function checkFilePermissions() {
		foreach ($this->requiredWritableFolders as $folder) {
			$folderPath = $this->rootPath . $folder;
			if (!is_dir($folderPath) && !\TYPO3\FLOW3\Utility\Files::is_link($folderPath)) {
				try {
					\TYPO3\FLOW3\Utility\Files::createDirectoryRecursively($folderPath);
				} catch(\TYPO3\FLOW3\Utility\Exception $e) {
					return sprintf('An error occured when trying to create folder "%s" (Error #1330363887)', $folderPath);
				}
			}
			if (!is_writable($folderPath)) {
				return sprintf('The folder "%s" is not writable (Error #1330372964)', $folderPath);
			}
		}
		return NULL;
	}

}
?>