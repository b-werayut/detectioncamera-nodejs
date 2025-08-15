<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Custom YouTube-Style Video Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body {
            background: #000;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .video-container {
            position: relative;
            width: 800px;
            max-width: 100%;
        }

        video {
            width: 100%;
            height: auto;
            display: block;
        }

        /* .controls {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            padding: 10px;
            box-sizing: border-box;
            color: white;
            font-family: sans-serif;
        } */

        .progress-container {
            position: relative;
            height: 5px;
            background: #444;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .progress {
            background: red;
            height: 100%;
            width: 0%;
        }

        .control-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .left-controls, .right-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .time {
            font-size: 14px;
        }

        .volume-slider {
            width: 100px;
        }

        .control-button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            position: absolute;
            bottom: 0px;
            right: 0px;
        }

        .control-button:hover {
            color: red;
        }
    </style>
</head>
<body>
    <div class="video-container">
        <video id="video" muted playsinline autoplay></video>

        <div class="controls" id="controls">
            <!-- <div class="progress-container" id="progressContainer"> -->
                <!-- <div class="progress" id="progressBar"></div> -->
            </div>

            <div class="control-row">
                <div class="left-controls">
                    <!-- <button class="control-button" id="playPause">▶️</button>
                    <span class="time" id="currentTime">0:00</span> / 
                    <span class="time" id="duration">0:00</span> -->
                </div>
                <div class="right-controls">
                    <!-- <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.01" value="1"> -->
                    <button class="control-button" id="fullscreen">⛶</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const playPauseBtn = document.getElementById('playPause');
        const currentTimeEl = document.getElementById('currentTime');
        const durationEl = document.getElementById('duration');
        const progressBar = document.getElementById('progressBar');
        const progressContainer = document.getElementById('progressContainer');
        const volumeSlider = document.getElementById('volume');
        const fullscreenBtn = document.getElementById('fullscreen');

        // Load HLS stream
        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource('st.m3u8'); // ← เปลี่ยน URL ได้
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => {
                video.play();
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = 'st.m3u8';
            video.addEventListener('canplay', () => {
                video.play();
            });
        }

        // function formatTime(seconds) {
        //     const mins = Math.floor(seconds / 60);
        //     const secs = Math.floor(seconds % 60);
        //     return `${mins}:${secs.toString().padStart(2, '0')}`;
        // }

        // video.addEventListener('timeupdate', () => {
        //     const percent = (video.currentTime / video.duration) * 100;
        //     progressBar.style.width = `${percent}%`;
        //     currentTimeEl.textContent = formatTime(video.currentTime);
        // });

        // video.addEventListener('loadedmetadata', () => {
        //     durationEl.textContent = formatTime(video.duration);
        // });

        // playPauseBtn.addEventListener('click', () => {
        //     if (video.paused) {
        //         video.play();
        //         playPauseBtn.textContent = '⏸️';
        //     } else {
        //         video.pause();
        //         playPauseBtn.textContent = '▶️';
        //     }
        // });

        // progressContainer.addEventListener('click', (e) => {
        //     const rect = progressContainer.getBoundingClientRect();
        //     const clickX = e.clientX - rect.left;
        //     const width = rect.width;
        //     const percent = clickX / width;
        //     video.currentTime = percent * video.duration;
        // });

        // volumeSlider.addEventListener('input', () => {
        //     video.volume = volumeSlider.value;
        // });

        fullscreenBtn.addEventListener('click', () => {
        if (document.fullscreenElement || document.webkitFullscreenElement) {
            document.exitFullscreen?.() || document.webkitExitFullscreen?.();
        } else {
            if (video.requestFullscreen) {
                video.requestFullscreen();
            } else if (video.webkitEnterFullscreen) {
                video.webkitEnterFullscreen(); // iOS Safari
            }
        }
    });

    </script>
</body>
</html>
