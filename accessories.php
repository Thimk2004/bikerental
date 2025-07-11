<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Define available sorting options
$sort_options = [
    'default' => 'Best Match',
    'recent' => 'Recent', // Using post_date as recent
    'price_high_to_low' => 'Price - High to Low',
    'price_low_to_high' => 'Price - Low to High',
    'transaction_low_to_high' => 'Transaction Count - Low to High',
    'transaction_high_to_low' => 'Transaction Count - High to Low'
];

// Initialize filter variables from GET parameters
$selected_sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sort_options) ? $_GET['sort'] : 'default';
$selected_category_id = (isset($_GET['category']) && $_GET['category'] !== '') ? intval($_GET['category']) : null;

// Price filters: set to null if input is empty, so they don't participate in SQL filtering
$min_price = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? floatval($_GET['min_price']) : null;
$max_price = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? floatval($_GET['max_price']) : null;


// Fetch accessory categories (subcategories specifically, as they are the direct types)
$accessory_categories = [];
try {
    $sql_categories = "SELECT subcategory_id, subcategory_name FROM tbl_accessory_subcategories ORDER BY subcategory_name ASC";
    $query_categories = $dbh->prepare($sql_categories);
    $query_categories->execute();
    $accessory_categories = $query_categories->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    error_log("PDO Exception in accessories.php (fetching categories): " . $e->getMessage());
}


// Build SQL query for accessories
$sql = "SELECT ta.*, tasc.subcategory_name
        FROM tbl_accessories ta
        LEFT JOIN tbl_accessory_category_map tacm ON ta.accessory_id = tacm.accessory_id
        LEFT JOIN tbl_accessory_subcategories tasc ON tacm.subcategory_id = tasc.subcategory_id";

$params = []; // For PDO binding parameters
$where_clauses = ["ta.is_active = 1"]; // Only show active accessories

// Apply filters
if ($selected_category_id !== null) {
    $where_clauses[] = "tasc.subcategory_id = :category_id";
    $params[':category_id'] = $selected_category_id;
}
if ($min_price !== null) {
    $where_clauses[] = "ta.price >= :minprice";
    $params[':minprice'] = $min_price;
}
if ($max_price !== null) {
    $where_clauses[] = "ta.price <= :maxprice";
    $params[':maxprice'] = $max_price;
}

// Combine all WHERE clauses
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// Add sorting
switch ($selected_sort) {
    case 'recent':
        $sql .= " ORDER BY ta.post_date DESC";
        break;
    case 'price_high_to_low':
        $sql .= " ORDER BY ta.price DESC";
        break;
    case 'price_low_to_high':
        $sql .= " ORDER BY ta.price ASC";
        break;
    case 'transaction_low_to_high':
        $sql .= " ORDER BY ta.transaction_count ASC";
        break;
    case 'transaction_high_to_low':
        $sql .= " ORDER BY ta.transaction_count DESC";
        break;
    case 'default':
    default:
        $sql .= " ORDER BY ta.post_date DESC";
        break;
}

