<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('includes/config.php');

// 1. User Authentication Check
if(strlen($_SESSION['login']) == 0) {
    header('location:index.php'); // Redirect to homepage or login if not logged in
    exit();
} else { // User is logged in

    $error = '';
    $msg = '';

    $accid = intval($_GET['id']); // Get accessory ID from URL (using 'id' as per my-accessories.php link)

    // Get the ID of the current logged-in user
    $loggedInUserEmail = $_SESSION['login'];
    $userIdForAuth = null;
    $sql_get_userid_auth = "SELECT id FROM tblusers WHERE EmailId = :email";
    $query_get_userid_auth = $dbh->prepare($sql_get_userid_auth);
    $query_get_userid_auth->bindParam(':email', $loggedInUserEmail, PDO::PARAM_STR);
    $query_get_userid_auth->execute();
    $user_row_auth = $query_get_userid_auth->fetch(PDO::FETCH_OBJ);
    if ($user_row_auth) {
        $userIdForAuth = $user_row_auth->id;
    } else {
        $error = "Error: Your user ID could not be determined. Please log in again."; // 錯誤：無法確定您的使用者 ID。請重新登入。
        exit(); // It's critical to exit here if user ID isn't found
    }

    // --- Handle "Delete Accessory" action ---
    if (isset($_POST['delete_accessory'])) {
        // SQL DELETE statement for tbl_accessories
        $sql_delete_accessory = "DELETE FROM tbl_accessories WHERE accessory_id = :accid AND user_id = :userid_check";
        $query_delete_accessory = $dbh->prepare($sql_delete_accessory);
        $query_delete_accessory->bindParam(':accid', $accid, PDO::PARAM_INT);
        $query_delete_accessory->bindParam(':userid_check', $userIdForAuth, PDO::PARAM_INT);
        try {
            $query_delete_accessory->execute();
            if ($query_delete_accessory->rowCount() > 0) {
                $msg = "Accessory successfully deleted!"; // 配件已成功刪除！
                // Redirect to my-accessories.php after deletion
                header('location: /bikerental/my-accessories.php?msg=' . urlencode($msg));
                exit();
            } else {
                $error = "Failed to delete accessory. Ensure you are the owner."; // 刪除配件失敗。請確認您是所有者。
            }
        } catch (PDOException $e) {
            $error = "Database error while deleting accessory: " . $e->getMessage(); // 刪除配件時發生資料庫錯誤：
        }
    }

    // --- 2. Process Form Submission (Save Changes) ---
    if(isset($_POST['submit'])) {
        // Get all form fields' values for accessory
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = floatval($_POST['price']);
        $condition = $_POST['condition'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $transaction_count = isset($_POST['transaction_count']) ? intval($_POST['transaction_count']) : 0;

        // User ID for the accessory is the logged-in user's ID
        $selectedUserId = $userIdForAuth;

        // SQL UPDATE statement for tbl_accessories
        $sql = "UPDATE tbl_accessories SET
                    title=:title,
                    description=:description,
                    price=:price,
                    `condition`=:condition,
                    is_active=:is_active,
                    transaction_count=:transaction_count
              WHERE accessory_id=:accid AND user_id=:userid_check"; // Ensure the user owns this accessory

        $query = $dbh->prepare($sql);
        // Parameter binding
        $query->bindParam(':title', $title, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':price', $price, PDO::PARAM_STR); // Use PDO::PARAM_STR for decimal
        $query->bindParam(':condition', $condition, PDO::PARAM_STR);
        $query->bindParam(':is_active', $is_active, PDO::PARAM_INT);
        $query->bindParam(':transaction_count', $transaction_count, PDO::PARAM_INT);
        $query->bindParam(':accid', $accid, PDO::PARAM_INT);
        $query->bindParam(':userid_check', $userIdForAuth, PDO::PARAM_INT); // Security check

        try {
            $query->execute();
            if ($query->rowCount() > 0) { // Check if any row was updated
                $msg = "Accessory data updated successfully!"; // 配件資料已成功更新！

                // --- Handle update of additional contact methods for this ACCESSORY ---
                // 1. Delete all contacts associated with this specific accessory from tbl_accessory_contacts
                $sql_delete_contacts = "DELETE FROM tbl_accessory_contacts WHERE accessory_id = :accid";
                $query_delete_contacts = $dbh->prepare($sql_delete_contacts);
                $query_delete_contacts->bindParam(':accid', $accid, PDO::PARAM_INT);
                $query_delete_contacts->execute();

                // 2. Insert new contact methods for this accessory
                if (isset($_POST['contact_type']) && is_array($_POST['contact_type'])) {
                    $contactTypes = $_POST['contact_type'];
                    $contactValues = $_POST['contact_value'];
                    $contactDescriptions = $_POST['contact_description'];

                    for ($i = 0; $i < count($contactTypes); $i++) {
                        $type = trim($contactTypes[$i]);
                        $value = trim($contactValues[$i]);
                        $description = trim($contactDescriptions[$i]);

                        if (!empty($type) && !empty($value)) {
                            $sql_insert_contact = "INSERT INTO tbl_accessory_contacts(accessory_id, contact_type, contact_value, description) VALUES(:accid, :contacttype, :contactvalue, :description)";
                            $query_insert_contact = $dbh->prepare($sql_insert_contact);
                            $query_insert_contact->bindParam(':accid', $accid, PDO::PARAM_INT);
                            $query_insert_contact->bindParam(':contacttype', $type, PDO::PARAM_STR);
                            $query_insert_contact->bindParam(':contactvalue', $value, PDO::PARAM_STR);
                            $query_insert_contact->bindParam(':description', $description, PDO::PARAM_STR);
                            $query_insert_contact->execute();
                        }
                    }
                }
            } else {
                 $error = "Accessory data could not be updated. Ensure you are the owner of this accessory."; // 配件資料無法更新。請確認您是此配件的所有者。
            }
        } catch (PDOException $e) {
            $error = "Database error during update: " . $e->getMessage(); // 更新時發生資料庫錯誤：
        }
    }

    // --- 3. Retrieve current accessory details, including owner info and additional contact methods ---
    $sql_accessory_data = "SELECT ta.*, tu.FullName, tu.EmailId as SellerEmail, tu.ContactNo as SellerPhone
                           FROM tbl_accessories ta
                           LEFT JOIN tblusers tu ON tu.id = ta.user_id
                           WHERE ta.accessory_id=:accid AND ta.user_id=:userid_check";
    $query_accessory_data = $dbh->prepare($sql_accessory_data);
    $query_accessory_data->bindParam(':accid', $accid, PDO::PARAM_INT);
    $query_accessory_data->bindParam(':userid_check', $userIdForAuth, PDO::PARAM_INT);
    $query_accessory_data->execute();
    $accessory_data = $query_accessory_data->fetch(PDO::FETCH_OBJ);

    // If accessory data is not found or user is not authorized, redirect
    if (!$accessory_data) {
        header('location: my-accessories.php'); // Or a suitable "Access Denied" page
        exit();
    }

    // Fetch current accessory-specific contacts (from tbl_accessory_contacts)
    $current_contacts = [];
    if ($accid) {
        $sql_current_contacts = "SELECT contact_type, contact_value, description FROM tbl_accessory_contacts WHERE accessory_id = :accid";
        $query_current_contacts = $dbh->prepare($sql_current_contacts);
        $query_current_contacts->bindParam(':accid', $accid, PDO::PARAM_INT);
        $query_current_contacts->execute();
        $current_contacts = $query_current_contacts->fetchAll(PDO::FETCH_OBJ);
    }
?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">

	<title>Motorcycle Rental Portal | Edit My Accessory</title>

	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-social.css">
	<link rel="stylesheet" href="assets/css/bootstrap-select.css">
	<link rel="stylesheet" href="assets/css/fileinput.min.css">
	<link rel="stylesheet" href="assets/css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="assets/css/styles.css">
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
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap{
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
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
        /* Styling for the remove button */
        .remove-contact-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
            font-size: 0.9em;
        }
        .remove-contact-btn:hover {
            background-color: #c82333;
        }
	</style>
</head>

<body>
	<?php include('includes/header.php');?>
    <?php include('includes/colorswitcher.php');?>


	<section class="page-header profile_page">
        <div class="container">
            <div class="page-header_wrap">
                <div class="page-heading">
                    <h1>Edit My Accessory</h1>
                </div>
                <ul class="coustom-breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li><a href="my-accessories.php">My Accessories</a></li>
                    <li>Edit Accessory</li>
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

						<h2 class="page-title">Edit Accessory Details</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Basic Info</div>
									<div class="panel-body">
    <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

    <?php
    if($accessory_data) { // If accessory data is found
    ?>

    <form method="post" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
            <label class="col-sm-2 control-label">Accessory Title<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="text" name="title" class="form-control" value="<?php echo htmlentities($accessory_data->title)?>" required>
            </div>
            <label class="col-sm-2 control-label">Condition<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="condition" required>
                    <option value="<?php echo htmlentities($accessory_data->condition);?>"><?php echo htmlentities($accessory_data->condition); ?> </option>
                    <?php
                    $conditions = ['全新', '二手', '翻新', '損壞']; // Define possible conditions
                    foreach($conditions as $cond) {
                        if($cond == $accessory_data->condition) {
                            continue;
                        } else {
                    ?>
                    <option value="<?php echo htmlentities($cond);?>"><?php echo htmlentities($cond);?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Accessory Description<span style="color:red">*</span></label>
            <div class="col-sm-10">
                <textarea class="form-control" name="description" rows="3" required><?php echo htmlentities($accessory_data->description);?></textarea>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Price (HKD)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="price" class="form-control" value="<?php echo htmlentities($accessory_data->price);?>" required step="0.01">
            </div>
            <label class="col-sm-2 control-label">Is Active</label>
            <div class="col-sm-4">
                <div class="checkbox checkbox-inline">
                    <input type="checkbox" id="is_active" name="is_active" value="1" <?php if($accessory_data->is_active == 1) echo 'checked'; ?>>
                    <label for="is_active"> Active </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Transaction Count</label>
            <div class="col-sm-4">
                <input type="number" name="transaction_count" class="form-control" value="<?php echo htmlentities($accessory_data->transaction_count);?>" min="0">
            </div>
            <div class="col-sm-6"></div>
        </div>
        <div class="hr-dashed"></div>

        <div class="contact-info-section">
            <div class="form-group">
                <div class="col-sm-12">
                    <h4><b>Additional Contact Methods (for this Accessory)</b></h4>
                    <p class="help-block">Add or update contact methods specific to this accessory listing.</p>
                </div>
            </div>

            <div id="additional-contacts-container">
                <?php if (!empty($current_contacts)) {
                    foreach ($current_contacts as $contact) { ?>
                    <div class="form-group additional-contact-item">
                        <label class="col-sm-2 control-label">Contact Type</label>
                        <div class="col-sm-3">
                            <select class="selectpicker" name="contact_type[]">
                                <option value="<?php echo htmlentities($contact->contact_type); ?>" selected><?php echo htmlentities($contact->contact_type); ?></option>
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
                        <label class="col-sm-2 control-label">Value</label>
                        <div class="col-sm-3">
                            <input type="text" name="contact_value[]" class="form-control" value="<?php echo htmlentities($contact->contact_value); ?>">
                        </div>
                        <label class="col-sm-1 control-label">Description</label>
                        <div class="col-sm-1">
                            <input type="text" name="contact_description[]" class="form-control" value="<?php echo htmlentities($contact->description); ?>">
                        </div>
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" class="remove-contact-btn">Remove</button>
                        </div>
                    </div>
                <?php }
                } else { ?>
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
                        <label class="col-sm-2 control-label">Value</label>
                        <div class="col-sm-3">
                            <input type="text" name="contact_value[]" class="form-control">
                        </div>
                        <label class="col-sm-1 control-label">Description</label>
                        <div class="col-sm-1">
                            <input type="text" name="contact_description[]" class="form-control">
                        </div>
                    </div>
                <?php } ?>
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
                <h4><b>Accessory Images</b></h4>
                <p class="help-block">Images must be changed individually.</p>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                Image 1 <br>
                <?php if (!empty($accessory_data->image_url1)) { ?>
                    <img src="admin/img/accessoryimages/<?php echo htmlentities($accessory_data->image_url1);?>" width="200" height="150" style="border:solid 1px #000">
                    <a href="change-accessory-image1.php?imgid=<?php echo htmlentities($accessory_data->accessory_id)?>">Change Image 1</a>
                <?php } else { ?>
                    No Image 1
                    <a href="change-accessory-image1.php?imgid=<?php echo htmlentities($accessory_data->accessory_id)?>">Add Image 1</a>
                <?php } ?>
            </div>
            <div class="col-sm-4">
                Image 2<br>
                <?php if (!empty($accessory_data->image_url2)) { ?>
                    <img src="admin/img/accessoryimages/<?php echo htmlentities($accessory_data->image_url2);?>" width="200" height="150" style="border:solid 1px #000">
                    <a href="change-accessory-image2.php?imgid=<?php echo htmlentities($accessory_data->accessory_id)?>">Change Image 2</a>
                <?php } else { ?>
                    No Image 2
                    <a href="change-accessory-image2.php?imgid=<?php echo htmlentities($accessory_data->accessory_id)?>">Add Image 2</a>
                <?php } ?>
            </div>
            <div class="col-sm-4">
                Image 3<br>
                <?php if (!empty($accessory_data->image_url3)) { ?>
                    <img src="admin/img/accessoryimages/<?php echo htmlentities($accessory_data->image_url3);?>" width="200" height="150" style="border:solid 1px #000">
                    <a href="change-accessory-image3.php?imgid=<?php echo htmlentities($accessory_data->accessory_id)?>">Change Image 3</a>
                <?php } else { ?>
                    No Image 3
                    <a href="change-accessory-image3.php?imgid=<?php echo htmlentities($accessory_data->accessory_id)?>">Add Image 3</a>
                <?php } ?>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2" >
                <button class="btn btn-primary" name="submit" type="submit" style="margin-top:4%">Save Changes</button>
                <button class="btn btn-danger" name="delete_accessory" type="submit" style="margin-top:4%; margin-left:10px;" onclick="return confirm('Are you sure you want to PERMANENTLY delete this accessory? This action cannot be undone.');">Delete My Accessory</button>
            </div>
        </div>

    </form>
    <?php
    } else { // If no accessory data is found
        echo "<div class='container-fluid'><div class='row'><div class='col-md-12'><h3 class='text-center'>Error: Accessory not found or you are not authorized to edit it.</h3></div></div></div>";
    }
    ?>
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
                            <label class="col-sm-2 control-label">Value</label>
                            <div class="col-sm-3">
                                <input type="text" name="contact_value[]" class="form-control" placeholder="e.g., 23456789">
                            </div>
                            <label class="col-sm-1 control-label">Description</label>
                            <div class="col-sm-1">
                                <input type="text" name="contact_description[]" class="form-control" placeholder="e.g., WhatsApp only">
                            </div>
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="remove-contact-btn">Remove</button>
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

                // Initial attachment for existing elements
                attachRemoveEventListeners();
            });
        </script>
    </body>
</html>
<?php } ?>
