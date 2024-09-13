<?php
// Koneksi ke database
include "../koneksi.php";

// Ambil kode_lahan dari URL
$kode_lahan = isset($_GET['id']) ? $_GET['id'] : '';

// Query untuk mengambil data dari tabel note_psm
$sql = "SELECT catatan_psmlegal, legal_date, catatan_psmfat, fat_date FROM note_psm WHERE kode_lahan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kode_lahan);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $catatan_psmlegal = $row['catatan_psmlegal'];
    $legal_date = $row['legal_date'];
    $catatan_psmfat = $row['catatan_psmfat'];
    $fat_date = $row['fat_date'];
} else {
    $catatan_psmlegal = '';
    $legal_date = '';
    $catatan_psmfat = '';
    $fat_date = '';
}
?>
<!DOCTYPE html>
<html lang="en" dir="">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard Resto | Mie Gacoan</title>
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <link href="../dist-assets/css/themes/lite-purple.min.css" rel="stylesheet" />
    <link href="../dist-assets/css/plugins/perfect-scrollbar.min.css" rel="stylesheet" />
    <link href="../dist-assets/css/plugins/datatables.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="../dist-assets/css/feather-icon.css">
    <link rel="stylesheet" type="text/css" href="../dist-assets/css/icofont.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .hidden {
            display: none;
        }
        .small-column {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        th, td {
            white-space: nowrap;
        }
        table.dataTable {
            border-collapse: collapse !important;
        }
        .chat-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        .chat-bubble {
            max-width: 60%;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .chat-left {
            background-color: #f1f1f1;
            margin-right: auto;
            text-align: left;
        }
        .chat-right {
            background-color: #007bff;
            color: white;
            margin-left: auto;
            text-align: right;
        }
        .chat-date {
            font-size: 0.8em;
            color: white;
            margin-bottom: 5px;
        }
    </style>
</head>
<body class="text-left">
<div class="app-admin-wrap layout-sidebar-compact sidebar-dark-purple sidenav-open clearfix">
    <?php include '../layouts/right-sidebar.php'; ?>
    <div class="main-content-wrap d-flex flex-column">
        <?php include '../layouts/top-sidebar.php'; ?>
        <div class="main-content">
            <div class="breadcrumb">
                <h1>History Note PSM (TAF - Legal)</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row mb-4">
                <div class="col-md-8 mb-4 justify-content-center">
                    <div class="card text-left">
                        <div class="card-body">
                            <h4 class="card-title mb-3"></h4>
                            <div class="table-responsive">
                                <div class="chat-container">
                                    <?php if (!empty($catatan_psmlegal)): ?>
                                        <div class="chat-bubble chat-left">
                                            <div class="chat-date"><?php echo date('M d, Y', strtotime($legal_date)); ?> | Catatan Legal</div>
                                            <div><?php echo nl2br(htmlspecialchars($catatan_psmlegal)); ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($catatan_psmfat)): ?>
                                        <div class="chat-bubble chat-right">
                                            <div class="chat-date"><?php echo date('M d, Y', strtotime($fat_date)); ?> | Catatan TAF</div>
                                            <div><?php echo nl2br(htmlspecialchars($catatan_psmfat)); ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end of main-content -->
            <!-- Footer Start -->
            <div class="flex-grow-1"></div>
            <!-- fotter end -->
        </div>
    </div>
</div>
<!-- ============ Search UI Start ============= -->
    
<!-- ============ Search UI End ============= -->
<div class="customizer">
    <div class="handle"><i class="i-Gear spin"></i></div>
    <div class="customizer-body" data-perfect-scrollbar="" data-suppress-scroll-x="true">
        <div class="accordion" id="accordionCustomizer">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <p class="mb-0">Sidebar Colors</p>
                </div>
                <div class="collapse show" id="collapseOne" aria-labelledby="headingOne" data-parent="#accordionCustomizer">
                    <div class="card-body">
                        <div class="colors sidebar-colors">
                            <a class="color gradient-purple-indigo" data-sidebar-class="sidebar-gradient-purple-indigo"><i class="i-Eye"></i></a>
                            <a class="color gradient-black-blue" data-sidebar-class="sidebar-gradient-black-blue"><i class="i-Eye"></i></a>
                            <a class="color gradient-black-gray" data-sidebar-class="sidebar-gradient-black-gray"><i class="i-Eye"></i></a>
                            <a class="color gradient-steel-gray" data-sidebar-class="sidebar-gradient-steel-gray"><i class="i-Eye"></i></a>
                            <a class="color dark-purple active" data-sidebar-class="sidebar-dark-purple"><i class="i-Eye"></i></a>
                            <a class="color slate-gray" data-sidebar-class="sidebar-slate-gray"><i class="i-Eye"></i></a>
                            <a class="color midnight-blue" data-sidebar-class="sidebar-midnight-blue"><i class="i-Eye"></i></a>
                            <a class="color blue" data-sidebar-class="sidebar-blue"><i class="i-Eye"></i></a>
                            <a class="color indigo" data-sidebar-class="sidebar-indigo"><i class="i-Eye"></i></a>
                            <a class="color pink" data-sidebar-class="sidebar-pink"><i class="i-Eye"></i></a>
                            <a class="color red" data-sidebar-class="sidebar-red"><i class="i-Eye"></i></a>
                            <a class="color purple" data-sidebar-class="sidebar-purple"><i class="i-Eye"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingOne">
                    <p class="mb-0">RTL</p>
                </div>
                <div class="collapse show" id="collapseTwo" aria-labelledby="headingTwo" data-parent="#accordionCustomizer">
                    <div class="card-body">
                        <label class="checkbox checkbox-primary">
                            <input id="rtl-checkbox" type="checkbox" /><span>Enable RTL</span><span class="checkmark"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../dist-assets/js/plugins/jquery-3.3.1.min.js"></script>
<script src="../dist-assets/js/plugins/bootstrap.bundle.min.js"></script>
<script src="../dist-assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="../dist-assets/js/plugins/datatables.min.js"></script>
<script src="../dist-assets/js/elephant.min.js"></script>
<script src="../dist-assets/js/application.js"></script>

<!-- ============ Search UI Start ============= -->
    
    <!-- ============ Search UI End ============= -->
    <div class="customizer">
        <div class="handle"><i class="i-Gear spin"></i></div>
        <div class="customizer-body" data-perfect-scrollbar="" data-suppress-scroll-x="true">
            <div class="accordion" id="accordionCustomizer">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <p class="mb-0">Sidebar Colors</p>
                    </div>
                    <div class="collapse show" id="collapseOne" aria-labelledby="headingOne" data-parent="#accordionCustomizer">
                        <div class="card-body">
                            <div class="colors sidebar-colors"><a class="color gradient-purple-indigo" data-sidebar-class="sidebar-gradient-purple-indigo"><i class="i-Eye"></i></a><a class="color gradient-black-blue" data-sidebar-class="sidebar-gradient-black-blue"><i class="i-Eye"></i></a><a class="color gradient-black-gray" data-sidebar-class="sidebar-gradient-black-gray"><i class="i-Eye"></i></a><a class="color gradient-steel-gray" data-sidebar-class="sidebar-gradient-steel-gray"><i class="i-Eye"></i></a><a class="color dark-purple active" data-sidebar-class="sidebar-dark-purple"><i class="i-Eye"></i></a><a class="color slate-gray" data-sidebar-class="sidebar-slate-gray"><i class="i-Eye"></i></a><a class="color midnight-blue" data-sidebar-class="sidebar-midnight-blue"><i class="i-Eye"></i></a><a class="color blue" data-sidebar-class="sidebar-blue"><i class="i-Eye"></i></a><a class="color indigo" data-sidebar-class="sidebar-indigo"><i class="i-Eye"></i></a><a class="color pink" data-sidebar-class="sidebar-pink"><i class="i-Eye"></i></a><a class="color red" data-sidebar-class="sidebar-red"><i class="i-Eye"></i></a><a class="color purple" data-sidebar-class="sidebar-purple"><i class="i-Eye"></i></a></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <p class="mb-0">RTL</p>
                    </div>
                    <div class="collapse show" id="collapseTwo" aria-labelledby="headingTwo" data-parent="#accordionCustomizer">
                        <div class="card-body">
                            <label class="checkbox checkbox-primary">
                                <input id="rtl-checkbox" type="checkbox" /><span>Enable RTL</span><span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../dist-assets/js/plugins/jquery-3.3.1.min.js"></script>
    <script src="../dist-assets/js/plugins/bootstrap.bundle.min.js"></script>
    <script src="../dist-assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../dist-assets/js/scripts/script.min.js"></script>
    <script src="../dist-assets/js/scripts/sidebar.compact.script.min.js"></script>
    <script src="../dist-assets/js/scripts/customizer.script.min.js"></script>
    <script src="../dist-assets/js/plugins/datatables.min.js"></script>
    <script src="../dist-assets/js/scripts/datatables.script.min.js"></script>	
    <script src="../dist-assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="../dist-assets/js/icons/feather-icon/feather-icon.js"></script>
    <script>
    $(document).ready(function(){
        // Saat tombol edit diklik
        $('.edit-btn').click(function(){
            // Ambil data-id dari tombol edit
            var id = $(this).data('id');

            // Isi nilai input tersembunyi dengan ID yang diambil
            $('#modalKodeLahan').val(id);
        });
    });
    
    // Function to toggle the visibility of issue detail section
    function toggleIssueDetail() {
        var statusSelect = document.getElementById("statusSelect");
        var issueDetailSection = document.getElementById("issueDetailSection");

        if (statusSelect.value === "Pending") {
            issueDetailSection.style.display = "block";
        } else {
            issueDetailSection.style.display = "none";
        }
    }

    // Event listener for statusSelect change
    $('#statusSelect').on('change', function () {
        toggleIssueDetail();
    });
</script>
    <script>
    $(document).ready(function(){
        // Saat tombol edit diklik
        $('.edit-btn').click(function(){
            // Ambil data-id dari tombol edit
            var id = $(this).data('id');

            // Isi nilai input tersembunyi dengan ID yang diambil
            $('#modalId').val(id);
        });
    });
    
    // Function to toggle the visibility of issue detail section
    function toggleIssueDetail() {
        var statusSelect = document.getElementById("statusSelect");
        var issueDetailSection = document.getElementById("issueDetailSection");

        if (statusSelect.value === "Pending") {
            issueDetailSection.style.display = "block";
        } else {
            issueDetailSection.style.display = "none";
        }
    }

    // Event listener for statusSelect change
    $('#statusSelect').on('change', function () {
        toggleIssueDetail();
    });
</script>
    <script>
        // Fungsi untuk mengatur id data yang akan dihapus ke dalam modal
        function setDelete(element) {
            var id = element.id;
            document.getElementById('delete').value = id;
        }
    </script>
</body>

</html>