<?php

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
       getStudentsProfile();
        break;
    case 'POST':
        uploadImage();
        break;    
    
    default:
        echo '{"Result":"Unknown Request."}';
        break;
}
function getStudentsProfile(){
    include "../db.php";
    $getAllDataQuery = "SELECT * FROM students_profile";
    try {
        $getAllData = mysqli_query($conn,$getAllDataQuery);
        if (mysqli_num_rows($getAllData)>0) {
            $rows = array();
            while ($rowsData = mysqli_fetch_assoc($getAllData)) {
                $rows[] = $rowsData;
            }

            echo json_encode($rows);
        }else {
             echo '{"Result":"No data available."}';
        }

    } catch (\Throwable $th) {
        echo "Error ".$th;
    }
}

function uploadImage(){
    $uploadDirectory = "./../uploads/";

$file_title = $_POST['title'];
$file_desc = $_POST['description'];


if (is_writable($uploadDirectory)) {
  //  echo "Directory is writable.";

    if (!isset($_FILES['my_file']['error'])) {
        echo '{"Result":"Invalid parameter given."}';
        die();
    }

    $errorMsg = "";
    switch ($_FILES['my_file']['error']) {
        case UPLOAD_ERR_OK:
           // $errorMsg = "Success.You may go now!";
          //  echo '{"Result":"Success.You may go now!"}';
            break;
        case UPLOAD_ERR_NO_FILE:
            $errorMsg = "No file selected!";
           // echo '{"Result":"No file selected!"}';
            break;
        case UPLOAD_ERR_INI_SIZE:
            $errorMsg = "File size exceeds.";
            //echo '{"Result":"File size exceeds."}';
            break;
        case UPLOAD_ERR_FORM_SIZE:
             $errorMsg = "File size exceeds.";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $errorMsg = "Missing a temporary file.";
            break;
        case UPLOAD_ERR_EXTENSION:
            $errorMsg = "Extension is not allowed.";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $errorMsg = "File can not be written.";
            break;        
        default:
            $errorMsg = "Unknown error occurred.";
            break;
    }

    if (!empty($errorMsg)) {
        echo '{"Result":"'.$errorMsg.'"}';
        die();
    }






    $originalFileName = $_FILES['my_file']['name'];
    $extension = pathinfo($originalFileName,PATHINFO_EXTENSION);

    $temporaryFilePath = $_FILES['my_file']['tmp_name'];
    $hashedFile = sha1_file($temporaryFilePath);
//uploads/3439sslr3343.jpg
$finalDestinationPath = $uploadDirectory . $hashedFile . "." . $extension;
$finalDestinationPathWithoutDot = "/uploads/" . $hashedFile . "." . $extension;


//validaty check jpg/png
$fileInfo = new finfo(FILEINFO_MIME_TYPE);

$isValidExtension = array_search($fileInfo->file($temporaryFilePath),array('jpg'=>'image/jpeg','png'=>'image/png'),true);
//image/png
if (!$isValidExtension) {
    echo '{"Result":"Not valid format."}';
    die();
}else{
   //  echo '{"Result":"Valid format."}';
    
}


$fileSize = $_FILES['my_file']['size'];

if($fileSize > 2000000){
    echo '{"Result":"File size can not be exceeded 2MB."}';
    die();
}


if (move_uploaded_file($temporaryFilePath,$finalDestinationPath)) {

    include "../db.php";

    $profileInsertQuery = "INSERT INTO students_profile(project_title, project_desc, photoUrl) VALUES ('$file_title','$file_desc','$finalDestinationPathWithoutDot')";

    try {
        if (mysqli_query($conn,$profileInsertQuery)) {
             echo '{"Result":"Data saved to database."}'; 
        }else{
             echo '{"Result":"Failed to save data."}';
        }
    } catch (\Throwable $th) {
       echo "Error ".$th;
    }

    echo '{"Result":"File uploaded successfully!","Title":"'.$file_title.'","Description":"'.$file_desc.'","photoUrl":"'.$finalDestinationPathWithoutDot.'"}';
}else {
    echo '{"Result":"Failed to upload file."}';
}




}else {
    echo "Directory is not writable.";
    die();
}
}






