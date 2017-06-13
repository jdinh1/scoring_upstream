<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>UpStream</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="styles.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<div id="content">
<div id="back">
<!-- header begins -->
<div id="header"> 
	 <div id="menu">
		<ul>
			<li><a href="index.php"  title="">Home</a></li>
			<li><a href="scores.php" title="">Scores</a></li>
			<li><a href="images.php" title="">Gallery</a></li>
			<li><a href="about.html" title="">About</a></li>
			<li><a href="game.html" title="">Frog Click</a></li>
		</ul>
	</div>
	<div id="logo">
		<h1><a href="#">UpStream for Android!</a></h1>
		<h2></a></h2>
	</div>	
</div>
<!-- header ends -->
<!-- content begins -->
 <div id="main">
	<div id="right">
		<h2>HIGH SCORE LEADERBOARD</h2><br />
			<h4>UpStream</h4><br />
			<p>
<?php

// ++++SESSION+++++++++++++++++++++++++++++++++++++++++++
if(isset($_POST['sess_id']))
{
session_id($_POST['sess_id']); //starts session with given session id
//session_start();
$_SESSION['count']++;
}
else {
//session_start(); //starts a new session
$_SESSION['count']=0;
}
echo session_id();
// ======================================================
// get new entry
$param = $_GET['param'];
//echo "param is: " . $param . "<br>";
//put into array
$params = explode(',', $param);

