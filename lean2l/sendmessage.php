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
$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($courseid);
require_capability('local/lean2l:teachermode', $context);

define("ATRISK", 1);
define("MEDIUM", 2);
define("GOOD", 3);
define("VERY_GOOD", 4);

$students_cohort = required_param('stcoh', PARAM_INT);
$message_subject = "";
$stndrd_message = "";
$str_studentcohort = "";
switch($students_cohort)
{
case ATRISK:
   $message_subject = "Performance alert!";
   $str_studentcohort = " having ranked as \"At-Risk\"";
   $stndrd_message = "From the analysis of your activity in the learning platform, it has been come out that you need to change your study behaviour in order to pass the course.
      Otherwise your current performance show that you are at risk of failing to pass the course. Your results till now are:<br/>";
   break;
case MEDIUM:
   $message_subject = "Performance alert!";
   $str_studentcohort = " having ranked as \"Medium\"";
   $stndrd_message = "From the analysis of your activity in the learning platform, it has been come out that you need to intensify your effort to improve your performance. Although your results till now is a pass, nevertheless the risk of fail cannot be excluded. Your results till now are:<br/>";
   break;
case GOOD:
   $message_subject = "Performace overview!";
   $str_studentcohort = " having ranked as \"Good\"";
   $stndrd_message = "From the analysis of your activity in the learning platform, it has been come out that your performance is in a good level. Nevertheless, there is a margin for improvement. Your results till now are:<br/>";
   break;
case VERY_GOOD:
   $message_subject = "Performace overview!";
   $str_studentcohort = " having ranked as \"Very Good\"";
   $stndrd_message = "From the analysis of your activity in the learning platform, it has been come out that your performance is in a very good level. Keep on the good work. Your results till now are:<br/>";
   break;
default:
   break;
}

$json = file_get_contents('php://input');
$students = json_decode($json);
$str_students = "";
$flag = false;
foreach($students as $studentslist)
   foreach($studentslist as $value)
   {
      $message = new \core\message\message();
      $message->component = 'moodle';
      $message->name = 'instantmessage';
      $message->userfrom = $USER;
      $message->userto = $value[0];
      $message->subject = $message_subject;
      $message->fullmessage = $stndrd_message 	. "Average grade in your first attempts on quizzes: " . $value[2] . " | "
	 . "Average grade in your last attempts on quizzes: " . $value[3] . " | "
	 . "Average grade in all your attempts on quizzes: " . $value[4] . " | "
	 . "Average grade in your assignments: " . $value[5];
      $message->fullmessageformat = FORMAT_MARKDOWN;
      $message->fullmessagehtml = '<p>message body</p>';
      $messageid = message_send($message);
      if($flag) $str_students .= ',';
      else $flag = true;
      if($messageid) $str_students .= " " . $value[1];
   }



echo "<p>The following students " . $str_studentcohort . ", received a message with their performance results:<br/>" . $str_students . "</p>";

?>
