<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();
// check if user is logged in and has user data in session
if (!isset($_SESSION['user_data'])) {
    header('Location: index.php');
    exit();
}

// get user data from session
$user_data = $_SESSION['user_data'];
$user_id = $user_data['user_account_id'];
$useraccount_query = "SELECT * FROM useraccount WHERE user_account_id = $user_id";
$useraccount_result = $con->query($useraccount_query);
$useraccount_data = $useraccount_result->fetch_assoc();

$role_account_id = $useraccount_data['role_account_id'];
$user_account_id = $useraccount_data['user_account_id'];
$group_id = $useraccount_data['group_id'];
// $qr_code = $useraccount_data['qrimage'];

$role = "SELECT * FROM roleaccount WHERE role_account_id = $role_account_id";
$result = $con->query($role);
$role_data = $result->fetch_assoc();

if ($role_data['role_name'] == 'Admin') {
    header('Location: admin.php');
} elseif ($role_data['role_name'] == 'Teacher') {
    header('Location: teacher.php');
} 
// $qr_code = $user_data['qrimage'];
// Calling the side bar
include_once('./studentsidebar.php');
?>
<style>
    .idContainer{
        display: flex;
        flex-direction: row;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap-reverse;
    }
   .card{
    min-width: 204px;
    max-width: 204px;
    min-height: 324px;
    max-height: 324px;
    background-image: linear-gradient(328deg, rgba(29, 29, 29, 0.05) 0%, rgba(29, 29, 29, 0.05) 25%,rgba(226, 226, 226, 0.05) 25%, rgba(226, 226, 226, 0.05) 50%,rgba(21, 21, 21, 0.05) 50%, rgba(21, 21, 21, 0.05) 75%,rgba(216, 216, 216, 0.05) 75%, rgba(216, 216, 216, 0.05) 100%),linear-gradient(172deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.05) 25%,rgba(108, 108, 108, 0.05) 25%, rgba(108, 108, 108, 0.05) 50%,rgba(21, 21, 21, 0.05) 50%, rgba(21, 21, 21, 0.05) 75%,rgba(236, 236, 236, 0.05) 75%, rgba(236, 236, 236, 0.05) 100%),linear-gradient(207deg, rgba(153, 153, 153, 0.05) 0%, rgba(153, 153, 153, 0.05) 25%,rgba(83, 83, 83, 0.05) 25%, rgba(83, 83, 83, 0.05) 50%,rgba(5, 5, 5, 0.05) 50%, rgba(5, 5, 5, 0.05) 75%,rgba(82, 82, 82, 0.05) 75%, rgba(82, 82, 82, 0.05) 100%),linear-gradient(297deg, rgba(26, 26, 26, 0.05) 0%, rgba(26, 26, 26, 0.05) 25%,rgba(223, 223, 223, 0.05) 25%, rgba(223, 223, 223, 0.05) 50%,rgba(232, 232, 232, 0.05) 50%, rgba(232, 232, 232, 0.05) 75%,rgba(153, 153, 153, 0.05) 75%, rgba(153, 153, 153, 0.05) 100%),linear-gradient(204deg, rgba(120, 120, 120, 0.05) 0%, rgba(120, 120, 120, 0.05) 25%,rgba(191, 191, 191, 0.05) 25%, rgba(191, 191, 191, 0.05) 50%,rgba(246, 246, 246, 0.05) 50%, rgba(246, 246, 246, 0.05) 75%,rgba(123, 123, 123, 0.05) 75%, rgba(123, 123, 123, 0.05) 100%),linear-gradient(90deg, rgb(229,241,249),rgb(88,174,216));
    position: relative;
    display: flex;
   }
   .logo {
    width: 100%;
    height: 60px;
    display: flex;
    justify-content: center;
    padding: .5rem;
   }
   .logo img{
    height: 100%;
    width: auto;
   }
   .back {
        page-break-after: always;
    }
   .picture{
    width: 100%;
    height: 90px;
    display: flex;
    justify-content: center;
    padding: .5rem;
   }
   .picture img{
    height: 100%;
    width: auto;
    border-radius: 50%;
   }
   .personalInformation{
    width: 100%;
    flex: 1;
    padding: .25rem .5rem;
   }
   .studentName{
    display: flex;
    flex-direction: column;
    text-align: center;
    margin-bottom: .5rem;
   }
   .studentName span, .studentNumber span{
    font-size: 13.5px;
    font-weight: 500;
   }
   .studentName label, .studentNumber label{
    font-size: 11px;
   }
   .studentNumber{
    display: flex;
    flex-direction: row;
    margin-top: .25rem;
    gap: .25rem;
   }

   .qrCode{
    width: 100%;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
   }
   .contactInformation{
    width: 100%;
    flex: 1;
    padding: .25rem .5rem;
    display: flex;
    flex-direction: column;
   }
   .contactTitle{
    text-align: center;
    font-size: 14.5px;
    font-weight: 600;
   }
   .contactNumber{
    width: 100%;
    display: flex;
    flex-direction: column;
    text-align: center;
    margin-top: .25rem;
   }
   .contactNumber span{
    font-size: 13.5px;
    font-weight: 500;
    text-wrap: balance;
   }
   .contactNumber label{
    font-size: 10px;
   }
