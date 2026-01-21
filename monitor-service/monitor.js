const http = require("http");
const { exec } = require("child_process");

const API_HOST = "127.0.0.1";
const API_PORT = 9997;

const CHECK_INTERVAL = 5000; // 5 วิ
const FREEZE_TIMEOUT = 15000; // 15 วิ (ภาพไม่ขยับ)

const cameras = [
  {
    name: "nptcam01",
    path: "nptcam01",
    status: "UNKNOWN",
    lastBytes: 0,
    lastBytesTime: 0,
  },
  {
    name: "CAM_BACK",
    path: "cam2",
    status: "UNKNOWN",
    lastBytes: 0,
    lastBytesTime: 0,
  },
];

function fetchPaths() {
  return new Promise((resolve, reject) => {
    http
      .get(`http://${API_HOST}:${API_PORT}/v3/paths/list`, (res) => {
        let data = "";
        res.on("data", (c) => (data += c));
        res.on("end", () => resolve(JSON.parse(data)));
      })
      .on("error", reject);
  });
}

async function monitor() {
  console.log("--------------------------------");
  console.log("=== MediaMTX RTMP PUSH MONITOR ===");
  console.log(new Date().toLocaleString());

  try {
    const result = await fetchPaths();
    const items = result.items || [];
    const now = Date.now();

    cameras.forEach((cam) => {
      const pathInfo = items.find((p) => p.name === cam.path);

      // ===== OFFLINE =====
      if (
        !pathInfo ||
        pathInfo.ready !== true ||
        !pathInfo.source ||
        pathInfo.source.type !== "rtmpConn"
      ) {
        alertIfChanged(cam, "OFFLINE");
        console.log(`🔴 ${cam.name} (${cam.path}) OFFLINE`);
        return;
      }

      // ===== FROZEN CHECK =====
      const currentBytes = pathInfo.bytesReceived || 0;

      if (cam.lastBytes === 0) {
        cam.lastBytes = currentBytes;
        cam.lastBytesTime = now;
        cam.status = "ONLINE";
        console.log(`🟢 ${cam.name} (${cam.path}) ONLINE`);
        return;
      }

      if (currentBytes > cam.lastBytes) {
        cam.lastBytes = currentBytes;
        cam.lastBytesTime = now;
        alertIfChanged(cam, "ONLINE");
        console.log(`🟢 ${cam.name} (${cam.path}) ONLINE`);
        return;
      }

      if (now - cam.lastBytesTime > FREEZE_TIMEOUT) {
        alertIfChanged(cam, "FROZEN");
        console.log(`🟡 ${cam.name} (${cam.path}) FROZEN`);
        return;
      }

      // still online but waiting
      cam.status = "ONLINE";
      console.log(`🟢 ${cam.name} (${cam.path}) ONLINE`);
    });
  } catch (err) {
    console.log("❌ Cannot connect to MediaMTX API");
  }
}

// ===== ALERT HANDLER =====
function alertIfChanged(cam, newStatus) {
  if (cam.status === newStatus) return;

  cam.status = newStatus;
  process.stdout.write("\x07"); // beep

  // popup (เปิดถ้าต้องการ)
  // exec(
  //   `powershell -command "Add-Type -AssemblyName PresentationFramework;[System.Windows.MessageBox]::Show('${cam.name} ${newStatus}','MediaMTX ALERT')"`
  // );
}

// ===== START =====
setInterval(monitor, CHECK_INTERVAL);
monitor();
