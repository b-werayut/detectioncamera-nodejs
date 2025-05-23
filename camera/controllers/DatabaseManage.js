const { STRING } = require("txt-file-to-json/src/constants")
const prisma = require("../Config/prisma")

exports.insertFolderNameLogs = async (foldername, FormatDatTime) => {
    try {
        const insertfoldername = await prisma.tmstCameraDetectionLogs.create({
            data: {
                foldername: foldername,
                createdAt: FormatDatTime
            }
        })
        return insertfoldername
    } catch (err) {
        console.log(err)
    }
}

exports.insertDetectionLineSendLogs = async (foldername, statuscode, statustext = '', timeinsert) => {
    try {
        const status = `${statuscode}:${statustext}`
        console.log('Status SendLine :', status)

        const find = await prisma.tmstCameraDetectionLogs.findFirst({
            where: { foldername: foldername }
        })

        const updatelinesendstatus = await prisma.tmstCameraDetectionLogs.update({
            where: {
                id: find.id
            },
            data: {
                linesendstatus: String(status),
                updatedAt: timeinsert
            }
        })

        return updatelinesendstatus
    } catch (err) {
        console.log(err)
    }
}

exports.insertPicStatusLogs = async (foldername, status, timeinsert) => {
    try {
        const find = await prisma.tmstCameraDetectionLogs.findFirst({
            where: { foldername: foldername }
        })

        const updatepicstatus = await prisma.tmstCameraDetectionLogs.update({
            where: {
                id: find.id
            },
            data: {
                picstatus: Number(status),
                updatedAt: timeinsert
            }
        })

        return updatepicstatus
    } catch (err) {
        console.log(err)
    }
}

exports.insertVdoStatusLogs = async (foldername, status, timeinsert) => {
    try {

        const find = await prisma.tmstCameraDetectionLogs.findFirst({
            where: { foldername: foldername }
        })

        const updatevdostatus = await prisma.tmstCameraDetectionLogs.update({
            where: {
                id: find.id
            },
            data: {
                vdostatus: Number(status),
                updatedAt: timeinsert
            }
        })

        return updatevdostatus
    } catch (err) {
        console.log(err)
    }
}

exports.insertPowerLogs = async (req, res) => {
    try {
        const {point, status, val, timestamp} = req.params
        const insert = await prisma.tmstPowerDetectionLogs.create({
            data: {
                point: point,
                status: status,
                value: Number(val),
                timestamp: timestamp
            }
        })
        res.send(insert)
    } catch (err) {
        console.log(err)
        res.status(500).send('Server Err!')
    }
}

exports.getUserIDCustomer = async (req, res) => {
    try {
        const useriddb = await prisma.tmstLineUserIdCustomer.findMany(
            {
                select: {
                    userID: true,
                }
            }
        )
        const userid = useriddb.map(user => user.userID);
        // console.log('userid In getUserIDCustomer :', userid)
        return userid
    } catch (err) {
        console.log(err)
        res.status(500).send("Server Error")
    }
}

exports.getUserIDCustomerexternal = async (req, res) => {
    try {
        const useriddb = await prisma.tmstLineUserIdCustomer.findMany(
            {
                select: {
                    userID: true,
                    replyToken: true
                }
            }
        )

        // const obj = []
        // const userid = useriddb.map(user => obj.push(user.userID));

        const obj = {userid:[],replytoken:[]}
        const userid = useriddb.map(user => obj.userid.push(user.userID));
        const users = useriddb.map( items => obj.replytoken.push(items.replyToken))
        res.send(obj)
    } catch (err) {
        console.log(err)
        res.status(500).send("Server Error")
    }
}

exports.getDataFlutter = async (req, res) => {
    try {
        const useriddb = await prisma.tmstPowerDetectionLogs.findMany(
            {
                select: {
                    id: true,
                    status: true,
                    timestamp: true
                }
            }
        )
        // const obj = []
        // const userid = useriddb.map(user => obj.push(user.userID));
        const obj = {id:[],status:[],timestamp:[]}
        const id = useriddb.map(items => obj.id.push(items.id));
        const userid = useriddb.map(items => obj.status.push(items.status));
        const users = useriddb.map(items => obj.timestamp.push(items.timestamp))
        res.send(obj)
    } catch (err) {
        console.log(err)
        res.status(500).send("Server Error")
    }
}

exports.deleteDataFlutter = async (req, res)=>{
    try{
        const { id } = req.params;
        const deletedata = await prisma.tmstPowerDetectionLogs.delete({
            where: {
                id: Number(id)
            }
        })
        console.log(deletedata)
        res.send(deletedata)
    }catch(err){
        console.log(err)
    }
}