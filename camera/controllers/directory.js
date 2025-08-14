const { mkdirSync, existsSync, copyFile, readFile, readdir, writeFileSync, stat, rm } = require('fs')
const fs = require('fs/promises');
const path = require('path');
const cron = require('node-cron')
const { convertToMp4Funct } = require('./convertmp4');
const { glob } = require('glob');
const { insertDetectionLineSendLogs, insertFolderNameLogs, insertPicStatusLogs, insertVdoStatusLogs, getUserIDCustomer } = require('./DatabaseManage');
const axios = require('axios')
const dayjs = require('dayjs');
require('dayjs/locale/th')

let roundXdirCheck = 0
let roundPicdirCheck = 0
let roundvdoCheck = 0

exports.NewDateTime = () => {
    const newdate = new Date()
    const getFullYear = newdate.getFullYear()
    const getMonth = newdate.getMonth() + 1
    const monthFormat = ('0' + getMonth).slice(-2)
    const getDate = newdate.getDate()
    const dateFormat = ('0' + getDate).slice(-2)
    const getHours = newdate.getHours()
    const hourFormat = ('0' + getHours).slice(-2)
    const getMinutes = newdate.getMinutes()
    const minuteFormat = ('0' + getMinutes).slice(-2)
    const getSecond = newdate.getSeconds()
    const secondFormat = ('0' + getSecond).slice(-2)
    const FormatDatTime = `${getFullYear}${monthFormat}${dateFormat}${hourFormat}${minuteFormat}${secondFormat}`
    return FormatDatTime
}

const newDateTimeinManageDir = () => {
    const newdate = new Date()
    const getFullYear = newdate.getFullYear()
    const getMonth = newdate.getMonth() + 1
    const monthFormat = ('0' + getMonth).slice(-2)
    const getDate = newdate.getDate()
    const dateFormat = ('0' + getDate).slice(-2)
    const getHours = newdate.getHours()
    const hourFormat = ('0' + getHours).slice(-2)
    const getMinutes = newdate.getMinutes()
    const minuteFormat = ('0' + getMinutes).slice(-2)
    const getSecond = newdate.getSeconds()
    const secondFormat = ('0' + getSecond).slice(-2)
    const FormatDatTime = `${getFullYear}${monthFormat}${dateFormat}${hourFormat}${minuteFormat}${secondFormat}`
    return FormatDatTime
}

const newDateTimeinCronFunct = () => {
    const newdate = new Date()
    const getFullYear = newdate.getFullYear()
    const getMonth = newdate.getMonth() + 1
    const monthFormat = ('0' + getMonth).slice(-2)
    const getDate = newdate.getDate()
    const dateFormat = ('0' + getDate).slice(-2)
    const getHours = newdate.getHours()
    const hourFormat = ('0' + getHours).slice(-2)
    const getMinutes = newdate.getMinutes()
    const minuteFormat = ('0' + getMinutes).slice(-2)
    const getSecond = newdate.getSeconds()
    const secondFormat = ('0' + getSecond).slice(-2)
    const FormatDatTime = `${getFullYear}-${monthFormat}-${dateFormat} ${hourFormat}:${minuteFormat}:${secondFormat}`
    return FormatDatTime
}

const newDateTimeinSendlineFunct = () => {
    const newdate = new Date()
    const getFullYear = newdate.getFullYear()
    const getMonth = newdate.getMonth() + 1
    const monthFormat = ('0' + getMonth).slice(-2)
    const getDate = newdate.getDate()
    const dateFormat = ('0' + getDate).slice(-2)
    const getHours = newdate.getHours()
    const hourFormat = ('0' + getHours).slice(-2)
    const getMinutes = newdate.getMinutes()
    const minuteFormat = ('0' + getMinutes).slice(-2)
    const getSecond = newdate.getSeconds()
    const secondFormat = ('0' + getSecond).slice(-2)
    const FormatDatTime = `${getFullYear}${monthFormat}${dateFormat}${hourFormat}${minuteFormat}${secondFormat}`
    return FormatDatTime
}

const newDateTimeLogsinSendlineFunct = () => {
    const newdate = new Date()
    const getFullYear = newdate.getFullYear()
    const getMonth = newdate.getMonth() + 1
    const monthFormat = ('0' + getMonth).slice(-2)
    const getDate = newdate.getDate()
    const dateFormat = ('0' + getDate).slice(-2)
    const getHours = newdate.getHours()
    const hourFormat = ('0' + getHours).slice(-2)
    const getMinutes = newdate.getMinutes()
    const minuteFormat = ('0' + getMinutes).slice(-2)
    const getSecond = newdate.getSeconds()
    const secondFormat = ('0' + getSecond).slice(-2)
    const FormatDatTime = `${getFullYear}-${monthFormat}-${dateFormat}_${hourFormat}:${minuteFormat}:${secondFormat}`
    return FormatDatTime
}

