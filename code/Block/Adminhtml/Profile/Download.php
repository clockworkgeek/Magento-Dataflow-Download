<?php

class Clockworkgeek_DataflowDownload_Block_Adminhtml_Profile_Download
 extends Mage_Adminhtml_Block_Template
{

	/**
	 * Check current profile is the right type before outputting HTML
	 * 
	 * @return string
	 */
	protected function _toHtml()
	{
		if (($profile = $this->getProfile()) && ($data = $profile->getGuiData())
			&& ($profile->getDirection() == 'export')
			&& (@$data['file']['type'] == 'file'))
		{
			return parent::_toHtml();
		}

		// empty string is intentional
		return '';
	}

	/**
	 * @return Mage_Dataflow_Model_Profile
	 */
	public function getProfile()
	{
		return Mage::registry('current_convert_profile');
	}

	public function getDownloadLink()
	{
		if (($profile = $this->getProfile()))
		{
			return $this->getUrl('adminhtml/dataflowdownload/download',
				array('id' => $profile->getId()));
		}

		return '';
	}

	public function getFilename()
	{
		if (($profile = $this->getProfile) && ($data = $profile->getGuiData())
			&& (@$data['file']['type'] == 'file'))
		{
			return @$data['file']['filename'];
		}

		return '';
	}

}

