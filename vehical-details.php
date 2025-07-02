<?php
session_start();
error_reporting(0); // 關閉所有錯誤報告，正式環境建議開啟或設定為 E_ALL & ~E_NOTICE
include('includes/config.php'); // 包含數據庫配置

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
  <link href="assets/css/font-awesome.min.css" rel="stylesheet"> <!-- 確保 Font Awesome CSS 引用已啟用 -->

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

    /* Styles for the SVG icons */
    .listing_other_info .feature-box svg.feature-icon {
      width: 40px;
      /* Adjust size as needed */
      height: 40px;
      /* Adjust size as needed */
      margin-bottom: 5px;
      fill: #007bff;
      /* Set SVG fill color to match your theme */
    }

    /* Styles for Font Awesome icons */
    .listing_other_info .feature-box i.fa {
      font-size: 2em;
      /* Keep Font Awesome icon size */
      color: #007bff;
      /* Keep Font Awesome icon color */
      margin-bottom: 5px;
      /* Ensure no background image interferes */
      background-image: none;
      width: auto;
      height: auto;
      display: inline-block;
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
                  <svg fill="#616ae5" width="50px" height="50px" version="1.1" viewBox="144 144 512 512" xmlns="http://www.w3.org/2000/svg" fill="#616ae5">

                    <g id="SVGRepo_bgCarrier" stroke-width="0" />

                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />

                    <g id="SVGRepo_iconCarrier">
                      <defs>
                        <clipPath id="a">
                          <path d="m148.09 161h503.81v478h-503.81z" />
                        </clipPath>
                      </defs>
                      <g clip-path="url(#a)">
                        <path d="m152.03 460.99 55.477 32.027c1.8047 1.0469 3.957 1.3281 5.9727 0.78516 2.0156-0.53906 3.7344-1.8594 4.7773-3.668l2-3.4609 32.129 18.809c-0.76172 2.7344-1.1445 5.5547-1.1445 8.3906-0.03125 8.2227 3.2266 16.117 9.0508 21.922 5.8125 5.8164 13.699 9.082 21.922 9.0664 0.59766 0 1.1953-0.023437 1.793-0.058594-0.035156 0.58984-0.054687 1.1797-0.054687 1.7812-0.015625 8.2227 3.2422 16.109 9.0547 21.926 5.8125 5.8125 13.699 9.0703 21.922 9.0508 0.60156 0 1.1992-0.023438 1.793-0.054688-0.035156 0.59375-0.054687 1.1914-0.054687 1.793-0.019531 8.2188 3.2383 16.109 9.0508 21.922s13.703 9.0703 21.922 9.0547c2.1797 0 4.3555-0.22656 6.4883-0.67969 0.98828 6.0742 4.1094 11.594 8.8008 15.574 4.6914 3.9805 10.648 6.1602 16.801 6.1445 3.1133 0.003906 6.1992-0.55078 9.1172-1.6367l1.5625 1.5625c4.5195 4.5273 10.555 7.2266 16.941 7.5781 6.3906 0.34766 12.684-1.6719 17.672-5.6797 4.9883-4.0078 8.3203-9.7148 9.3555-16.027 2.1328 0.45312 4.3047 0.68359 6.4844 0.6875 8.5352 0.011719 16.691-3.5 22.547-9.707 5.8594-6.207 8.8906-14.555 8.3867-23.074 0.58984 0.035156 1.1797 0.054687 1.7812 0.054687 8.5312 0.015626 16.691-3.4922 22.551-9.6953 5.8555-6.207 8.8906-14.555 8.3828-23.07 0.58984 0.035156 1.1797 0.054687 1.7812 0.054687 7.6055-0.007813 14.941-2.8125 20.617-7.8789 5.6719-5.0625 9.2891-12.039 10.156-19.594 0.86328-7.5547-1.0742-15.168-5.4531-21.387l43.426-24.969 6.6992 11.602c2.1719 3.7656 6.9883 5.0547 10.754 2.8828l55.477-32.023v-0.003907c3.7617-2.1719 5.0547-6.9883 2.8789-10.754l-72.758-126.02c-2.1719-3.7617-6.9883-5.0547-10.75-2.8789l-55.477 32.027c-3.7656 2.1758-5.0547 6.9883-2.8828 10.754l6.4336 11.145-9.1953 3.4805c-4.2188 1.6914-8.9102 1.7852-13.195 0.26953l-61.316-20.438v-0.003907c-6.875-2.3516-14.406-1.8359-20.895 1.4258l-12.203 6.0156c-0.11328-0.070313-0.22656-0.14453-0.34766-0.20703l-14.535-7.6758c-7.9492-4.2617-17.492-4.3203-25.492-0.15625l-19.402 9.9375-0.003906 0.003906c-1.4688 0.82031-3.1211 1.2617-4.8086 1.2852l-46.25 0.53125 3.2422-5.6172c2.1758-3.7656 0.88672-8.5781-2.8789-10.754l-55.473-32.027v0.003907c-3.7656-2.1758-8.5781-0.88281-10.754 2.8789l-72.758 126.02c-2.1719 3.7656-0.88281 8.582 2.8828 10.754zm119.39 63.676c-2.8477-2.8711-4.4414-6.75-4.4414-10.789 0-4.043 1.5938-7.9219 4.4414-10.793l27.684-27.684v0.003906c3.8594-3.8359 9.4648-5.3242 14.719-3.9102 5.2539 1.4141 9.3555 5.5195 10.77 10.77 1.4141 5.2539-0.078125 10.863-3.9102 14.723l-27.68 27.68c-2.8672 2.8555-6.7461 4.4609-10.793 4.4609-4.043 0-7.9258-1.6055-10.789-4.4609zm32.715 32.715c-2.8438-2.8711-4.4414-6.7461-4.4453-10.789 0-4.0391 1.5938-7.918 4.4375-10.789l0.007813-0.007812 27.684-27.68h-0.003907c3.8672-3.8047 9.4609-5.2734 14.695-3.8555 5.2383 1.4141 9.3281 5.5039 10.746 10.742 1.4141 5.2344-0.054688 10.832-3.8594 14.695l-27.684 27.684c-2.8633 2.8555-6.7422 4.4609-10.789 4.4609-4.0469 0-7.9258-1.6055-10.789-4.4609zm32.715 32.715-0.003907-0.003906c-2.8555-2.8633-4.4609-6.7461-4.4609-10.789 0-4.0469 1.6055-7.9297 4.4609-10.793l27.68-27.68h0.003906c3.8555-3.8555 9.4727-5.3594 14.738-3.9492 5.2656 1.4102 9.3789 5.5234 10.793 10.789 1.4102 5.2656-0.097656 10.887-3.9531 14.742-0.26172 0.26562-0.50781 0.55469-0.73047 0.85547-0.82422 0.64453-1.6133 1.3398-2.3555 2.0781l-21.66 21.656v0.003906c-0.73828 0.74219-1.4336 1.5312-2.082 2.3594-0.30078 0.22266-0.58594 0.46484-0.85156 0.73047-2.8633 2.8555-6.7461 4.4609-10.789 4.4609-4.0469 0-7.9297-1.6055-10.793-4.4648zm35.652 22.508-0.003906-0.003906c-3.9922-3.9922-3.9922-10.469 0-14.465l21.656-21.656c3.9961-3.9961 10.473-3.9961 14.465 0 3.9961 3.9961 3.9961 10.469 0 14.465l-21.656 21.656c-4.0078 3.9688-10.461 3.9688-14.465 0zm43.5 7.5117-0.003906-0.003906c-3.9609 3.9492-10.359 3.9883-14.363 0.085937l14.453-14.453c3.9023 4.0078 3.8633 10.406-0.089844 14.367zm35.652-22.508v-0.003907c-2.8672 2.8594-6.7461 4.4609-10.793 4.4609-4.043 0-7.9219-1.6016-10.789-4.4609-0.26562-0.26172-0.55469-0.50781-0.85547-0.73047-0.64453-0.82812-1.3398-1.6133-2.0781-2.3555l-1.5078-1.5078c2.8047-7.2812 2.1797-15.441-1.7031-22.211-3.8789-6.7695-10.602-11.434-18.301-12.695 0.85938-4.0469 0.90625-8.2266 0.14063-12.293 2.8945-1.6094 6.2383-2.2344 9.5234-1.7773s6.332 1.9688 8.6797 4.3086l27.684 27.684c2.8555 2.8633 4.4609 6.7422 4.4609 10.789s-1.6055 7.9258-4.4609 10.789zm32.715-32.715h-0.003906c-2.8672 2.8438-6.7461 4.4375-10.789 4.4375-4.0391 0-7.918-1.5938-10.789-4.4375l-27.684-27.684c-3.7383-3.875-5.1602-9.4336-3.7383-14.629 1.4258-5.1953 5.4805-9.2539 10.672-10.684 5.1953-1.4258 10.754-0.011719 14.633 3.7227v0.007812l27.684 27.68c2.8594 2.8633 4.4648 6.7461 4.4688 10.789 0 4.0469-1.6016 7.9297-4.4609 10.797zm32.711-32.715c-2.8633 2.8555-6.7461 4.4609-10.789 4.4609-4.0469 0-7.9258-1.6055-10.789-4.4609l-27.684-27.684c-3.8086-3.8633-5.2773-9.457-3.8594-14.695 1.418-5.2344 5.5078-9.3242 10.742-10.742 5.2383-1.418 10.832 0.050781 14.695 3.8594l27.68 27.688c2.8438 2.8711 4.4414 6.7461 4.4414 10.789s-1.5977 7.918-4.4414 10.789zm51.312-193.27 64.887 112.38-41.84 24.156-64.891-112.39zm-150.62 35.215c2.7656-1.4492 6.0156-1.6719 8.9531-0.61328l61.32 20.441c7.7227 2.6484 16.137 2.4805 23.75-0.48047l11.598-4.3945 43.777 75.824-46.68 26.84-19.957-19.957h-0.003906c-2.1133-2.1133-4.5195-3.9102-7.1523-5.332l-62.754-47.047c-3.9883-2.9844-8.75-4.7617-13.719-5.1172l-9.2227-0.8125h0.003906c-5.1641-0.49609-10.363 0.49219-14.98 2.8516-4.6211 2.3594-8.4727 5.9883-11.098 10.461l-12.223 20.102v0.003906c-0.37109 0.69531-1.1523 1.0703-1.9258 0.92578l-18.598-2.3438v0.003906c-0.57031-0.074218-1.0781-0.38672-1.3945-0.86719-0.31641-0.47656-0.41016-1.0664-0.25391-1.6211l9.7422-34.539c0.83984-3.2578 3.0938-5.9648 6.1445-7.3828zm-83.57 10.824v-0.003906c4.1172-0.042968 8.168-1.0781 11.805-3.0156l19.402-9.9375c3.4297-1.8477 7.5664-1.8242 10.973 0.066407l4.8125 2.5391-25.059 12.352c-7.0625 3.3828-12.297 9.6719-14.336 17.23l-9.7422 34.539c-1.3984 4.9648-0.56641 10.293 2.2812 14.594 2.8516 4.3008 7.4336 7.1445 12.551 7.7891l18.598 2.3398c3.3945 0.45703 6.8438-0.09375 9.9297-1.5781 3.082-1.4883 5.6602-3.8477 7.418-6.7852l12.227-20.102h-0.003906c2.2695-3.9883 6.6758-6.2656 11.242-5.8125l9.2188 0.8125c2.0469 0.10156 4.0156 0.80859 5.6602 2.0312l44.641 33.465h-0.003906c-5.4297 2.4531-10.035 6.4258-13.262 11.438-3.2266 5.0078-4.9375 10.844-4.9258 16.805 0 0.59766 0.023438 1.1914 0.054688 1.7812-8.5156-0.50781-16.863 2.5234-23.07 8.3828-6.2031 5.8555-9.7148 14.016-9.6953 22.551 0 0.59766 0.023437 1.1914 0.054687 1.7812-5.543-0.32422-11.066 0.84375-16.004 3.3789-0.54688-0.64453-1.1172-1.2773-1.7227-1.8828-6.2539-6.2539-14.887-9.5352-23.715-9.0078 0.035156-0.58984 0.054688-1.1797 0.054688-1.7812h-0.003907c0.019531-8.5352-3.4844-16.695-9.6875-22.555-6.1992-5.8594-14.547-8.8945-23.062-8.3906 0.50391-8.5156-2.5312-16.859-8.3867-23.062-5.8594-6.2031-14.016-9.7109-22.547-9.6953-8.2227-0.03125-16.117 3.2305-21.922 9.0547l-27.633 27.641-32.215-18.863 50.492-87.457zm-102.6-46.039 41.84 24.156-64.887 112.39-41.84-24.156zm168.39 6.7656c24.465 0 47.93-9.7188 65.23-27.02 17.301-17.301 27.02-40.766 27.02-65.23 0-24.469-9.7188-47.934-27.02-65.234s-40.766-27.02-65.23-27.02-47.93 9.7188-65.23 27.02-27.02 40.766-27.02 65.23c0.027344 24.461 9.7539 47.906 27.047 65.203 17.297 17.293 40.742 27.023 65.203 27.051zm0-168.76c20.289 0 39.75 8.0586 54.098 22.406 14.348 14.352 22.41 33.809 22.41 54.102 0 20.289-8.0625 39.75-22.41 54.098-14.348 14.348-33.809 22.41-54.098 22.41-20.293 0-39.75-8.0625-54.102-22.41-14.348-14.348-22.406-33.809-22.406-54.098 0.023437-20.285 8.0898-39.73 22.434-54.074s33.789-22.41 54.074-22.434zm-35.02 76.875c-1.2266-1.6875-1.7344-3.793-1.4062-5.8555 0.32422-2.0625 1.457-3.9102 3.1445-5.1406 3.5195-2.5547 8.4414-1.7773 10.996 1.7422l12.051 16.582 40.109-32.859c3.3672-2.7188 8.2969-2.2148 11.039 1.1328 2.7422 3.3477 2.2695 8.2812-1.0625 11.047l-46.562 38.145c-1.6758 1.3711-3.8438 1.9922-5.9922 1.7188-2.1523-0.27734-4.0898-1.4297-5.3633-3.1797z" />
                      </g>
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