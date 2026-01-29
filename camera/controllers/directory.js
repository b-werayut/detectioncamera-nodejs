/**
 * @fileoverview Directory management controller for camera detection system.
 * Handles folder creation, file copying, video processing, and LINE notifications.
 */

const {
  mkdirSync,
  existsSync,
  copyFile,
  readFile,
  writeFileSync,
} = require("fs");
const fs = require("fs-extra");
const path = require("path");
const cron = require("node-cron");
const { convertToMp4Funct } = require("./convertmp4");
const { glob } = require("glob");
const {
  insertDetectionLineSendLogs,
  insertFolderNameLogs,
  insertPicStatusLogs,
  insertVdoStatusLogs,
  getUserIDCustomer,
} = require("./DatabaseManage");
const axios = require("axios");
const dayjs = require("dayjs");
require("dayjs/locale/th");

// ============================================================================
// CONSTANTS
// ============================================================================

const CONFIG_PATH = "C:\\inetpub\\wwwroot\\camera\\config.txt";
const CAMERA_RAW_DIR = "C:/inetpub/wwwroot/Camera_Raw";
const EVENT_FOLDER_DIR = "C:/inetpub/wwwroot/eventfolder";
const CHECK_INTERVAL_MS = 60000; // 60 seconds

// ============================================================================
// DATE/TIME UTILITIES
// ============================================================================

/**
 * Date format types for formatDateTime function
 * @readonly
 * @enum {string}
 */
const DateFormat = {
  /** Format: YYYYMMDDHHmmss (e.g., 20240115143025) */
  COMPACT: "compact",
  /** Format: YYYY-MM-DD HH:mm:ss (e.g., 2024-01-15 14:30:25) */
  DATABASE: "database",
  /** Format: YYYY-MM-DD_HH:mm:ss (e.g., 2024-01-15_14:30:25) */
  LOGS: "logs",
};

/**
 * Format current date/time according to specified format type.
 * Consolidates multiple date formatting functions into one.
 *
 * @param {string} [format='compact'] - Format type: 'compact', 'database', or 'logs'
 * @returns {string} Formatted date/time string
 * @example
 * formatDateTime('compact')   // "20240115143025"
 * formatDateTime('database')  // "2024-01-15 14:30:25"
 * formatDateTime('logs')      // "2024-01-15_14:30:25"
 */
const formatDateTime = (format = DateFormat.COMPACT) => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  const day = String(now.getDate()).padStart(2, "0");
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");

  switch (format) {
    case DateFormat.DATABASE:
      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    case DateFormat.LOGS:
      return `${year}-${month}-${day}_${hours}:${minutes}:${seconds}`;
    case DateFormat.COMPACT:
    default:
      return `${year}${month}${day}${hours}${minutes}${seconds}`;
  }
};

/**
 * Get current date in Thai format with Buddhist year.
 * @returns {string} Thai formatted date (e.g., "วันจันทร์ที่ 15 มกราคม 2567")
 */
const getThaiDate = () => {
  dayjs.locale("th");
  const now = dayjs();
  const buddhistYear = now.year() + 543;
  return now.format("ddddที่ D MMMM") + ` ${buddhistYear} `;
};

/**
 * Get current time in Thai format.
 * @returns {string} Thai formatted time (e.g., "เวลา: 14:30:25")
 */
const getThaiTime = () => {
  dayjs.locale("th");
  return `เวลา: ${dayjs().format("HH:mm:ss")}`;
};

// Export for external use
exports.NewDateTime = () => formatDateTime(DateFormat.COMPACT);

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * Promise-based sleep function.
 * @param {number} ms - Milliseconds to sleep
 * @returns {Promise<void>}
 */
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

/**
 * Load configuration from config file.
 * @returns {Promise<Array<{json: Object}>>} Configuration object array
 */
const Config = async () => {
  return new Promise((resolve, reject) => {
    readFile(CONFIG_PATH, "utf8", (err, data) => {
      if (err) {
        console.error("❌ Error reading config:", err);
        return reject(err);
      }
      try {
        const jsonData = JSON.parse(data);
        resolve([{ json: jsonData }]);
      } catch (parseErr) {
        console.error("❌ Error parsing config JSON:", parseErr);
        reject(parseErr);
      }
    });
  });
};

/**
 * Get time values for file filtering.
 * @param {Object} config - Configuration object with beforetime and futuretime
 * @returns {{ currentTime: string, beforeTime: string, futureTime: string }}
 */
const getTimeWindow = (config) => {
  const now = new Date();
  const { beforetime, futuretime } = config;

  // Current time
  const formatTime = (date) => {
    return [
      date.getFullYear(),
      String(date.getMonth() + 1).padStart(2, "0"),
      String(date.getDate()).padStart(2, "0"),
      String(date.getHours()).padStart(2, "0"),
      String(date.getMinutes()).padStart(2, "0"),
      String(date.getSeconds()).padStart(2, "0"),
    ].join("");
  };

  const currentTime = formatTime(now);

  // Before time
  const beforeDate = new Date(now);
  beforeDate.setMinutes(beforeDate.getMinutes() - parseInt(beforetime));
  const beforeTimeStr = formatTime(beforeDate);

  // Future time
  const futureOffset =
    parseInt(beforetime) * 2 + (parseInt(futuretime) - parseInt(beforetime));
  const futureDate = new Date(beforeDate);
  futureDate.setMinutes(futureDate.getMinutes() + futureOffset);
  const futureTimeStr = formatTime(futureDate);

  return { currentTime, beforeTime: beforeTimeStr, futureTime: futureTimeStr };
};

