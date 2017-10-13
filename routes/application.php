<?php

// Landing Page route
$app->get(
	'/',
	function() use ($app,$appConf) {
    $openStack = $app->openStack;

		$compute = $openStack->computeV2();
		$ocpNodes = $compute->listServers(true, ['flavorId'=>'4d3d73a3-4575-49a1-93d3-2a20c565aded']);
		$ocpControlNodes = $compute->listServers(true, ['flavorId'=>'50503afe-7a3e-4768-9a35-3f097264d6ee']);

		$ocpControlStatus = array();
		$ocpNodeStatus = array();

		foreach ($ocpControlNodes as $node) {
			$nodeStatus = array("type"=>"Master/Infra","id"=>$node->id,"name"=>$node->name,"status"=>$node->status);
			array_push($ocpControlStatus,$nodeStatus);
		}

		foreach ($ocpNodes as $node) {
			$nodeStatus = array("type"=>"Node","id"=>$node->id,"name"=>$node->name,"status"=>$node->status);
			array_push($ocpNodeStatus,$nodeStatus);
		}

		$app->render(
			'index.html',
			array(
				'ocpControlStatus'=>$ocpControlStatus,
				'ocpNodeStatus'=>$ocpNodeStatus
			)
		);
	}
);

$app->get(
	'/ocp-cluster-status',
	function() use ($app,$appConf) {
    $openStack = $app->openStack;

		$compute = $openStack->computeV2();
		$ocpNodes = $compute->listServers(true, ['flavorId'=>'4d3d73a3-4575-49a1-93d3-2a20c565aded']);
		$ocpControlNodes = $compute->listServers(true, ['flavorId'=>'50503afe-7a3e-4768-9a35-3f097264d6ee']);

		$ocpControlStatus = array();
		$ocpNodeStatus = array();

		foreach ($ocpControlNodes as $node) {
			$nodeStatus = array("type"=>"Master/Infra","id"=>$node->id,"name"=>$node->name,"status"=>$node->status);
			array_push($ocpControlStatus,$nodeStatus);
		}

		foreach ($ocpNodes as $node) {
			$nodeStatus = array("type"=>"Node","id"=>$node->id,"name"=>$node->name,"status"=>$node->status);
			array_push($ocpNodeStatus,$nodeStatus);
		}

		$app->render(
			'ocpClusterStatus.html',
			array(
				'ocpControlStatus'=>$ocpControlStatus,
				'ocpNodeStatus'=>$ocpNodeStatus
			)
		);
	}
);

$app->get(
	'/ocp-control-status',
	function() use ($app,$appConf) {
    $openStack = $app->openStack;

		$compute = $openStack->computeV2();
		$ocpControlNodes = $compute->listServers(true, ['flavorId'=>'50503afe-7a3e-4768-9a35-3f097264d6ee']);

		$ocpControlStatus = array();

		foreach ($ocpControlNodes as $node) {
			$nodeStatus = array("type"=>"Master/Infra","id"=>$node->id,"name"=>$node->name,"status"=>$node->status);
			array_push($ocpControlStatus,$nodeStatus);
		}

		$response = $app->response();
  	$response['Content-Type'] = 'application/json';
  	$response->body(json_encode($ocpControlStatus));
	}
);

$app->get(
	'/ocp-node-status',
	function() use ($app,$appConf) {
    $openStack = $app->openStack;

		$compute = $openStack->computeV2();
		$ocpNodes = $compute->listServers(true, ['flavorId'=>'4d3d73a3-4575-49a1-93d3-2a20c565aded']);
		$ocpNodeStatus = array();

		foreach ($ocpNodes as $node) {
			$nodeStatus = array("type"=>"Node","id"=>$node->id,"name"=>$node->name,"status"=>$node->status);
			array_push($ocpNodeStatus,$nodeStatus);
		}

		$response = $app->response();
  	$response['Content-Type'] = 'application/json';
  	$response->body(json_encode($ocpNodeStatus));
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
	'/random-ocp-node-id',
	function() use ($app, $appConf) {
		$serverArr = array();
		$openStack = $app->openStack;

		$compute = $openStack->computeV2();
		$servers = $compute->listServers(true, ['flavorId'=>'4d3d73a3-4575-49a1-93d3-2a20c565aded']);

		$nodeIds = array();
		foreach ($servers as $server) {
			array_push($nodeIds, $server->id);
    }

		$result = false;
		if (count($nodeIds) > 1) {
			$result = $nodeIds[array_rand($nodeIds)];
		}

		$response = $app->response();
  	$response['Content-Type'] = 'application/json';
  	$response->body(json_encode($result));
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
			$servers = $compute->listServers(false, ['flavorId'=>'4d3d73a3-4575-49a1-93d3-2a20c565aded']);

			$isOcpNode = false;
			$nodeCount = 0;
			foreach ($servers as $server) {
    		if ($id == $server->id) {
    			$isOcpNode = true;
    		}
				$nodeCount++;
    	}

    	if ($isOcpNode && $nodeCount > 1) {
    		$server = $compute->getServer(['id' => $id]);
				//TODO: uncomment later when done development
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
      $results['message'] = "Malformed request";
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
