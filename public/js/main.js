//NOTE: Only functionality that is application wide should be applied here.
$(document).ready(function() {
	var stdout = [];
	var randomOcpNodeId = false;

	$("#button-chaos").click(function() {
		$(this).hide();
		//$("#img-dk-gif").removeClass("hidden").show();

		stdout.push("<li>I can't believe you actually clicked the Big Red Button!</li>");
		stdout.push("<li>The button's job is to destroy a random node in your OpenShift cluster</li>");

		response = $.getJSON( "/random-ocp-node-id" ).done(function(data){
			stdout.push("<li>Now selecting random OpenShift App Node for termination</li>");
			randomOcpNodeId = data;
			stdout.push("<li>OpenShift Node with UUID: "+ randomOcpNodeId +" selected for termination</li>");

			$.getJSON("/delete-server/"+randomOcpNodeId, function(data){
				stdout.push("<li>Sent request to terminate OpenShift Node: "+ randomOcpNodeId +"</li>");
			}).done(function(result){
				stdout.push("<li>"+ result.message +"</li>");
			})
		});

		stdout.push("<li>You should probably call the support for help!</li>");
	});

	//Check OCP Cluster Status
	setInterval(function() {
		getOcpControlStatus();
		getOcpNodeStatus();
		getLatestCfRequest();
	}, 15000);

	//Write to stdout
	setInterval(function() {
		writeStdOut();
	}, 3000);

	//setInterval(function() {
		//$("#div-app-console").animate({ scrollTop: $("#div-app-console").prop("scrollHeight") }, 3000);
	//}, 6000);

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

			for (var i = 0; i < results.length; i++) {
				nodeIds.push(results[i].id);
				if(results[i].novaStatus == 'ACTIVE') {
					$("#badge-node-"+results[i].id).removeClass('alert-danger').addClass('alert-success').html("Nova: "+results[i].novaStatus);
				} else {
					$("#badge-node-"+results[i].id).removeClass('alert-success').addClass('alert-danger').html("Nova: "+results[i].novaStatus);
				}

				if(results[i].ocpStatus == 'Ready') {
					$("#badge-ocp-node-"+results[i].id).removeClass('alert-danger').addClass('alert-success').html('OCP: '+results[i].ocpStatus);
				} else {
					$("#badge-ocp-node-"+results[i].id).removeClass('alert-success').addClass('alert-danger').html('OCP: '+results[i].ocpStatus);
				}
			}

			$(".li-ocp-control").each(function() {
				nodeId = $(this).attr("aria-ocp-node-id");
				visibleNodeIds.push(nodeId);
				if(nodeIds.indexOf(nodeId) == -1) { //== -1 for not in array
					$("#badge-node-"+nodeId).removeClass('alert-success').addClass('alert-danger').html("Nova: Unhealthy");
					$("#badge-ocp-node-"+nodeId).removeClass('alert-success').addClass('alert-danger').html("OCP: Not Ready");
					$(this).fadeOut(5000).remove();
					deletedNodeIds.push(nodeId);
				}
			});

			for (var i = 0; i < results.length; i++) {
				nodeId = results[i].id;
				nodeName = results[i].name;
				nodeNovaStatus = results[i].novaStatus;
				nodeOcpStatus = results[i].ocpStatus;

				if(visibleNodeIds.indexOf(nodeId) == -1 && deletedNodeIds.indexOf(nodeId) == -1) {
					liContent = '<li id="li-node-'+nodeId+'" class="li-ocp-node list-group-item" aria-ocp-node-id="'+nodeId+'">'+nodeName;

					if(nodeOcpStatus == 'Ready') {
						liContent = liContent + '<span id="badge-ocp-node-'+nodeId+'" class="badge alert-success pull-right">OCP: '+nodeOcpStatus+'</span>';
					} else {
						liContent = liContent + '<span id="badge-ocp-node-'+nodeId+'" class="badge alert-danger pull-right">OCP: '+nodeOcpStatus+'</span>';
					}

					if(nodeNovaStatus == 'ACTIVE') {
						liContent = liContent + '<span id="badge-node-'+nodeId+'" class="badge alert-success pull-right">Nova: '+nodeNovaStatus+'</span>';
					} else {
						liContent = liContent + '<span id="badge-node-'+nodeId+'" class="badge alert-danger pull-right">Nova: '+nodeNovaStatus+'</span>';
					}

					liContent = liContent + '</li>';

					$(".ul-ocp-control").append(liContent);
				}
			}

			var datetime = "LastSync: " + new Date().today() + " @ " + new Date().timeNow();
			$("#badge-ocp-control-update").addClass("alert-info").html(datetime);
			//stdout.push( "<li>OCP Master and Infra Status refreshed</li>" );
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

			for (var i = 0; i < results.length; i++) {
				nodeIds.push(results[i].id);
				if(results[i].novaStatus == 'ACTIVE') {
					$("#badge-node-"+results[i].id).removeClass('alert-danger').addClass('alert-success').html('Nova: '+results[i].novaStatus);
				} else {
					$("#badge-node-"+results[i].id).removeClass('alert-success').addClass('alert-danger').html('Nova: '+results[i].novaStatus);
				}

				if(results[i].ocpStatus == 'Ready') {
					$("#badge-ocp-node-"+results[i].id).removeClass('alert-danger').addClass('alert-success').html('OCP: '+results[i].ocpStatus);
				} else {
					$("#badge-ocp-node-"+results[i].id).removeClass('alert-success').addClass('alert-danger').html('OCP: '+results[i].ocpStatus);
				}
			}

			$(".li-ocp-node").each(function() {
				nodeId = $(this).attr("aria-ocp-node-id");
				visibleNodeIds.push(nodeId);
				if(nodeIds.indexOf(nodeId) == -1) { //== -1 for not in array
					$("#badge-node-"+nodeId).removeClass('alert-success').addClass('alert-danger').html("Nova: Unhealthy");
					$("#badge-ocp-node-"+nodeId).removeClass('alert-success').addClass('alert-danger').html("OCP: Not Ready");
					$(this).fadeOut(10000, function() {
						$(this).remove()
					});
					deletedNodeIds.push(nodeId);
				}
			});

			for (var i = 0; i < results.length; i++) {
				nodeId = results[i].id;
				nodeName = results[i].name;
				nodeNovaStatus = results[i].novaStatus;
				nodeOcpStatus = results[i].ocpStatus;

				if(visibleNodeIds.indexOf(nodeId) == -1 && deletedNodeIds.indexOf(nodeId) == -1) {
					liContent = '<li id="li-node-'+nodeId+'" class="li-ocp-node list-group-item" aria-ocp-node-id="'+nodeId+'">'+nodeName;

					if(nodeOcpStatus == 'Ready') {
						liContent = liContent + '<span id="badge-ocp-node-'+nodeId+'" class="badge alert-success pull-right">OCP: '+nodeOcpStatus+'</span>';
					} else {
						liContent = liContent + '<span id="badge-ocp-node-'+nodeId+'" class="badge alert-danger pull-right">OCP: '+nodeOcpStatus+'</span>';
					}

					if(nodeNovaStatus == 'ACTIVE') {
						liContent = liContent + '<span id="badge-node-'+nodeId+'" class="badge alert-success pull-right">Nova: '+nodeNovaStatus+'</span>';
					} else {
						liContent = liContent + '<span id="badge-node-'+nodeId+'" class="badge alert-danger pull-right">Nova: '+nodeNovaStatus+'</span>';
					}

					liContent = liContent + '</li>';

					$(".ul-ocp-node").append(liContent);
				}
			}

			var datetime = "LastSync: " + new Date().today() + " @ " + new Date().timeNow();
			$("#badge-ocp-node-update").addClass("alert-info").html(datetime);
			//stdout.push( "<li>OCP Node Status refreshed</li>" );
		});
	}

	function writeStdOut() {
		$("#div-app-console").append(stdout.shift());
	}

	function getLatestCfRequest() {
		var requestContent = '';
		$.get( "/cf-latest-service-request" ).done(function(results){

			if (results.request_state == 'finished') {
				if (results.status == 'Ok') {
					requestContent += '<span class="badge alert-success pull-right">Ok - ';
				} else {
					requestContent += '<span class="badge alert-danger pull-right">Error - ';
				}
			} else if (results.request_state == 'active') {
				requestContent += '<span class="badge alert-warning pull-right">';
				//$("#img-dk-gif").hide();
			} else if (results.request_state == 'pending') {
				requestContent += '<span class="badge alert-info pull-right">';
			} else {
				requestContent += '<span class="badge alert-info pull-right">';
			}
			requestContent += results.request_state+'</span>';

			requestContent += '<div><strong>Description:</strong> '+results.description;
			requestContent += '<br/><strong>Message:</strong> '+results.message;
			requestContent += '<br/><strong>Created:</strong> '+results.created_on+' <strong>Updated:</strong> '+results.updated_on+' <strong>Fulfilled:</strong> '+results.fulfilled_on+'</div>';

			$("#cf-latest-service-request").html(requestContent);

			var datetime = "LastSync: " + new Date().today() + " @ " + new Date().timeNow();
			$("#badge-cf-request-update").addClass("alert-info").html(datetime);
			//stdout.push( "<li>OCP Master and Infra Status refreshed</li>" );
		});
	}

	//OCP Functions

	// For todays date;
	Date.prototype.today = function () {
	    return (((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+ this.getFullYear();
	}

	// For the time now
	Date.prototype.timeNow = function () {
	     return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
	}

});
