<?php
session_start();
error_reporting(0); // Turn off all error reporting; in a production environment, it's recommended to enable or set to E_ALL & ~E_NOTICE
include('includes/config.php'); // Include database configuration

// Check if user is logged in
if (strlen($_SESSION['login']) == 0) {
    header('location:index.php'); // Redirect to homepage or login page if not logged in
    exit(); // Terminate script execution
} else { // User is logged in

    $error = ''; // Initialize error message variable
    $msg = '';   // Initialize success message variable

    // Get the ID of the current logged-in user
    $loggedInUserEmail = $_SESSION['login'];
    $userIdForAccessory = null;
    $sql_get_userid = "SELECT id FROM tblusers WHERE EmailId = :email";
    $query_get_userid = $dbh->prepare($sql_get_userid);
    $query_get_userid->bindParam(':email', $loggedInUserEmail, PDO::PARAM_STR);
    $query_get_userid->execute();
    $user_row = $query_get_userid->fetch(PDO::FETCH_OBJ);
    if ($user_row) {
        $userIdForAccessory = $user_row->id;
    } else {
        // If user ID is not found, it might be a database issue or session anomaly
        $error = "Error: User ID not found. Please log in again.";
    }

    // Fetch accessory subcategories for the dropdown
    $accessory_subcategories = [];
    try {
        $sql_subcategories = "SELECT subcategory_id, subcategory_name FROM tbl_accessory_subcategories ORDER BY subcategory_name ASC";
        $query_subcategories = $dbh->prepare($sql_subcategories);
        $query_subcategories->execute();
        $accessory_subcategories = $query_subcategories->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $error = "Error fetching accessory categories: " . $e->getMessage();
    }


    if (isset($_POST['submit']) && $userIdForAccessory) // Ensure there is a user ID before processing submission
    {
        // Get accessory details from form
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = floatval($_POST['price']);
        $condition = $_POST['condition']; // This is the problematic keyword
        $subcategory_id = $_POST['subcategory_id'];
        $transaction_count = isset($_POST['transaction_count']) ? intval($_POST['transaction_count']) : 0;

        // Handle image uploads - Generate unique names
        $target_dir = "admin/img/accessoryimages/";
        if (!file_exists($target_dir) && !is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Create directory if it does not exist
        }

        $image_url1 = '';
        if (!empty($_FILES["img1"]["name"])) {
            $ext = pathinfo($_FILES["img1"]["name"], PATHINFO_EXTENSION);
            $image_url1 = md5(uniqid(rand(), true)) . '.' . $ext; // Generate unique file name
            move_uploaded_file($_FILES["img1"]["tmp_name"], $target_dir . $image_url1);
        }

        $image_url2 = '';
        if (!empty($_FILES["img2"]["name"])) {
            $ext = pathinfo($_FILES["img2"]["name"], PATHINFO_EXTENSION);
            $image_url2 = md5(uniqid(rand(), true)) . '.' . $ext;
            move_uploaded_file($_FILES["img2"]["tmp_name"], $target_dir . $image_url2);
        }

        $image_url3 = '';
        if (!empty($_FILES["img3"]["name"])) {
            $ext = pathinfo($_FILES["img3"]["name"], PATHINFO_EXTENSION);
            $image_url3 = md5(uniqid(rand(), true)) . '.' . $ext;
            move_uploaded_file($_FILES["img3"]["tmp_name"], $target_dir . $image_url3);
        }
        // End of Handle image uploads

        // Insert into tbl_accessories table
        // Corrected: Enclosed `condition` in backticks
        $sql_accessory = "INSERT INTO tbl_accessories(user_id, title, description, price, `condition`, image_url1, image_url2, image_url3, transaction_count) VALUES(:user_id, :title, :description, :price, :condition, :image_url1, :image_url2, :image_url3, :transaction_count)";
        $query_accessory = $dbh->prepare($sql_accessory);
        $query_accessory->bindParam(':user_id', $userIdForAccessory, PDO::PARAM_INT);
        $query_accessory->bindParam(':title', $title, PDO::PARAM_STR);
        $query_accessory->bindParam(':description', $description, PDO::PARAM_STR);
        $query_accessory->bindParam(':price', $price, PDO::PARAM_STR); // Use PARAM_STR for decimal
        $query_accessory->bindParam(':condition', $condition, PDO::PARAM_STR); // Bind the condition parameter
        $query_accessory->bindParam(':image_url1', $image_url1, PDO::PARAM_STR);
        $query_accessory->bindParam(':image_url2', $image_url2, PDO::PARAM_STR);
        $query_accessory->bindParam(':image_url3', $image_url3, PDO::PARAM_STR);
        $query_accessory->bindParam(':transaction_count', $transaction_count, PDO::PARAM_INT);

        try {
            $dbh->beginTransaction(); // Start transaction

            $query_accessory->execute();
            $lastInsertAccessoryId = $dbh->lastInsertId();

            if ($lastInsertAccessoryId) {
                // Insert into tbl_accessory_category_map
                if ($subcategory_id !== null) {
                    $sql_category_map = "INSERT INTO tbl_accessory_category_map(accessory_id, subcategory_id) VALUES(:accessory_id, :subcategory_id)";
                    $query_category_map = $dbh->prepare($sql_category_map);
                    $query_category_map->bindParam(':accessory_id', $lastInsertAccessoryId, PDO::PARAM_INT);
                    $query_category_map->bindParam(':subcategory_id', $subcategory_id, PDO::PARAM_INT);
                    $query_category_map->execute();
                }

                // Process and insert additional contact info into tbl_accessory_contacts
                if (isset($_POST['contact_type']) && is_array($_POST['contact_type'])) {
                    $contactTypes = $_POST['contact_type'];
                    $contactValues = $_POST['contact_value'];
                    $contactDescriptions = $_POST['contact_description'];

                    for ($i = 0; $i < count($contactTypes); $i++) {
                        $type = trim($contactTypes[$i]);
                        $value = trim($contactValues[$i]);
                        $description = trim($contactDescriptions[$i]);

                        if (!empty($type) && !empty($value)) {
                            $sql_contact = "INSERT INTO tbl_accessory_contacts(accessory_id, contact_type, contact_value, description) VALUES(:accessory_id, :contact_type, :contact_value, :description)";
                            $query_contact = $dbh->prepare($sql_contact);
                            $query_contact->bindParam(':accessory_id', $lastInsertAccessoryId, PDO::PARAM_INT);
                            $query_contact->bindParam(':contact_type', $type, PDO::PARAM_STR);
                            $query_contact->bindParam(':contact_value', $value, PDO::PARAM_STR);
                            $query_contact->bindParam(':description', $description, PDO::PARAM_STR);
                            $query_contact->execute();
                        }
                    }
                }
                $dbh->commit(); // Commit transaction
                $msg = "Accessory posted successfully!";
            } else {
                $dbh->rollBack(); // Rollback on failure
                $error = "Accessory post failed. Please try again.";
            }
        } catch (PDOException $e) {
            $dbh->rollBack(); // Rollback on exception
            $error = "Database error: " . $e->getMessage();
        }
    }
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
    <title>Motorcycle Rental Portal | Post Your Accessory</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
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

    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        /* Custom styles for contact info section alignment */
        .contact-info-section .form-group .col-sm-1,
        .contact-info-section .form-group .col-sm-2,
        .contact-info-section .form-group .col-sm-3 {
            padding-right: 5px;
            /* Adjust padding to make columns closer */
            padding-left: 5px;
        }

        .contact-info-section .form-group .control-label {
            text-align: right;
            /* Align labels to the right */
        }

        @media (max-width: 767px) {
            .contact-info-section .form-group .control-label {
                text-align: left;
                /* On small screens, align labels to the left */
            }
        }
    </style>

</head>

<body>
    <?php include('includes/header.php'); ?>

    <section class="page-header profile_page">
        <div class="container">
            <div class="page-header_wrap">
                <div class="page-heading">
                    <h1>Post Your Accessory</h1>
                </div>
                <ul class="coustom-breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li>Post Your Accessory</li>
                </ul>
            </div>
        </div>
        <div class="dark-overlay"></div>
    </section>

    <div class="ts-main-content">
        <div class="content-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">

                        <h2 class="page-title">Post Your Accessory</h2>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Accessory Details</div>
                                    <?php if ($error) { ?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if ($msg) { ?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

                                    <div class="panel-body">
                                        <form method="post" class="form-horizontal" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Accessory Title<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <input type="text" name="title" class="form-control" required>
                                                </div>
                                                <label class="col-sm-2 control-label">Price (HKD)<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" name="price" class="form-control" step="0.01" required>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Accessory Overview<span style="color:red">*</span></label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="description" rows="3" required></textarea>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Condition<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <select class="selectpicker" name="condition" required>
                                                        <option value=""> Select </option>
                                                        <option value="New">New</option>
                                                        <option value="Used">Used</option>
                                                        <option value="Refurbished">Refurbished</option>
                                                    </select>
                                                </div>
                                                <label class="col-sm-2 control-label">Category Type<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <select class="selectpicker" name="subcategory_id" required>
                                                        <option value=""> Select Category </option>
                                                        <?php foreach ($accessory_subcategories as $subcat) { ?>
                                                            <option value="<?php echo htmlentities($subcat->subcategory_id); ?>"><?php echo htmlentities($subcat->subcategory_name); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Transaction Count</label>
                                                <div class="col-sm-4">
                                                    <input type="number" name="transaction_count" class="form-control" value="0" min="0">
                                                </div>
                                                <div class="col-sm-6"></div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="contact-info-section">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <h4><b>Additional Contact Methods (for this Accessory)</b></h4>
                                                        <p class="help-block">You can add multiple contact methods specific to this accessory listing.</p>
                                                    </div>
                                                </div>

                                                <div id="additional-contacts-container">
                                                    <div class="form-group additional-contact-item">
                                                        <label class="col-sm-2 control-label">Contact Type</label>
                                                        <div class="col-sm-3">
                                                            <select class="selectpicker" name="contact_type[]">
                                                                <option value="">Select Type</option>
                                                                <option value="Phone">Phone</option>
                                                                <option value="Email">Email</option>
                                                                <option value="WeChat">WeChat</option>
                                                                <option value="WhatsApp">WhatsApp</option>
                                                                <option value="Telegram">Telegram</option>
                                                                <option value="Line">Line</option>
                                                                <option value="Nickname">Nickname</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>
                                                        <label class="col-sm-2 control-label">Contact Value</label>
                                                        <div class="col-sm-3">
                                                            <input type="text" name="contact_value[]" class="form-control" placeholder="e.g., 23456789">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <input type="text" name="contact_description[]" class="form-control" placeholder="Description (e.g., WhatsApp only)">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-12 text-center">
                                                        <button type="button" class="btn btn-info" id="add-contact-method">Add More Contact Methods</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <h4><b>Upload Images (Max 3)</b></h4>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    Image 1 <span style="color:red">*</span><input type="file" name="img1" required>
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 2<input type="file" name="img2">
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 3<input type="file" name="img3">
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-2">
                                                    <button class="btn btn-default" type="reset">Cancel</button>
                                                    <button class="btn btn-primary" name="submit" type="submit">Post Accessory</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/fileinput.js"></script>
    <script src="js/chartData.js"></script>
    <script src="js/main.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize selectpicker
            $('.selectpicker').selectpicker();

            // Add more contact methods dynamically
            $('#add-contact-method').click(function() {
                var newContactHtml = `
                    <div class="form-group additional-contact-item">
                        <label class="col-sm-2 control-label">Contact Type</label>
                        <div class="col-sm-3">
                            <select class="selectpicker" name="contact_type[]">
                                <option value="">Select Type</option>
                                <option value="Phone">Phone</option>
                                <option value="Email">Email</option>
                                <option value="WeChat">WeChat</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Telegram">Telegram</option>
                                <option value="Line">Line</option>
                                <option value="Nickname">Nickname</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Contact Value</label>
                        <div class="col-sm-3">
                            <input type="text" name="contact_value[]" class="form-control" placeholder="e.g., 23456789">
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="contact_description[]" class="form-control" placeholder="Description (e.g., WhatsApp only)">
                        </div>
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" class="remove-contact-btn btn btn-danger btn-sm" style="margin-top: 5px;">Remove</button>
                        </div>
                    </div>
                `;
                $('#additional-contacts-container').append(newContactHtml);
                $('.selectpicker').selectpicker('refresh'); // Re-initialize selectpicker for new elements
                attachRemoveEventListeners(); // Attach event listeners to new remove buttons
            });

            // Function to attach remove event listeners
            function attachRemoveEventListeners() {
                $('.remove-contact-btn').off('click').on('click', function() {
                    $(this).closest('.additional-contact-item').remove();
                });
            }

            // Initial attachment for existing elements (if any, though not applicable for new post)
            attachRemoveEventListeners();
        });
    </script>
</body>

</html>
