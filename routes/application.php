<?php

// Landing Page route
$app->get(
	'/',
	function() use ($app) {
		$openStack = $app->openStack;

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
	'/ocp-cluster-status/',
	function() use ($app) {
		$openstack = $app->openstack;

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
