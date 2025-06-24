<?php
session_start();
error_reporting(0);
include('includes/config.php');

$postid = intval($_GET['id']); // 获取文章ID，并确保是整数

if(empty($postid)) {
    header('location:page.php?type=blog'); // 如果没有ID，重定向回博客列表
    exit;
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
<title>Bike Rental Portal | Blog Post</title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/styles.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<link href="assets/css/slick.css" rel="stylesheet">
<link href="assets/css/bootstrap-slider.min.js" rel="stylesheet">
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
<?php
$sql = "SELECT PostTitle, PostContent, PostImage, Author, PostingDate FROM tblblogposts WHERE id = :postid";
$query = $dbh->prepare($sql);
$query->bindParam(':postid', $postid, PDO::PARAM_INT);
$query->execute();
$post = $query->fetch(PDO::FETCH_OBJ);

if($post) {
?>
<section class="page-header aboutus_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1><?php echo htmlentities($post->PostTitle); ?></h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Homepage</a></li>
        <li><a href="page.php?type=blog">Blog</a></li>
        <li><?php echo htmlentities($post->PostTitle); ?></li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>

<section class="about_us section-padding">
  <div class="container">
    <div class="section-header text-center">
      <h2><?php echo htmlentities($post->PostTitle); ?></h2>
      <p class="post-meta">
          Post Date: <?php echo date('Y-m-d H:i', strtotime($post->PostingDate)); ?>
          <?php if(!empty($post->Author)) { ?>
          &nbsp; | &nbsp; Author: <?php echo htmlentities($post->Author); ?>
          <?php } ?>
      </p>
    </div>
    <div class="blog-content">
        <?php if (!empty($post->PostImage)) { ?>
            <img src="admin/img/blogimages/<?php echo htmlentities($post->PostImage); ?>" class="img-responsive center-block" alt="<?php echo htmlentities($post->PostTitle); ?>" style="max-width: 80%; margin-bottom: 20px;">
        <?php } ?>
        <p><?php echo nl2br(htmlentities($post->PostContent)); ?></p>
    </div>
  </div>
</section>
<?php
} else {
    echo "<section class='about_us section-padding'><div class='container'><p class='text-center'>Sorry, that blog post was not found.</p></div></section>";
}
?>

<?php include('includes/footer.php');?>
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<?php include('includes/login.php');?>
<?php include('includes/registration.php');?>

<?php include('includes/forgotpassword.php');?>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/interface.js"></script>
<script src="assets/switcher/js/switcher.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script>
<script src="assets/js/slick.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>

</body>
</html>