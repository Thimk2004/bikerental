<?php
session_start();
// error_reporting(E_ALL); // 關閉所有錯誤報告，正式環境建議開啟或設定為 E_ALL & ~E_NOTICE
// ini_set('display_errors', 1); // 關閉錯誤顯示
error_reporting(0); // 正式環境建議關閉所有錯誤報告
include('includes/config.php');

// 定義可用的排序選項
$sort_options = [
    'default' => 'Best Match',
    'recent' => 'Recent', // 使用 RegDate (註冊日期) 作為最近發佈的依據
    'price_high_to_low' => 'Price - High to Low',
    'price_low_to_high' => 'Price - Low to High',
    'transaction_low_to_high' => 'Transaction Count - Low to High',
    'transaction_high_to_low' => 'Transaction Count - High to Low'
];

// 定義電單車類型
$bike_types = ['Naked', 'Cruiser', 'Sports', 'Touring', 'Off-road', 'Scooter', 'Electric motorcycle'];

// 定義可篩選的排氣量範圍
$engine_displacement_ranges = [
    '0-150' => '0 - 150 CC',
    '151-250' => '151 - 250 CC',
    '251-400' => '251 - 400 CC',
    '401-600' => '401 - 600 CC',
    '601-800' => '601 - 800 CC',
    '801-1000' => '801 - 1000 CC',
    '1001-over' => '1001+ CC'
];

// 初始化篩選變量，從 GET 參數獲取，並確保空值為 null
$selected_sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sort_options) ? $_GET['sort'] : 'default';
$selected_type = (isset($_GET['type']) && $_GET['type'] !== '') ? $_GET['type'] : null;
$selected_brand_id = (isset($_GET['brand']) && $_GET['brand'] !== '') ? intval($_GET['brand']) : null;
$selected_model_year = (isset($_GET['modelyear']) && $_GET['modelyear'] !== '') ? intval($_GET['modelyear']) : null;
$selected_engine_displacement = (isset($_GET['enginedisplacement']) && $_GET['enginedisplacement'] !== '') ? $_GET['enginedisplacement'] : null;
$transaction_count_range = (isset($_GET['transaction_count_range']) && $_GET['transaction_count_range'] !== '') ? $_GET['transaction_count_range'] : null;

// 價格篩選：如果輸入框為空，則設為 null，這樣就不會參與 SQL 篩選
$min_price = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? intval($_GET['min_price']) : null;
$max_price = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? intval($_GET['max_price']) : null;

// 從數據庫獲取品牌列表
$brands_from_db = [];
// 移除 try-catch，因為這部分通常不會是主要問題來源，且錯誤會在主查詢中捕獲
$ret_brands = "SELECT id, BrandName FROM tblbrands ORDER BY BrandName ASC";
$query_brands = $dbh->prepare($ret_brands);
$query_brands->execute();
$result_brands = $query_brands->fetchAll(PDO::FETCH_OBJ);
if (!empty($result_brands)) { // 使用 !empty() 檢查結果
    foreach ($result_brands as $brand) {
        $brands_from_db[$brand->id] = $brand->BrandName; // 儲存為 ID => Name 的映射
    }
}


// 構建 SQL 查詢基礎
$sql = "SELECT tv.*, tb.BrandName FROM tblvehicles tv JOIN tblbrands tb ON tv.VehiclesBrand = tb.id";
$params = []; // 用於 PDO 綁定參數
$where_clauses = ["tv.IsActive = 1"]; // 始終包含此條件

// 應用篩選條件
if ($selected_type !== null) { // 檢查是否為 null
    // 檢查 $selected_type 是否在 $bike_types 數組中，以防止注入無效值
    if (in_array($selected_type, $bike_types)) {
        $where_clauses[] = "tv.BikeType = :biketype";
        $params[':biketype'] = $selected_type;
    }
}
if ($selected_brand_id !== null) { // 檢查是否為 null
    $where_clauses[] = "tv.VehiclesBrand = :brand_id";
    $params[':brand_id'] = $selected_brand_id;
}
if ($selected_model_year !== null) { // 檢查是否為 null
    $where_clauses[] = "tv.ModelYear = :modelyear";
    $params[':modelyear'] = $selected_model_year;
}
if ($min_price !== null) {
    $where_clauses[] = "tv.PricePerDay >= :minprice";
    $params[':minprice'] = $min_price;
}
if ($max_price !== null) {
    $where_clauses[] = "tv.PricePerDay <= :maxprice";
    $params[':maxprice'] = $max_price;
}