// ============================================================================
// FILE OPERATIONS
// ============================================================================

/**
 * Copy file with retry mechanism for handling busy files.
 * @param {string} sourceFile - Source file path
 * @param {string} destFile - Destination file path
 * @param {number} [maxRetries=5] - Maximum retry attempts
 * @param {number} [delay=500] - Delay between retries in ms
 * @returns {boolean} True if copy succeeded, false otherwise
 */
const copyFileWithRetry = (
  sourceFile,
  destFile,
  maxRetries = 5,
  delay = 500,
) => {
  for (let attempt = 1; attempt <= maxRetries; attempt++) {
    try {
      fs.copyFileSync(sourceFile, destFile);
      console.log(`📂 Copied: ${path.basename(sourceFile)}`);
      return true;
    } catch (err) {
      if (err.code === "EBUSY") {
        console.log(
          `⚠️ File busy: ${path.basename(
            sourceFile,
          )}. Retry ${attempt}/${maxRetries}`,
        );
        Atomics.wait(new Int32Array(new SharedArrayBuffer(4)), 0, 0, delay);
      } else {
        console.error(
          `❌ Error copying ${path.basename(sourceFile)}:`,
          err.message,
        );
        return false;
      }
    }
  }
  console.error(
    `❌ Failed to copy ${path.basename(sourceFile)} after ${maxRetries} retries`,
  );
  return false;
};

/**
 * Get files matching time window criteria.
 * @param {string} sourcePath - Directory path to search
 * @param {string} extension - File extension (e.g., "jpg", "dav")
 * @param {string} beforeTime - Start time filter
 * @param {string} futureTime - End time filter
 * @param {Function} [timeExtractor] - Function to extract timestamp from filename
 * @returns {Promise<string[]>} Array of matching file paths
 */
const getMatchingFiles = async (
  sourcePath,
  extension,
  beforeTime,
  futureTime,
  timeExtractor,
) => {
  // Use forward slashes for glob pattern (glob requires forward slashes even on Windows)
  const normalizedPath = sourcePath.replace(/\\/g, "/");
  const pattern = `${normalizedPath}*.${extension}`;

  console.log(`🔍 Glob pattern: ${pattern}`);

  const files = await glob(pattern);
  console.log(`📁 Found ${files.length} total .${extension} files in source`);

  // Filename format: 001_20260116164907_[M][0@0][0].jpg
  // Timestamp is at index 4-17 (14 chars: YYYYMMDDHHMMSS)
  const defaultExtractor = (filename) => filename.slice(4, 18);
  const extractor = timeExtractor || defaultExtractor;

  const matchedFiles = files.filter((file) => {
    const filename = path.basename(file);
    const timestamp = extractor(filename);
    const ts = parseInt(timestamp);
    const bt = parseInt(beforeTime);
    const ft = parseInt(futureTime);
    const match = ts >= bt && ts <= ft;

    if (files.length <= 5) {
      console.log(
        `  📄 ${filename}: ts=${timestamp} bt=${beforeTime} ft=${futureTime} match=${match}`,
      );
    }
    return match;
  });

  console.log(
    `✅ Matched ${matchedFiles.length} files within time window [${beforeTime} - ${futureTime}]`,
  );
  return matchedFiles;
};

// ============================================================================
// FOLDER MANAGEMENT
// ============================================================================

/**
 * Create directory if it doesn't exist and log to database.
 * @param {string} directory - Directory path to create
 * @param {string} directoryfm - Directory name for logging
 * @param {boolean} [skipLog=false] - Skip database logging if true
 * @returns {Promise<string>} Created directory path
 */
const createFolder = async (
  directory,
  directoryfm,
  skipLog = false,
  camname,
) => {
  if (!existsSync(directory)) {
    mkdirSync(directory, { recursive: true });

    if (!skipLog && camname) {
      const timeinsert = formatDateTime(DateFormat.DATABASE);
      await insertFolderNameLogs(camname, directoryfm, timeinsert);
    }

    console.log(`📁 Created folder: ${directory}`);
  }
  return directory;
};

/**
 * Create subdirectory under given folder.
 * @param {string} folderName - Parent folder path
 * @param {string} subFolder - Subdirectory name (e.g., "Pic", "Vdo", "Pic/x")
 * @returns {Promise<string>} Parent folder path
 */
const createSubFolder = async (folderName, subFolder) => {
  const subPath = path.join(folderName, subFolder);
  if (!existsSync(subPath)) {
    mkdirSync(subPath, { recursive: true });
    console.log(`📁 Created subfolder: ${subPath}`);
  }
  return folderName;
};

// ============================================================================
// LINE NOTIFICATION
// ============================================================================

