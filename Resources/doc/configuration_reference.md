CCDNMessage MessageBundle Configuration Reference.
==================================================

All available configuration options are listed below with their default values.

``` yml
#        
# for CCDNMessage MessageBundle      
#
ccdn_message_message:             # Required
    template:
        engine:                   twig
        pager_theme:              CCDNMessageMessageBundle:Common:Paginator/twitter_bootstrap.html.twig
    entity:                       # Required
        folder:
            class:                CCDNMessage\MessageBundle\Entity\Folder
        envelope:
            class:                CCDNMessage\MessageBundle\Entity\Envelope
        message:
            class:                CCDNMessage\MessageBundle\Entity\Message
        thread:
            class:                CCDNMessage\MessageBundle\Entity\Thread
        registry:
            class:                CCDNMessage\MessageBundle\Entity\Registry
        user:                     # Required
            class:                Acme\YourUserBundle\Entity\User # Required
    repository:
        folder:
            class:                CCDNMessage\MessageBundle\Model\Component\Repository\FolderRepository
        envelope:
            class:                CCDNMessage\MessageBundle\Model\Component\Repository\EnvelopeRepository
        message:
            class:                CCDNMessage\MessageBundle\Model\Component\Repository\MessageRepository
        registry:
            class:                CCDNMessage\MessageBundle\Model\Component\Repository\RegistryRepository
        thread:
            class:                CCDNMessage\MessageBundle\Model\Component\Repository\ThreadRepository
        user:
            class:                CCDNMessage\MessageBundle\Model\Component\Repository\UserRepository
    gateway:
        folder:
            class:                CCDNMessage\MessageBundle\Model\Component\Gateway\FolderGateway
        envelope:
            class:                CCDNMessage\MessageBundle\Model\Component\Gateway\EnvelopeGateway
        message:
            class:                CCDNMessage\MessageBundle\Model\Component\Gateway\MessageGateway
        thread:
            class:                CCDNMessage\MessageBundle\Model\Component\Gateway\ThreadGateway
        registry:
            class:                CCDNMessage\MessageBundle\Model\Component\Gateway\RegistryGateway
        user:
            class:                CCDNMessage\MessageBundle\Model\Component\Gateway\UserGateway
    manager:
        folder:
            class:                CCDNMessage\MessageBundle\Model\Component\Manager\FolderManager
        envelope:
            class:                CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
        message:
            class:                CCDNMessage\MessageBundle\Model\Component\Manager\MessageManager
        thread:
            class:                CCDNMessage\MessageBundle\Model\Component\Manager\ThreadManager
        registry:
            class:                CCDNMessage\MessageBundle\Model\Component\Manager\RegistryManager
        user:
            class:                CCDNMessage\MessageBundle\Model\Component\Manager\UserManager
    model:
        folder:
            class:                CCDNMessage\MessageBundle\Model\FrontModel\FolderModel
        envelope:
            class:                CCDNMessage\MessageBundle\Model\FrontModel\EnvelopeModel
        message:
            class:                CCDNMessage\MessageBundle\Model\FrontModel\MessageModel
        thread:
            class:                CCDNMessage\MessageBundle\Model\FrontModel\ThreadModel
        registry:
            class:                CCDNMessage\MessageBundle\Model\FrontModel\RegistryModel
        user:
            class:                CCDNMessage\MessageBundle\Model\FrontModel\UserModel
    form:
        type:
            message:
                class:                CCDNMessage\MessageBundle\Form\Type\User\MessageFormType
        handler:
            message:
                class:                CCDNMessage\MessageBundle\Form\Handler\User\MessageFormHandler
            message_reply:
                class:                CCDNMessage\MessageBundle\Form\Handler\User\MessageReplyFormHandler
            message_forward:
                class:                CCDNMessage\MessageBundle\Form\Handler\User\MessageForwardFormHandler
        validator:
            send_to:
                class:                CCDNMessage\MessageBundle\Form\Validator\SendToValidator
    component:
        dashboard:
            integrator:
                class:                CCDNMessage\MessageBundle\Component\Dashboard\DashboardIntegrator
        twig_extension:
            unread_message_count:
                class:                CCDNMessage\MessageBundle\Component\TwigExtension\UnreadMessageCountExtension
            folder_list:
                class:                CCDNMessage\MessageBundle\Component\TwigExtension\FolderListExtension
        flood_control:
            class:                CCDNMessage\MessageBundle\Component\FloodControl
    seo:
        title_length:         67
    folder:
        show:
            layout_template:      CCDNMessageMessageBundle::base.html.twig
            messages_per_page:    10
            subject_truncate:     50
            sent_datetime_format:  d-m-Y - H:i
    message:
        flood_control:
            send_limit:           4
            block_for_minutes:    1
        show:
            layout_template:      CCDNMessageMessageBundle::base.html.twig
            sent_datetime_format:  d-m-Y - H:i
        compose:
            layout_template:      CCDNMessageMessageBundle::base.html.twig
            form_theme:           CCDNMessageMessageBundle:Common:Form/fields.html.twig
    quotas:
        max_messages:             200
```

Replace Acme\YourUserBundle\Entity\User with the user class of your chosen user bundle.

- [Return back to the docs index](index.md).
