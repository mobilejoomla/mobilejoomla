<?php
defined('_JEXEC') or die();

include_once dirname(__FILE__).'/classes/mjinstaller.php';

class Com_MobilejoomlaInstallerScript
{
	/**
	 * @param string $type
	 * @param JInstallerComponent $adapter
	 */
	function postflight($type, $adapter)
	{
		$path = $adapter->getParent()->getPath('source');
		$name = $adapter->get('name');
		$xml = $path.'/'.$name.'.xml';
		$xmlsrc = $path.'/'.$name.'.j2x.xml';
		if(JFile::exists($xmlsrc))
		{
			if(JFile::exists($xml))
				JFile::delete($xml);
			JFile::move($xmlsrc, $xml);
		}
		$adapter->getParent()->setPath('manifest', $xml);
	}

	/**
	 * @param JInstallerComponent $adapter
	 * @return bool
	 */
	function install($adapter)
	{
		return MjInstaller::install();
	}
	/**
	 * @param JInstallerComponent $adapter
	 * @return bool
	 */
	function update($adapter)
	{
		return MjInstaller::install();
	}

	/**
	 * @param JInstallerComponent $adapter
	 * @return bool
	 */
	function uninstall($adapter)
	{
		return MjInstaller::uninstall();
	}
}