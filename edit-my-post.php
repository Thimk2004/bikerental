<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('includes/config.php');

// 1. User Authentication Check
if(strlen($_SESSION['login']) == 0) {
    header('location:index.php'); // Redirect to homepage or login if not logged in
    exit();
} else { // User is logged in

    $error = '';
    $msg = '';

    $id = intval($_GET['id']); // Get vehicle ID from URL

    // Get the ID of the current logged-in user
    $loggedInUserEmail = $_SESSION['login'];
    $userIdForAuth = null;
    $sql_get_userid_auth = "SELECT id FROM tblusers WHERE EmailId = :email";
    $query_get_userid_auth = $dbh->prepare($sql_get_userid_auth);
    $query_get_userid_auth->bindParam(':email', $loggedInUserEmail, PDO::PARAM_STR);
    $query_get_userid_auth->execute();
    $user_row_auth = $query_get_userid_auth->fetch(PDO::FETCH_OBJ);
    if ($user_row_auth) {
        $userIdForAuth = $user_row_auth->id;
    } else {
        $error = "Error: Your user ID could not be determined. Please log in again.";
        exit(); // It's critical to exit here if user ID isn't found
    }

    // --- 2. Process Form Submission ---
    if(isset($_POST['submit'])) {
        // Get all form fields' values
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

        // User ID for the vehicle is the logged-in user's ID
        $selectedUserId = $userIdForAuth; // This is now fixed to the logged-in user

        // Handle all checkbox fields: 1 if checked, 0 otherwise
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

        // SQL UPDATE statement, update all relevant fields in tblvehicles, including UserId
        // IMPORTANT: Added security check to only update if UserId matches the logged-in user
        $sql = "UPDATE tblvehicles SET
                    VehiclesTitle=:vehicletitle,
                    VehiclesBrand=:brand,
                    UserId=:userid,
                    BikeType=:biketype,
                    VehiclesOverview=:vehicleoverview,
                    PricePerDay=:priceperday,
                    FuelType=:fueltype,
                    EngineDisplacement=:enginedisplacement,
                    ModelYear=:modelyear,
                    SeatingCapacity=:seatingcapacity,
                    TransactionCount=:transactioncount,
                    AirConditioner=:airconditioner,
                    PowerDoorLocks=:powerdoorlocks,
                    AntiLockBrakingSystem=:antilockbrakingsys,
                    BrakeAssist=:brakeassist,
                    PowerSteering=:powersteering,
                    DriverAirbag=:driverairbag,
                    PassengerAirbag=:passengerairbag,
                    PowerWindows=:powerwindow,
                    CDPlayer=:cdplayer,
                    CentralLocking=:centrallocking,
                    CrashSensor=:crashcensor,
                    LeatherSeats=:leatherseats
              WHERE id=:id AND UserId=:userid_check"; // Ensure the user owns this vehicle

        $query = $dbh->prepare($sql);
        // Parameter binding
        $query->bindParam(':vehicletitle', $vehicletitle, PDO::PARAM_STR);
        $query->bindParam(':brand', $brand, PDO::PARAM_INT);
        $query->bindParam(':userid', $selectedUserId, PDO::PARAM_INT);
        $query->bindParam(':biketype', $biketype, PDO::PARAM_STR);
        $query->bindParam(':vehicleoverview', $vehicleoverview, PDO::PARAM_STR);
        $query->bindParam(':priceperday', $priceperday, PDO::PARAM_INT);
        $query->bindParam(':fueltype', $fueltype, PDO::PARAM_STR);
        $query->bindParam(':enginedisplacement', $enginedisplacement, PDO::PARAM_INT);
        $query->bindParam(':modelyear', $modelyear, PDO::PARAM_INT);
        $query->bindParam(':seatingcapacity', $seatingcapacity, PDO::PARAM_INT);
        $query->bindParam(':transactioncount', $transactioncount, PDO::PARAM_INT);
        $query->bindParam(':airconditioner', $airconditioner, PDO::PARAM_INT);
        $query->bindParam(':powerdoorlocks', $powerdoorlocks, PDO::PARAM_INT);
        $query->bindParam(':antilockbrakingsys', $antilockbrakingsys, PDO::PARAM_INT);
        $query->bindParam(':brakeassist', $brakeassist, PDO::PARAM_INT);
        $query->bindParam(':powersteering', $powersteering, PDO::PARAM_INT);
        $query->bindParam(':driverairbag', $driverairbag, PDO::PARAM_INT);
        $query->bindParam(':passengerairbag', $passengerairbag, PDO::PARAM_INT);
        $query->bindParam(':powerwindow', $powerwindow, PDO::PARAM_INT);
        $query->bindParam(':cdplayer', $cdplayer, PDO::PARAM_INT);
        $query->bindParam(':centrallocking', $centrallocking, PDO::PARAM_INT);
        $query->bindParam(':crashcensor', $crashcensor, PDO::PARAM_INT);
        $query->bindParam(':leatherseats', $leatherseats, PDO::PARAM_INT);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':userid_check', $userIdForAuth, PDO::PARAM_INT); // Security check

        try {
            $query->execute();
            if ($query->rowCount() > 0) { // Check if any row was updated
                $msg = "Motorcycle data updated successfully!";

                // --- Handle update of additional contact methods for the LOGGED-IN USER ---
                // 1. Delete all contacts associated with this specific UserId from tbluser_contacts
                $sql_delete_contacts = "DELETE FROM tbluser_contacts WHERE UserId = :userid";
                $query_delete_contacts = $dbh->prepare($sql_delete_contacts);
                $query_delete_contacts->bindParam(':userid', $userIdForAuth, PDO::PARAM_INT); // Delete contacts for the logged-in user
                $query_delete_contacts->execute();

                // 2. Insert new contact methods for the logged-in user
                if (isset($_POST['contact_type']) && is_array($_POST['contact_type'])) {
                    $contactTypes = $_POST['contact_type'];
                    $contactValues = $_POST['contact_value'];
                    $contactDescriptions = $_POST['contact_description'];

                    for ($i = 0; $i < count($contactTypes); $i++) {
                        $type = trim($contactTypes[$i]);
                        $value = trim($contactValues[$i]);
                        $description = trim($contactDescriptions[$i]);

                        if (!empty($type) && !empty($value)) {
                            $sql_insert_contact = "INSERT INTO tbluser_contacts(UserId, ContactType, ContactValue, Description) VALUES(:userid, :contacttype, :contactvalue, :description)";
                            $query_insert_contact = $dbh->prepare($sql_insert_contact);
                            $query_insert_contact->bindParam(':userid', $userIdForAuth, PDO::PARAM_INT); // Bind to logged-in UserId
                            $query_insert_contact->bindParam(':contacttype', $type, PDO::PARAM_STR);
                            $query_insert_contact->bindParam(':contactvalue', $value, PDO::PARAM_STR);
                            $query_insert_contact->bindParam(':description', $description, PDO::PARAM_STR);
                            $query_insert_contact->execute();
                        }
                    }
                }
            } else {
                 $error = "Motorcycle data could not be updated. Ensure you are the owner of this post.";
            }
        } catch (PDOException $e) {
            $error = "Database error during update: " . $e->getMessage();
        }
    }

    // --- 3. Retrieve current vehicle details, including owner info and additional contact methods ---
    // IMPORTANT: Added UserId to WHERE clause for authorization
    $sql_vehicle_data = "SELECT tv.*, tb.BrandName, tb.id as bid, tu.FullName, tu.EmailId, tu.id as seller_userid
                         FROM tblvehicles tv
                         JOIN tblbrands tb ON tb.id=tv.VehiclesBrand
                         LEFT JOIN tblusers tu ON tu.id = tv.UserId
                         WHERE tv.id=:id AND tv.UserId=:userid_check";
    $query_vehicle_data = $dbh->prepare($sql_vehicle_data);
    $query_vehicle_data->bindParam(':id', $id, PDO::PARAM_INT);
    $query_vehicle_data->bindParam(':userid_check', $userIdForAuth, PDO::PARAM_INT);
    $query_vehicle_data->execute();
    $vehicle_data = $query_vehicle_data->fetch(PDO::FETCH_OBJ);

    // If vehicle data is not found or user is not authorized, redirect
    if (!$vehicle_data) {
        header('location: my-vehicles.php'); // Or a suitable "Access Denied" page
        exit();
    }

    // Fetch current user's contacts (from tbluser_contacts)
    $current_contacts = [];
    if ($userIdForAuth) { // Ensure user ID exists before fetching contacts
        $sql_current_contacts = "SELECT ContactType, ContactValue, Description FROM tbluser_contacts WHERE UserId = :userid";
        $query_current_contacts = $dbh->prepare($sql_current_contacts);
        $query_current_contacts->bindParam(':userid', $userIdForAuth, PDO::PARAM_INT);
        $query_current_contacts->execute();
        $current_contacts = $query_current_contacts->fetchAll(PDO::FETCH_OBJ);
    }
