<?php
/** adjuster for hijridatetime class
 * by hubaishan http://salafitech.net
 * ver 2.1
 * 8 dulqidah 1436 h 
 * 
 */
// These setting can by edited
define("our_pwd", "hijri"); //password
$hijri_settings=array(
	'umalqura' => TRUE,
	'langecode' => 'ar',
 
	);

// example when using file  
// END of edit able setting
// do not edit below
require_once("../hijri.class.php");
session_start();
if (array_key_exists('adj_data', $_SESSION))
{
	$hijri_settings['adj_data']=$_SESSION['adj_data'];
}

$adj=new hijri\calendaradjustment();

$msg = '';
if (!empty($_POST['login'])) {
    $_SESSION['password'] = $_POST['password'];
    header("Location: " . $_SERVER["SCRIPT_NAME"]);
    exit;
} elseif (array_key_exists('add', $_GET)) {
    header("Location: " . $_SERVER["SCRIPT_NAME"] . "?action=add&month=" . $_GET[month] ."&year=". $_GET[year]);
    exit;
} elseif (array_key_exists('exit', $_POST)) {
    session_destroy();
    header("Location: " . $_SERVER["SCRIPT_NAME"]);
    exit;
} elseif (array_key_exists('addadj', $_POST)) {
	$adj->add_adj($_POST['month'],$_POST['year'],$_POST['v']);
	$_SESSION['adj_data']=$adj->get_adjdata(TRUE);
	header("Location: " . $_SERVER["SCRIPT_NAME"]);
    exit;
} elseif (array_key_exists('deladj', $_POST)) {
	$adj->del_adj($_POST['month'],$_POST['year']);
	$_SESSION['adj_data']=$adj->get_adjdata(TRUE);
    header("Location: " . $_SERVER["SCRIPT_NAME"]);
    exit;
}
?>
<html dir="rtl">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>تعديل تقويم أم القرى</title>
     </head>

    <body>
<?php
$no_session = TRUE;
if (array_key_exists('password', $_SESSION)) {
    if ($_SESSION['password'] == our_pwd) {
        $no_session = FALSE;
    }
}
if ($no_session) {
    echo '
	<br><br><center>
	<form method="post">
	كلمة المرور
	<input type="password" name="password" value="" /><br>
	<input type="submit" name="login" value="دخول" />
	</form>';
    exit;
}

$hmonths = array(1=>"محرم", "صفر", "ربيع الأول", "ربيع الثاني", "جمادى الأولى", "جمادى الآخرة", "رجب", "شعبان", "رمضان", "شوال", "ذو القعدة", "ذو الحجة");

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'del') {
        echo "هل تريد بالتأكيد حذف تعديل الشهر" . $hmonths[$_GET['month']] . " من السنة " . $_GET['year'];
        $auto_del = $adj->auto_del_info($_GET['month'],$_GET['year']);
        if (!empty($auto_del)) {
            echo " سيتم حذف تعديلات الأشهر ";
            foreach ($auto_del as $k) {
                echo $hmonths[$k['month']] . ' من سنة  ' . $k['year'] ;
            }
            echo "تلقائيا";
        }
        echo "\n".'<form method="post"><input type="hidden" name="deladj" value=1><input type="hidden" name="month" value=' . $_GET['month'] . '><input type="hidden" name="year" value=' . $_GET['year'] . '><input type="submit" name="submit" value="نعم بالتأكيد" /></from>';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">إلغاء</a>';
    } elseif ($_GET['action'] == 'edit' or $_GET['action'] == 'add') {
        $hm =$_GET['month'];
		$hy = $_GET['year'];
        echo "تعديل بداية الشهر " . $hmonths[$hm ] . " من سنة $hy إلى:";
        echo '<form method="post"><input type="hidden" name="addadj" value=1><input type="hidden" name="month" value=' . $hm . '><input type="hidden" name="year" value=' . $hy . '><select name="v">';
		$starts=$adj->get_possible_starts($hm,$hy);
		foreach ($starts as $start)
		{
			echo '<option value="'.$start['jd'] .'"'. (($start['currentset'])? ' selected':'') . ' >' . $start['grdate']; 
            foreach ($start['alsoadjdata'] as $v) {
                echo " وسيتم أيضا تعديل بداية شهر " . $hmonths[$v['month'] ] . " من سنة ".$v['year']." إلى:" . $v['grdate'];
            }
            echo "</option>";
		}
        echo '</select><input type="submit" name="submit" value="إرسال" />';
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '">إلغاء</a>';
    }
} else {


    echo '<h2>التعديلات الحالية على تقويم أم القرى</h2>';
    echo $msg . '<br/>';
    foreach ($adj->get_current_adjs() as $v) {
        echo $v['year'] ."/ ". $v['month'] . " - ". $hmonths[$v['month']]  . " => " . $v['current'] . " الافتراضي هو " . $v['default'] . " [<a href='" . $_SERVER['SCRIPT_NAME'] . "?action=del&amp;month=". $v['month']. "&amp;year=". $v['year']. "'>حذف</a>] [<a href='" . $_SERVER['SCRIPT_NAME'] . "?action=edit&amp;month=". $v['month']. "&amp;year=". $v['year']. "'>تعديل</a>]<br/>";
    }
    echo '<h2>إضافة تعديل على تقويم أم القرى</h2>';
	echo hijri\datetime::createFromHijri(1436, 11, 0)->format('_d _M _Y=d M Y').'<br/>';
	echo hijri\datetime::createFromHijri(1436, 12, 0)->format('_d _M _Y=d M Y').'<br/>';
	echo hijri\datetime::createFromHijri(1437, 1, 0)->format('_d _M _Y=d M Y').'<br/>';
	echo hijri\datetime::createFromHijri(1437, 2, 0)->format('_d _M _Y=d M Y').'<br/>';
	echo hijri\datetime::createFromHijri(1437, 3, 0)->format('_d _M _Y=d M Y').'<br/>';
    echo '<form method="get">السنة :<select name="year">';
	$d= new hijri\datetime();
    list($mymonth, $myyear) = explode(' ', $d->format('_m _Y'));
    for ($n =  hijri\Calendar::umstartyear ; $n < hijri\Calendar::umendyear +1; $n++) {
        echo "<option value='$n'";
        if ($n == $myyear) {
            echo " selected ";
        }
        echo ">$n</option>\n";
    }
    echo '</select> الشهر :<select name="month">';
    for ($n = 1; $n < 13; $n++) {
        echo "<option value='$n'";
        if ($n == $mymonth) {
            echo "selected";
        }
        echo ">" . $hmonths[$n] . "</option>\n";
    }
    echo '</select><input type="submit" name="add" value="طلب إضافة" /></form>';
    echo '<br/>بيانات التعديل<br/><textarea rows="6" cols="50" style="text-align:left;direction: ltr;">';
	echo $adj->get_adjdata(TRUE);
    echo '</textarea><br/>';
    echo '<br/><form method="post"><input type="submit" name="exit" value="خروج" /></form>';
}
?>

    </body>

</html>
