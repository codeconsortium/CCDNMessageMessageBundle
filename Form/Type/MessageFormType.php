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

namespace CCDNMessage\MessageBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageFormType extends AbstractType
{

    /**
     *
     */
    protected $container;

    /**
     *
     * @access protected
     */
    protected $defaults = array();

	/**
	 *
	 * @access public
	 * @param $doctrine, $container
	 */
    public function __construct($doctrine, $container)
    {
        $this->container = $container;

        $this->defaults = array('send_to' => '');
    }

    /**
     *
     * @access public
     * @param array $defaults
     */
    public function setDefaultValues(array $defaults = null)
    {
        $this->defaults = array_merge($this->defaults, $defaults);

        return $this;
    }

    /**
     *
     * @access public
     * @param FormBuilder $builder, array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('send_to', 'text', array('data' => $this->getRespondentSendTo()));
        $builder->add('subject', 'text', array('data' => $this->getQuotedSubject()));
        $builder->add('body', 'textarea', array('data' => $this->getQuotedBody()));
        $builder->add('is_flagged', 'checkbox', array('data' => false, 'required' => false));

        $userId = $this->defaults['sender']->getId();
        $attachments = $this->container->get('ccdn_component_attachment.repository.attachment')->findForUserByIdAsQB($userId);

        $builder->add('attachment', 'entity', array(
            'class' => 'CCDNComponentAttachmentBundle:Attachment',
            'choices' => $attachments,
            'property' => 'filename_original',
            'required' => false,
            )
        );

    }

    /**
     *
     * @access protected
     * @return string
     */
    protected function getRespondentSendTo()
    {
        if (isset($this->defaults['send_to'])) {
            $respondent = $this->defaults['send_to'];
        } else {
            if (isset($this->defaults['action']) && isset($this->defaults['message'])) {
                if ($this->defaults['action'] == 'reply') {
                    if ($this->defaults['message']->getSentFrom()) {
                        $respondent = $this->defaults['message']->getSentFrom()->getUsername();
                    } else {
                        $respondent = '';
                    }
                } else {
                    $respondent = '';
                }
            } else {
                $respondent = '';
            }
        }

        return $respondent;
    }

    /**
     *
     * @access protected
     * @return string
     */
    public function getQuotedSubject()
    {
        if (isset($this->defaults['action']) && isset($this->defaults['message'])) {
            if ($this->defaults['action'] == 'reply') {
                $subject = 'Re: ' . $this->defaults['message']->getSubject();
            } else {
                $subject = $this->defaults['message']->getSubject();
            }
        } else {
            $subject = '';
        }

        return $subject;
    }

    /**
     *
     * @access protected
     * @return string
     */
    public function getQuotedBody()
    {
        if (isset($this->defaults['action']) && isset($this->defaults['message'])) {
            if ($this->defaults['action'] == 'reply') {
                if ($this->defaults['message']->getSentFrom()) {
                    $from = $this->defaults['message']->getSentFrom()->getUsername();
                } else {
                    $from = 'guest';
                }

                $message = '[QUOTE=' . $from . ']' . $this->defaults['message']->getBody() . '[/QUOTE]';
            } else {
                $message = $this->defaults['message']->getBody();
            }
        } else {
            $message = '';
        }

        return $message;
    }

    /**
	 *
     * @access public
     * @param array $options
	 * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'CCDNMessage\MessageBundle\Entity\Message',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'message_item',
            //'validation_groups' => '',
        );
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'Message';
    }

}
