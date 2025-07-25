<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Network Link Co., Ltd.</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
  <style>
    .video-js {
      max-width: 100%;
      height: auto;
    }
    html, body {
      overflow: hidden;
      margin: 0;
      background: #000;
    }
  </style>
</head>
<body>
  <video
    id="km_19"
    class="video-js vjs-default-skin vjs-big-play-centered"
    controls
    autoplay
    muted
    playsinline
    data-setup='{"fluid": true}'
  >
    <source src="st.m3u8?v=<?=time()?>" type="application/x-mpegURL" />
  </video>

  <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>

  <script>
    var player = videojs('km_19');
    player.ready(function() {
      player.play();
    });

    player.on('error', function() {
      console.error('VideoJS Error:', player.error());
      alert("เกิดข้อผิดพลาดในการโหลดสตรีม");
    });
  </script>
</body>
</html>
