<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
if (($_SESSION['c45_id'])==2) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "import/excel_reader2.php";
include_once "fungsi.php";
//object database class
$db_object = new database();
?>
<div class="content"><!-- start: PAGE -->
    <div class="main-content">
        <div class="container">
            <!-- start: PAGE HEADER -->
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    //include "styleSelectorBox.php";
                    ?>
                    <!-- start: PAGE TITLE & BREADCRUMB -->

                    <div class="page-header">
                        <h1>Olah Data </h1>
                    </div>
                    <!-- end: PAGE TITLE & BREADCRUMB -->
                </div>
            </div>
            <!-- end: PAGE HEADER -->
            <!-- start: PAGE CONTENT -->
            <?php
            if (isset($_POST['upload'])) {
                $data = new Spreadsheet_Excel_Reader($_FILES['file_data']['tmp_name']);

                $baris = $data->rowcount($sheet_index = 0);
                $column = $data->colcount($sheet_index = 0);
                //import data excel dari baris kedua, karena baris pertama adalah nama kolom
                // $temp_date = $temp_produk = "";
                for ($i = 2; $i <= $baris; $i++) {
                    if (!empty($data->val($i, 2))) {
                        $income = str_replace(".", "", $data->val($i, 5));

                        $value = "(\"" . $data->val($i, 2) . "\", '" . strtolower(trim($data->val($i, 3))) . "', '"
                                . strtolower(trim($data->val($i, 4))) . "', '" . $income . "', "
                                . $data->val($i, 6) . ", '" . strtolower(trim($data->val($i, 7))) . "')";
                        $sql = "INSERT INTO data_latih "
                                . " (name, status_of_marriage, status_of_house, income, age, dependents, payment_status)"
                                . " VALUES " . $value;
                        $result = $db_object->db_query($sql);
                    }
                }
                if ($result) {
                    ?>
                    <script> location.replace("?menu=olah_data&pesan_success=Data berhasil disimpan");</script>
                    <?php
                } else {
                    ?>
                    <script> location.replace("?menu=olah_data&pesan_error=Data gagal disimpan");</script>
                    <?php
                }
            }
            
            if(isset($_POST['delete_all'])){
                $sql = "TRUNCATE data_latih";
                        $result = $db_object->db_query($sql);
                 
                if ($result) {
                    ?>
                    <script> location.replace("?menu=olah_data&pesan_success=Data berhasil dihapus");</script>
                    <?php
                } else {
                    ?>
                    <script> location.replace("?menu=olah_data&pesan_error=Data gagal dihapus");</script>
                    <?php
                }
            }


            $query = $db_object->db_query("SELECT * FROM data_latih order by(id)");
            $jumlah = $db_object->db_num_rows($query);
            echo "<br><br>";
            
            if(isset($_REQUEST['pesan_success'])){
                display_success($_REQUEST['pesan_success']);
            }
            
            if(isset($_REQUEST['pesan_error'])){
                display_error($_REQUEST['pesan_error']);
            }
            
            ?>

            <form method="post" enctype="multipart/form-data" action="">
                <div class="form-group">
                    <div class="input-group">
                        <label>Import data from excel</label>
                        <input name="file_data" type="file" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <input name="upload" type="submit" value="Upload Data" class="btn btn-success">
                    <input name="delete_all" type="submit" value="Delete All Data" class="btn btn-danger">
                    <a href="index.php?menu=olah_data" class="btn btn-default">
                        <i class="fa fa-refresh"></i>Refresh</a>
                </div>
            </form>
            <?php
            if ($jumlah == 0) {
                echo "<center><h3>Data Latih masih kosong...</h3></center>";
            } else {
                ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sample-table-1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Status Of Marriage</th>
                                <th>Status Of House</th>
                                <th>Income</th>
                                <th>Age</th>
                                <th>Dependents</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = $db_object->db_fetch_array($query)) {
                                echo "<tr>";
                                echo "<td>" . $no . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['status_of_marriage'] . "</td>";
                                echo "<td>" . $row['status_of_house'] . "</td>";
                                echo "<td>" . $row['income'] . "</td>";
                                echo "<td>" . $row['age'] . "</td>";
                                echo "<td>" . $row['dependents'] . "</td>";
                                echo "<td>" . $row['payment_status'] . "</td>";
                                echo "</tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>