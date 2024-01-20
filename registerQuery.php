<?php
include('connection.php');
$con = connection();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the form
    $firstName = ucfirst(strtolower(htmlspecialchars($_POST['firstName'])));
    $middleName = ucfirst(strtolower(htmlspecialchars($_POST['middleName'])));
    $lastName = ucfirst(strtolower(htmlspecialchars($_POST['lastName'])));
    $studentEmail = htmlspecialchars($_POST['studentEmail']);
    $studentNumber = htmlspecialchars($_POST['studentNumber']);
    $studentCourse = htmlspecialchars($_POST['studentCourse']);
    $studentYearLevel = htmlspecialchars($_POST['studentYearLevel']);
    $studentSection = htmlspecialchars($_POST['studentSection']);
    $studentGender = htmlspecialchars($_POST['studentGender']);
    $studentBirthMonth = htmlspecialchars($_POST['studentBirthMonth']);
    $studentBirthDay = htmlspecialchars($_POST['studentBirthDay']);
    $studentBirthYear = htmlspecialchars($_POST['studentBirthYear']);
    $studentComponent = htmlspecialchars($_POST['studentComponent']);
    $studentContactNumber = htmlspecialchars($_POST['studentContactNumber']);
    $studentStreet = ucwords(strtolower(htmlspecialchars($_POST['studentStreet'])));
    $studentCity = ucwords(strtolower(htmlspecialchars($_POST['studentCity'])));
    $studentProvince = ucwords(strtolower(htmlspecialchars($_POST['studentProvince'])));
    $studentPassword = htmlspecialchars($_POST['studentPassword']);
    $studentConfirmPassword = htmlspecialchars($_POST['studentConfirmPassword']);

    // This is Query to get the Current School Year
    $currentSY = "SELECT * FROM schoolyeartable ORDER BY schoolyear_id DESC LIMIT 1";
    $currentSYResult = $con->query($currentSY);

    if($currentSYResult->num_rows > 0){
        $currentSYData = $currentSYResult->fetch_assoc();
        $schoolYearId = $currentSYData['schoolyear_id'];
        $semesterId = $currentSYData['semester_id'];

        // Concatination of String
        $middleInitial = strtoupper(substr($middleName, 0, 1));
        $studentFullName = $firstName . " " . $middleInitial . " " . $lastName;
        $fullAddress = $studentStreet . " " . $studentCity . " " . $studentProvince;
        $studentFullBirthDay = $studentBirthMonth . "/" . $studentBirthDay . "/" . $studentBirthYear;

        // This Query Check if the Student is Enrolled
        $isEnrolled = "SELECT COUNT(*) AS enrolledCount FROM enrolledstudent WHERE student_number = '$studentNumber' AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId";
        $isEnrolledResult = $con->query($isEnrolled);

        if($isEnrolledResult->num_rows > 0){
            $isEnrolledData = $isEnrolledResult->fetch_assoc();
            if($isEnrolledData['enrolledCount'] != 0){
                
                $isRegistered = "SELECT COUNT(*) AS registerCount FROM useraccount WHERE student_number = '$studentNumber' AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId";
                $isRegisteredResult = $con->query($isRegistered);
                if($isRegisteredResult->num_rows > 0){
                    $isRegisteredData = $isRegisteredResult->fetch_assoc();
                    if($isRegisteredData['registerCount'] == 0){
                        
                        // This Query Check if the Email is Already use
                        $emailIsUse = "SELECT COUNT(*) AS emailCount FROM useraccount WHERE email_address = '$studentEmail' AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId";
                        $emailIsUseResult = $con->query($emailIsUse);
                        if($emailIsUseResult->num_rows > 0){
                            $emailIsUseData = $emailIsUseResult->fetch_assoc();
                            if($emailIsUseData['emailCount'] == 0){
                                
                                // Get the equivalent of $studentComponent
                                $equivalentName = "SELECT * FROM componenttable WHERE component_id = $studentComponent";
                                $equivalentNameResult = $con->query($equivalentName);
                                $equivalentNameData = $equivalentNameResult->fetch_assoc();
                                $studentComponentName = $equivalentNameData['component_name'];

                                // Encryption of Student Number and Student Password using Based64
                                $encrypPassword = base64_encode($studentPassword);
                                $encrypStudentNumber = base64_encode($studentNumber);


                                // Check if Already have folder for storing Qrcodes
                                $qrcodes_dir = 'qrcodes/';
                                    if (!is_dir($qrcodes_dir)) {
                                    mkdir($qrcodes_dir);
                                }

                                // For Creating and Storing of QRcode of Student based on their Student Number
                                require_once('phpqrcode/qrlib.php');
                                $qrdata = $encrypStudentNumber;
                                $qrfull_name = str_replace(['/', '\\', ' '], '', $studentFullName);
                                $qrCodeFileName = "qrcodes/$qrfull_name.png";
                                QRcode::png($qrdata, $qrCodeFileName);

                                $updateStatus = "UPDATE enrolledstudent SET registration_status = 'Registered' WHERE student_number = $studentNumber";
                                $updateStatusRsult = $con->query($updateStatus);
                                
                                // Automatic Grouping of Student
                                if($studentComponentName == 'CWTS'){
                                    
                                    // This Query get all record of each group of CWTS
                                    $cwtsGroup = "SELECT * FROM grouptable WHERE component_id = 2 AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId";
                                    $cwtsGroupResult = $con->query($cwtsGroup);
                                    $cwtsCounter = 0;
                                
                                    if($cwtsGroupResult->num_rows > 0){
                                        
                                        
                                        while($cwtsGroupData = mysqli_fetch_assoc($cwtsGroupResult)){
                                            
                                            $cwtsGroupId = $cwtsGroupData['group_id'];
                                            $cwtsGroupName = $cwtsGroupData['group_name'];
                                            $cwtsGroupNumberOfStudent = $cwtsGroupData['number_student'];

                                            // This Query Check if the Group Are already have student
                                            $checkStudentGroup = "SELECT *, COUNT(*) AS NumOfStudentGroup FROM useraccount WHERE group_id = '$cwtsGroupId' AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId AND role_account_id = 2";
                                            $checkStudentGroupResult = $con->query($checkStudentGroup);
                                            
                                            if($checkStudentGroupResult->num_rows > 0){
                                                $checkStudentGroupData  = $checkStudentGroupResult->fetch_assoc();
                                                $studentGroupCourse = $checkStudentGroupData['course'];
                                                $NumOfStudentGroup = $checkStudentGroupData['NumOfStudentGroup'];


                                                // This Condition Check if the group have student
                                                if($NumOfStudentGroup > 0 ){
                                                    
                                                    if($studentCourse == $studentGroupCourse){

                                                        $numberOfStudentInAGroup = "SELECT COUNT(*) AS numberOfStudentInAGroup FROM useraccount WHERE group_id = '$cwtsGroupId' AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId AND role_account_id = 2";
                                                        $numberOfStudentInAGroupResult = $con->query($numberOfStudentInAGroup);
                                                        $numberOfStudentInAGroupData = mysqli_fetch_assoc($numberOfStudentInAGroupResult);
                                                        $totalCount = $numberOfStudentInAGroupData['numberOfStudentInAGroup'];

                                                        if($NumOfStudentGroup < $cwtsGroupNumberOfStudent){

                                                            $assignStudentToGroup = "INSERT INTO useraccount (password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES ('$encrypPassword', 2, '$lastName', '$firstName', '$middleName', '$studentFullName', '$studentEmail', '$studentContactNumber', '$studentStreet', '$studentCity', '$studentProvince', '$fullAddress', '$studentCourse', '$studentYearLevel', '$studentSection', '$studentGender', '$studentFullBirthDay', '$studentNumber', '$studentComponentName', '$cwtsGroupId', 'Active', '$qrCodeFileName', '$schoolYearId', '$semesterId')";
                                                            $assignStudentToGroupResult = $con->query($assignStudentToGroup);
        
                                                            $response = [
                                                                'status' => 'success',
                                                                'title' => 'Success Registration',
                                                                'message' => 'Registration Complete!',
                                                            ];
                                                            break;

                                                        }else{
                                                            $cwtsCounter++;
                                                            continue;
                                                        }

                                                    }else{
                                                        $cwtsCounter++;
                                                        continue;
                                                    }

                                                }else{
                                                    $assignStudentToGroup = "INSERT INTO useraccount (password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES ('$encrypPassword', 2, '$lastName', '$firstName', '$middleName', '$studentFullName', '$studentEmail', '$studentContactNumber', '$studentStreet', '$studentCity', '$studentProvince', '$fullAddress', '$studentCourse', '$studentYearLevel', '$studentSection', '$studentGender', '$studentFullBirthDay', '$studentNumber', '$studentComponentName', '$cwtsGroupId', 'Active', '$qrCodeFileName', '$schoolYearId', '$semesterId')";
                                                    $assignStudentToGroupResult = $con->query($assignStudentToGroup);

                                                    $response = [
                                                        'status' => 'success',
                                                        'title' => 'Success Registration',
                                                        'message' => 'Registration Complete!',
                                                    ];
                                                    break;
                                                }

                                            }else{
                                                $response = [
                                                    'status' => 'error',
                                                    'title' => 'Something Wrong In Regrouping',
                                                    'message' => 'Please try to register Again!',
                                                ];
                                            }
                                        }
                                        
                                    }else{
                                        $response = [
                                            'status' => 'error',
                                            'title' => 'No Group',
                                            'message' => 'Currently No Group In CWTS',
                                        ];
                                    }
                                }elseif ($studentComponentName == 'ROTC') {
                                    
                                    $rotcGroup = "SELECT * FROM grouptable WHERE component_id = 1 AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId";
                                    $rotcGroupResult = $con->query($rotcGroup);
                                    $rotcCounter = 0;

                                    if($rotcGroupResult->num_rows > 0){
                                        
                                        while($rotcGroupData = mysqli_fetch_assoc($rotcGroupResult)){

                                            $rotcGroupId = $rotcGroupData['group_id'];
                                            $rotcGroupName = $rotcGroupData['group_name'];
                                            $rotcGroupNumberOfStudent = $rotcGroupData['number_student'];

                                            $checkStudentGroups = "SELECT * FROM useraccount WHERE group_id = '$rotcGroupId' AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId AND role_account_id = 2";
                                            $checkStudentGroupsResult = $con->query($checkStudentGroups);

                                            if($checkStudentGroupsResult->num_rows > 0){
                                                
                                                $checkStudentsGroupData  = $checkStudentGroupsResult->fetch_assoc();
                                                $studentGroupGender = $checkStudentsGroupData['gender'];
                                                
                                                if($studentGender == $studentGroupGender){

                                                    $numberOfStudentInAGroup = "SELECT COUNT(*) AS numberOfStudentInAGroup FROM useraccount WHERE group_id = '$rotcGroupId' AND schoolyear_id = '$schoolYearId' AND semester_id = $semesterId AND role_account_id = 2";
                                                    $numberOfStudentInAGroupResult = $con->query($numberOfStudentInAGroup);
                                                    $numberOfStudentInAGroupData = mysqli_fetch_assoc($numberOfStudentInAGroupResult);
                                                    $totalCount = $numberOfStudentInAGroupData['numberOfStudentInAGroup'];

                                                    if($totalCount < $rotcGroupNumberOfStudent){

                                                        $assignStudentToGroup = "INSERT INTO useraccount (password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES ('$encrypPassword', 2, '$lastName', '$firstName', '$middleName', '$studentFullName', '$studentEmail', '$studentContactNumber', '$studentStreet', '$studentCity', '$studentProvince', '$fullAddress', '$studentCourse', '$studentYearLevel', '$studentSection', '$studentGender', '$studentFullBirthDay', '$studentNumber', '$studentComponentName', '$rotcGroupId', 'Active', '$qrCodeFileName', '$schoolYearId', '$semesterId')";
                                                        $assignStudentToGroupResult = $con->query($assignStudentToGroup);
        
                                                        $response = [
                                                            'status' => 'success',
                                                            'title' => 'Success Registration',
                                                            'message' => 'Registration Complete!',
                                                        ];
                                                        break;

                                                    }else{
                                                        $rotcCounter++;
                                                        continue;
                                                    }

                                                }else{
                                                    $rotcCounter++;
                                                    continue;
                                                }

                                            }else{
                                                $assignStudentToGroup = "INSERT INTO useraccount (password, role_account_id, surname, firstname, middlename, full_name, email_address, contactNumber, baranggay, city, province, homeaddress, course, year_level, student_section, gender, birthday, student_number, component_name, group_id, user_status, qrimage, schoolyear_id, semester_id) VALUES ('$encrypPassword', 2, '$lastName', '$firstName', '$middleName', '$studentFullName', '$studentEmail', '$studentContactNumber', '$studentStreet', '$studentCity', '$studentProvince', '$fullAddress', '$studentCourse', '$studentYearLevel', '$studentSection', '$studentGender', '$studentFullBirthDay', '$studentNumber', '$studentComponentName', '$rotcGroupId', 'Active', '$qrCodeFileName', '$schoolYearId', '$semesterId')";
                                                $assignStudentToGroupResult = $con->query($assignStudentToGroup);

                                                $response = [
                                                    'status' => 'success',
                                                    'title' => 'Success Registration',
                                                    'message' => 'Registration Complete!',
                                                ];
                                                break;
                                            }

                                        }

                                    }else{

                                        $response = [
                                            'status' => 'error',
                                            'title' => 'No Group',
                                            'message' => 'Currently No Group In ROTC',
                                        ];
                                    }

                                }else{
                                    $response = [
                                        'status' => 'error',
                                        'title' => 'Invalid Component',
                                        'message' => 'Component are not Available',
                                    ];
                                }

                            }else{
                                $response = [
                                    'status' => 'error',
                                    'title' => 'Already Used',
                                    'message' => 'Email Address is already Used/Registered in system',
                                ];
                            }

                        }else{
                            $response = [
                                'status' => 'error',
                                'title' => 'Already Used',
                                'message' => 'Email Address is already Used/Registered in system',
                            ];
                        }

                    }else{
                        $response = [
                            'status' => 'error',
                            'title' => 'Already Registered',
                            'message' => 'Student Number is already Registered in system',
                        ];
                    }
                    
                }else{
                    $response = [
                        'status' => 'error',
                        'title' => 'Already Registered',
                        'message' => 'Student Number is already Registered in system',
                    ];
                }

            }else{
                $response = [
                    'status' => 'error',
                    'title' => 'Not Enrolled',
                    'message' => 'Student Number is not Enrolled in NSTP Subject',
                ];
            }
            
        }else{
            $response = [
                'status' => 'error',
                'title' => 'Not Enrolled',
                'message' => 'Student Number is not Enrolled in NSTP Subject',
            ];
        }

    }else{
        $response = [
            'status' => 'error',
            'title' => 'No School Year Yet',
            'message' => 'No School Year Yet.',
        ];
    }

    // // Insert data into the database
    // $query = "INSERT INTO your_table_name (first_name, middle_name, last_name, student_email, student_number, student_course, student_year_level, student_section, student_gender, student_birth_month, student_birth_day, student_birth_year, student_component, student_contact_number, student_street, student_city, student_province, student_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // $stmt = $con->prepare($query);
    // $stmt->bind_param('ssssssssssssssssss', $firstName, $middleName, $lastName, $studentEmail, $studentNumber, $studentCourse, $studentYearLevel, $studentSection, $studentGender, $studentBirthMonth, $studentBirthDay, $studentBirthYear, $studentComponent, $studentContactNumber, $studentStreet, $studentCity, $studentProvince, $studentPassword);
    
    // if ($stmt->execute()) {
    //     echo "Data saved successfully!";
    // } else {
    //     echo "Error: " . $con->error;
    // }
    
    // $stmt->close();
    // $con->close();
    header("Content-type: application/json");
    echo json_encode($response);
}
?>
