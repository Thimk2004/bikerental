<?php
session_start();
error_reporting(0); // Disable all error reporting; for production, enable or set to E_ALL & ~E_NOTICE
include('includes/config.php'); // Include database configuration

// Handle booking request (if present) - Keeping this PHP logic as it was, but removing the frontend form
if (isset($_POST['submit'])) {
  if (strlen($_SESSION['login']) == 0) { // If user is not logged in
    echo "<script>alert('Please login to book this bike.');</script>"; // Use alert to prompt login
  } else {
    $vhid = $_GET['vhid'];
    $fromdate = $_POST['fromdate'];
    $todate = $_POST['todate'];
    $message = $_POST['message'];
    $useremail = $_SESSION['login'];

    // Check if date format is correct (dd/mm/yyyy)
    if (!preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $fromdate) || !preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $todate)) {
      echo "<script>alert('Invalid date format. Please use dd/mm/yyyy.');</script>";
    } else {
      // Convert dates to YYYY-MM-DD format for database comparison
      $fromdate_db = DateTime::createFromFormat('d/m/Y', $fromdate)->format('Y-m-d');
      $todate_db = DateTime::createFromFormat('d/m/Y', $todate)->format('Y-m-d');

      // Check for overlapping bookings
      $sql_check_booking = "SELECT id FROM tblbooking WHERE VehicleId=:vhid AND Status IN (0,1) AND ((:fromdate_db BETWEEN FromDate AND ToDate) OR (:todate_db BETWEEN FromDate AND ToDate) OR (FromDate BETWEEN :fromdate_db AND :todate_db))";
      $query_check_booking = $dbh->prepare($sql_check_booking);
      $query_check_booking->bindParam(':vhid', $vhid, PDO::PARAM_INT);
      $query_check_booking->bindParam(':fromdate_db', $fromdate_db, PDO::PARAM_STR);
      $query_check_booking->bindParam(':todate_db', $todate_db, PDO::PARAM_STR);
      $query_check_booking->execute();

      if ($query_check_booking->rowCount() > 0) {
        echo "<script>alert('This bike is already booked or unavailable for the selected dates.');</script>";
      } else {
        // Insert new booking record
        $status = 0; // 0 for Pending
        $sql_insert_booking = "INSERT INTO tblbooking(userEmail,VehicleId,FromDate,ToDate,message,Status) VALUES(:useremail,:vhid,:fromdate,:todate,:message,:status)";
        $query_insert_booking = $dbh->prepare($sql_insert_booking);
        $query_insert_booking->bindParam(':useremail', $useremail, PDO::PARAM_STR);
        $query_insert_booking->bindParam(':vhid', $vhid, PDO::PARAM_INT);
        $query_insert_booking->bindParam(':fromdate', $fromdate, PDO::PARAM_STR); // Keep as varchar in DB
        $query_insert_booking->bindParam(':todate', $todate, PDO::PARAM_STR);   // Keep as varchar in DB
        $query_insert_booking->bindParam(':message', $message, PDO::PARAM_STR);
        $query_insert_booking->bindParam(':status', $status, PDO::PARAM_INT);
        $query_insert_booking->execute();
        $lastInsertId = $dbh->lastInsertId();

        if ($lastInsertId) {
          echo "<script>alert('Booking successful.');</script>";
        } else {
          echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
      }
    }
  }
}


// Get vehicle ID from URL
$vhid = intval($_GET['vhid']);

// Fetch vehicle details from database
// Ensure all necessary columns like BikeType, EngineDisplacement, TransactionCount are selected
$sql = "SELECT tv.*, tb.BrandName FROM tblvehicles tv JOIN tblbrands tb ON tv.VehiclesBrand = tb.id WHERE tv.id=:vhid";
$query = $dbh->prepare($sql);
$query->bindParam(':vhid', $vhid, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ); // Use fetch() as we expect only one record

?>

