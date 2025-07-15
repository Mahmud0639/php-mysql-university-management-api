<?php
header("Content-Type: application/json");

$req_method = $_SERVER['REQUEST_METHOD'];

switch ($req_method) {
    case 'GET':
        //echo '{"Result":"GET Method"}';
        getSubjectsData();
        break;
    case 'POST':
       // echo '{"Result":"POST Method"}';
       insertSubjectsData();
        break;
    case 'PUT':
        updateSubjectsInfo();
        break;
    default:
        echo '{"Result":"Unknown Request."}';
        break;
}

function getSubjectsData(){
    $getSubjectsQuery = "SELECT * FROM subjects";
    include("../db.php");
    try {
        $responseData = mysqli_query($conn,$getSubjectsQuery);
        if (mysqli_num_rows($responseData)>0) {
            $rowsData = array();
            while ($res = mysqli_fetch_assoc($responseData)) {
                $rowsData[] = $res;
            }

            echo json_encode($rowsData);
        }else {
            echo '{"Result":"No data found."}';
        }
    } catch (\Throwable $th) {
       echo "Error ".$th;
    }
}

function insertSubjectsData(){
    $data = json_decode(file_get_contents("php://input"),true);

    include("../db.php");

    $sub_id = $data['sub_id'];
    $subject_name = $data['subject_name'];
    $insertQuery = "INSERT INTO subjects(sub_id, subject_name) VALUES ('$sub_id','$subject_name')";

    try {
        $isInserted = mysqli_query($conn,$insertQuery);
        if ($isInserted) {
           echo '{"Result":"Data inserted successfully!"}';
        }else {
            echo '{"Result":"Failed to insert data."}';
        }
    } catch (\Throwable $th) {
        echo "Error ".$th;
    }
}

function updateSubjectsInfo(){
    $data = json_decode(file_get_contents("php://input"),true);
    include("../db.php");

    $sub_id = $data['sub_id'];
    $subject_name = $data['subject_name'];


    $updateSubjectsQuery = "UPDATE subjects SET sub_id='$sub_id',subject_name='$subject_name' WHERE sub_id = '$sub_id'";

    try {
        $isUpdated = mysqli_query($conn,$updateSubjectsQuery);
        if ($isUpdated) {
            echo '{"Result":"Updated successfully!"}';
        }else{
            echo '{"Result":"Error occurred."}';
        }
    } catch (\Throwable $th) {
        echo "Error ".$th;
    }
}