$(function() {
  var stdout = []

  $("#button-chaos").click(function() {
    $(this).hide();
    $("#img-dk-gif").removeClass("d-none").addClass("d-block").show();

    stdout.push("> I have summoned the Human Chaos Monkey!!!!<br/>");
    stdout.push("> His job is to destroy a random node in your OCP cluster<br/>");
    stdout.push("> Prepare for destruction!!!!<br/>");

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
