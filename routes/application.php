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
			$nodeStatus = array("type"=>"Master/Infra","id"=>$node->id,"name"=>$node->name,"novaStatus"=>$node->status,"ocpStatus"=>"Ready");
			array_push($ocpControlStatus,$nodeStatus);
		}

		foreach ($ocpNodes as $node) {
			$nodeStatus = array("type"=>"Node","id"=>$node->id,"name"=>$node->name,"novaStatus"=>$node->status,"ocpStatus"=>"Ready");
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
			$nodeStatus = array("type"=>"Master/Infra","id"=>$node->id,"name"=>$node->name,"novaStatus"=>$node->status,"ocpStatus"=>"Ready");
			array_push($ocpControlStatus,$nodeStatus);
		}

		foreach ($ocpNodes as $node) {
			$nodeStatus = array("type"=>"Node","id"=>$node->id,"name"=>$node->name,"novaStatus"=>$node->status,"ocpStatus"=>"Ready");
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
			$nodeStatus = array("type"=>"Master/Infra","id"=>$node->id,"name"=>$node->name,"novaStatus"=>$node->status,"ocpStatus"=>"Ready");
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
		$ocpNodeStatus = array();
		$ocpNodes = array();

		$openStack = $app->openStack;

		$compute = $openStack->computeV2();
		$ospNodes = $compute->listServers(true, ['flavorId'=>'4d3d73a3-4575-49a1-93d3-2a20c565aded']);

		$ocpNodes = array();
		$client = new GuzzleHttp\Client( ['base_uri' => $appConf->__get('ocpApiUrl')]);
		$headers = [
    	'Authorization' => 'Bearer ' . $appConf->__get('ocpToken'),
    	'Accept'        => 'application/json',
		];
		$results = $client->request('GET','api/v1/nodes', [
		    'headers' => $headers,
				'verify' => false
		]);

		$contentsClass = json_decode($results->getBody()->getContents());

		foreach ($contentsClass->items as $ocpNode) {
			if(stripos($ocpNode->metadata->name,'master') !== false) {
				continue;
			}
			if(isset($ocpNode->metadata->labels->region) && $ocpNode->metadata->labels->region == 'infra') {
				continue;
			}

			$ocpStatus = "Not Ready";
			if($ocpNode->status->conditions[3]->status == "True") {
				$ocpStatus = "Ready";
			}

			$ocpNodes[substr($ocpNode->metadata->name,0,strpos($ocpNode->metadata->name,'.'))] = $ocpStatus;
		}

		foreach ($ospNodes as $ospNode) {
			$ocpStatus = "Not Ready";
			if(isset($ocpNodes[$ospNode->name])) {
				$ocpStatus = $ocpNodes[$ospNode->name];
			}

			$nodeStatus = array("type"=>"Node","id"=>$ospNode->id,"name"=>$ospNode->name,"novaStatus"=>$ospNode->status,"ocpStatus"=>$ocpStatus);
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

//CF routes
$app->get(
	'/cf-latest-service-request',
	function() use ($app, $appConf) {
		$client = new GuzzleHttp\Client();
		$results = $client->get($appConf->__get('cfApiUrl').'service_requests', [
		    'auth' => [
		      $appConf->__get('cfUsername'),
		     	$appConf->__get('cfPassword')
		    ],
				'verify' => false
		]);

		$contentsClass = json_decode($results->getBody()->getContents());

		$requestIds = array();
		foreach ($contentsClass->resources as $resource) {
			$parts = parse_url($resource->href);
			$requestId = str_replace('/api/service_requests/','',$parts['path']);
			array_push($requestIds, $requestId);
		}

		sort($requestIds);

		$results = $client->get($appConf->__get('cfApiUrl').'service_requests/'.end($requestIds), [
		    'auth' => [
					$appConf->__get('cfUsername'),
					$appConf->__get('cfPassword')
		    ],
				'verify' => false
		]);

		$lastRequest = json_decode($results->getBody()->getContents());

    $response = $app->response();
  	$response['Content-Type'] = 'application/json';
  	$response->body(json_encode($lastRequest));
  }
);

// OCP Routes
$app->get(
	'/ocp-api',
	function() use ($app, $appConf) {
		$nodes = array();
		$client = new GuzzleHttp\Client( ['base_uri' => $appConf->__get('ocpApiUrl')]);
		$headers = [
    	'Authorization' => 'Bearer ' . $appConf->__get('ocpToken'),
    	'Accept'        => 'application/json',
		];
		$results = $client->request('GET','api/v1/nodes', [
		    'headers' => $headers,
				'verify' => false
		]);

		$contentsClass = json_decode($results->getBody()->getContents());

		foreach ($contentsClass->items as $node) {
			var_dump($node->metadata->name);
			var_dump(stripos($node->metadata->name,'master'));
			if(stripos($node->metadata->name,'master') !== false) {
				print("I'm a master node");
				continue;
			}
			if(isset($node->metadata->labels->region) && $node->metadata->labels->region == 'infra') {
				print("I'm an infra node");
				continue;
			}
			var_dump($node);
			//$node->status->conditions
			array_push($nodes, $node);
		}

    $response = $app->response();
  	$response['Content-Type'] = 'application/json';
  	//$response->body(json_encode($contentsClass));
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
