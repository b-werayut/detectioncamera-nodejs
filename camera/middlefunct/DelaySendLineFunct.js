const { readFile } = require('fs')

const Config = async () => {
    return new Promise((resolve, reject) => {
        const obj = []
        const readF = readFile('C:\\inetpub\\wwwroot\\camera\\config.txt', 'utf8', (err, data) => {
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

const getLogsDelay = async () => {
    return new Promise((resolve, reject) => {
        const obj = []
        readFile('C:\\inetpub\\wwwroot\\camera\\delaylogs.txt', 'utf8', (err, data) => {
            if (err) {
                console.error(err);
            }
            const json = JSON.parse(data)
            obj.push(
                {
                    jsondat: json
                }
            )
            return resolve(obj)
        })
    })
}

const setTime = (configdelaytime) => {
    let currenttimes = new Date()
    let Year = String(currenttimes.getFullYear())
    let Month = String(currenttimes.getMonth() + 1).padStart(2, '0');
    let date = String(currenttimes.getDate()).padStart(2, '0');

    currenttimes.setMinutes(currenttimes.getMinutes() - configdelaytime); // Set time delay
    let hours = String(currenttimes.getHours()).padStart(2, '0');
    let minutes = String(currenttimes.getMinutes()).padStart(2, '0');
    let seconds = String(currenttimes.getSeconds()).padStart(2, '0');

    return `${Year}${Month}${date}${hours}${minutes}${seconds}`;
}

exports.delayEventFunct = async (req, res, next) => {
    try {
        const confraw = await Config()
        const configdelaytime = confraw[0].json.delaysendlineminute
        const getLogsdelay = await getLogsDelay()
        const settime = setTime(configdelaytime)
        const logsdelay = getLogsdelay[0].jsondat.sendlinelogs
        const datetimelogsdelay = getLogsdelay[0].jsondat.datetimelogs

        if (parseInt(settime) > parseInt(logsdelay) || parseInt(logsdelay) == '') {
            console.log('Sendline Is Sending')
            next()
        } else {
            console.log('Sendline Is Delay latest: ', datetimelogsdelay)
            res.send(`Sendline Is Delay latest: ${datetimelogsdelay}`)
            return false
        }
    } catch (error) {
        console.log(error).res.status(500).send("Server Error")
    }
}