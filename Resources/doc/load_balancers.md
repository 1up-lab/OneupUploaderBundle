Use Chunked Uploads behind Load Balancers
=========================================

If you want to use Chunked Uploads behind load balancers that is not configured to use sticky sessions you'll eventually end up with a bunch of chunks on every instance and the bundle is not able to reassemble the file on the server.

You can avoid this problem by using Gaufrette as an abstract filesystem.