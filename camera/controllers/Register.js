const jwt = require("jsonwebtoken");
const prisma = require("../Config/prisma");
const bcrypt = require("bcrypt");
require("dotenv").config();

exports.registerHandler = async (req, res) => {
  try {
    const {
      Username,
      Password,
      Firstname,
      Lastname,
      Email,
      ProjectCode,
      PhoneNumber,
      Role,
      ProjectName,
    } = req.body;

    // console.log("req.body :>", req.body);
    // Validate input
    if (
      !Username ||
      !Password ||
      !Firstname ||
      !Lastname ||
      !Email ||
      !ProjectCode ||
      !PhoneNumber ||
      !ProjectName
    ) {
      return res.status(400).json({
        status: false,
        message: "Please provide all required fields",
        val: 0,
      });
    }

    const existingUsername = await prisma.users.findFirst({
      where: { Username },
    });

    if (existingUsername) {
      return res.json({
        status: false,
        message: "Username already exists",
        val: 1,
      });
    }

    const existingEmail = await prisma.users.findFirst({
      where: { Email },
    });

    if (existingEmail) {
      return res.json({
        status: false,
        message: "Email already exists",
        val: 2,
      });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(Password, 10);

    const newUser = await prisma.users.create({
      data: {
        Username,
        Firstname,
        Lastname,
        Email,
        PhoneNumber,
        isActive: false,
        LineNotifyActive: false,

        Password: {
          create: {
            PasswordHash: hashedPassword,
          },
        },

        // ❗ แนะนำ: connect role แทน create (กัน role ซ้ำ)
        Role: {
          connectOrCreate: {
            where: { UserRole: Role },
            create: { UserRole: Role },
          },
        },

        Project: {
          create: {
            ProjectName,
            ProjectCode,
          },
        },
      },

      select: {
        UserId: true,
        Username: true,
        Email: true,
      },
    });

    return res.status(201).json({
      status: true,
      message: "Account created Successfully",
      user: newUser,
      val: 3,
    });
  } catch (error) {
    console.error("Register Error:", error);
    return res.status(500).json({
      status: false,
      message: "Error creating account",
      error: error.message,
      val: 0,
    });
  }
};