const getThaiDate = () => {
    dayjs.locale('th')
    const now = dayjs();
    const buddhistYear = now.year() + 543;
    return now.format('ddddที่ D MMMM') + ` ${buddhistYear} `
}

const getThaiTime = () => {
    dayjs.locale('th')
    const now = dayjs();
    const time = now.format('HH:mm:ss')
    return `เวลา: ${time}`
}

const timeInsertDB = () => {
    const newdate = new Date()
    const getFullYear = newdate.getFullYear()
    const getMonth = newdate.getMonth() + 1
    const monthFormat = ('0' + getMonth).slice(-2)
    const getDate = newdate.getDate()
    const dateFormat = ('0' + getDate).slice(-2)
    const getHours = newdate.getHours()
    const hourFormat = ('0' + getHours).slice(-2)
    const getMinutes = newdate.getMinutes()
    const minuteFormat = ('0' + getMinutes).slice(-2)
    const getSecond = newdate.getSeconds()
    const secondFormat = ('0' + getSecond).slice(-2)
    const FormatDatTime = `${getFullYear}-${monthFormat}-${dateFormat} ${hourFormat}:${minuteFormat}:${secondFormat}`
    return FormatDatTime
}

const createFirstFolder = async (directory, directoryfm) => {
    const timeinsert = timeInsertDB()
    if (!existsSync(directory)) {
        mkdirSync(directory)
        const insert = await insertFolderNameLogs(directoryfm, timeinsert)
        console.log('CreateFolder Insert Status In CreateFolderFunct', insert)
        result = "Create Folder Success"
    } else {
        result = "Folder Date is exist"
    }
    return directory
}

const createFolder = async (directory, directoryfm) => {
    const timeinsert = timeInsertDB()
    if (!existsSync(directory)) {
        mkdirSync(directory)
        const insert = await insertFolderNameLogs(directoryfm, timeinsert)
        console.log('CreateFolder Insert Status In CreateFolderFunct', insert)
        result = "Create Folder Success"
    } else {
        result = "Folder Date is exist"
    }
    return directory
}

