<?php
session_start();
error_reporting(0); // Turn off all error reporting for production, recommended to enable for development.
include('includes/config.php');

// Redirect to index if user is not logged in
if(strlen($_SESSION['login'])==0) {
    header('location:index.php');
    exit(); // Terminate script execution after redirect
} else {
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="">
<meta name="description" content="">
<title>BikeForYou | My Posts</title> <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
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
</head>
<body>

<?php include('includes/colorswitcher.php');?>
<?php include('includes/header.php');?>
<section class="page-header profile_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>My Posts</h1> </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Home</a></li>
        <li>My Posts</li> </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>
<?php
$useremail=$_SESSION['login'];
$sql = "SELECT * from tblusers where EmailId=:useremail";
$query = $dbh -> prepare($sql);
$query -> bindParam(':useremail',$useremail, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>
<section class="user_profile inner_pages">
  <div class="container">
    <div class="user_profile_info gray-bg padding_4x4_40">
      <div class="upload_user_logo"> <img src="assets/images/dealer-logo.jpg" alt="image"> </div>

      <div class="dealer_info">
        <h5><?php echo htmlentities($result->FullName);?></h5>
        <p><?php echo htmlentities($result->Address);?><br>
          <?php echo htmlentities($result->City);?>&nbsp;<?php echo htmlentities($result->Country); }}?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 col-sm-3">
       <?php include('includes/sidebar.php');?> <div class="col-md-6 col-sm-8">
        <div class="profile_wrap">
          <h5 class="uppercase underline">My Posted Motorcycles</h5> <div class="my_vehicles_list">
            <ul class="vehicle_listing">
<?php
$useremail=$_SESSION['login'];
// Query to fetch vehicles posted by the current user
$sql_posts = "SELECT tblvehicles.Vimage1 as Vimage1, tblvehicles.VehiclesTitle, tblvehicles.id as vid, tblbrands.BrandName, tblvehicles.PricePerDay, tblvehicles.RegDate
              FROM tblvehicles
              JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand
              WHERE tblvehicles.UserId = (SELECT id FROM tblusers WHERE EmailId = :useremail)"; // Filter by UserId of the logged-in user
$query_posts = $dbh -> prepare($sql_posts);
$query_posts-> bindParam(':useremail', $useremail, PDO::PARAM_STR);
$query_posts->execute();
$results_posts = $query_posts->fetchAll(PDO::FETCH_OBJ); // Use a distinct variable name

if($query_posts->rowCount() > 0)
{
    foreach($results_posts as $post) // Loop through each posted vehicle
    {
?>
<li>
    <div class="vehicle_img">
        <a href="bike-details.php?vhid=<?php echo htmlentities($post->vid);?>"> <img src="admin/img/vehicleimages/<?php echo htmlentities($post->Vimage1);?>" alt="image"> </a>
    </div>
    <div class="vehicle_title">
        <h6><a href="bike-details.php?vhid=<?php echo htmlentities($post->vid);?>"> <?php echo htmlentities($post->BrandName);?> , <?php echo htmlentities($post->VehiclesTitle);?></a></h6>
        <p><b>Price:</b> HKD <?php echo htmlentities($post->PricePerDay);?></p>
        <p><b>Posted On:</b> <?php echo htmlentities($post->RegDate);?></p>
    </div>
    <div class="vehicle_actions">
        <a href="edit-my-post.php?id=<?php echo htmlentities($post->vid);?>" class="btn btn-primary btn-xs">Edit Post</a>
    </div>
</li>
<?php
    }
} else {
?>
    <p>You have not posted any motorcycles yet.</p>
<?php
}
?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include('includes/footer.php');?>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/interface.js"></script>
<script src="assets/switcher/js/switcher.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script>
<script src="assets/js/slick.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
</body>
</html>
<?php } ?>