<!DOCTYPE HTML>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <title>Bike Rental Portal | Vehicle Details</title>
  <!--Bootstrap -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
  <!--Custom Style -->
  <link rel="stylesheet" href="assets/css/styles.css" type="text/css">
  <!--OWL Carousel slider-->
  <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
  <link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
  <!--slick-slider -->
  <link href="assets/css/slick.css" rel="stylesheet">
  <!--bootstrap-slider -->
  <link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
  <!--FontAwesome Font Style -->
  <!-- 移除 Font Awesome CSS 引用，因為我們將使用 SVG -->
  <!-- <link href="assets/css/font-awesome.min.css" rel="stylesheet"> -->

  <!-- SWITCHER -->
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
    /* Custom styles for details page */
    .listing-detail {
      padding: 30px 0;
    }

    .listing_images img {
      width: 100%;
      height: auto;
      object-fit: cover;
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .listing_images .small-images img {
      height: 120px;
      /* Smaller height for thumbnails */
      width: 100%;
      object-fit: cover;
      border-radius: 4px;
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .listing_images .small-images img:hover {
      transform: scale(1.05);
    }

    .main_features {
      background-color: #f8f8f8;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .main_features h2 {
      margin-top: 0;
      font-size: 2em;
      color: #333;
    }

    .main_features .list-price {
      font-size: 1.8em;
      font-weight: bold;
      color: #d9534f;
      /* Red color for price */
      margin-bottom: 15px;
    }

    .listing_other_info .feature-box {
      text-align: center;
      padding: 15px;
      border: 1px solid #eee;
      border-radius: 8px;
      margin-bottom: 10px;
      background-color: #fff;
      /* Ensure feature boxes align well in a grid */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100px;
      /* Adjust as needed for consistent height */
    }

    /* Styles for the new SVG icons */
    .listing_other_info .feature-box svg.feature-icon {
      width: 40px;
      /* Adjust size as needed */
      height: 40px;
      /* Adjust size as needed */
      margin-bottom: 5px;
      fill: #007bff;
      /* Set SVG fill color to match your theme */
    }

    .listing_other_info .feature-box p {
      margin: 0;
      font-size: 0.9em;
      color: #555;
      font-weight: bold;
    }

    .listing_other_info .feature-box span {
      display: block;
      font-size: 0.8em;
      color: #888;
    }

    .listing_detail_wrap h3 {
      font-size: 1.5em;
      margin-top: 20px;
      margin-bottom: 15px;
      color: #333;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
    }

    .listing_detail_wrap p {
      line-height: 1.8;
      color: #555;
    }

    .sidebar {
      background-color: #f8f8f8;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .sidebar h5 {
      font-size: 1.2em;
      margin-top: 0;
      margin-bottom: 15px;
      color: #333;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
    }

    .sidebar .form-group {
      margin-bottom: 15px;
    }

    .sidebar input[type="date"],
    .sidebar textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }

    .sidebar .btn-block {
      margin-top: 20px;
    }
  </style>
</head>

<body>

  <!-- Start Switcher -->
  <?php include('includes/colorswitcher.php'); ?>
  <!-- /Switcher -->

  <!--Header-->
  <?php include('includes/header.php'); ?>
  <!-- /Header -->

  <?php
  // Fetch vehicle details from database
  $vhid = intval($_GET['vhid']);
  // Ensure all necessary columns like BikeType, EngineDisplacement, TransactionCount are selected
  $sql = "SELECT tv.*, tb.BrandName FROM tblvehicles tv JOIN tblbrands tb ON tv.VehiclesBrand = tb.id WHERE tv.id=:vhid";
  $query = $dbh->prepare($sql);
  $query->bindParam(':vhid', $vhid, PDO::PARAM_INT); // Changed to PARAM_INT as vhid is int
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ); // Use fetch() as we expect only one record

  if ($result) { // Ensure there is vehicle data to display
  ?>

    <section class="page-header listing_page">
      <div class="container">
        <div class="page-header_wrap">
          <div class="page-heading">
            <h1><?php echo htmlentities($result->VehiclesTitle); ?></h1>
          </div>
          <ul class="coustom-breadcrumb">
            <li><a href="#">Home</a></li>
            <li><a href="bike-listing.php">Bike Market</a></li>
            <li><?php echo htmlentities($result->VehiclesTitle); ?></li>
          </ul>
        </div>
      </div>
      <!-- Dark Overlay-->
      <div class="dark-overlay"></div>
    </section>

    <!-- Listing Detail Section -->
    <section class="listing-detail">
      <div class="container">
        <div class="row">
          <div class="col-md-9">
            <!-- Main Image and Thumbnails -->
            <div class="listing_images">
              <?php if (!empty($result->Vimage1)) { ?>
                <img id="mainImage" src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1); ?>" alt="<?php echo htmlentities($result->VehiclesTitle); ?>">
              <?php } else { ?>
                <img id="mainImage" src="https://placehold.co/800x450/cccccc/333333?text=No+Image" alt="No Image">
              <?php } ?>

              <!-- Thumbnail Images (All 5 images shown as clickable thumbnails) -->
              <div class="row small-images">
                <?php if (!empty($result->Vimage1)) { ?>
                  <div class="col-sm-2 col-xs-4"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1); ?>" onclick="changeMainImage(this)" alt="Thumbnail 1"></div>
                <?php } ?>
                <?php if (!empty($result->Vimage2)) { ?>
                  <div class="col-sm-2 col-xs-4"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage2); ?>" onclick="changeMainImage(this)" alt="Thumbnail 2"></div>
                <?php } ?>
                <?php if (!empty($result->Vimage3)) { ?>
                  <div class="col-sm-2 col-xs-4"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage3); ?>" onclick="changeMainImage(this)" alt="Thumbnail 3"></div>
                <?php } ?>
                <?php if (!empty($result->Vimage4)) { ?>
                  <div class="col-sm-2 col-xs-4"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage4); ?>" onclick="changeMainImage(this)" alt="Thumbnail 4"></div>
                <?php } ?>
                <?php if (!empty($result->Vimage5)) { ?>
                  <div class="col-sm-2 col-xs-4"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage5); ?>" onclick="changeMainImage(this)" alt="Thumbnail 5"></div>
                <?php } ?>
              </div>
            </div>

            <!-- Main Features / Price / Basic Info -->
            <div class="main_features">
              <div class="row">
                <div class="col-md-8">
                  <h2><?php echo htmlentities($result->BrandName); ?>, <?php echo htmlentities($result->VehiclesTitle); ?></h2>
                  <p class="list-price">HKD $<?php echo htmlentities($result->PricePerDay); ?> <span style="font-size:0.7em; color:#666;">Price</span></p>
                </div>
                <div class="col-md-4 text-right">
                  <div class="share-buttons">
                    <!-- Social Share Buttons -->
                    <p>Share:
                      <a href="#"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
                      <a href="#"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
                      <a href="#"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
                      <a href="#"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>
                    </p>
                  </div>
                </div>
              </div>

              <div class="listing_other_info row">
                <!-- Reg. Year -->
                <div class="col-md-2 col-sm-4 col-xs-6 feature-box">
                  <svg fill="#616ae5" width="50px" height="50px" version="1.1" viewBox="144 144 512 512" xmlns="http://www.w3.org/2000/svg" stroke="#4932b8">
                    <g id="SVGRepo_bgCarrier" stroke-width="0" />
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />
                    <g id="SVGRepo_iconCarrier">
                      <path d="m169.09 316.03v-41.984c0.019531-16.699 6.6602-32.707 18.465-44.512 11.809-11.805 27.816-18.445 44.512-18.465h62.977v62.977c0 7.5 4 14.43 10.496 18.18 6.4922 3.75 14.496 3.75 20.992 0 6.4922-3.75 10.496-10.68 10.496-18.18v-62.977h125.95v62.977c0 7.5 4 14.43 10.496 18.18 6.4922 3.75 14.496 3.75 20.988 0 6.4961-3.75 10.496-10.68 10.496-18.18v-62.977h62.977c16.699 0.019532 32.707 6.6602 44.512 18.465 11.809 11.805 18.449 27.812 18.465 44.512v41.984zm461.82 41.984v209.92c-0.015625 16.699-6.6562 32.707-18.465 44.512-11.805 11.809-27.812 18.449-44.512 18.465h-335.87c-16.695-0.015625-32.703-6.6562-44.512-18.465-11.805-11.805-18.445-27.812-18.465-44.512v-209.92zm-251.9 146.94c0-5.5664-2.2109-10.906-6.1484-14.844s-9.2773-6.1484-14.844-6.1484h-83.969c-7.5 0-14.43 4.0039-18.18 10.496-3.75 6.4961-3.75 14.5 0 20.992 3.75 6.4961 10.68 10.496 18.18 10.496h83.969c5.5664 0.003906 10.906-2.207 14.844-6.1445s6.1484-9.2773 6.1484-14.848zm0-83.969v0.003907c0-5.5703-2.2109-10.91-6.1484-14.848s-9.2773-6.1484-14.844-6.1445h-83.969c-7.5 0-14.43 4-18.18 10.496-3.75 6.4922-3.75 14.496 0 20.992 3.75 6.4922 10.68 10.496 18.18 10.496h83.969c5.5664 0 10.906-2.2109 14.844-6.1484s6.1484-9.2773 6.1484-14.844zm167.94 83.969h-0.003906c0.003906-5.5664-2.207-10.906-6.1445-14.844s-9.2773-6.1484-14.848-6.1484h-83.965c-7.5 0-14.43 4.0039-18.18 10.496-3.75 6.4961-3.75 14.5 0 20.992 3.75 6.4961 10.68 10.496 18.18 10.496h83.969-0.003906c5.5703 0.003906 10.91-2.207 14.848-6.1445s6.1484-9.2773 6.1445-14.848zm0-83.969-0.003906 0.003907c0.003906-5.5703-2.207-10.91-6.1445-14.848s-9.2773-6.1484-14.848-6.1445h-83.965c-7.5 0-14.43 4-18.18 10.496-3.75 6.4922-3.75 14.496 0 20.992 3.75 6.4922 10.68 10.496 18.18 10.496h83.969-0.003906c5.5703 0 10.91-2.2109 14.848-6.1484s6.1484-9.2773 6.1445-14.844z" />
                    </g>
                  </svg>
                  <p><?php echo htmlentities($result->ModelYear); ?></p>
                  <span>Reg. Year</span>
                </div>
                <!-- Engine Displacement -->
                <div class="col-md-2 col-sm-4 col-xs-6 feature-box">
                  <svg fill="#616ae5" width="50px" height="50px" version="1.1" viewBox="144 144 512 512" xmlns="http://www.w3.org/2000/svg" stroke="#5990d9">
                    <g id="SVGRepo_bgCarrier" stroke-width="0" />
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />
                    <g id="SVGRepo_iconCarrier">
                      <g>
                        <path d="m311.47 384.88h-36.863v92.047h43.668l25.418 39.406h192.09l-0.003906-132.27h-43.668l-42.848-35.219h-105.56zm80.902 6.7305h35.887l-3.1406 26.465h28.559l-45.082 65.125-1.6445-40.902-21.387 0.078125z" />
                        <path d="m408.92 333.89v-25.688h37.242v-24.547h-102.45v24.547h37.242v25.688z" />
                        <path d="m210.82 476.92h23.176v-39.496h25.688v-27.969h-25.688v-24.547h-38.957v33.387h15.781z" />
                        <path d="m576.42 428.71h-25.688v27.969h25.688v49.344l28.543-8.9414v-93.82l-28.543-8.9414z" />
                      </g>
                    </g>
                  </svg>
                  <p><?php echo htmlentities($result->EngineDisplacement); ?> CC</p>
                  <span>Engine</span>
                </div>
                <!-- Bike Type -->
                <div class="col-md-2 col-sm-4 col-xs-6 feature-box">
                  <svg fill="#616ae5" width="50px" height="50px" version="1.1" viewBox="144 144 512 512" xmlns="http://www.w3.org/2000/svg" stroke="#616ae5">

                    <g id="SVGRepo_bgCarrier" stroke-width="0" />

                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />

                    <g id="SVGRepo_iconCarrier">
                      <path d="m534.35 416.79c-4.6602 0-9.2188 0.49219-13.613 1.3945l-9.7422-23.488c7.4766-1.9531 15.266-3.0977 23.355-3.0977h33.582l16.797-33.59-50.383-8.3984v-16.793h30.996l-64.578-83.961h-16.793l16.793 67.172h-67.176l-50.383 33.59h-50.383l-134.34-25.191v26.223l117.55 40.949 33.59 75.57-18.645 1.6914c-6.8711-29.801-33.438-52.074-65.32-52.074-37.121 0.003906-67.18 30.086-67.18 67.184 0 37.094 30.059 67.172 67.172 67.172 31.258 0 57.305-21.469 64.797-50.383h111.54v-16.793c0-33.98 18.398-63.602 45.742-79.641l9.7578 23.566c-18.234 12.004-30.309 32.605-30.309 56.074 0 37.098 30.062 67.176 67.176 67.176 37.098 0 67.176-30.078 67.176-67.172 0-37.098-30.078-67.18-67.176-67.18zm-239.77 83.973c-5.8242 10-16.531 16.793-28.93 16.793-18.566 0-33.59-15.035-33.59-33.582 0-18.555 15.02-33.594 33.586-33.594 14.297 0 26.434 8.9883 31.281 21.582l-39.676 3.6094v25.191zm239.77 16.789c-18.57 0-33.59-15.035-33.59-33.582 0-9.3672 3.8555-17.828 10.035-23.93l15.121 36.508 23.27-9.6445-15.121-36.484c0.10156 0 0.17969-0.035157 0.28125-0.035157 18.551 0 33.582 15.039 33.582 33.59 0.007813 18.547-15.027 33.578-33.578 33.578z" />
                    </g>

                  </svg>
                  <p><?php echo htmlentities($result->BikeType ?: 'N/A'); ?></p>
                  <span>Bike Type</span>
                </div>
                <!-- Transaction Count -->
                <div class="col-md-2 col-sm-4 col-xs-6 feature-box">
                  <svg fill="#616ae5" width="50px" height="50px" version="1.1" viewBox="144 144 512 512" xmlns="http://www.w3.org/2000/svg" stroke="#616ae5">

                    <g id="SVGRepo_bgCarrier" stroke-width="0" />

                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />

                    <g id="SVGRepo_iconCarrier">
                      <path d="m534.35 416.79c-4.6602 0-9.2188 0.49219-13.613 1.3945l-9.7422-23.488c7.4766-1.9531 15.266-3.0977 23.355-3.0977h33.582l16.797-33.59-50.383-8.3984v-16.793h30.996l-64.578-83.961h-16.793l16.793 67.172h-67.176l-50.383 33.59h-50.383l-134.34-25.191v26.223l117.55 40.949 33.59 75.57-18.645 1.6914c-6.8711-29.801-33.438-52.074-65.32-52.074-37.121 0.003906-67.18 30.086-67.18 67.184 0 37.094 30.059 67.172 67.172 67.172 31.258 0 57.305-21.469 64.797-50.383h111.54v-16.793c0-33.98 18.398-63.602 45.742-79.641l9.7578 23.566c-18.234 12.004-30.309 32.605-30.309 56.074 0 37.098 30.062 67.176 67.176 67.176 37.098 0 67.176-30.078 67.176-67.172 0-37.098-30.078-67.18-67.176-67.18zm-239.77 83.973c-5.8242 10-16.531 16.793-28.93 16.793-18.566 0-33.59-15.035-33.59-33.582 0-18.555 15.02-33.594 33.586-33.594 14.297 0 26.434 8.9883 31.281 21.582l-39.676 3.6094v25.191zm239.77 16.789c-18.57 0-33.59-15.035-33.59-33.582 0-9.3672 3.8555-17.828 10.035-23.93l15.121 36.508 23.27-9.6445-15.121-36.484c0.10156 0 0.17969-0.035157 0.28125-0.035157 18.551 0 33.582 15.039 33.582 33.59 0.007813 18.547-15.027 33.578-33.578 33.578z" />
                    </g>

                  </svg>
                  <p><?php echo htmlentities($result->TransactionCount); ?></p>
                  <span>Transactions</span>
                </div>
                <!-- Fuel Type -->
                <div class="col-md-2 col-sm-4 col-xs-6 feature-box">
                  <svg fill="#616ae5" width="50px" height="50px" version="1.1" viewBox="144 144 512 512" xmlns="http://www.w3.org/2000/svg">

                    <g id="SVGRepo_bgCarrier" stroke-width="0" />

                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />

                    <g id="SVGRepo_iconCarrier">
                      <g>
                        <path d="m274.05 299.24c0-13.91-25.191-50.383-25.191-50.383s-25.191 36.473-25.191 50.383c0 13.91 11.281 25.188 25.191 25.188 13.91 0 25.191-11.277 25.191-25.188z" />
                        <path d="m248.86 400c0 13.91 11.281 25.188 25.191 25.188s25.191-11.277 25.191-25.188-25.191-50.383-25.191-50.383-25.191 36.473-25.191 50.383z" />
                        <path d="m525.95 299.24h-50.379l-50.383-50.383-16.281 16.285-48.219-48.223c-11.902-11.895-27.711-18.441-44.531-18.441h-67.301v25.188h67.301c10.09 0 19.582 3.9297 26.715 11.066l48.219 48.219-16.281 16.289 50.379 50.379-50.379 50.383v151.14c0 27.824 22.555 50.383 50.379 50.383h125.95v-125.95l25.191-25.191v-100.76c0-27.824-22.555-50.379-50.383-50.379zm-25.188 277.09h-75.574c-13.91 0-25.188-11.281-25.188-25.191v-125.95h50.379c0 20.629 0 72.602-23.859 120.32l22.531 11.266c26.52-53.039 26.52-109.18 26.52-131.59 13.91 0 25.191 11.281 25.191 25.191z" />
                      </g>
                    </g>

                  </svg>
                  <p><?php echo htmlentities($result->FuelType); ?></p>
                  <span>Fuel Type</span>
                </div>
                <!-- Seats -->
                <div class="col-md-2 col-sm-4 col-xs-6 feature-box">
                  <svg fill="#616ae5" width="50px" height="50px" version="1.1" viewBox="144 144 512 512" xmlns="http://www.w3.org/2000/svg">

                    <g id="SVGRepo_bgCarrier" stroke-width="0" />

                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />

                    <g id="SVGRepo_iconCarrier">
                      <g>
                        <path d="m324.74 500.22c10.258-0.003906 19.996 4.5117 26.617 12.348 0.63281 0.70313 1.2227 1.4492 1.7617 2.2305 0.77344 1.0664 1.4727 2.1875 2.0938 3.3477 2.0742-1.6719 3.9688-3.5547 5.6523-5.6172 1.6797-2.0117 3.1523-4.1875 4.3945-6.4922 3.2031-6.0469 5.1133-12.691 5.6133-19.516 1.0625-14.125-2.6289-28.195-10.484-39.98-8.0586-12.758-15.254-26.039-21.547-39.754-10.746-23.332-18.484-47.93-23.031-73.211-0.8125-4.4648-1.6914-8.7578-2.6406-12.82-1.6016-7.0508-5.5508-13.348-11.203-17.855-5.6484-4.5117-12.664-6.9688-19.895-6.9688-1.457 0-2.7539 0.13281-4.1953 0.26953l-0.53906 0.035156c-1.3984 0.21875-2.7852 0.51562-4.1523 0.88672v-13.773c7.4727-0.11328 14.656-2.9023 20.242-7.8672 5.5859-4.9609 9.2031-11.766 10.191-19.176l4.9727-37.582v0.003907c1.0703-8.1797-1.1445-16.453-6.1602-23.004-5.0195-6.5508-12.43-10.844-20.605-11.938-1.3633-0.17969-2.7344-0.26953-4.1055-0.26953-7.543 0.019531-14.82 2.7734-20.488 7.75-5.668 4.9727-9.3438 11.832-10.344 19.309l-4.9766 37.582h0.003906c-1.0234 7.7891 0.93359 15.676 5.4805 22.082s11.348 10.859 19.039 12.465v17.008-0.003906c-8.0273 3.8516-14.398 10.473-17.941 18.641-3.543 8.1719-4.0195 17.344-1.3438 25.84l51.578 167.63c6.5898-7.4375 16.074-11.668 26.012-11.602z" />
                        <path d="m352.95 535.2c-0.12109-10-5.5234-19.188-14.203-24.156-8.6797-4.9648-19.34-4.9648-28.02 0-8.6797 4.9688-14.082 14.156-14.203 24.156 0.12109 10 5.5234 19.191 14.203 24.156 8.6797 4.9648 19.34 4.9648 28.02 0 8.6797-4.9648 14.082-14.156 14.203-24.156z" />
                        <path d="m557.52 520.28c-1.3594-10.387-6.793-19.809-15.105-26.184-8.3125-6.3789-18.824-9.1875-29.207-7.8047l-139.93 18.668c-0.61328 1.4766-1.3047 2.9219-2.0664 4.3281-1.4688 2.6836-3.1914 5.2227-5.1406 7.5781-1.9805 2.4023-4.1953 4.6016-6.6133 6.5664l-1.4531 1.1484c1.125 3.4297 1.6953 7.0156 1.6914 10.625-0.015624 9.2734-3.707 18.16-10.262 24.715-6.5547 6.5547-15.441 10.246-24.711 10.262-1.1523 0-2.2656-0.066407-3.3867-0.16797v14.344l210.95-0.003907 0.8125-0.54297c19.684-12.723 28.105-34.711 24.422-63.531z" />
                        <path d="m321.35 591.11v4.6992c0.007813 5.4805 2.1875 10.734 6.0625 14.605 3.875 3.875 9.1289 6.0547 14.605 6.0625h172c5.4766-0.015625 10.723-2.1953 14.59-6.0703 3.8672-3.875 6.0391-9.125 6.043-14.598v-4.918l-0.30469 0.20312z" />
                      </g>
                    </g>

                  </svg>
                  <p><?php echo htmlentities($result->SeatingCapacity); ?></p>
                  <span>Seats</span>
                </div>
              </div>
            </div>

            <!-- Vehicle Overview & Accessories Tabs -->
            <div class="listing_detail_wrap">
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#vehicle-overview" aria-controls="vehicle-overview" role="tab" data-toggle="tab">Vehicle Overview</a></li>
                <li role="presentation"><a href="#accessories" aria-controls="accessories" role="tab" data-toggle="tab">Accessories</a></li>
              </ul>

              <div class="tab-content">
                <!-- Vehicle Overview Tab -->
                <div role="tabpanel" class="tab-pane active" id="vehicle-overview">
                  <h3>Vehicle Overview</h3>
                  <p><?php echo nl2br(htmlentities($result->VehiclesOverview)); ?></p>
                </div>

                <!-- Accessories Tab -->
                <div role="tabpanel" class="tab-pane" id="accessories">
                  <h3>Accessories</h3>
                  <div class="row">
                    <div class="col-md-4">
                      <?php if ($result->AirConditioner == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Air Conditioner</p><?php } ?>
                      <?php if ($result->PowerDoorLocks == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Power Door Locks</p><?php } ?>
                      <?php if ($result->AntiLockBrakingSystem == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> AntiLock Braking System</p><?php } ?>
                      <?php if ($result->BrakeAssist == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Brake Assist</p><?php } ?>
                    </div>
                    <div class="col-md-4">
                      <?php if ($result->PowerSteering == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Power Steering</p><?php } ?>
                      <?php if ($result->DriverAirbag == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Driver Airbag</p><?php } ?>
                      <?php if ($result->PassengerAirbag == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Passenger Airbag</p><?php } ?>
                      <?php if ($result->PowerWindows == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Power Windows</p><?php } ?>
                    </div>
                    <div class="col-md-4">
                      <?php if ($result->CDPlayer == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> CD Player</p><?php } ?>
                      <?php if ($result->CentralLocking == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Central Locking</p><?php } ?>
                      <?php if ($result->CrashSensor == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Crash Sensor</p><?php } ?>
                      <?php if ($result->LeatherSeats == 1) { ?><p><i class="fa fa-check-circle" aria-hidden="true"></i> Leather Seats</p><?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar (Removed Booking Form) -->
          <div class="col-md-3">
            <!-- Removed the entire "Book Now" section as requested -->
            <div class="sidebar">
              <h5>Contact Seller</h5>
              <p>For inquiries about this bike, please contact the seller directly.</p>
              <p>Email: <a href="mailto:seller@example.com">seller@example.com</a></p>
              <p>Phone: +123-456-7890</p>
              <!-- You might want to implement a contact form here later -->
            </div>
          </div>
        </div>
      </div>
    </section>

  <?php } else { // If no vehicle data found 
  ?>
    <section class="page-header listing_page">
      <div class="container">
        <div class="page-header_wrap">
          <div class="page-heading">
            <h1>Bike Not Found</h1>
          </div>
          <ul class="coustom-breadcrumb">
            <li><a href="#">Home</a></li>
            <li><a href="bike-listing.php">Bike Market</a></li>
            <li>Error</li>
          </ul>
        </div>
      </div>
      <div class="dark-overlay"></div>
    </section>
    <section class="listing-detail">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center">
            <h3>Sorry, the bike you are looking for does not exist or is unavailable.</h3>
            <p><a href="bike-listing.php" class="btn btn-primary">Back to Bike Market</a></p>
          </div>
        </div>
      </div>
    </section>
  <?php } ?>

  <!--Footer -->
  <?php include('includes/footer.php'); ?>
  <!-- /Footer-->

  <!--Back to top-->
  <div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
  <!--/Back to top-->

  <!--Login-Form -->
  <?php include('includes/login.php'); ?>
  <!--/Login-Form -->

  <!--Register-Form -->
  <?php include('includes/registration.php'); ?>
  <!--/Register-Form -->

  <!--Forgot-password-Form -->
  <?php include('includes/forgotpassword.php'); ?>
  <!--/Forgot-password-Form -->

  <!-- Scripts -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/interface.js"></script>
  <!--Switcher-->
  <script src="assets/switcher/js/switcher.js"></script>
  <!--bootstrap-slider-JS-->
  <script src="assets/js/bootstrap-slider.min.js"></script>
  <!--Slider-JS-->
  <script src="assets/js/slick.min.js"></script>
  <script src="assets/js/owl.carousel.min.js"></script>

  <script>
    // JavaScript for changing main image on thumbnail click
    function changeMainImage(thumbnail) {
      document.getElementById('mainImage').src = thumbnail.src;
    }
  </script>

</body>

</html>