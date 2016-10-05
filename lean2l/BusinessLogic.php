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
 * Learning Analytics plugin
 *
 * @package    local_lean2l
 * @copyright  2016 Ioannis Chaniotakis (jhaniot@hotmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class BusinessLogic
{
   private $dbconn;
   private $context;
   private $isteachermode;
   private $userid;

   function __construct($db, $context, $isteachermode, $userid = -1)
   {
      require_once(dirname(__FILE__) . '/../../config.php');
      $this->context = $context;
      $this->dbconn = $db;
      $this->isteachermode = $isteachermode;
      $this->userid = $userid;
   }
   
   function getQuizMaxNumAttempts($quizid)
   {
      $max_atttempt = 10;
      $rs = $this->dbconn->get_recordset_sql('SELECT MAX(attempt) AS MAXATT FROM mdl_quiz_attempts WHERE quiz=?', array($quizid));
      foreach($rs as $record)
	 $max_atttempt = $record->maxatt;
      $rs->close();

      return $max_atttempt;
   }

   function getCourseQuizzesMaxNumAttempts($courseid)
   {
      $max_atttempt = 10;
      $rs = $this->dbconn->get_recordset_sql('SELECT MAX(qat.attempt) AS MAXATT FROM mdl_quiz_attempts qat, mdl_quiz qz WHERE qz.id=qat.quiz AND course=?', array($courseid));
      foreach($rs as $record)
	 $max_atttempt = $record->maxatt;
      $rs->close();

      return $max_atttempt;
   }   

   function getQuizzes($courseid)
   {
      $data = array();
      $rs = $this->dbconn->get_recordset_sql('SELECT id, name FROM mdl_quiz WHERE course=? ORDER BY timeopen ASC', array($courseid));
      foreach($rs as $record)
	 $data[$record->id] = $record->name;
      $rs->close();

      return $data;
   }

   function getAssignments($courseid)
   {
      $data = array();
      $rs = $this->dbconn->get_recordset_sql('SELECT id, name FROM mdl_assign WHERE course=? ORDER BY duedate ASC', array($courseid));
      foreach($rs as $record)
	 $data[$record->id] = $record->name;
      $rs->close();

      return $data;
   }

   function getQuizName($quizid)
   {
      $quiz = $this->dbconn->get_record_sql('SELECT name FROM mdl_quiz WHERE id=?', array($quizid), MUST_EXIST);
      return $quiz->name;
   }

   function getAssignmentName($asgnid)
   {
      $quiz = $this->dbconn->get_record_sql('SELECT name FROM mdl_assign WHERE id=?', array($asgnid), MUST_EXIST);
      return $quiz->name;
   }

   function getStudentsOverallPerformance($courseid)
   {
      $fat_quizzes_grades = $this->getIdKeyData('getCourseQuizzesFirstAttemptsAverageGrades', array($courseid));
      $lat_quizzes_grades = $this->getIdKeyData('getCourseQuizzesLastAttemptsAverageGrades', array($courseid));
      $avrg_quizzes_grades = $this->getIdKeyData('getCourseQuizzesAverageOfAverageGrades', array($courseid));
      $avrg_assignments_grades = $this->getIdKeyData('getAssignmentAverageGrades', array($courseid));

      $students_num = count($fat_quizzes_grades);
      assert(count($lat_quizzes_grades) == $students_num && count($avrg_assignments_grades) == $students_num 
	 && count($avrg_quizzes_grades) == $students_num, "Unequal data sets in getStudentsOverallPerformance");

      $combined_array = array();
	 $helparrayforsorting = array();

      foreach($fat_quizzes_grades as $key=>$value)
      {
	 $fatg = $value['Average grade in quizzes\' first attempts'];
	 $latg = $lat_quizzes_grades[$key]['Average grades in quizzes\' last attempts'];
	 $avr_qg = $avrg_quizzes_grades[$key]['Average grades'];
	 $avr_ag = $avrg_assignments_grades[$key]['Average grade'];
	 $performance_index = round(0.3*$fatg + 0.2*$latg + 0.1*$avr_qg + 0.4*$avr_ag, 2);
	 $combined_array[] = array(		"ID" => $key, 
	    					"Student" => $value['Student'],
						"First Attempt Quizzes Grades (FATG)" => $fatg,
						"Last Attempt Quizzes Grades (LATG)" => $latg,
						"Average Quizzes Grades (AVR_QG)" => $avr_qg,
						"Assignments Average Grades (AVR_AG)" => $avr_ag,
						"Performance Index" => $performance_index,
					     	"Visual Index" => "");
	 $helparrayforsorting[] = $performance_index;
      }

      array_multisort($helparrayforsorting, SORT_ASC, $combined_array);
      return $combined_array;
   }

   function getIdKeyData($functiontocall, $param_arr, $sortedbyid = false)
   {
      $counter = 0;
      $data = call_user_func_array(array($this, $functiontocall), $param_arr);
      $idforkey_data = array();
      foreach($data as $student)
	 $idforkey_data[$student['ID']] = $student;
      if($sortedbyid == true) 
	 ksort($idforkey_data);

      //var_dump($idforkey_data);
      return $idforkey_data;
   }

   function getData($sqlquery, $arrayparams=null, $firstcolumntitle='Student', $secondcolumntitle, $secondcolumndefaultvalue = 0)
   {
      $data = array();
      //$enrolled_users = get_enrolled_users($this->context, '', 0, "u.id, u.firstname, u.lastname");
      $enrolled_users = get_role_users(5, $this->context);
      $enrolled_users_count = count($enrolled_users);

      for($i = 0; $i < $enrolled_users_count; $i++)
	 $data[$i] = array("","","");
      $i = $enrolled_users_count - 1;

      $rs = $this->dbconn->get_recordset_sql($sqlquery, $arrayparams);

      $keep_usrval_forcomparison = -1;
      foreach($rs as $record)
      {
	 $str_student = ($this->isteachermode == true)?$record->firstname . " " . $record->lastname:$i;
	 $data[$i--] = array("ID" => $record->id, "Student" => $str_student, $secondcolumntitle => round($record->value, 2), "Yours" => ($keep_usrval_forcomparison = round($record->usrval_forcomparison, 2)));

	 unset($enrolled_users[$record->id]);
      }
      $rs->close();

      foreach($enrolled_users as $record)
      {
	 $str_student = ($this->isteachermode == true)?$record->firstname . " " . $record->lastname:$i;
	 $data[$i--] = array("ID" => $record->id, "Student" => $str_student, $secondcolumntitle => round($secondcolumndefaultvalue,2), "Yours" => $keep_usrval_forcomparison);
      }

      return $data;
   }

/*   function getFAQuizzesAssignmentGradesCorrelation($courseid) //FA for First Attempt
   {
      $quizdata = getCourseQuizzesFirstAttemptsAverageGrades

   }*/

/*   function getQuizFAClassAverageGrade($quizid) //FA for first attempt
   {
      $sqlquery = "SELECT AVGqat.sumgrades/qz.grade as GRD, qat.attempt, qat.quiz, qz.name 
	 		FROM mdl_quiz_attempts qat, mdl_quiz qz WHERE qat.quiz=qz.id AND qz.course=? AND userid=" . $this->userid . " ORDER BY qat.quiz, qat.attempt";
    }*/
/*
   function getUserQuizzesGrades($courseid)
   {
      $counter = 0;
      $data = array();
      $sqlquery = "SELECT qat.userid, 100*qat.sumgrades/qz.grade as GRD, qat.attempt, qat.quiz, qz.name 
	 		FROM mdl_quiz_attempts qat, mdl_quiz qz WHERE qat.quiz=qz.id AND qz.course=? AND userid=" . $this->userid . " ORDER BY qat.quiz, qat.attempt";
      $rs = $this->dbconn->get_recordset_sql($sqlquery, array($courseid));
      foreach($rs as $record)
	 $data[$counter++] = array("Quiz" => $record->name, "Attempt" => $record->attempt, "Grade" => $record->grd);
      $rs->close();
      return $data;
   }


   function getUserPerformance()
   {
      
   }
 */
   function getAssignmentAverageGrades($courseid)
   {
      $userspecific_query = ", (SELECT AVG(100*g.grade/asgn.grade) FROM mdl_assign_grades g, mdl_assign asgn WHERE g.assignment=asgn.id AND asgn.course=" . $courseid . " 
	 			AND userid=" . $this->userid . ") AS usrval_forcomparison ";
      
      $sqlquery = "SELECT users.id, users.firstname, users.lastname, AVG(100*grades.grade/mdl_assign.grade) AS value " . $userspecific_query . "
			FROM mdl_assign_grades grades, mdl_user users, mdl_assign 
			WHERE mdl_assign.course=? AND grades.userid=users.id AND mdl_assign.id=grades.assignment
			GROUP BY users.id
			ORDER BY value DESC";
      
      return $this->getData($sqlquery, array($courseid), "Student", "Average grade");
   }

   function getAssignmentGrades($asgnid)
   {
      $userspecific_query = ", (SELECT 100*gr.grade/asgn.grade FROM mdl_assign_grades gr, mdl_assign asgn 
	 			WHERE gr.assignment=asgn.id AND gr.userid=" . $this->userid . " AND gr.assignment = " . $asgnid . ") AS usrval_forcomparison ";

      $sqlquery = "SELECT users.id, users.firstname, users.lastname, 100*grades.grade/asgns.grade AS value " . $userspecific_query . "
	 		FROM mdl_assign_grades grades, mdl_user users, mdl_assign asgns
			WHERE asgns.id=grades.assignment AND grades.assignment=? AND grades.userid=users.id
			ORDER BY grades.grade DESC";
            
      return $this->getData($sqlquery, array($asgnid), "Student", "Grade");
   }

   function getQuizAttempts($quizid)
   {
      $userspecific_query = ",(SELECT MAX(attempt) FROM mdl_quiz_attempts WHERE userid=" . $this->userid . " AND quiz = " . $quizid . ") AS usrval_forcomparison ";

      $sqlquery = "SELECT COUNT(qat.attempt) AS value, u.id, u.firstname, u.lastname, q.name " . $userspecific_query . "
	 		FROM mdl_quiz_attempts qat, mdl_user u, mdl_quiz q 
			WHERE qat.quiz=? AND qat.quiz=q.id AND qat.userid=u.id 
			GROUP BY userid ORDER BY value DESC";
            
      return $this->getData($sqlquery, array($quizid), "Student", "Attempts");
   }

   function getQuizFirstAttemptGrades($quizid)
   {
      $grade_sqlquery = "SELECT grade FROM mdl_quiz WHERE id=" . $quizid;
      $userspecific_query = ", (SELECT 100*sumgrades/(" . $grade_sqlquery . ") FROM mdl_quiz_attempts WHERE userid=" . $this->userid . " AND quiz = " . $quizid . " AND attempt=1) AS usrval_forcomparison ";

      $sqlquery = "SELECT u.firstname, u.lastname, u.id, 100*qat.sumgrades/(" . $grade_sqlquery . ") AS value " . $userspecific_query . " FROM mdl_quiz_attempts qat, mdl_user u 
	 		WHERE qat.quiz = ? AND qat.state = 'finished' AND qat.attempt=1 AND qat.userid = u.id
	 		ORDER BY value DESC";
      
      return $this->getData($sqlquery, array($quizid), "Student", "Grade", "");
   }
   
   function getQuizAverageGrades($quizid)
   {
      $grade_sqlquery = "SELECT grade FROM mdl_quiz WHERE id=" . $quizid;
      $userspecific_query = ", (SELECT 100*AVG(sumgrades)/(" . $grade_sqlquery . ") FROM mdl_quiz_attempts WHERE userid=" . $this->userid . " AND quiz = " . $quizid . ") AS usrval_forcomparison ";

      $sqlquery = "SELECT u.username, 100*AVG(qat.sumgrades)/(" . $grade_sqlquery . ") AS value, u.firstname, u.lastname, u.id " . $userspecific_query . "
			FROM mdl_quiz_attempts qat, mdl_user u 
			WHERE qat.quiz = ? AND u.id=qat.userid GROUP BY qat.userid ORDER BY value DESC";
            
      return $this->getData($sqlquery, array($quizid), "Student", "Grade", "");
   }

   function getQuizLastAttemptGrades($quizid)
   {
      $grade_sqlquery = "SELECT grade FROM mdl_quiz WHERE id=" . $quizid;      
      $userspecific_query = ", (SELECT 100*qt.sumgrades/(" . $grade_sqlquery . ") FROM mdl_quiz_attempts qt, 
	 			(SELECT MAX(attempt) AS MAXAT, sumgrades FROM mdl_quiz_attempts WHERE userid=" . $this->userid . " AND quiz=" . $quizid . ") AS usds
				WHERE qt.attempt = usds.MAXAT AND qt.userid=" . $this->userid . " AND qt.quiz=" . $quizid . ") AS usrval_forcomparison ";

      $sqlquery = "SELECT 100*qat2.sumgrades/(" . $grade_sqlquery . ") AS value, u.id, u.firstname, u.lastname, qat2.attempt " . $userspecific_query . "
		FROM (SELECT MAX(qat.attempt) AS MAXATT, qat.userid 
		FROM mdl_quiz_attempts qat 
		WHERE qat.quiz = ? AND qat.state = 'finished' GROUP BY qat.userid) X, mdl_quiz_attempts qat2, mdl_user u 
		WHERE x.userid=qat2.userid AND x.MAXATT=qat2.attempt AND qat2.quiz = ? and u.id=qat2.userid ORDER BY value DESC";

      return $this->getData($sqlquery, array($quizid, $quizid), "Student", "Grade", "");
   }

   /* Metric 2: Average number of attempts per student in a course’s quizzes.*/
   
   function getCourseQuizzesAverageAttempts($courseid)
   {
      $userspecific_query = ", (SELECT AVG(AT.MAXAT) FROM (SELECT MAX(attempt) AS MAXAT FROM mdl_quiz_attempts a, mdl_quiz b 
	 			WHERE b.course=" . $courseid . " AND a.quiz=b.id AND userid=" . $this->userid . " GROUP BY quiz) AS AT) AS usrval_forcomparison ";

      $quizzes = $this->getQuizzes($courseid);
      $sqlquery = "SELECT AVG(attempt) AS value, firstname, lastname, id " . $userspecific_query . " FROM (";
      $flag = 0;

      foreach($quizzes as $quizid=>$quizname)
      {
	 if($flag)
	    $sqlquery .= " UNION ";
	 $flag = 1;

	 $sqlquery .= "SELECT u.id, u.firstname, u.lastname, u.username, qat2.attempt 
	    FROM (SELECT MAX(qat.attempt) AS MAXATT, qat.userid 
	    FROM mdl_quiz_attempts qat 
	    WHERE qat.quiz = " . $quizid . " AND qat.state = 'finished' GROUP BY qat.userid) X, mdl_quiz_attempts qat2, mdl_user u 
	    WHERE x.userid=qat2.userid AND x.MAXATT=qat2.attempt AND qat2.quiz = " . $quizid . " AND u.id=qat2.userid";

      }
      $sqlquery .= ") AS MERGEDTBL GROUP BY id ORDER BY value DESC";
      return $this->getData($sqlquery, null, "Student", "Average quizzes' attempts", 0);      
   }

   /* Metric 6: Distribution of the average grades of the first attempt grades in all quizzes of a course. */
   function getCourseQuizzesFirstAttemptsAverageGrades($courseid)
   {
      $userspecific_query = ", (SELECT AVG(100*qat.sumgrades/q.grade) FROM mdl_quiz_attempts qat, mdl_quiz q 
	 			WHERE qat.userid=" . $this->userid . " AND qat.attempt=1 AND qat.quiz=q.id AND q.course=" . $courseid . ") AS usrval_forcomparison ";

      $quizzes = $this->getQuizzes($courseid);
      $sqlquery = "SELECT U.id, U.firstname, u.lastname, AVG(X.percentgrades) AS value " . $userspecific_query . " FROM (";
      $flag = 0;

      foreach($quizzes as $quizid=>$quizname)
      {
	 if($flag)
	    $sqlquery .= " UNION ";
	 $flag = 1;

	 $sqlquery .= "SELECT qat.attempt, qat.userid, 100*qat.sumgrades/qz.grade AS percentgrades FROM mdl_quiz_attempts qat, mdl_quiz qz 
	    		WHERE qat.quiz = " . $quizid . " AND qat.quiz=qz.id AND qat.attempt = 1";
      }
      $sqlquery .= ") AS X, mdl_user U WHERE X.userid = U.id GROUP BY id ORDER BY value DESC";

      return $this->getData($sqlquery, null, "Student", "Average grade in quizzes' first attempts", 0);
   }



   function getCourseQuizzesLastAttemptsAverageGrades($courseid)
   {
      $userspecific_query = ", (SELECT AVG(100*qt.sumgrades/qz.grade) FROM mdl_quiz_attempts qt, mdl_quiz qz,
	 			(SELECT MAX(attempt) AS MAXAT, sumgrades FROM mdl_quiz_attempts WHERE userid=" . $this->userid . ") AS usds
				WHERE qt.attempt = usds.MAXAT AND qt.userid=" . $this->userid . " AND qz.id=qt.quiz) AS usrval_forcomparison";

      $sqlquery = "SELECT AVG(100*qat2.sumgrades/qz.grade) AS value, u.id, u.firstname, u.lastname, qat2.attempt" . $userspecific_query . "
		FROM (SELECT MAX(qat.attempt) AS MAXATT, qat.userid 
		FROM mdl_quiz_attempts qat 
		WHERE qat.state = 'finished' GROUP BY qat.userid) X, mdl_quiz_attempts qat2, mdl_user u, mdl_quiz qz 
		WHERE x.userid=qat2.userid AND x.MAXATT=qat2.attempt AND qz.course=? AND qz.id=qat2.quiz AND u.id=qat2.userid GROUP BY u.id ORDER BY value DESC";

      return $this->getData($sqlquery, array($courseid), "Student", "Average grades in quizzes' last attempts", "");
   }   

   
   function getCourseQuizzesAverageOfAverageGrades($courseid)
   {
      $userspecific_query = ", (SELECT AVG(100*qzt.sumgrades/uqz.grade) FROM mdl_quiz_attempts qzt, mdl_quiz uqz 
	 			WHERE qzt.quiz=uqz.id AND qzt.userid=" . $this->userid . ") AS usrval_forcomparison ";

      $sqlquery = "SELECT u.username, AVG(100*qat.sumgrades/qz.grade) AS value, u.firstname, u.lastname, u.id " . $userspecific_query . "
			FROM mdl_quiz_attempts qat, mdl_user u, mdl_quiz qz
			WHERE u.id=qat.userid AND qat.quiz=qz.id AND qz.course=? GROUP BY qat.userid ORDER BY value DESC";
            
      return $this->getData($sqlquery, array($courseid), "Student", "Average grades", "");
   }
}
?>
