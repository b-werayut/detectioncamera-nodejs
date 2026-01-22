const { STRING } = require("txt-file-to-json/src/constants");
const prisma = require("../Config/prisma");

exports.insertFolderNameLogs = async (
  camname,
  foldername,
  timeinsert = new Date(),
) => {
  try {
    const camera = await prisma.Camera.findUnique({
      where: { CameraName: camname },
      select: { CameraID: true },
    });

    if (!camera) {
      console.warn("ไม่พบ Camera:", camname);
      return null;
    }

    return await prisma.EventLogs.create({
      data: {
        CameraID: camera.CameraID,
        CameraEvent: foldername,
      },
    });
  } catch (err) {
    console.error("insertFolderNameLogs error:", err);
    throw err;
  }
};

exports.insertDetectionLineSendLogs = async (
  camname,
  foldername,
  statuscode,
  statustext = "",
  timeinsert = new Date(),
) => {
  try {
    console.log("Status SendLine :", statuscode, statustext);

    const camera = await prisma.Camera.findUnique({
      where: { CameraName: camname },
      select: { CameraID: true },
    });

    if (!camera) {
      console.warn("ไม่พบ Camera:", camname);
      return null;
    }

    return await prisma.EventLogs.upsert({
      where: {
        CameraID_CameraEvent: {
          CameraID: camera.CameraID,
          CameraEvent: foldername,
        },
      },
      update: {
        LineStatus: statuscode,
        LineMessage: statustext,
      },
      create: {
        CameraID: camera.CameraID,
        CameraEvent: foldername,
        LineStatus: statuscode,
        LineMessage: statustext,
      },
    });
  } catch (err) {
    console.error("insertDetectionLineSendLogs error:", err);
    throw err;
  }
};

exports.insertPicStatusLogs = async (foldername, status, timeinsert) => {
  try {
    const find = await prisma.EventLogs.findFirst({
      where: { CameraEvent: foldername },
    });

    if (!find) {
      console.warn("ไม่พบ EventLogs สำหรับ CameraEvent:", foldername);
      return null;
    }

    const modifiedDate =
      timeinsert && !isNaN(new Date(timeinsert))
        ? new Date(timeinsert)
        : new Date();

    const updatePicStatus = await prisma.eventLogs.update({
      where: {
        EventLogsId: find.EventLogsId,
      },
      data: {
        SnapStatus: Number(status),
        ModifiedDate: modifiedDate,
      },
    });

    return updatePicStatus;
  } catch (err) {
    console.error(err);
    throw err;
  }
};

exports.insertVdoStatusLogs = async (foldername, status, timeinsert) => {
  try {
    const find = await prisma.eventLogs.findFirst({
      where: {
        CameraEvent: foldername,
      },
    });

    if (!find) {
      console.warn("ไม่พบ EventLogs สำหรับ CameraEvent:", foldername);
      return null;
    }

    const modifiedDate =
      timeinsert && !isNaN(new Date(timeinsert))
        ? new Date(timeinsert)
        : new Date();

    const updateVdoStatus = await prisma.eventLogs.update({
      where: {
        EventLogsId: find.EventLogsId,
      },
      data: {
        VdoStatus: Number(status),
        ModifiedDate: modifiedDate,
      },
    });

    return updateVdoStatus;
  } catch (err) {
    console.error(err);
    throw err;
  }
};

exports.insertPowerLogs = async (req, res) => {
  try {
    const point = req.params?.point;
    const status = req.params?.status;
    const val = req.params?.val;
    const timestamp = req.params?.timestamp;
    const insert = await prisma.PowerLogs.create({
      data: {
        Point: point,
        PowerStatus: status,
        Value: Number(val),
        Timestamp: timestamp,
      },
    });
    res.send(insert);
  } catch (err) {
    console.log(err);
    res.status(500).send("Server Err!");
  }
};

exports.getUserIDCustomer = async (req, res) => {
  try {
    const result = await prisma.lineUser.findMany({
      where: {
        Users: {
          LineNotifyActive: true,
        },
      },
      select: {
        UserIdLine: true,
      },
    });

    const userIdLineList = result
      .map((u) => u.UserIdLine)
      .filter((id) => id !== null); // กันค่า null

    console.log("userIdLineList:", userIdLineList);
    return userIdLineList;
  } catch (err) {
    console.error(err);
    res.status(500).send("Server Error");
  }
};

exports.getUserIDCustomerexternal = async (req, res) => {
  try {
    const useriddb = await prisma.tmstLineUserIdCustomer.findMany({
      select: {
        userID: true,
        replyToken: true,
      },
    });

    // const obj = []
    // const userid = useriddb.map(user => obj.push(user.userID));

    const obj = { userid: [], replytoken: [] };
    const userid = useriddb.map((user) => obj.userid.push(user.userID));
    const users = useriddb.map((items) =>
      obj.replytoken.push(items.replyToken),
    );
    res.send(obj);
  } catch (err) {
    console.log(err);
    res.status(500).send("Server Error");
  }
};

exports.getDataFlutter = async (req, res) => {
  try {
    const useriddb = await prisma.tmstPowerDetectionLogs.findMany({
      select: {
        id: true,
        status: true,
        timestamp: true,
      },
    });
    // const obj = []
    // const userid = useriddb.map(user => obj.push(user.userID));
    const obj = { id: [], status: [], timestamp: [] };
    const id = useriddb.map((items) => obj.id.push(items.id));
    const userid = useriddb.map((items) => obj.status.push(items.status));
    const users = useriddb.map((items) => obj.timestamp.push(items.timestamp));
    res.send(obj);
  } catch (err) {
    console.log(err);
    res.status(500).send("Server Error");
  }
};
