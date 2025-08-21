<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>วิดีโอแบบขยายได้</title>
  <style>
    body {
      margin: 0;
      padding: 20px;
      font-family: sans-serif;
      background-color: #f0f0f0;
    }

    iframe {
      width: 100%;
      height: 500px;
      border: none;
    }

    button {
      margin-top: 10px;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <h2>ตัวอย่างวิดีโอจาก YouTube</h2>

  <iframe id="myIframe"
    src="https://www.youtube.com/embed/dQw4w9WgXcQ"
    allowfullscreen
    allow="fullscreen">
  </iframe>

  <button onclick="openFullscreen()">เปิดวิดีโอแบบเต็มหน้าจอ</button>

  <script>
    function openFullscreen() {
      const iframe = document.getElementById("myIframe");
      if (iframe.requestFullscreen) {
        iframe.requestFullscreen();
      } else if (iframe.webkitRequestFullscreen) {
        iframe.webkitRequestFullscreen(); // Safari
      } else if (iframe.msRequestFullscreen) {
        iframe.msRequestFullscreen(); // IE11
      } else {
        alert("เบราว์เซอร์ของคุณไม่รองรับ Fullscreen API");
      }
    }
  </script>

</body>
</html>