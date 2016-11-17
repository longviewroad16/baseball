<html>
<head>
  <title>Baseball Simulation</title>
<?php
/* 
 * This is a server-side baseball simulation written in PHP.
 * The game includes:
 * r0: Strikeouts, Groundouts and Flyouts with Sacrifice
 * r0: Walk, Single, Double, Triple, and Home Run
 * r0: Extra innings (if necessary)
 * r0: Line score after each inning
 * r1: Game over (partial inning)if Home team ahead after 9+ innings
 */
?>
</head>
<body>
<FONT FACE="Arial, Helvetica, Sans Serif" SIZE="-4">
<?php
$AtBats = array("Walk", "Single", "Double", 
			"Triple", "Home Run", "Strikeout", 
			"Flyout....", "Groundout");
$theOuts = array("Strikeout", "Flyout....", "Groundout");
$v_inn = 0;
$h_inn = 0;
$v_total = 0;
$h_total = 0;
$v_score = array(0);
$h_score = array(0);
$game_over = 0;
$inning = 1;

while ($game_over == 0) {
// echo "--------- ", $v_total, $h_total;
//   echo "> ", $inning, $v_inn, $h_inn, "<br>";
   list($v_runs, $v_hits, $v_errors) = play_inning("Visitor", $inning, $h_total, $v_total);
   $v_score[$v_inn] = $v_runs;
   $v_total += $v_runs;
   $v_inn++;
// echo "--------- ", $v_runs, $h_runs, "<br>";
if ($inning == 7) { echo   "<br>", file_get_contents("Take_Me.txt");}
   if (($inning==9) and ($h_total>$v_total)){ $game_over = 1; }
   else {
      list($h_runs, $h_hits, $h_errors) = play_inning("Home", $inning, $h_total, $v_total);
      $h_score[$h_inn] = $h_runs;
      $h_total += $h_runs;
      $h_inn++;
   }
   if (($inning >= 9 ) and ($v_total <> $h_total)) { $game_over = 1; }

// echo "+++++++++ ", $v_total, $h_total;

//   echo "> ", $inning, $v_inn, $h_inn, "<br>";

   echo "<br>";
   echo "Visitor: ";
   for ($i=0; $i<$v_inn; $i++) {echo $v_score[$i]; if (($i+1) % 3 ==0) echo ".";} 
   echo " - ", $v_total, "<br>";
   echo "Home : ";
   for ($i=0; $i<$h_inn; $i++) {echo $h_score[$i]; if (($i+1) % 3 ==0) echo ".";}
   echo " - ", $h_total, "<br>";
   $inning++; 
//   echo ">> ", $inning, $v_runs, $h_runs, "<br>";
} // end for $inning
?>
<br>
<FORM ACTION="http://www.bebelfamily.com/baseball.php" METHOD="post">
<INPUT TYPE="Submit" NAME="Play Ball" VALUE="Play Again?">
</FORM>

