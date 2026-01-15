const express = require("express");
const { GetLogsData } = require("../controllers/getLogs");
const {
  insertPowerLogs,
  getUserIDCustomer,
  getUserIDCustomerexternal,
  getDataFlutter,
  testPort,
} = require("../controllers/DatabaseManage");
const { delayEventFunct } = require("../Middleware/DelaySendLineFunct");
const { registerHandler } = require("../controllers/Register");
const { loginHandler } = require("../controllers/Login");
const {
  manageDirectory,
  deleteOldDir,
  delDir,
} = require("../controllers/directory");
const { getCameraStat } = require("../controllers/getCameraStat");
const {
  pushAndroidNotificationPower,
} = require("../controllers/Firebase-Services");
const { insertObjectLogs } = require("../controllers/ObjectDetect");
const { liffRegister } = require("../controllers/Liff-Register");
const router = express.Router();

router.get("/getuserid/", getUserIDCustomerexternal);
router.get("/getDataFlutter/", getDataFlutter);
router.get("/directory/:camname", delayEventFunct, manageDirectory);
router.get("/directory2/:camname", manageDirectory);
// router.get('/directory/', manageDirectory)
router.get("/getlogs/:params", GetLogsData);
router.get("/getcamerastat", getCameraStat);
router.get("/deldir", delDir);
// router.delete('/testdelfolder', deleteOldDir)

router.post("/detect-object", insertObjectLogs);
router.post("/powerlogs/:point/:status/:val/:timestamp/", insertPowerLogs);
router.post("/register", registerHandler);
router.post("/login", loginHandler);
router.post("/liff/register", liffRegister);

//Api Mobile platform
router.post("/pushnotification/:point/:val/", pushAndroidNotificationPower);
// router.delete('/deletedataflutter/:id', deleteDataFlutter);

module.exports = router;
