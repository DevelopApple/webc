<?php
$magicword = "changeme"; //This lists all apps
$applicationsfolder = "applications/";

//////////////////////////////////////////////////////////////
$appstolist = preg_split("/,/", $_GET["apps"]);
require_once("libraries/load.php");
require_once("functions.php");
require_once("groups.php");
if (isset($_GET["plist"])) {
	generateiOSPlist($_GET["plist"]);
	exit();
}
$appsFolders = scanDirBlacklist($applicationsfolder, array(".", ".."));
$count = 0;
$name = "";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Unc Inc App Installer</title>
        <meta name="viewport" content="width=device-width" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </head>
    <body class="browser-desktop">
        <div id="container" class="container">
                           <div class='new-ios'>

                    <h1>Install Apps</h1>

                    <p>If installation of an application fails, your device might not have been approved yet.</p>
                   <p class="bordertop"></p>
                    </div>
                    <div style='clear:both;'><br/></div>
                    <?php if (true) {
                    		//TODO, hide on Android
                     ?>
        <div class="row">
        <h2>iOS Apps</h2>
            <?php if (disableIfNotiDevice() == "disabled") {?>
        <p>Please visit this page on your smart device to start the installation.</p>
<?php }

foreach (getiOSApps($appsFolders, $applicationsfolder) as $app) :
if(in_array($app["bundle"], $appstolist) || in_array($magicword, $appstolist)) {
    $count = $count + 1;
    if($count == 3) {
        echo '</div><div class="row">';
        $count = 0;
    }
    $name = $app["name"];
?>
		
		
		<?php
//******************************************************************************
//check if any errors occurred during the upload.
$isDocUploaded = true; //initialise flag that says whether jpg was uploaded
$errorsOccurred = false;
 
echo '<div style="font-family: tahoma, sans-serif; font-size: 10pt">';
 
foreach($_FILES['txtUploadFile']['error'] as $key => $value) {
    if($_FILES['txtUploadFile']['name'][$key] != '') {
        echo '<p><hr />Uploading File: '.$_FILES['txtUploadFile']['name'][$key].'</p>';
        if($value > 0) {
            $isDocUploaded = false; //attachment was not submitted or an upload error occurred
            $errorsOccurred = true;
            switch($value) {    //check which error code was returned
                case 1:
                    echo '<p style="color: rgb(178,34,34)">** Error: Attachment file is larger than allowed by the server.</p>';
                    break;
                case 2:
                    echo '<p style="color: rgb(178,34,34)">** Error: Attachment file is larger than the maximum allowed - 100kb.</p>';
                    break;
                case 3:
                    echo '<p style="color: rgb(178,34,34)">** Error: Attachment file was only partially uploaded.</p>';
                    break;
                //case 4: echo 'No attachment file was uploaded. '; break;
            }
        }
 
        //if an attachment document was uploaded, move the file to desired folder
        if($isDocUploaded && $_FILES['txtUploadFile']['name'][$key] != '') {
        
            $uploadedFile = '../images/'.$_FILES['txtUploadFile']['name'][$key];
            if(@is_uploaded_file($_FILES['txtUploadFile']['tmp_name'][$key])) //initial temp location of uploaded file
            {
                if(@!move_uploaded_file($_FILES['txtUploadFile']['tmp_name'][$key], $uploadedFile)) { //move the file to images folder
                    echo '<p style="color: rgb(178,34,34)"><br />**Error**: Could not move '.$_FILES['txtUploadFile']['name'][$key].' to the destination folder on the server.<br /></p>';
                    $errorsOccurred = true;
                } else {    //file was uploaded successfully
                    echo '<p style="color: rgb(0,128,0)"> File: '.$_FILES['txtUploadFile']['name'][$key].' was uploaded successfully</p>';
                }
            } else {
                echo '<p style="color: rgb(178,34,34)">Error: '.$_FILES['txtUploadFile']['name'][$key].' was not uploaded correctly to temp area.</p>';
                $errorsOccurred = true;
            }
        }
        $isDocUploaded = true;  //reset to true for the next document
    }
}   //end of Main foreach
 
$message = "\r\nMVC Uploader - New files were uploaded - with UNKNOWN errors";
if($errorsOccurred) {
    echo '<p><input type="button" value="Go back" onclick="window.history.back();" />'.
        '<a href="displayImages.php">Display Uploaded Files</a></p>';
    $message = "\r\nMVC Uploader - New files were uploaded - with errors";
} else {    //at this point, the file was uploaded successfully
    echo '<script type="text/javascript">window.location.href="displayImages.php"</script>';
    $message = "\r\nMVC Uploader - New files were uploaded - without errors";
}
 
echo '</div>';
 
?>
        <div class="col-sm-4">
                    <div class="version">
                        <p class="borderbottom"></p>
                        <h2><?php echo $app["name"]." (".$app["version"].")"; ?></h2>
                        <h5><?php echo $app["bundle"]; ?></h5>
                        <div style='clear:both;'></div>
                        <a class="btn btn-info <?php echo disableIfNotiDevice();?>" role="button" href="itms-services://?action=download-manifest&amp;url=<?php echo urlencode("https://apps.uncinc.nl/?plist=".$app["filepath"]) ?>">Install Application</a>
                    </div>
        </div>
<?php }
endforeach;
if ($count == 2) {
    echo "</div>";
}
}
if (disableIfNotiDevice() == "disabled") {
?>
        </div><div class="row">
        <hr>
        <h2>Android Apps</h2>
<?php
$count = 0;
    foreach (getAndroidApps($appsFolders, $applicationsfolder) as $app) :
        if(in_array($app["bundle"], $appstolist) || in_array($magicword, $appstolist)) {
            $count = $count + 1;
            if($count == 3) {
                echo '</div><div class="row">';
                $count = 0;
            }
            $name = $app["name"];
?>
        <div class="col-sm-4">
                    <div class="version">
                        <p class="borderbottom"></p>
                        <h2><?php echo $app["name"]." (".$app["version"].")"; ?></h2>
                        <h5><?php echo $app["bundle"]; ?></h5>
                        <div style='clear:both;'></div>
                        <a class="btn btn-info" role="button" href="<?php echo "https://apps.uncinc.nl/".$app["filepath"]; ?>">Download Application</a>
                    </div>
        </div>
<?php }
endforeach;
if ($count == 2) {
    echo "</div>";
}
}?>

                </div>
              </body>
</html>
