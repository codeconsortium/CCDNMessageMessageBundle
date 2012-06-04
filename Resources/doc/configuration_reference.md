CCDNMessage MessageBundle Configuration Reference.
==================================================

All available configuration options are listed below with their default values.

``` yml
#        
# for CCDNMessage MessageBundle      
#
ccdn_message_message:  
    user:
        profile_route: cc_profile_show_by_id
    template:
        engine: twig
#    folder:
#        show:
#            layout_template: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
#            messages_per_page: 10
#            subject_truncate: 50
#            sent_datetime_format: "d-m-Y - H:i"
#    message:
#        compose:
#            layout_template: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
#            form_theme: CCDNMessageMessageBundle:Form:fields.html.twig
#        show:
#            layout_template: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
#            sent_datetime_format: "d-m-Y - H:i"
    quotas:
        max_messages: 100

```

- [Return back to the docs index](index.md).