// ============SORT==========================================
function array_sort_by_column(&$array, $column, $direction = SORT_DESC) {
    $reference_array = array();

    foreach($array as $key => $row) {
        $reference_array[$key] = $row[$column];
    }

    array_multisort($reference_array, $direction, $array);
}
// =================================================
//read in xml data
if (file_exists('androidHS.xml')) {
    $xml = simplexml_load_file('androidHS.xml');
//echo var_dump($xml);

foreach($xml->player as $playerElement) {
	//echo (string)$playerElement->mode;
	//echo "<br> ";
	if(intval($playerElement->mode)==1){
		$playerElement->mode="easy";
	}
	if(intval($playerElement->mode)==2){
		$playerElement->mode="medium";
		$playerElement->score = $playerElement->score;
	}
	if(intval($playerElement->mode)==3){
		$playerElement->mode="hard";
		$playerElement->score = $playerElement->score;
	}
	  $playerList[] = array(
                     'name'     => (string)$playerElement->name,
                     'score'    => intval($playerElement->score),
                     'mode'     => (string)$playerElement->mode,
                     );
	}
if($params[0]=='Androidupstream54321')
{
	array_push($playerList, array('name' => $params[1],
							'score' => intval($params[2]),
							'mode' =>  $params[3]	)	);
echo 'updated';
}
// ===================================================
array_sort_by_column($playerList, 'mode'); //sort by mode
// =====================================================
// find lowest value for each mode 
$easy = 99999999;
$medium = 999999999;
$hard = 9999999999;

foreach ($playerList as $key => $val) {
    if ( $playerList[$key]['mode'] == 'easy' && 
			$playerList[$key]['score'] < $easy ){
			$easy = $playerList[$key]['score'];
			}
	if ( $playerList[$key]['mode'] == 'hard' && 
			$playerList[$key]['score'] < $hard ){
			$hard = $playerList[$key]['score'];
			}
	if ( $playerList[$key]['mode'] == 'medium' && 
			$playerList[$key]['score'] < $medium ){
			$medium = $playerList[$key]['score'];
			}
	if ( $playerList[$key]['mode'] == '' && 
			$playerList[$key]['score'] < $medium ){
			$nomode = $playerList[$key]['score'];
			}		
}
if($easy>=9999999)
	$easy=0;
if($medium>=9999999)
	$medium=0;
if($hard>=9999999)
	$hard=0;
//adjust scores 
foreach ($playerList as $key => $val) {
    if ( $playerList[$key]['mode'] == 'easy'){
			$playerList[$key]['score'] = $playerList[$key]['score'];
			}
	if ( $playerList[$key]['mode'] == 'hard' ){
			$playerList[$key]['score'] = $playerList[$key]['score']*3;
			}
	if ( $playerList[$key]['mode'] == 'medium' ){
			$playerList[$key]['score'] = $playerList[$key]['score']*2;
			}
	if ( $playerList[$key]['mode'] == 'nomode' ){
			$playerList[$key]['score'] = $playerList[$key]['score']*2;
			}		
}
// =============================================================
// write lowest values to text file
$text_file = 'androidlowScore.xml';
$lowScores .= "<easy> \n";
$lowScores .= $easy ;
$lowScores .= ",1 \n </easy>";
$lowScores .= "\n <medium> \n";
$lowScores .= $medium;
$lowScores .= ",2 \n </medium>";
$lowScores .= " \n <hard> \n" ;
$lowScores .= $hard;
$lowScores .= ",3 \n </hard> \n\n";
$lowScores .= "<nomode> \n";
$lowScores .= $nomode ;
$lowScores .= ",1 \n </nomode>";
// Write the contents to the file
file_put_contents($text_file, $lowScores);
	//echo $hard; ?> <br> <?php
	//echo $medium; ?> <br> <?php
	//echo $easy; ?> <br> <?php

// ===================================================
array_sort_by_column($playerList, 'score'); //sort by score


//echo var_dump($playerList);
//
//<br/><hr><br />
//

// ===================================================

//slice off excess players
$playerList = array_slice($playerList, 0, 20);   // first 20

//adjust scores back
foreach ($playerList as $key => $val) {
    if ( $playerList[$key]['mode'] == 'easy'){
			$playerList[$key]['score'] = $playerList[$key]['score'];
			}
	if ( $playerList[$key]['mode'] == 'hard' ){
			$playerList[$key]['score'] = $playerList[$key]['score']/3;
			}
	if ( $playerList[$key]['mode'] == 'medium' ){
			$playerList[$key]['score'] = $playerList[$key]['score']/2;
			}
}
// ===================================================
//put back in xml format
function to_xml(SimpleXMLElement $object, array $data)
{
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $new_object = $object->addChild($key);
            to_xml($new_object, $value);
        } else {
            $object->addChild($key, $value);
        }
    }
}
// ======================================================
  $doc = new DOMDocument();
  $doc->formatOutput = true;

  $r = $doc->createElement( "players" );
  $doc->appendChild( $r );

  foreach( $playerList as $player )
  {
  $b = $doc->createElement( "player" );

  $name = $doc->createElement( "name" );
  $name->appendChild(
  $doc->createTextNode( $player['name'] )
  );
  $b->appendChild( $name );

  $score = $doc->createElement( "score" );
  $score->appendChild(
  $doc->createTextNode( $player['score'] )
  );
  $b->appendChild( $score );

  $mode = $doc->createElement( "mode" );
  $mode->appendChild(
  $doc->createTextNode( $player['mode'] )
  );
  $b->appendChild( $mode );

  $r->appendChild( $b );
  }

 // echo $doc->saveXML();
  $doc->save("androidHS.xml");
}
// =======================================================
//$xml = new SimpleXMLElement('<player/>');
//to_xml($xml, $playerList);
//echo var_dump($xml);


//read in xml data
//if (file_exists('androidHS.xml')) {
//    $xml = simplexml_load_file('androidHS.xml');
if (file_exists('highscores.json')) {
    $data = json_decode(file_get_contents('highscores.json'));


?>
<div id ="score">
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>score</th>
      <th>Mode</th>
     </tr>
  </thead>
  <tbody>

<?php foreach ($data->players->player as $playerElement) :?>
    <tr>
      <td><?php echo $playerElement->name; ?></td>
      <td><?php echo $playerElement->score; ?></td>
      <td><?php echo $playerElement->mode; ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
</div>
<?php }  ?>



</p>
	<br />
          <br />
		<p></p>
			<h4><a href="#"> </a></h4><br />
			<p></p>
				</div>
	<div id="left">
		<canvas id="frogAnimation"></canvas>
        <script src="frog-animation.js"></script>
			<br />
			<h3>Champion Players </h3>
			<ul>
				<li><ul>
					<li><a href="#"> </a></li>
					<li><a href="#"> </a></li>
					<li><a href="#"> </a></li>
					<li><a href="#"> </a></li>
					</ul>
			  </li>
			</ul>
			<br />
	</div>

<!--content ends -->
<!--
		// -->
<!--footer begins -->
	</div>

<div id="footer">
	</div>
</div>
</div>
<!-- footer ends-->
</body>
</html>
