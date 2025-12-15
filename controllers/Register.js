const jwt = require("jsonwebtoken");
const prisma = require("../Config/Prisma");
const bcrypt = require("bcrypt");
require("dotenv").config();

exports.registerHandler = async (req, res) => {
  try {
    const { username, email, password } = req.body;
    const updatedAt = new Date();

    // Validate input
    if (!username || !email || !password) {
      return res.json({
        success: false,
        message: "Please provide all required fields",
      });
    }

    // Check if user already exists
    const existingUsername = await prisma.users.findFirst({
      where: { username: username },
    });

    if (existingUsername) {
      return res.json({
        success: false,
        message: "Username already exists",
        val: 1,
      });
    }

    // Check if email already exists
    const existingEmail = await prisma.users.findFirst({
      where: { email: email },
    });

    if (existingEmail) {
      return res.json({
        success: false,
        message: "Email already exists",
        val: 2,
      });
    }

    // Hash the password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Save user to database if JWT generation succeeded
    const newUser = await prisma.users.create({
      data: {
        username,
        email,
        password: hashedPassword,
        updatedAt,
      },
    });

    res.status(201).json({
      success: true,
      message: "Account created successfully",
      user: {
        id: newUser.id,
        username: newUser.username,
        email: newUser.email,
      },
      val: 3,
    });
  } catch (error) {
    console.error("Register Error:", error);
    res.status(500).json({
      success: false,
      message: "Error creating account",
      error: error.message,
      val: 0,
    });
  }
};