/**
 * Send LINE notification for detection alert.
 * @param {string} FolderName - Event folder name
 * @param {string} directoryfm - Directory name for logging
 * @param {string} camname - Camera name identifier
 * @returns {Promise<string>} Folder name
 */

const sendLineAxios = async (FolderName, directoryfm, camname) => {
  const cf = await Config();
  const urlEndpoint = cf[0].json.lineurlendpointcamera;
  const cftoken = cf[0].json.tokencameradetect;
  const timeinsert = formatDateTime(DateFormat.DATABASE);
  const time = getThaiTime();
  const date = getThaiDate();

  const logsdata = {
    sendlinelogs: formatDateTime(DateFormat.COMPACT),
    datetimelogs: formatDateTime(DateFormat.LOGS),
  };

  const useridindb = await getUserIDCustomer();
  const title = "ระบบตรวจสอบผู้บุกรุก";
  const message = `ตรวจพบผู้ต้องสงสัย!\nกล้อง: ${camname}\nวัน${date}\n${time} น.`;

  const headerAuth = {
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${cftoken}`,
    },
  };

  // Ensure userIds is a valid array of strings
  const validUserIds = Array.isArray(useridindb)
    ? useridindb.filter(id => id && typeof id === 'string' && id.trim() !== '')
    : [];

  if (validUserIds.length === 0) {
    console.warn("⚠️ No valid LINE User IDs found. Skipping notification.");
    return FolderName;
  }

  // Clean up time string for display (remove "เวลา: " prefix)
  const cleanTime = time.replace("เวลา: ", "");

  const flexMessage = createFlexMessage(validUserIds, {
    title: title,
    alertTitle: "ตรวจพบผู้ต้องสงสัย!",
    camName: camname,
    date: date,
    time: cleanTime,
    location: "โครงการวิจัยนครปฐม กม.61",
    imageUrl: "https://www.centrecities.com/assets/icon/human-detect.png",
    link: "http://www.centrecities.com:26080/LiveNotifyVideo/index.php?auth=1",
    altText: "🚨 ตรวจพบผู้บุกรุก! กรุณาตรวจสอบทันที"
  });

  // Debug Payload
  // console.log("Payload:", JSON.stringify(flexMessage, null, 2));

  try {
    const resp = await axios.post(urlEndpoint, flexMessage, headerAuth);

    await insertDetectionLineSendLogs(
      camname,
      directoryfm,
      resp.status,
      resp.statusText,
      timeinsert,
    );

    writeFileSync("delaylogs.txt", JSON.stringify(logsdata));
    console.log(`✅ LINE notification sent: ${resp.statusText}`);
  } catch (err) {
    const status = err.response?.status || 500;
    const statusText = err.response?.statusText || "Error";
    const errorDetails = err.response?.data ? JSON.stringify(err.response.data) : err.message;

    await insertDetectionLineSendLogs(
      camname,
      directoryfm,
      status,
      statusText,
      timeinsert,
    );

    writeFileSync("delaylogs.txt", JSON.stringify(logsdata));
    console.error(`❌ LINE notification failed: ${statusText}`);
    console.error(`🔍 Error Details: ${errorDetails}`);
  }

  return FolderName;
};

/**
 * Create LINE Flex Message structure with modern premium design.
 * @param {Array|string} userIds - User IDs to send to
 * @param {Object} data - Message data object
 * @param {string} data.title - Main system title
 * @param {string} data.alertTitle - Alert headline
 * @param {string} data.camName - Camera name
 * @param {string} data.date - Date string
 * @param {string} data.time - Time string
 * @param {string} data.location - Location string
 * @param {string} data.imageUrl - Hero image URL
 * @param {string} data.link - Action link
 * @param {string} data.altText - Notification preview text
 * @returns {Object} LINE message payload
 */
const createFlexMessage = (userIds, data) => {
  // Premium Color Palette
  const COLORS = {
    headerBg: "#1A237E",      // Deep Indigo
    accent: "#FF5252",         // Coral Red
    accentLight: "#FFCDD2",    // Light Coral
    textPrimary: "#212121",
    textSecondary: "#616161",
    textMuted: "#9E9E9E",
    white: "#FFFFFF",
    cardBg: "#FAFAFA",
    success: "#43A047",
    divider: "#E0E0E0",
  };

  const eventId = Date.now().toString().slice(-8);

  return {
    to: userIds,
    messages: [
      {
        type: "flex",
        altText: data.altText || "🚨 แจ้งเตือนระบบรักษาความปลอดภัย",
        contents: {
          type: "bubble",
          size: "giga",
          header: {
            type: "box",
            layout: "vertical",
            contents: [
              // Top Bar with Logo and Badge
              {
                type: "box",
                layout: "horizontal",
                contents: [
                  // Shield Icon
                  {
                    type: "box",
                    layout: "vertical",
                    contents: [
                      {
                        type: "text",
                        text: "🛡️",
                        size: "xxl",
                        align: "center",
                      },
                    ],
                    width: "50px",
                    height: "50px",
                    backgroundColor: "#FFFFFF20",
                    cornerRadius: "25px",
                    justifyContent: "center",
                    alignItems: "center",
                  },
                  // Title Section
                  {
                    type: "box",
                    layout: "vertical",
                    contents: [
                      {
                        type: "text",
                        text: "SECURITY SYSTEM",
                        color: "#FFFFFF99",
                        size: "xxs",
                        weight: "bold",
                      },
                      {
                        type: "text",
                        text: "แจ้งเตือนฉุกเฉิน",
                        color: COLORS.white,
                        size: "xl",
                        weight: "bold",
                      },
                    ],
                    paddingStart: "lg",
                    justifyContent: "center",
                  },
                  // Urgent Badge
                  {
                    type: "box",
                    layout: "vertical",
                    contents: [
                      {
                        type: "text",
                        text: "URGENT",
                        color: COLORS.white,
                        size: "xxs",
                        weight: "bold",
                        align: "center",
                      },
                    ],
                    backgroundColor: COLORS.accent,
                    cornerRadius: "md",
                    paddingAll: "sm",
                    paddingStart: "md",
                    paddingEnd: "md",
                    position: "absolute",
                    offsetEnd: "lg",
                    offsetTop: "lg",
                  },
                ],
                paddingAll: "lg",
                paddingTop: "xl",
                paddingBottom: "xl",
              },
            ],
            backgroundColor: COLORS.headerBg,
            paddingAll: "none",
          },
          hero: {
            type: "box",
            layout: "vertical",
            contents: [
              {
                type: "image",
                url: data.imageUrl,
                size: "full",
                aspectRatio: "16:9",
                aspectMode: "cover",
              },
              // Overlay Gradient
              {
                type: "box",
                layout: "vertical",
                contents: [
                  {
                    type: "text",
                    text: data.alertTitle,
                    color: COLORS.white,
                    size: "lg",
                    weight: "bold",
                    align: "center",
                  },
                ],
                position: "absolute",
                offsetBottom: "0px",
                offsetStart: "0px",
                offsetEnd: "0px",
                paddingAll: "lg",
                background: {
                  type: "linearGradient",
                  angle: "0deg",
                  startColor: "#00000099",
                  endColor: "#00000000",
                },
              },
            ],
            paddingAll: "none",
            action: {
              type: "uri",
              uri: data.link,
            },
          },
          body: {
            type: "box",
            layout: "vertical",
            contents: [
              // System Title
              {
                type: "text",
                text: data.title,
                color: COLORS.textSecondary,
                size: "xs",
                margin: "none",
              },
              // Divider
              {
                type: "separator",
                margin: "lg",
                color: COLORS.divider,
              },
              // Info Cards Container
              {
                type: "box",
                layout: "vertical",
                contents: [
                  // Camera Info Card
                  {
                    type: "box",
                    layout: "horizontal",
                    contents: [
                      {
                        type: "box",
                        layout: "vertical",
                        contents: [
                          {
                            type: "text",
                            text: "📹",
                            size: "lg",
                            align: "center",
                          },
                        ],
                        width: "40px",
                        height: "40px",
                        backgroundColor: "#E3F2FD",
                        cornerRadius: "md",
                        justifyContent: "center",
                        alignItems: "center",
                      },
                      {
                        type: "box",
                        layout: "vertical",
                        contents: [
                          {
                            type: "text",
                            text: "กล้องที่ตรวจพบ",
                            color: COLORS.textMuted,
                            size: "xs",
                          },
                          {
                            type: "text",
                            text: data.camName,
                            color: COLORS.textPrimary,
                            size: "md",
                            weight: "bold",
                          },
                        ],
                        paddingStart: "md",
                        justifyContent: "center",
                      },
                    ],
                    paddingAll: "md",
                    backgroundColor: COLORS.cardBg,
                    cornerRadius: "md",
                  },
                  // Time Info Card
                  {
                    type: "box",
                    layout: "horizontal",
                    contents: [
                      {
                        type: "box",
                        layout: "vertical",
                        contents: [
                          {
                            type: "text",
                            text: "🕐",
                            size: "lg",
                            align: "center",
                          },
                        ],
                        width: "40px",
                        height: "40px",
                        backgroundColor: "#FFF3E0",
                        cornerRadius: "md",
                        justifyContent: "center",
                        alignItems: "center",
                      },
                      {
                        type: "box",
                        layout: "vertical",
                        contents: [
                          {
                            type: "text",
                            text: "วันและเวลา",
                            color: COLORS.textMuted,
                            size: "xs",
                          },
                          {
                            type: "text",
                            text: `${data.date}`,
                            color: COLORS.textPrimary,
                            size: "sm",
                            weight: "bold",
                          },
                          {
                            type: "text",
                            text: `${data.time} น.`,
                            color: COLORS.textSecondary,
                            size: "sm",
                          },
                        ],
                        paddingStart: "md",
                        justifyContent: "center",
                      },
                    ],
                    paddingAll: "md",
                    backgroundColor: COLORS.cardBg,
                    cornerRadius: "md",
                    margin: "sm",
                  },
                  // Location Info Card
                  {
                    type: "box",
                    layout: "horizontal",
                    contents: [
                      {
                        type: "box",
                        layout: "vertical",
                        contents: [
                          {
                            type: "text",
                            text: "📍",
                            size: "lg",
                            align: "center",
                          },
                        ],
                        width: "40px",
                        height: "40px",
                        backgroundColor: "#E8F5E9",
                        cornerRadius: "md",
                        justifyContent: "center",
                        alignItems: "center",
                      },
                      {
                        type: "box",
                        layout: "vertical",
                        contents: [
                          {
                            type: "text",
                            text: "สถานที่",
                            color: COLORS.textMuted,
                            size: "xs",
                          },
                          {
                            type: "text",
                            text: data.location,
                            color: COLORS.textPrimary,
                            size: "sm",
                            weight: "bold",
                            wrap: true,
                          },
                        ],
                        paddingStart: "md",
                        justifyContent: "center",
                        flex: 1,
                      },
                    ],
                    paddingAll: "md",
                    backgroundColor: COLORS.cardBg,
                    cornerRadius: "md",
                    margin: "sm",
                  },
                ],
                margin: "lg",
                spacing: "none",
              },
            ],
            paddingAll: "xl",
            backgroundColor: COLORS.white,
          },
          footer: {
            type: "box",
            layout: "vertical",
            contents: [
              // Primary Action Button
              {
                type: "button",
                style: "primary",
                height: "md",
                action: {
                  type: "uri",
                  label: "🔍 ตรวจสอบเหตุการณ์",
                  uri: data.link,
                },
                color: COLORS.accent,
              },
              // Secondary Action Button
              {
                type: "button",
                style: "secondary",
                height: "md",
                action: {
                  type: "uri",
                  label: "📞 ติดต่อเจ้าหน้าที่",
                  uri: "tel:020000000",
                },
                margin: "sm",
              },
              // Event ID Footer
              {
                type: "box",
                layout: "horizontal",
                contents: [
                  {
                    type: "text",
                    text: `Event ID: #${eventId}`,
                    color: COLORS.textMuted,
                    size: "xxs",
                    flex: 1,
                  },
                  {
                    type: "text",
                    text: "Powered by Networklink",
                    color: COLORS.textMuted,
                    size: "xxs",
                    align: "end",
                    flex: 1,
                  },
                ],
                margin: "lg",
              },
            ],
            paddingAll: "xl",
            backgroundColor: COLORS.white,
          },
          styles: {
            header: {
              separator: false,
            },
            hero: {
              separator: false,
            },
            body: {
              separator: false,
            },
            footer: {
              separator: true,
            },
          },
        },
      },
    ],
  };
};

