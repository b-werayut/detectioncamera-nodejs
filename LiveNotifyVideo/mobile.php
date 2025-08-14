<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>NetWorklink.Co.Ltd,</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="fonts/font-kanit.css" />
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/snappaging_.css">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>

<style>
    #streamContainer {
        gap: 8px;
    }

    .video-wrapper {
        width: 390px;
        height: 304px;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    #cameraDropdown {
        max-width: 400px;
        max-height: 200px;
        overflow-y: auto;
    }


    .camera-status {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 8px;
    }

    .online {
        background-color: #28a745;
    }

    .offline {
        background-color: #dc3545;
    }

    .video-wrapper {
        margin: 10px;
        position: relative;
    }

    .dropdown-custom {
        border: 1px solid black;
        border-radius: 10px;
        padding: 10px;
        width: 300px;
        margin-top: 10px;
    }

    .camera-item {
        display: flex;
        align-items: center;

    }

    .camera-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
    }

    .camera-label {
        flex-grow: 1;
        font-weight: bold;
    }


    .camera-item input[type="checkbox"] {
        -webkit-appearance: checkbox !important;
        -moz-appearance: checkbox !important;
        appearance: checkbox !important;
        width: 18px !important;
        height: 18px !important;
        margin-right: 10px !important;
        opacity: 1 !important;
        display: inline-block !important;
        visibility: visible !important;
    }
</style>

