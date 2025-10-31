const {
  mkdirSync,
  existsSync,
  copyFile,
  readFile,
  readdir,
  writeFileSync,
  stat,
  rm,
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

let roundXdirCheck = 0;
let roundPicdirCheck = 0;
let roundvdoCheck = 0;

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

exports.NewDateTime = () => {
  const newdate = new Date();
  const getFullYear = newdate.getFullYear();
  const getMonth = newdate.getMonth() + 1;
  const monthFormat = ("0" + getMonth).slice(-2);
  const getDate = newdate.getDate();
  const dateFormat = ("0" + getDate).slice(-2);
  const getHours = newdate.getHours();
  const hourFormat = ("0" + getHours).slice(-2);
  const getMinutes = newdate.getMinutes();
  const minuteFormat = ("0" + getMinutes).slice(-2);
  const getSecond = newdate.getSeconds();
  const secondFormat = ("0" + getSecond).slice(-2);
  const FormatDatTime = `${getFullYear}${monthFormat}${dateFormat}${hourFormat}${minuteFormat}${secondFormat}`;
  return FormatDatTime;
};

const newDateTimeinManageDir = () => {
  const newdate = new Date();
  const getFullYear = newdate.getFullYear();
  const getMonth = newdate.getMonth() + 1;
  const monthFormat = ("0" + getMonth).slice(-2);
  const getDate = newdate.getDate();
  const dateFormat = ("0" + getDate).slice(-2);
  const getHours = newdate.getHours();
  const hourFormat = ("0" + getHours).slice(-2);
  const getMinutes = newdate.getMinutes();
  const minuteFormat = ("0" + getMinutes).slice(-2);
  const getSecond = newdate.getSeconds();
  const secondFormat = ("0" + getSecond).slice(-2);
  const FormatDatTime = `${getFullYear}${monthFormat}${dateFormat}${hourFormat}${minuteFormat}${secondFormat}`;
  return FormatDatTime;
};

const newDateTimeinCronFunct = () => {
  const newdate = new Date();
  const getFullYear = newdate.getFullYear();
  const getMonth = newdate.getMonth() + 1;
  const monthFormat = ("0" + getMonth).slice(-2);
  const getDate = newdate.getDate();
  const dateFormat = ("0" + getDate).slice(-2);
  const getHours = newdate.getHours();
  const hourFormat = ("0" + getHours).slice(-2);
  const getMinutes = newdate.getMinutes();
  const minuteFormat = ("0" + getMinutes).slice(-2);
  const getSecond = newdate.getSeconds();
  const secondFormat = ("0" + getSecond).slice(-2);
  const FormatDatTime = `${getFullYear}-${monthFormat}-${dateFormat} ${hourFormat}:${minuteFormat}:${secondFormat}`;
  return FormatDatTime;
};

const newDateTimeinSendlineFunct = () => {
  const newdate = new Date();
  const getFullYear = newdate.getFullYear();
  const getMonth = newdate.getMonth() + 1;
  const monthFormat = ("0" + getMonth).slice(-2);
  const getDate = newdate.getDate();
  const dateFormat = ("0" + getDate).slice(-2);
  const getHours = newdate.getHours();
  const hourFormat = ("0" + getHours).slice(-2);
  const getMinutes = newdate.getMinutes();
  const minuteFormat = ("0" + getMinutes).slice(-2);
  const getSecond = newdate.getSeconds();
  const secondFormat = ("0" + getSecond).slice(-2);
  const FormatDatTime = `${getFullYear}${monthFormat}${dateFormat}${hourFormat}${minuteFormat}${secondFormat}`;
  return FormatDatTime;
};

const newDateTimeLogsinSendlineFunct = () => {
  const newdate = new Date();
  const getFullYear = newdate.getFullYear();
  const getMonth = newdate.getMonth() + 1;
  const monthFormat = ("0" + getMonth).slice(-2);
  const getDate = newdate.getDate();
  const dateFormat = ("0" + getDate).slice(-2);
  const getHours = newdate.getHours();
  const hourFormat = ("0" + getHours).slice(-2);
  const getMinutes = newdate.getMinutes();
  const minuteFormat = ("0" + getMinutes).slice(-2);
  const getSecond = newdate.getSeconds();
  const secondFormat = ("0" + getSecond).slice(-2);
  const FormatDatTime = `${getFullYear}-${monthFormat}-${dateFormat}_${hourFormat}:${minuteFormat}:${secondFormat}`;
  return FormatDatTime;
};

const getThaiDate = () => {
  dayjs.locale("th");
  const now = dayjs();
  const buddhistYear = now.year() + 543;
  return now.format("ddddที่ D MMMM") + ` ${buddhistYear} `;
};

const getThaiTime = () => {
  dayjs.locale("th");
  const now = dayjs();
  const time = now.format("HH:mm:ss");
  return `เวลา: ${time}`;
};

const timeInsertDB = () => {
  const newdate = new Date();
  const getFullYear = newdate.getFullYear();
  const getMonth = newdate.getMonth() + 1;
  const monthFormat = ("0" + getMonth).slice(-2);
  const getDate = newdate.getDate();
  const dateFormat = ("0" + getDate).slice(-2);
  const getHours = newdate.getHours();
  const hourFormat = ("0" + getHours).slice(-2);
  const getMinutes = newdate.getMinutes();
  const minuteFormat = ("0" + getMinutes).slice(-2);
  const getSecond = newdate.getSeconds();
  const secondFormat = ("0" + getSecond).slice(-2);
  const FormatDatTime = `${getFullYear}-${monthFormat}-${dateFormat} ${hourFormat}:${minuteFormat}:${secondFormat}`;
  return FormatDatTime;
};

const createFirstFolder = async (directory, directoryfm) => {
  const timeinsert = timeInsertDB();
  if (!existsSync(directory)) {
    mkdirSync(directory);
    const insert = await insertFolderNameLogs(directoryfm, timeinsert);
    console.log("CreateFolder Insert Status In CreateFolderFunct", insert);
    result = "Create Folder Success";
  } else {
    result = "Folder Date is exist";
  }
  return directory;
};

const createFolder = async (directory, directoryfm) => {
  const timeinsert = timeInsertDB();
  if (!existsSync(directory)) {
    mkdirSync(directory);
    const insert = await insertFolderNameLogs(directoryfm, timeinsert);
    console.log("CreateFolder Insert Status In CreateFolderFunct", insert);
    result = "Create Folder Success";
  } else {
    result = "Folder Date is exist";
  }
  return directory;
};

const sendLineAxios = async (FolderName, directoryfm, camname) => {
  const cf = await Config();
  const urlEndpoint = cf[0].json.lineurlendpointcamera;
  const cftoken = cf[0].json.tokencameradetect;
  const cfurldest = cf[0].json.urllocation;
  const timeinsert = timeInsertDB();
  const time = getThaiTime();
  const date = getThaiDate();
  const delaytime = newDateTimeinSendlineFunct();
  const timelogs = newDateTimeLogsinSendlineFunct();

  let logsdata = {
    sendlinelogs: delaytime,
    datetimelogs: timelogs,
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

  const datas = {
    to: useridindb,
    messages: [
      {
        type: "flex",
        altText: "ข้อมูลเพิ่มเติม",
        contents: {
          type: "bubble",
          size: "mega",
          // "styles": {
          //   "header": {
          //     "backgroundColor": "#FFF8F6"
          //   },
          //   "body": {
          //     "backgroundColor": "#FFFFFF"
          //   },
          //   "footer": {
          //     "backgroundColor": "#F5F5F5"
          //   }
          // },
          header: {
            type: "box",
            layout: "vertical",
            spacing: "sm",
            contents: [
              {
                type: "box",
                layout: "horizontal",
                contents: [
                  {
                    type: "text",
                    text: "แจ้งเตือน",
                    size: "xs",
                    align: "center",
                    gravity: "center",
                  },
                ],
                backgroundColor: "#EC3D44",
                paddingAll: "2px",
                paddingStart: "4px",
                paddingEnd: "4px",
                flex: 0,
                position: "absolute",
                offsetStart: "18px",
                offsetTop: "18px",
                cornerRadius: "100px",
                width: "60px",
                height: "25px",
              },
              {
                type: "text",
                text: "แจ้งเตือน!",
                size: "xxl",
                weight: "bold",
                wrap: true,
                align: "center",
                color: "#222222",
              },
              {
                type: "text",
                text: title,
                size: "lg",
                wrap: true,
                align: "center",
                color: "#333333",
              },
              {
                type: "separator",
                margin: "md",
              },
            ],
          },
          hero: {
            type: "image",
            url: "https://www.centrecities.com/assets/icon/human-detect.png",
            size: "full",
            aspectRatio: "2:1",
          },
          body: {
            type: "box",
            layout: "vertical",
            spacing: "md",
            contents: [
              {
                type: "text",
                text: "📍สถานที่",
                size: "lg",
                align: "center",
                weight: "bold",
                color: "#5D4037",
              },
              {
                type: "text",
                text: "โครงการวิจัยนครปฐม กม.61",
                size: "lg",
                align: "center",
                weight: "bold",
                color: "#1E1E1E",
              },
              {
                type: "separator",
              },
              {
                type: "text",
                text: message,
                size: "md",
                align: "center",
                color: "#D32F2F",
                wrap: true,
                weight: "bold",
              },
              {
                type: "separator",
              },
            ],
          },
          footer: {
            type: "box",
            layout: "vertical",
            spacing: "md",
            contents: [
              {
                type: "button",
                style: "primary",
                color: "#4CAF50",
                action: {
                  type: "uri",
                  label: "ดูข้อมูลเพิ่มเติม",
                  uri: "http://www.centrecities.com:26080/LiveNotifyVideo/index.php?auth=1",
                },
                height: "sm",
              },
              {
                type: "text",
                text: `อัปเดตล่าสุด: ${new Date().toLocaleString("th-TH", {
                  hour12: false,
                })}`,
                size: "xs",
                color: "#888888",
                align: "center",
              },
            ],
          },
        },
      },
    ],
  };

  // const datas = {
  //     "to": useridindb,
  //     "messages": [
  //         {
  //             "type": "flex",
  //             "altText": "ข้อมูลเพิ่มเติม",
  //             "contents": {
  //                 "type": "bubble",
  //                 "styles": {
  //                     "header": {
  //                         "backgroundColor": "#FFFFFF"
  //                     },
  //                     "body": {
  //                         "backgroundColor": "#FFFFFF"
  //                     },
  //                     "footer": {
  //                         "backgroundColor": "#FFFFFF"
  //                     }
  //                 },
  //                 "size": "mega",
  //                 "header": {
  //                     "type": "box",
  //                     "layout": "vertical",
  //                     "spacing": "sm",
  //                     "contents": [
  //                         {
  //                             "type": "box",
  //                             "layout": "horizontal",
  //                             "contents": [
  //                                 {
  //                                     "type": "text",
  //                                     "text": "แจ้งเตือน!",
  //                                     "size": "xs",
  //                                     "color": "#ffffff",
  //                                     "align": "center",
  //                                     "gravity": "center"
  //                                 }
  //                             ],
  //                             "backgroundColor": "#EC3D44",
  //                             "paddingAll": "2px",
  //                             "paddingStart": "4px",
  //                             "paddingEnd": "4px",
  //                             "flex": 0,
  //                             "position": "absolute",
  //                             "offsetStart": "18px",
  //                             "offsetTop": "18px",
  //                             "cornerRadius": "100px",
  //                             "width": "60px",
  //                             "height": "25px"
  //                         },
  //                         {
  //                             "type": "text",
  //                             "text": "แจ้งเตือน!",
  //                             "size": "xxl",
  //                             "scaling": true,
  //                             "weight": "bold",
  //                             "wrap": true,
  //                             "align": "center"
  //                         },
  //                         {
  //                             "type": "text",
  //                             "text": title,
  //                             "size": "lg",
  //                             "scaling": true,
  //                             "wrap": true,
  //                             "align": "center"
  //                         },
  //                         {
  //                             "type": "separator"
  //                         }
  //                     ]
  //                 },
  //                 "hero": {
  //                     "type": "image",
  //                     "url": "https://www.drrrayong.com/VMS/assets/human-detect.png",
  //                     "size": "full",
  //                     "aspectRatio": "2:1"
  //                 },
  //                 "body": {
  //                     "type": "box",
  //                     "layout": "vertical",
  //                     "spacing": "md",
  //                     "contents": [
  //                         {
  //                             "type": "text",
  //                             "text": "สถานที่",
  //                             "size": "lg",
  //                             "align": "center",
  //                             "scaling": true,
  //                             "wrap": true,
  //                             "weight": "bold"
  //                         },
  //                         {
  //                             "type": "text",
  //                             "text": "หาดแม่รำพึงจุดที่ 1",
  //                             "size": "lg",
  //                             "align": "center",
  //                             "scaling": true,
  //                             "wrap": true,
  //                             "weight": "bold"
  //                         },
  //                         {
  //                             "type": "separator"
  //                         },
  //                         {
  //                             "type": "box",
  //                             "layout": "horizontal",
  //                             "spacing": "md",
  //                             "contents": [
  //                                 {
  //                                     "type": "text",
  //                                     "text": message,
  //                                     "size": "lg",
  //                                     "align": "center",
  //                                     "color": "#EC3D44",
  //                                     "scaling": true,
  //                                     "wrap": true,
  //                                     "weight": "bold"
  //                                 }
  //                             ]
  //                         },
  //                         {
  //                             "type": "separator"
  //                         }
  //                     ]
  //                 },
  //                 "footer": {
  //                     "type": "box",
  //                     "layout": "vertical",
  //                     "contents": [
  //                         {
  //                             "type": "separator"
  //                         },
  //                         {
  //                             "type": "button",
  //                             "style": "primary",
  //                             "color": "#412500",
  //                             "action": {
  //                                 "type": "uri",
  //                                 "label": ">> คลิกเพื่อดูข้อมูลเพิ่มเติม <<",
  //                                 // "uri": `${cfurldest}?param=${directoryfm}`
  //                                 "uri": `http://www.centrecities.com:26080/LiveNotifyVideo/`
  //                             }
  //                         },
  //                         {
  //                             "type": "text",
  //                             "text": "อัปเดตล่าสุด: ".date('d/m/Y H:i'),
  //                             "size": "xs",
  //                             "color": "#999999",
  //                             "align": "center"
  //                         }
  //                     ]
  //                 }
  //             }
  //         }
  //     ]
  // }

  const sendlinemsgapi = await axios
    .post(urlEndpoint, datas, headerAuth)
    .then((resp) => {
      let statuscodeok = resp.status;
      let statustextok = resp.statusText;
      // console.log('Line success stat =', resp.statusText)
      const updatelinestatus = insertDetectionLineSendLogs(
        directoryfm,
        statuscodeok,
        statustextok,
        timeinsert
      );

      const Logs = writeFileSync(
        "delaylogs.txt",
        JSON.stringify(logsdata),
        (err) => {
          if (err) {
            console.log("Error WriteFile: ", err);
          }
        }
      );
    })
    .catch((resp) => {
      let statuscodeerr = resp.response.status;
      let statustexterr = resp.response.statusText;
      // console.log('Line err stat =', resp.response.data.message)
      const updatelinestatus = insertDetectionLineSendLogs(
        directoryfm,
        statuscodeerr,
        statustexterr,
        timeinsert
      );
      const Logs = writeFileSync(
        "delaylogs.txt",
        JSON.stringify(logsdata),
        (err) => {
          if (err) {
            console.log("Error WriteFile: ", err);
          }
        }
      );
    });

  return FolderName;
};

const createSubFolderPic = async (FolderName) => {
  if (!existsSync(`${FolderName}/Pic`)) {
    mkdirSync(`${FolderName}/Pic`);
    result = "Create Folder Pic Success";
  } else {
    result = "Folder Pic is exist";
  }
  return FolderName;
};

const createSubFolderX = async (FolderName) => {
  // console.log(FolderName)
  if (!existsSync(`${FolderName}/Pic/x`)) {
    mkdirSync(`${FolderName}/Pic/x`);
    result = "Create Folder Success";
  } else {
    result = "Folder is exist";
  }
  return FolderName;
};

const createSubFolderVdo = async (FolderName) => {
  if (!existsSync(`${FolderName}/Vdo`)) {
    mkdirSync(`${FolderName}/Vdo`);
    result = "Create Folder Success";
  } else {
    result = "Folder is exist";
  }
  return FolderName;
};

const vdoFileCheck = async (
  vdofiles,
  folderdest,
  beforetime,
  futuretime,
  foldername,
  currenttime,
  directoryfm,
  maxRetries = 10 // retry สูงสุด
) => {
  const arrvdos = [];

  const conf = await Config();
  const conffuturetime = conf[0].json.futuretime;
  const confbeforetime = conf[0].json.beforetime;
  const totalWaitMinutes = parseInt(conffuturetime) + parseInt(confbeforetime);
  const totalWaitMs = totalWaitMinutes * 60000;
  const checkInterval = 60000; // 60 sec
  const maxRound = totalWaitMs / checkInterval;
  const extraRounds = 8;

  fs.ensureDirSync(folderdest);

  const checkRounds = async () => {
    for (let round = 1; round <= maxRound; round++) {
      const files = await glob(`${folderdest}*.dav`); // หรือ .mp4 ตามต้องการ
      const matchedFiles = files.filter(file => {
        const fname = path.basename(file).replaceAll(".", "").replace("-", "");
        const vdonamestart = fname.slice(0, 6);
        const vdonameend = fname.slice(6, 12);
        return parseInt(vdonamestart) >= parseInt(beforetime.slice(-6)) &&
               parseInt(vdonameend) <= parseInt(futuretime.slice(-6));
      });

      if (matchedFiles.length > 0) return matchedFiles;
      console.log(`⏳ Round ${round}: No VDO found yet. Waiting...`);
      await sleep(checkInterval);
    }

    // extra rounds
    for (let extra = 1; extra <= extraRounds; extra++) {
      const files = await glob(`${folderdest}*.dav`);
      const matchedFiles = files.filter(file => {
        const fname = path.basename(file).replaceAll(".", "").replace("-", "");
        const vdonamestart = fname.slice(0, 6);
        const vdonameend = fname.slice(6, 12);
        return parseInt(vdonamestart) >= parseInt(beforetime.slice(-6)) &&
               parseInt(vdonameend) <= parseInt(futuretime.slice(-6));
      });

      if (matchedFiles.length > 0) return matchedFiles;
      console.log(`🔁 Extra round ${extra}: Still no VDO. Waiting...`);
      await sleep(checkInterval);
    }

    return [];
  };

  let matchedFiles = vdofiles && vdofiles.length > 0 ? vdofiles : await checkRounds();

  if (matchedFiles.length > 0) {
    console.log(`✅ Found ${matchedFiles.length} VDO file(s). Converting...`);
    await convertToMp4Funct(matchedFiles, foldername, beforetime, futuretime);

    const status = 1;
    const timeinsert = timeInsertDB();
    const updatevdostat = await insertVdoStatusLogs(directoryfm, status, timeinsert);
    console.log("📝 updateVdoStatus in vdoFileCheck =", updatevdostat);

    return {
      vdofile: matchedFiles,
      foldername,
      beforeTime: beforetime,
      futuretime,
    };
  } else if (maxRetries > 0) {
    console.log(`⚠️ No VDO files found. Retrying... (${maxRetries} retries left)`);
    return await vdoFileCheck(
      vdofiles,
      folderdest,
      beforetime,
      futuretime,
      foldername,
      currenttime,
      directoryfm,
      maxRetries - 1
    );
  } else {
    console.log("⏰ Timeout reached — no VDO files found after all retries.");
    return {
      vdofile: [],
      foldername,
      beforeTime: beforetime,
      futuretime,
    };
  }
};

const globVdoFile = async (
  dir,
  foldername,
  beforetime,
  futuretime,
  currenttime,
  directoryfm,
  maxRetries = 5 // retry สำหรับ copy/convert
) => {
  const videoDir = dir.replace("pic_001", "video_001");

  // ✅ Ensure directory exists (ไม่มี wildcard)
  fs.ensureDirSync(videoDir);

  const videoPattern = path.join(videoDir, "*.dav");
  const checkInterval = 60000; // 1 นาที
  const extraRounds = 8;
  const minVideos = 1; // ปรับตามต้องการ

  console.log("------------------------------");
  console.log("🎥 Scanning for .dav video files...");
  console.log(`Directory: ${videoDir}`);
  console.log(`Time Window: ${beforetime} - ${futuretime}`);

  const getMatchingFiles = async () => {
    const foundFiles = await glob(videoPattern);
    return foundFiles.filter((file) => {
      const baseName = path.basename(file, ".dav");
      const clean = baseName.replaceAll(".", "").replace("-", "");
      const start = clean.slice(0, 6);
      const end = clean.slice(7, 13);
      const bftime = beforetime.slice(-6);
      const fttime = futuretime.slice(-6);
      return parseInt(start) >= parseInt(bftime - 50) &&
             parseInt(end) <= parseInt(fttime);
    });
  };

  const processFiles = async (videoFiles) => {
    try {
      await convertToMp4Funct(videoFiles, foldername, beforetime, futuretime);
      const status = 1;
      const timeinsert = timeInsertDB();
      const updateStatus = await insertVdoStatusLogs(directoryfm, status, timeinsert);
      console.log("updateVdoStatus In globVdoFile =", updateStatus);
    } catch (err) {
      console.error("❌ Error during video conversion:", err);
    }
  };

  let round = 0;
  const conf = await Config();
  const conffuturetime = conf[0].json.futuretime;
  const confbeforetime = conf[0].json.beforetime;
  const maxRound = ((parseInt(conffuturetime) + parseInt(confbeforetime)) * 60000) / checkInterval;

  // 🔄 Normal rounds
  while (round < maxRound) {
    const videoFiles = await getMatchingFiles();
    if (videoFiles.length >= minVideos) {
      console.log(`✅ Found ${videoFiles.length} video(s) in round ${round + 1}`);
      await processFiles(videoFiles);
      return { vdofile: videoFiles, folderdest: videoDir };
    }
    console.log(`⏳ Round ${round + 1}: Only ${videoFiles.length} videos. Waiting...`);
    await sleep(checkInterval);
    round++;
  }

  // 🔁 Extra rounds
  console.log(`⚠️ Minimum videos not reached after ${maxRound} rounds. Extending check for ${extraRounds} extra rounds...`);
  for (let extra = 1; extra <= extraRounds; extra++) {
    const videoFiles = await getMatchingFiles();
    if (videoFiles.length >= minVideos) {
      console.log(`✅ Found ${videoFiles.length} video(s) in extra round ${extra}`);
      await processFiles(videoFiles);
      return { vdofile: videoFiles, folderdest: videoDir };
    }
    console.log(`🔁 Extra round ${extra}: Only ${videoFiles.length} videos. Waiting...`);
    await sleep(checkInterval);
  }

  console.log("⏰ Timeout reached — not enough video files found after all extra rounds.");
  return { vdofile: [], folderdest: videoDir };
};

const Config = async (foldername) => {
  return new Promise((resolve, reject) => {
    const obj = [];
    readFile(
      "C:\\inetpub\\wwwroot\\camera\\config.txt",
      "utf8",
      (err, data) => {
        if (err) {
          console.error(err);
        }
        const jsondat = JSON.parse(data);
        obj.push({
          json: jsondat,
        });
        return resolve(obj);
      }
    );
  });
};

const globDirectory = async (FolderName) => {
  const conftxt = await Config();
  const configbeforetime = conftxt[0].json.beforetime;
  const configfuturetime = conftxt[0].json.futuretime;
  const now = new Date();
  const getcurrenttime = getCurrentTime(now);
  const CurrentTimesplit = getcurrenttime.split(":");
  const CurrentTime = String(CurrentTimesplit).replaceAll(",", "");

  const getBeforeTime = setBeforeTime(now, configbeforetime);
  const BeforeTimesplit = getBeforeTime.split(":");
  const BeforeTime = String(BeforeTimesplit).replaceAll(",", "");

  const getFutureTime = setFutureTime(now, configbeforetime, configfuturetime);
  const FutureTimesplit = getFutureTime.split(":");
  const FutureTime = String(FutureTimesplit).replaceAll(",", "");

  console.log("CurrentTime", CurrentTime);
  console.log("BeforeTime", BeforeTime);
  console.log("FutureTime", FutureTime);

  const filesx = { fileX: [] };
  const filespic = { filesPic: [] };
  const pathName = path.basename(FolderName);
  const pathNamefyear = pathName.slice(13, 17);
  const pathNamefmonth = pathName.slice(17, 19);
  const pathNamefday = pathName.slice(19, 21);
  const pathNamefCamname = pathName.slice(0, 12);
  const DirectoryName = `C:/inetpub/wwwroot/Camera_Raw/${pathNamefCamname}/${pathNamefyear}-${pathNamefmonth}-${pathNamefday}/pic_001/`;
  console.log("DirectoryName in Glob Funct", DirectoryName);

  const globfileindir = await glob(`${DirectoryName}*.jpg`);

  globfileindir.map((items) => {
    const filename = path.basename(items);
    const filenamefm = filename.slice(4, 18);

    if (
      parseInt(filenamefm) <= parseInt(CurrentTime) &&
      filenamefm >= parseInt(BeforeTime)
    ) {
      filesx.fileX.push(items);
    } else if (
      parseInt(filenamefm) >= parseInt(BeforeTime) &&
      filenamefm <= parseInt(FutureTime)
    ) {
      filespic.filesPic.push(items);
    }
  });

  return {
    filex: filesx,
    filepic: filespic,
    sourcepath: DirectoryName,
    foldername: FolderName,
    beforetime: BeforeTime,
    futuretime: FutureTime,
    currenttime: CurrentTime,
  };
};

const copyFileinDir = async (
  filex,
  filespic,
  sourcepath,
  foldername,
  beforetime,
  futuretime,
  currenttime,
  directoryfm
) => {
  const destPathX = `${foldername}/Pic/x/`;
  const destPathPic = `${foldername}/Pic/`;
  const fileCount = {
    filesx: [filex.fileX.length],
    filespic: [filespic.filesPic.length],
  };

  if (filex.fileX) {
    await filex.fileX.map((items) => {
      const fileX = path.basename(items);
      const sourceFile = `${sourcepath}${fileX}`;
      const destfilex = `${destPathX}${fileX}`;
      copyFile(sourceFile, destfilex, (err) => {
        if (err) {
          console.log(err);
        }
      });
    });
    const sourceFilex = `${sourcepath}`;
    const destfilex = `${destPathX}`;
    xDirCheck(
      filespic,
      sourceFilex,
      destfilex,
      beforetime,
      futuretime,
      currenttime,
      directoryfm
    );
  }
  if (filespic.filesPic) {
    await filespic.filesPic.map((items) => {
      const filePic = path.basename(items);
      const sourceFile = `${sourcepath}${filePic}`;
      const destfilePic = `${destPathPic}${filePic}`;
      copyFile(sourceFile, destfilePic, (err) => {
        if (err) {
          console.log(err);
        }
      });
    });
    const sourceFilesend = `${sourcepath}`;
    const destfilePicsend = `${destPathPic}`;
    picDirCheck(
      filespic,
      sourceFilesend,
      destfilePicsend,
      beforetime,
      futuretime,
      currenttime,
      directoryfm
    );
  }

  return {
    filecount: fileCount,
    foldername: foldername,
    sourcedir: sourcepath,
    beforeTime: beforetime,
    futuretime: futuretime,
    currenttime: currenttime,
  };
};

const xDirCheck = async (
  filespic,
  sourcepath,
  destpath,
  beforetime,
  futuretime,
  currenttime,
  directoryfm,
  maxRetries = 5 // จำนวนครั้งสูงสุดในการ retry copy
) => {
  const conf = await Config();
  const conffuturetime = conf[0].json.futuretime;
  const confbeforetime = conf[0].json.beforetime;

  const confvalraw = parseInt(conffuturetime) + parseInt(confbeforetime);
  const confvalmilisec = confvalraw * 60000; // total waiting time (ms)
  const checkInterval = 60000; // check every 60 seconds
  const maxRound = confvalmilisec / checkInterval;
  const extraRounds = 8; // รอบเสริม
  const minFiles = 20; // จำนวนไฟล์ขั้นต่ำ

  console.log(
    `🖼️ Starting X images (supports late files)... Checking every ${checkInterval / 1000}s. Minimum files: ${minFiles}`
  );

  fs.ensureDirSync(destpath);

  const getMatchingFiles = async () => {
    const files = await glob(`${sourcepath}*.jpg`);
    return files.filter(file => {
      const timestamp = path.basename(file).slice(4, 18);
      return parseInt(timestamp) >= parseInt(beforetime) &&
             parseInt(timestamp) <= parseInt(futuretime);
    });
  };

  const copyFileWithRetry = (sourceFile, destFile, retries = maxRetries, delay = 500) => {
    for (let attempt = 1; attempt <= retries; attempt++) {
      try {
        fs.copyFileSync(sourceFile, destFile);
        console.log(`📂 Copied X file: ${path.basename(sourceFile)}`);
        return true;
      } catch (err) {
        if (err.code === 'EBUSY') {
          console.log(`⚠️ File busy: ${path.basename(sourceFile)}. Retry ${attempt}/${retries} in ${delay}ms`);
          Atomics.wait(new Int32Array(new SharedArrayBuffer(4)), 0, 0, delay); // sleep
        } else {
          console.error(`❌ Error copying X file (${path.basename(sourceFile)}):`, err);
          return false;
        }
      }
    }
    console.error(`❌ Failed to copy ${path.basename(sourceFile)} after ${retries} retries`);
    return false;
  };

  const processFiles = async (arrFiles) => {
    for (const file of arrFiles) {
      const fileName = path.basename(file);
      const sourceFile = path.join(sourcepath, fileName);
      const destFile = path.join(destpath, fileName);
      copyFileWithRetry(sourceFile, destFile);
    }
    const status = 1;
    const timeinsert = timeInsertDB();
    const updateStatus = await insertPicStatusLogs(directoryfm, status, timeinsert);
    console.log("📝 updateXstat in xDirCheck =", updateStatus);
  };

  let round = 0;

  while (round < maxRound) {
    const arrdatx = await getMatchingFiles();
    if (arrdatx.length >= minFiles) {
      console.log(`✅ Found ${arrdatx.length} X file(s) in round ${round + 1}.`);
      await processFiles(arrdatx);
      return true;
    }

    console.log(`⏳ Round ${round + 1}: Only ${arrdatx.length} X Images. Waiting...`);
    await sleep(checkInterval);
    round++;
  }

  // Extra rounds
  console.log(`⚠️ Minimum files not reached after ${maxRound} rounds. Extending check for ${extraRounds} extra rounds...`);
  for (let extra = 1; extra <= extraRounds; extra++) {
    const arrdatx = await getMatchingFiles();
    if (arrdatx.length >= minFiles) {
      console.log(`✅ Found ${arrdatx.length} X file(s) in extra round ${extra}.`);
      await processFiles(arrdatx);
      return true;
    }
    console.log(`🔁 Extra round ${extra}: Only ${arrdatx.length} X Images. Waiting...`);
    await sleep(checkInterval);
  }

  console.log("⏰ Timeout reached — not enough X files found after all extra rounds.");
  return false;
};

const picDirCheck = async (
  filespic,
  sourcepath,
  destpath,
  beforetime,
  futuretime,
  currenttime,
  directoryfm,
  maxRetries = 5 // จำนวนครั้งสูงสุดในการ retry copy
) => {
  const conf = await Config();
  const conffuturetime = conf[0].json.futuretime;
  const confbeforetime = conf[0].json.beforetime;

  const confvalraw = parseInt(conffuturetime) + parseInt(confbeforetime);
  const confvalmilisec = confvalraw * 60000; // total waiting time (ms)
  const checkInterval = 60000; // check every 1 minute
  const maxRound = confvalmilisec / checkInterval;
  const extraRounds = 8; // wait extra rounds if still not enough images
  const minImages = 60; // จำนวนรูปขั้นต่ำ

  console.log(
    `🖼️ Starting Pic images (supports late files)... Checking every ${
      checkInterval / 1000
    } seconds. Minimum images: ${minImages}`
  );

  fs.ensureDirSync(destpath);

  const getMatchingFiles = async () => {
    const files = await glob(`${sourcepath}*.jpg`);
    return files.filter((file) => {
      const filename = path.basename(file);
      const timestamp = filename.slice(4, 18);
      return parseInt(timestamp) >= parseInt(beforetime) &&
             parseInt(timestamp) <= parseInt(futuretime);
    });
  };

  const copyFileWithRetry = (sourceFile, destFile, retries = maxRetries, delay = 500) => {
    for (let attempt = 1; attempt <= retries; attempt++) {
      try {
        fs.copyFileSync(sourceFile, destFile);
        console.log(`📂 Copied file: ${path.basename(sourceFile)}`);
        return true;
      } catch (err) {
        if (err.code === 'EBUSY') {
          console.log(`⚠️ File busy: ${path.basename(sourceFile)}. Retry ${attempt}/${retries} in ${delay}ms`);
          Atomics.wait(new Int32Array(new SharedArrayBuffer(4)), 0, 0, delay);
        } else {
          console.error(`❌ Error copying file (${path.basename(sourceFile)}):`, err);
          return false;
        }
      }
    }
    console.error(`❌ Failed to copy ${path.basename(sourceFile)} after ${retries} retries`);
    return false;
  };

  const processFiles = async (arrFiles) => {
    for (const file of arrFiles) {
      const filePic = path.basename(file);
      const sourceFile = path.join(sourcepath, filePic);
      const destFile = path.join(destpath, filePic);
      copyFileWithRetry(sourceFile, destFile);
    }
    const status = 1;
    const timeinsert = timeInsertDB();
    const updatepicstat = await insertPicStatusLogs(directoryfm, status, timeinsert);
    console.log("📝 updatepicstat in picDirCheck =", updatepicstat);
  };

  let round = 0;

  while (round < maxRound) {
    const arrdat = await getMatchingFiles();

    if (arrdat.length >= minImages) {
      console.log(`✅ Found ${arrdat.length} image(s) in round ${round + 1}.`);
      await processFiles(arrdat);
      return true;
    }

    console.log(`⏳ Round ${round + 1}: Only ${arrdat.length} Pic images. Waiting...`);
    await sleep(checkInterval);
    round++;
  }

  // Extra rounds
  console.log(`⚠️ Minimum images not reached after ${maxRound} rounds. Extending check for ${extraRounds} extra rounds...`);
  for (let extra = 1; extra <= extraRounds; extra++) {
    const arrdat = await getMatchingFiles();
    if (arrdat.length >= minImages) {
      console.log(`✅ Found ${arrdat.length} image(s) in extra round ${extra}.`);
      await processFiles(arrdat);
      return true;
    }
    console.log(`🔁 Extra round ${extra}: Only ${arrdat.length} Pic images. Waiting...`);
    await sleep(checkInterval);
  }

  console.log("⏰ Timeout reached — not enough images found after all extra rounds.");
  return false;
};

const getCurrentTime = (currenttime) => {
  let Year = String(currenttime.getFullYear());
  let Month = String(currenttime.getMonth() + 1).padStart(2, "0");
  let date = String(currenttime.getDate()).padStart(2, "0");
  let hours = String(currenttime.getHours()).padStart(2, "0");
  let minutes = String(currenttime.getMinutes()).padStart(2, "0");
  let seconds = String(currenttime.getSeconds()).padStart(2, "0");
  return `${Year}:${Month}:${date}:${hours}:${minutes}:${seconds}`;
};

const setBeforeTime = (currenttimes, configbeforetime) => {
  let confbeforetime = configbeforetime;
  let Year = String(currenttimes.getFullYear());
  let Month = String(currenttimes.getMonth() + 1).padStart(2, "0");
  let date = String(currenttimes.getDate()).padStart(2, "0");

  currenttimes.setMinutes(currenttimes.getMinutes() - confbeforetime); // Set time
  let hours = String(currenttimes.getHours()).padStart(2, "0");
  let minutes = String(currenttimes.getMinutes()).padStart(2, "0");
  let seconds = String(currenttimes.getSeconds()).padStart(2, "0");
  // if (parseInt(hours) == 0) {
  //     date = String(currenttimes.getDate() - 1).padStart(2, '0');
  // } else {
  //     date = String(currenttimes.getDate()).padStart(2, '0');
  // }
  return `${Year}:${Month}:${date}:${hours}:${minutes}:${seconds}`;
};

const setFutureTime = (currenttimers, configbeforetime, configfuturetime) => {
  const conffuturetime =
    parseInt(configbeforetime) +
    parseInt(configbeforetime) +
    (parseInt(configfuturetime) - parseInt(configbeforetime));
  // console.log('conffuturetime*2 from beforetime : ', conffuturetime)
  let Year = String(currenttimers.getFullYear());
  let Month = String(currenttimers.getMonth() + 1).padStart(2, "0");
  date = String(currenttimers.getDate()).padStart(2, "0");

  currenttimers.setMinutes(currenttimers.getMinutes() + conffuturetime); // Set time
  let hours = String(currenttimers.getHours()).padStart(2, "0");
  let minutes = String(currenttimers.getMinutes()).padStart(2, "0");
  let seconds = String(currenttimers.getSeconds()).padStart(2, "0");
  // if (parseInt(hours) == 0) {
  //     date = String(currenttimers.getDate() + 1).padStart(2, '0');
  // } else {
  //     date = String(currenttimers.getDate()).padStart(2, '0');
  // }
  return `${Year}:${Month}:${date}:${hours}:${minutes}:${seconds}`;
};

const getPastDate = (days) => {
  return new Date(Date.now() - days * 24 * 60 * 60 * 1000);
};

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

      if (ext === ".node" || ext === ".entries" || base === "DVRWorkDirectory")
        continue;

      try {
        const fileStats = await fs.stat(filePath);

        if (fileStats.mtime < cutoffTime) {
          await fs.rm(filePath, { recursive: true, force: true });
          console.log(`✅ Deleted: ${filePath}`);
        }
      } catch (err) {
        console.error(`❌ Error handling file ${filePath}:`, err);
      }
    }
  }
};