// ============================================================================
// FILE DIRECTORY CHECK (GENERIC)
// ============================================================================

/**
 * Generic file directory check with retry and wait mechanism.
 * Used for both X images and Pic images.
 *
 * @param {Object} options - Configuration options
 * @param {string} options.sourcePath - Source directory to scan
 * @param {string} options.destPath - Destination directory for copying
 * @param {string} options.beforeTime - Start time filter
 * @param {string} options.futureTime - End time filter
 * @param {string} options.directoryfm - Directory name for logging
 * @param {number} [options.minFiles=20] - Minimum files required
 * @param {number} [options.extraRounds=8] - Extra wait rounds
 * @param {string} [options.logPrefix='📸'] - Log prefix emoji
 * @returns {Promise<boolean>} True if files were processed
 */
const fileDirCheck = async (options) => {
  const {
    sourcePath,
    destPath,
    beforeTime,
    futureTime,
    directoryfm,
    minFiles = 20,
    extraRounds = 8,
    logPrefix = "📸",
  } = options;

  const conf = await Config();
  const { futuretime: confFuture, beforetime: confBefore } = conf[0].json;

  const totalWaitMs = (parseInt(confFuture) + parseInt(confBefore)) * 60000;
  const maxRound = totalWaitMs / CHECK_INTERVAL_MS;

  console.log(`${logPrefix} Starting file check... Min files: ${minFiles}`);
  console.log(
    `${logPrefix} Fixed time window: [${beforeTime} - ${futureTime}]`,
  );
  fs.ensureDirSync(destPath);

  const getFiles = () =>
    getMatchingFiles(sourcePath, "jpg", beforeTime, futureTime);

  const processFiles = async (files) => {
    for (const file of files) {
      const fileName = path.basename(file);
      copyFileWithRetry(
        path.join(sourcePath, fileName),
        path.join(destPath, fileName),
      );
    }
    const status = 1;
    await insertPicStatusLogs(
      directoryfm,
      status,
      formatDateTime(DateFormat.DATABASE),
    );
    console.log(`${logPrefix} Processed ${files.length} files`);
  };

  // Normal rounds
  for (let round = 1; round <= maxRound; round++) {
    const files = await getFiles();
    if (files.length >= minFiles) {
      console.log(`✅ Found ${files.length} files in round ${round}`);
      await processFiles(files);
      return true;
    }
    console.log(
      `⏳ Round ${round}: ${files.length}/${minFiles} files. Waiting...`,
    );
    await sleep(CHECK_INTERVAL_MS);
  }

  // Extra rounds
  console.log(`⚠️ Extending check for ${extraRounds} extra rounds...`);
  for (let extra = 1; extra <= extraRounds; extra++) {
    const files = await getFiles();
    if (files.length >= minFiles) {
      console.log(`✅ Found ${files.length} files in extra round ${extra}`);
      await processFiles(files);
      return true;
    }
    console.log(
      `🔁 Extra ${extra}: ${files.length}/${minFiles} files. Waiting...`,
    );
    await sleep(CHECK_INTERVAL_MS);
  }

  // Final attempt with whatever we have
  const finalFiles = await getFiles();
  if (finalFiles.length > 0) {
    console.log(`${logPrefix} Processing ${finalFiles.length} available files`);
    await processFiles(finalFiles);
    return true;
  }

  console.log("🚫 No files found after timeout");
  return false;
};

