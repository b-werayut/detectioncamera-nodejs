<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>NetWorklink.Co.Ltd,</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="fonts/font-kanit.css" />
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/snappaging.css">
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/pagination.js"></script>
    <!-- Date Pick jquery ui-->
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link href="css/sweetalert2.min.css" rel="stylesheet">
    <script src="js/sweetalert2.all.min.js"></script>
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-lg-5">
            <a class="navbar-brand" href="#!">NetWorklink.Co.Ltd,</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item bg-dark"><a class="nav-link" aria-current="page"
                            href="http://49.0.91.113:20080/LiveNotifyVideo/">Streamimg</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link " href="<?php 
                             if(isset($_GET['dateparam']) && isset($_GET['snapparam'])){
                              $dateparam = $_GET['dateparam'];
                              $snapparam = $_GET['snapparam'];
                                echo "http://49.0.91.113:20080/SnapShot/snappaging.php?dateparam={$dateparam}&snapparam={$snapparam}";
                            }else{
                                echo "http://49.0.91.113:20080/SnapShot/snappaging.php";
                            }
                            ?>">Snap picture</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link active" href="<?php 
                            if(isset($_GET['dateparam']) && isset($_GET['snapparam'])){
                              $dateparam = $_GET['dateparam'];
                              $snapparam = $_GET['snapparam'];
                                echo "http://49.0.91.113:20080/SnapShot/vdopaging.php?dateparam={$dateparam}&snapparam={$snapparam}";
                            }else{
                                echo "http://49.0.91.113:20080/SnapShot/vdopaging.php";
                            }
                            ?>">Snap vdo</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Header-->
    <header class="py-2 ">
        <div class="container px-lg-5 ">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center bg-dark">
                <div class="">
                    <h1 class="display-5 fw-bold text-white text-uppercase" style="letter-spacing: 10px">Snapvdo</h1>
                </div>
                <div class="col-md-12 d-flex justify-content-center align-items-center d-none">
                    <select class="form-select " aria-label="Default select example" id="selectoption"
                        style="width: 15%;">
                        <option selected>Open this select menu</option>
                    </select>
                </div>
            </div>
    </header>
    <!-- Page Content-->
    <section class="p-1 mb-4">
        <div class="container" style="margin-bottom: 120px;">
            <div class="content pt-0 px-lg-5 ">

                <!-- Page Features-->
                <!-- content -->
                <div class="col-md-12 px-5 pt-2 pb-5 rounded-2" style="background-color: #f7f7f7;">
                    <form id="dateform" class="col-md-12 position-relative" method="GET" action="">
                        <label for="dateinput">Date:</label>
                        <div class="py-1 d-inline-block">
                            <input type="text" disable class="form-control form-control-sm" id="dateinput"
                                name="dateinput" placeholder="Choose date"><i class="fa fa-calendar"
                                aria-hidden="true"></i>
                        </div>
                        <!-- <input type="date" class="form-control" id="dateinput" name="dateinput"> -->
                        <div class="py-1 d-inline-block">
                            <input class="btn btn-secondary btn-sm" type="submit" value="Search">
                        </div>
                    </form>
                    <hr>
                    <?php 
            $dateparam = date("d-m-Y");
            if(isset($_GET['dateparam']) && isset($_GET['snapparam'])){
              $dateparam = $_GET['dateparam'];
              $snapparam = $_GET['snapparam'];
              $nums = '';
              $page = '';
              $datefile = glob("../snapfolder/$dateparam");
            ?>
                    <div id="" class="date py-2">
                        <h4 class="head m-0" style="font-family: Kanit;"><?= "วันที่: {$dateparam}" ?></h4>
                    </div>
                    <div class="row py-2 content1" style="background-color: white; ">
                        <ul>
                            <li style="background-color: white;">
                                <span><?= "Snap:{$snapparam}"; ?> </span>
                                <hr>
                                <?php
                          $snap = "../snapfolder/{$dateparam}/{$snapparam}/vdo/*";
                          $snapshots = glob($snap); ?>
                                <div class=" row p-0 gap-0">
                                    <?php
                          if(empty($snapshots)){
                            echo '<input class="d-none" id="paramnone" value="">';
                            echo '<div class="col-md-12 text-center p-3">';
                            echo '<h1>No Data</h1>';
                            echo '</div>';
                          }
                          foreach($snapshots as $snapshotss) {   
                          $snapshotssex = basename($snapshotss);
                          $snapshotssexmp4 = explode(".",$snapshotssex);
                          if($snapshotssexmp4[5] == 'mp4'){?>
                                    <div class=" col-md-3 p-0">
                                        <div class="p-0 containerimgshow">
                                            <video width="320" height="240" muted controls loop class="img-thumbnail">
                                                <source src="<?= $snapshotss ?>" type="video/mp4">
                                            </video>
                                        </div>
                                    </div>
                                    <?php }else{ ?>
                                    <div class=" col-md-3 p-0">
                                        <div class="p-0 containerimgshow" onclick="showimg('<?= $snapshotss; ?>')">
                                            <!-- <input type="checkbox" id="zoomCheck<?//= $nums; ?>" >  -->
                                            <label for="zoomCheck<?= $nums; ?>">
                                                <img src="<?= $snapshotss; ?>" class="img-thumbnail" alt="...">
                                            </label>
                                        </div>
                                    </div>
                                    <?php 
                        }
                      $nums++;
                      } ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php }else{
            if(isset($_GET['dateinput'])){
              $nums = '';
              $page = '';
              $dateparam = $_GET['dateinput'];
              if($dateparam == ''){
                echo '<input class="d-none" id="paramnone" value="">';
                echo '<div class="col-md-12 text-center p-3">';
                echo '<h1>No Data</h1>';
                echo '</div>';
              }else{
                $datefile = glob("../snapfolder/$dateparam");
                if($datefile[0] == ''){
                echo '<input class="d-none" id="paramnone" value="">';
                echo '<div class="col-md-12 text-center p-3">';
                echo '<h1>No Data</h1>';
                echo '</div>';
                }else{
                ?>
                <div id="snappath" class="date py-2">
                    <h4 class="head m-0" style="font-family: Kanit;"><?= "วันที่: {$dateparam}" ?></h4>
                </div>
                <div class="row py-2 content1" style="background-color: white; ">
                    <ul>
                        <?php 
                            $pathsnap = "../snapfolder/{$dateparam}/*";
                            $pathsnaps = glob($pathsnap);
                            if(empty($pathsnaps)){
                            echo '<hr><input class="d-none" id="paramnone" value="">';
                            echo '<div class="col-md-12 text-center p-3">';
                            echo '<h1>No Data</h1>';
                            echo '</div>';
                            }else{
                            foreach($pathsnaps as $pathsnapss) { 
                            $pathsnapfm = basename($pathsnapss); 
                            ?>
                        <li style="background-color: white;">
                            <span><?= "Snap:{$pathsnapfm}"; ?> </span>
                            <hr>
                            <?php
                            $snap = "../snapfolder/{$dateparam}/{$pathsnapfm}/vdo/*";
                            $snapshots = glob($snap); 
                            if(empty($snapshots)){
                                echo '<input class="d-none" id="paramnone" value="">';
                                echo '<div class="col-md-12 text-center p-3">';
                                echo '<h1>No Data</h1>';
                                echo '</div>';
                              }else{
                            ?>
                            <div class=" row p-0 gap-0">
                                <?php
                            foreach($snapshots as $snapshotss){   
                            $snapshotssex = basename($snapshotss);
                            $snapshotssexmp4 = explode(".",$snapshotssex);
                            if($snapshotssexmp4[5] == 'mp4'){?>
                                <div class=" col-md-3 p-0">
                                    <div class="p-0 containerimgshow">
                                        <video width="320" height="240" muted controls loop class="img-thumbnail">
                                            <source src="<?= $snapshotss ?>" type="video/mp4">
                                        </video>
                                    </div>
                                </div>
                                <?php }else{ ?>
                                <div class=" col-md-3 p-0">
                                    <div class="p-0 containerimgshow" onclick="showimg('<?= $snapshotss; ?>')">
                                        <!-- <input type="checkbox" id="zoomCheck<?//= $nums; ?>" >  -->
                                        <label for="zoomCheck<?= $nums; ?>">
                                            <img src="<?= $snapshotss; ?>" class="img-thumbnail" alt="...">
                                        </label>
                                    </div>
                                </div>
                                <?php 
                        }
                        $nums++;
                          }
                        } 
                          ?>
                        </li>
                        <?php 
                      }
                    }
                  }
                }
                }else{
                $nums = '';
                $page = '';
                $datefile = glob("../snapfolder/$dateparam");
              ?>
                        <div id="snappath" class="date py-2">
                            <h4 class="head m-0" style="font-family: Kanit;"><?= "วันที่: {$dateparam}" ?></h4>
                        </div>
                        <div class="row py-2 content1" style="background-color: white; ">
                            <ul>
                                <?php 
                          $pathsnap = "../snapfolder/{$dateparam}/*";
                          $pathsnaps = glob($pathsnap);
                          if(empty($pathsnaps)){
                          echo '<hr><input class="d-none" id="paramnone" value="">';
                          echo '<div class="col-md-12 text-center p-3">';
                          echo '<h1>No Data</h1>';
                          echo '</div>';
                          }else{
                          foreach($pathsnaps as $pathsnapss) { 
                          $pathsnapfm = basename($pathsnapss); 
                          ?>
                                <li style="background-color: white;">
                                    <span><?= "Snap:{$pathsnapfm}"; ?> </span>
                                    <hr>
                                    <?php
                          $snap = "../snapfolder/{$dateparam}/{$pathsnapfm}/vdo/*";
                          $snapshots = glob($snap); 
                          if(empty($snapshots)){
                            echo '<input class="d-none" id="paramnone" value="">';
                            echo '<div class="col-md-12 text-center p-3">';
                            echo '<h1>No Data</h1>';
                            echo '</div>';
                          }else{
                          ?>
                                    <div class=" row p-0 gap-0">
                                        <?php
                          foreach($snapshots as $snapshotss) {   
                          $snapshotssex = basename($snapshotss);
                          $snapshotssexmp4 = explode(".",$snapshotssex);
                          if($snapshotssexmp4[5] == 'mp4'){?>
                                        <div class=" col-md-3 p-0">
                                            <div class="p-0 containerimgshow">
                                                <video width="320" height="240" muted controls loop
                                                    class="img-thumbnail">
                                                    <source src="<?= $snapshotss ?>" type="video/mp4">
                                                </video>
                                            </div>
                                        </div>
                                        <?php }else{ ?>
                                        <div class=" col-md-3 p-0">
                                            <div class="p-0 containerimgshow" onclick="showimg('<?= $snapshotss; ?>')">
                                                <!-- <input type="checkbox" id="zoomCheck<?//= $nums; ?>" >  -->
                                                <label for="zoomCheck<?= $nums; ?>">
                                                    <img src="<?= $snapshotss; ?>" class="img-thumbnail" alt="...">
                                                </label>
                                            </div>
                                        </div>
                                        <?php 
                        }
                      $nums++;
                          }
                      } 
                      ?>
                                </li>
                                <?php 
                    }
                  }
                  }
                ?>
                            </ul>
                        </div>
                </div>
                <?php } ?>
            </div>
    </section>
    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; NetWorklink.Co.Ltd,</p>
        </div>
    </footer>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script>
    $("#dateform").submit(function(e) {
        if ($("#dateinput").val() == "") {
            Swal.fire({
                icon: "warning",
                title: "กรุณาเลือกวันที่!",
                confirmButtonColor: "#3085d6",
            })
            e.preventDefault();
        }
    });

    $("#dateinput").datepicker({
        maxDate: 0,
        dateFormat: 'dd-mm-yy',
        showOn: "button",
        buttonImage: "assets/calendar.png",
        buttonImageOnly: true
    });
    </script>
</body>

</html>