$accessories = []; // Initialize $accessories as an empty array
try {
    if (!$dbh) {
        throw new PDOException("Database connection object is null! Check includes/config.php");
    }

    $query = $dbh->prepare($sql);
    $query->execute($params);
    $accessories = $query->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $e) {
    error_log("PDO Exception in accessories.php (main query): " . $e->getMessage());
    $accessories = []; // Ensure $accessories is empty on error
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
<title>Bike Rental Portal | Accessories Market</title>
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
/* Custom styles for grid layout and filters (similar to bike-listing) */
.listing-section {
    padding: 30px 0;
}
.filter-sidebar {
    background-color: #f8f8f8;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 20px;
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
    box-sizing: border-box;
}
.filter-sidebar .btn-block {
    margin-top: 20px;
}

.accessory-grid-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Spacing between cards */
    justify-content: flex-start;
}
.accessory-item-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
    width: calc(33.33% - 20px); /* 3 column layout, minus gap */
    box-sizing: border-box;
    background-color: #fff;
    display: flex;
    flex-direction: column;
}
.accessory-item-card:hover {
    transform: translateY(-5px);
}
.accessory-item-card img {
    width: 100%;
    height: 200px; /* Fixed image height */
    object-fit: cover; /* Cover area, crop if necessary */
}
.accessory-item-content {
    padding: 15px;
    flex-grow: 1; /* Allow content area to expand */
    display: flex;
    flex-direction: column;
}
.accessory-item-content h5 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.3em;
    color: #333;
}
.accessory-item-content p {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 8px;
}
.accessory-item-price {
    font-size: 1.2em;
    font-weight: bold;
    color: #007bff; /* Or your brand color */
    margin-top: auto; /* Push price to bottom */
    text-align: right;
}
.accessory-item-link {
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
.accessory-item-link:hover {
    background-color: #0056b3;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .accessory-item-card {
        width: calc(50% - 20px); /* 2 columns on medium screens */
    }
}
@media (max-width: 768px) {
    .filter-sidebar {
        margin-right: 0; /* Full width on small screens */
    }
    .accessory-item-card {
        width: 100%; /* 1 column on small screens */
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

<!-- Page Header -->
<section class="page-header listing_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>Accessories Market</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Home</a></li>
        <li>Accessories</li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>
<!-- /Page Header -->

<!-- Listing Section -->
<section class="listing-section">
  <div class="container">
    <div class="row">

      <!-- Filter Sidebar -->
      <div class="col-md-3 col-sm-4">
        <!-- Post My Accessory Button -->
        <?php if (isset($_SESSION['login']) && strlen($_SESSION['login']) > 0) { ?>
        <div class="mb-3">
            <a href="post-accessory.php" class="btn btn-success btn-block">Post My Accessory</a>
        </div>
        <?php } ?>

        <div class="filter-sidebar">
          <form action="accessories.php" method="get">
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
              <label for="accessory_category">Category Type:</label>
              <select class="form-control" id="accessory_category" name="category" onchange="this.form.submit()">
                <option value="">Select Category</option>
                <?php foreach ($accessory_categories as $category) { ?>
                  <option value="<?php echo htmlentities($category->subcategory_id); ?>" <?php if ($selected_category_id == $category->subcategory_id) echo 'selected'; ?>>
                    <?php echo htmlentities($category->subcategory_name); ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label for="min_price">Price (HKD):</label>
              <input type="number" class="form-control" id="min_price" name="min_price" placeholder="Min Price" value="<?php echo htmlentities($min_price); ?>">
            </div>
            <div class="form-group">
              <input type="number" class="form-control" id="max_price" name="max_price" placeholder="Max Price" value="<?php echo htmlentities($max_price); ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
            <a href="accessories.php" class="btn btn-default btn-block">Clear Filters</a>
          </form>
        </div>
      </div>
      <!-- /Filter Sidebar -->

      <!-- Accessories List -->
      <div class="col-md-9 col-sm-8">
        <div class="accessory-grid-container">
          <?php if (!empty($accessories)) {
              foreach ($accessories as $accessory) { ?>
                <div class="accessory-item-card">
                  <?php if (!empty($accessory->image_url1)) { ?>
                    <img src="admin/img/accessoryimages/<?php echo htmlentities($accessory->image_url1); ?>" alt="<?php echo htmlentities($accessory->title); ?>">
                  <?php } else { ?>
                    <img src="https://placehold.co/400x200/cccccc/333333?text=No+Image" alt="No Image">
                  <?php } ?>
                  <div class="accessory-item-content">
                    <h5><?php echo htmlentities($accessory->title); ?></h5>
                    <p>Condition: <?php echo htmlentities($accessory->condition ?: 'N/A'); ?></p>
                    <p>Category: <?php echo htmlentities($accessory->subcategory_name ?: 'N/A'); ?></p>
                    <?php if (isset($accessory->transaction_count)) { ?>
                      <p>Transactions: <?php echo htmlentities($accessory->transaction_count); ?></p>
                    <?php } ?>
                    <div class="accessory-item-price">HKD $<?php echo htmlentities($accessory->price); ?></div>
                    <a href="accessory-details.php?accid=<?php echo htmlentities($accessory->accessory_id); ?>" class="accessory-item-link">View Details</a>
                  </div>
                </div>
              <?php }
            } else { ?>
              <div class="col-md-12">
                <p class="text-center">No accessories found matching your criteria.</p>
              </div>
            <?php } ?>
          </div>
        </div>
        <!-- /Accessories List -->

      </div>
    </div>
  </section>
  <!-- /Listing Section -->

  <!--Footer-->
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
