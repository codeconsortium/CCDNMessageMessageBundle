<?php

/*
 * This file is part of the CCDN MessageBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNMessage\MessageBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use CCDNMessage\MessageBundle\Manager\ManagerInterface;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageFormHandler
{

    /**
     *
     * @access protected
     */
    protected $factory;

    /**
     *
     * @access protected
     */
    protected $container;

    /**
     *
     * @access protected
     */
    protected $request;

    /**
     *
     * @access protected
     */
    protected $manager;

    /**
     *
     * @access protected
     */
    protected $defaults = array();

    /**
     *
     * @access protected
     */
    protected $form;

    /**
     *
     * @access protected
     */
    protected $mode;
    const NORMAL = 0;
    const PREVIEW = 1;
    const DRAFT = 2;

    /**
     *
     * @access public
     * @param FormFactory $factory, ContainerInterface $container, ManagerInterface $manager
     */
    public function __construct(FormFactory $factory, ContainerInterface $container, ManagerInterface $manager)
    {
        $this->defaults = array();
        $this->factory = $factory;
        $this->container = $container;

        $this->mode = self::NORMAL;
        $this->manager = $manager;

        $this->request = $container->get('request');
    }

    /**
     *
     *
     * @access public
     */
    public function setMode($mode)
    {
        switch ($mode) {
            case self::NORMAL:
                $this->mode = self::NORMAL;
            break;
            case self::PREVIEW:
                $this->mode = self::PREVIEW;
            break;
            case self::DRAFT:
                $this->mode = self::DRAFT;
            break;
        }
    }

    /**
     *
     * @access public
     * @param Array() $options
     * @return $this
     */
    public function setDefaultValues(array $defaults = null)
    {
        $this->defaults = array_merge($this->defaults, $defaults);

        return $this;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function process()
    {
        $this->getForm();

        if ($this->request->getMethod() == 'POST') {
            $formData = $this->form->getData();

            $formData->setSentFrom($this->defaults['sender']);
            $formData->setSentDate(new \DateTime());
            $formData->setCreatedDate(new \DateTime());
            $formData->setIsDraft(false);

            if (isset($this->defaults['action'])) {
                if ($this->defaults['action'] == 'forward') {
                    $formData->setInResponseTo($this->defaults['message']);
                }
            }

            if ($this->form->isValid()) {
//				echo '<pre>' . print_r($this->form->getData(), true) . '</pre>'; die();
//				$this->onSuccess($this->form->getData());
                $this->onSuccess($formData);

                return true;
            }
        }

        return false;
    }

    /**
     *
     * @access public
     * @return Form
     */
    public function getForm()
    {
        if (! $this->form) {
            $messageType = $this->container->get('ccdn_message_message.message.form.type');

            $defaultValues = array();

            $defaultValues['sender'] = $this->defaults['sender'];

            if (isset($this->defaults['send_to'])) {
                $defaultValues['send_to'] = $this->defaults['send_to']->getUsername();
            }

            if (isset($this->defaults['action'])) {
                if ($this->defaults['action'] == 'reply') {
                    $defaultValues['action'] = 'reply';
                    $defaultValues['message'] = $this->defaults['message'];
                }
                if ($this->defaults['action'] == 'forward') {
                    $defaultValues['action'] = 'forward';
                }
            }

            $messageType->setDefaultValues($defaultValues);

            if (isset($this->defaults['message']) && isset($this->defaults['action'])) {
                if ($this->defaults['action'] == 'forward') {
                    $this->form = $this->factory->create($messageType, $this->defaults['message']);
                } else {
                    $this->form = $this->factory->create($messageType);
                }
            } else {
                $this->form = $this->factory->create($messageType);
            }

            if ($this->request->getMethod() == 'POST') {
                $this->form->bindRequest($this->request);
            }
        }

        return $this->form;
    }

    /**
     *
     * @access protected
     * @param $entity
     * @return MessageManager
     */
    protected function onSuccess($entity)
    {

        if ($this->mode == self::DRAFT) {
            return $this->manager->saveDraft($entity)->flush();
        }

        return $this->manager->insert($entity)->flush();
    }

}
