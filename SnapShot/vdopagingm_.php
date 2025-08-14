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
        $getparam = '';
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
                <div class="row justify-content-between align-items-center py-1">
                    <div class="col-md-3 d-flex py-1 btn-stream" style="justify-content: flex-start;">
                        <button class="btn btn-md btn-secondary" onclick="location.href='../LiveNotifyVideo/'">STREAMING</button>
                    </div>
                    <div class="col-md-3 selectcam d-flex py-1" style="justify-content: flex-end;">
                        <select id="selectcam" onchange="selectCam()" class="form-select" aria-label="Default select example">
                            <option value="0" selected="">เลือกล้อง</option>
                            <?php
                            $subselectfolder = glob("../eventfolder/*");
                            $subselectfolder = array_map("basename", $subselectfolder);
                            ?>
                            <?php
                            foreach ($subselectfolder as $k => $v) {
                            ?>
                                <option value="<?= $v ?>"><?php echo "กล้อง {$v}"; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 selectdiv d-flex py-1" style="justify-content: center;">
                        <select id="selectdatas" onchange="selectData()" class="form-select w-100" aria-label="Default select example" disabled>
                            <option value="0" selected="">กรุณาเลือกกล้องก่อน</option>
                            <option class="selectdataoption" value="CAM202412001"></option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex py-1 btn-vdo" style="justify-content: flex-end;">
                        <button class="btn btn-md btn-secondary" onclick="location.href='/SnapShot/vdopaging_.php'">SNAP VDO</button>
                    </div>
                </div>
                <hr>
                <div class="col-md-12 px-5 pt-2 pb-5 rounded-2 ct" style="background-color: #f7f7f7;">
                    <div id="snappath" class="date mt-2">
                        <span id="filedate" class="badge rounded-pill bg-warning px-3 py-2 text-black" style="font-family: 'Kanit', sans-serif; font-size: 14px;">
                        </span>
                    <hr>
                    </div>
                    <div class="p-4 content1 ctm" style="background-color: white;">
                        <div class="text-center" id="nodata">
                            <h5 id="nodatah2">อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>
                        </div>
                        <ul class="vdonamex row" id="vdonamex" style="margin: 0; padding:0;"></ul>
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
        let snappath = $('#snappath')
        snappath.hide()
        
        function selectCam(){
            const selectcamval = $('#selectcam').val()
            const selectdatasbtn = $('#selectdatas')
            const thaiMonths = [
            "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
            "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            $('.selectdataoption').remove()
            
            if(parseInt(selectcamval) === 0){
                selectdatasbtn.attr('disabled', 'disabled')
            }else{
                selectdatasbtn.removeAttr('disabled', 'disabled')

                $.ajax({
                url: 'vdopagingdata.php',
                data: `selectcamval=${selectcamval}`,
                method: 'GET',
                success: (resp) => {
                    let obj = jQuery.parseJSON(resp)
                    if (obj.datas == '') {
                        alert("nodatas")
                    }
                    // console.log(obj.datas)
                    $.each(obj.datas, (i, items)=>{
                        let datasoption = ''
                        let datas = items
                        let datassplit = datas.split("_")
                        let camname = datassplit[0]
                        let camnamedisplay = `กล้อง ${camname}`
                        let day = datassplit[1].slice(6)
                        let month = datassplit[1].slice(4,6)
                        let monththai = thaiMonths[parseInt(month)-1]
                        let year = datassplit[1].slice(0,4)
                        let yearthai = parseInt(year) + 543
                        let hour = datassplit[2].slice(0,2)
                        let minute = datassplit[2].slice(2,4)
                        let sec = datassplit[2].slice(4)
                        let datetimedisplay = `วันที่ ${day} ${monththai} ${yearthai} เวลา ${hour}:${minute}:${sec}`
                        datasoption += `<option class="selectdataoption" value="${items}">${datetimedisplay}</option>`
                        selectdatasbtn.append(datasoption)
                    })
                },
                error: (data)=>{
                    Swal.fire({
                        icon: "error",
                        title: "อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง!",
                    })
                    $('#nodatah2').appendTo('#nodata')
                }
            })
            }
        }

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

        function pagingSelectDatas(path, json, camnamef) {
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

                    vdo += `<li class="vdobox col-md-3 p-0" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="/eventfolder/${camnamef}/${path}/vdo/${item}" type="video/mp4"></video> </li>`;
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

        function formatDate(selectdatasdatefm) {
            let ndt = new Date(selectdatasdatefm)
            let year = String(ndt.getFullYear()).padStart(2, '0')
            let month = String(ndt.getMonth() + 1).padStart(2, '0')
            let day = String(ndt.getDate()).padStart(2, '0')
            let hours = String(ndt.getHours()).padStart(2, '0')
            ndt.setMinutes(ndt.getMinutes() + parseInt(futuretime)); // Set time
            let minutes = String(ndt.getMinutes()).padStart(2, '0')
            let sec = String(ndt.getSeconds()).padStart(2, '0')
            let datefm = `${year}${month}${day}${hours}${minutes}${sec}` //20250401090316 + 2 minute
            return datefm
        }

        function selectData() {
            $('#nodatah2').remove()
            $('.page-item').remove()
            let selectdatas = $('#selectdatas').val()
            let nodata = $("<h5 id='nodatah2'>อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>")
            let selectdatasval = $('#selectdatas').val()
            let camname = selectdatasval.split("_");
            let camnamef = camname[0];
            let selectdatasdt = selectdatasval.slice(13, 29).replaceAll('_', '') // 20250401090316
            let selectdatasdty = selectdatasdt.slice(0, 4)
            let selectdatasdtmth = selectdatasdt.slice(4, 6)
            let selectdatasdtd = selectdatasdt.slice(6, 8)
            let selectdatasdth = selectdatasdt.slice(8, 10)
            let selectdatasdtminute = selectdatasdt.slice(10, 12)
            let selectdatasdts = selectdatasdt.slice(12, 14)
            let selectdatasdatefm = `${selectdatasdty}-${selectdatasdtmth}-${selectdatasdtd} ${selectdatasdth}:${selectdatasdtminute}:${selectdatasdts}`
            
            const futuretime = formatDate(selectdatasdatefm)

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
                                    snappath.fadeIn(function (){
                                        filedate.text(`ข้อมูลวันที่: ${obj.filedates}`)
                                    })
                                    pagingSelectDatas(selectdatas, obj.vdonames, camnamef)
                                    $.each(obj.vdonamexs, function(i, item) {
                                        let vdox = ''
                                        if (i >= 5) {
                                            return false;
                                        }
                                        vdox += `<li class="vdobox col-md-3 p-0 text-center" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="/eventfolder/${camnamef}/${selectdatas}/vdo/x/${item}" type="video/mp4"></video> </li>`;
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
            if (!getparams || getparams.trim() === '') {
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
                            title: "โหลดข้อมูลไม่สำเร็จ",
                        });
                        nodata.appendTo('#nodata');
                    }
                });
                return;
            } else if (roundcalldata == 3) {
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
                                     snappath.fadeIn(function (){
                                        filedate.text(`ข้อมูลวันที่: ${obj.filedates}`)
                                    })
                                    paging(obj.vdoname)
                                    $.each(obj.vdonamex, function(i, item) {
                                        let vdo = '';
                                        if (i >= 5) {
                                            return false;
                                        }
                                        vdo += `<li class="vdobox col-md-3 p-0 text-center" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="/eventfolder/<?= $getparam ?>/vdo/x/${item}" type="video/mp4"></video> </li>`;
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
            const itemsPerPage = 20;
            let currentPage = 1;

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
                    vdo += `<li class="vdobox col-md-3 p-0 text-center" > <video width="320" height="240" muted controls class="img-thumbnail"><source class="vdobox col-md-3 p-0" src="/eventfolder/<?= $getparam ?>/vdo/${item}" type="video/mp4"></video> </li>`;
                    vdodisplay.append(vdo);

                });
                vdodisplay.fadeOut(100);
                vdodisplay.fadeIn(400);
            }

            function displayPagination() {
                const totalPages = Math.ceil(items.length / itemsPerPage);
                const pagination = document.getElementById('pagination');
                pagination.innerHTML = "";

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

            function updatePagination() {
                displayItems(currentPage);
                displayPagination();
            }

            updatePagination();
        }
    </script>
</body>

</html>