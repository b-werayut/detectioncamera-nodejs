const { STRING } = require("txt-file-to-json/src/constants");
const prisma = require("../Config/prisma");

exports.insertFolderNameLogs = async (foldername, FormatDatTime) => {
  try {
    const insertfoldername = await prisma.EventLogs.create({
      data: {
        CameraEvent: foldername,
        // CreatedAt: FormatDatTime,
      },
    });
    return insertfoldername;
  } catch (err) {
    console.log(err);
  }
};

exports.insertDetectionLineSendLogs = async (
  foldername,
  statuscode,
  statustext = "",
  timeinsert = new Date()
) => {
  try {
    console.log("Status SendLine :", statuscode, statustext);

    const find = await prisma.EventLogs.findFirst({
      where: {
        CameraEvent: foldername,
      },
    });

    if (!find) {
      console.warn("ไม่พบ EventLogs สำหรับ CameraEvent:", foldername);
      return null;
    }

    const updatelinesendstatus = await prisma.EventLogs.update({
      where: {
        EventLogsId: find.EventLogsId,
      },
      data: {
        LineStatus: statuscode,
        LineMessage: statustext,
        ModifiedDate: new Date(),
      },
    });

    return updatelinesendstatus;
  } catch (err) {
    console.error(err);
    throw err;
  }
};

exports.insertPicStatusLogs = async (
  foldername,
  status,
  timeinsert = new Date()
) => {
  try {
    const find = await prisma.EventLogs.findFirst({
      where: {
        CameraEvent: foldername,
      },
    });

    if (!find) {
      console.warn("ไม่พบ EventLogs สำหรับ CameraEvent:", foldername);
      return null;
    }

    const updatePicStatus = await prisma.eventLogs.update({
      where: {
        EventLogsId: find.EventLogsId,
      },
      data: {
        SnapStatus: Number(status),
        ModifiedDate: timeinsert,
      },
    });

    return updatePicStatus;
  } catch (err) {
    console.error(err);
    throw err;
  }
};

exports.insertVdoStatusLogs = async (
  foldername,
  status,
  timeinsert = new Date()
) => {
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

    const updateVdoStatus = await prisma.eventLogs.update({
      where: {
        EventLogsId: find.EventLogsId,
      },
      data: {
        VdoStatus: Number(status),
        ModifiedDate: timeinsert,
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
      obj.replytoken.push(items.replyToken)
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
