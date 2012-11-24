<?php
/**
 * News Admin helper
 *
 * @author Magento
 */
class Magentostudy_News_Helper_Admin extends Mage_Core_Helper_Abstract
{
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    public function isActionAllowed($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('news/manage/' . $action);
    }
}