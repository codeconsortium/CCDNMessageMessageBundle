CCDNMessage MessageBundle Configuration Reference.
==================================================

All available configuration options are listed below with their default values.

``` yml
#        
# for CCDNMessage MessageBundle      
#
ccdn_message_message:  
    user:                 
        profile_route:        ccdn_user_profile_show_by_id 
    template:             
        engine:               twig 
    seo:                  
        title_length:         67 
    folder:               
        show:                 
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig 
            messages_per_page:    10 
            subject_truncate:     50 
            sent_datetime_format:  d-m-Y - H:i 
    message:              
        flood_control:        
            send_limit:           4 
            block_for_minutes:    1 
        show:                 
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig 
            sent_datetime_format:  d-m-Y - H:i 
            enable_bb_parser:     true 
        compose:              
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig 
            form_theme:           CCDNMessageMessageBundle:Form:fields.html.twig 
            enable_bb_editor:     true 
    quotas:               
        max_messages:         200 
```

- [Return back to the docs index](index.md).
