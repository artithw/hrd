<?php include 'header.php'; if(isset($_GET['unset'])){ unset_session();}
if (empty($_SESSION[user])) {
    echo "<meta http-equiv='refresh' content='0;url=index.php'/>";
    exit();
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1><font color='blue'> ฝึกอบรมภายนอกหน่วยงานที่ยังไม่สรุป </font></h1> 
        <ol class="breadcrumb alert-success">
            <li><a href="index.php"><i class="fa fa-home"></i> หน้าหลัก</a></li>
            <li class="active"><i class="fa fa-edit"></i> ฝึกอบรมภายนอกหน่วยงานที่ยังไม่สรุป</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">ตารางบันทึกการฝึกอบรมภายนอกหน่วยงาน</h3>
            </div>
            <div class="panel-body">
                <form class="navbar-form navbar-right" name="frmSearch" role="search" method="post" action="pre_trainout(N).php">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div class="form-group">
                                    <input type="text" placeholder="ค้นหา" name='txtKeyword' class="form-control" value="<?php echo $Search_word; ?>" >
                                    <input type='hidden' name='method'  value='txtKeyword'>
                                </div> <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i> Search</button> </td>


                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </form>
                <?php

// สร้างฟังก์ชั่น สำหรับแสดงการแบ่งหน้า   
                function page_navigator($before_p, $plus_p, $total, $total_p, $chk_page) {
                    global $e_page;
                    global $querystr;
                    $urlfile = "pre_trainout(N).php"; // ส่วนของไฟล์เรียกใช้งาน ด้วย ajax (ajax_dat.php)
                    $per_page = 30;
                    $num_per_page = floor($chk_page / $per_page);
                    $total_end_p = ($num_per_page + 1) * $per_page;
                    $total_start_p = $total_end_p - $per_page;
                    $pPrev = $chk_page - 1;
                    $pPrev = ($pPrev >= 0) ? $pPrev : 0;
                    $pNext = $chk_page + 1;
                    $pNext = ($pNext >= $total_p) ? $total_p - 1 : $pNext;
                    $lt_page = $total_p - 4;
                    if ($chk_page > 0) {
                        echo "<a  href='$urlfile?s_page=$pPrev" . $querystr . "' class='naviPN'>Prev</a>";
                    }
                    for ($i = $total_start_p; $i < $total_end_p; $i++) {
                        $nClass = ($chk_page == $i) ? "class='selectPage'" : "";
                        if ($e_page * $i <= $total) {
                            echo "<a href='$urlfile?s_page=$i" . $querystr . "' $nClass  >" . intval($i + 1) . "</a> ";
                        }
                    }
                    if ($chk_page < $total_p - 1) {
                        echo "<a href='$urlfile?s_page=$pNext" . $querystr . "'  class='naviPN'>Next</a>";
                    }
                }


                    if ($_POST[method] == 'txtKeyword') {
                        $_SESSION['txtKeyword'] = $_POST[txtKeyword];
                    }
                    $Search_word = ($_SESSION['txtKeyword']);
                    if ($Search_word != "") {
//คำสั่งค้นหา
                        $q = "SELECT tro.memberbook, tro.projectName, tro.anProject, tro.Beginedate, tro.endDate, tro.tuid,
(SELECT COUNT(po.empno) FROM plan_out po WHERE po.idpo=tro.tuid and po.status_out='N') count_preson
FROM training_out tro
INNER JOIN plan_out po on po.idpo=tro.tuid
WHERE po.status_out='N' and (tro.memberbook LIKE '%$Search_word%' or tro.projectName LIKE '%$Search_word%')
GROUP BY po.idpo";
                    } else{
                        $q = "SELECT tro.memberbook, tro.projectName, tro.anProject, tro.Beginedate, tro.endDate,  tro.tuid,
(SELECT COUNT(po.empno) FROM plan_out po WHERE po.idpo=tro.tuid and po.status_out='N') count_preson
FROM training_out tro
INNER JOIN plan_out po on po.idpo=tro.tuid
WHERE po.status_out='N'
GROUP BY po.idpo";
                    }
                $qr = mysql_query($q);
                if ($qr == '') {
                    exit();
                }
                $total = mysql_num_rows($qr);

                $e_page = 30; // กำหนด จำนวนรายการที่แสดงในแต่ละหน้า   
                if (!isset($_GET['s_page'])) {
                    $_GET['s_page'] = 0;
                } else {
                    $chk_page = $_GET['s_page'];
                    $_GET['s_page'] = $_GET['s_page'] * $e_page;
                }
                $q.=" LIMIT " . $_GET['s_page'] . ",$e_page";
                $qr = mysql_query($q);
                if (mysql_num_rows($qr) >= 1) {
                    $plus_p = ($chk_page * $e_page) + mysql_num_rows($qr);
                } else {
                    $plus_p = ($chk_page * $e_page);
                }
                $total_p = ceil($total / $e_page);
                $before_p = ($chk_page * $e_page) + 1;
                echo mysql_error();
                ?>

                    <?php include_once ('option/funcDateThai.php'); ?>
                แสดงคำที่ค้นหา : <?= $Search_word; ?>
                <table align="center" width="100%" border="1">
                    <tr align="center" bgcolor="#898888">
                        <td width="3%" align="center"><b>ลำดับ</b></td>
                        <td width="7%" align="center"><b>เลขที่หนังสือ</b></td>
                        <td width="40%" align="center"><b>โครงการ</b></td>
                        <td width="19%" align="center"><b>หน่วยงานผู้จัด</b></td>
                        <td width="15%" align="center"><b>วันที่จัด</b></td>
                        <td width="6%" align="center"><b>จำนวนผู้เข้าร่วม</b></td>
                    </tr>

                    <?php
                    $i = 1;
                    while ($result = mysql_fetch_assoc($qr)) {
                        ?>
                        <tr>
                            <td align="center"><?= ($chk_page * $e_page) + $i ?></td>
                            <td align="center"><a href="#" onclick="return popup('pre_project_out(N).php?id=<?= $result[tuid]; ?>',popup,700,500);"><?= $result[memberbook]; ?></a>
                            </td>
                            <td><a href="#" onclick="return popup('pre_project_out(N).php?id=<?= $result[tuid]; ?>',popup,700,500);"><?= $result[projectName]; ?></a></td>
                            <td align="center"><?= $result[anProject]; ?></td>
                            <td align="center"><?= DateThai1($result[Beginedate]);?> <b>ถึง</b> <?= DateThai1($result[endDate]);?></td>
                            <td align="center"><?= $result[count_preson]; ?></td>
                        </tr>
                    <?php $i++;
                }
                ?>

                </table>
<?php
if ($total > 0) {
    echo mysql_error();
    ?><BR><BR>
                    <div class="browse_page">

                        <?php
                        // เรียกใช้งานฟังก์ชั่น สำหรับแสดงการแบ่งหน้า   
                        page_navigator($before_p, $plus_p, $total, $total_p, $chk_page);

                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size='2'>มีจำนวนทั้งหมด  <B>$total รายการ</B> จำนวนหน้าทั้งหมด ";
                        echo $count = ceil($total / 30) . "&nbsp;<B>หน้า</B></font>";
                    }
                    ?> 
                </div>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>
