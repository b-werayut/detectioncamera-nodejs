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
    <link rel="stylesheet" href="css/snappaging_.css">
    <script src="js/jquery-3.7.1.min.js"></script>
    <!-- Date Pick jquery ui-->
</head>
<style>

@media only screen and (max-width: 500px) {
.bottom-bar{
            justify-content: space-between!important;
        }
        .snap-btn{
            justify-content: center!important;
        }
        .vdo-btn{
            justify-content: center!important;
        }
        .streamdiv{
            height: 560px!important;
        }
    }

    @media only screen and (max-width: 600px) {
        iframe {
            width: 350px;
            height: 282px;
        }
    }

    @media only screen and (min-width: 601px) {
        iframe {
            width: 800px;
            height: 650px;
        }
    }
</style>

<body>
    <?php
    if (isset($_GET['param'])) {
        $getparam = $_GET['param'];
        $urlimg = "http://49.0.91.113:20080/SnapShot/snappaging_.php?param={$getparam}";
        $urlvdo = "http://49.0.91.113:20080/SnapShot/vdopaging_.php?param={$getparam}";
    } else {
        $urlimg = "http://49.0.91.113:20080/SnapShot/snappaging_.php";
        $urlvdo = "http://49.0.91.113:20080/SnapShot/vdopaging_.php";
    }
    ?>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-lg-5">
            <img src="../snapshot/assets/nwl-logo.png" alt="NetWorklink" width="50">
            <span style="letter-spacing: 1px;" class="text-white" href="#!">NetWorklink.Co.Ltd,</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item bg-dark"><a class="nav-link active" aria-current="page" href="http://49.0.91.113:20080/LiveNotifyVideo/">Streamimg</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link" href="<?= $urlimg; ?>">Snapshot</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link" href="<?= $urlvdo; ?>">Snap Videos</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Header-->
    <header class="py-2 ">
        <div class="container px-lg-5 ">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center bg-dark">
                <div class="">
                    <h1 class="display-5 fw-bold text-white text-uppercase" style="letter-spacing: 10px">Streaming</h1>
                </div>
                <div class="col-md-12 d-flex justify-content-center align-items-center d-none">
                    <select class="form-select " aria-label="Default select example" id="selectoption" style="width: 15%;">
                        <option selected>Open this select menu</option>
                    </select>
                </div>
            </div>
    </header>
    <!-- Page Content-->
    <section class="p-1 text-center" style="height_: 100vh;">

        <div class="container px-lg-5">
            <div class="p-4 p-lg-5 bg-light streamdiv rounded-3 text-center " style="background-color: #f7f7f7; height: 950px;">
                <div class="row justify-content-center align-items-center" id="streaming-box">
                    <div class="p-0">
                        <iframe allowfullscreen src="http://49.0.91.113:20080/Vdo1/" class="iframe" scrolling="no" frameborder="0"></iframe>
                    </div>

                    <div class="col-md-12 btn-box pt-3 d-flex align-items-center bottom-bar" style="gap: 1rem;">
                        <div class="col-md-6 d-flex snap-btn" style="justify-content: flex-end; padding: 3px 0;">
                        <button type="button" <?= $getparam == '' ? "style='display: none;'" : null ?> class="btn btn-lg btn-secondary btn-snap" onclick="location.href='<?= $urlimg; ?>'">SNAP SHOT</button>
                        </div>
                        <div class="col-md-6 d-flex vdo-btn" style="justify-content: flex-start; padding: 3px 0;">
                        <button type="button" <?= $getparam == '' ? "style='display: none;'" : null ?> class="btn btn-lg btn-secondary btn-vdo" onclick="location.href='<?= $urlvdo; ?>'">VDO SNAP</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer-->
    <script>
        let roundcheck = 0
        const getparams = '<?= $getparam ?>'

        const Calldata = async () => {
            if(!getparams){
                console.log('No Params')
                return false
            }else{
                const url = `http://49.0.91.113:3000/api/getlogs/${getparams}`
            await fetch(url)
                .then(resp => {
                    if (!resp.ok) {
                        throw new Error('Network response was not ok')
                    }
                    return resp.json()
                })
                .then(resp => {
                    const picstatus = resp.picstatus
                    const vdostatus = resp.vdostatus
                    if (picstatus == 1) {
                        $('.btn-snap').removeClass("btn-secondary")
                        $('.btn-snap').addClass("btn-success")
                    } else {
                        FetchDatas()
                    }
                    if (vdostatus == 1) {
                        $('.btn-vdo').removeClass("btn-secondary")
                        $('.btn-vdo').addClass("btn-success")
                    } else {
                        FetchDatas()
                    }
                })
        }
            }
        Calldata()

        const FetchDatas = async () => {
            if(!getparams){
                console.log('No Params')
                return false
            }else{
            const url = `http://localhost:3000/api/getlogs/${getparams}`
            console.log('Round Check =', roundcheck)
            if (roundcheck == 5) {
                return false
            }
            let time = 60
            console.log('timer: ', time)
            const setinterval = setInterval(async () => {
                time = time - 10
                console.log('timer: ', time)
                if (time == 0) {
                    fetch(url)
                        .then(resp => {
                            if (!resp.ok) {
                                throw new Error('Network response was not ok')
                            }
                            return resp.json()
                        })
                        .then(resp => {
                            const picstatus = resp.picstatus
                            const vdostatus = resp.vdostatus
                            if (picstatus == 1) {
                                clearInterval(setinterval)
                                $('.btn-snap').removeClass("btn-secondary")
                                $('.btn-snap').addClass("btn-success")
                            } else {
                                FetchDatas()
                            }
                            if (vdostatus == 1) {
                                clearInterval(setinterval)
                                $('.btn-vdo').removeClass("btn-secondary")
                                $('.btn-vdo').addClass("btn-success")
                            } else {
                                FetchDatas()
                            }
                        })
                }
            }, 10000)
            roundcheck++
        }
    }
    </script>
</body>
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white" style="letter-spacing: 1px;">Copyright &copy; NetWorklink.Co.Ltd,</p>
    </div>
</footer>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>

</html>