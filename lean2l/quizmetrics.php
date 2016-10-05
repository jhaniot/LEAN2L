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
$PAGE->requires->jquery();
$PAGE->set_title(get_string('Welcome', 'local_lean2l'));
$courseid = required_param('id', PARAM_INT);
$metricid = required_param('mid', PARAM_INT);
$quizid = required_param('qid', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
require_login($course);
$PAGE->set_pagelayout('incourse');
$context = context_course::instance($courseid);

$PAGE->navbar->add('Learning Analytics', new moodle_url('./index.php?id=' . $courseid));
$PAGE->navbar->add('Quizzes\' metrics');

$chartscombination = false;
$testObj = NULL;
if(has_capability('local/lean2l:teachermode', $context))
{
   $testObj = new BusinessLogic($DB, $context, true);
   add_to_log(SITEID, 'local_lean2l', 'teachermode', 'local/lean2l/index.php?name=' . urlencode($name));
}
else if(has_capability('local/lean2lstudentmode', $context))
{
   $testObj = new BusinessLogic($DB, $context, false, $USER->id);
   add_to_log(SITEID, 'local_lean2l', 'studentmode', 'local/lean2l/index.php?name=' . urlencode($name));
   $chartscombination = true;
}
else
   require_capability('local/lean2l:teachermode', $context);

$PAGE->set_context($context);                        




echo $OUTPUT->header();
?>
<link href="js/c3.min.css" rel="stylesheet" type="text/css">
<link href="js/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
<script src="js/datatables.min.js" charset="utf-8"></script>
<script src="js/d3.min.js" charset="utf-8"></script>
<script src="js/c3.min.js"></script>
<script src="js/datavisualization.js"></script>

<?php
echo $OUTPUT->heading('Learning Analytics');
echo $OUTPUT->box('<h4 id=\'metrictitle\'></h4>');
echo $OUTPUT->box('<hr/>');
echo $OUTPUT->box('<div id=\'chart\'></div>');
echo $OUTPUT->box('<hr/>');
echo $OUTPUT->box('<table id="datatable" class="display" width="100%"></table>');


switch($metricid) 
{
case 1:
   $data = $testObj->getQuizAttempts($quizid);
   $metrictitle = "Number of attempts per student on the quiz <i>\"" . $testObj->getQuizName($quizid) . "\"</i>.";
   $PAGE->requires->js_init_call('metric', array(json_encode($data, JSON_PRETTY_PRINT), "Attempts", intval($testObj->getQuizMaxNumAttempts($quizid)), $metrictitle, $chartscombination));
   break;
case 2:
   $data = $testObj->getCourseQuizzesAverageAttempts($courseid);
   $metrictitle = "Average number of attempts of each student in the quizzes of the course.";
   $PAGE->requires->js_init_call('metric', array(json_encode($data, JSON_PRETTY_PRINT), "Average quizzes' attempts", intval($testObj->getCourseQuizzesMaxNumAttempts($courseid)), $metrictitle, $chartscombination));
   break;   
case 3:
   $data = $testObj->getQuizFirstAttemptGrades($quizid);
   $metrictitle = "Grades achieved by students in their first attempt on the quiz <i>\"" . $testObj->getQuizName($quizid) . "\"</i>.";
   $PAGE->requires->js_init_call('metric', array(json_encode($data, JSON_PRETTY_PRINT), "Grade", 99, $metrictitle, $chartscombination));
   break;
case 4:
   $data = $testObj->getQuizLastAttemptGrades($quizid);
   $metrictitle = "Grades achieved by students in their last attempt on the quiz <i>\"" . $testObj->getQuizName($quizid) . "\"</i>.";
   $PAGE->requires->js_init_call('metric', array(json_encode($data, JSON_PRETTY_PRINT), "Grade", 99, $metrictitle, $chartscombination));
   break;
case 5:
   $data = $testObj->getQuizAverageGrades($quizid);
   $metrictitle = "Average grades achieved by students in all of their attempts on the quiz <i>\"" . $testObj->getQuizName($quizid) . "\"</i>.";
   $PAGE->requires->js_init_call('metric', array(json_encode($data, JSON_PRETTY_PRINT), "Grade", 99, $metrictitle, $chartscombination));
   break;
case 6:
   $data = $testObj->getCourseQuizzesFirstAttemptsAverageGrades($courseid);
   $metrictitle = "Average grades of the students in their first attempts on the quizzes of the course.";
   $PAGE->requires->js_init_call('metric', array(json_encode($data, JSON_PRETTY_PRINT), "Average grade in quizzes' first attempts", 99, $metrictitle, $chartscombination));
   break;
case 7:
   $data = $testObj->getCourseQuizzesLastAttemptsAverageGrades($courseid);
   $metrictitle = "Average grades of the students in their last attempts on the quizzes of the course.";
   $PAGE->requires->js_init_call('metric', array(json_encode($data, JSON_PRETTY_PRINT), "Average grades in quizzes' last attempts", 99, $metrictitle, $chartscombination));
   break;
case 8:
   $data = $testObj->getCourseQuizzesAverageOfAverageGrades($courseid);
   $metrictitle = "Average grades of the students in all their attempts on the quizzes of a course.";
   $PAGE->requires->js_init_call('metric', array(json_encode($data, JSON_PRETTY_PRINT), "Average grades", 99, $metrictitle, $chartscombination));
   break;    
default:
   break;

}

echo $OUTPUT->footer();                            

