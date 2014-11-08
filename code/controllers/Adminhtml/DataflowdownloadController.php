<?php

class Clockworkgeek_DataflowDownload_Adminhtml_DataflowdownloadController
 extends Mage_Adminhtml_Controller_Action
{

	public function downloadAction()
	{
		$profileId = (int) $this->getRequest()->getParam('id');

		try
		{
			$profile = Mage::getModel('dataflow/profile')->load($profileId);
			if ($profile->isObjectNew())
			{
				throw new Mage_Exception("Profile with ID '{$profileId}' could not be found.");
			}
			if ($profile->getDirection() != 'export')
			{
				throw new Mage_Exception("Profile '{$profile->getName()}' is not an export profile.");
			}
			$data = $profile->getGuiData();
			if (@$data['file']['type'] != 'file')
			{
				throw new Mage_Exception("Profile '{$profile->getName()}' is not a local export.");
			}

			$filename = trim(@$data['file']['path'], DS) . DS . ltrim(@$data['file']['filename'], DS);
			$fullFilename = Mage::getBaseDir() . DS . $filename;
			if (!file_exists($fullFilename))
			{
				throw new Mage_Exception("File '{$filename}' does not exist.");
			}
			if (!is_readable($fullFilename))
			{
				throw new Mage_Exception("File '{$filename}' is not readable to this process.");
			}

			switch (@$data['parse']['type'])
			{
				case 'csv':
					$type = 'text/csv';
					break;
				case 'excel_xml':
					$type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
					break;
				default:
					$type = 'application/octet-stream';
			}
			$this->_prepareDownloadResponse(@$data['file']['filename'],
				array('type' => 'filename', 'value' => $fullFilename),
				$type);
		}
		catch (Mage_Exception $e)
		{
			Mage::logException($e);
			// This action probably linked from profile run popup, redirecting
			// to referrer is bad and there is no obvious place to show error.
			// By re-throwing exception the default error screen is shown.
			throw $e;
		}
	}

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('system/convert');
	}

}