// ============================================================================
// DIRECTORY SCANNING
// ============================================================================

/**
 * Calculate time window from event time.
 * @param {Date} eventDate - The event date/time
 * @param {Object} config - Configuration with beforetime and futuretime
 * @returns {{ currentTime: string, beforeTime: string, futureTime: string }}
 */
const getTimeWindowFromEvent = (eventDate, config) => {
  const { beforetime, futuretime } = config;

  const formatTime = (date) => {
    return [
      date.getFullYear(),
      String(date.getMonth() + 1).padStart(2, "0"),
      String(date.getDate()).padStart(2, "0"),
      String(date.getHours()).padStart(2, "0"),
      String(date.getMinutes()).padStart(2, "0"),
      String(date.getSeconds()).padStart(2, "0"),
    ].join("");
  };

  // Current time = event time
  const currentTime = formatTime(eventDate);

  // Before time = event time - beforetime minutes
  const beforeDate = new Date(eventDate);
  beforeDate.setMinutes(beforeDate.getMinutes() - parseInt(beforetime));
  const beforeTimeStr = formatTime(beforeDate);

  // Future time = event time + futuretime minutes
  const futureDate = new Date(eventDate);
  futureDate.setMinutes(futureDate.getMinutes() + parseInt(futuretime));
  const futureTimeStr = formatTime(futureDate);

  return { currentTime, beforeTime: beforeTimeStr, futureTime: futureTimeStr };
};

