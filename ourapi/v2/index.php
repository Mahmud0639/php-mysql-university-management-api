<?php

header("Content-Type: application/json");

//echo '{"Message":"Hello world"}';

$req_method = $_SERVER['REQUEST_METHOD'];

switch ($req_method) {
    case 'GET':
       // echo '{"Message":"GET request"}';
        getUserData();
        break;
    case 'POST':
        //echo '{"Message":"POST request"}';
        insertStudents();
         break;
    case 'PUT':
        echo '{"Message":"PUT request"}';
        break;
    case 'DELETE':
        echo '{"Message":"DELETE request"}';
        break;
                            
    default:
    echo '{"Message":"UNKNOWN request"}';
        break;
}

function getUserData(){
$sql = "SELECT\n"

    . "students.sId AS students_id,\n"

    . "students.name,\n"

    . "students.email,\n"

    . "students.phone,\n"

    . "students.credit,\n"

    . "\n"

    . "departments.d_id AS departments_id,\n"

    . "departments.departmentName AS dept_name,\n"

    . "\n"

    . "students_subjects.id AS students_sub_id,\n"

    . "students_subjects.subject_id AS students_sub_sub_id,\n"

    . "\n"

    . "subjects.sub_id AS subjects_id,\n"

    . "subjects.subject_name\n"

    . "\n"

    . "FROM\n"

    . "students LEFT JOIN departments ON students.sId = departments.d_student_id\n"

    . "LEFT JOIN students_subjects ON students.sId = students_subjects.students_id\n"

    . "LEFT JOIN subjects ON students_subjects.subject_id = subjects.sub_id\n"

    . "ORDER BY students.sId ASC;";


    include("db.php");
    $response = mysqli_query($conn,$sql);

    if (mysqli_num_rows($response)>0) {
        $data_rows = array();
        while ($res = mysqli_fetch_assoc($response)) {
         // $data_rows[] = $res;

         $studentId = $res['students_id'];

         if (in_array($studentId,array_column($data_rows,"student_id"))) {
            $subInfo = array();
            $subId = $res['subjects_id'];

            if(!empty($subId)){
                $subInfo = array(
                    "subject_id" => $res['subjects_id'],
                    "subject_name" => $res['subject_name'],
                );
            }

            array_push($data_rows[count($data_rows)-1]['subjects'],$subInfo);

            }else {
                  $deptInfo = array();
         $deptId = $res['departments_id'];
         
         if(!empty($deptId)){
            $deptInfo = array(
                 "dept_id"      => $res['departments_id'],
                 "dept_name"    => $res['dept_name']
            );


         }

         $subInfo = array();
         $subId = $res['subjects_id'];

         if(!empty($subId)){
            $subInfo = array(
                 "subject_id"   => $res['subjects_id'],
                 "subject_name" => $res['subject_name']
            );
         }

                    $data_rows[] = array(
                        "student_id"    => $res['students_id'],
                        "name"          => $res['name'],
                        "phone"         => $res['phone'],
                        "email"         => $res['email'],
                        "total_credit"  => $res['credit'],
                        "department"    => $deptId != NULL ? $deptInfo : (object)array(),
                        "subjects"      => $subId !=NULL ? array($subInfo) : array()
                    );
            }


        }
        echo json_encode($data_rows);

    }else {
        echo '{"Message":"Data not found."}';
    }



}

function insertStudents(){

  

    $insertQuery = INSERT INTO `students`(`sId`, `name`, `email`, `phone`, `credit`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]');


}
