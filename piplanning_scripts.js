$(function () {
  
  'use strict';
  function autoForm() {
    document.getElementById('maincap').submit();
  }
  
  function autoLoad(overhead,duration,value,totalcap_old) {
    var overhead = "";
    var duration = "";
    var value = "";
    var totalcap_old = "";
    var velocity = $("input[name='velocity[]']")

    .map(function(){return $(this).val();}).get();
    var daysoff = $("input[name='daysoff[]']")
    .map(function(){return $(this).val();}).get();
    var rownum = $("input[name='rownum[]']")
    .map(function(){return $(this).val();}).get();
    var icap = 0;
    for (var i in rownum) {
      var storypts = Math.round( ( duration - daysoff[i] ) * ( ( 100-overhead ) / 100 ) * ( velocity[i] / 100 ) );
      $("input[name='storypoints[]']").eq(i).val(storypts);
      icap += storypts;
    }
    document.getElementsByName("icap")[0].innerHTML = icap;
    var capdiff = icap - icap_old;
    var tcap = parseInt(capdiff) + parseInt(totalcap_old);
    document.getElementsByName("totalcap")[0].innerHTML = tcap;
  }
});