// 排氣量篩選
if ($selected_engine_displacement !== null) { // 檢查是否為 null
    if ($selected_engine_displacement === '1001-over') {
        $where_clauses[] = "tv.EngineDisplacement >= 1001";
    } else {
        list($min_cc, $max_cc) = explode('-', $selected_engine_displacement);
        $where_clauses[] = "tv.EngineDisplacement BETWEEN :mincc AND :maxcc";
        $params[':mincc'] = intval($min_cc);
        $params[':maxcc'] = intval($max_cc);
    }
}
// 交易次數篩選 (根據 TransactionCount 字段)
if ($transaction_count_range !== null) { // 檢查是否為 null
    if ($transaction_count_range === '0-0') {
        $where_clauses[] = "tv.TransactionCount = 0";
    } elseif ($transaction_count_range === '11-over') {
        $where_clauses[] = "tv.TransactionCount >= 11";
    } else {
        list($min_tx, $max_tx) = explode('-', $transaction_count_range);
        $where_clauses[] = "tv.TransactionCount BETWEEN :mintx AND :maxtx";
        $params[':mintx'] = intval($min_tx);
        $params[':maxtx'] = intval($max_tx);
    }
}

// 將所有 WHERE 子句組合起來
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// 添加排序
switch ($selected_sort) {
    case 'recent':
        $sql .= " ORDER BY tv.RegDate DESC"; // 假設 'RegDate' 是記錄創建日期
        break;
    case 'price_high_to_low':
        $sql .= " ORDER BY tv.PricePerDay DESC";
        break;
    case 'price_low_to_high':
        $sql .= " ORDER BY tv.PricePerDay ASC";
        break;
    case 'transaction_low_to_high':
        $sql .= " ORDER BY tv.TransactionCount ASC";
        break;
    case 'transaction_high_to_low':
        $sql .= " ORDER BY tv.TransactionCount DESC";
        break;
    case 'default':
    default:
        $sql .= " ORDER BY tv.RegDate DESC"; // 預設按 RegDate 降序 (最新)
        break;
}

// 在這裡添加 try-catch 塊來處理主查詢
$vehicles = []; // 初始化 $vehicles 為空陣列，以防查詢失敗
try {
    if (!$dbh) {
        throw new PDOException("數據庫連接對象為空！請檢查 includes/config.php");
    }

    $query = $dbh->prepare($sql);
    // 直接將參數陣列傳遞給 execute()，這是推薦的做法
    $query->execute($params); 
    $vehicles = $query->fetchAll(PDO::FETCH_OBJ); // 這裡只調用一次 fetchAll()

} catch (PDOException $e) {
    // 捕獲 PDO 異常，打印錯誤訊息並記錄日誌
    // 在生產環境中，你可能希望將這些錯誤記錄到文件而不是直接顯示給用戶
    echo "<div style='color: red; padding: 10px; border: 1px solid red; background-color: #ffe0e0;'>";
    echo "<strong>數據庫查詢失敗！</strong><br>";
    echo "錯誤訊息: " . htmlentities($e->getMessage());
    echo "<br>請檢查數據庫連接和 SQL 語法。";
    echo "</div>";
    // 將錯誤記錄到伺服器日誌中，以便後續分析
    error_log("PDO Exception in bike-listing.php (main query): " . $e->getMessage());
    // 確保 $vehicles 在錯誤時依然是空陣列，這樣頁面就不會嘗試遍歷未定義的變數
    $vehicles = [];
}


// 從數據庫獲取所有獨特的型號年份，用於篩選下拉菜單
$model_years = [];
// 移除 try-catch
$ret_years = "SELECT DISTINCT ModelYear FROM tblvehicles ORDER BY ModelYear DESC";
$query_years = $dbh->prepare($ret_years);
$query_years->execute();
$result_years = $query_years->fetchAll(PDO::FETCH_OBJ);
foreach ($result_years as $year) {
    $model_years[] = $year->ModelYear;
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
<title>Bike Rental Portal | Market Listings</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/styles.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">

<!-- SWITCHER -->
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

<style>
/* 自定義樣式，用於網格佈局和篩選器 */
.listing-section {
    padding: 30px 0;
}
.filter-sidebar {
    background-color: #f8f8f8;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 20px; /* 在手機上顯示間距 */
}
.filter-sidebar h5 {
    font-size: 1.2em;
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}
.filter-sidebar .form-group {
    margin-bottom: 15px;
}
.filter-sidebar label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}
.filter-sidebar select,
.filter-sidebar input[type="text"],
.filter-sidebar input[type="number"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box; /* 將 padding 和 border 包含在寬度內 */
}
.filter-sidebar .btn-block {
    margin-top: 20px;
}

