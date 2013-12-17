<?php

/*
 * This file is part of the CCDNMessage MessageBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNMessage\MessageBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @category CCDNMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNMessageMessageBundle
 *
 */
class BaseController extends ContainerAware
{
    /**
     *
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
     */
    private $translator;

    /**
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    private $router;

    /**
     *
     * @var \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine $templating
     */
    private $templating;

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request $request
     */
    protected $request;

    /**
     *
     * @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher  $dispatcher;
     */
    protected $dispatcher;

    /**
     *
     * @var \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    private $securityContext;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\FolderModel $folderModel
     */
    private $folderModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\EnvelopeModel $envelopeModel
     */
    private $envelopeModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\MessageModel $messageModel
     */
    private $messageModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\ThreadModel $threadModel
     */
    private $threadModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\RegistryModel $registryModel
     */
    private $registryModel;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\UserModel $userModel
     */
    protected $userModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Component\FloodControl $floodControl;
     */
    private $floodControl;

    /**
     *
     * @access protected
     * @return \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected function getTranslator()
    {
        if (null == $this->translator) {
            $this->translator = $this->container->get('translator');
        }

        return $this->translator;
    }

    /**
     *
     * @access protected
     * @param  string $message
     * @param  Array  $params
     * @param  string $bundle
     * @return string
     */
    protected function trans($message, $params = array(), $bundle = 'CCDNMessageMessageBundle')
    {
        return $this->getTranslator()->trans($message, $params, $bundle);
    }

    /**
     *
     * @access protected
     * @param  string $action, string $value
     * @return string
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }

    /**
     *
     * @access protected
     * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected function getRouter()
    {
        if (null == $this->router) {
            $this->router = $this->container->get('router');
        }

        return $this->router;
    }

    /**
     *
     * @access protected
     * @param  string $route
     * @param  Array  $params
     * @return string
     */
    protected function path($route, $params = array())
    {
        return $this->getRouter()->generate($route, $params);
    }

    /**
     *
     * @access protected
     * @return \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
     */
    protected function getTemplating()
    {
        if (null == $this->templating) {
            $this->templating = $this->container->get('templating');
        }

        return $this->templating;
    }

    /**
     *
     * @access protected
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        if (null == $this->request) {
            $this->request = $this->container->get('request');
        }

        return $this->request;
    }

    /**
     *
     * @access protected
     * @param  string $prefix
     * @return Array
     */
    protected function getCheckedItemIds($prefix = 'check_', $enforceNumericType = true)
    {
        $request = $this->getRequest();

        $sanitarisedIds = array();

        if ($request->request->has($prefix)) {
            $itemIds = $request->request->get($prefix);

            foreach ($itemIds as $id => $val) {
                if ($enforceNumericType == true) {
                    if (! is_numeric($id)) {
                        continue;
                    }
                }

                $sanitarisedIds[] = $id;
            }
        }

        return $sanitarisedIds;
    }

    /**
     *
     * @access protected
     * @param  string $template
     * @param  Array  $params
     * @param  string $engine
     * @return string
     */
    protected function renderResponse($template, $params = array(), $engine = null)
    {
        return $this->getTemplating()->renderResponse($template . ($engine ?: $this->getEngine()), $params);
    }

    /**
     *
     * @access protected
     * @param  string                                             $url
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectResponse($url)
    {
        return new RedirectResponse($url);
    }

    /**
     *
     * @access protected
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_message_message.template.engine');
    }

    /**
     *
     * @access protected
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    protected function getSecurityContext()
    {
        if (null == $this->securityContext) {
            $this->securityContext = $this->container->get('security.context');
        }

        return $this->securityContext;
    }

    /**
     *
     * @access protected
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    protected function getUser()
    {
        return $this->getSecurityContext()->getToken()->getUser();
    }

    /**
     *
     * @access protected
     * @param  string $role
     * @return bool
     */
    protected function isGranted($role)
    {
        if (! $this->getSecurityContext()->isGranted($role)) {
            return false;
        }

        return true;
    }

    /**
     *
     * @access protected
     * @param  string                                                           $role|boolean $role
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function isAuthorised($role)
    {
        if (is_bool($role)) {
            if ($role == false) {
                throw new AccessDeniedException('You do not have permission to use this resource.');
            }

            return true;
        }

        if (! $this->isGranted($role)) {
            throw new AccessDeniedException('You do not have permission to use this resource.');
        }

        return true;
    }

    /**
     *
     * @access protected
     * @param  \Object                                                      $item
     * @param  string                                                       $message
     * @return bool
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function isFound($item, $message = null)
    {
        if (null == $item) {
            throw new NotFoundHttpException($message ?: 'Page you are looking for could not be found!');
        }

        return true;
    }

    /**
     *
     * @access protected
     * @return string
     */
    protected function getSubmitAction()
    {
        $request = $this->getRequest();

        if ($request->request->has('submit')) {
            $action = key($request->request->get('submit'));
        } else {
            $action = 'post';
        }

        return $action;
    }

    protected function getQuery($query, $default)
    {
        return $this->getRequest()->query->get($query, $default);
    }

    protected function dispatch($name, Event $event)
    {
        if (! $this->dispatcher) {
            $this->dispatcher = $this->container->get('event_dispatcher');
        }

        $this->dispatcher->dispatch($name, $event);
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\FrontModel\FolderModel
     */
    protected function getFolderModel()
    {
        if (null == $this->folderModel) {
            $this->folderModel = $this->container->get('ccdn_message_message.model.folder');
        }

        return $this->folderModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\FrontModel\EnvelopeModel
     */
    protected function getEnvelopeModel()
    {
        if (null == $this->envelopeModel) {
            $this->envelopeModel = $this->container->get('ccdn_message_message.model.envelope');
        }

        return $this->envelopeModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\FrontModel\MessageModel
     */
    protected function getMessageModel()
    {
        if (null == $this->messageModel) {
            $this->messageModel = $this->container->get('ccdn_message_message.model.message');
        }

        return $this->messageModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\FrontModel\ThreadModel
     */
    protected function getThreadModel()
    {
        if (null == $this->threadModel) {
            $this->threadModel = $this->container->get('ccdn_message_message.model.thread');
        }

        return $this->threadModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\FrontModel\RegistryModel
     */
    protected function getRegistryModel()
    {
        if (null == $this->registryModel) {
            $this->registryModel = $this->container->get('ccdn_message_message.model.registry');
        }

        return $this->registryModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\FrontModel\UserModel
     */
    protected function getUserModel()
    {
        if (null == $this->userModel) {
            $this->userModel = $this->container->get('ccdn_message_message.model.user');
        }

        return $this->userModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Component\FloodControl
     */
    protected function getFloodControl()
    {
        if (null == $this->floodControl) {
            $this->floodControl = $this->container->get('ccdn_message_message.component.flood_control');
        }

        return $this->floodControl;
    }

    /**
     *
     * @access protected
     */
    protected function getCrumbs()
    {
        return $this->container->get('ccdn_message_message.component.crumb_builder');
    }

    /**
     *
     * @access protected
     */
    protected function getFolderHelper()
    {
        return $this->container->get('ccdn_message_message.component.helper.folder');
    }
}
