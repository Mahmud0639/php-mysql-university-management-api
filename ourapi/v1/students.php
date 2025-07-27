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
        insertStudent();
        //echo '{"Message":"POST request"}';
         break;
    case 'PUT':
        //echo '{"Message":"PUT request"}';
        updateStudentInfo();
        break;
    case 'DELETE':
        //echo '{"Message":"DELETE request"}';
        deleteStudent();
        break;
                            
    default:
    echo '{"Message":"UNKNOWN request"}';
        break;
}

function getUserData(){

    //without validation
    // $currentPage = 1;
    // if (isset($_GET['currentPage'])) {
    //     $currentPage = $_GET['currentPage'];
    // }

    // $limit = 3;
    // if (isset($_GET['limit'])) {
    //     $limit = $_GET['limit'];
    // }

    $currentPage = (isset($_GET['currentPage']) && is_numeric($_GET['currentPage']) && $_GET['currentPage']>0) ? (int)$_GET['currentPage']:1;
    $limit = (isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit']>0) ? (int)$_GET['limit']:50;
 

    
    $offset = $limit * ($currentPage - 1);//3 * (2-1)


    $sql = "SELECT\n"

    . "st.sId AS students_id,\n"

    . "\n"

    . "st.name,\n"

    . "\n"

    . "st.email,\n"

    . "\n"

    . "st.phone,\n"

    . "\n"

    . "st.credit,\n"

    . "\n"

    . "\n"

    . "students_profile.id AS profile_id,\n"

    . "\n"

    . "students_profile.project_title,\n"

    . "\n"

    . "students_profile.project_desc,\n"

    . "\n"

    . "students_profile.photoUrl AS imageUrl,\n"

    . "\n"

    . "departments.d_id AS departments_id,\n"

    . "\n"

    . "departments.departmentName AS dept_name,\n"

    . "\n"

    . "students_subjects.id AS students_sub_id,\n"

    . "\n"

    . "students_subjects.subject_id AS students_sub_sub_id,\n"

    . "\n"

    . "subjects.sub_id AS subjects_id,\n"

    . "\n"

    . "subjects.subject_name\n"

    . "\n"

    . "FROM\n"

    . "\n"

    . "(SELECT DISTINCT * FROM students ORDER BY students.sId ASC LIMIT $limit OFFSET $offset) AS st LEFT JOIN departments ON st.sId = departments.d_student_id\n"

    . "LEFT JOIN students_profile ON st.sId = students_profile.id\n"

    . "LEFT JOIN students_subjects ON st.sId = students_subjects.students_id\n"

    . "LEFT JOIN subjects ON students_subjects.subject_id = subjects.sub_id;";





// $sql = "SELECT\n"

//     . "students.sId AS students_id,\n"

//     . "students.name,\n"

//     . "students.email,\n"

//     . "students.phone,\n"

//     . "students.credit,\n"

//     . "\n"

//     . "departments.d_id AS departments_id,\n"

//     . "departments.departmentName AS dept_name,\n"

//     . "\n"

//     . "students_subjects.id AS students_sub_id,\n"

//     . "students_subjects.subject_id AS students_sub_sub_id,\n"

//     . "\n"

//     . "subjects.sub_id AS subjects_id,\n"

//     . "subjects.subject_name\n"

//     . "\n"

//     . "FROM\n"

//     . "students LEFT JOIN departments ON students.sId = departments.d_student_id\n"

//     . "LEFT JOIN students_subjects ON students.sId = students_subjects.students_id\n"

//     . "LEFT JOIN subjects ON students_subjects.subject_id = subjects.sub_id\n"

//     . "ORDER BY students.sId ASC;";


    include("../db.php");
    $response = mysqli_query($conn,$sql);

    if (mysqli_num_rows($response)>0) {
        $data_rows = array();
        while ($res = mysqli_fetch_assoc($response)) {
           //$data_rows[] = $res;


           $studentId = $res['students_id'];
           if(in_array($studentId,array_column($data_rows,"student_id"))){

                $subId = $res['subjects_id'];
                $subInfo = array();
                if (!empty($subId)) {
                        $subInfo = array(
                            "subject_id" => (int)$subId,
                            "subject_name" => $res['subject_name']
                        );
                }

                array_push($data_rows[count($data_rows)-1]["subjects"],$subInfo);
           }else {
                 $deptId = $res['departments_id'];
           $deptInfo = array();
           if (!empty($deptId)) {
                $deptInfo = array(
                     "dept_id" => (int)$deptId,
                     "dept_name" =>$res['dept_name']
                );
           }

           $subId = $res['subjects_id'];
           $subInfo = array();
           if (!empty($subId)) {
                $subInfo = array(
                    "subject_id" => (int)$subId,
                    "subject_name" => $res['subject_name']
                );
           }

           $data_rows[] = array(
                "student_id" => (int)$res['students_id'],
                "name" => $res['name'],
                "email" => $res['email'],
                "phone" => $res['phone'],
                "profile_id" => $res['profile_id'],
                "project_title" => $res['project_title'],
                "project_desc" => $res['project_desc'],
                "imageUrl" => $res['imageUrl'],
                "total_credits"=> (int)$res['credit'],
                "departments" => $deptId != NULL ? $deptInfo : (object)array(),
                "subjects" => $subId != NULL ? array($subInfo) : array()
           );
           }

          

    //        {
    //     "students_id": "2",
    //     "name": "Rahim Uddin",
    //     "email": "rahim.cs@gmail.com",
    //     "phone": "01711-111111",
    //     "credit": "20",
    //     "departments_id": "2",
    //     "dept_name": "CSE",
    //     "students_sub_id": "1",
    //     "students_sub_sub_id": "101",
    //     "subjects_id": "101",
    //     "subject_name": "Data Structure"
    // },
    
    
        }
        echo json_encode($data_rows);

    }else {
        echo '{"Message":"Data not found."}';
    }



}

function insertStudent(){

    $inputData = json_decode(file_get_contents("php://input"),true);
   
    $name = $inputData['name'];
    $email = $inputData['email'];
    $phone = $inputData['phone'];
    $total_credit = $inputData['total_credits'];
    $department_name = $inputData['dept_name'];
    $subjects = $inputData['subjects'];//array data
    
    include("../db.php");
    $insertData = "INSERT INTO students(name, email, phone, credit) VALUES ('$name','$email','$phone','$total_credit')";

    try {
       $studentInserted =  mysqli_query($conn,$insertData);
       if ($studentInserted) {
            $studentId = mysqli_insert_id($conn);
       }
       if (!empty($department_name)) {
            $deptInsert = "INSERT INTO departments(departmentName, d_student_id) VALUES ('$department_name','$studentId')";

            mysqli_query($conn,$deptInsert);
       }

       if (!empty($subjects)) {
            $subjectQuery = "INSERT INTO students_subjects(students_id, subject_id) VALUES";
            foreach ($subjects as $key => $subject) {
                $subjectQuery .= "('$studentId','$subject')";
                if ($key < count($subjects)-1) {//0 < 2
                    $subjectQuery .= ",";
                }
            }

           $res =  mysqli_query($conn, $subjectQuery);
           if ($res) {
                 echo '{"Result":"Success!"}';
           }else {
                 echo '{"Result":"Failed."}';
           }
       }else {
        if ($studentInserted) {
            echo '{"Result":"Student data inserted successfully!"}';
       }else {
        echo '{"Result":"Failed to insert data."}';
       }
       }

       



       
    } catch (\Throwable $th) {
        echo "Error".$th;
    }
}

function updateStudentInfo(){
    $data = json_decode(file_get_contents("php://input"),true);
    $studentId = $data['student_id'];
    $name = $data['name'];
    $phone = $data['phone'];
    $email = $data['email'];
    $total_credit = $data['total_credits'];
    $dept_name = $data['dept_name'];
    $subjects = $data['subjects'];

    include("../db.php");

    $checkStudentQuery = "SELECT * FROM students WHERE sId = '$studentId'";
    $checkStudentResult = mysqli_query($conn,$checkStudentQuery);
    if (mysqli_num_rows($checkStudentResult)==0) {
        echo '{"Result":"Error student id is not available."}';
        return;
    }

    $updateStudent = "UPDATE students SET name= CASE WHEN '$name' != '' THEN '$name' ELSE name END,email= CASE WHEN '$email' != '' THEN '$email' ELSE email END,phone = CASE WHEN '$phone' != '' THEN '$phone' ELSE phone END,credit= CASE WHEN '$total_credit' != '' THEN '$total_credit' ELSE credit END WHERE sId = '$studentId'";
    try {

         $resultMessage = "";

        $isStudentUpdated = mysqli_query($conn,$updateStudent);

        if (!empty($dept_name)) {
            $stIdFromDepartments = "SELECT d_student_id FROM departments WHERE d_student_id = '$studentId'";
            $stIdQuery = mysqli_query($conn,$stIdFromDepartments);
            $sIdRow = $stIdQuery->fetch_assoc();
            $student_id = $sIdRow['d_student_id'];

            if (!empty($student_id)) {
                $departmentUpdateQuery = "UPDATE departments SET departmentName = '$dept_name' WHERE d_student_id = '$studentId'";
                mysqli_query($conn,$departmentUpdateQuery);
            }else{
                $insertStudentQuery = "INSERT INTO departments(departmentName, d_student_id) VALUES ('$dept_name','$studentId')";
                mysqli_query($conn,$insertStudentQuery);
            }
        }

        try {
            $deleteSubjectQuery = "DELETE FROM students_subjects WHERE students_id = '$studentId'";
            mysqli_query($conn,$deleteSubjectQuery);
        } catch (\Throwable $th) {
            echo "Error ".$th;
        }

        $subjectsQuery = "INSERT INTO students_subjects(students_id, subject_id) VALUES";
        foreach ($subjects as $key => $subject) {
            $subjectsQuery .= "('$studentId','$subject')";

            if($key < count($subjects)-1){
                $subjectsQuery .= ",";
            }
        }

        if (!mysqli_query($conn,$subjectsQuery)) {
           // echo '{"Result":"Success."}';
           $resultMessage = "Failed";
        }
        // }else{
        //     echo '{"Result":"Failed."}';
        // }





        if ($isStudentUpdated) {
            //echo '{"Result":"Students updated."}';
            $resultMessage = "Students updated.";
        }else {
           echo '{"Result":"Failed to update data."}'; 
        }


         echo json_encode(["Result" => $resultMessage]);

    } catch (\Throwable $th) {
        echo "Error ".$th;
    }


}

function deleteStudent(){
    $data = json_decode(file_get_contents("php://input"),true);

    $stdId = $data['student_id'];
    $stdQuery = "DELETE FROM students WHERE sId = '$stdId'";

    include("../db.php");

    try {
        $isDeleted = mysqli_query($conn,$stdQuery);
        if ($isDeleted) {
            echo '{"Result":"Data deleted successfully!"}';
        }else{
            echo '{"Result":"Failed to delete data."}';
        }
    } catch (\Throwable $th) {
        echo "Error ".$th;
    }
}



