//NOTE: Only functionality that is application wide should be applied here.
$(document).ready(function() {
	var stdout = [];
	var randomOcpNodeId = false;

	$("#button-chaos").click(function() {
		$(this).hide();
		$("#img-dk-gif").removeClass("d-none").addClass("d-block").show();

		stdout.push("<li>I have summoned the Human Chaos Monkey!!!!</li>");
		stdout.push("<li>His job is to destroy a random node in your OCP cluster</li>");
		stdout.push("<li>Prepare for destruction!!!!</li>");
		stdout.push("<li>Now selecting random OCP Node for termination</li>");

		response = $.getJSON( "/random-ocp-node-id", function( data ) {
			console.log("data: "+data);
		}).done(function(data){
			randomOcpNodeId = data;
			console.log(randomOcpNodeId);
			stdout.push("<li>OCP Node with UUID: "+ randomOcpNodeId +" selected for termination</li>");

			$.getJSON("/delete-server/"+randomOcpNodeId, function(data){
				console.log("data: "+data);
				stdout.push("<li>Sent request to terminate OCP Node: "+ randomOcpNodeId +"</li>");
			}).done(function(result){
				stdout.push("<li>"+ result.message +"</li>");
			})
		});

		console.log(stdout);
	});

	//Check OCP Cluster Status
	setInterval(function() {
		console.log(stdout);
		getOcpControlStatus();
		getOcpNodeStatus();
	}, 15000);

	//Write to stdout
	setInterval(function() {
		console.log(stdout);
		writeStdOut();
	}, 3000);

	setInterval(function() {
		$("#div-app-console").animate({ scrollTop: $("#div-app-console").prop("scrollHeight") }, 3000);
	}, 6000);

	function getOcpClusterStatus() {
		$.get( "/ocp-cluster-status").done(function(results){
			$( "#div-cluster-health" ).html( results );
			stdout.push( "<li>OCP Cluster Status refreshed</li>" );
		});
	}

	function getOcpControlStatus() {
		$.get( "/ocp-control-status" ).done(function(results){
			var nodeIds = [];
			var nodeId;
			var nodeName;
			var nodeStatus;
			var visibleNodeIds =[];
			var deletedNodeIds = [];
			var liContent;

			console.log(results);

			for (var i = 0; i < results.length; i++) {
				console.log(results[i]);
				nodeIds.push(results[i].id);
				if(results[i].status == 'ACTIVE') {
					$("#badge-node-"+results[i].id).removeClass('alert-danger').addClass('alert-success').html("Healthy");
				} else {
					$("#badge-node-"+results[i].id).removeClass('alert-success').addClass('alert-danger').html("Unhealthy");
				}
			}

			console.log(nodeIds);

			$(".li-ocp-control").each(function() {
				nodeId = $(this).attr("aria-ocp-node-id");
				console.log(nodeId);
				visibleNodeIds.push(nodeId);
				if(nodeIds.indexOf(nodeId) == -1) { //== -1 for not in array
					$("#badge-node-"+nodeId).removeClass('alert-success').addClass('alert-danger').html("Unhealthy");
					$(this).fadeOut(5000).remove();
					deletedNodeIds.push(nodeId);
				}
			});

			console.log(visibleNodeIds);
			console.log(deletedNodeIds);

			for (var i = 0; i < results.length; i++) {
				console.log(results[i]);
				nodeId = results[i].id;
				nodeName = results[i].name;
				nodeStatus = results[i].status;

				console.log(nodeId);

				if(visibleNodeIds.indexOf(nodeId) == -1 && deletedNodeIds.indexOf(nodeId) == -1) {
					liContent = '<li id="li-node-'+nodeId+'" class="li-ocp-node list-group-item" aria-ocp-node-id="'+nodeId+'">'+nodeName;

					if(nodeStatus == 'ACTIVE') {
						liContent = liContent + '<span id="badge-node-'+nodeId+'" class="badge alert-success pull-right">Healthy</span>';
					} else {
						liContent = liContent + '<span id="badge-node-'+nodeId+'" class="badge alert-danger pull-right">Unhealthy</span>';
					}

					liContent = liContent + '</li>';

					$(".ul-ocp-control").append(liContent);
				}
			}

			stdout.push( "<li>OCP Master and Infra Status refreshed</li>" );
		});
	}

	function getOcpNodeStatus() {
		$.get( "/ocp-node-status" ).done(function(results){
			var nodeIds = [];
			var nodeId;
			var nodeName;
			var nodeStatus;
			var visibleNodeIds =[];
			var deletedNodeIds = [];
			var liContent;
			console.log(results);

			for (var i = 0; i < results.length; i++) {
				console.log(results[i]);
				nodeIds.push(results[i].id);
				if(results[i].status == 'ACTIVE') {
					$("#badge-node-"+results[i].id).removeClass('alert-danger').addClass('alert-success').html("Healthy");
				} else {
					$("#badge-node-"+results[i].id).removeClass('alert-success').addClass('alert-danger').html("Unhealthy");
				}
			}

			console.log(nodeIds);

			$(".li-ocp-node").each(function() {
				nodeId = $(this).attr("aria-ocp-node-id");
				console.log(nodeId);
				visibleNodeIds.push(nodeId);
				if(nodeIds.indexOf(nodeId) == -1) { //== -1 for not in array
					$("#badge-node-"+nodeId).removeClass('alert-success').addClass('alert-danger').html("Unhealthy");
					$(this).fadeOut(5000).remove();
					deletedNodeIds.push(nodeId);
				}
			});

			for (var i = 0; i < results.length; i++) {
				nodeId = results[i].id;
				nodeName = results[i].name;
				nodeStatus = results[i].status;

				if(visibleNodeIds.indexOf(nodeId) == -1 && deletedNodeIds.indexOf(nodeId) == -1) {
					liContent = '<li id="li-node-'+nodeId+'" class="li-ocp-node list-group-item" aria-ocp-node-id="'+nodeId+'">'+nodeName;

					if(nodeStatus == 'ACTIVE') {
						liContent = liContent + '<span id="badge-node-'+nodeId+'" class="badge alert-success pull-right">Healthy</span>';
					} else {
						liContent = liContent + '<span id="badge-node-'+nodeId+'" class="badge alert-danger pull-right">Unhealthy</span>';
					}

					liContent = liContent + '</li>';

					$(".ul-ocp-node").append(liContent);
				}
			}

			stdout.push( "<li>OCP Node Status refreshed</li>" );
		});
	}

	function writeStdOut() {
		$("#div-app-console").append(stdout.shift());
	}

});