exports.cronDelDir = async () => {
  console.log(`🟢 NodeCronFunct is Running!`);

  const task = cron.schedule(
    "0 0 * * *",
    async () => {
      // const task = cron.schedule('*/5 * * * * *', async () => {
      const time = newDateTimeinCronFunct();

      try {
        const delconfraw = await Config();

        await deleteOldFiles(
          "C:/inetpub/wwwroot/Camera_Raw",
          delconfraw[0].json.deloldrawdirpastday
        );
        await deleteOldFiles(
          "C:/inetpub/wwwroot/eventfolder",
          delconfraw[0].json.delolddirpastday
        );

        console.log(`🕒 NodeCron ran successfully at ${time}`);
      } catch (err) {
        console.error(`❌ Error during cron job at ${time}:`, err);
      }
    },
    {
      scheduled: true,
      timezone: "Asia/Bangkok",
    }
  );

  return "✅ NodeCron is set: Delete files every 00:00 Asia/Bangkok timezone";
};

exports.delDir = async (req, res) => {
  console.log(`🟢 delDirFunct is Running!`);

  const time = newDateTimeinCronFunct();

  try {
    const delconfraw = await Config();

    await deleteOldFiles(
      "C:/inetpub/wwwroot/Camera_Raw",
      delconfraw[0].json.deloldrawdirpastday
    );
    await deleteOldFiles(
      "C:/inetpub/wwwroot/eventfolder",
      delconfraw[0].json.delolddirpastday
    );

    console.log(`🕒 delDir at ${time}`);
  } catch (err) {
    console.error(`❌ delDir at Error ${time}:`, err);
  }

  res.send(`🕒 delDir at ${time}`);
};