</style>

        <div class="home-main-container">
            <div class="studentList-container">
                <?php
                if ($group_id !== null){
                    $personal_query = "SELECT useraccount.*, grouptable.group_name
                    FROM useraccount
                    LEFT JOIN grouptable ON useraccount.group_id = grouptable.group_id
                    WHERE user_account_id = '$user_account_id'";
                    $personal_result = $con->query($personal_query);
                    $personal_data = $personal_result->fetch_assoc();
                    // echo $personal_data['full_name'];
                    $isEmptyInfo = empty($personal_data['full_name']) || empty($personal_data['student_number']) || empty($personal_data['component_name']) || empty($personal_data['group_name']) || empty($personal_data['contactNumber']) || empty($personal_data['email_address']) || empty($personal_data['homeaddress']);

                ?>
                <div class="page-title">
                    <div class="titleContainer">
                        <span class="group_id">QRcode ID</span>
                    </div>
                </div>
                <div class="header-container" style="margin-bottom: 1rem;">
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1" id="downloadBtn"><i class="bx bx-download"></i>Download</button>
                </div>
                <div class="idContainer" id="cardContent">
                    <div class="card front">
                        <div class="logo">
                            <img src="../assets/img/Logo.png" alt="University Logo">
                        </div>
                        <div class="picture">
                            <?php
                                $picture = $personal_data['picture'];
                                $imageSrc = $picture ? "./$picture" : "./uploads/default.jpeg";
                            ?>

                            <img src="./<?php echo $imageSrc; ?>" alt="Id Picture">
                        </div>
                        <div class="personalInformation">
                                <div class="studentName">
                                    <span><?php echo $personal_data['full_name']; ?></span>
                                    <label>Student Name</label>
                                </div>
                                <div class="studentNumber">
                                    <label>Student Number:</label>
                                    <span><?php echo $personal_data['student_number']; ?></span >
                                </div>
                                <div class="studentNumber">
                                    <label>Component:</label>
                                    <span><?php echo $personal_data['component_name']; ?></span>
                                </div>

                                <div class="studentNumber">
                                    <label>Group Name:</label>
                                    <span><?php echo $personal_data['group_name']; ?></span>
                                </div>
                        </div>
                    </div>
                    <div class="card back">
                        <div class="qrCode">
                            <?php
                            $qrcode = ltrim($personal_data['qrimage'], './');
                            // $imageSrc = $picture ? "./$picture" : "./uploads/default.png";
                            // echo"<img src='../$qrcode' alt='QR Code'>";
                            ?>
                            <img src="../<?php echo $qrcode; ?>" alt="Id Picture">
                        </div>
                        <div class='contactInformation'>
                            <span class="contactTitle">Contact Information</span>
                            <div class='contactNumber'>
                                <span><?php echo $personal_data['contactNumber']; ?></span>
                                <label>Contact Number</label>
                            </div>

                            <div class="contactNumber">
                                <span><?php echo $personal_data['email_address']; ?></span>
                                <label>Email Address</label>
                            </div>
                        
                            <div class="contactNumber">
                                <span><?php echo $personal_data['homeaddress']; ?></span>
                                <label>Home Address</label>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }else{
                    echo "<h2 style='text-align: center;'>No Assigned Group yet.</h2>";
                    $isEmptyInfo = true;
                }
                ?>
            
            </div>
        </div>
    </section>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 
<script>
    const downloadBtn = document.getElementById('downloadBtn');
downloadBtn.addEventListener('click', () => {
    <?php if ($isEmptyInfo) { ?>
        Swal.fire({
            icon: "warning",
            title: "You can't download yet",
            text: "Please complete the information in your profile.",
            showConfirmButton: false,
            timer: 5000
        }).then(() => {
            window.location.href = "profile.php";
        });
    <?php } else { ?>
        const content = document.getElementById('cardContent');
        const clonedContent = content.cloneNode(true);
        clonedContent.classList.remove('idContainer');
        clonedContent.classList.remove('home-main-container');
        // Set the width of the cloned content to 843px
        clonedContent.style.width = '843px';

        // Calculate the left margin to center horizontally
        // const marginLeft = `${(window.innerWidth - parseInt(clonedContent.style.width)) / 2}px`;
        const screenWidth = window.innerWidth;

        if (screenWidth <= 767) {
            marginLeft = `calc(((100% - ${clonedContent.style.width}) / 2) - 100)px`;
            // // marginLeft = `${(window.innerWidth - parseInt(clonedContent.style.width)) / 2 + 100}px`;
            // firstMarginLeft = `${(window.innerWidth - parseInt(clonedContent.style.width)) / 2}`;
            // marginLeft = `${(parseInt(firstMarginLeft) / 2) + 50}px`;
        } else {
            marginLeft = `${(window.innerWidth - parseInt(clonedContent.style.width)) / 2}px`;
        }
        console.log(marginLeft);
        // console.log(firstMarginLeft);
        // Set margin-right to 0 and margin-top to 0, and apply the margin-left
        clonedContent.style.margin = '0 auto';
        clonedContent.style.marginLeft = marginLeft;

        // Apply flex properties to the cloned content
        clonedContent.style.display = 'flex';
        clonedContent.style.flexDirection = 'row';
        clonedContent.style.gap = '2rem';
        clonedContent.style.justifyContent = 'center';

        const opt = {
            margin: 10,
            filename: 'NSTP_Id.pdf',
            image: { type: 'jpeg', quality: .98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // New Promise-based usage:
        html2pdf().set(opt).from(clonedContent).save();
    <?php } ?>
});

</script>
    <script src="../asset/js/index.js"></script>
    <script src="../asset/js/topbar.js"></script>
    </body>
</html>