<?php
session_start();
error_reporting(0); // 關閉所有錯誤報告，建議在開發階段開啟。
include('includes/config.php'); // 包含資料庫設定檔

// 如果使用者未登入，則重定向到首頁
if(strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit(); // 重定向後終止腳本執行
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
<title>BikeForYou | My Accessories</title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/styles.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<link href="assets/css/slick.css" rel="stylesheet">
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<link href="assets/css/font-awesome.min.css" rel="stylesheet">

<!-- 切換器 CSS -->
<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />

<!-- Fav and touch icons -->
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
        <h1>My Accessories</h1>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Home</a></li>
        <li>My Accessories</li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>

<?php
$useremail = $_SESSION['login'];
// 查詢使用者資訊
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
      <div class="upload_user_logo"> <img src="assets/images/dealer-logo.jpg" alt="User Image"> </div>

      <div class="dealer_info">
        <h5><?php echo htmlentities($result->FullName);?></h5>
        <p><?php echo htmlentities($result->Address);?><br>
          <?php echo htmlentities($result->City);?>&nbsp;<?php echo htmlentities($result->Country); }}?></p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 col-sm-3">
       <?php include('includes/sidebar.php');?>
      <div class="col-md-6 col-sm-8"> <!-- 這是我修正的開始 -->
        <div class="profile_wrap">
          <h5 class="uppercase underline">My Posted Accessories</h5>
          <div class="my_vehicles_list">
            <ul class="vehicle_listing">
<?php
$useremail = $_SESSION['login'];
// 查詢當前使用者發布的配件
$sql_accessories = "SELECT ta.accessory_id as aid, ta.title, ta.price, ta.condition, ta.image_url1, ta.post_date
                    FROM tbl_accessories ta
                    JOIN tblusers tu ON tu.id = ta.user_id
                    WHERE tu.EmailId = :useremail";
$query_accessories = $dbh -> prepare($sql_accessories);
$query_accessories-> bindParam(':useremail', $useremail, PDO::PARAM_STR);
$query_accessories->execute();
$results_accessories = $query_accessories->fetchAll(PDO::FETCH_OBJ);

if($query_accessories->rowCount() > 0)
{
    foreach($results_accessories as $accessory)
    {
?>
<li>
    <div class="vehicle_img">
        <a href="accessory-details.php?acid=<?php echo htmlentities($accessory->aid);?>">
            <img src="admin/img/accessoryimages/<?php echo htmlentities($accessory->image_url1);?>" alt="Accessory Image">
        </a>
    </div>
    <div class="vehicle_title">
        <h6><a href="accessory-details.php?acid=<?php echo htmlentities($accessory->aid);?>"> <?php echo htmlentities($accessory->title);?></a></h6>
        <p><b>Price:</b> HKD <?php echo htmlentities($accessory->price);?></p>
        <p><b>Condition:</b> <?php echo htmlentities($accessory->condition);?></p>
        <p><b>Posted On:</b> <?php echo htmlentities($accessory->post_date);?></p>
    </div>
    <div class="vehicle_actions">
        <a href="edit-my-accessory.php?id=<?php echo htmlentities($accessory->aid);?>" class="btn btn-primary btn-xs">Edit Accessory</a>
    </div>
</li>
<?php
    }
} else {
?>
    <p>You have not posted any accessories yet.</p>
<?php
}
?>
            </ul>
          </div>
        </div>
      </div> <!-- 這是我修正的結束 -->
    </div>
  </div>
</section>

<?php include('includes/footer.php');?>

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

</body>
</html>
<?php } ?>