exports.manageDirectory = async (req, res) => {
  const camnameconf = await Config();
  // const camname = camnameconf[0].json.cameraname
  const { camname } = req.params; // CAM202412001

  const time = newDateTimeinManageDir();
  const Fdate = time.substring(0, 8);
  const Ftime = time.substring(time.length - 6);
  const firstdir = `C:/inetpub/wwwroot/eventfolder/${camname}`;
  const directory = `${firstdir}/${camname}_${Fdate}_${Ftime}`;
  const directorysplit = directory.split("/");
  const directoryfm = directorysplit[5];

  createFirstFolder(firstdir, directoryfm)
    .then((resp) => createFolder(directory, directoryfm))
    .then((resp) => sendLineAxios(resp, directoryfm, camname))
    .then((resp) => createSubFolderPic(resp))
    .then((resp) => createSubFolderX(resp))
    .then((resp) => createSubFolderVdo(resp))
    .then((resp) => globDirectory(resp))
    .then((resp) =>
      copyFileinDir(
        resp.filex,
        resp.filepic,
        resp.sourcepath,
        resp.foldername,
        resp.beforetime,
        resp.futuretime,
        resp.currenttime,
        directoryfm
      )
    )
    .then((resp) =>
      globVdoFile(
        resp.sourcedir,
        resp.foldername,
        resp.beforeTime,
        resp.futuretime,
        resp.currenttime,
        directoryfm
      )
    )
    .then((resp) => res.send(`NodeCronFunct is Running!`))
    .catch((err) => {
      console.log(err), res.status(500).send("Server Error");
    });
};