/**
 * Scan directory for files matching time criteria.
 * @param {string} FolderName - Target folder name
 * @returns {Promise<Object>} Object containing file lists and metadata
 */
const globDirectory = async (FolderName) => {
  const conf = await Config();

  // FolderName format: CAM202412001_20260116_162002
  // Index:             0-11          13-20     22-27
  const pathName = path.basename(FolderName);
  const camName = pathName.split("_")[0]; // CAM202412001
  const dateStr = pathName.split("_")[1]; // 20260116
  const timeStr = pathName.split("_")[2]; // 162002

  // Parse date: YYYYMMDD -> YYYY-MM-DD
  const year = dateStr.slice(0, 4); // 2026
  const month = dateStr.slice(4, 6); // 01
  const day = dateStr.slice(6, 8); // 16

  // Parse time: HHMMSS
  const hours = timeStr.slice(0, 2); // 16
  const minutes = timeStr.slice(2, 4); // 20
  const seconds = timeStr.slice(4, 6); // 02

  // Create event date from folder name
  const eventDate = new Date(
    parseInt(year),
    parseInt(month) - 1, // Month is 0-indexed
    parseInt(day),
    parseInt(hours),
    parseInt(minutes),
    parseInt(seconds),
  );

  console.log(`🕐 Event time from folder: ${eventDate.toISOString()}`);

  // Calculate time window from EVENT time, not current time
  const { currentTime, beforeTime, futureTime } = getTimeWindowFromEvent(
    eventDate,
    conf[0].json,
  );

  console.log(
    `⏰ Time window (from event): ${beforeTime} - ${currentTime} - ${futureTime}`,
  );

  const DirectoryName = `${CAMERA_RAW_DIR}/${camName}/${year}-${month}-${day}/pic_001/`;
  console.log(`📂 Scanning: ${DirectoryName}`);
  console.log(`📁 Folder Exists: ${existsSync(DirectoryName)}`);

  const allFiles = await glob(`${DirectoryName}*.jpg`);
  const filesX = [];
  const filesPic = [];

  allFiles.forEach((file) => {
    const timestamp = path.basename(file).slice(4, 18);
    const ts = parseInt(timestamp);
    const bt = parseInt(beforeTime);
    const ct = parseInt(currentTime);
    const ft = parseInt(futureTime);

    if (ts <= ct && ts >= bt) {
      filesX.push(file);
    } else if (ts >= bt && ts <= ft) {
      filesPic.push(file);
    }
  });

  console.log(`📊 Files X (before event): ${filesX.length}`);
  console.log(`📊 Files Pic (after event): ${filesPic.length}`);

  return {
    filex: { fileX: filesX },
    filepic: { filesPic: filesPic },
    sourcepath: DirectoryName,
    foldername: FolderName,
    beforetime: beforeTime,
    futuretime: futureTime,
    currenttime: currentTime,
  };
};

/**
 * Copy files from source to destination directories.
 */
