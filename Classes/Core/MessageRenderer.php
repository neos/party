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
use TYPO3\FLOW3\Error\Message;

/**
 * Rendering class for displaying messages before the FLOW3 proxy classes are built.
 *
 * Because this class is extremely low-level, we cannot rely on most of FLOW3's
 * magic: There are no caches built yet, no resources published and the object
 * manager is not yet initialized. Only package management is loaded so far.
 *
 * @FLOW3\Proxy(false)
 * @FLOW3\Scope("singleton")
 */
class MessageRenderer {

	/**
	 * @var \TYPO3\FLOW3\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * Constructor.
	 *
	 * @param \TYPO3\FLOW3\Core\Bootstrap $bootstrap
	 */
	public function __construct(\TYPO3\FLOW3\Core\Bootstrap $bootstrap) {
		$this->bootstrap = $bootstrap;
	}

	/**
	 * Display a message. As we cannot rely on any FLOW3 requirements being fulfilled here,
	 * we have to statically include the CSS styles at this point, and have to in-line the TYPO3 logo.
	 *
	 * @param \TYPO3\FLOW3\Error\Message $message
	 * @param string $extraHeaderHtml extra HTML code to include at the end of the head tag
	 * @return void This method never returns.
	 */
	public function showMessage(Message $message, $extraHeaderHtml = '') {
		$packageManager = $this->bootstrap->getEarlyInstance('TYPO3\FLOW3\Package\PackageManagerInterface');

		$css = '';
		if ($packageManager->isPackageAvailable('TYPO3.Setup')) {
			$css .= file_get_contents($packageManager->getPackage('TYPO3.Setup')->getResourcesPath() . 'Public/StyleSheet/Setup.css');

			$logoImage = file_get_contents($packageManager->getPackage('TYPO3.Setup')->getResourcesPath() . 'Public/Images/TYPO3_logo.png');
			$css = str_replace('url(\'../Images/TYPO3_logo.png\')', 'url(data:image/png;base64,'. base64_encode($logoImage) .')', $css);
		}
		if ($packageManager->isPackageAvailable('Twitter.Bootstrap')) {
			$css .= file_get_contents($packageManager->getPackage('Twitter.Bootstrap')->getResourcesPath() . 'Public/2/css/bootstrap.min.css');
		}

		echo '<html>';
		echo '<head>';
		echo '<title>' . $message->getTitle() . '</title>';
		echo '<style type="text/css">';
		echo $css;
		echo '</style>';
		echo $extraHeaderHtml;
		echo '</head>';
		echo '<body>';

		switch ($message->getSeverity()) {
			case Message::SEVERITY_ERROR:
				$severity = 'error';
				break;
			case Message::SEVERITY_WARNING:
				$severity = 'warning';
				break;
			case Message::SEVERITY_OK:
				$severity = 'success';
				break;
			case Message::SEVERITY_NOTICE:
			default:
				$severity = 'info';
				break;
		}


		echo sprintf('
			<h1>%s</h1>
			<br />
			<div class="alert alert-%s">
				%s %s
			</div>
			', $message->getTitle(), $severity, $message->render(), ($message->getSeverity() !== Message::SEVERITY_OK ? '(#' . $message->getCode() . ')' : ''));
		echo '</body></html>';
		exit(0);
	}
}
?>