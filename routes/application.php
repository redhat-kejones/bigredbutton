<?php

// Landing Page route
$app->get(
	'/',
	function() use ($app,$appConf) {
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
	'/ocp-nodes',
	function() use ($app, $appConf) {
		$serverArr = array();
		$openStack = $app->openStack;

		$compute = $openStack->computeV2();
		$servers = $compute->listServers(true, ['flavorId'=>'4d3d73a3-4575-49a1-93d3-2a20c565aded']);

		foreach ($servers as $server) {
        	array_push($serverArr, $server);
        }

        $response = $app->response();
    	$response['Content-Type'] = 'application/json';
    	$response->body(json_encode($serverArr));
    }
);

$app->get(
	'/ocp-control-nodes',
	function() use ($app, $appConf) {
		$serverArr = array();
		$openStack = $app->openStack;

		$compute = $openStack->computeV2();
		$servers = $compute->listServers(true, ['flavorId'=>'50503afe-7a3e-4768-9a35-3f097264d6ee']);

		foreach ($servers as $server) {
        	array_push($serverArr, $server);
        }

        $response = $app->response();
    	$response['Content-Type'] = 'application/json';
    	$response->body(json_encode($serverArr));
    }
);

$app->get(
	'/delete-server/:id',
	function($id) use ($app, $appConf) {
		try {
			$serverArr = array();
			$openStack = $app->openStack;

			$compute = $openStack->computeV2();

			$servers = $compute->listServers(true, ['flavorId'=>'4d3d73a3-4575-49a1-93d3-2a20c565aded']);

			$isOcpNode = false;
			foreach ($servers as $server) {
        		if ($id == $server->id) {
        			$isOcpNode = true;
        		}
        	}

        	if ($isOcpNode) {
        		$server = $compute->getServer(['id' => $id]);
				$server->delete();

				$results['success'] = true;
	        	$results['message'] = "Server (".$id.") was deleted successfully";
        	} else {
        		$results['success'] = false;
	        	$results['message'] = "Server (".$id.") is not an OCP Node";
        	}
			

        } catch (\Exception $e) {
        	$app->log->error($e);
            $results['success'] = false;
            $results['message'] = "Server (".$id.") was not deleted";
        }

        header('Content-Type: application/json');

        $response = $app->response();
    	$response['Content-Type'] = 'application/json';
    	$response->body(json_encode($results));
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
