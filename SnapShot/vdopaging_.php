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
    <link rel="stylesheet" href="css/vdopaging_.css">
    <script src="js/jquery-3.7.1.min.js"></script>
    <link href="css/sweetalert2.min.css" rel="stylesheet">
    <script src="js/sweetalert2.all.min.js"></script>
</head>

<style>
    @media only screen and (max-width: 600px) {
        .top-bar {
            flex-direction: column;
        }

        .d-flex {
            display: inline-block !important;
        }

        .selectdiv {
            width: 100%;
        }

        .btn-hide {
            display: none !important;
        }

        .ct {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }
        
        .ctm{
            padding: 0.5rem!important;
        }
    }
</style>

<body>
    <?php
    $myfile = fopen("C:\inetpub\wwwroot\camera\config.txt", "r") or die("Unable to open file!");
    $cfraw = fgets($myfile);
    $cfdatas = json_decode($cfraw, true);
    $futuretimecf = $cfdatas['futuretime'];
    $beforetime = $cfdatas['beforetime'];
    // echo $futuretimecf;
    fclose($myfile);

    if (isset($_GET['param'])) {
        $getparam = $_GET['param'];
        $urlimg = "/SnapShot/snappaging_.php?param={$getparam}";
        $urlvdo = "/SnapShot/vdopaging_.php?param={$getparam}";
        $urlstream = "../LiveNotifyVideo/index.php?param={$getparam}";
    } else {
        $urlimg = "/SnapShot/snappaging_.php";
        $urlvdo = "/SnapShot/vdopaging_.php";
        $urlstream = "../LiveNotifyVideo/";
    }
    ?>
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
                            href="<?= $urlstream ?>">Streamimg</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link" href="<?= $urlimg; ?>">Snapshot</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link active" href="<?= $urlvdo; ?>">Snap Videos</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Header-->
    <header class="py-2 ">
        <div class="container px-lg-5 ">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center bg-dark">
                <div class="">
                    <h1 class="display-5 fw-bold text-white text-uppercase" style="letter-spacing: 5px">Snap Videos</h1>
                </div>
            </div>
    </header>
    <!-- Page Content-->
    <section class="p-1 mb-4">
        <div class="container" style="margin-bottom: 120px;">
            <div class="content pt-0 px-lg-5 ">
                <!-- content -->
                <div class="col-md-12 d-flex top-bar justify-content-between align-items-center py-1">
                    <div class="col-md-4 d-inline-block d-flex py-1 btn-vdo" style="justify-content: flex-start">
                        <button class="btn btn-md btn-secondary" onclick="location.href='<?= $urlimg ?>'">SNAP SHOT</button>
                    </div>
                    <div class="col-md-4 d-inline-block selectdiv">
                        <select id="selectdatas" onchange='selectData()' class="form-select" aria-label="Default select example">
                            <option value="0" selected>เลือกข้อมูลวิดีโอ</option>
                            <?php
                            $subselectfolder = glob("C:/\FTP/*");
                            $subselectfolder = array_map("basename", $subselectfolder);
                            ?>
                            <?php
                            foreach ($subselectfolder as $k => $v) {
                                $namefcam = substr($v, 0, 3);
                                $namefcam .= substr($v, 9, 3);
                                $namefdate = substr($v, 13, 4);
                                $namefdate .= "-" . substr($v, 17, 2);
                                $namefdate .= "-" . substr($v, 19, 2);
                                $namefdate .= " " . substr($v, 22, 2);
                                $namefdate .= ":" . substr($v, 24, 2);
                                $namefdate .= ":" . substr($v, 26, 2);
                                $namefdate = date_create($namefdate);
                                $namefdate = date_format($namefdate, "วันที่ d/m/Y เวลา H:i:s น.");
                                $namefdate = "{$namefcam} {$namefdate}";
                            ?>
                                <option value="<?= $v ?>"><?= $namefdate; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-inline-block d-flex py-1 btn-hide" style="justify-content: flex-end">
                    </div>
                </div>
                <hr>
                <div class="col-md-12 px-5 pt-2 pb-5 rounded-2 ct" style="background-color: #f7f7f7;">
                    <hr>
                    <div id="snappath" class="date py-2">
                        <h4 class="head m-0" id="filedate" style="font-family: Kanit;"></h4>
                    </div>
                    <div class="p-4 content1 ctm" style="background-color: white;">
                        <div class="text-center" id="nodata">
                            <h5 id="nodatah2">อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>
                        </div>
                        <ul class="vdonamex row" id="vdonamex" style="margin: 0; padding:0;"></ul>
                        <hr id="line">
                        <ul class="vdodisplay row" id="vdodisplay" style="margin: 0; padding:0;"></ul>
                        <div class="pagination py-2 flex-wrap" id="pagination" style="display: flex;"></div>
                    </div>
                </div>
            </div>
    </section>
    <!-- Footer-->
    <footer class="py-5 bg-dark footer">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; NetWorklink.Co.Ltd,</p>
        </div>
    </footer>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        let futuretime = '<?= $futuretimecf ?>'
        let beforetime = '<?= $beforetime ?>'
        // let beforetime = parseInt(beforetimeraw)+1
        let roundselectData = 0
        let roundcalldata = 0

        function formatDatefuturetime(selectdatasdatefm) {
            let ndt = new Date(selectdatasdatefm)
            let year = String(ndt.getFullYear()).padStart(2, '0')
            let month = String(ndt.getMonth() + 1).padStart(2, '0')
            let day = String(ndt.getDate()).padStart(2, '0')
            let hours = String(ndt.getHours()).padStart(2, '0')
            ndt.setMinutes(ndt.getMinutes() + parseInt(futuretime)); // Set minutes
            ndt.setSeconds(ndt.getSeconds() + 40); // Set second
            let minutes = String(ndt.getMinutes()).padStart(2, '0')
            let sec = String(ndt.getSeconds()).padStart(2, '0')
            let datefm = `${year}${month}${day}${hours}${minutes}${sec}` //20250401090316 + 2 minute
            return datefm
        }

        function formatDatebeforetime(selectdatasdatefm) {
            let ndt = new Date(selectdatasdatefm)
            let year = String(ndt.getFullYear()).padStart(2, '0')
            let month = String(ndt.getMonth() + 1).padStart(2, '0')
            let day = String(ndt.getDate()).padStart(2, '0')
            let hours = String(ndt.getHours()).padStart(2, '0')
            ndt.setMinutes(ndt.getMinutes() - parseInt(beforetime)); // Set minutes
            ndt.setSeconds(ndt.getSeconds() - 40); // Set second
            let minutes = String(ndt.getMinutes()).padStart(2, '0')
            let sec = String(ndt.getSeconds()).padStart(2, '0')
            let datefm = `${year}${month}${day}${hours}${minutes}${sec}` //20250401090316 + 2 minute
            return datefm
        }

        function pagingSelectDatas(path, json) {
            const items = json;

            const itemsPerPage = 20;
            let currentPage = 1;

            function displayItems2(page) {
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const itemsToDisplay = items.slice(startIndex, endIndex);

                const itemList = document.getElementById('vdodisplay');
                itemList.innerHTML = "";
                let vdodisplay = $('.vdodisplay');
                itemsToDisplay.map(item => {
                    let vdo = '';
                    if (item == "X") {
                        return false;
                    }

                    vdo += `<li class="vdobox col-md-3 p-0" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="http://49.0.91.113:20080/ftpapp/${path}/vdo/${item}" type="video/mp4"></video> </li>`;
                    vdodisplay.append(vdo);

                });
                vdodisplay.fadeOut(100);
                vdodisplay.fadeIn(400);
            }

            function displayPagination2() {
                const totalPages = Math.ceil(items.length / itemsPerPage);
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = "";


                const prevPage = document.createElement('div');
                prevPage.classList.add("page-item");
                prevPage.innerHTML = '<a class="page-link" >Previous</a>';
                prevPage.addEventListener('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination2();
                    }
                });
                pagination.appendChild(prevPage);


                for (let i = 1; i <= totalPages; i++) {
                    const page = document.createElement('div');
                    page.classList.add("page-item");
                    page.classList.toggle('active', i === currentPage);
                    page.innerHTML = `<a class="page-link" >${i}</a>`;
                    page.addEventListener('click', function() {
                        currentPage = i;
                        updatePagination2();
                    });
                    pagination.appendChild(page);
                }

                const nextPage = document.createElement('div');
                nextPage.classList.add("page-item");
                nextPage.innerHTML = '<a class="page-link" >Next</a>';
                nextPage.addEventListener('click', function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination2();
                    }
                });
                if (totalPages > 0) {
                    pagination.appendChild(nextPage);
                }

            }

            function updatePagination2() {
                displayItems2(currentPage);
                displayPagination2();
            }

            updatePagination2();
        }

        function selectData() {
            $('#nodatah2').remove()
            $('.page-item').remove()
            let selectdatas = $('#selectdatas').val()
            let nodata = $("<h5 id='nodatah2'>อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>")
            $('.vdobox').fadeOut(100)
            $('.vdodisplay').fadeOut(100);
            $('.vdonamex').fadeOut(100);
            if (selectdatas == 0) {
                Swal.fire({
                    icon: "error",
                    title: "กรุณาเลือกข้อมูล!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        nodata.appendTo('#nodata')
                        return false
                        // location.reload()
                    }
                });
            } else {
                $.ajax({
                    url: 'vdopagingdata.php',
                    data: `selectdatas=${selectdatas}`,
                    method: 'GET',
                    success: (resp) => {
                        swal.close();
                        let nodata = $("<h5 id='nodatah2'>อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>")
                        $('.vdodisplay').fadeIn(200)
                        let filedate = $('#filedate')
                        let obj = jQuery.parseJSON(resp)
                        let vdonamex = $('.vdonamex')

                        if (obj.vdonames == '' && obj.vdonamexs == '') {
                            Swal.fire({
                                title: "กำลังดึงข้อมูลวิดีโอ!",
                                timer: 2000,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง",
                                    })
                                    nodata.appendTo('#nodata')
                                    return false
                                }
                            })
                        } else {
                            $('#nodatah2').remove()
                            Swal.fire({
                                title: "กำลังดึงข้อมูลวิดีโอ!",
                                timer: 2000,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    filedate.text(`ข้อมูลวันที่: ${obj.filedates}`)
                                    pagingSelectDatas(selectdatas, obj.vdonames)
                                    $.each(obj.vdonamexs, function(i, item) {
                                        let vdox = ''
                                        if (i >= 5) {
                                            return false;
                                        }
                                        vdox += `<li class="vdobox col-md-3 p-0 text-center" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="http://49.0.91.113:20080/ftpapp/${selectdatas}/vdo/x/${item}" type="video/mp4"></video> </li>`;
                                        vdonamex.append(vdox);
                                    })
                                    vdonamex.fadeIn(400)
                                }
                            })
                        }
                    }
                })
            }
        }

        function calldata() {
            let nodata = $('<h5 id="nodatah2" >อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง!</h5>')
            $('#nodatah2').remove()
            let getparams = '<?= $getparam ?>'
            if (!getparams) {
                Swal.fire({
                    title: "กำลังดึงข้อมูลวิดีโอ!!",
                    timer: 3000,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        Swal.fire({
                            icon: "error",
                            title: "โหลดข้อมูลไม่สำเร็จ ไม่มี Params!",
                        })
                        nodata.appendTo('#nodata')
                        return false
                    }
                })
            } else if (roundcalldata == 3) {
                // location.reload()
                Swal.fire({
                    icon: "error",
                    title: "โหลดข้อมูลวิดีโอไม่สำเร็จ!!",
                })
                nodata.appendTo('#nodata')
                return false
            }

            let getparamsdatasdt = getparams.slice(13, 29).replaceAll('_', '')
            let getparamsdatasdty = getparamsdatasdt.slice(0, 4)
            let getparamsdatasdtmth = getparamsdatasdt.slice(4, 6)
            let getparamsdatasdtd = getparamsdatasdt.slice(6, 8)
            let getparamsdatasdth = getparamsdatasdt.slice(8, 10)
            let getparamsdatasdtminute = getparamsdatasdt.slice(10, 12)
            let getparamsdatasdts = getparamsdatasdt.slice(12, 14)
            let getparamsdatasdatefm = `${getparamsdatasdty}-${getparamsdatasdtmth}-${getparamsdatasdtd} ${getparamsdatasdth}:${getparamsdatasdtminute}:${getparamsdatasdts}`
            const beforetime = formatDatebeforetime(getparamsdatasdatefm).slice(-6)
            const futuretime = formatDatefuturetime(getparamsdatasdatefm).slice(-6)

            $.ajax({
                data: `param=${getparams}`,
                url: "vdopagingdata.php",
                type: "GET",
                success: (resp) => {
                    let filedate = $('#filedate')
                    let vdonamex = $('.vdonamex')
                    let obj = jQuery.parseJSON(resp)
                    let vdoname = obj.vdoname[0]

                    if (obj.vdoname == '' && obj.vdonamex == '') {
                        Swal.fire({
                            title: "กำลังดึงข้อมูลวิดีโอ!",
                            timer: 2000,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer) {
                                Swal.fire({
                                    icon: "error",
                                    title: "อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง",
                                })
                                nodata.appendTo('#nodata')
                                return false
                            }
                        })
                    } else {
                        const vdonameraw = vdoname.replaceAll('.', '')
                        const vdonamestart = vdonameraw.replaceAll('-', '').slice(0, 6) //082515
                        const vdonameend = vdonameraw.replaceAll('-', '').slice(6, 12) //082526
                        const getparamfm = getparams.replaceAll('_', '').slice(-6) //082722

                        if (parseInt(vdonamestart) < parseInt(beforetime) && parseInt(vdonameend) < parseInt(futuretime)) {
                            Swal.fire({
                                title: "กำลังดึงข้อมูลวิดีโอ!",
                                timer: 10000,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    roundcalldata++
                                    calldata(getparams)
                                }
                            })
                        } else {
                            $('#nodatah2').remove()
                            Swal.fire({
                                title: "กำลังดึงข้อมูลวิดีโอ!",
                                timer: 2000,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    filedate.text(`ข้อมูลวันที่: ${obj.filedate}`)
                                    paging(obj.vdoname)
                                    $.each(obj.vdonamex, function(i, item) {
                                        let vdo = '';
                                        if (i >= 5) {
                                            return false;
                                        }
                                        vdo += `<li class="vdobox col-md-3 p-0 text-center" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="http://49.0.91.113:20080/ftpapp/<?= $getparam ?>/vdo/x/${item}" type="video/mp4"></video> </li>`;
                                        vdonamex.append(vdo).fadeIn(500);
                                    })
                                }
                            })


                        }
                    }
                },
                error: (data) => {
                    let nodata = $("<h5 id='nodatah2'>อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>")
                    Swal.fire({
                        icon: "error",
                        title: "โหลดข้อมูลวิดีโอไม่สำเร็จ!",
                    })
                    nodata.appendTo('#nodata')
                    return false
                }
            })
        }
        calldata();

        function paging(json) {
            const items = json;
            // ขนาดของแต่ละหน้า
            const itemsPerPage = 20;
            let currentPage = 1;

            // ฟังก์ชันแสดงรายการสินค้า
            function displayItems(page) {
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const itemsToDisplay = items.slice(startIndex, endIndex);

                const itemList = document.getElementById('vdodisplay');
                itemList.innerHTML = "";
                let vdodisplay = $('.vdodisplay');
                itemsToDisplay.map(item => {
                    let vdo = '';
                    if (item == "X") {
                        return false;
                    }
                    // console.log(item);
                    vdo += `<li class="vdobox col-md-3 p-0 text-center" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="http://49.0.91.113:20080/ftpapp/<?= $getparam ?>/vdo/${item}" type="video/mp4"></video> </li>`;
                    vdodisplay.append(vdo);

                });
                vdodisplay.fadeOut(100);
                vdodisplay.fadeIn(400);
            }

            // ฟังก์ชันแสดง Pagination
            function displayPagination() {
                const totalPages = Math.ceil(items.length / itemsPerPage);
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = "";

                // ปุ่มก่อนหน้า
                const prevPage = document.createElement('div');
                prevPage.classList.add("page-item");
                prevPage.innerHTML = '<a class="page-link" >Previous</a>';
                prevPage.addEventListener('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                    }
                });
                pagination.appendChild(prevPage);

                // ปุ่มหน้าทุกหน้า
                for (let i = 1; i <= totalPages; i++) {
                    const page = document.createElement('div');
                    page.classList.add("page-item");
                    page.classList.toggle('active', i === currentPage);
                    page.innerHTML = `<a class="page-link" >${i}</a>`;
                    page.addEventListener('click', function() {
                        currentPage = i;
                        updatePagination();
                    });
                    pagination.appendChild(page);
                }

                // ปุ่มถัดไป
                const nextPage = document.createElement('div');
                nextPage.classList.add("page-item");
                nextPage.innerHTML = '<a class="page-link" >Next</a>';
                nextPage.addEventListener('click', function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination();
                    }
                });
                pagination.appendChild(nextPage);
            }

            // ฟังก์ชันอัพเดต Pagination และแสดงข้อมูล
            function updatePagination() {
                displayItems(currentPage);
                displayPagination();
            }

            // เริ่มต้นการแสดงผล
            updatePagination();
        }
    </script>
</body>

</html>