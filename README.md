OneupUploaderBundle
===================
The OneupUploaderBundle for Symfony2 adds support for handling file uploads using one of the following Javascript libraries, or [your own implementation](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/custom_uploader.md).

* [FineUploader](http://fineuploader.com/)
* [jQuery File Uploader](http://blueimp.github.io/jQuery-File-Upload/)
* [YUI3 Uploader](http://yuilibrary.com/yui/docs/uploader/)
* [Uploadify](http://www.uploadify.com/)
* [FancyUpload](http://digitarald.de/project/fancyupload/)
* [MooUpload](https://github.com/juanparati/MooUpload)
* [Plupload](http://www.plupload.com/)

Features included:

* Multiple file uploads handled by your chosen frontend library
* Chunked uploads
* Supports [Gaufrette](https://github.com/KnpLabs/Gaufrette) and/or local filesystem
* Provides an orphanage for cleaning up orphaned files
* Fully unit tested

[![Build Status](https://travis-ci.org/1up-lab/OneupUploaderBundle.png?branch=master)](https://travis-ci.org/1up-lab/OneupUploaderBundle)

Documentation
-------------

The entry point of the documentation can be found in the file `Resources/docs/index.md`

[Read the documentation for master](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/index.md)

Upgrade Notes
-------------
Event names [changed](https://github.com/1up-lab/OneupUploaderBundle/commit/f5d5fe4b6f7b9a04ce633acbc9c94a2dd0e0d6be) in Version 0.9.3, update your EventListener accordingly.

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/1up-lab/OneupUploaderBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
