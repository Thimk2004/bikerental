<?php
session_start();
error_reporting(0); // It is recommended to turn off all error reporting in production.
include('includes/config.php');

// Define available sorting options
$sort_options = [
    'default' => 'Best Match',
    'recent' => 'Recent', // Use RegDate as the basis for recently posted
    'price_high_to_low' => 'Price - High to Low',
    'price_low_to_high' => 'Price - Low to High',
    'transaction_low_to_high' => 'Transaction Count - Low to High',
    'transaction_high_to_low' => 'Transaction Count - High to Low'
];

// Define motorcycle types
$bike_types = ['Naked', 'Cruiser', 'Sports', 'Touring', 'Off-road', 'Scooter', 'Electric motorcycle'];

// Define filterable engine displacement ranges
$engine_displacement_ranges = [
    '0-150' => '0 - 150 CC',
    '151-250' => '151 - 250 CC',
    '251-400' => '251 - 400 CC',
    '401-600' => '401 - 600 CC',
    '601-800' => '601 - 800 CC',
    '801-1000' => '801 - 1000 CC',
    '1001-over' => '1001+ CC'
];

// Initialize filter variables, get from GET parameters, and ensure null for empty values
$selected_sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sort_options) ? $_GET['sort'] : 'default';
$selected_type = (isset($_GET['type']) && $_GET['type'] !== '') ? $_GET['type'] : null;
$selected_brand_id = (isset($_GET['brand']) && $_GET['brand'] !== '') ? intval($_GET['brand']) : null;
$selected_model_year = (isset($_GET['modelyear']) && $_GET['modelyear'] !== '') ? intval($_GET['modelyear']) : null;
$selected_engine_displacement = (isset($_GET['enginedisplacement']) && $_GET['enginedisplacement'] !== '') ? $_GET['enginedisplacement'] : null;
$transaction_count_range = (isset($_GET['transaction_count_range']) && $_GET['transaction_count_range'] !== '') ? $_GET['transaction_count_range'] : null;

// Price filter: if the input box is empty, set it to null so it doesn't participate in SQL filtering
$min_price = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? intval($_GET['min_price']) : null;
$max_price = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? intval($_GET['max_price']) : null;

// Get brand list from database
$brands_from_db = [];
$ret_brands = "SELECT id, BrandName FROM tblbrands ORDER BY BrandName ASC";
$query_brands = $dbh->prepare($ret_brands);
$query_brands->execute();
$result_brands = $query_brands->fetchAll(PDO::FETCH_OBJ);
if (!empty($result_brands)) {
    foreach ($result_brands as $brand) {
        $brands_from_db[$brand->id] = $brand->BrandName;
    }
}


// Build SQL query base
$sql = "SELECT tv.*, tb.BrandName FROM tblvehicles tv JOIN tblbrands tb ON tv.VehiclesBrand = tb.id";
$params = []; // Used for PDO parameter binding
$where_clauses = []; // Remove tv.IsActive = 1 condition

