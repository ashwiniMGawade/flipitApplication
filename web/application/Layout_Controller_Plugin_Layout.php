<?php
class Layout_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    protected $moduleName = '';
    protected $localeDirectoryName ='';
    protected $frontController = '';
    public $currentLocale = null;

    /**
     * This function is called once after router shutdown. It automatically
     * sets the layout for the default MVC or a module-specific layout. If
     * you need to set a custom layout based on the controller called, you
     * can set it here using a switch based on the action or controller or
     * set the layout in the controller itself.
     *
     * @param Zend_Controller_Request_Abstract $request
     *
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->moduleName = strtolower($request->getModuleName());
        $this->localeDirectoryName = strtolower($request->getParam('lang'));

        if (strpos(HTTP_HOST, 'kortingscode.nl') !== false && $this->localeDirectoryName != '') {
            throw new \Zend_Controller_Action_Exception('', 404);
        }

        if (strpos(HTTP_HOST, 'kortingscode.nl') === false && $this->localeDirectoryName == '' && $this->moduleName != '' && $this->moduleName != 'admin') {
            throw new \Zend_Controller_Action_Exception('', 404);
        }
        // print in case public keyword exists in url
        preg_match('/public/', REQUEST_URI, $matches, PREG_OFFSET_CAPTURE, 1);

        if (count($matches) > 0) {
            $this->moduleName = 'default';
        }

        self::setLayoutPath();

        // redirect to login page if url is flipit.com/ADMIN
        if ($this->localeDirectoryName == 'admin') {
            header('Location: http://'.$request->getScheme .'/'. $request->getHttpHost() .'/admin', true, 301);
            exit();
        }

        if (
            $request->getModuleName() != 'admin'
            && $this->getRequest()->getActionName() == 'storedetail'
        ) {
            if (preg_match('/[A-Z]/', Zend_Controller_Front::getInstance()->getRequest()->getServer('REQUEST_URI'))) {
                header(
                    'Location: http://'.$_SERVER['HTTP_HOST'].
                    strtolower(
                        Zend_Controller_Front::getInstance()->getRequest()->getServer('REQUEST_URI')
                    ),
                    true,
                    301
                );
                exit();
            }
        }


        $this->frontController = Zend_Controller_Front::getInstance();
        self::getErrorHandlerPlugin($request);
    }

    public function setLayoutPath()
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath(self::getLayoutPath());
        $layout->setLayout('layout');
    }

    public function getLayoutPath()
    {
        if ($this->moduleName == 'default' || $this->moduleName == null) {
            $layoutPath = APPLICATION_PATH . '/layouts/scripts/';
        } elseif ($this->moduleName=='admin') {
            $layoutPath = APPLICATION_PATH . '/modules/'.$this->moduleName.'/layouts/scripts/';
        } else {
            $layoutPath = APPLICATION_PATH . '/layouts/scripts/';
        }

        return $layoutPath;
    }

    public function getErrorHandlerPlugin($request)
    {
        if (!($this->frontController->getPlugin('Zend_Controller_Plugin_ErrorHandler')
                instanceof Zend_Controller_Plugin_ErrorHandler)) {
            return;
        }

        $errorHandler = $this->frontController->getPlugin('Zend_Controller_Plugin_ErrorHandler');

        $httpRequest = new Zend_Controller_Request_Http();
        $httpRequest->setModuleName($request->getModuleName())
        ->setControllerName($errorHandler->getErrorHandlerController())
        ->setActionName($errorHandler->getErrorHandlerAction());

        if ($this->frontController->getDispatcher()->isDispatchable($httpRequest)) {
            $errorHandler->setErrorHandlerModule($request->getModuleName());

            return;
        }
    }

    /**
     * This function is called after the request is dispatch to a controller.
     * We validate logge in user based upon the locale
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->localeDirectoryName   =   strtolower($request->getParam('lang'));

        if (isset( $this->localeDirectoryName ) &&  !empty($this->localeDirectoryName)) {

            if (Auth_VisitorAdapter::hasIdentity()) {
                $req = \Zend_Controller_Front::getInstance()->getRequest();
                $lang  = $req->getParam('lang', false);
                # set propertry to current lcoale during login in case of flipit
                if ($lang) {
                    $this->currentLocale = $lang ;
                }
                $visitorCurrentLocale =  $this->currentLocale;
                if ($visitorCurrentLocale != $this->localeDirectoryName) {
                    $request->setControllerName('login')->setActionName('logout');
                }
            }

        }

        $actionName   = strtolower($request->getActionName());
        // log every request from cms
        if ($this->moduleName === 'admin') {

            self::cmsActivityLog($request);
            $sessionNamespace = new Zend_Session_Namespace();
            // force user to chnage his password if it's older than two months
            if ($sessionNamespace->showPasswordChange && $actionName != 'logout') {
                if (Auth_StaffAdapter::hasIdentity()) {
                    $request->setControllerName('Auth')->setActionName('update-password');
                }
            }

        }
    }

    /**
     * cmsActivityLog
     *
     * write log all post request from cms regardless http or xmlHttp request
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function cmsActivityLog($request)
    {
        // ignore if a request is from datatable
        $requestFromDatatable = $request->getParam('iDisplayStart');

        if ($request->isPost() && $requestFromDatatable == null) {
            $adminActivityForLogs = $request->getParams();
            unset($adminActivityForLogs['module']);
            // hide password fields
            $replacements = array(
                'pwd' => '********',
                'oldPassword' => '********',
                'newPassword' => '********',
                'confirmNewPassword' => '********',
                'password' => '********',
                'confPassword' => '********'
            );

            foreach ($replacements as $replacementKey => $replacementValue) {
                if (array_key_exists($replacementKey, $adminActivityForLogs)) {
                    $adminActivityForLogs[$replacementKey] = $replacementValue;
                }
            }

            $adminActivityForLogs = Zend_Json::encode($adminActivityForLogs);

            $logStorageLocation = APPLICATION_PATH . '/../logs/';
            // create directory if it isn't exists and write log file
            if (!file_exists($logStorageLocation)) {
                mkdir($logStorageLocation, 776, true);
            }

            $fileName = $logStorageLocation  . 'cms';

            $requestURI = $request->getRequestUri();
            $emailOfCurrentUser = '';

            if (Auth_StaffAdapter::hasIdentity()) {
                $emailOfCurrentUser = ';' . Auth_StaffAdapter::getIdentity()->email;
            } else {
                $emailOfCurrentUser = ';';
            }

                $locale = LOCALE == '' ? 'kc' : LOCALE;
                // please avoid to format below template (like tab or space )
                $requestLog = <<<EOD
                {$locale}{$emailOfCurrentUser};{$requestURI}; $adminActivityForLogs
EOD;
                FrontEnd_Helper_viewHelper::writeLog($requestLog, $fileName);
        }
    }
}
