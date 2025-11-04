const express = require('express')
const { GetLogsData } = require('../controllers/getLogs')
const { insertPowerLogs, getUserIDCustomer, getUserIDCustomerexternal, getDataFlutter } = require('../controllers/DatabaseManage')
const { delayEventFunct } = require('../middlefunct/DelaySendLineFunct')
const { manageDirectory, deleteOldDir, delDir } = require('../controllers/directory')
const { getCameraStat } = require('../controllers/getCameraStat')
// const { pushAndroidNotificationPower } = require('../controllers/Firebase-Services')
const router = express.Router()

router.get('/getuserid/', getUserIDCustomerexternal)
router.get('/getDataFlutter/', getDataFlutter)
router.get('/directory/:camname', delayEventFunct, manageDirectory)
router.get('/directory2/:camname', manageDirectory)
// router.get('/directory/', manageDirectory)
router.get('/getlogs/:params', GetLogsData)
router.get('/getcamerastat', getCameraStat)
router.get('/deldir', delDir)
// router.delete('/testdelfolder', deleteOldDir)

router.post('/powerlogs/:point/:status/:val/:timestamp/', insertPowerLogs)

//Api Mobile platform
// router.post('/pushnotification/:point/:val/:timestamp', pushAndroidNotificationPower)
// router.delete('/deletedataflutter/:id', deleteDataFlutter);

module.exports = router