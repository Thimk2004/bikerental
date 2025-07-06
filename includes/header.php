<?php // 確保這裡有 <?php 開頭，以便 session_start() 能正常運行
// 雖然在 bike-listing.php 中已經有 session_start()，但在 header.php 中重複加上是常見且安全的做法，
// 以防 header.php 被其他沒有 session_start() 的頁面單獨包含。
// 但由於您已經在 bike-listing.php 開頭添加了，這裡可以選擇不重複添加。
// 如果您遇到 session 相關問題，可以考慮在此處也加上 session_start();
// session_start(); // 如果需要，可以取消註解此行
?>
<header>
  <div class="default-header">
    <div class="container">
      <div class="row header-top-row"> <!-- 新增 custom class 'header-top-row' 用於 CSS 定位 -->
        <div class="col-sm-3 col-md-2 logo-column"> <!-- Logo 欄位 -->
          <div class="logo"> <a href="index.php"><img src="assets/images/logg2.png" alt="image" /></a> </div>
        </div>
        <div class="col-sm-6 col-md-8 slogan-column"> <!-- 口號欄位 -->
          <div class="site-slogan">Ride Smart, Buy Pre-Loved.</div>
        </div>
        <div class="col-sm-3 col-md-2 buttons-column"> <!-- 按鈕欄位 -->
          <div class="header_info"> <!-- header_info 現在只包含按鈕組 -->
            <!-- 舊的 header_widgets 區塊已移除 -->

            <div class="header-buttons-group"> <!-- 按鈕組容器 -->
              <!-- 「Post Your Bike」按鈕 -->
              <div class="post-bike-btn">
                <?php if (isset($_SESSION['login']) && strlen($_SESSION['login']) > 0) { ?>
                  <!-- 如果已登入，連結到發布電單車頁面 -->
                  <a href="post-bike.php" class="btn btn-xs uppercase">Post Your Bike</a> <!-- 連結已修改為 post-bike.php -->
                <?php } else { ?>
                  <!-- 如果未登入，點擊按鈕彈出登入/註冊模態框 -->
                  <a href="#loginform" class="btn btn-xs uppercase" data-toggle="modal" data-dismiss="modal">Post Your Bike</a>
                <?php } ?>
              </div>

              <?php
              // 登入/註冊或歡迎訊息
              if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
              ?>
                <div class="login_btn"> <a href="#loginform" class="btn btn-xs uppercase" data-toggle="modal" data-dismiss="modal">Login / Register</a> </div>
              <?php } else {
                echo "<div class='login_status'>Welcome To A+ motorbike</div>"; // 使用 div 包裹，方便樣式控制
              } ?>
            </div> <!-- End of header-buttons-group -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <nav id="navigation_bar" class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <button id="menu_slide" data-target="#navigation" aria-expanded="false" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      </div>
      <div class="header_wrap">
        <div class="user_login">
          <ul>
            <li class="dropdown"> <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user-circle" aria-hidden="true"></i>
                <?php
                // 檢查 $_SESSION['login'] 是否存在且不為空
                if (isset($_SESSION['login']) && $_SESSION['login'] != '') {
                  $email = $_SESSION['login'];
                  $sql = "SELECT FullName FROM tblusers WHERE EmailId=:email ";
                  $query = $dbh->prepare($sql);
                  $query->bindParam(':email', $email, PDO::PARAM_STR);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  if ($query->rowCount() > 0) {
                    foreach ($results as $result) {
                      echo htmlentities($result->FullName);
                    }
                  } else {
                    echo "Guest"; // 如果找不到使用者名稱，顯示 "Guest"
                  }
                } else {
                  echo "Guest"; // 如果未登入，顯示 "Guest"
                }
                ?><i class="fa fa-angle-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <?php
                // 檢查 $_SESSION['login'] 是否存在且為真 (已登入)
                if (isset($_SESSION['login']) && $_SESSION['login']) { ?>
                  <li><a href="profile.php">Profile Settings</a></li>
                  <li><a href="update-password.php">Update Password</a></li>
                  <li><a href="my-post.php">My Post</a></li>
                  <li><a href="post-testimonial.php">Post a Testimonial</a></li>
                  <li><a href="my-testimonials.php">My Testimonial</a></li>
                  <li><a href="logout.php">Sign Out</a></li>
                <?php } else { ?>
                  <li><a href="#loginform" data-toggle="modal" data-dismiss="modal">Profile Settings</a></li>
                  <li><a href="#loginform" data-toggle="modal" data-dismiss="modal">Update Password</a></li>
                  <li><a href="#loginform" data-toggle="modal" data-dismiss="modal">My Booking</a></li>
                  <li><a href="#loginform" data-toggle="modal" data-dismiss="modal">Post a Testimonial</a></li>
                  <li><a href="#loginform" data-toggle="modal" data-dismiss="modal">My Testimonial</a></li>
                  <li><a href="#loginform" data-toggle="modal" data-dismiss="modal">Sign Out</a></li>
                <?php } ?>
              </ul>
            </li>
          </ul>
        </div>
        <div class="header_search">
          <div id="search_toggle"><i class="fa fa-search" aria-hidden="true"></i></div>
          <form action="#" method="get" id="header-search-form">
            <input type="text" placeholder="Search..." class="form-control">
            <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
          </form>
        </div>
      </div>
      <div class="collapse navbar-collapse" id="navigation">
        <ul class="nav navbar-nav">
          <li><a href="index.php">Home</a> </li>
          <li><a href="page.php?type=aboutus">About Us</a></li>
          <li class="dropdown"><a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">MARKET <i aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
              <li><a href="bike-listing.php">Bike Listing</a></li>
              <li><a href="accessories.php">Accessories</a></li>
            </ul>
          </li>
          <li><a href="page.php?type=blog">Blog</a></li>
          <li><a href="contact-us.php">Contact Us</a></li>

        </ul>
      </div>
    </div>
  </nav>
</header>