.bike-grid-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* 卡片之間的間距 */
    justify-content: flex-start;
}
.bike-item-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
    width: calc(33.33% - 20px); /* 3 列佈局，減去間距 */
    box-sizing: border-box;
    background-color: #fff;
    display: flex;
    flex-direction: column;
}
.bike-item-card:hover {
    transform: translateY(-5px);
}
.bike-item-card img {
    width: 100%;
    height: 200px; /* 圖片固定高度 */
    object-fit: cover; /* 覆蓋區域，必要時裁剪 */
}
.bike-item-content {
    padding: 15px;
    flex-grow: 1; /* 允許內容區域擴展 */
    display: flex;
    flex-direction: column;
}
.bike-item-content h5 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.3em;
    color: #333;
}
.bike-item-content p {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 8px;
}
.bike-item-price {
    font-size: 1.2em;
    font-weight: bold;
    color: #007bff; /* 或你的品牌顏色 */
    margin-top: auto; /* 將價格推到底部 */
    text-align: right;
}
.bike-item-link {
    display: block;
    text-align: center;
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    margin-top: 15px;
    transition: background-color 0.2s ease;
}
.bike-item-link:hover {
    background-color: #0056b3;
}

/* 響應式調整 */
@media (max-width: 992px) {
    .bike-item-card {
        width: calc(50% - 20px); /* 在中等屏幕上顯示 2 列 */
    }
}
@media (max-width: 768px) {
    .filter-sidebar {
        margin-right: 0; /* 在小屏幕上全寬顯示 */
    }
    .bike-item-card {
        width: 100%; /* 在小屏幕上顯示 1 列 */
    }
}
</style>
</head>
<body>
<!-- Start Switcher -->
<?php include('includes/colorswitcher.php');?>
<!-- /Switcher -->

<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header -->

<!-- 頁面標頭 (你可以進一步自定義) -->
<section class="page-header listing_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>電單車市場</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">首頁</a></li>
        <li>電單車市場</li>
      </ul>
    </div>
  </div>
  <!-- 黑色疊層 -->
  <div class="dark-overlay"></div>
</section>
<!-- /頁面標頭 -->

