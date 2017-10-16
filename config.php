<?php

$appConf = \com\sgtinc\ApplicationConfig::getInstance();

$appConf->__set('environment','production');
$appConf->__set('title','Big Red Button');
$appConf->__set('uriRoot','/');

//TODO: Should the DB Connection Info be configurable on init and within admin?
//$appConf->__set('dbHost','localhost');
//$appConf->__set('dbPort','3306');
//$appConf->__set('dbName','skeleton');
//$appConf->__set('dbUsername','skeleton');
//$appConf->__set('dbPassword','...');

//TODO: Make LDAP configurable in the application
//$appConf->__set('ldapHost','ldap://localhost');
//$appConf->__set('ldapPort','636');

//TODO: Make SMTP configurable in the application
//$appConf->__set('smtpHost','localhost');
//$appConf->__set('smtpPort','25');
//$appConf->__set('smtpFromEmail','no-reply@sgt-inc.com');
//$appConf->__set('smtpFromName','SGT Slim Skeleton');

switch($_SERVER['SERVER_NAME']) {
	case "localhost":
	case "127.0.0.1":
		$appConf->__set('environment','local');
		$appConf->__set('title',$appConf->__get('title').' - local');
		$appConf->__set('uriRoot','/lites/slim-twig-skeleton/public/');

		$appConf->__set('smtpHost','localhost');
		$appConf->__set('smtpPort','8025');

		break;
	default:
		//OpenStack
		$appConf->__set('osAuthUrl','http://192.168.1.40:5000/v3/');
		$appConf->__set('osRegion','regionOne');
		$appConf->__set('osUserId','3dc52851db9844419a4d9b4bb44fc846');
		$appConf->__set('osPassword','redhat');
		$appConf->__set('osProjectId','0c8e55a7e7824437aa0aa9c89dec6b2a');
		//CloudForms
		$appConf->__set('cfApiUrl','https://192.168.1.78/api/');
		$appConf->__set('cfUsername','admin');
		$appConf->__set('cfPassword','RedhatMIQ1234');
		//OCP
		$appConf->__set('ocpApiUrl','https://192.168.1.129:8443/');
		$appConf->__set('ocpToken','eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJrdWJlcm5ldGVzL3NlcnZpY2VhY2NvdW50Iiwia3ViZXJuZXRlcy5pby9zZXJ2aWNlYWNjb3VudC9uYW1lc3BhY2UiOiJtYW5hZ2VtZW50LWluZnJhIiwia3ViZXJuZXRlcy5pby9zZXJ2aWNlYWNjb3VudC9zZWNyZXQubmFtZSI6Im1hbmFnZW1lbnQtYWRtaW4tdG9rZW4tbWJmM2IiLCJrdWJlcm5ldGVzLmlvL3NlcnZpY2VhY2NvdW50L3NlcnZpY2UtYWNjb3VudC5uYW1lIjoibWFuYWdlbWVudC1hZG1pbiIsImt1YmVybmV0ZXMuaW8vc2VydmljZWFjY291bnQvc2VydmljZS1hY2NvdW50LnVpZCI6IjAwZGQ5ZjBiLWEzODYtMTFlNy1hMTU5LWZhMTYzZTllYjY2ZCIsInN1YiI6InN5c3RlbTpzZXJ2aWNlYWNjb3VudDptYW5hZ2VtZW50LWluZnJhOm1hbmFnZW1lbnQtYWRtaW4ifQ.cIvzrSHuruMnL9nkEy-XJ9ciGtu6uCI8PslVrhgTA6fMEzA7e9fASwCa02ijIaacZNs-5FcQmiJfV1GLEIVra2bl0kEiMIlAtawj-wVpY6G87Knln6yla554fTGHAG2SkSSMmFAd4XofnpWzfjzXXkRskBdpURysNBdBfKxAleT31ivlS5ZXySo0o5w_pVN7pSVBkOFu8Av95ijHGx3VsRrJdpI9anaDOhikwPLI7ICHd3GnGUAXPuTs-4shnhw-gQlebtQVU5r9jcIu0YyFKKSVwK3mEKgvkSjqQyMiKXjBjtRW1d7AtKGiFT6hM3BFIGQAc0Q2-fTvdjq_GkmTWg');
		break;
}

//Instantiate MySQL DB connection
//$appConf->__set('dbConnString','mysql:host=' . $appConf->__get('dbHost') . ';port=' . $appConf->__get('dbPort') . ';dbname=' . $appConf->__get('dbName'));

$GLOBALS['appConf'] = $appConf;
