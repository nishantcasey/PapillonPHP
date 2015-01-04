PapillonPHP
===========

A PHP version of the Papillon API by Stratergia Inc. Version 1 includes many of the basic informative requests allowed by the API.

In order to install and use this document to create PHP based 
applications for servers reporting to the Papillon system you must:

0. Ensure permissions are 774 for this folder (this can be changed once the process is completed)
1. Have a Papillon Master Server installed and running on a Server. 
2. Ensure REST requests are responding from the server. 
3. Run, from the cli the following command: 
$ php PapillonRefactored.php <Master IP Address>
replacing '<Master IP Address>' with the IP address of your Master Papillon Server including the socket :8080. 

4. You can then 'include' or 'require' this file, declare an instance of the PapillonAPI class and use the 
functions provided and detailed in the PapillonRefactored.php file. 

Thank you and enjoy!

Authors: 

Nishant Casey
Garrett Coleman
Mike Finn
Tom McAllister
David O'Connor
