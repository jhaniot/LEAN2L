// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Learning Analytics plugin
 *
 * @package    local_learninganalytics
 * @copyright  2016 Ioannis Chaniotakis (jhaniot@hotmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$(document).ready(function() {
   $('#messageresponse').hide();

   $("#SendMsgButtonToAtRiskStuds").click(function(){
      $('#messageresponse').html("Sending the message to the students ranked as \"At-Risk\". Please wait...<img src='./images/ajax-loader.gif' />");
      var table = $('#datatable').DataTable();
      var datatosend = '{"Students":[';
      
      var datalength = table.rows().count();
      var flag = false;
      for(i = 0; i < datalength; i++)
      	if(table.cell(i, 6).data() < 50)
	      {
		 if(flag == true) datatosend += ',';
		 else flag = true;
		 datatosend += '[' + table.cell(i,0).data() + ',';
		 datatosend += '"' + table.cell(i,1).data() + '",';
		 datatosend += table.cell(i,2).data() + ',';
		 datatosend += table.cell(i,3).data() + ',';
		 datatosend += table.cell(i,4).data() + ',';
		 datatosend += table.cell(i,5).data() + ']';
	      }
      datatosend += ']}';

      $('#messageresponse').show();
      $.ajax({
	 type: 'POST',
	 url: 'sendmessage.php?id=' + $("#SendMsgButtonToAtRiskStuds").attr("courseid") + '&stcoh=1',
	 data: datatosend,
	 success: function(response) {
	    $("#messageresponse").addClass("userinfobox");
	    $("#messageresponse").html(response);

	 }
      });
   });


   $("#SendMsgButtonToMediumStuds").click(function(){
      $('#messageresponse').html("Sending the message to the students ranked as \"Medium\". Please wait...<img src='./images/ajax-loader.gif' />");
      var table = $('#datatable').DataTable();
      var datatosend = '{"Students":[';
      
      var datalength = table.rows().count();
      var flag = false;
      for(i = 0; i < datalength; i++)
      	if(table.cell(i, 6).data() >= 50 && table.cell(i, 6).data() < 65)
	      {
		 if(flag == true) datatosend += ',';
		 else flag = true;
		 datatosend += '[' + table.cell(i,0).data() + ',';
		 datatosend += '"' + table.cell(i,1).data() + '",';
		 datatosend += table.cell(i,2).data() + ',';
		 datatosend += table.cell(i,3).data() + ',';
		 datatosend += table.cell(i,4).data() + ',';
		 datatosend += table.cell(i,5).data() + ']';
	      }
      datatosend += ']}';

      $('#messageresponse').show();
      $.ajax({
	 type: 'POST',
	 url: 'sendmessage.php?id=' + $("#SendMsgButtonToMediumStuds").attr("courseid") + '&stcoh=2',
	 data: datatosend,
	 success: function(response) {
	    $("#messageresponse").addClass("userinfobox");
	    $("#messageresponse").html(response);

	 }
      });
   });


   $("#SendMsgButtonToGoodStuds").click(function(){
      $('#messageresponse').html("Sending the message to the students ranked as \"Good\". Please wait...<img src='./images/ajax-loader.gif' />");
      var table = $('#datatable').DataTable();
      var datatosend = '{"Students":[';
      
      var datalength = table.rows().count();
      var flag = false;
      for(i = 0; i < datalength; i++)
      	if(table.cell(i, 6).data() >= 65 && table.cell(i, 6).data() < 80)
	      {
		 if(flag == true) datatosend += ',';
		 else flag = true;
		 datatosend += '[' + table.cell(i,0).data() + ',';
		 datatosend += '"' + table.cell(i,1).data() + '",';
		 datatosend += table.cell(i,2).data() + ',';
		 datatosend += table.cell(i,3).data() + ',';
		 datatosend += table.cell(i,4).data() + ',';
		 datatosend += table.cell(i,5).data() + ']';
	      }
      datatosend += ']}';

      $('#messageresponse').show();
      $.ajax({
	 type: 'POST',
	 url: 'sendmessage.php?id=' + $("#SendMsgButtonToGoodStuds").attr("courseid") + '&stcoh=3',
	 data: datatosend,
	 success: function(response) {
	    $("#messageresponse").addClass("userinfobox");
	    $("#messageresponse").html(response);

	 }
      });      
   });
   

   $("#SendMsgButtonToVeryGoodStuds").click(function(){
      $('#messageresponse').html("Sending the message to the students ranked as \"Very Good\". Please wait...<img src='./images/ajax-loader.gif' />");
      var table = $('#datatable').DataTable();
      var datatosend = '{"Students":[';
      
      var datalength = table.rows().count();
      var flag = false;
      for(i = 0; i < datalength; i++)
      	if(table.cell(i, 6).data() >= 80)
	      {
		 if(flag == true) datatosend += ',';
		 else flag = true;
		 datatosend += '[' + table.cell(i,0).data() + ',';
		 datatosend += '"' + table.cell(i,1).data() + '",';
		 datatosend += table.cell(i,2).data() + ',';
		 datatosend += table.cell(i,3).data() + ',';
		 datatosend += table.cell(i,4).data() + ',';
		 datatosend += table.cell(i,5).data() + ']';
	      }
      datatosend += ']}';

      $('#messageresponse').show();
      $.ajax({
	 type: 'POST',
	 url: 'sendmessage.php?id=' + $("#SendMsgButtonToVeryGoodStuds").attr("courseid") + '&stcoh=4',
	 data: datatosend,
	 success: function(response) {
	    $("#messageresponse").addClass("userinfobox");
	    $("#messageresponse").html(response);

	 }
      });      
   });


});
