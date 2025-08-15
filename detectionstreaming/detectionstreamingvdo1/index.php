<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8 />
    <title>Network Link Co., Ltd.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <script src="../../css/video.js"></script>
	<script src="../../css/hls.js"></script>
    <style>
        video {
            width: 100%;
            height: auto;
        }
        html,
        body {
            overflow-y: hidden;
            overflow-x: hidden;
        }
        @media only screen and (max-width: 600px) {
            #km_19 {
                width: 350px;
                height: 350px;
            }
        }
        @media only screen and (min-width: 601px) {
            #km_19 {
                width: 790px;
                height: 650px;
            }
        }
    </style>
</head>
<body>
    <html>
    <body>
        <video id="video" muted playsinline controls autoplay ></video>
        <script>
            if (Hls.isSupported()) {
                var video = document.getElementById('video');
                var hls = new Hls();
                hls.loadSource('st.m3u8');
                hls.attachMedia(video);
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    video.play();
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = 'st.m3u8';
                video.addEventListener('canplay', function() {
                    video.play();
                });
            }
        </script>
    </body>
</body>
</html>