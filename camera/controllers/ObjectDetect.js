
const prisma = require("../Config/prisma")

exports.insertObjectLogs = async (req, res) => {
    try {
        const { camera, location, filename, thai_time, utc_time } = req.body;

        // console.log("camera>", Camera);
        // console.log("location>", Location);
        // console.log("filename>", FileName);
        // console.log("thai_time>", Thai_Time);
        // console.log("utc_time>", UtC_Time);

        const insertObjectLog = await prisma.DetectObject.create({
            data: {
                Camera: camera,
                Location: location,
                FileName: filename,
                Thai_Time: thai_time,
                UtC_Time: utc_time
            }
        })
        
        console.log(insertObjectLog);

        res.send("Success");
    } catch (err) {
        console.log(err)
    }
}