// Apply filter conditions
if ($selected_type !== null) {
    if (in_array($selected_type, $bike_types)) {
        $where_clauses[] = "tv.BikeType = :biketype";
        $params[':biketype'] = $selected_type;
    }
}
if ($selected_brand_id !== null) {
    $where_clauses[] = "tv.VehiclesBrand = :brand_id";
    $params[':brand_id'] = $selected_brand_id;
}
if ($selected_model_year !== null) {
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

// Engine displacement filter
if ($selected_engine_displacement !== null) {
    if ($selected_engine_displacement === '1001-over') {
        $where_clauses[] = "tv.EngineDisplacement >= 1001";
    } else {
        list($min_cc, $max_cc) = explode('-', $selected_engine_displacement);
        $where_clauses[] = "tv.EngineDisplacement BETWEEN :mincc AND :maxcc";
        $params[':mincc'] = intval($min_cc);
        $params[':maxcc'] = intval($max_cc);
    }
}
// Transaction count filter (based on TransactionCount field)
if ($transaction_count_range !== null) {
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

// Combine all WHERE clauses
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// Add sorting
switch ($selected_sort) {
    case 'recent':
        $sql .= " ORDER BY tv.RegDate DESC";
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
        $sql .= " ORDER BY tv.RegDate DESC";
        break;
}

$vehicles = []; // Initialize $vehicles as an empty array in case the query fails
try {
    if (!$dbh) {
        throw new PDOException("Database connection object is null! Please check includes/config.php");
    }

    $query = $dbh->prepare($sql);
    $query->execute($params);
    $vehicles = $query->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $e) {
    // Catch PDO exceptions and log them, but do not display directly to the user in a production environment
    error_log("PDO Exception in bike-listing.php (main query): " . $e->getMessage());
    $vehicles = []; // Ensure $vehicles is still an empty array on error
}


// Get all unique model years from the database for the filter dropdown
$model_years = [];
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
/* Custom styles for grid layout and filters */
.listing-section {
    padding: 30px 0;
}
.filter-sidebar {
    background-color: #f8f8f8;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 20px; /* Spacing on mobile */
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
    box-sizing: border-box; /* Include padding and border in width */
}
.filter-sidebar .btn-block {
    margin-top: 20px;
}

.bike-grid-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Spacing between cards */
    justify-content: flex-start;
}
.bike-item-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
    width: calc(33.33% - 20px); /* 3 column layout, minus spacing */
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
    height: 200px; /* Fixed image height */
    object-fit: cover; /* Cover area, crop if necessary */
}
.bike-item-content {
    padding: 15px;
    flex-grow: 1; /* Allow content area to expand */
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
    color: #007bff; /* Or your brand color */
    margin-top: auto; /* Push price to the bottom */
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

/* Responsive adjustments */
@media (max-width: 992px) {
    .bike-item-card {
        width: calc(50% - 20px); /* Show 2 columns on medium screens */
    }
}
@media (max-width: 768px) {
    .filter-sidebar {
        margin-right: 0; /* Full width on small screens */
    }
    .bike-item-card {
        width: 100%; /* Show 1 column on small screens */
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

<!-- Page Header (You can further customize) -->
<section class="page-header listing_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>Motorcycle Market</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Home</a></li>
        <li>Motorcycle Market</li>
      </ul>
    </div>
  </div>
  <!-- Dark overlay -->
  <div class="dark-overlay"></div>
</section>
<!-- /Page Header -->

<!-- Listing Section -->
<section class="listing-section">
  <div class="container">
    <div class="row">

      <!-- Filter Sidebar -->
      <div class="col-md-3 col-sm-4">
        <div class="filter-sidebar">
          <form action="bike-listing.php" method="get">
            <h5>Filters</h5>

            <div class="form-group">
              <label for="sort_by">Sort By:</label>
              <select class="form-control" id="sort_by" name="sort" onchange="this.form.submit()">
                <?php foreach ($sort_options as $key => $value) { ?>
                  <option value="<?php echo htmlentities($key); ?>" <?php if ($selected_sort == $key) echo 'selected'; ?>>
                    <?php echo htmlentities($value); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="bike_type">Motorcycle Type:</label>
              <select class="form-control" id="bike_type" name="type" onchange="this.form.submit()">
                <option value="">Select Type</option>
                <?php foreach ($bike_types as $type) { ?>
                  <option value="<?php echo htmlentities($type); ?>" <?php if ($selected_type == $type) echo 'selected'; ?>>
                    <?php echo htmlentities($type); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="brand_name">Brand:</label>
              <select class="form-control" id="brand_name" name="brand" onchange="this.form.submit()">
                <option value="">Select Brand</option>
                <?php foreach ($result_brands as $brand_obj) { ?>
                  <option value="<?php echo htmlentities($brand_obj->id); ?>" <?php if ($selected_brand_id == $brand_obj->id) echo 'selected'; ?>>
                    <?php echo htmlentities($brand_obj->BrandName); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="model_year">Year:</label>
              <select class="form-control" id="model_year" name="modelyear" onchange="this.form.submit()">
                <option value="">Select Year</option>
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
              <label for="transaction_count_range">Transaction Count:</label>
              <select class="form-control" id="transaction_count_range" name="transaction_count_range" onchange="this.form.submit()">
                <option value="">Select Range</option>
                <option value="0-0" <?php if ($transaction_count_range == '0-0') echo 'selected'; ?>>0 Transactions</option>
                <option value="1-5" <?php if ($transaction_count_range == '1-5') echo 'selected'; ?>>1-5 Transactions</option>
                <option value="6-10" <?php if ($transaction_count_range == '6-10') echo 'selected'; ?>>6-10 Transactions</option>
                <option value="11-over" <?php if ($transaction_count_range == '11-over') echo 'selected'; ?>>11+ Transactions</option>
                <!-- Add more ranges as needed -->
              </select>
            </div>

            <div class="form-group">
              <label for="min_price">Budget (HKD):</label>
              <input type="number" class="form-control" id="min_price" name="min_price" placeholder="Min Price" value="<?php echo htmlentities($min_price); ?>">
            </div>
            <div class="form-group">
              <input type="number" class="form-control" id="max_price" name="max_price" placeholder="Max Price" value="<?php echo htmlentities($max_price); ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
            <a href="bike-listing.php" class="btn btn-default btn-block">Clear Filters</a>
          </form>
        </div>
      </div>
      <!-- /Filter Sidebar -->

      <!-- Motorcycle List -->
      <div class="col-md-9 col-sm-8">
        <div class="bike-grid-container">
          <?php if (!empty($vehicles)) { // Use !empty($vehicles) to check
              foreach ($vehicles as $vehicle) { ?>
                <div class="bike-item-card">
                  <?php if (!empty($vehicle->Vimage1)) { ?>
                    <img src="admin/img/vehicleimages/<?php echo htmlentities($vehicle->Vimage1); ?>" alt="<?php echo htmlentities($vehicle->VehiclesTitle); ?>">
                  <?php } else { ?>
                    <img src="https://placehold.co/400x200/cccccc/333333?text=No+Image" alt="No Image">
                  <?php } ?>
                  <div class="bike-item-content">
                    <h5><?php echo htmlentities($vehicle->VehiclesTitle); ?></h5>
                    <p>Brand: <?php echo htmlentities($vehicle->BrandName); ?></p>
                    <p>Type: <?php echo htmlentities($vehicle->BikeType ?: 'N/A'); ?></p> <!-- Display motorcycle type -->
                    <p>Engine: <?php echo htmlentities($vehicle->EngineDisplacement); ?> CC</p>
                    <p>Year: <?php echo htmlentities($vehicle->ModelYear); ?></p>
                    <!-- Assuming TransactionCount field exists -->
                    <?php if (isset($vehicle->TransactionCount)) { ?>
                      <p>Transactions: <?php echo htmlentities($vehicle->TransactionCount); ?></p>
                    <?php } ?>
                    <div class="bike-item-price">HKD $<?php echo htmlentities($vehicle->PricePerDay); ?></div>
                    <a href="vehical-details.php?vhid=<?php echo htmlentities($vehicle->id); ?>" class="bike-item-link">View Details</a>
                  </div>
                </div>
              <?php }
            } else { ?>
              <div class="col-md-12">
                <p class="text-center">No motorcycles found matching your criteria.</p>
              </div>
            <?php } ?>
          </div>
        </div>
        <!-- /Motorcycle List -->

      </div>
    </div>
  </section>
  <!-- /Listing Section -->

  <!--Footer -->
  <?php include('includes/footer.php');?>
  <!-- /Footer -->

  <!-- Back to top -->
  <div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
  <!--/Back to top -->

  <!-- Login Form -->
  <?php include('includes/login.php');?>
  <!--/Login Form -->

  <!-- Registration Form -->
  <?php include('includes/registration.php');?>

  <!--/Registration Form -->

  <!-- Forgot Password Form -->
  <?php include('includes/forgotpassword.php');?>
  <!--/Forgot Password Form -->

  <!-- Scripts -->
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
