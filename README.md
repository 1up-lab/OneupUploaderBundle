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
* [Dropzone](http://www.dropzonejs.com/)

Features included:

* Multiple file uploads handled by your chosen frontend library
* Chunked uploads
* Supports [Gaufrette](https://github.com/KnpLabs/Gaufrette) and/or local filesystem
* Provides an orphanage for cleaning up orphaned files
* Supports [Session upload progress & cancelation of uploads](http://php.net/manual/en/session.upload-progress.php) as of PHP 5.4
* Fully unit tested

[![Build Status](https://travis-ci.org/1up-lab/OneupUploaderBundle.png?branch=master)](https://travis-ci.org/1up-lab/OneupUploaderBundle)
[![Total Downloads](https://poser.pugx.org/oneup/uploader-bundle/d/total.png)](https://packagist.org/packages/oneup/uploader-bundle)

Documentation
-------------

The entry point of the documentation can be found in the file `Resources/docs/index.md`

[Read the documentation for master](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/index.md)

Upgrade Notes
-------------
* Version **v1.0.0** introduced some backward compatibility breaks. For a full list of changes, head to the [dedicated pull request](https://github.com/1up-lab/OneupUploaderBundle/pull/57).
* If you're using chunked uploads consider upgrading from **v0.9.6** to **v0.9.7**. A critical issue was reported regarding the assembly of chunks. More information in ticket [#21](https://github.com/1up-lab/OneupUploaderBundle/issues/21#issuecomment-21560320).
* Error management [changed](https://github.com/1up-lab/OneupUploaderBundle/pull/25) in Version **0.9.6**. You can now register an `ErrorHandler` per configured frontend. This comes bundled with some adjustments to the `blueimp` controller. More information is available in [the documentation](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/custom_error_handler.md).
* Event dispatching [changed](https://github.com/1up-lab/OneupUploaderBundle/commit/a408548b241f47af3539b2137c1817a21a51fde9) in Version **0.9.5**. The dispatching is now handled in the `upload*` functions. So if you have created your own implementation, be sure to remove the call to the `dispatchEvents` function, otherwise it will be called twice. Furthermore no `POST_UPLOAD` event will be fired anymore after uploading a chunk. You can get more information on this topic in the [documentation](https://github.com/1up-lab/OneupUploaderBundle/blob/master/Resources/doc/custom_logic.md#using-chunked-uploads).
* Event names [changed](https://github.com/1up-lab/OneupUploaderBundle/commit/f5d5fe4b6f7b9a04ce633acbc9c94a2dd0e0d6be) in Version **0.9.3**, update your EventListener accordingly.

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