?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">

	<title>Motorcycle Rental Portal | Edit My Motorcycle Post</title>

	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-social.css">
	<link rel="stylesheet" href="assets/css/bootstrap-select.css">
	<link rel="stylesheet" href="assets/css/fileinput.min.css">
	<link rel="stylesheet" href="assets/css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="assets/css/styles.css"> <link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
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
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap{
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
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
        /* Styling for the remove button */
        .remove-contact-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
            font-size: 0.9em;
        }
        .remove-contact-btn:hover {
            background-color: #c82333;
        }
	</style>
</head>

<body>
	<?php include('includes/header.php');?>
    <?php include('includes/colorswitcher.php');?>


	<section class="page-header profile_page">
        <div class="container">
            <div class="page-header_wrap">
                <div class="page-heading">
                    <h1>Edit My Motorcycle Post</h1>
                </div>
                <ul class="coustom-breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li><a href="my-vehicles.php">My Posts</a></li> <li>Edit Post</li>
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

						<h2 class="page-title">Edit Motorcycle Details</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Basic Info</div>
									<div class="panel-body">
    <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

    <?php
    if($vehicle_data) { // If vehicle data is found
    ?>

    <form method="post" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
            <label class="col-sm-2 control-label">Motorcycle Title<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="text" name="vehicletitle" class="form-control" value="<?php echo htmlentities($vehicle_data->VehiclesTitle)?>" required>
            </div>
            <label class="col-sm-2 control-label">Select Brand<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="brandname" required>
                    <option value="<?php echo htmlentities($vehicle_data->bid);?>"><?php echo htmlentities($vehicle_data->BrandName); ?> </option>
                    <?php
                    $ret_brands="select id,BrandName from tblbrands";
                    $query_brands= $dbh -> prepare($ret_brands);
                    $query_brands-> execute();
                    $resultss_brands = $query_brands -> fetchAll(PDO::FETCH_OBJ);
                    if($query_brands -> rowCount() > 0) {
                        foreach($resultss_brands as $results_brand) {
                            if($results_brand->BrandName == $vehicle_data->BrandName) {
                                continue;
                            } else {
                    ?>
                    <option value="<?php echo htmlentities($results_brand->id);?>"><?php echo htmlentities($results_brand->BrandName);?></option>
                    <?php
                            }
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Motorcycle Overview<span style="color:red">*</span></label>
            <div class="col-sm-10">
                <textarea class="form-control" name="vehicalorcview" rows="3" required><?php echo htmlentities($vehicle_data->VehiclesOverview);?></textarea>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Price (HKD)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="priceperday" class="form-control" value="<?php echo htmlentities($vehicle_data->PricePerDay);?>" required>
            </div>
            <label class="col-sm-2 control-label">Select Motorcycle Type<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="biketype" required>
                    <option value="<?php echo htmlentities($vehicle_data->BikeType);?>"><?php echo htmlentities($vehicle_data->BikeType);?> (<?php // 這裡需要根據英文值顯示對應的中文翻譯
                        switch($vehicle_data->BikeType) {
                            case 'Naked': echo '街車'; break;
                            case 'Cruiser': echo '巡航車'; break;
                            case 'Sports': echo '跑車'; break;
                            case 'Touring': echo '旅行車'; break;
                            case 'Off-road': echo '越野車'; break;
                            case 'Scooter': echo '綿羊仔'; break;
                            case 'Electric motorcycle': echo '電動車'; break;
                            default: echo ''; break;
                        }
                    ?>) </option>
                    <?php
                    $bike_types = ['Naked', 'Cruiser', 'Sports', 'Touring', 'Off-road', 'Scooter', 'Electric motorcycle'];
                    foreach ($bike_types as $type) {
                        if($type == $vehicle_data->BikeType) continue;
                    ?>
                    <option value="<?php echo htmlentities($type);?>"><?php echo htmlentities($type);?> (<?php
                        switch($type) {
                            case 'Naked': echo '街車'; break;
                            case 'Cruiser': echo '巡航車'; break;
                            case 'Sports': echo '跑車'; break;
                            case 'Touring': echo '旅行車'; break;
                            case 'Off-road': echo '越野車'; break;
                            case 'Scooter': echo '綿羊仔'; break;
                            case 'Electric motorcycle': echo '電動車'; break;
                            default: echo ''; break;
                        }
                    ?>)</option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Select Fuel Type<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="fueltype" required>
                    <option value="<?php echo htmlentities($vehicle_data->FuelType);?>"> <?php echo htmlentities($vehicle_data->FuelType);?> </option>
                    <?php if ($vehicle_data->FuelType != 'Petrol') { ?><option value="Petrol">Petrol</option><?php } ?>
                    <?php if ($vehicle_data->FuelType != 'Diesel') { ?><option value="Diesel">Diesel</option><?php } ?>
                    <?php if ($vehicle_data->FuelType != 'Electric') { ?><option value="Electric">Electric</option><?php } ?>
                </select>
            </div>
            <label class="col-sm-2 control-label">Engine Displacement (CC)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="enginedisplacement" class="form-control" value="<?php echo htmlentities($vehicle_data->EngineDisplacement);?>" required>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Model Year<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="modelyear" class="form-control" value="<?php echo htmlentities($vehicle_data->ModelYear);?>" required>
            </div>
            <label class="col-sm-2 control-label">Seating Capacity<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="seatingcapacity" class="form-control" value="<?php echo htmlentities($vehicle_data->SeatingCapacity);?>" required>
            </div>
        </div>
        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Transaction Count</label>
            <div class="col-sm-4">
                <input type="number" name="transactioncount" class="form-control" value="<?php echo htmlentities($vehicle_data->TransactionCount);?>" min="0">
            </div>
            <div class="col-sm-6"></div>
        </div>
        <div class="hr-dashed"></div>

        <div class="contact-info-section">
            <div class="form-group">
                <div class="col-sm-12">
                    <h4><b>Additional Contact Methods (for this Motorcycle)</b></h4>
                    <p class="help-block">Add or update contact methods specific to this vehicle listing.</p>
                </div>
            </div>

            <div id="additional-contacts-container">
                <?php if (!empty($current_contacts)) {
                    foreach ($current_contacts as $contact) { ?>
                    <div class="form-group additional-contact-item">
                        <label class="col-sm-2 control-label">Contact Type</label>
                        <div class="col-sm-3">
                            <select class="selectpicker" name="contact_type[]">
                                <option value="<?php echo htmlentities($contact->ContactType); ?>" selected><?php echo htmlentities($contact->ContactType); ?></option>
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
                            <input type="text" name="contact_value[]" class="form-control" value="<?php echo htmlentities($contact->ContactValue); ?>">
                        </div>
                        <label class="col-sm-1 control-label">Description</label>
                        <div class="col-sm-1">
                            <input type="text" name="contact_description[]" class="form-control" value="<?php echo htmlentities($contact->Description); ?>">
                        </div>
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" class="remove-contact-btn">Remove</button>
                        </div>
                    </div>
                <?php }
                } else { ?>
                    <div class="form-group additional-contact-item">
                        <label class="col-sm-2 control-label">Contact Type</label>
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
                            <input type="text" name="contact_description[]" class="form-control">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="form-group">
                <div class="col-sm-12 text-center">
                    <button type="button" class="btn btn-info" id="add-contact-method">Add More Contact Methods</button>
                </div>
            </div>
        </div>
        <div class="hr-dashed"></div>

        <div class="form-group">
            <div class="col-sm-12">
                <h4><b>Motorcycle Images</b></h4>
                <p class="help-block">Images must be changed individually.</p>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                Image 1 <br>
                <?php if (!empty($vehicle_data->Vimage1)) { ?>
                    <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage1);?>" width="200" height="150" style="border:solid 1px #000">
                    <a href="changeimage1.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 1</a>
                <?php } else { ?>
                    No Image 1
                    <a href="changeimage1.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Add Image 1</a>
                <?php } ?>
            </div>
            <div class="col-sm-4">
                Image 2<br>
                <?php if (!empty($vehicle_data->Vimage2)) { ?>
                    <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage2);?>" width="200" height="150" style="border:solid 1px #000">
                    <a href="changeimage2.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 2</a>
                <?php } else { ?>
                    No Image 2
                    <a href="changeimage2.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Add Image 2</a>
                <?php } ?>
            </div>
            <div class="col-sm-4">
                Image 3<br>
                <?php if (!empty($vehicle_data->Vimage3)) { ?>
                    <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage3);?>" width="200" height="150" style="border:solid 1px #000">
                    <a href="changeimage3.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 3</a>
                <?php } else { ?>
                    No Image 3
                    <a href="changeimage3.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Add Image 3</a>
                <?php } ?>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-4">
                Image 4<br>
                <?php if (!empty($vehicle_data->Vimage4)) { ?>
                    <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage4);?>" width="200" height="150" style="border:solid 1px #000">
                    <a href="changeimage4.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 4</a>
                <?php } else { ?>
                    No Image 4
                    <a href="changeimage4.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Add Image 4</a>
                <?php } ?>
            </div>
            <div class="col-sm-4">
                Image 5<br>
                <?php if (!empty($vehicle_data->Vimage5)) { ?>
                    <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage5);?>" width="200" height="150" style="border:solid 1px #000">
                    <a href="changeimage5.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 5</a>
                <?php } else { ?>
                    No Image 5
                    <a href="changeimage5.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Add Image 5</a>
                <?php } ?>
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
                                <?php if($vehicle_data->AirConditioner==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="airconditioner" name="airconditioner" checked value="1">
                                    <label for="airconditioner"> Air Conditioner </label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="airconditioner" name="airconditioner" value="1">
                                    <label for="airconditioner"> Air Conditioner </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->PowerDoorLocks==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powerdoorlocks" name="powerdoorlocks" checked value="1">
                                    <label for="powerdoorlocks"> Power Door Locks </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powerdoorlocks" name="powerdoorlocks" value="1">
                                    <label for="powerdoorlocks"> Power Door Locks </label>
                                </div>
                                <?php }?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->AntiLockBrakingSystem==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" checked value="1">
                                    <label for="antilockbrakingsys"> AntiLock Braking System </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" value="1">
                                    <label for="antilockbrakingsys"> AntiLock Braking System </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->BrakeAssist==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="brakeassist" name="brakeassist" checked value="1">
                                    <label for="brakeassist"> Brake Assist </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="brakeassist" name="brakeassist" value="1">
                                    <label  for="brakeassist"> Brake Assist </label>
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-3">
                                <?php if($vehicle_data->PowerSteering==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powersteering" name="powersteering" checked value="1">
                                    <label for="powersteering"> Power Steering </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powersteering" name="powersteering" value="1">
                                    <label for="powersteering"> Power Steering </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->DriverAirbag==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="driverairbag" name="driverairbag" checked value="1">
                                    <label for="driverairbag">Driver Airbag</label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="driverairbag" name="driverairbag" value="1">
                                    <label for="driverairbag">Driver Airbag</label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->PassengerAirbag==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="passengerairbag" name="passengerairbag" checked value="1">
                                    <label for="passengerairbag"> Passenger Airbag </label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="passengerairbag" name="passengerairbag" value="1">
                                    <label for="passengerairbag"> Passenger Airbag </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->PowerWindows==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powerwindow" name="powerwindow" checked value="1">
                                    <label for="powerwindow"> Power Windows </label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powerwindow" name="powerwindow" value="1">
                                    <label for="powerwindow"> Power Windows </label>
                                </div>
                                <?php } ?>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-3">
                                <?php if($vehicle_data->CDPlayer==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="cdplayer" name="cdplayer" checked value="1">
                                    <label for="cdplayer"> CD Player </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="cdplayer" name="cdplayer" value="1">
                                    <label for="cdplayer"> CD Player </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->CentralLocking==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="centrallocking" name="centrallocking" checked value="1">
                                    <label for="centrallocking">Central Locking</label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="centrallocking" name="centrallocking" value="1">
                                    <label for="centrallocking">Central Locking</label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->CrashSensor==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="crashcensor" name="crashcensor" checked value="1">
                                    <label for="crashcensor"> Crash Sensor </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="crashcensor" name="crashcensor" value="1">
                                    <label for="crashcensor"> Crash Sensor </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($vehicle_data->LeatherSeats==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="leatherseats" name="leatherseats" checked value="1">
                                    <label for="leatherseats"> Leather Seats </label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="leatherseats" name="leatherseats" value="1">
                                    <label for="leatherseats"> Leather Seats </label>
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2" >
                <button class="btn btn-primary" name="submit" type="submit" style="margin-top:4%">Save changes</button>
            </div>
        </div>

    </form>
    <?php
    } else { // If no vehicle data is found
        echo "<div class='container-fluid'><div class='row'><div class='col-md-12'><h3 class='text-center'>Error: Motorcycle not found or you are not authorized to edit it.</h3></div></div></div>";
    }
    ?>
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
            $(document).ready(function() {
                // Initialize selectpicker
                $('.selectpicker').selectpicker();

                // Add more contact methods dynamically
                $('#add-contact-method').click(function() {
                    var newContactHtml = `
                        <div class="form-group additional-contact-item">
                            <label class="col-sm-2 control-label">Contact Type</label>
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
                                <input type="text" name="contact_value[]" class="form-control" placeholder="e.g., 23456789">
                            </div>
                            <label class="col-sm-1 control-label">Description</label>
                            <div class="col-sm-1">
                                <input type="text" name="contact_description[]" class="form-control" placeholder="e.g., WhatsApp only">
                            </div>
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="remove-contact-btn">Remove</button>
                            </div>
                        </div>
                    `;
                    $('#additional-contacts-container').append(newContactHtml);
                    $('.selectpicker').selectpicker('refresh'); // Re-initialize selectpicker for new elements
                    attachRemoveEventListeners(); // Attach event listeners to new remove buttons
                });

                // Function to attach remove event listeners
                function attachRemoveEventListeners() {
                    $('.remove-contact-btn').off('click').on('click', function() {
                        $(this).closest('.additional-contact-item').remove();
                    });
                }

                // Initial attachment for existing elements
                attachRemoveEventListeners();
            });
        </script>
    </body>
</html>
<?php } ?>
