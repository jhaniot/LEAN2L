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
   var div_id_selector = "#div_m";
   var anchor_id_selector = "#anchor_m";
   var quizlistitemanchor = "quizlistitemanchor";
   
   for(i = 1; i <= 8; i++)
      $(div_id_selector+i).hide();
   $("#div_asgn_m1").hide();

   $("#div_m1").find("a").each(function(){
      $(this).attr("href", "quizmetrics.php?mid=1&qid=" + $(this).find("li").first().attr("qid") + "&id=" + $(this).find("li").first().attr("cid"));
   });
   $("#div_m3").find("a").each(function(){
      $(this).attr("href", "quizmetrics.php?mid=3&qid=" + $(this).find("li").first().attr("qid") + "&id=" + $(this).find("li").first().attr("cid"));
   });
   $("#div_m4").find("a").each(function(){
      $(this).attr("href", "quizmetrics.php?mid=4&qid=" + $(this).find("li").first().attr("qid") + "&id=" + $(this).find("li").first().attr("cid"));
   });
   $("#div_m5").find("a").each(function(){
      $(this).attr("href", "quizmetrics.php?mid=5&qid=" + $(this).find("li").first().attr("qid") + "&id=" + $(this).find("li").first().attr("cid"));
   });
   $("#div_m7").find("a").each(function(){
      $(this).attr("href", "quizmetrics.php?mid=7&qid=" + $(this).find("li").first().attr("qid") + "&id=" + $(this).find("li").first().attr("cid"));
   });
   $("#div_m8").find("a").each(function(){
      $(this).attr("href", "quizmetrics.php?mid=8&qid=" + $(this).find("li").first().attr("qid") + "&id=" + $(this).find("li").first().attr("cid"));
   });
//   $("#div_m9").find("a").each(function(){
//      $(this).attr("href", "quizmetrics.php?mid=9&qid=" + $(this).find("li").first().attr("qid") + "&id=" + $(this).find("li").first().attr("cid"));
//   });

   $("#div_asgn_m1").find("a").each(function(){
      $(this).attr("href", "assignmentmetrics.php?mid=1&asgnid=" + $(this).find("li").first().attr("asgnid") + "&id=" + $(this).find("li").first().attr("cid"));
   });


   $("#anchor_m1").click(function(){$("#div_m1").toggle();});
   $("#anchor_m2").click(function(){$("#div_m2").toggle();});
   $("#anchor_m3").click(function(){$("#div_m3").toggle();});
   $("#anchor_m4").click(function(){$("#div_m4").toggle();});   
   $("#anchor_m5").click(function(){$("#div_m5").toggle();});
   $("#anchor_m6").click(function(){$("#div_m6").toggle();});
   $("#anchor_m7").click(function(){$("#div_m7").toggle();});
   $("#anchor_m8").click(function(){$("#div_m8").toggle();});

   $("#anchor_asgn_m1").click(function(){$("#div_asgn_m1").toggle();});

   $("#anchor_gm1").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_m1").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_m2").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_m3").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_m4").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_m5").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_m6").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_m7").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_m8").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_asgn_m1").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});
   $("#anchor_asgn_m2").tooltip({show: {effect: "slideDown", delay: 100}, position: { my: "left+15 center", at: "right center" }});

});
