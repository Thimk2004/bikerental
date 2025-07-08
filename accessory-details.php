<?php
session_start();
error_reporting(0); // Turn off all error reporting for production, consider E_ALL & ~E_NOTICE for development
include('includes/config.php'); // Include database configuration

// Get accessory ID from URL
$accid = intval($_GET['accid']);

// Fetch accessory details from database
$sql = "SELECT ta.*, tu.FullName, tu.EmailId as SellerEmail, tu.ContactNo as SellerPhone,
               tasc.subcategory_name as AccessoryCategoryName
        FROM tbl_accessories ta
        LEFT JOIN tblusers tu ON ta.user_id = tu.id
        LEFT JOIN tbl_accessory_category_map tacm ON ta.accessory_id = tacm.accessory_id
        LEFT JOIN tbl_accessory_subcategories tasc ON tacm.subcategory_id = tasc.subcategory_id
        WHERE ta.accessory_id = :accid AND ta.is_active = 1"; // Ensure only active accessories are shown
$query = $dbh->prepare($sql);
$query->bindParam(':accid', $accid, PDO::PARAM_INT);
$query->execute();
$accessory = $query->fetch(PDO::FETCH_OBJ); // Use fetch() as we expect only one record

?>

<!DOCTYPE HTML>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <title>Bike Rental Portal | Accessory Details</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
  <link rel="stylesheet" href="assets/css/styles.css" type="text/css">
  <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
  <link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
  <link href="assets/css/slick.css" rel="stylesheet">
  <link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
  <link href="assets/css/font-awesome.min.css" rel="stylesheet"> <link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
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
      margin-bottom: 15px;
    }

    .listing_other_info .feature-box {
      text-align: center;
      padding: 15px;
      border: 1px solid #eee;
      border-radius: 8px;
      margin-bottom: 10px;
      background-color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100px;
    }

    .listing_other_info .feature-box i.fa {
      font-size: 2em;
      color: #007bff;
      margin-bottom: 5px;
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

  <?php include('includes/colorswitcher.php'); ?>
  <?php include('includes/header.php'); ?>
  <?php

  if ($accessory) { // Ensure there is accessory data to display
  ?>

    <section class="page-header listing_page">
      <div class="container">
        <div class="page-header_wrap">
          <div class="page-heading">
            <h1><?php echo htmlentities($accessory->title); ?></h1>
          </div>
          <ul class="coustom-breadcrumb">
            <li><a href="#">Home</a></li>
            <li><a href="accessories.php">Accessories Market</a></li>
            <li><?php echo htmlentities($accessory->title); ?></li>
          </ul>
        </div>
      </div>
      <div class="dark-overlay"></div>
    </section>

    <section class="listing-detail">
      <div class="container">
        <div class="row">
          <div class="col-md-9">
            <div class="listing_images">
              <?php if (!empty($accessory->image_url1)) { ?>
                <img id="mainImage" src="admin/img/accessoryimages/<?php echo htmlentities($accessory->image_url1); ?>" alt="<?php echo htmlentities($accessory->title); ?>">
              <?php } else { ?>
                <img id="mainImage" src="https://placehold.co/800x450/cccccc/333333?text=No+Image" alt="No Image">
              <?php } ?>

              <div class="row small-images">
                <?php if (!empty($accessory->image_url1)) { ?>
                  <div class="col-sm-2 col-xs-4"><img src="admin/img/accessoryimages/<?php echo htmlentities($accessory->image_url1); ?>" onclick="changeMainImage(this)" alt="Thumbnail 1"></div>
                <?php } ?>
                <?php if (!empty($accessory->image_url2)) { ?>
                  <div class="col-sm-2 col-xs-4"><img src="admin/img/accessoryimages/<?php echo htmlentities($accessory->image_url2); ?>" onclick="changeMainImage(this)" alt="Thumbnail 2"></div>
                <?php } ?>
                <?php if (!empty($accessory->image_url3)) { ?>
                  <div class="col-sm-2 col-xs-4"><img src="admin/img/accessoryimages/<?php echo htmlentities($accessory->image_url3); ?>" onclick="changeMainImage(this)" alt="Thumbnail 3"></div>
                <?php } ?>
                <!-- Add more image placeholders if tbl_accessories has more image columns -->
              </div>
            </div>

            <div class="main_features">
              <div class="row">
                <div class="col-md-8">
                  <h2><?php echo htmlentities($accessory->title); ?></h2>
                  <p class="list-price">HKD $<?php echo htmlentities(number_format($accessory->price, 2)); ?> <span style="font-size:0.7em; color:#666;">Price</span></p>
                </div>
              </div>

              <div class="listing_other_info row">
                <div class="col-md-3 col-sm-4 col-xs-6 feature-box">
                  <i class="fa fa-tag" aria-hidden="true"></i>
                  <p><?php echo htmlentities($accessory->AccessoryCategoryName ?: 'N/A'); ?></p>
                  <span>Category</span>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-6 feature-box">
                  <i class="fa fa-check-circle" aria-hidden="true"></i>
                  <p><?php echo htmlentities($accessory->condition ?: 'N/A'); ?></p>
                  <span>Condition</span>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-6 feature-box">
                  <i class="fa fa-exchange" aria-hidden="true"></i>
                  <p><?php echo htmlentities($accessory->transaction_count ?: 0); ?></p>
                  <span>Transactions</span>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-6 feature-box">
                  <i class="fa fa-calendar" aria-hidden="true"></i>
                  <p><?php echo htmlentities(date('Y-m-d', strtotime($accessory->post_date))); ?></p>
                  <span>Posted Date</span>
                </div>
              </div>
            </div>

            <div class="listing_detail_wrap">
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#accessory-overview" aria-controls="accessory-overview" role="tab" data-toggle="tab">Accessory Overview</a></li>
                <li role="presentation"><a href="#seller-info" aria-controls="seller-info" role="tab" data-toggle="tab">Seller Info</a></li>
                <li role="presentation"><a href="#contacts-info" aria-controls="contacts-info" role="tab" data-toggle="tab">Contacts Info</a></li>
              </ul>

              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="accessory-overview">
                  <h3>Accessory Overview</h3>
                  <p><?php echo nl2br(htmlentities($accessory->description)); ?></p>
                </div>

                <div role="tabpanel" class="tab-pane" id="seller-info">
                  <h3>Seller Info</h3>
                  <?php if ($accessory->user_id) { ?>
                    <p><strong>Seller Name:</strong> <?php echo htmlentities($accessory->FullName ?: 'N/A'); ?></p>
                    <p><strong>Seller Email:</strong> <?php echo htmlentities($accessory->SellerEmail ?: 'N/A'); ?></p>
                    <p><strong>Seller Phone:</strong> <?php echo htmlentities($accessory->SellerPhone ?: 'N/A'); ?></p>
                  <?php } else { ?>
                    <p>Seller information is not available.</p>
                  <?php } ?>
                </div>

                <div role="tabpanel" class="tab-pane" id="contacts-info">
                  <h3>Contacts Info</h3>
                  <?php
                  $sellerUserId = $accessory->user_id; // Get seller's UserId from tbl_accessories
                  $accessoryId = $accessory->accessory_id; // Get accessory ID

                  // Fetch general user contacts (from tbluser_contacts)
                  if ($sellerUserId) {
                    $sql_user_contacts = "SELECT ContactType, ContactValue, Description FROM tbluser_contacts WHERE UserId = :userid";
                    $query_user_contacts = $dbh->prepare($sql_user_contacts);
                    $query_user_contacts->bindParam(':userid', $sellerUserId, PDO::PARAM_INT);
                    $query_user_contacts->execute();
                    $user_contacts = $query_user_contacts->fetchAll(PDO::FETCH_OBJ);

                    if ($user_contacts) {
                      echo "<h4>Seller's General Contact Methods:</h4>";
                      echo "<ul>";
                      foreach ($user_contacts as $contact) {
                        echo "<li><strong>" . htmlentities($contact->ContactType) . ":</strong> " . htmlentities($contact->ContactValue);
                        if (!empty($contact->Description)) {
                          echo " (" . htmlentities($contact->Description) . ")";
                        }
                        echo "</li>";
                      }
                      echo "</ul>";
                    } else {
                      echo "<p>No general contact information available for this seller.</p>";
                    }
                  }

                  // Fetch accessory-specific contacts (from tbl_accessory_contacts)
                  if ($accessoryId) {
                    $sql_accessory_contacts = "SELECT contact_type, contact_value, description FROM tbl_accessory_contacts WHERE accessory_id = :accessoryid";
                    $query_accessory_contacts = $dbh->prepare($sql_accessory_contacts);
                    $query_accessory_contacts->bindParam(':accessoryid', $accessoryId, PDO::PARAM_INT);
                    $query_accessory_contacts->execute();
                    $accessory_specific_contacts = $query_accessory_contacts->fetchAll(PDO::FETCH_OBJ);

                    if ($accessory_specific_contacts) {
                      echo "<h4>Accessory Specific Contact Methods:</h4>";
                      echo "<ul>";
                      foreach ($accessory_specific_contacts as $contact) {
                        echo "<li><strong>" . htmlentities($contact->contact_type) . ":</strong> " . htmlentities($contact->contact_value);
                        if (!empty($contact->description)) {
                          echo " (" . htmlentities($contact->description) . ")";
                        }
                        echo "</li>";
                      }
                      echo "</ul>";
                    } else {
                      echo "<p>No specific contact information available for this accessory.</p>";
                    }
                  }

                  if (!$sellerUserId && !$accessoryId) {
                    echo "<p>Contact information is not available.</p>";
                  }
                  ?>
                </div>
              </div>
            </div>

          </div>
          <!-- Right sidebar - you can add a contact form here if needed -->
          <div class="col-md-3">
            <div class="sidebar">
              <h5>Inquire About This Accessory</h5>
              <p>Contact the seller directly for more details.</p>
              <!-- You can add a contact form here, similar to the booking form in vehical-details.php -->
              <!-- For example: -->
              <!-- <form method="post">
                <div class="form-group">
                  <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                  <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                  <textarea name="message" class="form-control" placeholder="Your Message" rows="3" required></textarea>
                </div>
                <button type="submit" name="inquire" class="btn btn-primary btn-block">Send Inquiry</button>
              </form> -->
            </div>
          </div>
        </div>
    </section>

  <?php } else { // If no accessory data found
  ?>
    <section class="page-header listing_page">
      <div class="container">
        <div class="page-header_wrap">
          <div class="page-heading">
            <h1>Accessory Not Found</h1>
          </div>
          <ul class="coustom-breadcrumb">
            <li><a href="#">Home</a></li>
            <li><a href="accessories.php">Accessories Market</a></li>
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
            <h3>Sorry, the accessory you are looking for does not exist or is unavailable.</h3>
            <p><a href="accessories.php" class="btn btn-primary">Back to Accessories Market</a></p>
          </div>
        </div>
      </div>
    </section>
  <?php } ?>

  <?php include('includes/footer.php'); ?>
  <div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
  <?php include('includes/login.php'); ?>
  <?php include('includes/registration.php'); ?>
  <?php include('includes/forgotpassword.php'); ?>
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/interface.js"></script>
  <script src="assets/switcher/js/switcher.js"></script>
  <script src="assets/js/bootstrap-slider.min.js"></script>
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
