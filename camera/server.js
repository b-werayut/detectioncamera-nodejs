const express = require("express");
const app = express();
const morgan = require("morgan");
const { readdirSync } = require("fs");
const cors = require("cors");
require("dotenv").config();
const { cronDelDir } = require("./controllers/directory");

const port = process.env.PORT
app.use(morgan("dev"));
app.use(cors());
app.use(express.json());

readdirSync("./routes").map((c) => app.use("/api", require(`./routes/${c}`)));

app.listen(port, async () => {
  await cronDelDir();
  console.log(`Server is running on port ${port} `);
});
