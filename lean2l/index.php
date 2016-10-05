<?php
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
 * LEAN2L - LEarning ANalytics TOOL
 *
 * @package    local_lean2l
 * @copyright  2016 Ioannis Chaniotakis (jhaniot@hotmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');        
require_once('BusinessLogic.php');


$PAGE->set_title(get_string('Welcome', 'local_lean2l'));
$PAGE->requires->jquery();

$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
require_login($course);
$PAGE->set_pagelayout('incourse');
$context = context_course::instance($courseid);

if(has_capability('local/lean2l:teachermode', $context))
{
   $testObj = new BusinessLogic($DB, $context, true);
   add_to_log(SITEID, 'local_lean2l', 'teachermode', 'local/lean2l/index.php?name=' . urlencode($name));
}
else if(has_capability('local/lean2l:studentmode', $context))
{
   $testObj = new BusinessLogic($DB, $context, false);
   add_to_log(SITEID, 'local_lean2l', 'studentmode', 'local/lean2l/index.php?name=' . urlencode($name));
}
else
   require_capability('local/lean2l:teachermode', $context);

$PAGE->set_context($context);                        



echo $OUTPUT->header();
?>

<link href="js/jquery-ui.min.css" rel="stylesheet" type="text/css">
<script src="js/rjquery.js" charset="utf-8"></script>
<script src="js/jquery-ui.min.js" charset="utf-8"></script>
<script>
  $( function() {
    $( "#dialog" ).dialog({
      autoOpen: false,
      show: {
        effect: "drop",
        duration: 500
      },
      hide: {
        effect: "drop",
        duration: 500
      },
    });
 
    $( "#opener" ).on( "click", function() {
      $( "#dialog" ).dialog( "open" );
    });
  } );
  </script>

<?php

echo $OUTPUT->heading('Learning Analytics');
/*$developer = optional_param('dev', NULL, PARAM_INT);
if($developer != NULL)
{
   echo $OUTPUT->box("<p style=\"padding:5px;line-height:1.5;background-color:#D8F6CE;'\">This Moodle extension has been developed by Ioannis Chaniotakis within the context of the dissertation for the MBA course of Hellenic Open University.<br />For further details and assistance, please contact at jhaniot@hotmail.com or at +30 6944871169</p>");
}*/
echo $OUTPUT->box("<hr />");
if(has_capability('local/lean2l:teachermode', $context))
{
   echo $OUTPUT->heading("General metrics", 3);
   echo $OUTPUT->box("<a href='generalmetrics.php?mid=1&id=" . $courseid . "'  id='anchor_gm1' title='This gives an overview of the performance of the students with a relevant coloured indicator.'>Overview of the students' performance.</a>");
   echo $OUTPUT->box("<hr />");
}
echo $OUTPUT->heading('Quizzes metrics', 3);
$data = $testObj->getQuizzes($courseid);

$divcontent = "<ul>";
foreach($data as $key=>$value)
   $divcontent .= "<a><li cid='" . $courseid . "' qid='" . $key . "'>Quiz:&nbsp;" . $value . "</li></a>";
$divcontent .= "</ul>";

$str_quizmetricslist = "<ol><li><a id='anchor_m1' title='Presents the number of times each student has attempted a quiz. Click to show/hide the quizzes.'>
   				Number of attempts per student for a quiz.</a><div id='div_m1'>" . $divcontent . "</div></li>";
$str_quizmetricslist .= "<li><a href='quizmetrics.php?mid=2&id=" . $courseid . "&qid=0' id='anchor_m2' 
   				title='Presents the average number of times each student attempts the quizzes of the course.'>
				Average number of attempts per student in the course's quizzes.</a></li>";
$str_quizmetricslist .= "<li><a id='anchor_m3' title='Presents the grade each student has achieved in their first attempt on a quiz. Click to show/hide the quizzes.'>
   				First attempt grades of a quiz</a><div id='div_m3'>" . $divcontent . "</div></li>";
$str_quizmetricslist .= "<li><a id='anchor_m4' title='Presents the grade each student has achieved in their last attempt on a quiz. Click to show/hide the quizzes.'>
   				Last attempt grades of a quiz.</a><div id='div_m4'>" . $divcontent . "</div></li>";
$str_quizmetricslist .= "<li><a id='anchor_m5' title='Presents the average grade each student has achieved in their attempts on a quiz. Click to show/hide the quizzes.'>
   				Average grades in all attempts of a quiz.</a><div id='div_m5'>" . $divcontent . "</div></li>";
$str_quizmetricslist .= "<li><a href='quizmetrics.php?mid=6&id=" . $courseid . "&qid=0' id='anchor_m6' 
   				title='Presents the average grade each student has achieved in their first attempts on all the quizzes of the course. Click to show/hide the quizzes.'>
   				Average grades of the first attempt grades in all quizzes of the course.</a></li>";
$str_quizmetricslist .= "<li><a id='anchor_m7' 
   				title='Presents the average grade each student has achieved in their last attempts on all the quizzes of the course. Click to show/hide the quizzes.'>
   				Average grades of the last attempt grades in all quizzes of the course.</a><div id='div_m7'>" . $divcontent . "</div></li>";
$str_quizmetricslist .= "<li><a id='anchor_m8'
				title='Presents the average grade each student has achieved in all their attempts on all the quizzes of the course. Click to show/hide the quizzes.'>
   				Average grades of the average grades in all quizzes.</a><div id='div_m8'>" . $divcontent . "</div></li></ol>";
echo $OUTPUT->box($str_quizmetricslist);

$data = $testObj->getAssignments($courseid);
$divcontent = "<ul>";
foreach($data as $key=>$value)
   $divcontent .= "<a><li cid='" . $courseid . "' asgnid='" . $key . "'>Assignment:&nbsp;" . $value . "</li></a>";
$divcontent .= "</ul>";

$str_assignmentmetricslist = "<ol><li><a id='anchor_asgn_m1' title='Presents the grades achieved by students in an assignemnt. Click to show/hide the assignments'>
   					Grades of students in an assignment.</a><div id='div_asgn_m1'>" . $divcontent . "</div></li>";
$str_assignmentmetricslist .= "<li><a href='assignmentmetrics.php?mid=2&id=" . $courseid . "&asgnid=0' id='anchor_asgn_m2' 
   					title='Presents the average grades achieved by the students in all the assignments of the course'>
   					Average grades in all assignments.</a></li></ol>";
echo $OUTPUT->box("<hr />");
echo $OUTPUT->heading('Assignment metrics', 3);
echo $OUTPUT->box($str_assignmentmetricslist);
echo $OUTPUT->box("<hr />");
?>
<div id="dialog" title="About">
  <p>This Moodle extension has been developed by Ioannis Chaniotakis within the context of the dissertation for the MBA course of Hellenic Open University.<br />For further details and assistance, please contact at jhaniot@hotmail.com or at +306944871169</p>
</div>
 
<p style="text-align: center;"><span><a href="#" id="opener">About</a></span></p>

<?php
/*
$str_correlationmetricslist = "<ol><li><a id='m12_title'>Correlation between quizzes grades and assignment grades.</a></li>";
$str_correlationmetricslist .= "<li><a id='m13_title'>Correlation between the number of attempts in the quizzes and the assignment grades.</a></li></ol>";
echo $OUTPUT->box("<hr />");
echo $OUTPUT->heading('Correlation metrics', 3);
echo $OUTPUT->box($str_correlationmetricslist);
 */


echo $OUTPUT->footer();
