Support a custom Uploader
=========================

If you have written your own Uploader or you want to use an implementation that is currently not supported by the OneupUploaderBundle follow these steps to integrate it to your Symfony2 application.

## Configuration

Configure your custom uploader according to the following example.

```yml
oneup_uploader:
    mappings:
        gallery:
            frontend: custom
            custom_frontend:
                class: Acme\DemoBundle\Controller\CustomController
                name: MyFancyCustomUploader
```

This will automatically create everything you need later.
The next step is to include the logic of your custom Uploader to your provided Controller. For having a consistent interface consider extending one of the following classes:

* `Oneup\UploaderBundle\Controller\AbstractController`: For any implementation that don't support chunked uploads.
* `Oneup\UploaderBundle\Controller\AbstractChunkedController`: For any implementation that should support chunked uploads.

## The Controller part

If you decided to extend the AbstractController, do the following

```php
namespace Acme\DemoBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Oneup\UploaderBundle\Controller\UploaderController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class CustomUploader extends UploaderController
{
    public function upload()
    {
        // get some basic stuff together
        $request = $this->container->get('request');
        $response = new EmptyResponse();
        
        // get file from request (your own logic)
        $file = ...;
        
        try {
            $uploaded = $this->handleUpload($file);
        } catch(UploadException $e) {
            // return nothing
            return new JsonResponse(array());
        }
        
        // return assembled response
        return new JsonResponse($response->assemble());
    }
}
```

## Implement chunked upload
If you want to additionaly support chunked upload, you have to overwrite the `AbstractChunkedController` and implement the `parseChunkedRequest` method. This method should return an array containing the following values:

* `$last`: Is this the last chunk of a file (`true`/`false`)
* `$uuid`: A truly unique id which will become the directory name for the `ChunkManager` to use.
* `$index`: Which part (chunk) is it? Its not important that you provide exact numbers, but they must be higher for a subsequent chunk!
* `$orig`: The original filename.

Take any chunked upload implementation in `Oneup\UploaderBundle\Controller` as an example.

After that, you manually have to check if you have to do a chunked upload or not. This differs from implementation to implementation, so heres an example of the jQuery File Uploader:

```php        
$chunked = !is_null($request->headers->get('content-range'));
$uploaded = $chunked ? $this->handleChunkedUpload($file) : $this->handleUpload($file);
```

## Using a custom Response class
If your frontend implementation relies on specific data returned, it is highly recommended to create your own `Response` class. Here is an example for FineUploader, I guess you'll get the point:

```php
namespace Oneup\UploaderBundle\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class FineUploaderResponse extends AbstractResponse
{
    protected $success;
    protected $error;
    
    public function __construct()
    {
        $this->success = true;
        $this->error = null;
        
        parent::__construct();
    }
    
    public function assemble()
    {
        // explicitly overwrite success and error key
        // as these keys are used internaly by the
        // frontend uploader
        $data = $this->data;
        $data['success'] = $this->success;
        
        if($this->success)
            unset($data['error']);
        
        if(!$this->success)
            $data['error'] = $this->error;
        
        return $data;
    }
    
    // ... snip, setters/getters
}
```

## Notes

It is highly recommended to use the internal and inherited methods `handleUpload` and `handleChunkedUpload` as these will mind your configuration file. Nonetheless; it is possible to overwrite the behaviour completely in your Controller class.
