<?php
session_start();
include '../auth/auth_check.php';
?>
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
    <link href="css/sweetalert2.min.css" rel="stylesheet">
    <script src="js/sweetalert2.all.min.js"></script>
</head>

<style>
    @media only screen and (max-width: 600px) {
        .top-bar {
            display: flex !important;
        }

        .btn-stream {
            display: none !important;
        }

        .btn-vdo {
            display: none !important;
        }

        .selectdiv {
            width: 100% !important;
        }

        .ct {
            padding-right: 1rem !important;
            padding-left: 1rem !important;
        }

        .ctm {
            padding: 0.5rem !important;
        }
    }
</style>

<body>
    <?php
    $myfile = fopen("C:\inetpub\wwwroot\camera\config.txt", "r") or die("Unable to open file!");
    $cfraw = fgets($myfile);
    $cfdatas = json_decode($cfraw, true);
    $futuretimecf = $cfdatas['futuretime'];
    fclose($myfile);
    ?>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-lg-5">
            <img src="assets/nwl-logo.png" alt="NetWorklink" width="50">
            <span style="letter-spacing: 1px;" class="text-white" href="#!">NetWorklink.Co.Ltd,</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item bg-dark"><a class="nav-link" aria-current="page" href="../LiveNotifyVideo/">Streamimg</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link active" href="/SnapShot/snappaging_.php">Snapshot</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link" href="/SnapShot/vdopaging_.php">Snap Videos</a></li>
                    <?php
                    if($logout){
                    "<li class='nav-item bg-dark'><a class='nav-link' href='../logout.php'>Logout</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Header-->
    <header class="py-2 ">
        <div class="container px-lg-5 ">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center bg-dark">
                <div class="">
                    <h1 class="display-5 fw-bold text-white text-uppercase" style="letter-spacing: 5px">Snapshot</h1>
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
                            <option value="0" selected="">เลือกกล้อง</option>
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
                            <option class="selectdataoption" value="CAM202412001">วันที่ 01/01/1970 เวลา 00:00:00 น.</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex py-1 btn-vdo" style="justify-content: flex-end;">
                        <button class="btn btn-md btn-secondary" onclick="location.href='/SnapShot/vdopaging_.php'">SNAP VDO</button>
                    </div>
                </div>
                <hr>
                <div class="col-md-12 px-5 pt-2 pb-5 rounded-2 justify-between ct" style="background-color: #f7f7f7;">
                    <div id="snappath" class="date mt-2">
                        <span id="filedate" class="badge rounded-pill bg-warning px-3 py-2 text-black" style="font-family: 'Kanit', sans-serif; font-size: 14px;">
                        </span>
                    <hr>
                    </div>
                    <div class="p-4 content1 ctm" style="background-color: white;">
                        <div class="text-center" id="nodata">
                            <h5 id="nodatah2">กรุณาเลือกข้อมูล</h5>
                        </div>
                        <ul class="imgnamex row" id="imgnamex" style="margin: 0; padding:0;"></ul>
                        <ul class="imgdisplay row" id="imgdisplay" style="margin: 0; padding:0;"></ul>
                        <div class="pagination py-2 d-flex flex-wrap" id="pagination"></div>
                    </div>
                </div>
            </div>
    </section>
    <!-- Footer-->
    <footer class="py-2 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white" style="letter-spacing: 1px;">Copyright &copy; NetWorklink.Co.Ltd,</p>
    </div>
</footer>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $('#selectdatas').attr('disabled', 'disabled')
        let futuretime = '<?= $futuretimecf ?>'
        let roundselectData = 0
        let roundcalldata = 0
        let snappath = $('#snappath')
        snappath.hide()

        function disabledfunct(){
            const selectcamval = $('#selectcam').val()
            if(parseInt(selectcamval) === 0){
                alert(55555)
            }
        }

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
                url: 'snappagingdata.php',
                data: `selectcamval=${selectcamval}`,
                method: 'GET',
                success: (resp) => {
                    let obj = jQuery.parseJSON(resp)
                    if (obj.datas == '') {
                        $('#selectdatas').prop('disabled', true);
                        Swal.fire({
                        title: "ไม่มีข้อมูล!",
                        icon: "warning",
                        position: "center",
                        confirmButtonText: "ตกลง",
                    });
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
            let nodata = $('<h5 id="nodatah2" >อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง</h5>')
            $('#nodatah').remove()
            $('#nodatah2').remove()
            
            $('.page-item').remove()
            $('#nodatah2').remove()
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

            $('.imgbox').fadeOut(100);
            $('.imgdisplay').fadeOut(100);
            $('.imgnamex').fadeOut(100);
            if (selectdatasval == 0) {
                Swal.fire({
                    icon: "error",
                    title: "กรุณาเลือกข้อมูล!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    title: "กำลังดึงข้อมูลข้อมูลรูปภาพ!",
                    timer: 2000,
                    didOpen: () => {
                        Swal.showLoading();
                        const timer = Swal.getPopup().querySelector("b");
                    },
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        $.ajax({
                            url: 'snappagingdata.php',
                            data: `selectdatas=${selectdatasval}`,
                            method: 'GET',
                            success: (resp) => {
                                let obj = jQuery.parseJSON(resp)
                                if (obj.imgnames == '' && obj.imgnamexs == '') {
                                    $('.page-item').remove()
                                    Swal.fire({
                                        icon: "error",
                                        title: "อาจเกิดจากระบบยังดึงข้อมูลมาไม่ทัน ให้ลองใหม่ภายหลัง",
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // location.reload()
                                            nodata.appendTo('#nodata')
                                            return false
                                        }
                                    });
                                } else {
                                    let imgpic = obj.imgnames[obj.imgnames.length - 1]?.slice(4, 18)
                                    if (parseInt(imgpic) < parseInt(futuretime - 30)) {
                                        roundselectData++
                                        if (roundselectData == 3) {
                                            roundselectData = 0
                                            if(imgpic.length <= 0){
                                            Swal.fire({
                                                icon: "error",
                                                title: "โหลดข้อมูลไม่สำเร็จ!",
                                            });
                                            $('.imgdisplay').remove()
                                            $('.imgnamex').remove()
                                            nodata.appendTo('#nodata')
                                            }
                                            return false
                                        }
                                        //selectData(selectdatasval)
                                    } else {
                                        swal.close()
                                        $('.imgdisplay').fadeIn(200)
                                        let filedate = $('#filedate')
                                        let imgnamex = $('.imgnamex')
                                        snappath.fadeIn(function (){
                                        filedate.text(`ข้อมูลวันที่: ${obj.filedates}`)
                                        })
                                        pagingSelectDatas(selectdatasval, obj.imgnames, camnamef)
                                        $.each(obj.imgnamexs, function(i, item) {
                                            let imgx = '';
                                            console.log(item);
                                            if (i >= 8) { // show x img
                                                return false;
                                            }
                                            imgx += `<li class="imgbox col-md-3 p-0"><img class="img-thumbnail" onclick="showimgx2('${selectdatasval}','${item}','${camnamef}')" src="/eventfolder/${camnamef}/${selectdatasval}/pic/X/${item}" "></li>`;
                                            imgnamex.append(imgx);
                                        })
                                        imgnamex.fadeIn(400);
                                        $('#nodatah2').remove();
                                    }
                                    $('.imgdisplay').fadeIn(200)
                                        let filedate = $('#filedate')
                                        let imgnamex = $('.imgnamex')
                                        snappath.fadeIn(function (){
                                        filedate.text(`ข้อมูลวันที่: ${obj.filedates}`)
                                        })
                                        pagingSelectDatas(selectdatasval, obj.imgnames, camnamef)
                                        $.each(obj.imgnamexs, function(i, item) {
                                            let imgx = '';
                                            console.log(item);
                                            if (i >= 8) { // show x img
                                                return false;
                                            }
                                            imgx += `<li class="imgbox col-md-3 p-0"><img class="img-thumbnail" onclick="showimgx2('${selectdatasval}','${item}','${camnamef}')" src="/eventfolder/${camnamef}/${selectdatasval}/pic/X/${item}" "></li>`;
                                            imgnamex.append(imgx);
                                        })
                                        imgnamex.fadeIn(400);
                                        $('#nodatah2').remove();
                                }
                            },
                            error: function(data) {
                                Swal.fire({
                                    icon: "error",
                                    title: "โหลดข้อมูลไม่สำเร็จ!",
                                });
                                nodata.appendTo('#nodata')
                                return false
                            }
                        })
                    }
                })
            }
        }

        function pagingSelectDatas(path, json, camname) {
            const items = json;
            if (!items) {
                return false
            }

            const itemsPerPage = 12;
            let currentPage = 1;

            function displayItems2(page) {
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const itemsToDisplay = items.slice(startIndex, endIndex);

                const itemList = document.getElementById('imgdisplay');
                itemList.innerHTML = "";
                let imgdisplay = $('.imgdisplay');
                itemsToDisplay.map(item => {
                    let img = ''
                    if (item == "X") {
                        return false;
                    }
                    // console.log(item);
                    img += `<li class="imgbox col-md-3 p-0"><img class="img-thumbnail" onclick="showimg2('${path}', '${item}', '${camname}')" src="/eventfolder/${camname}/${path}/pic/${item}"></li>`;
                    imgdisplay.append(img);

                });
                imgdisplay.fadeOut(100);
                imgdisplay.fadeIn(400);
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

        function paging(json) {
            const items = json;
            if (!items) {
                return false
            }
            const itemsPerPage = 12;
            let currentPage = 1;

            function displayItems(page) {
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const itemsToDisplay = items.slice(startIndex, endIndex);

                const itemList = document.getElementById('imgdisplay');
                itemList.innerHTML = "";
                let imgdisplay = $('.imgdisplay');
                itemsToDisplay.map(item => {
                    let img = '';
                    if (item == "X") {
                        return false;
                    }
                    // console.log(item);
                    img += `<li class="imgbox col-md-3 p-0"><img class="img-thumbnail" onclick="showimg('${item}')" src="/eventfolder/${camname}/${path}/pic/${item}"></li>`;
                    imgdisplay.append(img);

                });
                imgdisplay.fadeOut(100);
                imgdisplay.fadeIn(400);
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

        function showimg2(path, img, camname) {
            Swal.fire({
                imageUrl: `/eventfolder/${camname}/${path}/pic/${img}`,
                imageWidth: 600,
                imageHeight: 400,
                width: 650,
            });
        }

        function showimgx2(path, img, camname) {
            Swal.fire({
                imageUrl: `/eventfolder/${camname}/${path}/pic/X/${img}`,
                imageWidth: 600,
                imageHeight: 400,
                width: 650,
            });
        }

    </script>
</body>

</html>