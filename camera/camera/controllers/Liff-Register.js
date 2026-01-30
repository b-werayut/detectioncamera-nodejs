exports.liffRegister = async (req, res) => {
    const { userId, lastName, phone, email } = req.body;

    // บันทึกลง DB ได้ตรงนี้
    console.log(userId, lastName, phone, email);

    // ส่งข้อความกลับไปหา user
    // await axios.post(
    //     "https://api.line.me/v2/bot/message/push",
    //     {
    //         to: userId,
    //         messages: [
    //             {
    //                 type: "text",
    //                 text: `สวัสดีคุณ ${displayName} 🎉`
    //             }
    //         ]
    //     },
    //     {
    //         headers: {
    //             Authorization: `Bearer ${CHANNEL_ACCESS_TOKEN}`,
    //             "Content-Type": "application/json"
    //         }
    //     }
    // );

    res.json({ success: true });
};
