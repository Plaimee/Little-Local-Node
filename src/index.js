const express = require("express");
const bodyParser = require("body-parser");
const dotenv = require("dotenv");
const http = require("http");
const fs = require('fs');
const { Server } = require("socket.io");
const { configCors } = require("./config/cors");
// const { db } = require("./config/database");
const outputRoute = require("./routers/outputRoute");
const { generateNoId } = require("./helper/generateNoId");
const path = require("path");

const app = express();
dotenv.config();
const server = http.createServer(app);
const io = new Server(server, configCors);

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(configCors);

app.use("/uploads", express.static(path.join(__dirname, "uploads")));
app.use("/outputs", outputRoute);
app.get("/", (req, res) => {
  res.sendFile(path.join(__dirname, 'views/public', 'index.html'));
});

io.on("connection", (socket) => {
  console.log("Client connected: ", socket.id);

  socket.on("imgPath", (data) => {
    console.log("Image Path: ", data.path);
    console.log("Max results: ", data.max_results);

    const filePath = data.path.trim();

    socket.broadcast.emit("sendtoAI", { path: filePath, max_results: data.max_results });
  });

  socket.on("keypointsData", (data) => {
    try {
      if (!data || typeof data !== "object") {
        throw new Error("Invalid keypoint data format");
      }
      const noId = generateNoId();
      console.log("Generate No ID: ", noId);

      console.log("Keypoint Update: ", data);
      socket.broadcast.emit("keypointUpdate", data);
      socket.broadcast.emit("outputId", noId);
    } catch (error) {
      console.error("Error handling keypointUpdate: ", error.message);
    }
  });

  socket.on("removedBGImage", (data) => {
    console.log("Remove Background Image: ", data);

    socket.broadcast.emit("removedOrgBg", data);
  });

  socket.on("annotatedImage", (data) => {
    console.log("Annotated Image: ", data);

    socket.broadcast.emit("imageToUnity", data);
  });

  socket.on("nfd", (data)=> {
    console.log("data: ", data);

    socket.broadcast.emit("noFaceDetected", data);
  });

  socket.on("sendData", (data) => {
    try {
      console.log("📥 Received Data:", data);
      if (!data || typeof data !== "object") {
        console.error("❌ Invalid data received:", data);
        return;
      }

      const { noId, chaop, secop, fiop } = data;
      if (!noId) {
        console.error("❌ No ID not provided by the client!");
        return;
      }

      const chaopPath = chaop?.trim();
      const secopPath = secop?.trim();
      const fiopPath = fiop?.trim();

      if (!chaopPath || !secopPath || !fiopPath) {
        console.error("❌ One or more file paths are empty!");
        return;
      }

      if (!fs.existsSync(chaopPath) || !fs.existsSync(secopPath) || !fs.existsSync(fiopPath)) {
        console.error("❌ One or more files do not exist!");
        return;
      }

      console.log("✅ All files exist! Proceeding with move operation...");

      const uploadDir = path.join(__dirname, "uploads");
      if (!fs.existsSync(uploadDir)) fs.mkdirSync(uploadDir, { recursive: true });

      const timestamp = Date.now();
      const newChaopName = `chaop_${timestamp}${path.extname(chaopPath)}`;
      const newSecopName = `secop_${timestamp}${path.extname(secopPath)}`;
      const newFiopName = `fiop_${timestamp}${path.extname(fiopPath)}`;

      const base64String1 = toBase64(chaopPath);
      const withPrefix1 = 'data:image/png;base64,' + base64String1;
      const base64String2 = toBase64(secopPath);
      const withPrefix2 = 'data:image/png;base64,' + base64String2;
      const base64String3 = toBase64(fiopPath);
      const withPrefix3 = 'data:image/png;base64,' + base64String3;
      const uploadtoServer = 'https://funcslash.com/projects/2025/lclm/savetoServer.php';
      const formData = new FormData();
      formData.append('NoId', noId);
      formData.append('ImageName1', newChaopName)
      formData.append('Image1', withPrefix1);
      formData.append('ImageName2', newSecopName)
      formData.append('Image2', withPrefix2);
      formData.append('ImageName3', newFiopName)
      formData.append('Image3', withPrefix3);

      fetch(uploadtoServer, {
        method: "POST",
        body: formData,
      })
      .then((response) => response.text())
      .then((data) => {
        console.log(data);
        // socket.broadcast.emit("urltoQrCode", 'https://funcslash.com/projects/2025/lclm/get.php?noId=',noId);
      })

      const newChaopPath = path.join(uploadDir, newChaopName);
      const newSecopPath = path.join(uploadDir, newSecopName);
      const newFiopPath = path.join(uploadDir, newFiopName);

      fs.copyFileSync(chaopPath, newChaopPath);
      fs.copyFileSync(secopPath, newSecopPath);
      fs.copyFileSync(fiopPath, newFiopPath);
      console.log("Files moved to uploads folder!");

      // const sql = "INSERT INTO lclm_outputs (images, noId) VALUES (?, ?), (?, ?), (?, ?)";
      // db.query(sql, [newChaopName, noId, newSecopName, noId, newFiopName, noId], (err, result) => {
      //   if (err) {
      //     console.error("Error inserting into database: ", err);
      //   } else {
      //     console.log("Data inserted successfully! Insert ID: ", result.insertId);
      //   }
      // });
    } catch (error) {
      console.error("Error processing sendData: ", error.message);
    }
    
  })

  socket.on("disconnect", () => {
    console.log("Client disconnected:", socket.id);
  });
});

const port = process.env.PORT || 3001;
server.listen(port, () => console.log(`Server is running on http://localhost:${port}`))
// db.getConnection((err, connection) => {
//   if(err) {
//     console.error("Failed to connect to the database: ", err);
//     process.exit(1);
//   } else {
//     console.log("Database connected successfully");
//     connection.release();
    
//   }
// });

function toBase64(filPath) {
  const img = fs.readFileSync(filPath);

  return Buffer.from(img).toString('base64');
}
