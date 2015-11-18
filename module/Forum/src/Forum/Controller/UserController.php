<?php
namespace Forum\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Forum\Form\UserForm;
use Forum\Model\User;
use Zend\Debug\Debug;
use Zend\InputFilter\InputFilter;
use Forum\Service\UserService;
use Application;
use Zend\Authentication\AuthenticationService;
use Application\Common\WAuthUtil;
class UserController  extends AbstractActionController
{
    /* 
     * @var UserService
     *  */
    protected $userService;

    public  function __construct(){

    }
    
    /*用来说明所有页面实现顶部状态更新注销的过程
     * 已经封装成两个函数十分方便
     */
    public function testAction(){
        WAuthUtil::whetherLogout($this);//所有Action第一行加上这一行，用于进行顶部显示操作
        /*以上一行代表下面这几行
        $request = $this->getRequest();
        if ($request->isGet()&&$request->getQuery()->offsetGet('logout')) {
            $auth = new AuthenticationService();
            $auth->getStorage()->clear();
        }*/
        
        WAuthUtil::addUserpanelToLayout($this, '/test');
        /*上面一行代表下面这几行
        $idenstr = $this->getservice()->get_auth();
       // Debug::dump($tmp);//用户名的string字符串
        //layout()用法
        $v1=new ViewModel(array(
            'identity'=>$idenstr,
            'currentPage'=>'/test'//用于trace当前页面，必须要加
        ));
        //网页顶部显示登陆信息一般过程
        $v1->setTemplate('blog/user/userPanel');
        $layout=$this->layout();
        $layout->addChild($v1,'userPanel');
        */
        $v=new ViewModel(array(
        ));
        return $v;
    }
    public function testaddAction(){
        
    }
    public function loginAction(){
        WAuthUtil::whetherLogout($this);
        
        $form = new UserForm();
//         $username2=$form->get('username');
//         $username2->setAttribute('class', 'username');
        $form->remove('schoolID');
        $form->get('submit')->setValue('login');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
        
            $form->setInputFilter($user->getInputFilter());//就算没有输入id也可以通过检验。
            $form->bind($user);
            $form->setData($request->getPost());
            if ($form->isValid()) {
                if($this->getservice()->auth($user)){
                    return $this->redirect()->toRoute('add');
                }
            }
            else {
                $messages = $form->getMessages();
                Debug::dump($messages);
            }
        }
        
        WAuthUtil::addUserpanelToLayout($this, '/login');
        
        return new ViewModel(array(
            'userform'=>$form
        ));
    }
    
    /*用户注册action
     * 
     */
    public function registerAction()
    {
        $form = new UserForm();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
//             echo $_GET['user'];
//             Debug::dump($request->getPost());
            $form->setInputFilter($user->getInputFilter());//就算没有输入id也可以通过检验。
            $form->bind($user);
            $form->setData($request->getPost());
            if ($form->isValid()) {
//                 $user->setId(9);
//                 Debug::dump($user);
                $this->getservice()->save($user);
                $this->getservice()->auth($user);
//                 Debug::dump($form->getData());//经过bind,是一个$user对象，必须要实现exchangeArray ，get和set不必要
                //Debug::dump($user);//同上
//                 Redirect to list of albums如果想要dump就不要转业
                return $this->redirect()->toRoute('add');
            }
            else {
                $messages = $form->getMessages();
                Debug::dump($messages);
            }
        }
        
        //layout()用法
//         $v1=new ViewModel(array(
//             'identity'=>'wyj'
//         ));
        
//         //网页顶部显示登陆信息一般过程
//         $v1->setTemplate('blog/user/userPanel');
//         $layout=$this->layout();
//         $layout->addChild($v1,'userPanel');
        $v=new ViewModel(array(
            'userform'=>$form
        ));
        return $v;
     
    }
    public function getservice()
    {
        if (!$this->userService) {
            $sm = $this->getServiceLocator();//$sm service manager
            $this->userService = $sm->get('Forum\Service\UserService');
        }
        return $this->userService;
    }
}