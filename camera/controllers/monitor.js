const http = require("http");

const API_HOST = "127.0.0.1";
const API_PORT = 9997;

const CHECK_INTERVAL = 10000; // 5 วิ
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
    name: "nptcam02",
    path: "hls2/st",
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

  const results = [];

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
        cam.status = "OFFLINE";

        console.log(`🔴 ${cam.name} (${cam.path}) OFFLINE`);
        results.push({
          name: cam.name,
          path: cam.path,
          status: cam.status,
        });
        return;
      }

      // ===== FROZEN CHECK =====
      const currentBytes = pathInfo.bytesReceived || 0;

      console.log("cam.lastBytes", cam.lastBytes);

      if (cam.lastBytes === 0) {
        cam.lastBytes = currentBytes;
        cam.lastBytesTime = now;
        cam.status = "ONLINE";

        console.log(`🟢 ${cam.name} (${cam.path}) ONLINE`);
        results.push({
          name: cam.name,
          path: cam.path,
          status: cam.status,
        });
        return;
      }

      if (currentBytes > cam.lastBytes) {
        cam.lastBytes = currentBytes;
        cam.lastBytesTime = now;
        alertIfChanged(cam, "ONLINE");

        console.log(`🟢 ${cam.name} (${cam.path}) ONLINE`);
        results.push({
          name: cam.name,
          path: cam.path,
          status: cam.status,
        });
        return;
      }

      if (now - cam.lastBytesTime > FREEZE_TIMEOUT) {
        alertIfChanged(cam, "FROZEN");
        cam.status = "FROZEN";

        console.log(`🟡 ${cam.name} (${cam.path}) FROZEN`);
        results.push({
          name: cam.name,
          path: cam.path,
          status: cam.status,
        });
        return;
      }

      // still online
      cam.status = "ONLINE";
      console.log(`🟢 ${cam.name} (${cam.path}) ONLINE`);
      results.push({
        name: cam.name,
        path: cam.path,
        status: cam.status,
      });
    });

    return results;
  } catch (err) {
    console.log("❌ Cannot connect to MediaMTX API");
    return [];
  }
}

// ===== ALERT HANDLER =====
function alertIfChanged(cam, newStatus) {
  if (cam.status === newStatus) return;

  cam.status = newStatus;
  process.stdout.write("\x07");
}

// ===== START =====
// setInterval(monitor, CHECK_INTERVAL);
exports.streamCheck = async (req, res) => {
  const result = await monitor();
  console.log("result", result);
  res.json({ result });
};
