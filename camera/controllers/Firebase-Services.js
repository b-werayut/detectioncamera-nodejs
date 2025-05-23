const admin = require('firebase-admin');
const serviceAccount = require('../firebase/serviceAccountKey.json');

exports.pushAndroidNotification = async (title,messageparams)=>{

    if (!admin.apps.length) {
        admin.initializeApp({
          credential: admin.credential.cert(serviceAccount),
        });
      }
      
      const message = {
        notification: {
          title: `${title}`,
          body: `${messageparams}`,
        },
        token: 'cgzTkl42Q4STqYyoY0-ocq:APA91bF2bEKP99orjUnvvt9_1jklo2jV8MIxjKB_pwF_-MuYtqP2PU6ACtn2Rgi6WJSmIB9l10kRtBDIX2wTiJcwgo-TRfs29TNTodXplZhyk95SBdJBSkc',
      };
      
      await admin.messaging().send(message)
        .then((response) => {
          console.log('Send pushNotification:', response);
        })
        .catch((error) => {
          console.error('Error Send pushNotification:', error);
        });

}

exports.pushAndroidNotificationPower = async (req, res)=>{
  try{

    let { point, val, timestamp } = req.params

    val = val == 1 ? "ไฟฟ้าปกติ" : 'ไฟฟ้าขัดข้อง'

    if (!admin.apps.length) {
      admin.initializeApp({
        credential: admin.credential.cert(serviceAccount),
      });
    }
    
    const message = {
      notification: {
        title: `${val}`,
        body: `${timestamp}`,
      },
      token: 'cgzTkl42Q4STqYyoY0-ocq:APA91bF2bEKP99orjUnvvt9_1jklo2jV8MIxjKB_pwF_-MuYtqP2PU6ACtn2Rgi6WJSmIB9l10kRtBDIX2wTiJcwgo-TRfs29TNTodXplZhyk95SBdJBSkc',
    };
    
    await admin.messaging().send(message)
      .then((response) => {
        console.log('Send pushNotification:', response);
        res.send('push Notification Success');
      })
      .catch((error) => {
        console.error('Error Send pushNotification:', error);
        res.send(error)
      });
  }catch(err){
    console.log(err)
    res.status(500).send(err)
  }
  
  
  

}

