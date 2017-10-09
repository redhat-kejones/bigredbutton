<?php

// Landing Page route
$app->get(
	'/',
	function() use ($app) {
		//$openstack = $app->openstack;

		$openstack = new OpenStack\OpenStack([
		    'authUrl' => 'http://192.168.1.40:5000/v3',
		    'region'  => 'regionOne',
		    'user'    => [
		        'id'       => 'operator',
		        'password' => 'redhat'
		    ],
		    'scope'   => ['project' => ['id' => '0c8e55a7e7824437aa0aa9c89dec6b2a']]
		]);

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
