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

function metric(Y, data, datatitle, max_yaxisvalue, metrictitle, combinedCharts)
{
   var jsondata = JSON.parse(data);

   var chartproperties = {
      bindto: '#chart',
      size: 	{height: 240},
      legend:	{show: false},
      grid: 	{y: {show: true}},
      data: 	{json: jsondata, keys: {x: 'Student', value: [datatitle]}, type: 'bar'},
      bar: 	{width: {ratio: 0.75}},
      axis: 	{x: {type: 'category'}, y: {max: max_yaxisvalue}}
   };

   if(combinedCharts)
      chartproperties.data = {json: jsondata, keys: 	{x: 'Student', value: [datatitle, 'Yours']}, type: 'bar', types: {Yours: 'area'}};

   var chart = c3.generate(chartproperties);

   var result = [];
   for(var k in jsondata)
   {
      var temp = [];
      temp.push(jsondata[k].Student);
      temp.push(jsondata[k][datatitle]);
      result.push(temp);
   }

   $(document).ready(function() {
      $('#metrictitle').html(metrictitle);

      $('#datatable').DataTable({
	 data: result,
	 columns: [{ title: "Student" }, {title: datatitle}],
	 "order": [[ 1, "desc" ]]
      });
   });	
}

function performance_overview(Y, data, datatitle, max_yaxisvalue, metrictitle)
{   
   var jsondata = JSON.parse(data);

   var chartproperties = {
      bindto: '#chart',
      size: 	{height: 240},
      legend:	{show: false},
      grid: 	{y: {show: true}},
      data: 	{json: jsondata, keys: {x: 'Student', value: [datatitle]}, type: 'bar'},
      bar: 	{width: {ratio: 0.75}},
      axis: 	{x: {type: 'category'}, y: {max: max_yaxisvalue}}
   };

   var chart = c3.generate(chartproperties);

   var result = [];
   for(var k in jsondata)
   {
      var temp = [];
      temp.push(jsondata[k]["ID"]);
      temp.push(jsondata[k]["Student"]);
      temp.push(jsondata[k]["First Attempt Quizzes Grades (FATG)"]);
      temp.push(jsondata[k]["Last Attempt Quizzes Grades (LATG)"]);
      temp.push(jsondata[k]["Average Quizzes Grades (AVR_QG)"]);
      temp.push(jsondata[k]["Assignments Average Grades (AVR_AG)"]);
      temp.push(jsondata[k]["Performance Index"]);
      temp.push(jsondata[k]["Visual Index"]);
      result.push(temp);
   }

   $(document).ready(function() {
      $('#metrictitle').html(metrictitle);

      $('#datatable').DataTable({
	 data: result,
	 "columnDefs": [ {"searchable": false, "orderable": false, "targets": [6]} ],
	 columns: [
			{title: "ID"},
	 		{title: "Student"},
		 	{title: "First Attempt Quizzes Grades (FATG)"},
		 	{title: "Last Attempt Quizzes Grades (LATG)"},
		 	{title: "Average Quizzes Grades (AVR_QG)"},
	 		{title: "Assignments Average Grades (AVR_AG)"},
		 	{title: "Performance Index"},
		 	{title: "Visual Index"}
      		  ],
	 order: [[ 6, "desc" ]]
      });

      var table = $('#datatable').DataTable();
      table.columns(0).visible(false);

      for(i = 0; i < result.length; i++)
      {
	 //var checkboxid = table.cell(i,0).data();
	 //table.cell(i,0).data("<input type='checkbox' id='" + checkboxid + "' />");
	 if(table.cell(i,6).data() >= 80)
      	   table.cell(i,7).data("<img src='./images/perf_green.png' />");
	 else if(table.cell(i,6).data() >= 65)
	    table.cell(i,7).data("<img src='./images/perf_lightgreen.png' />");
	 else if(table.cell(i,6).data() >= 50)
	    table.cell(i,7).data("<img src='./images/perf_orange.png' />");
	 else 
	    table.cell(i,7).data("<img src='./images/perf_red.png' />");
      }
   });	
}

