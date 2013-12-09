<?php
namespace TYPO3\Setup\Core;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Setup".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;

/**
 * Rendering class for displaying messages before the Flow proxy classes are built.
 *
 * Because this class is extremely low-level, we cannot rely on most of Flow's
 * magic: There are no caches built yet, no resources published and the object
 * manager is not yet initialized. Only package management is loaded so far.
 *
 * @Flow\Proxy(false)
 * @Flow\Scope("singleton")
 */
class MessageRenderer {

	/**
	 * @var \TYPO3\Flow\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * Constructor.
	 *
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap
	 */
	public function __construct(\TYPO3\Flow\Core\Bootstrap $bootstrap) {
		$this->bootstrap = $bootstrap;
	}

	/**
	 * Display a message. As we cannot rely on any Flow requirements being fulfilled here,
	 * we have to statically include the CSS styles at this point, and have to in-line the TYPO3 logo.
	 *
	 * @param \TYPO3\Flow\Error\Message $message
	 * @param string $extraHeaderHtml extra HTML code to include at the end of the head tag
	 * @return void This method never returns.
	 */
	public function showMessage(Message $message, $extraHeaderHtml = '') {
		/** @var \TYPO3\Flow\Package\PackageManagerInterface $packageManager */
		$packageManager = $this->bootstrap->getEarlyInstance('TYPO3\Flow\Package\PackageManagerInterface');

		$css = '';
		if ($packageManager->isPackageAvailable('TYPO3.Twitter.Bootstrap')) {
			$css .= file_get_contents($packageManager->getPackage('TYPO3.Twitter.Bootstrap')->getResourcesPath() . 'Public/3/css/bootstrap.min.css');
			$css = str_replace('url(\'../', 'url(\'_Resources/Static/Packages/TYPO3.Twitter.Bootstrap/3.0/', $css);
		}
		if ($packageManager->isPackageAvailable('TYPO3.Setup')) {
			$css .= file_get_contents($packageManager->getPackage('TYPO3.Setup')->getResourcesPath() . 'Public/Styles/Setup.css');
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

		$messageBody = $message->render();
		if ($message->getSeverity() !== Message::SEVERITY_OK && $message->getCode() !== NULL) {
			$messageBody .= ' (<a href="http://typo3.org/go/exception/' . $message->getCode() . '">More information</a>)';
		}

		echo sprintf('
			<div class="logo"></div>
			<div class="well">
				<div class="container">
					<ul class="breadcrumb">
						<li><a class="active">TYPO3 Setup</a></li>
					</ul>
					<h3>%s</h3>
					<div class="t3-module-container indented">
						<div class="alert alert-%s">
							<span class="glyphicon glyphicon-refresh glyphicon-spin"></span>
							%s
						</div>
					</div>
				</div>
			</div>
			', $message->getTitle(), $severity, $messageBody);
		echo '</body></html>';
		exit(0);
	}
}
