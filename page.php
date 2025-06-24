<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="">
<meta name="description" content="">
<title>Bike Rental Portal |
<?php
// 根据不同的页面类型显示不同的标题
if(isset($_GET['type']) && $_GET['type'] == 'blog') {
    echo "Blog Posts";
} else {
    $pagetype=$_GET['type'];
    $sql = "SELECT PageName from tblpages where type=:pagetype";
    $query = $dbh -> prepare($sql);
    $query->bindParam(':pagetype',$pagetype,PDO::PARAM_STR);
    $query->execute();
    $pageNameResult = $query->fetch(PDO::FETCH_OBJ);
    if($pageNameResult) {
        echo htmlentities($pageNameResult->PageName);
    } else {
        echo "Page Details"; // 默认标题
    }
}
?>
</title>
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

<style>
.blog-post-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* 小格之间的间距 */
    justify-content: flex-start; /* 或者 center */
}
.blog-post-item {
    border: 1px solid #ddd;
    padding: 15px;
    width: calc(33.33% - 20px); /* 3列布局，减去gap */
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    background-color: #fff;
    box-sizing: border-box; /* 包含padding和border在宽度内 */
    transition: transform 0.2s ease-in-out;
}
.blog-post-item:hover {
    transform: translateY(-5px);
}
.blog-post-item img {
    max-width: 100%;
    height: auto;
    margin-bottom: 10px;
}
.blog-post-item h4 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.2em;
}
.blog-post-item p {
    font-size: 0.9em;
    color: #666;
    line-height: 1.5;
    /* 限制显示行数 */
    display: -webkit-box;
    -webkit-line-clamp: 3; /* 显示3行 */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
.blog-post-item .read-more {
    display: block;
    margin-top: 10px;
    color: #007bff;
    text-decoration: none;
}
.blog-post-item .read-more:hover {
    text-decoration: underline;
}

/* 响应式调整 */
@media (max-width: 992px) {
    .blog-post-item {
        width: calc(50% - 20px); /* 2列布局 */
    }
}
@media (max-width: 768px) {
    .blog-post-item {
        width: calc(100% - 20px); /* 1列布局 */
    }
}
</style>

</head>
<body>
<?php include('includes/colorswitcher.php');?>
<?php include('includes/header.php');?>
<?php
$pagetype = $_GET['type'];

if ($pagetype == 'blog') {
    // 处理博客列表页
    ?>
    <section class="page-header aboutus_page">
      <div class="container">
        <div class="page-header_wrap">
          <div class="page-heading">
            <h1>Blog Posts</h1>
          </div>
          <ul class="coustom-breadcrumb">
            <li><a href="#">HomePage</a></li>
            <li>Blog</li>
          </ul>
        </div>
      </div>
      <div class="dark-overlay"></div>
    </section>

    <section class="about_us section-padding">
        <div class="container">
            <div class="section-header text-center">
                <h2>News Blog Posts</h2>
            </div>
            <div class="blog-post-grid">
                <?php
                $sql = "SELECT id, PostTitle, SUBSTRING(PostContent, 1, 200) AS PostExcerpt, PostImage, PostingDate FROM tblblogposts ORDER BY PostingDate DESC";
                $query = $dbh->prepare($sql);
                $query->execute();
                $blogPosts = $query->fetchAll(PDO::FETCH_OBJ);

                if ($query->rowCount() > 0) {
                    foreach ($blogPosts as $post) {
                        ?>
                        <div class="blog-post-item">
                            <?php if (!empty($post->PostImage)) { ?>
                                <img src="admin/img/blogimages/<?php echo htmlentities($post->PostImage); ?>" alt="<?php echo htmlentities($post->PostTitle); ?>">
                            <?php } ?>
                            <h4><?php echo htmlentities($post->PostTitle); ?></h4>
                            <p><?php echo htmlentities($post->PostExcerpt); ?>...</p>
                            <span class="post-date">release date: <?php echo date('Y-m-d', strtotime($post->PostingDate)); ?></span>
                            <a href="blog-details.php?id=<?php echo htmlentities($post->id); ?>" class="read-more">Read more <i class="fa fa-arrow-right"></i></a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-center'>There are no blog posts yet.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <?php
} else {
    // 现有逻辑，用于显示其他静态页面（如 About Us, Contact Us, 原来的 FAQs 等）
    $sql = "SELECT type,detail,PageName from tblpages where type=:pagetype";
    $query = $dbh -> prepare($sql);
    $query->bindParam(':pagetype',$pagetype,PDO::PARAM_STR);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    if($query->rowCount() > 0)
    {
    foreach($results as $result)
    { ?>
    <section class="page-header aboutus_page">
      <div class="container">
        <div class="page-header_wrap">
          <div class="page-heading">
            <h1><?php   echo htmlentities($result->PageName); ?></h1>
          </div>
          <ul class="coustom-breadcrumb">
            <li><a href="#">HomePage</a></li>
            <li><?php   echo htmlentities($result->PageName); ?></li>
          </ul>
        </div>
      </div>
      <div class="dark-overlay"></div>
    </section>
    <section class="about_us section-padding">
      <div class="container">
        <div class="section-header text-center">
          <h2><?php   echo htmlentities($result->PageName); ?></h2>
          <p><?php  echo nl2br(htmlentities($result->detail)); ?> </p>
        </div>
       <?php } } else {
           // 如果没有找到对应的静态页面
           echo "<section class='about_us section-padding'><div class='container'><p class='text-center'>页面未找到。</p></div></section>";
       }
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