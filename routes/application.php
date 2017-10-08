<?php

// Landing Page route
$app->get(
	'/',
	function() use ($app) {
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