const copyFileinDir = async (
  filex,
  filespic,
  sourcepath,
  foldername,
  beforetime,
  futuretime,
  currenttime,
  directoryfm,
) => {
  const destPathX = `${foldername}/Pic/x/`;
  const destPathPic = `${foldername}/Pic/`;

  console.log(`📂 Source: ${sourcepath}`);
  console.log(`📂 Dest X: ${destPathX}`);
  console.log(`📂 Dest Pic: ${destPathPic}`);
  console.log(`📊 Initial X files: ${filex.fileX?.length || 0}`);
  console.log(`📊 Initial Pic files: ${filespic.filesPic?.length || 0}`);

  // Ensure destination directories exist
  fs.ensureDirSync(destPathX);
  fs.ensureDirSync(destPathPic);

  // Copy initial X files if any
  if (filex.fileX?.length > 0) {
    for (const file of filex.fileX) {
      const fileName = path.basename(file);
      const src = path.join(sourcepath, fileName);
      const dest = path.join(destPathX, fileName);
      copyFileWithRetry(src, dest);
    }
  }

  // Copy initial Pic files if any
  if (filespic.filesPic?.length > 0) {
    for (const file of filespic.filesPic) {
      const fileName = path.basename(file);
      const src = path.join(sourcepath, fileName);
      const dest = path.join(destPathPic, fileName);
      copyFileWithRetry(src, dest);
    }
  }

  // Always run fileDirCheck to wait for and copy any late-arriving files
  console.log("🔍 Starting X file directory check...");
  await fileDirCheck({
    sourcePath: sourcepath,
    destPath: destPathX,
    beforeTime: beforetime,
    futureTime: futuretime,
    directoryfm,
    minFiles: 20,
    extraRounds: 3,
    logPrefix: "🖼️ X",
  });

  console.log("🔍 Starting Pic file directory check...");
  await fileDirCheck({
    sourcePath: sourcepath,
    destPath: destPathPic,
    beforeTime: beforetime,
    futureTime: futuretime,
    directoryfm,
    minFiles: 30,
    extraRounds: 5,
    logPrefix: "📸 Pic",
  });

  return {
    filecount: {
      filesx: [filex.fileX?.length || 0],
      filespic: [filespic.filesPic?.length || 0],
    },
    foldername,
    sourcedir: sourcepath,
    beforeTime: beforetime,
    futuretime,
    currenttime,
  };
};

// ============================================================================
// VIDEO PROCESSING
// ============================================================================

/**
 * Scan and process video files.
 */
const globVdoFile = async (
  dir,
  foldername,
  beforetime,
  futuretime,
  currenttime,
  directoryfm,
) => {
  const videoDir = dir.replace("pic_001", "video_001");
  fs.ensureDirSync(videoDir);

  const conf = await Config();
  const { futuretime: confFuture, beforetime: confBefore } = conf[0].json;
  const maxRound =
    ((parseInt(confFuture) + parseInt(confBefore)) * 60000) / CHECK_INTERVAL_MS;
  const extraRounds = 8;
  const minVideos = 1;

  console.log("🎥 Scanning for .dav video files...");

  const getMatchingVideos = async () => {
    const files = await glob(path.join(videoDir, "*.dav"));
    return files.filter((file) => {
      const baseName = path.basename(file, ".dav");
      const clean = baseName.replaceAll(".", "").replace("-", "");
      const start = parseInt(clean.slice(0, 6));
      const end = parseInt(clean.slice(7, 13));
      const bfTime = parseInt(beforetime.slice(-6)) - 50;
      const ftTime = parseInt(futuretime.slice(-6));
      return start >= bfTime && end <= ftTime;
    });
  };

  const processVideos = async (videoFiles) => {
    try {
      await convertToMp4Funct(videoFiles, foldername, beforetime, futuretime);
      await insertVdoStatusLogs(
        directoryfm,
        1,
        formatDateTime(DateFormat.DATABASE),
      );
      console.log(`✅ Converted ${videoFiles.length} video(s)`);
    } catch (err) {
      console.error("❌ Video conversion error:", err.message);
    }
  };

  // Normal rounds
  for (let round = 1; round <= maxRound; round++) {
    const videos = await getMatchingVideos();
    if (videos.length >= minVideos) {
      console.log(`✅ Found ${videos.length} video(s) in round ${round}`);
      await processVideos(videos);
      return { vdofile: videos, folderdest: videoDir };
    }
    console.log(
      `⏳ Round ${round}: ${videos.length}/${minVideos} videos. Waiting...`,
    );
    await sleep(CHECK_INTERVAL_MS);
  }

  // Extra rounds
  for (let extra = 1; extra <= extraRounds; extra++) {
    const videos = await getMatchingVideos();
    if (videos.length >= minVideos) {
      console.log(`✅ Found ${videos.length} video(s) in extra round ${extra}`);
      await processVideos(videos);
      return { vdofile: videos, folderdest: videoDir };
    }
    console.log(`🔁 Extra ${extra}: ${videos.length} videos. Waiting...`);
    await sleep(CHECK_INTERVAL_MS);
  }

  const finalVideos = await getMatchingVideos();
  if (finalVideos.length > 0) {
    await processVideos(finalVideos);
    return { vdofile: finalVideos, folderdest: videoDir };
  }

  console.log("🚫 No video files found");
  return { vdofile: [], folderdest: videoDir };
};

// ============================================================================
// CLEANUP FUNCTIONS
// ============================================================================

