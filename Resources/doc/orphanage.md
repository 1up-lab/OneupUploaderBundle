Using the Orphanage
===================

If you are providing a create-form with file uploads you can easily run into a problem if the user decides to not submit the form. You then have uploaded files that don't belong to any Entity or the like. For these use cases the OneupUploaderBundle has the possibility to use a so called `Orphanage`.

The purpose of the `Orphanage` is to collect orphaned files until you come along and collect them or choose to clean up. To enable the `Orphanage` simply switch the `use_orphanage` property in your mapping to `true`.

```yaml
# app/config/config.yml

oneup_uploader:
    mappings:
        gallery:
            use_orphanage: true
```

As soon as you have `use_orphanage` set to true, uploaded files are not going to be moved directly to the configured directory. Instead, they will be moved to the directory specified under the `orphanage` key in the configuration. (see _Configure the Orphanage_)
They will be moved to the correct place as soon as you trigger the `uploadFiles` method on the Storage.

## Prerequisites
The `Orphanage` will save your files based on the current SessionId. Because of that you need a running session, even if you provide an uploader for anonymous users.

```yaml
# app/config/security.yml

security:
    firewalls:
        main:
            pattern: ^/
            anonymous: true
```

## The Controller part
Upload the files by triggering the `uploadFiles` method on the correct orphanage storage.

```php
// src/Acme/Controller/AcmeController.php

class AcmeController extends Controller
{
    public function storeAction()
    {
        $manager = $this->get('oneup_uploader.orphanage_manager')->get('gallery');

        // get files
        $files = $manager->getFiles();

        // upload all files to the configured storage
        $files = $manager->uploadFiles();
    }
}
```

You will get an array containing the moved files.

> If you are using Gaufrette, these files are instances of `Gaufrette\File`, otherwise `SplFileInfo`.

## Configure the Orphanage
You can configure the `Orphanage` by using the following configuration parameters.

```
oneup_uploader:
    orphanage:
        maxage: 86400
        directory: %kernel.cache_dir%/uploader/orphanage
```

You can choose a custom directory to save the orphans temporarily while uploading by changing the parameter `directory`.

If you are using a gaufrette filesystem as the chunk storage, the ```directory``` specified above should be
relative to the filesystem's root directory. It will detect if you are using a gaufrette chunk storage
and default to ```orphanage```.

> The orphanage and the chunk storage are forced to be on the same filesystem.

## Clean up
The `OrphanageManager` can be forced to clean up orphans by using the command provided by the OneupUploaderBundle.

    $> php app/console oneup:uploader:clear-orphans

This parameter will clean all orphaned files older than the `maxage` value in your configuration.

## Known Limitations
The `Orphanage` will save uploaded files in a directory like the following:

    %kernel.cache_dir%/uploader/orphanage/{session_id}/uploaded_file.ext

It is currently not possible to change the part after `%kernel.cache_dir%/uploader/orphanage` dynamically. This has some implications. If a user will upload files through your `gallery` mapping, and choose not to submit the form, but instead start over with a new form handled by the `gallery` mapping, the newly uploaded files are going to be moved in the same directory. Therefore you will get both the files uploaded the first time and the second time if you trigger the `uploadFiles` method.
