<?php
set_time_limit(0);
require_once ('../QueryProcessor.php');

//----------------Edit this lines----------------------------------
$FOLDER_PATH = 'C:\xampp\htdocs\lab04\example\files';
$INVERTED_INDEX_PATH = 'C:\xampp\htdocs\lab04\example\invertedIndex.dat';
//----------------Edit this lines----------------------------------

//compare function to use on uasort
function compareSize($a,$b){
	return sizeof($b) - sizeof($a);
}
	
if (isset($_POST['and']) || isset($_POST['or'])) {
	$fullPath = $FOLDER_PATH . '\\';
	if (!file_exists('invertedIndex.dat')) {
		//Creates a inverted index
		$files = QueryProcessor::listDirectoryFiles($FOLDER_PATH);
		$words = QueryProcessor::countSameWords($files, $fullPath);
		file_put_contents('invertedIndex.dat', serialize($words));
	}
	//Loads the inverted index for memory and makes the query
	$invertedIndex = unserialize(file_get_contents($INVERTED_INDEX_PATH));
	$fileList = isset($_POST['and']) ? QueryProcessor::processQuery(strtoupper($_POST['term']), $invertedIndex, 'and') : QueryProcessor::processQuery(strtoupper($_POST['term']), $invertedIndex, 'or');
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Finder</title>
<link rel="stylesheet" href="styles.css">
<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>
<body>
<section id="wrapper">
	<h1>Finder</h1>
	<div id="main">
		<form method="post">
			<input type="text" id="term" name="term" value="<?php echo isset($_POST['term']) ? $_POST['term'] : ''; ?>">
			<input type="submit" class="solid" value="SEARCH" id="and" name="and">
		</form>
	</div>
	<!--main-->
	<?php if (!empty($fileList)) : ?>
	<?php 
	uasort($fileList, 'compareSize'); 
	$FOLDER_PATH = 'C:\xampp\htdocs\lab04\example\files';
	$files = QueryProcessor::listDirectoryFiles($FOLDER_PATH);
	?>
	<div class="resultBox">
		<p><em>Results for: <?php echo strtoupper($_POST['term']); ?></em></p>
		<ul>
			<?php foreach ($fileList as $k => $v) : ?>
			<li><a href="files/<?php echo $files[$k]; ?>" target="_blank"><strong><?php echo $files[$k]; ?></strong></a> - Score <?php echo sizeof($v); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php else: ?>
		<?php if (isset($_POST['term'])) : ?>
		<div class="resultBox">
			<p><em>Not found for: <?php echo strtoupper($_POST['term']); ?></em></p>
		</div>
		<?php endif; ?>
	<?php endif; ?>
</section>
<!--wrapper-->
</body>
</html>