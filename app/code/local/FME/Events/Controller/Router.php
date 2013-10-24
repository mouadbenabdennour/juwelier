<?php
class FME_Events_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initProtocol($observer)
    {
        $front = $observer->getEvent()->getFront();
        $router = new FME_Events_Controller_Router();
        $front->addRouter('events', $router);
    }
    
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled())
        {
            Mage::app()->getFrontController()->getResponse()
                    ->setRedirect(Mage::getUrl('install'))
                    ->sendResponse();
            exit;
        }
        
        $identifierNew = Mage::helper('events')->extIdentifier();
        $currRouter = trim($request->getPathInfo(), '/');
        $explode = explode('/', $currRouter); // echo '<pre>';print_r($explode);exit;
        
        if ($currRouter == $identifierNew)
        {
            $request->setModuleName('events')
                    ->setControllerName('index')
                    ->setActionName('index');
                    
             return true;       
        }
        elseif ($currRouter != $identifierNew)
        { //event/index/view/pfx/live-nash 
            $identifier = trim($explode[0]);
            if ($identifier != Mage::helper('events')->extIdentifier())
            {
                return false;
            }
            
            $pfx = trim($explode[1]);
            $model = Mage::getModel('events/events')->loadByPrefix($pfx); // echo '<pre>'; print_r($model->getData());exit;
            $isPfx = $model->getEventId();
            
            if ($isPfx > 0)
            {
                $request->setModuleName('events')
                        ->setControllerName('index')
                        ->setActionName('view')
                        ->setParam('pfx', $pfx);
                $request->setAlias(
                            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                            $currRouter
                );
                
                return true;
            }
            elseif ($currRouter == $identifierNew.'/calendar')
            {
                 $request->setModuleName('events')
                        ->setControllerName('index')
                        ->setActionName(trim($explode[1]));
                
                $request->setAlias(
                            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                            $currRouter
                );
                
                return true;
            }
            elseif ($currRouter == $identifierNew.'/'.$explode[1])
            {
                $request->setModuleName('events')
                        ->setControllerName('index')
                        ->setActionName('index');
                        if (Mage::helper('events')->isValidDate($explode[1]))
                        {
                            $request->setParam('date_event', $explode[1]);
                        }
                
                $request->setAlias(
                            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                            $currRouter
                );
                
                return true;
            }
            
            return false;   
        }
        
        return false;
    }
}