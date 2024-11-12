<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php

	$basePathName = 'test/Orfeo';
	$basePathName2 = 'test/Orfeo1';
	$basePathName3 = 'test/Orfeo2';
    $basePathNameExt = '.pdf';
    $targetPath = $basePathName .  "-" . time() .$basePathNameExt;
	$targetPath2 = $basePathName2 .  "-" . time() .$basePathNameExt;
	$targetPath3 = $basePathName3 .  "-" . time() .$basePathNameExt;
	
	if (isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
		$name = $_REQUEST['name'];
		
		copy($basePathName . $basePathNameExt, $targetPath);
		copy($basePathName2 . $basePathNameExt, $targetPath2);
		copy($basePathName3 . $basePathNameExt, $targetPath3);
		$absolutePath = realpath ($targetPath) . "|" . realpath ($targetPath2). "|" . realpath ($targetPath3);
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Software Colombia - 2017-2018</title>

        <link href="css/bootstrap.min.css" rel="stylesheet"/>
        <link href="css/bootstrap-grid.min.css" rel="stylesheet"/>
        <link href="css/bootstrap-reboot.min.css" rel="stylesheet"/>
        <link href="css/font-awesome.min.css" rel="stylesheet"/>
	</head>

	<body>

    <?php
        if (!isset($id))
        {
    ?>
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="card text-center" style="width: 30rem;">

                        <div class="card-header">
                            Orfeo digital signature test form
                        </div>

                        <div class="card-body">
                            <form name="test" id="test" action="index.php" method="post">
                                <div class="form-group">
                                    <label for="id">Identification number:</label>
                                    <input type="text" name="id" id="id" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label for="id">Common name:</label>
                                    <input type="text" name="name" id="name" class="form-control"/>

                                </div>
                                <button type="submit" class="btn btn-default">Sign and send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
        else
        {
    ?>
	
	<iframe frameborder="0" scrolling="auto" src="signature.php?signerId=<?=$id?>&signerCommonName=<?=$name?>&file=<?= $absolutePath ?>" width="100%" height="500"></iframe>
	
	<!--
	<iframe frameborder="0" scrolling="auto" src="signature.php?signerId=<?=$id?>&signerCommonName=<?=$name?>&file=<?= $absolutePath ?>&timestampFlag=<?= "true" ?>&visible=<?= "true" ?>&lowerLeftX=<?= "5" ?>&lowerLeftY=<?= "169" ?>&upperRightX=<?= "75" ?>&upperRightY=<?= "667" ?>&qrCodeSize=<?= "200" ?>&qrCodeVisibility=<?= "true" ?>&page=<?= "1" ?>&placeholder=<?= "[firma]" ?>" width="100%" height="500"></iframe>
	-->
	
    <?php
        }
    ?>
	</body>
</html>
