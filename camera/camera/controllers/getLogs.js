const prisma = require("../Config/prisma")


exports.GetLogsData = async (req,res)=>{
    try{
        const { params } = req.params
        const getid = await prisma.tmstCameraDetectionLogs.findFirst({
            where: { foldername: params }
        })
        res.send(getid)
        return getid
    }catch(err){
console.log(err)
    }
}