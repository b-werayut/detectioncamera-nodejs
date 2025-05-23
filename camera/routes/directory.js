const express = require('express')
const { GetLogsData } = require('../controllers/getLogs')
const { insertPowerLogs, getUserIDCustomer, getUserIDCustomerexternal, getDataFlutter, deleteDataFlutter } = require('../controllers/DatabaseManage')
const { delayEventFunct } = require('../middlefunct/DelaySendLineFunct')
const { manageDirectory } = require('../controllers/directory')
const { pushAndroidNotificationPower } = require('../controllers/Firebase-Services')
const router = express.Router()

router.get('/getuserid/', getUserIDCustomerexternal)
router.get('/getDataFlutter/', getDataFlutter)
router.delete('/deletedataflutter/:id', deleteDataFlutter)
router.get('/directory/', delayEventFunct, manageDirectory)
// router.get('/directory/', manageDirectory)
router.get('/getlogs/:params', GetLogsData)
router.post('/pushnotification/:point/:val/:timestamp', pushAndroidNotificationPower)

router.post('/powerlogs/:point/:status/:val/:timestamp/', insertPowerLogs)

module.exports = router