const sendLineAxios = async (FolderName, directoryfm) => {

    const cf = await Config()
    const urlEndpoint = cf[0].json.lineurlendpointcamera
    const cftoken = cf[0].json.tokencameradetect
    const cfurldest = cf[0].json.urllocation
    const timeinsert = timeInsertDB()
    const time = getThaiTime()
    const date = getThaiDate()
    const delaytime = newDateTimeinSendlineFunct()
    const timelogs = newDateTimeLogsinSendlineFunct()

    let logsdata = {
        sendlinelogs: delaytime,
        datetimelogs: timelogs
    }

    const useridindb = await getUserIDCustomer()
    const title = 'ระบบตรวจสอบผู้บุกรุก'
    const message = `ตรวจพบผู้ต้องสงสัย!\nวัน${date}\n${time} น.`;

    const headerAuth = {
        headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${cftoken}`
        }
    }

    const datas = {
        "to": useridindb,
        "messages": [
            {
                "type": "flex",
                "altText": "ข้อมูลเพิ่มเติม",
                "contents": {
                    "type": "bubble",
                    "styles": {
                        "header": {
                            "backgroundColor": "#FFFFFF"
                        },
                        "body": {
                            "backgroundColor": "#FFFFFF"
                        },
                        "footer": {
                            "backgroundColor": "#FFFFFF"
                        }
                    },
                    "size": "mega",
                    "header": {
                        "type": "box",
                        "layout": "vertical",
                        "spacing": "sm",
                        "contents": [
                            {
                                "type": "box",
                                "layout": "horizontal",
                                "contents": [
                                    {
                                        "type": "text",
                                        "text": "แจ้งเตือน!",
                                        "size": "xs",
                                        "color": "#ffffff",
                                        "align": "center",
                                        "gravity": "center"
                                    }
                                ],
                                "backgroundColor": "#EC3D44",
                                "paddingAll": "2px",
                                "paddingStart": "4px",
                                "paddingEnd": "4px",
                                "flex": 0,
                                "position": "absolute",
                                "offsetStart": "18px",
                                "offsetTop": "18px",
                                "cornerRadius": "100px",
                                "width": "60px",
                                "height": "25px"
                            },
                            {
                                "type": "text",
                                "text": "แจ้งเตือน!",
                                "size": "xxl",
                                "scaling": true,
                                "weight": "bold",
                                "wrap": true,
                                "align": "center"
                            },
                            {
                                "type": "text",
                                "text": title,
                                "size": "lg",
                                "scaling": true,
                                "wrap": true,
                                "align": "center"
                            },
                            {
                                "type": "separator"
                            }
                        ]
                    },
                    "hero": {
                        "type": "image",
                        "url": "https://www.drrrayong.com/VMS/assets/human-detect.png",
                        "size": "full",
                        "aspectRatio": "2:1"
                    },
                    "body": {
                        "type": "box",
                        "layout": "vertical",
                        "spacing": "md",
                        "contents": [
                            {
                                "type": "text",
                                "text": "สถานที่",
                                "size": "lg",
                                "align": "center",
                                "scaling": true,
                                "wrap": true,
                                "weight": "bold"
                            },
                            {
                                "type": "text",
                                "text": "หาดแม่รำพึงจุดที่ 1",
                                "size": "lg",
                                "align": "center",
                                "scaling": true,
                                "wrap": true,
                                "weight": "bold"
                            },
                            {
                                "type": "separator"
                            },
                            {
                                "type": "box",
                                "layout": "horizontal",
                                "spacing": "md",
                                "contents": [
                                    {
                                        "type": "text",
                                        "text": message,
                                        "size": "lg",
                                        "align": "center",
                                        "color": "#EC3D44",
                                        "scaling": true,
                                        "wrap": true,
                                        "weight": "bold"
                                    }
                                ]
                            },
                            {
                                "type": "separator"
                            }
                        ]
                    },
                    "footer": {
                        "type": "box",
                        "layout": "vertical",
                        "contents": [
                            {
                                "type": "separator"
                            },
                            {
                                "type": "button",
                                "style": "primary",
                                "color": "#412500",
                                "action": {
                                    "type": "uri",
                                    "label": ">> คลิกเพื่อดูข้อมูลเพิ่มเติม <<",
                                    "uri": `${cfurldest}?param=${directoryfm}`
                                }
                            }
                        ]
                    }
                }
            }
        ]
    }

    const sendlinemsgapi = await axios.post(urlEndpoint, datas, headerAuth)
        .then(resp => {
            let statuscodeok = resp.status
            let statustextok = resp.statusText
            // console.log('Line success stat =', resp.statusText)
            const updatelinestatus = insertDetectionLineSendLogs(directoryfm, statuscodeok, statustextok, timeinsert)

            const Logs = writeFileSync('delaylogs.txt', JSON.stringify(logsdata), (err) => {
                if (err) {
                    console.log('Error WriteFile: ', err)
                }
            })
        })
        .catch(resp => {
            let statuscodeerr = resp.response.status
            let statustexterr = resp.response.statusText
            // console.log('Line err stat =', resp.response.data.message)
            const updatelinestatus = insertDetectionLineSendLogs(directoryfm, statuscodeerr, statustexterr, timeinsert)
            const Logs = writeFileSync('delaylogs.txt', JSON.stringify(logsdata), (err) => {
                if (err) {
                    console.log('Error WriteFile: ', err)
                }
            })
        })

    return FolderName
}

const createSubFolderPic = async (FolderName) => {
    if (!existsSync(`${FolderName}/Pic`)) {
        mkdirSync(`${FolderName}/Pic`)
        result = "Create Folder Pic Success"
    } else {
        result = "Folder Pic is exist"
    }
    return FolderName
}

const createSubFolderX = async (FolderName) => {
    // console.log(FolderName)
    if (!existsSync(`${FolderName}/Pic/x`)) {
        mkdirSync(`${FolderName}/Pic/x`)
        result = "Create Folder Success"
    } else {
        result = "Folder is exist"
    }
    return FolderName
}

const createSubFolderVdo = async (FolderName) => {
    if (!existsSync(`${FolderName}/Vdo`)) {
        mkdirSync(`${FolderName}/Vdo`)
        result = "Create Folder Success"
    } else {
        result = "Folder is exist"
    }
    return FolderName
}

const vdoFileCheck = async (vdofiles, folderdest, beforetime, futuretime, foldername, currenttime, directoryfm) => {
    if (roundvdoCheck != 0) {
        console.log('roundvdoCheck =', roundvdoCheck)
    }

    if (parseInt(roundvdoCheck) == 3) {
        roundvdoCheck = 0
        console.log('RoundvdoCheck Timeout vdoFile is empty!')
        return false
    }

    const arrvdos = []
    const crtimef = currenttime.slice(-6) //095844
    const bftimef = beforetime.slice(-6) //095844
    const fttimef = futuretime.slice(-6) //120716

    if (!vdofiles[0] || vdofiles[0].length == 0) {
        const conf = await Config()
        const conffuturetime = conf[0].json.futuretime
        const confbeforetime = conf[0].json.beforetime
        const conftotal = parseInt(conffuturetime) + parseInt(confbeforetime)
        const confvalraw = parseInt(conftotal)
        const confvalmilisec = confvalraw * 60000
        const confvalminutes = confvalmilisec / 60000

        // let totalSeconds = 3
        let totalSeconds = confvalminutes * 60
        const interval = setInterval(async () => {
            let min = Math.floor(totalSeconds / 60);
            let sec = totalSeconds % 60;

            console.log(`Countvdofile : ${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}`);

            if (totalSeconds === 0) {
                clearInterval(interval)
                const getvdofile = await glob(folderdest)
                // console.log('crtimef in vdochk =', crtimef)
                getvdofile.map(items => {
                    const filevdoname = path.basename(items)
                    const fnameF = filevdoname.replaceAll('.', '')
                    const fnameFrp = fnameF.replace('-', '')
                    const vdonamestart = fnameFrp.slice(0, 6) // 074218
                    const vdonameend = fnameFrp.slice(6, 12) // 074258

                    if (parseInt(vdonamestart) >= parseInt(bftimef - 50) && parseInt(vdonameend) <= parseInt(fttimef + 50)) {
                        arrvdos.push(
                            items
                        )
                    }
                })

                const vdonameraw = path.basename(String(arrvdos[0]))
                const vdonamereplace = vdonameraw.replaceAll('.', '')
                const vdonamestart = vdonamereplace.slice(0, 6)
                const vdonameend = vdonamereplace.slice(7, 13)
                // console.log('vdonameraw =', vdonameraw) //10.33.25-10.35.33[M][0@0][0].dav
                // console.log('vdonamestart =', vdonamestart) //103325
                // console.log('vdonameend =', vdonameend) //103533

                if (arrvdos.length == 0 || arrvdos == undefined || !arrvdos) {
                    // console.log('checkvdo if 1 == OK')
                    roundvdoCheck++
                    await vdoFileCheck(vdofiles, folderdest, beforetime, futuretime, foldername, currenttime, directoryfm)
                } else if (parseInt(vdonamestart) < parseInt(bftimef) && parseInt(vdonameend) < parseInt(crtimef)) {
                    // console.log('checkvdo if 2 work')
                    await vdoFileCheck(vdofiles, folderdest, beforetime, futuretime, foldername, currenttime, directoryfm)
                } else if (parseInt(vdonamestart) >= parseInt(bftimef)) {
                    // console.log('checkvdo if 3  work')
                    const status = 1
                    const timeinsert = timeInsertDB()
                    await convertToMp4Funct(arrvdos, foldername, beforetime, futuretime)
                    const updatevdostat = await insertVdoStatusLogs(directoryfm, status, timeinsert)
                    console.log('UpdateVdostatus In vdoFileCheckFunct', updatevdostat)
                }
                console.log('VdoCheck AfterCheck:=', arrvdos.length)
            } else {
                totalSeconds--
            }
        }, 1000);
    } else {
        const status = 1
        const timeinsert = timeInsertDB()
        await convertToMp4Funct(vdofiles, foldername, beforetime, futuretime)
        const updatevdostat = await insertVdoStatusLogs(directoryfm, status, timeinsert)
        console.log('UpdateVdostatus In vdoFileCheckFunct', updatevdostat)
    }
    return ({ vdofile: arrvdos, foldername: foldername, beforeTime: beforetime, futuretime: futuretime })
}

const globVdoFile = async (dir, foldername, beforetime, futuretime, currenttime, directoryfm) => {
    const dirreplace = dir.replace('pic_001', 'video_001')
    const dirFullPath = `${dirreplace}*.dav`
    const vdofiles = []
    const getvdofile = await glob(dirFullPath)

    getvdofile.map(items => {
        const fname = path.basename(items, '.dav')
        const fnameF = fname.replaceAll('.', '')
        const fnameend = fnameF.slice(7, 13) // 074258
        const fnameFrp = fnameF.replace('-', '')
        const fnameFt = fnameFrp.slice(0, 12)
        const fnamestart = fnameFt.slice(0, 6) // 074218
        const bftimef = beforetime.slice(-6) //095844
        const fttimef = futuretime.slice(-6) //120716

        if (parseInt(fnamestart) >= parseInt(bftimef - 50) && parseInt(fnameend) <= parseInt(fttimef)) {
            vdofiles.push(
                items
            )
        }
    })

    if (!vdofiles[0]) {
        await vdoFileCheck(vdofiles, dirFullPath, beforetime, futuretime, foldername, currenttime, directoryfm)
    } else {
        const status = 1
        const timeinsert = timeInsertDB()
        await convertToMp4Funct(vdofiles, foldername, beforetime, futuretime)
        const updatevdostat = await insertVdoStatusLogs(directoryfm, status, timeinsert)
        console.log('UpdateVdostatus In vdoFileCheckFunct', updatevdostat)
    }

    return ({ vdofile: vdofiles, folderdest: dirFullPath, beforeTime: beforetime, futuretime: futuretime, foldername: foldername, directoryfm: directoryfm })
}

const Config = async (foldername) => {
    return new Promise((resolve, reject) => {
        const obj = []
        readFile('C:\\inetpub\\wwwroot\\camera\\config.txt', 'utf8', (err, data) => {
            if (err) {
                console.error(err);
            }
            const jsondat = JSON.parse(data)
            obj.push(
                {
                    json: jsondat
                }
            )
            return resolve(obj)
        })
    })
}

const globDirectory = async (FolderName) => {
    const conftxt = await Config()
    const configbeforetime = conftxt[0].json.beforetime
    const configfuturetime = conftxt[0].json.futuretime
    const now = new Date()
    const getcurrenttime = getCurrentTime(now)
    const CurrentTimesplit = getcurrenttime.split(':')
    const CurrentTime = String(CurrentTimesplit).replaceAll(',', '')

    const getBeforeTime = setBeforeTime(now, configbeforetime)
    const BeforeTimesplit = getBeforeTime.split(':')
    const BeforeTime = String(BeforeTimesplit).replaceAll(',', '')

    const getFutureTime = setFutureTime(now, configbeforetime, configfuturetime)
    const FutureTimesplit = getFutureTime.split(':')
    const FutureTime = String(FutureTimesplit).replaceAll(',', '')

    console.log('CurrentTime', CurrentTime)
    console.log('BeforeTime', BeforeTime)
    console.log('FutureTime', FutureTime)

    const filesx = { fileX: [] }
    const filespic = { filesPic: [] }
    const pathName = path.basename(FolderName)
    const pathNamefyear = pathName.slice(13, 17)
    const pathNamefmonth = pathName.slice(17, 19)
    const pathNamefday = pathName.slice(19, 21)
    const pathNamefCamname = pathName.slice(0, 12)
    const DirectoryName = `C:/inetpub/wwwroot/Camera_Raw/${pathNamefCamname}/${pathNamefyear}-${pathNamefmonth}-${pathNamefday}/pic_001/`
    console.log('DirectoryName in Glob Funct', DirectoryName)

    const globfileindir = await glob(`${DirectoryName}*.jpg`)

    globfileindir.map(items => {
        const filename = path.basename(items)
        const filenamefm = filename.slice(4, 18)

        if (parseInt(filenamefm) <= parseInt(CurrentTime) && filenamefm >= parseInt(BeforeTime)) {
            filesx.fileX.push(items)
        } else if (parseInt(filenamefm) >= parseInt(BeforeTime) && filenamefm <= parseInt(FutureTime)) {
            filespic.filesPic.push(items)
        }
    })

    return { filex: filesx, filepic: filespic, sourcepath: DirectoryName, foldername: FolderName, beforetime: BeforeTime, futuretime: FutureTime, currenttime: CurrentTime }
}

const copyFileinDir = async (filex, filespic, sourcepath, foldername, beforetime, futuretime, currenttime, directoryfm) => {

    const destPathX = `${foldername}/Pic/x/`
    const destPathPic = `${foldername}/Pic/`
    const fileCount = { filesx: [filex.fileX.length], filespic: [filespic.filesPic.length] }

    if (filex.fileX) {
        await filex.fileX.map(items => {
            const fileX = path.basename(items)
            const sourceFile = `${sourcepath}${fileX}`
            const destfilex = `${destPathX}${fileX}`
            copyFile(sourceFile, destfilex, (err) => {
                if (err) {
                    console.log(err);
                }
            });
        })
        const sourceFilex = `${sourcepath}`
        const destfilex = `${destPathX}`
        xDirCheck(filespic, sourceFilex, destfilex, beforetime, futuretime, currenttime, directoryfm)
    }
    if (filespic.filesPic) {
        await filespic.filesPic.map(items => {
            const filePic = path.basename(items)
            const sourceFile = `${sourcepath}${filePic}`
            const destfilePic = `${destPathPic}${filePic}`
            copyFile(sourceFile, destfilePic, (err) => {
                if (err) {
                    console.log(err);
                }
            })

        })
        const sourceFilesend = `${sourcepath}`
        const destfilePicsend = `${destPathPic}`
        picDirCheck(filespic, sourceFilesend, destfilePicsend, beforetime, futuretime, currenttime, directoryfm)
    }


    return ({ filecount: fileCount, foldername: foldername, sourcedir: sourcepath, beforeTime: beforetime, futuretime: futuretime, currenttime: currenttime })
}

const xDirCheck = async (filespic, sourcepath, destpath, beforetime, futuretime, currenttime, directoryfm) => {
    if (roundXdirCheck != 0) {
        console.log('roundXdirCheck:', roundXdirCheck)
    }

    if (parseInt(roundXdirCheck) == 3) {
        roundXdirCheck = 0
        console.log('RoundXdirCheck Timeout filex is empty!')
        return false
    }

    const conf = await Config()
    const confdata = conf[0].json.beforetime
    const confvalraw = parseInt(confdata)
    const confvalmilisec = confvalraw * 60000
    const confvalminutes = confvalmilisec / 60000
    const arrdatx = []

    let totalSeconds = confvalminutes * 60
    const interval = setInterval(async () => {
        let min = Math.floor(totalSeconds / 60);
        let sec = totalSeconds % 60;

        console.log(`CountXdir : ${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}`);

        if (totalSeconds === 0) {
            clearInterval(interval);
            const globvdofile = await glob(`${sourcepath}*.jpg`)
            globvdofile.map(items => {
                const filenameX = path.basename(items)
                const filenameXfm = filenameX.slice(4, 18)
                if (parseInt(filenameXfm) >= parseInt(beforetime) && parseInt(filenameXfm) <= parseInt(currenttime)) {
                    console.log('items is =', items)
                    arrdatx.push(items)
                }
            })

            let filenamepics = path.basename(String(arrdatx[0]))
            let filenamepicfms = filenamepics.slice(4, 18)

            if (arrdatx.length == 0 || arrdatx == undefined || !arrdatx) {
                console.log('checkX if 1 == OK')
                roundXdirCheck++
                await xDirCheck(filespic, sourcepath, destpath, beforetime, futuretime, currenttime)
            } else if (parseInt(filenamepicfms) < parseInt(beforetime) && parseInt(filenamepicfms) < parseInt(beforetime - 1) && parseInt(filenamepicfms) < parseInt(beforetime - 2) && parseInt(filenamepicfms) < parseInt(beforetime - 3)) {
                console.log('checkX if 2 == OK')
                roundXdirCheck++
                await xDirCheck(filespic, sourcepath, destpath, beforetime, futuretime, currenttime)
            } else if (parseInt(filenamepicfms) >= parseInt(beforetime) && parseInt(filenamepicfms) >= parseInt(currenttime - 1) && parseInt(filenamepicfms) >= parseInt(currenttime - 2) && parseInt(filenamepicfms) >= parseInt(currenttime - 3)) {
                // console.log('checkX if 3 == OK')
                const status = 1
                const timeinsert = timeInsertDB()
                arrdatx.map((items) => {
                    const filePic = path.basename(items)
                    const sourceFileX = `${sourcepath}${filePic}`
                    const destfilePicX = `${destpath}${filePic}`
                    copyFile(sourceFileX, destfilePicX, (err) => {
                        if (err) {
                            console.log(err);
                        }
                    })
                })
                const updatepicstat = await insertPicStatusLogs(directoryfm, status, timeinsert)
                console.log('updateXstat In Xcheck =', updatepicstat)
            }
            console.log('XdirCheck AfterCheck:=', arrdatx.length)
        } else {
            totalSeconds--
        }
    }, 1000)
}

const picDirCheck = async (filespic, sourcepath, destpath, beforetime, futuretime, currenttime, directoryfm) => {

    if (roundPicdirCheck != 0) {
        console.log('RoundPicdirCheck:', roundPicdirCheck)
    }

    if (parseInt(roundPicdirCheck) == 3) {
        roundPicdirCheck = 0
        console.log('RoundPicdirCheck Timeout filepic is empty')
        return false
    }

    const conf = await Config()
    const conffuturetime = conf[0].json.futuretime
    const confbeforetime = conf[0].json.beforetime
    const confvalraw = parseInt(conffuturetime) + parseInt(confbeforetime)
    const confvalmilisec = confvalraw * 60000 // 240000
    const confvalminutes = confvalmilisec / 60000 // 
    const arrdat = []

    let totalSeconds = confvalminutes * 60
    const interval = setInterval(async () => {
        let min = Math.floor(totalSeconds / 60);
        let sec = totalSeconds % 60;

        console.log(`CountPicdir : ${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}`);

        if (totalSeconds === 0) {
            clearInterval(interval);
            const globfileindirPic = await glob(`${sourcepath}*.jpg`)
            globfileindirPic.map(items => {
                const filenamepic = path.basename(items)
                const filenamepicfm = filenamepic.slice(4, 18)
                if (parseInt(filenamepicfm) >= parseInt(beforetime) && parseInt(filenamepicfm) <= parseInt(futuretime)) {
                    console.log('items is =', items)
                    arrdat.push(items)
                }
            })

            let filenamepics = path.basename(String(arrdat[0]))
            let filenamepicfms = filenamepics.slice(4, 18)

            if (arrdat.length == 0 || arrdat == undefined || !arrdat) {
                // console.log('checkPic if 1 == OK')
                await picDirCheck(filespic, sourcepath, destpath, beforetime, futuretime, currenttime)
                roundPicdirCheck++
            } else if (parseInt(filenamepicfms) < parseInt(beforetime) && parseInt(filenamepicfms) < parseInt(beforetime - 1) && parseInt(filenamepicfms) < parseInt(beforetime - 2) && parseInt(beforetime) < parseInt(futuretime - 3)) {
                // console.log('checkPic if 2 == OK')
                await picDirCheck(filespic, sourcepath, destpath, beforetime, futuretime, currenttime)
                roundPicdirCheck++
            } else if (parseInt(filenamepicfms) >= parseInt(currenttime) && parseInt(filenamepicfms) >= parseInt(currenttime - 1) && parseInt(filenamepicfms) >= parseInt(currenttime - 2) && parseInt(filenamepicfms) >= parseInt(currenttime - 3)) {
                // console.log('checkPic if 3 == OK')
                arrdat.map((items) => {
                    const filePic = path.basename(items)
                    const sourceFile = `${sourcepath}${filePic}`
                    const destfilePic = `${destpath}${filePic}`
                    copyFile(sourceFile, destfilePic, (err) => {
                        if (err) {
                            console.log(err);
                        }
                    })
                })
                const status = 1
                const timeinsert = timeInsertDB()
                const updatepicstat = await insertPicStatusLogs(directoryfm, status, timeinsert)
                console.log('updatepicstat In Piccheck =', updatepicstat)
            }
            console.log('PicdirCheck AfterCheck:=', arrdat.length)
        } else {
            totalSeconds--
        }
    }, 1000);

    let logsdata = {
        sourcepath: sourcepath,
        currenttime: currenttime,
        beforetime: beforetime,
        futuretime: futuretime
    }

    const Logs = writeFileSync('logs.txt', JSON.stringify(logsdata), (err) => {
        if (err) {
            console.log('Error WriteFile: ', err)
        }
    })
}

const getCurrentTime = (currenttime) => {
    let Year = String(currenttime.getFullYear())
    let Month = String(currenttime.getMonth() + 1).padStart(2, '0');
    let date = String(currenttime.getDate()).padStart(2, '0');
    let hours = String(currenttime.getHours()).padStart(2, '0');
    let minutes = String(currenttime.getMinutes()).padStart(2, '0');
    let seconds = String(currenttime.getSeconds()).padStart(2, '0');
    return `${Year}:${Month}:${date}:${hours}:${minutes}:${seconds}`;
}

const setBeforeTime = (currenttimes, configbeforetime) => {
    let confbeforetime = configbeforetime
    let Year = String(currenttimes.getFullYear())
    let Month = String(currenttimes.getMonth() + 1).padStart(2, '0');
    let date = String(currenttimes.getDate()).padStart(2, '0');

    currenttimes.setMinutes(currenttimes.getMinutes() - confbeforetime); // Set time
    let hours = String(currenttimes.getHours()).padStart(2, '0');
    let minutes = String(currenttimes.getMinutes()).padStart(2, '0');
    let seconds = String(currenttimes.getSeconds()).padStart(2, '0');
    // if (parseInt(hours) == 0) {
    //     date = String(currenttimes.getDate() - 1).padStart(2, '0');
    // } else {
    //     date = String(currenttimes.getDate()).padStart(2, '0');
    // }
    return `${Year}:${Month}:${date}:${hours}:${minutes}:${seconds}`;
}

const setFutureTime = (currenttimers, configbeforetime, configfuturetime) => {
    const conffuturetime = (parseInt(configbeforetime) + parseInt(configbeforetime)) + (parseInt(configfuturetime) - parseInt(configbeforetime))
    // console.log('conffuturetime*2 from beforetime : ', conffuturetime)
    let Year = String(currenttimers.getFullYear())
    let Month = String(currenttimers.getMonth() + 1).padStart(2, '0');
    date = String(currenttimers.getDate()).padStart(2, '0');

    currenttimers.setMinutes(currenttimers.getMinutes() + conffuturetime); // Set time
    let hours = String(currenttimers.getHours()).padStart(2, '0');
    let minutes = String(currenttimers.getMinutes()).padStart(2, '0');
    let seconds = String(currenttimers.getSeconds()).padStart(2, '0');
    // if (parseInt(hours) == 0) {
    //     date = String(currenttimers.getDate() + 1).padStart(2, '0');
    // } else {
    //     date = String(currenttimers.getDate()).padStart(2, '0');
    // }
    return `${Year}:${Month}:${date}:${hours}:${minutes}:${seconds}`;
}

const getPastDate = (days) => {
    return new Date(Date.now() - days * 24 * 60 * 60 * 1000);
}

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

            if (ext === '.node' || ext === '.entries' || base === 'DVRWorkDirectory') continue;

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

    const task = cron.schedule('0 0 * * *', async () => {
    // const task = cron.schedule('*/5 * * * * *', async () => {
        const time = newDateTimeinCronFunct();

        try {
            const delconfraw = await Config();

            await deleteOldFiles(
                'C:/inetpub/wwwroot/Camera_Raw',
                delconfraw[0].json.deloldrawdirpastday
            );
            await deleteOldFiles(
                'C:/inetpub/wwwroot/eventfolder',
                delconfraw[0].json.delolddirpastday
            );

            console.log(`🕒 NodeCron ran successfully at ${time}`);
        } catch (err) {
            console.error(`❌ Error during cron job at ${time}:`, err);
        }
    }, {
        scheduled: true,
        timezone: 'Asia/Bangkok',
    });

    return '✅ NodeCron is set: Delete files every 00:00 Asia/Bangkok timezone';
};

exports.manageDirectory = async (req, res) => {

    const camnameconf = await Config()
    // const camname = camnameconf[0].json.cameraname
    const { camname } = req.params // CAM202412001

    const time = newDateTimeinManageDir()
    const Fdate = time.substring(0, 8)
    const Ftime = time.substring(time.length - 6)
    const firstdir = `C:/inetpub/wwwroot/eventfolder/${camname}`
    const directory = `${firstdir}/${camname}_${Fdate}_${Ftime}`
    const directorysplit = directory.split('/')
    const directoryfm = directorysplit[5]


    createFirstFolder(firstdir, directoryfm)
        .then(resp => createFolder(directory, directoryfm))
        .then(resp => sendLineAxios(resp, directoryfm))
        .then(resp => createSubFolderPic(resp))
        .then(resp => createSubFolderX(resp))
        .then(resp => createSubFolderVdo(resp))
        .then(resp => globDirectory(resp))
        .then(resp => copyFileinDir(resp.filex, resp.filepic, resp.sourcepath, resp.foldername, resp.beforetime, resp.futuretime, resp.currenttime, directoryfm))
        .then(resp => globVdoFile(resp.sourcedir, resp.foldername, resp.beforeTime, resp.futuretime, resp.currenttime, directoryfm))
        .then(resp => res.send(`NodeCronFunct is Running!`))
        .catch(err => { console.log(err), res.status(500).send("Server Error") })
}
