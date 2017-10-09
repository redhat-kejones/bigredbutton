<?php

// Landing Page route
$app->get(
	'/',
	function() use ($app,$appConf) {
        	$openStack = $app->openStack;

		//print_r($openStack);

		$compute = $openStack->computeV2();
		$servers = $compute->listServers(true);

                foreach ($servers as $server) {
                     print_r($server);
                }

		$app->render(
			'index.html'
		);
	}
);

$app->get(
	'/nova-servers',
	function() use ($app) {
		//$openstack = $app->openstack;

		$openstack = new OpenStack\OpenStack([
		    'authUrl' => 'http://192.168.1.40:5000/v3/',
		    'region'  => 'regionOne',
		    'user'    => [
		        'id'       => '3dc52851db9844419a4d9b4bb44fc846',
		        'password' => 'redhat'
		    ],
		    'scope'   => ['project' => ['id' => '0c8e55a7e7824437aa0aa9c89dec6b2a']]
		]);

		print_r($openstack);

		$compute = $openstack->computeV2();
		$servers = $compute->listServers();

		$app->render(
			'index.html'
		);
	}
);

// Slim Doc route
$app->get(
	'/slim/',
	function () use($app) {
		$app->render('slimInfo.html');
	}
);

$app->get(
	'/permissionDenied/',
	function () use($app) {
		$app->render('permissionDenied.html');
	}
);
