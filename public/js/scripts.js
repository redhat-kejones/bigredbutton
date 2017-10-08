$(function() {
  var stdout = []

  $("#button-chaos").click(function() {
    $(this).hide();
    $("#img-dk-gif").removeClass("d-none").addClass("d-block").show();

    stdout.push("<li>I have summoned the Human Chaos Monkey!!!!</li>");
    stdout.push("<li>His job is to destroy a random node in your OCP cluster</li>");
    stdout.push("<li>Prepare for destruction!!!!</li>");

    console.log(stdout)
  });

  setInterval(function() {
      console.log(stdout);
      writeStdOut();
    }, 3000);

  function writeStdOut() {
      $("#div-app-console").append(stdout.shift());
  }

});