<!-- 列表區塊 -->
<section class="listing-section">
  <div class="container">
    <div class="row">

      <!-- 篩選側邊欄 -->
      <div class="col-md-3 col-sm-4">
        <div class="filter-sidebar">
          <form action="bike-listing.php" method="get">
            <h5>篩選器</h5>

            <div class="form-group">
              <label for="sort_by">排序方式:</label>
              <select class="form-control" id="sort_by" name="sort" onchange="this.form.submit()">
                <?php foreach ($sort_options as $key => $value) { ?>
                  <option value="<?php echo htmlentities($key); ?>" <?php if ($selected_sort == $key) echo 'selected'; ?>>
                    <?php echo htmlentities($value); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="bike_type">電單車類型:</label>
              <select class="form-control" id="bike_type" name="type" onchange="this.form.submit()">
                <option value="">選擇類型</option>
                <?php foreach ($bike_types as $type) { ?>
                  <option value="<?php echo htmlentities($type); ?>" <?php if ($selected_type == $type) echo 'selected'; ?>>
                    <?php echo htmlentities($type); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="brand_name">品牌:</label>
              <select class="form-control" id="brand_name" name="brand" onchange="this.form.submit()">
                <option value="">選擇品牌</option>
                <?php foreach ($result_brands as $brand_obj) { ?>
                  <option value="<?php echo htmlentities($brand_obj->id); ?>" <?php if ($selected_brand_id == $brand_obj->id) echo 'selected'; ?>>
                    <?php echo htmlentities($brand_obj->BrandName); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="model_year">年份:</label>
              <select class="form-control" id="model_year" name="modelyear" onchange="this.form.submit()">
                <option value="">選擇年份</option>
                <?php foreach ($model_years as $year) { ?>
                  <option value="<?php echo htmlentities($year); ?>" <?php if ($selected_model_year == $year) echo 'selected'; ?>>
                    <?php echo htmlentities($year); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="engine_displacement">Engine Displacement (CC):</label>
              <select class="form-control" id="engine_displacement" name="enginedisplacement" onchange="this.form.submit()">
                <option value="">Select CC Range</option>
                <?php foreach ($engine_displacement_ranges as $key => $value) { ?>
                  <option value="<?php echo htmlentities($key); ?>" <?php if ($selected_engine_displacement == $key) echo 'selected'; ?>>
                    <?php echo htmlentities($value); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="transaction_count_range">交易次數:</label>
              <select class="form-control" id="transaction_count_range" name="transaction_count_range" onchange="this.form.submit()">
                <option value="">選擇範圍</option>
                <option value="0-0" <?php if ($transaction_count_range == '0-0') echo 'selected'; ?>>0 次交易</option>
                <option value="1-5" <?php if ($transaction_count_range == '1-5') echo 'selected'; ?>>1-5 次交易</option>
                <option value="6-10" <?php if ($transaction_count_range == '6-10') echo 'selected'; ?>>6-10 次交易</option>
                <option value="11-over" <?php if ($transaction_count_range == '11-over') echo 'selected'; ?>>11+ 次交易</option>
                <!-- 可根據需要添加更多範圍 -->
              </select>
            </div>

            <div class="form-group">
              <label for="min_price">預算 (HKD):</label>
              <input type="number" class="form-control" id="min_price" name="min_price" placeholder="最低價格" value="<?php echo htmlentities($min_price); ?>">
            </div>
            <div class="form-group">
              <input type="number" class="form-control" id="max_price" name="max_price" placeholder="最高價格" value="<?php echo htmlentities($max_price); ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">應用篩選</button>
            <a href="bike-listing.php" class="btn btn-default btn-block">清除篩選</a>
          </form>
        </div>
      </div>
      <!-- /篩選側邊欄 -->

      <!-- 電單車列表 -->
      <div class="col-md-9 col-sm-8">
        <div class="bike-grid-container">
          <?php if (!empty($vehicles)) { // 這裡使用 !empty($vehicles) 判斷
              foreach ($vehicles as $vehicle) { ?>
                <div class="bike-item-card">
                  <?php if (!empty($vehicle->Vimage1)) { ?>
                    <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle->Vimage1); ?>" alt="<?php echo htmlentities($vehicle->VehiclesTitle); ?>">
                  <?php } else { ?>
                    <img src="https://placehold.co/400x200/cccccc/333333?text=無圖片" alt="無圖片">
                  <?php } ?>
                  <div class="bike-item-content">
                    <h5><?php echo htmlentities($vehicle->VehiclesTitle); ?></h5>
                    <p>品牌: <?php echo htmlentities($vehicle->BrandName); ?></p>
                    <p>類型: <?php echo htmlentities($vehicle->BikeType ?: 'N/A'); ?></p> <!-- 顯示電單車類型 -->
                    <p>Engine: <?php echo htmlentities($vehicle->EngineDisplacement); ?> CC</p>
                    <p>年份: <?php echo htmlentities($vehicle->ModelYear); ?></p>
                    <!-- 假設 TransactionCount 字段存在 -->
                    <?php if (isset($vehicle->TransactionCount)) { ?>
                      <p>交易次數: <?php echo htmlentities($vehicle->TransactionCount); ?></p>
                    <?php } ?>
                    <div class="bike-item-price">HKD $<?php echo htmlentities($vehicle->PricePerDay); ?></div>
                    <a href="vehical-details.php?vhid=<?php echo htmlentities($vehicle->id); ?>" class="bike-item-link">查看詳情</a>
                  </div>
                </div>
              <?php }
            } else { ?>
              <div class="col-md-12">
                <p class="text-center">沒有找到符合條件的電單車。</p>
              </div>
            <?php } ?>
          </div>
        </div>
        <!-- /電單車列表 -->

      </div>
    </div>
  </section>
  <!-- /列表區塊 -->

  <!--頁腳 -->
  <?php include('includes/footer.php');?>
  <!-- /頁腳 -->

  <!-- 回到頂部 -->
  <div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
  <!--/回到頂部 -->

  <!-- 登入表單 -->
  <?php include('includes/login.php');?>
  <!--/登入表單 -->

  <!-- 註冊表單 -->
  <?php include('includes/registration.php');?>

  <!--/註冊表單 -->

  <!-- 忘記密碼表單 -->
  <?php include('includes/forgotpassword.php');?>
  <!--/忘記密碼表單 -->

  <!-- 腳本 -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/interface.js"></script>
  <!--Switcher-->
  <script src="assets/switcher/js/switcher.js"></script>
  <!--bootstrap-slider-JS-->
  <script src="assets/js/bootstrap-slider.min.js"></script>
  <script src="assets/js/slick.min.js"></script>
  <script src="assets/js/owl.carousel.min.js"></script>
</body>

</html>
