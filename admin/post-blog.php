<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else
{
    if(isset($_POST['submit']))
    {
        $posttitle=$_POST['posttitle'];
        $postcontent=$_POST['postcontent'];
        $author=$_POST['author'];
        $postimage=$_FILES["postimage"]["name"];

        // Get the extension
        $extension = substr($postimage,strlen($postimage)-4,strlen($postimage));
        // Allowed extensions for upload images
        $allowed_extensions = array(".jpg","jpeg",".png",".gif");

        // Validation for allowed extensions
        if(!in_array($extension,$allowed_extensions))
        {
            $error="Invalid format. Only jpg / jpeg/ png /gif format allowed";
        } else {
            // Rename the image file
            $imgnewname = md5($postimage.time()).$extension; // 避免文件名重複
            move_uploaded_file($_FILES["postimage"]["tmp_name"],"img/blogimages/".$imgnewname);

            $sql="INSERT INTO tblblogposts(PostTitle,PostContent,PostImage,Author) VALUES(:posttitle,:postcontent,:postimage,:author)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':posttitle',$posttitle,PDO::PARAM_STR);
            $query->bindParam(':postcontent',$postcontent,PDO::PARAM_STR);
            $query->bindParam(':postimage',$imgnewname,PDO::PARAM_STR);
            $query->bindParam(':author',$author,PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();
            if($lastInsertId)
            {
                $msg="Blog Post Created successfully";
            }
            else
            {
                $error="Something went wrong. Please try again";
            }
        }
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

	<title>Bike Rental Portal | Admin Post Blog</title>

	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">
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
    </style>
</head>

<body>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Post New Blog Article</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Blog Article Info</div>
                                    <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php }
                                    else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
                                        <form method="post" class="form-horizontal" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Post Title<span style="color:red">*</span></label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="posttitle" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Post Content<span style="color:red">*</span></label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="postcontent" rows="8" required></textarea>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Author (Optional)</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="author" class="form-control">
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <h4><b>Upload Post Image</b></h4>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    Image <span style="color:red">*</span><input type="file" name="postimage" required>
                                                </div>
                                            </div>
                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-2">
                                                    <button class="btn btn-default" type="reset">Cancel</button>
                                                    <button class="btn btn-primary" name="submit" type="submit">Publish Post</button>
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

	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>

</body>
</html>
<?php } ?>