<body>
    <?php
    if (isset($_GET['param'])) {
        $getparam = $_GET['param'];
        $urlimg = "../SnapShot/snappagingm_.php?param={$getparam}";
        $urlvdo = "../SnapShot/vdopagingm_.php?param={$getparam}";
    } else {
        $urlimg = "../SnapShot/snappagingm_.php";
        $urlvdo = "../SnapShot/vdopagingm_.php";
        $getparam = '';
    }
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container px-lg-5">
            <img src="../snapshot/assets/nwl-logo.png" alt="NetWorklink" width="50">
            <span style="letter-spacing: 1px;" class="text-white" href="#!">NetWorklink.Co.Ltd,</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item bg-dark"><a class="nav-link active" aria-current="page"
                            href="/LiveNotifyVideo/">Streamimg</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link" href="<?= $urlimg; ?>">Snapshot</a></li>
                    <li class="nav-item bg-dark"><a class="nav-link" href="<?= $urlvdo; ?>">Snap Videos</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <header class="py-2 ">
        <div class="container px-lg-5 ">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center bg-dark">
                <div class="">
                    <h1 class="display-5 fw-bold text-white text-uppercase" style="letter-spacing: 10px">Streaming</h1>
                </div>
                <div class="col-md-12 d-flex justify-content-center align-items-center d-none">
                    <select class="form-select " aria-label="Default select example" id="selectoption"
                        style="width: 15%;">
                        <option selected>Open this select menu</option>
                    </select>
                </div>
            </div>
    </header>
    <section class="p-1 text-center" style="height_: 100vh;">
        <div class="container text-center mt-3 position-relative">
            <button class="btn btn-outline-dark mb-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#cameraDropdown" style="min-width: 300px; width: 300px; max-width: 100%;">
                Select Camera
            </button>

            <div id="cameraDropdown" class="collapse dropdown-menu p-3 mx-auto"
                style="width: 300px; left: 50%; transform: translateX(-50%);">
                </div>
        </div>

        <div id="streamContainer" class="mt-4 row g-3 container mx-auto overflow-auto" style="max-height: 500px;">
            </div>
    </section>

    <script>
        let roundcheck = 0;
        const getparams = '<?= $getparam; ?>';
        const BASE_URL = 'http://85.204.247.82';
        const PORT = '26300';
        const API_PATH = 'api/getCameraStat';
        const CAMERA_STATS_API_URL = `${BASE_URL}:${PORT}/${API_PATH}`;

    

        const Calldata = async () => {
            if (!getparams) {
                console.log('No Params');
                return false;
            } else {
                const url = `http://85.204.247.82:26300/api/getlogs/${getparams}`;
                await fetch(url)
                    .then(resp => {
                        if (!resp.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return resp.json();
                    })
                    .then(resp => {
                        const picstatus = resp.picstatus;
                        const vdostatus = resp.vdostatus;
                        if (picstatus == 1) {
                            $('.btn-snap').removeClass("btn-secondary").addClass("btn-success");
                        } else {
                            FetchDatas();
                        }
                        if (vdostatus == 1) {
                            $('.btn-vdo').removeClass("btn-secondary").addClass("btn-success");
                        } else {
                            FetchDatas();
                        }
                    });
            }
        };
        Calldata();

        const FetchDatas = async () => {
            if (!getparams) {
                console.log('No Params');
                return false;
            } else {
                const url = `http://85.204.247.82:26300/api/getlogs/${getparams}`;
                console.log('Round Check =', roundcheck);
                if (roundcheck == 5) {
                    return false;
                }
                let time = 60;
                console.log('timer: ', time);
                const setinterval = setInterval(async () => {
                    time = time - 10;
                    console.log('timer: ', time);
                    if (time == 0) {
                        await fetch(url)
                            .then(resp => {
                                if (!resp.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return resp.json();
                            })
                            .then(resp => {
                                const picstatus = resp.picstatus;
                                const vdostatus = resp.vdostatus;
                                if (picstatus == 1) {
                                    clearInterval(setinterval);
                                    $('.btn-snap').removeClass("btn-secondary").addClass("btn-success");
                                } else {
                                    FetchDatas();
                                }
                                if (vdostatus == 1) {
                                    clearInterval(setinterval);
                                    $('.btn-vdo').removeClass("btn-secondary").addClass("btn-success");
                                } else {
                                    FetchDatas();
                                }
                            });
                    }
                }, 10000);
                roundcheck++;
            }
        };

        async function fetchCameraStatusesFromAPI() {
            try {
                const response = await fetch(`${CAMERA_STATS_API_URL}?v=${Date.now()}`, {

                    headers: {
                        
                        'cache-control': 'no-cache'
                    }
                    
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                console.log('API Camera Stats Response:', data); 
                if (data.msg === "Success" && Array.isArray(data.cameraStat)) {
                    return data.cameraStat;
                } else {
                    console.error('API returned an unexpected format or error:', data);
                    return [];
                }
            } catch (error) {
                console.error('Error fetching camera stats from API:', error);
                return [];
            }
        }

        async function renderCameraDropdown() {
            const dropdown = document.getElementById('cameraDropdown');
            let i = 1
            dropdown.innerHTML = ''; 

            const apiCameraStats = await fetchCameraStatusesFromAPI();

            console.log('apiCameraStats (after fetch):>> ', apiCameraStats); 


            apiCameraStats.forEach(cameraData => {
                const cameraName = cameraData.camera; 

                console.log('cameraData :>> ', cameraData);

                const apiStatus = parseInt(cameraData.status, 10);

                const STREAMING_PORT = 26080

                const STREAMING_BASE_URL = `${BASE_URL}:${STREAMING_PORT}/detectionstreaming`

                const FULL_STREAMING_URL = `${STREAMING_BASE_URL}/${cameraName}`


                console.log(`Processing camera: ${cameraName}, Stream URL: ${FULL_STREAMING_URL}`); 

                if (!FULL_STREAMING_URL) {
                    console.warn(`No stream URL defined for camera: ${cameraName}. Skipping.`);
                    return; 
                }

                const htmlId = `camera-${cameraName.replace(/[^a-zA-Z0-9]/g, '-')}`;
                const statusClass = apiStatus === 1 ? 'online' : 'offline';


                const cameraItem = document.createElement('div');
                cameraItem.className = 'camera-item form-check';
                cameraItem.innerHTML = `
                    <input class="form-check-input"
                        type="checkbox"
                        id="${htmlId}"
                        data-camera-name="${cameraName}"
                        data-stream-url="${FULL_STREAMING_URL}"
                        data-status="${apiStatus}"
                        > <label class="form-check-label camera-label"
                        for="${htmlId}">Camera: ${i}</label> <span id="${htmlId}-status"
                        class="camera-status ${statusClass}"></span> `;

                dropdown.appendChild(cameraItem);
                i++
            });

            const checkboxes = document.querySelectorAll('.form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateStreams);
            });
        }

        async function updateAllCameraStatusesFromAPIAndUI() {
            const apiCameraStats = await fetchCameraStatusesFromAPI();

            document.querySelectorAll('.camera-item input[type="checkbox"]').forEach(checkbox => {
                const cameraName = checkbox.dataset.cameraName; 
                const statusElement = document.querySelector(`#${checkbox.id}-status`);

                if (cameraName && statusElement) {

                    const cameraInfo = apiCameraStats.find(cam => cam.camera === cameraName);
                    const apiStatus = cameraInfo ? cameraInfo.status : 0; // ถ้าไม่พบ ถือว่าเป็น offline

                    statusElement.classList.remove('online', 'offline');
                    if (apiStatus === 1) {
                        statusElement.classList.add('online');
                    } else {
                        statusElement.classList.add('offline');
                    }
                }
            });
        }


        async function updateStreams() {
            const $streamContainer = $('#streamContainer');
            $streamContainer.empty();

            const selectedCheckboxes = document.querySelectorAll('.form-check-input');
            const selected = Array.from(selectedCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => ({
                    name: cb.nextElementSibling.textContent.trim(),
                    streamUrl: cb.dataset.streamUrl,
                    apiStatus: parseInt(cb.dataset.status, 10)
                }));

            if (selected.length === 0) {
                $streamContainer.html('<p class="text-center w-100">Please select at least one camera</p>');
            } else {
                for (const stream of selected) {
                    const cameraStatus = stream.apiStatus;
                    const overlayStatusClass = cameraStatus === 1 ? 'online' : 'offline';

                    const $videoWrapper = $('<div></div>')
                        .addClass('video-wrapper position-relative')
                        .css('flex', '0 0 auto')
                        .hide();

                    const $overlay = $(`
                        <div style="position: absolute; top: 0; left: 0;
                                    background-color: rgba(0,0,0,0.7); color: white;
                                    padding: 2px 5px; font-size: 12px; z-index: 10;">
                            ${stream.name}
                            <span class="camera-status ${overlayStatusClass}"
                                style="display: inline-block; margin-left: 5px;"></span>
                        </div>
                    `);

                    if (cameraStatus === 0) {
                        const offlineContent = $(`
                            <div style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; background-color: #333; color: white; text-align: center; padding: 20px; border-radius: 4px;">
                                <p style="font-size: 1.2em; margin-bottom: 10px;">Camera is not available</p>
                            </div>
                        `);
                        $videoWrapper.append($overlay).append(offlineContent);
                        $streamContainer.append($videoWrapper);
                        $videoWrapper.fadeIn(800);
                    } else {
                        const $iframe = $('<iframe>', {
                            src: stream.streamUrl,
                            width: '100%',
                            height: '100%',
                            frameborder: '0',
                            allowfullscreen: true,
                            sandbox: 'allow-scripts allow-same-origin',
                            css: {
                                display: 'block'
                            }
                        });

                        let isLoaded = false;

                        const loadTimeout = setTimeout(() => {
                            if (!isLoaded) {
                                $iframe.remove();

                                const errorContent = $(`
                                    <div style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; background-color: #333; color: white; text-align: center; padding: 20px; border-radius: 4px;">
                                        <p style="font-size: 1.2em; margin-bottom: 10px;">Failed to load video (timeout)</p>
                                    </div>
                                `);
                                $videoWrapper.append(errorContent).fadeIn(800);
                            }
                        }, 10000);

                        $iframe.on('load', function () {
                            if (!isLoaded) {
                                isLoaded = true;
                                clearTimeout(loadTimeout);
                                $videoWrapper.fadeIn(800);
                            }
                        });

                        $videoWrapper.append($overlay).append($iframe);
                        $streamContainer.append($videoWrapper);
                    }
                }
            }
        }


            const streamContainer = document.getElementById('streamContainer');
            streamContainer.innerHTML = '';
            const selectedCheckboxes = document.querySelectorAll('.form-check-input');

            console.log('selectedCheckboxes :>> ', selectedCheckboxes);
            const selected = Array.from(selectedCheckboxes)
                .filter(item => item.checked)
                .map(item => ({
                    name: item.nextElementSibling.textContent.trim(),
                    streamUrl: item.dataset.streamUrl,
                    status: item.dataset.status,
                }));

            if (selected.length === 0) {
                    $('#streamContainer').html('<p class="text-center w-100">Please select at least one camera</p>');
                } else {
                    for (const stream of selected) {
                        const videoWrapper = document.createElement('div');
                        const cameraStatus = Number(stream.status);
                        videoWrapper.className = 'video-wrapper position-relative';
                        videoWrapper.style.flex = '0 0 auto';

                        videoWrapper.innerHTML = `
                            <div style="position: absolute; top: 0; left: 0;
                                        background-color: rgba(0,0,0,0.7); color: white;
                                        padding: 2px 5px; font-size: 12px; z-index: 10;">
                                ${stream.name}
                                <span class="camera-status ${cameraStatus ? 'online' : 'offline'}"
                                    style="display: inline-block; margin-left: 5px;"></span>
                            </div>
                            <iframe src="${stream.streamUrl}"
                                    width="100%"
                                    height="100%"
                                    frameborder="0"
                                    allowfullscreen
                                    sandbox="allow-scripts allow-same-origin"
                                    style="display: block;"></iframe>`;

                        $(videoWrapper).hide();
                        $('#streamContainer').append(videoWrapper);
                        $(videoWrapper).fadeIn(800);
                    }
                }
        
        document.addEventListener('DOMContentLoaded', async function () {
            const dropdownButton = document.querySelector('[data-bs-target="#cameraDropdown"]');
            const dropdownMenu = document.getElementById('cameraDropdown');

            document.addEventListener('click', function (event) {
                const isClickInside = dropdownMenu.contains(event.target) ||
                    dropdownButton.contains(event.target);

                if (!isClickInside && dropdownMenu.classList.contains('show')) {
                    const collapseInstance = bootstrap.Collapse.getInstance(dropdownMenu);
                    if (collapseInstance) {
                        collapseInstance.hide();
                    }
                }
            });

            await renderCameraDropdown(); 
            await updateStreams(); 
            setInterval(updateAllCameraStatusesFromAPIAndUI, 1000); 
        });
    </script>
</body>
<div style="position: fixed; bottom: 0; right: 0; width: 100%">
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white" style="letter-spacing: 1px;">Copyright &copy; NetWorklink.Co.Ltd,</p>
    </div>
</footer>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

</html>