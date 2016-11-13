<?php

/*
 * example for Hijri Date Lib
 * by Saeed Hubaishan
 */
require_once '../hijri.class.php';
date_default_timezone_set('Asia/Aden');

function buildMonthCal($month, $year, $outmonth = FALSE)
{
	$c = new hijri\Calendar();
	$d = new hijri\datetime(null, null, 'ar', $c);
	list($cday, $cmonth, $cyear) = explode('-', $d->format('_j-_n-_Y'));
	
	$d->setDateHijri($year, $month + 1, 0);
	list($gm2, $gy2) = explode('-', $d->format("M-Y"));
	$d->setDateHijri($year, $month, 1);
	list($start_wd, $month_name, $gm1, $gy1) = explode('-', $d->format("w-_M-M-Y"));
	$title = $month_name . " " . $year . "هـ (" . $gm1 . (($gy2 != $gy1) ? " " . $gy1 : '') . (($gm2 != $gm1) ? "-" . $gm2 : '') . " " . $gy2 . "م)";
	
	$wd = array(0 => 1, 2, 3, 4, 5, 6, 0);
	$month_length = $c->days_in_month($month, $year);
	$b_month = $month - 1;
	$b_year = $year;
	if ($b_month == 0) {
		$b_month = 12;
		$b_year--;
	}
	$a_month = $month + 1;
	$a_year = $year;
	if ($a_month == 13) {
		$a_month = 1;
		$a_year++;
	}
	echo '<div class="navigation"><a class="prev" href="monthCalendar.php?month=' . $b_month . '&year=' . $b_year . '">&lt;</a>' . '<div class="title" >' . $title . '<a class= "next" href="monthCalendar.php?month=' . $a_month . '&year=' . $a_year . '">&gt;</a>' . '</div>
</div>';
	if ($wd[$start_wd] > 0) {
		$d->modify("-" . $wd[$start_wd] . " day");
	}
	echo '<table>
    <tr>
        <th class="weekday">السبت</th>
        <th class="weekday">الأحد</th>
        <th class="weekday">الإثنين</th>
        <th class="weekday">الثلاثاء</th>
        <th class="weekday">الأربعاء</th>
        <th class="weekday">الخميس</th>
        <th class="weekday">الجمعة</th>
    </tr>';
	$dayw = 0;
	do {
		list($hd, $hm, $hy, $gd, $gm, $gy) = explode('-', $d->format("_j-_n-_Y-j-n-Y"));
		if ($dayw == 0) {
			echo "<tr>";
		}
		$class = '';
		if ($cday == $hd && $cmonth == $hm && $cyear == $hy) {
			$class = "today";
		} elseif ($hm == $month) {
			$class = "current";
		}
		echo "<td class='$class'><a href='javascript: void(0)'>$hd<br/>&nbsp;<span class='gre'>$gd</span></a></td>";
		if ($dayw == 6) {
			echo "</tr>";
			$dayw = 0;
			if (($hm > $month) || ($hy > $year) || ($hm == $month && $hd == $month_length)) {
				break;
			}
		} else {
			$dayw++;
		}
		$d->modify("+1 day");
	} while (TRUE);
}
$d = new hijri\datetime();
list($year, $month) = explode(' ', $d->format('_Y _n'));
// echo is_integer($_REQUEST['month'])).$_REQUEST['year'];
if (isset($_REQUEST['month']) && isset($_REQUEST['year'])) {
	$tmonth = (int) $_REQUEST['month'];
	$tyear = (int) $_REQUEST['year'];
	
	if ($tmonth > 0 && $tmonth < 13) {
		$year = $tyear;
		$month = $tmonth;
	}
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="utf-8" />
<meta name="viewport"
	content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>التقويم الهجري الشهري</title>
<!-- add styles and scripts -->
<style>
/* calendar styles */
#calendar {
	-moz-user-select: none;
	border: 1px solid #EEEEEE;
	border-radius: 6px 6px 6px 6px;
	color: #333333;
	font-family: Arial, sans-serif;
	font-size: 1.1em;
	margin: 10px auto;
	padding: 0.4em;
	width: 90%;
	direction: rtl;
}

#calendar .navigation {
	background-color: #CC0000;
	border: 1px solid #E3A1A1;
	border-radius: 6px 6px 6px 6px;
	color: #FFFFFF;
	font-weight: bold;
	padding: 1px;
	position: relative;
}

#calendar .navigation .title {
	background: none repeat scroll 0 0 transparent;
	border-color: rgba(0, 0, 0, 0);
	color: inherit;
	line-height: 1.8em;
	margin: 0 2.3em;
	text-align: center;
}

#calendar .navigation .prev, #calendar .navigation .next {
	text-decoration: none;
	color: #FFFFFF;
	height: 24px;
	opacity: 0.9;
	position: absolute;
	top: 4px;
	width: 24px;
}

#calendar .navigation .prev {
	background-position: 0 0;
	right: 4px;
}

#calendar .navigation .next {
	background-position: -24px 0;
	left: 4px;
}

#calendar .navigation .prev:hover, #calendar .navigation .next:hover {
	opacity: 1;
}

#calendar table {
	border-collapse: collapse;
	font-size: 0.9em;
	table-layout: fixed;
	width: 100%;
}

#calendar table th {
	border: 0 none;
	font-weight: bold;
	padding: 0.7em 0.3em;
	text-align: center;
}

#calendar table td {
	border: 0 none;
	padding: 1px;
}

#calendar table td a {
	background-color: #EEEEEE;
	border: 1px solid #D8DCDF;
	color: #004276;
	display: block;
	font-weight: normal;
	opacity: 0.7;
	padding: 0.2em;
	text-align: right;
	text-decoration: none;
}

#calendar table td a:hover {
	background-color: #F6F6F6;
	border: 1px solid #CDD5DA;
	color: #111111;
}

#calendar table td.current a {
	font-weight: bold;
	opacity: 1;
}

#calendar table td.today a {
	background-color: #FBF8EE;
	border: 1px solid #FCD3A1;
	color: #444444;
	font-weight: bold;
	opacity: 1;
}

#calendar span.gre {
	text-align: left;
	direction: ltr;
	color: #0080CC;
	float: left;
}
</style>
</head>
<body>
	<div id="calendar">
<?php
buildMonthCal($month, $year)?>
    </div>
</body>
</html>
