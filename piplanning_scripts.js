$(function (){
    'use strict';
    
    function getTeams(art_select){
        //gets values from JSON file
        $.getJSON('dataFiles/at_cache.json', function(data){
          //initializes an array to story the avilable teams
          var at_list = [];
          //for loop for adding team names to teams_list
          var x=data.length;
          for(var i=0; i < x ; i++){
            var parent = data[i].parent_name;
            if(parent == art_select){
              at_list.push(data[i].team_name);
            }
            //updates teams with the calculated list
            console.log(at_list);
            //sets teams as a cookie
            document.cookie = escape('teamCookie') + '=' + escape(at_list) ;
          };
        });
      };
      //function for capturing the cookie
      function getCookie(cookieName) {
        var name = cookieName + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1);
          }
          if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
          }
        }
        return "";
      };
});