/**
 * Get date N days in the past.
 * @param {number} days - Number of days in the past
 * @returns {Date} Past date
 */
const getPastDate = (days) => new Date(Date.now() - days * 24 * 60 * 60 * 1000);

/**
 * Delete old files from a directory.
 * @param {string} baseDir - Base directory to clean
 * @param {number} days - Delete files older than this many days
 */
const deleteOldFiles = async (baseDir, days) => {
  const cutoffTime = getPastDate(days);
  const folders = await fs.readdir(baseDir);

  for (const folder of folders) {
    const folderPath = path.join(baseDir, folder);
    const folderStats = await fs.stat(folderPath);

    if (!folderStats.isDirectory()) continue;

    const files = await fs.readdir(folderPath);
    for (const file of files) {
      const filePath = path.join(folderPath, file);
      const ext = path.extname(file);
      const base = path.basename(file);

      // Skip system files
      if (ext === ".node" || ext === ".entries" || base === "DVRWorkDirectory")
        continue;

      try {
        const fileStats = await fs.stat(filePath);
        if (fileStats.mtime < cutoffTime) {
          await fs.rm(filePath, { recursive: true, force: true });
          console.log(`✅ Deleted: ${filePath}`);
        }
      } catch (err) {
        console.error(`❌ Error deleting ${filePath}:`, err.message);
      }
    }
  }
};

// ============================================================================
// EXPORTED FUNCTIONS
// ============================================================================

/**
 * Schedule automatic directory cleanup using cron.
 * Runs daily at midnight (Asia/Bangkok timezone).
 * @returns {Promise<string>} Status message
 */
exports.cronDelDir = async () => {
  console.log("🟢 NodeCron is Running!");

  cron.schedule(
    "0 0 * * *",
    async () => {
      const time = formatDateTime(DateFormat.DATABASE);
      try {
        const conf = await Config();
        await deleteOldFiles(CAMERA_RAW_DIR, conf[0].json.deloldrawdirpastday);
        await deleteOldFiles(EVENT_FOLDER_DIR, conf[0].json.delolddirpastday);
        console.log(`🕒 Cron cleanup completed at ${time}`);
      } catch (err) {
        console.error(`❌ Cron error at ${time}:`, err.message);
      }
    },
    { scheduled: true, timezone: "Asia/Bangkok" },
  );

  return "✅ NodeCron scheduled: Delete files daily at 00:00 Asia/Bangkok";
};

/**
 * Manual directory cleanup endpoint.
 * @param {Object} req - Express request object
 * @param {Object} res - Express response object
 */
exports.delDir = async (req, res) => {
  console.log("🟢 Manual delDir triggered");
  const time = formatDateTime(DateFormat.DATABASE);

  try {
    const conf = await Config();
    await deleteOldFiles(CAMERA_RAW_DIR, conf[0].json.deloldrawdirpastday);
    await deleteOldFiles(EVENT_FOLDER_DIR, conf[0].json.delolddirpastday);
    console.log(`🕒 Manual cleanup at ${time}`);
    res.send(`🕒 Cleanup completed at ${time}`);
  } catch (err) {
    console.error(`❌ Cleanup error at ${time}:`, err.message);
    res.status(500).send("Server Error");
  }
};

/**
 * Main directory management endpoint.
 * Creates event folders, sends LINE notifications, and processes files/videos.
 * @param {Object} req - Express request object with camname param
 * @param {Object} res - Express response object
 */
exports.manageDirectory = async (req, res) => {
  const { projectcode, camname } = req.params;

  try {
    const camera = await prisma.Camera.findUnique({
      where: { CameraName: camname },
      select: { CameraID: true },
    });

    if (!camera) {
      console.log("❌ Camera not found :", camname);
      return res.status(200).send("Camera not found");
    }

    const time = formatDateTime(DateFormat.COMPACT);
    const fDate = time.substring(0, 8);
    const fTime = time.substring(8);

    const firstDir = `${EVENT_FOLDER_DIR}/${camname}`;
    const directory = `${firstDir}/${camname}_${fDate}_${fTime}`;
    const directoryfm = path.basename(directory);

    // Create folder structure
    await createFolder(firstDir, directoryfm, true, camname);
    await createFolder(directory, directoryfm, false, camname);

    // Send LINE notification
    await sendLineAxios(directory, directoryfm, camname);

    // Create subfolders
    await createSubFolder(directory, "Pic");
    await createSubFolder(directory, "Pic/x");
    await createSubFolder(directory, "Vdo");

    res.send("Detected System is Running!");

    // Process files
    const globResult = await globDirectory(directory);
    const copyResult = await copyFileinDir(
      globResult.filex,
      globResult.filepic,
      globResult.sourcepath,
      globResult.foldername,
      globResult.beforetime,
      globResult.futuretime,
      globResult.currenttime,
      directoryfm,
    );

    // Process videos
    await globVdoFile(
      copyResult.sourcedir,
      copyResult.foldername,
      copyResult.beforeTime,
      copyResult.futuretime,
      copyResult.currenttime,
      directoryfm,
    );
  } catch (err) {
    console.error("❌ manageDirectory error:", err);
    res.status(500).send("Server Error");
  }
};
