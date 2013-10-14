Use Chunked Uploads behind Load Balancers
=========================================

If you want to use Chunked Uploads behind load balancers that is not configured to use sticky sessions you'll eventually end up with a bunch of chunks on every instance and the bundle is not able to reassemble the file on the server.

You can avoid this problem by using Gaufrette as an abstract filesystem. Check the following configuration as an example.

```yaml
knp_gaufrette:
    adapters:
        gallery:
            local:
                directory: %kernel.root_dir%/../web/uploads
                create: true

    filesystems:
        gallery:
            adapter: gallery

    stream_wrapper: ~

oneup_uploader:
    chunks:
        storage:
            type: gaufrette
            filesystem: gaufrette.gallery_filesystem
            stream_wrapper: gaufrette://gallery/

    mappings:
        gallery:
            frontend: fineuploader
            storage:
                type: gaufrette
                filesystem: gaufrette.gallery_filesystem
```

> :exclamation: Event though it is possible to use two different Gaufrette filesystems - one for the the chunk storage - and one for the mapping, it is not recommended.

> :exclamation: Do not use a Gaufrette filesystem for the chunk storage and a local filesystem one for the mapping. This is not possible to check during configuration and will throw unexpected errors!

Using Gaufrette filesystems for chunked upload directories has some limitations. It is highly recommended to use a `Local` Gaufrette adapter as it is the only one that is able to `rename` a file but `move` it. Especially when working with bigger files this can have serious perfomance advantages as this way the file doesn't have to be moved entirely to memory!