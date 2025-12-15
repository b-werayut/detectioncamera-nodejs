const bcrypt = require("bcrypt");
const prisma = require("../Config/Prisma");
const jwt = require("jsonwebtoken");
require("dotenv").config();

JWT_SECRET = process.env.JWT_SECRET;
JWT_EXPIRES_IN = process.env.JWT_EXPIRES_IN;

function generateToken(user) {
  const payload = {
    id: user.id,
    username: user.username,
    email: user.email,
  };

  return jwt.sign(payload, JWT_SECRET, { expiresIn: JWT_EXPIRES_IN });
}

exports.loginHandler = async (req, res) => {
  try {
    const { username, password } = req.body;

    if (!username || !password) {
      return res.status(400).json({
        success: false,
        message: "กรุณากรอก Username และ Password",
        val: 0,
      });
    }

    const user = await prisma.users.findFirst({
      where: { username: username },
    });

    if (!user) {
      return res.status(401).json({
        success: false,
        message: "Username ไม่ถูกต้อง",
        val: 1,
      });
    }

    const isPasswordValid = await bcrypt.compare(password, user.password);
    if (!isPasswordValid) {
      return res.status(401).json({
        success: false,
        message: "Password ไม่ถูกต้อง",
        val: 2,
      });
    }

    const token = generateToken(user);

    await prisma.users.update({
      where: { id: user.id },
      data: { lastLogin: new Date() },
    });

    // เซ็ต cookie token
    res.cookie("token", token, {
      //httpOnly: true, // ไม่ให้ JS ฝั่ง client อ่าน cookie ได้ถ้าใช้ http
      maxAge: 15 * 60 * 1000, // อายุ 1 ชั่วโมง (ms)
      secure: process.env.NODE_ENV === "production", // ใช้เฉพาะ https ใน production
      sameSite: "Strict", // ป้องกัน CSRF เบื้องต้น
    });

    // ส่ง response กลับ client (ไม่ต้องส่ง token ใน body แล้วก็ได้)
    res.json({
      success: true,
      message: "เข้าสู่ระบบสำเร็จ",
      user: {
        id: user.id,
        username: user.username,
        email: user.email,
      },
      val: 3,
    });
  } catch (error) {
    console.error("Login Error:", error);
    res.status(500).json({
      success: false,
      message: "เกิดข้อผิดพลาดในการเข้าสู่ระบบ",
      error: error.message,
      val: 0,
    });
  }
};
