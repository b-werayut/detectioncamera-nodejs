const { STRING } = require("txt-file-to-json/src/constants");
const prisma = require("../Config/prisma");

exports.insertFolderNameLogs = async (
  camname,
  foldername,
  timeinsert,
  eventId,
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
        EventID: eventId,
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

    const updatePicStatus = await prisma.EventLogs.update({
      where: {
        EventLogsID: find.EventLogsID,
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
        EventLogsID: find.EventLogsID,
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

exports.getUserIDCustomer = async (camname) => {
  try {
    // const camname = "hls2/st";
    // const camname = "nptcam01";
    // const camname = "bkkcam01";

    if (!camname) {
      console.log("ไม่มี Camname");
      return { projectName: null, userIdLineList: [] };
    }

    const camera = await prisma.Camera.findFirst({
      where: {
        CameraName: camname,
      },
      select: {
        ProjectID: true,
        Project: {
          select: {
            ProjectName: true,
          },
        },
      },
    });

    if (!camera) {
      console.log("ไม่พบ Camera:", camname);
      return { projectName: null, userIdLineList: [] };
    }

    const users = await prisma.LineUser.findMany({
      where: {
        Users: {
          ProjectID: camera.ProjectID,
          LineNotifyActive: true,
        },
      },
      select: {
        UserIdLine: true,
      },
    });

    const userIdLineList = users
      .map((u) => u.UserIdLine)
      .filter((id) => id !== null);

    console.log("Proj ID", camera.ProjectID);
    console.log("Proj Name", camera.Project.ProjectName);
    console.log("userIdLineList", userIdLineList);

    return {
      projectName: camera.Project.ProjectName,
      userIdLineList,
    };
  } catch (err) {
    console.error(err);
    return { projectName: null, userIdLineList: [] };
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

exports.getCameraByUser = async (req, res) => {
  const { userID } = req.body;

  try {
    const result = await prisma.Camera.findMany({
      where: {
        Project: {
          Users: {
            some: {
              UserId: Number(userID),
            },
          },
        },
      },
      select: {
        CameraID: true,
        CameraName: true,
        isActive: true,
      },
    });
    res.send(result);
  } catch (err) {
    console.error("Server Error", err);
    res.status(500).send(err);
  }
};

exports.getAllCamera = async (req, res) => {
  const { projectID } = req.body;

  try {
    const result = await prisma.Camera.findMany({
      where: {
        ProjectID: Number(projectID), // 👈 ตรงตัว
      },
      select: {
        CameraID: true,
        CameraName: true,
        isActive: true,
      },
    });

    res.send(result);
  } catch (err) {
    console.error("getAllCamera error:", err);
    res.status(500).send({ message: "Server Error" });
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
