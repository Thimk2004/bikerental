<?php
session_start();
error_reporting(0); // Turn off all error reporting; in a production environment, it's recommended to enable or set to E_ALL & ~E_NOTICE
include('includes/config.php'); // Include database configuration

// Check if user is logged in
if (strlen($_SESSION['login']) == 0) {
    header('location:index.php'); // Redirect to homepage or login page if not logged in
    exit(); // Terminate script execution
} else { // User is logged in

    $error = ''; // Initialize error message variable
    $msg = '';   // Initialize success message variable

    // Get the ID of the current logged-in user
    $loggedInUserEmail = $_SESSION['login'];
    $userIdForVehicle = null;
    $sql_get_userid = "SELECT id FROM tblusers WHERE EmailId = :email";
    $query_get_userid = $dbh->prepare($sql_get_userid);
    $query_get_userid->bindParam(':email', $loggedInUserEmail, PDO::PARAM_STR);
    $query_get_userid->execute();
    $user_row = $query_get_userid->fetch(PDO::FETCH_OBJ);
    if ($user_row) {
        $userIdForVehicle = $user_row->id;
    } else {
        // If user ID is not found, it might be a database issue or session anomaly
        $error = "Error: User ID not found. Please log in again.";
    }

    if (isset($_POST['submit']) && $userIdForVehicle) // Ensure there is a user ID before processing submission
    {
        // Get motorcycle details from form
        $vehicletitle = $_POST['vehicletitle'];
        $brand = $_POST['brandname'];
        $vehicleoverview = $_POST['vehicalorcview'];
        $priceperday = floatval($_POST['priceperday']);
        $fueltype = $_POST['fueltype'];
        $enginedisplacement = intval($_POST['enginedisplacement']);
        $modelyear = intval($_POST['modelyear']);
        $seatingcapacity = intval($_POST['seatingcapacity']);
        $biketype = $_POST['biketype'];
        $transactioncount = isset($_POST['transactioncount']) ? intval($_POST['transactioncount']) : 0;

        // Handle image uploads - Generate unique names
        // IMPORTANT: Changed target directory to admin/img/vehicleimages/
        $target_dir = "admin/img/vehicleimages/";
        if (!file_exists($target_dir) && !is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Create directory if it does not exist
        }

        $vimage1 = '';
        if (!empty($_FILES["img1"]["name"])) {
            $ext = pathinfo($_FILES["img1"]["name"], PATHINFO_EXTENSION);
            $vimage1 = md5(uniqid(rand(), true)) . '.' . $ext; // Generate unique file name
            move_uploaded_file($_FILES["img1"]["tmp_name"], $target_dir . $vimage1);
        }

        $vimage2 = '';
        if (!empty($_FILES["img2"]["name"])) {
            $ext = pathinfo($_FILES["img2"]["name"], PATHINFO_EXTENSION);
            $vimage2 = md5(uniqid(rand(), true)) . '.' . $ext;
            move_uploaded_file($_FILES["img2"]["tmp_name"], $target_dir . $vimage2);
        }

        $vimage3 = '';
        if (!empty($_FILES["img3"]["name"])) {
            $ext = pathinfo($_FILES["img3"]["name"], PATHINFO_EXTENSION);
            $vimage3 = md5(uniqid(rand(), true)) . '.' . $ext;
            move_uploaded_file($_FILES["img3"]["tmp_name"], $target_dir . $vimage3);
        }

        $vimage4 = '';
        if (!empty($_FILES["img4"]["name"])) {
            $ext = pathinfo($_FILES["img4"]["name"], PATHINFO_EXTENSION);
            $vimage4 = md5(uniqid(rand(), true)) . '.' . $ext;
            move_uploaded_file($_FILES["img4"]["tmp_name"], $target_dir . $vimage4);
        }

        $vimage5 = '';
        if (!empty($_FILES["img5"]["name"])) {
            $ext = pathinfo($_FILES["img5"]["name"], PATHINFO_EXTENSION);
            $vimage5 = md5(uniqid(rand(), true)) . '.' . $ext;
            move_uploaded_file($_FILES["img5"]["tmp_name"], $target_dir . $vimage5);
        }
        // End of Handle image uploads

        // Handle checkbox values
        $airconditioner = isset($_POST['airconditioner']) ? 1 : 0;
        $powerdoorlocks = isset($_POST['powerdoorlocks']) ? 1 : 0;
        $antilockbrakingsys = isset($_POST['antilockbrakingsys']) ? 1 : 0;
        $brakeassist = isset($_POST['brakeassist']) ? 1 : 0;
        $powersteering = isset($_POST['powersteering']) ? 1 : 0;
        $driverairbag = isset($_POST['driverairbag']) ? 1 : 0;
        $passengerairbag = isset($_POST['passengerairbag']) ? 1 : 0;
        $powerwindow = isset($_POST['powerwindow']) ? 1 : 0;
        $cdplayer = isset($_POST['cdplayer']) ? 1 : 0;
        $centrallocking = isset($_POST['centrallocking']) ? 1 : 0;
        $crashcensor = isset($_POST['crashcensor']) ? 1 : 0;
        $leatherseats = isset($_POST['leatherseats']) ? 1 : 0;

        // Insert into tblvehicles table
        $sql_vehicle = "INSERT INTO tblvehicles(VehiclesTitle,VehiclesBrand,UserId,BikeType,VehiclesOverview,PricePerDay,FuelType,EngineDisplacement,ModelYear,SeatingCapacity,TransactionCount,Vimage1,Vimage2,Vimage3,Vimage4,Vimage5,AirConditioner,PowerDoorLocks,AntiLockBrakingSystem,BrakeAssist,PowerSteering,DriverAirbag,PassengerAirbag,PowerWindows,CDPlayer,CentralLocking,CrashSensor,LeatherSeats) VALUES(:vehicletitle,:brand,:userid,:biketype,:vehicleoverview,:priceperday,:fueltype,:enginedisplacement,:modelyear,:seatingcapacity,:transactioncount,:vimage1,:vimage2,:vimage3,:vimage4,:vimage5,:airconditioner,:powerdoorlocks,:antilockbrakingsys,:brakeassist,:powersteering,:driverairbag,:passengerairbag,:powerwindow,:cdplayer,:centrallocking,:crashcensor,:leatherseats)";
        $query_vehicle = $dbh->prepare($sql_vehicle);
        $query_vehicle->bindParam(':vehicletitle', $vehicletitle, PDO::PARAM_STR);
        $query_vehicle->bindParam(':brand', $brand, PDO::PARAM_INT);
        $query_vehicle->bindParam(':userid', $userIdForVehicle, PDO::PARAM_INT); // Bind UserId from logged-in user
        $query_vehicle->bindParam(':biketype', $biketype, PDO::PARAM_STR);
        $query_vehicle->bindParam(':vehicleoverview', $vehicleoverview, PDO::PARAM_STR);
        $query_vehicle->bindParam(':priceperday', $priceperday, PDO::PARAM_STR);
        $query_vehicle->bindParam(':fueltype', $fueltype, PDO::PARAM_STR);
        $query_vehicle->bindParam(':enginedisplacement', $enginedisplacement, PDO::PARAM_INT);
        $query_vehicle->bindParam(':modelyear', $modelyear, PDO::PARAM_INT);
        $query_vehicle->bindParam(':seatingcapacity', $seatingcapacity, PDO::PARAM_INT);
        $query_vehicle->bindParam(':transactioncount', $transactioncount, PDO::PARAM_INT);
        $query_vehicle->bindParam(':vimage1', $vimage1, PDO::PARAM_STR);
        $query_vehicle->bindParam(':vimage2', $vimage2, PDO::PARAM_STR);
        $query_vehicle->bindParam(':vimage3', $vimage3, PDO::PARAM_STR);
        $query_vehicle->bindParam(':vimage4', $vimage4, PDO::PARAM_STR);
        $query_vehicle->bindParam(':vimage5', $vimage5, PDO::PARAM_STR);
        $query_vehicle->bindParam(':airconditioner', $airconditioner, PDO::PARAM_INT);
        $query_vehicle->bindParam(':powerdoorlocks', $powerdoorlocks, PDO::PARAM_INT);
        $query_vehicle->bindParam(':antilockbrakingsys', $antilockbrakingsys, PDO::PARAM_INT);
        $query_vehicle->bindParam(':brakeassist', $brakeassist, PDO::PARAM_INT);
        $query_vehicle->bindParam(':powersteering', $powersteering, PDO::PARAM_INT);
        $query_vehicle->bindParam(':driverairbag', $driverairbag, PDO::PARAM_INT);
        $query_vehicle->bindParam(':passengerairbag', $passengerairbag, PDO::PARAM_INT);
        $query_vehicle->bindParam(':powerwindow', $powerwindow, PDO::PARAM_INT);
        $query_vehicle->bindParam(':cdplayer', $cdplayer, PDO::PARAM_INT);
        $query_vehicle->bindParam(':centrallocking', $centrallocking, PDO::PARAM_INT);
        $query_vehicle->bindParam(':crashcensor', $crashcensor, PDO::PARAM_INT);
        $query_vehicle->bindParam(':leatherseats', $leatherseats, PDO::PARAM_INT);

        try {
            $query_vehicle->execute();
            $lastInsertId = $dbh->lastInsertId();

            if ($lastInsertId) {
                // Process and insert additional contact info into tblvehicle_contacts
                if (isset($_POST['contact_type']) && is_array($_POST['contact_type'])) {
                    $contactTypes = $_POST['contact_type'];
                    $contactValues = $_POST['contact_value'];
                    $contactDescriptions = $_POST['contact_description'];

                    for ($i = 0; $i < count($contactTypes); $i++) {
                        $type = trim($contactTypes[$i]);
                        $value = trim($contactValues[$i]);
                        $description = trim($contactDescriptions[$i]);

                        if (!empty($type) && !empty($value)) {
                            // Insert into new tblvehicle_contacts table, linked to the new vehicle ID
                            $sql_contact = "INSERT INTO tblvehicle_contacts(VehicleId, ContactType, ContactValue, Description) VALUES(:vehicleid, :contacttype, :contactvalue, :description)";
                            $query_contact = $dbh->prepare($sql_contact);
                            $query_contact->bindParam(':vehicleid', $lastInsertId, PDO::PARAM_INT); // Link to VehicleId
                            $query_contact->bindParam(':contacttype', $type, PDO::PARAM_STR);
                            $query_contact->bindParam(':contactvalue', $value, PDO::PARAM_STR);
                            $query_contact->bindParam(':description', $description, PDO::PARAM_STR);
                            $query_contact->execute();
                        }
                    }
                }
                $msg = "Motorcycle posted successfully, contact information saved!";
            } else {
                $error = "Motorcycle post failed. Please try again.";
            }
        } // <-- this closes the try block
        catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <title>Motorcycle Rental Portal | Post Your Motorcycle</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="assets/css/styles.css" type="text/css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
    <link href="assets/css/slick.css" rel="stylesheet">
    <link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">

    <link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
    <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="assets/images/favicon-icon/24x24.png">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">

    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        /* Custom styles for contact info section alignment */
        .contact-info-section .form-group .col-sm-1,
        .contact-info-section .form-group .col-sm-2,
        .contact-info-section .form-group .col-sm-3 {
            padding-right: 5px;
            /* Adjust padding to make columns closer */
            padding-left: 5px;
        }

        .contact-info-section .form-group .control-label {
            text-align: right;
            /* Align labels to the right */
        }

        @media (max-width: 767px) {
            .contact-info-section .form-group .control-label {
                text-align: left;
                /* On small screens, align labels to the left */
            }
        }
    </style>

</head>

<body>
    <?php include('includes/header.php'); ?>

    <section class="page-header profile_page">
        <div class="container">
            <div class="page-header_wrap">
                <div class="page-heading">
                    <h1>Post Your Motorcycle</h1>
                </div>
                <ul class="coustom-breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li>Post Your Motorcycle</li>
                </ul>
            </div>
        </div>
        <div class="dark-overlay"></div>
    </section>

    <div class="ts-main-content">
        <div class="content-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">

                        <h2 class="page-title">Post Your Motorcycle</h2>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Motorcycle Details</div>
                                    <?php if ($error) { ?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if ($msg) { ?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

                                    <div class="panel-body">
                                        <form method="post" class="form-horizontal" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Motorcycle Title<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <input type="text" name="vehicletitle" class="form-control" required>
                                                </div>
                                                <label class="col-sm-2 control-label">Select Brand<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <select class="selectpicker" name="brandname" required>
                                                        <option value=""> Select </option>
                                                        <?php $ret = "select id,BrandName from tblbrands";
                                                        $query = $dbh->prepare($ret);
                                                        $query->execute();
                                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                        if ($query->rowCount() > 0) {
                                                            foreach ($results as $result) {
                                                        ?>
                                                                <option value="<?php echo htmlentities($result->id); ?>"><?php echo htmlentities($result->BrandName); ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Motorcycle Overview<span style="color:red">*</span></label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="vehicalorcview" rows="3" required></textarea>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Price (HKD)<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" name="priceperday" class="form-control" required>
                                                </div>
                                                <label class="col-sm-2 control-label">Select Motorcycle Type<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <select class="selectpicker" name="biketype" required>
                                                        <option value=""> Select </option>
                                                        <option value="Naked">Naked (Street)</option>
                                                        <option value="Cruiser">Cruiser</option>
                                                        <option value="Sports">Sports</option>
                                                        <option value="Touring">Touring</option>
                                                        <option value="Off-road">Off-road</option>
                                                        <option value="Scooter">Scooter</option>
                                                        <option value="Electric motorcycle">Electric motorcycle</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Select Fuel Type<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <select class="selectpicker" name="fueltype" required>
                                                        <option value=""> Select </option>
                                                        <option value="Petrol">Petrol</option>
                                                        <option value="Diesel">Diesel</option>
                                                        <option value="Electric">Electric</option>
                                                    </select>
                                                </div>
                                                <label class="col-sm-2 control-label">Engine Displacement (CC)<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" name="enginedisplacement" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Model Year<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" name="modelyear" class="form-control" required>
                                                </div>
                                                <label class="col-sm-2 control-label">Seating Capacity<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" name="seatingcapacity" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Transaction Count</label>
                                                <div class="col-sm-4">
                                                    <input type="number" name="transactioncount" class="form-control" value="0" min="0">
                                                </div>
                                                <div class="col-sm-6"></div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="contact-info-section">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <h4><b>Contact Information (for Motorcycle)</b></h4>
                                                        <p class="help-block">Enter additional contact methods for this motorcycle. These will be linked to this specific vehicle listing.</p>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Contact Method 1 Type</label>
                                                    <div class="col-sm-3">
                                                        <select class="selectpicker" name="contact_type[]">
                                                            <option value="">Select Type</option>
                                                            <option value="Phone">Phone</option>
                                                            <option value="Email">Email</option>
                                                            <option value="WeChat">WeChat</option>
                                                            <option value="WhatsApp">WhatsApp</option>
                                                            <option value="Telegram">Telegram</option>
                                                            <option value="Line">Line</option>
                                                            <option value="Nickname">Nickname</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2 control-label">Value</label>
                                                    <div class="col-sm-3">
                                                        <input type="text" name="contact_value[]" class="form-control">
                                                    </div>
                                                    <label class="col-sm-1 control-label">Description</label>
                                                    <div class="col-sm-1">
                                                        <input type="text" name="contact_description[]" class="form-control" placeholder="e.g., WhatsApp">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Contact Method 2 Type</label>
                                                    <div class="col-sm-3">
                                                        <select class="selectpicker" name="contact_type[]">
                                                            <option value="">Select Type</option>
                                                            <option value="Phone">Phone</option>
                                                            <option value="Email">Email</option>
                                                            <option value="WeChat">WeChat</option>
                                                            <option value="WhatsApp">WhatsApp</option>
                                                            <option value="Telegram">Telegram</option>
                                                            <option value="Line">Line</option>
                                                            <option value="Nickname">Nickname</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2 control-label">Value</label>
                                                    <div class="col-sm-3">
                                                        <input type="text" name="contact_value[]" class="form-control">
                                                    </div>
                                                    <label class="col-sm-1 control-label">Description</label>
                                                    <div class="col-sm-1">
                                                        <input type="text" name="contact_description[]" class="form-control" placeholder="e.g., Personal">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Contact Method 3 Type</label>
                                                    <div class="col-sm-3">
                                                        <select class="selectpicker" name="contact_type[]">
                                                            <option value="">Select Type</option>
                                                            <option value="Phone">Phone</option>
                                                            <option value="Email">Email</option>
                                                            <option value="WeChat">WeChat</option>
                                                            <option value="WhatsApp">WhatsApp</option>
                                                            <option value="Telegram">Telegram</option>
                                                            <option value="Line">Line</option>
                                                            <option value="Nickname">Nickname</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2 control-label">Value</label>
                                                    <div class="col-sm-3">
                                                        <input type="text" name="contact_value[]" class="form-control">
                                                    </div>
                                                    <label class="col-sm-1 control-label">Description</label>
                                                    <div class="col-sm-1">
                                                        <input type="text" name="contact_description[]" class="form-control" placeholder="e.g., Office">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <h4><b>Upload Images</b></h4>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    Image 1 <span style="color:red">*</span><input type="file" name="img1" required>
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 2<input type="file" name="img2">
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 3<input type="file" name="img3">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    Image 4<input type="file" name="img4">
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 5<input type="file" name="img5">
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                            <div class="hr-dashed"></div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">Accessories / Features</div>
                                                        <div class="panel-body">
                                                            <div class="form-group">
                                                                <div class="col-sm-3">
                                                                    <div class="checkbox checkbox-inline">
                                                                        <input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" value="1">
                                                                        <label for="antilockbrakingsys"> AntiLock Braking System </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="checkbox checkbox-inline">
                                                                        <input type="checkbox" id="brakeassist" name="brakeassist" value="1">
                                                                        <label for="brakeassist"> Brake Assist </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="checkbox checkbox-inline">
                                                                        <input type="checkbox" id="powersteering" name="powersteering" value="1">
                                                                        <label for="powersteering"> Power Steering </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="checkbox checkbox-inline">
                                                                        <input type="checkbox" id="centrallocking" name="centrallocking" value="1">
                                                                        <label for="centrallocking"> Central Locking </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <div class="col-sm-3">
                                                                    <div class="checkbox checkbox-inline">
                                                                        <input type="checkbox" id="crashcensor" name="crashcensor" value="1">
                                                                        <label for="crashcensor"> Crash Sensor </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="checkbox checkbox-inline">
                                                                        <input type="checkbox" id="leatherseats" name="leatherseats" value="1">
                                                                        <label for="leatherseats"> Leather Seats </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-2">
                                                    <button class="btn btn-default" type="reset">Cancel</button>
                                                    <button class="btn btn-primary" name="submit" type="submit">Save changes</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>



                    </div>
                </div>



            </div>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/fileinput.js"></script>
    <script src="js/chartData.js"></script>
    <script src="js/main.js"></script>
    <script>
        // JavaScript to add more contact methods dynamically
        $(document).ready(function() {
            // Remove the previous #add-contact-method click handler if it exists
            // This is important to prevent multiple bindings if the user reloads the page via AJAX or similar
            // For a simple page reload, this might not be strictly necessary, but it's good practice.
            // $('#add-contact-method').off('click'); // Uncomment if you find duplicate event bindings

            $('#add-contact-method').click(function() {
                var newContactHtml = `
                    <div class="form-group additional-contact-item">
                        <label class="col-sm-2 control-label">Contact Type</label>
                        <div class="col-sm-3">
                            <select class="selectpicker form-control" name="contact_type[]">
                                <option value="">Select Type</option>
                                <option value="Phone">Phone</option>
                                <option value="Email">Email</option>
                                <option value="WeChat">WeChat</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Telegram">Telegram</option>
                                <option value="Line">Line</option>
                                <option value="Nickname">Nickname</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Contact Value</label>
                        <div class="col-sm-3">
                            <input type="text" name="contact_value[]" class="form-control" placeholder="e.g., 23456789">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="contact_description[]" class="form-control" placeholder="Description (e.g., Whatsapp only)">
                        </div>
                    </div>
                `;
                $('#additional-contacts-container').append(newContactHtml);
                // Re-initialize selectpicker for newly added elements
                $('.selectpicker').selectpicker('refresh');
            });
        });
    </script>
</body>

</html>