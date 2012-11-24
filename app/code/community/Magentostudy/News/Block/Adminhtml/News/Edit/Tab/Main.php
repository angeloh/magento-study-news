<?php
/**
 * News List admin edit form main tab
 *
 * @author Magento
 */
class Magentostudy_News_Block_Adminhtml_News_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare form elements for tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::helper('magentostudy_news')->getNewsItemInstance();

        /**
         * Checking if user have permissions to save information
         */
        if (Mage::helper('magentostudy_news/admin')->isActionAllowed('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('news_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('magentostudy_news')->__('News Item Info')
        ));

        if ($model->getId()) {
            $fieldset->addField('news_id', 'hidden', array(
                'name' => 'news_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'     => 'title',
            'label'    => Mage::helper('magentostudy_news')->__('News Title'),
            'title'    => Mage::helper('magentostudy_news')->__('News Title'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('author', 'text', array(
            'name'     => 'author',
            'label'    => Mage::helper('magentostudy_news')->__('Author'),
            'title'    => Mage::helper('magentostudy_news')->__('Author'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('published_at', 'date', array(
            'name'     => 'published_at',
            'format'   => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'label'    => Mage::helper('magentostudy_news')->__('Publishing Date'),
            'title'    => Mage::helper('magentostudy_news')->__('Publishing Date'),
            'required' => true
        ));

        Mage::dispatchEvent('adminhtml_news_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('magentostudy_news')->__('News Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('magentostudy_news')->__('News Info');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