<?php
function play_inning($team, $this_inning, $h_score, $v_score) {
Global $game_over;
$theOuts = array("Strikeout", "Flyout....", "Groundout");
$nOuts = 0;
$runners = array(0, 0, 0, 0);
$runs = 0;
echo "<br>", $team, " Inning: ", $this_inning,"<br>";

while (($nOuts <3) and ($game_over == 0)) {
   $nbases = 0;
   $theAtBat = getAtBat();

   switch ($theAtBat) {
	case "Strikeout":
	case "Groundout":
	   $nOuts++;
	   $nbases = 0;
 	   echo "....1 ", $theAtBat, " ";
	   echo " : Outs = ", $nOuts;
	   break;
	case "Flyout....":
	   $nOuts++;
	   $nbases = 0;
	   $SacrificeScore = 0;
	   if ( $nOuts < 3 and $runners[3] == 1) {
		$theAtBat = "Sacrifice Fly1";
		$SacrificeScore = 1;
		$runners[3] = 0; 
//d		if ($runners[2] == 0) {echo "....2 ", $theAtBat, " ";}
            $runs = $runs + 1; //d	      echo " : Outs = ", $nOuts;
	   }
 	   if ( $nOuts < 3 and $runners[2] == 1) {
		$runners[3] = 1; $runners[2] = 0; 
		if($theAtBat == "Flyout....") {
		   $theAtBat = "Sacrifice Fly2";
//d 	         echo "....3 ", $theAtBat, " ";

		}
	   }
//d	   if(($runners[2] == 0) and ($runners[3] == 0)) { echo "....4 ", $theAtBat, " ";}
	   echo "....3 ", $theAtBat, " ";
	   if($SacrificeScore != 0) {echo "..... ", " s>Score ";}
	   echo " : Outs = ", $nOuts;
	   break;
	Case "Walk":
	   $nbases = 0;
         echo "....5 ", $theAtBat, " ";
	   if($runners[1] == 0) { $runners[1] = 1; }
	   elseif($runners[2] == 0) { $runners[2] = 1; }
	   elseif($runners[3] == 0) { $runners[3] = 1; }
	   else {$runs++; echo " w>Score "; }
	   break;
	Case "Single":
	   $nbases = 1;
         echo "....5 ", $theAtBat, " ";
	   break;
	Case "Double":
         echo "....5 ", $theAtBat, " ";
	   $nbases = 2;
	   break;
	Case "Triple":
         echo "....5 ", $theAtBat, " ";
	   $nbases = 3;
	   break;
	Case "Home Run":
         echo "....5 ", $theAtBat, " ";
	   $nbases = 4;
	   break;
	default:
	   echo "....6 ", $theAtBat, " ";
	   echo " : Outs = ", $nOuts;
   }

/* echo "+ ", $runners[0], $runners[1], $runners[2], $runners[3], "<br>";
   if ($theAtBat == "Single"){
	$nbases = 1;
   } elseif ($theAtBat == "Double"){
	$nbases = 2;
   } elseif ($theAtBat == "Triple"){
	$nbases = 3;
   } elseif ($theAtBat == "Home Run"){
	$nbases = 4;
   } else {
//	echo " ->Something else happened...<br>";
   } // end if $theAtBat
*/

if($nbases>0) {
   $runners[0] = 1;
   for ($i = 0; $i < 4; $i++) {
//   echo "++ ",$runners[0], $runners[1], $runners[2], $runners[3], "<br>";
      $nextbase = 3-$i+$nbases; // echo "# ", $nextbase, " ", $nbases, " ";
      if($nextbase > 3){
	   if($runners[3-$i]>0) {
            $runs = $runs + 1; echo " r>Score ";
	      $runners[3-$i] = 0;
         }
      } else {
	   $runners[$nextbase] = $runners[3-$i];
	   $runners[3-$i] = 0;
      }
   }
} // end if $nbases

// echo "......\t", $runners[0], $runners[1], $runners[2], $runners[3];
/*d
if ($theAtBat == "Walk") {
   if($runners[1] == 0) { $runners[1] = 1; }
   elseif($runners[2] == 0) { $runners[2] = 1; }
   elseif($runners[3] == 0) { $runners[3] = 1; }
   else {$runs++; echo " w>Score "; }
} // end if $theAtBat
*/
echo "......", $runners[0], $runners[1], $runners[2], $runners[3];
echo " : Runs = ", $runs, "<br>";
// echo "e9 ",$this_inning, "|", $team, "|", $v_score, "|", $h_score+$runs, "|", $game_over;
if(($this_inning >= 9) and ($team == "Home") and (($h_score+$runs) > $v_score)) { $game_over = 1; }
// echo "e9 ",$this_inning, "|", $team, "|", $h_score, "|", $h_score+$runs, "|", $game_over;
} // end while nOuts<3

return array($runs, $hits, $errors);
} // end play_inning

?>

<BR>
<?php
function OldgetAtBat() {
$outcome = rand(0,99);
if ($outcome<9) {
	return "Walk";
   } elseif ($outcome<21) {
	return "Single";
   } elseif ($outcome<28) { 
	return "Double";
   } elseif ($outcome<29) {
	return "Triple";
   } elseif ($outcome<33) {
	return "Home Run";
   } elseif ($outcome<50) {
	return "Strikeout";
   } elseif ($outcome<70) {
	return "Flyout....";
   } else {
	return "Groundout";
   }
} // end function getAtBat
?>
</FONT>
<?php
echo "<table border='1'>";
echo "<tr> <td>Visitor</td><td>$v_total</td></tr>"; 
echo "<tr> <td>Home</td><td>$h_total</td></tr>";
echo "</table>";
?> 
<?php
function getAtBat() {
$outcome = rand(0,99);
$levels = array(	"Walk"=>9,
			"Single"=>21,
			"Double"=>28,
			"Triple"=>29,
			"Home Run"=>33,
			"Strikeout"=>50,
			"Flyout"=>70,
			"Groundout"=>99
			);
if ($outcome< $levels["Walk"]) {
	return "Walk";
   } elseif ($outcome<$levels["Single"]) {
	return "Single";
   } elseif ($outcome<$levels["Double"]) { 
	return "Double";
   } elseif ($outcome<$levels["Triple"]) {
	return "Triple";
   } elseif ($outcome<$levels["Home Run"]) {
	return "Home Run";
   } elseif ($outcome<$levels["Strikeout"]) {
	return "Strikeout";
   } elseif ($outcome<$levels["Flyout"]) {
	return "Flyout....";
   } else {
	return "Groundout";
   }

}
?>
 /</body>
</html>
