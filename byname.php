<?php

	$error=null;
	

	if( empty($_POST['name']) )
	{
		$error="Please enter a Name";
		header("location: index.php?errorType=2&error=".$error);
		die();
	}

	//Input validation
	if(empty($_POST['year']) || ((int)$_POST['year'] < 1944 ) || ((int)$_POST['year'] > 2013 ) )
	{
		$error=" enter a Year between 1944 to 2013";
		header("location: index.php?errorType=2&error=".$error);
		die();
	}

	//Set the year
	$year=(int)$_POST['year'];
	$name=$_POST['name'];
	$name_upper=strtoupper($_POST['name']);
	$gender=$_POST['gender'];

	$nameList=array(); //Holds the matched names incase its a partial search

	for($i=$year;$i<2014;$i++)
	{
		$fh=fopen("names/".$gender."_cy".$i."_top.csv","r");

		while(!feof($fh))
		{
			$record=fgetcsv($fh);

			if(strpos($record[0],$name_upper) === 0 )
			{
					if(empty($nameList[$record[0]]))
					{
						$nameList[$record[0]]=array();
					}
					$record[2]=(int)preg_replace("/[^0-9]/", "", $record[2]);
					$nameList[$record[0]][$i]=array($record[1],$record[2]);
			}

		}
		fclose($fh);
	}


	//Make bar graphs for the names
	foreach($nameList as $_name=>$data)
	{
		$amounts=array();
		foreach($data as $val)
		{
			$amounts[]=$val[0];
		}
		$nameList[$_name]['graph']=makeGraph($amounts);
	}

?>


<html>

	<head>
		<title>:: Names::</title>
		<link rel='stylesheet' href='style.css' />
	</head>

	<body>

	<h1 align="center" style="margin:10px;color:#b0eoe6">
		<span style="float:left;font-size:18px" >
			<a href='index.php' style=";color:#800080" > Home </a>
		</span>
		LIST OF BABY NAMES	</h1>
	<hr style="width:40%">

	<table width='99%' style="margin:auto;padding:20px;box-sizing:border-box" border=0>

	<tr>
		<td colspan=2 align="left" style="color:#eee;font-size:16px;font-weight:bold">
			Showing popularity change for names starting with '<?php echo $name; ?>' 
		</td>
	</tr>

	 <tr>

	 	<td width="50%" align="center" style="padding-right:20px;vertical-align:top">
	 		<div style="background-color:#800080;padding:10px;font-size:18px;font-weight:bold;border:solid #800080 2px">
	 			Name Popularity Change
	 		</div>

	 		<?php
	 			foreach($nameList as $name=>$record )
	 			{
	 				$img=$record['graph'];
	 				unset($record['graph']);
	 		?>
		 		<div style="background-color:#fff;padding:20px 10px;font-size:16px;font-weight:bold;border:solid #222 1px">
		 			
		 			<div style="margin:10px;background-color:#eee;border-radius:10px;padding:10px;border:solid #222 1px;text-align:left;color:#233">
		 				<span style='color:#666'>Name:</span> <?php echo $name; ?>
		 				<hr>
		 				<table width="100%">
			 				<tr>
			 					<td align="left" style="vertical-align:top;font-size:15px;color:#227">
			 						
			 						<table align="left" style="color:#a0522d;padding:10px;width:90%;border:solid #777 2px">
			 							<tr>
			 								<td colspan="3" align="center">
			 									<h4 style="margin:0">Change in Rank</h4>
			 									<hr>
			 								</td>
			 							</tr>
			 							<tr>
			 								<td align="center" width="33%">
			 									<u>Year</u>
			 								</td>
			 								<td align="center" width="33%">
			 									<u>Rank</u>
			 								</td>
			 								<td align="center" width="34%">
			 									<u>Number Of Babies</u>
			 								</td>
			 							</tr>
			 							<?php
			 								foreach($record as $year=>$data)
			 								{
			 							?>
			 								<tr>
				 								<td align="center">
				 									<?php echo $year; ?>
				 								</td>
				 								<td align="center">
				 									<?php echo $data[1]; ?>
				 								</td>
				 								<td align="center">
				 									<?php echo $data[0]; ?>
				 								</td>
				 							</tr>
			 							<?php
			 								}
			 							?>
			 						</table>
			 					</td>
			 					<td  align="center" >
			 						<?php echo "<img src='data:image/jpeg;base64,".$img."' />"; ?>
			 						
			 						<div style="margin:10px;color:#669">
			 							Popularity Graph
			 						</div>
			 					</td>
			 				</tr>
			 				<tr>
			 					<td align="center" style="font-size:15px;color:#227" colspan="3">
			 						<hr>
			 						Data available for years:&nbsp;<?php echo implode(",",array_keys($record)); ?>
			 					</td>
			 				</tr>
		 				</table>
		 			</div>
		 			
		 		</div>
		 	<?php
		 		}
		 	?>
	 	</td>
	 </tr>

	</table>

	</body>

</html>	


<?php
function makeGraph($values)
{
	ob_start();

// Get the total number of columns we are going to plot

    $columns  = count($values);

// Get the height and width of the final image

    $width = 300;
    $height = 200;

// Set the amount of space between each column

    $padding = 5;

// Get the width of 1 column

    $column_width = $width / $columns ;

// Generate the image variables

    $im        = imagecreate($width,$height);
    $gray      = imagecolorallocate ($im,0xcc,0xcc,0xcc);
    $gray_lite = imagecolorallocate ($im,0xee,0xee,0xee);
    $gray_dark = imagecolorallocate ($im,0x7f,0x7f,0x7f);
    $white     = imagecolorallocate ($im,0xff,0xff,0xff);
    
// Fill in the background of the image

    imagefilledrectangle($im,0,0,$width,$height,$white);
    
    $maxv = 0;

// Calculate the maximum value we are going to plot

    for($i=0;$i<$columns;$i++)$maxv = max($values[$i],$maxv);

// Now plot each column
        
    for($i=0;$i<$columns;$i++)
    {
        $column_height = ($height / 100) * (( $values[$i] / $maxv) *100);

        $x1 = $i*$column_width;
        $y1 = $height-$column_height;
        $x2 = (($i+1)*$column_width)-$padding;
        $y2 = $height;

        imagefilledrectangle($im,$x1,$y1,$x2,$y2,$gray);

// This part is just for 3D effect

        imageline($im,$x1,$y1,$x1,$y2,$gray_lite);
        imageline($im,$x1,$y2,$x2,$y2,$gray_lite);
        imageline($im,$x2,$y1,$x2,$y2,$gray_dark);

    }

// Send the PNG header information. Replace for JPEG or GIF or whatever
    imagejpeg($im,NULL,100);

    $img=ob_get_clean();
    $img=base64_encode($img);

    return $img;

}

?>


