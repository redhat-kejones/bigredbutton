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
		getOcpClusterStatus();
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
		$.get( "/ocp-cluster-status", function( data ) {
			$( "#div-cluster-health" ).html( data );
		});
		stdout.push( "<li>OCP Cluster Status refreshed</li>" );
	}

	function writeStdOut() {
		$("#div-app-console").append(stdout.shift());
	}

});
