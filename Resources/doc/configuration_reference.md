Configuration Reference
=======================

All available configuration options along with their default values are listed below.

``` yaml
oneup_uploader:
    chunks:
        maxage:               604800
        storage:
            type:               filesystem
            directory:          ~
            filesystem:         ~
            sync_buffer_size:   100K
            stream_wrapper:     ~
            prefix:             'chunks'
        load_distribution:    true
    orphanage:
        maxage:               604800
        directory:            ~
    twig:                 true
    mappings:             # Required

        # Prototype
        id:
            frontend:             fineuploader
            custom_frontend:
                name:                 ~
                class:                ~
            storage:
                service:              ~
                type:                 filesystem
                filesystem:           ~
                directory:            ~
                stream_wrapper:       ~
                sync_buffer_size:     100K
            route_prefix:
            allowed_mimetypes:     []
            disallowed_mimetypes:  []
            error_handler:        oneup_uploader.error_handler.noop

            # Set max_size to -1 for gracefully downgrade this number to the systems max upload size.
            max_size:             9223372036854775807
            use_orphanage:        false
            enable_progress:      false
            enable_cancelation:   false
            namer:                oneup_uploader.namer.uniqid

```
