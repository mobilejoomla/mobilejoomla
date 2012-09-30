<?php
defined('_JEXEC') or die();

include_once dirname(__FILE__).'/classes/mjinstaller.php';

class Com_MobilejoomlaInstallerScript
{
	/**
	 * @param string $type
	 * @param JInstallerComponent $adapter
	 */
	function preflight($type, $adapter)
	{
		$path = $adapter->getParent()->getPath('source');
		$xmldest = $path.'/mobilejoomla.xml';
		$xmlsrc = $path.'/mobilejoomla.j2x.xml';
		if(JFile::exists($xmlsrc))
		{
			if(JFile::exists($xmldest))
				JFile::delete($xmldest);
			JFile::move($xmlsrc, $xmldest);
		}
		$adapter->getParent()->setPath('manifest', $xmldest);
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