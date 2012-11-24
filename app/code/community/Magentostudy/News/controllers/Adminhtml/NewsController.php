<?php
/**
 * News controller
 *
 * @author Magento
 */
class Magentostudy_News_Adminhtml_NewsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Magentostudy_News_Adminhtml_NewsController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('news/manage')
            ->_addBreadcrumb(
                  Mage::helper('magentostudy_news')->__('News'),
                  Mage::helper('magentostudy_news')->__('News')
              )
            ->_addBreadcrumb(
                  Mage::helper('magentostudy_news')->__('Manage News'),
                  Mage::helper('magentostudy_news')->__('Manage News')
              )
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('News'))
             ->_title($this->__('Manage News'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new News item
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit News item
     */
    public function editAction()
    {
        $this->_title($this->__('News'))
             ->_title($this->__('Manage News'));

        // 1. instance news model
        /* @var $model Magentostudy_News_Model_Item */
        $model = Mage::getModel('magentostudy_news/news');

        // 2. if exists id, check it and load data
        $newsId = $this->getRequest()->getParam('id');
        if ($newsId) {
            $model->load($newsId);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('magentostudy_news')->__('News item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            // prepare title
            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('magentostudy_news')->__('Edit Item');
        } else {
            $this->_title(Mage::helper('magentostudy_news')->__('New Item'));
            $breadCrumb = Mage::helper('magentostudy_news')->__('New Item');
        }

        // Init breadcrumbs
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('news_item', $model);

        // 5. render layout
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectPath   = '*/*';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->_filterPostData($data);
            // init model and set data
            /* @var $model Magentostudy_News_Model_Item */
            $model = Mage::getModel('magentostudy_news/news');

            // if news item exists, try to load it
            $newsId = $this->getRequest()->getParam('news_id');
            if ($newsId) {
                $model->load($newsId);
            }
            // save image data and remove from data array
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }
            $model->addData($data);

            try {
                $hasError = false;
                /* @var $imageHelper Magentostudy_News_Helper_Image */
                $imageHelper = Mage::helper('magentostudy_news/image');
                // remove image

                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                }

                // upload new image
                $imageFile = $imageHelper->uploadImage('image');
                if ($imageFile) {
                    if ($model->getImage()) {
                        $imageHelper->removeImage($model->getImage());
                    }
                    $model->setImage($imageFile);
                }
                // save the data
                $model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('magentostudy_news')->__('The news item has been saved.')
                );

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $redirectPath   = '*/*/edit';
                    $redirectParams = array('id' => $model->getId());
                }
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('magentostudy_news')->__('An error occurred while saving the news item.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            try {
                // init model and delete
                /** @var $model Magentostudy_News_Model_Item */
                $model = Mage::getModel('magentostudy_news/news');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('magentostudy_news')->__('Unable to find a news item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('magentostudy_news')->__('The news item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('magentostudy_news')->__('An error occurred while deleting the news item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('news/manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('news/manage/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('news/manage');
                break;
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('time_published'));
        return $data;
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Flush News Posts Images Cache action
     */
    public function flushAction()
    {
        if (Mage::helper('magentostudy_news/image')->flushImagesCache()) {
            $this->_getSession()->addSuccess('Cache successfully flushed');
        } else {
            $this->_getSession()->addError('There was error during flushing cache');
        }
        $this->_forward('index');
    }
}